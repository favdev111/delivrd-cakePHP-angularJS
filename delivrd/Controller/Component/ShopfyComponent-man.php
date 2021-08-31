<?php
App::uses('Component', 'Controller');
class ShopfyComponent extends Component {

    var $settings = [];
    public $components = array('Auth', 'Session');
    public $uoms = array('Piece' => 'Piece', 'Carton' => 'Carton', 'Kilogram' => 'Kilogram', 'Pound' => 'Pound', 'Box' => 'Box', 'ca' => 'Case');

    public function initialize(Controller $controller) {
        /*if (Configure::read('Shopfy') !== null) {
            $this->settings = Configure::read('Shopfy');
        }*/

       // $this->settings['pwd'] = 'e9a1251e8e3a62f93866c78cab2437b4';
      //  $this->settings['store'] = 'delivrd-2.myshopify.com';
    }

    /*
     *
     *
     *
     */
    public function import($products) {
        $this->Category = ClassRegistry::init('Category');
        $this->Product = ClassRegistry::init('Product');
        $this->Inventory = ClassRegistry::init('Inventory');

        $added = 0;
        $updated = 0;
        $skiped = 0;
        $errors = [];
        
        foreach ($products as $productdata) {
            $productdata['Product']['description'] = $this->prepare_desc($productdata['Product']['description']);
            $productdata['Product']['user_id'] = $this->Auth->user('id');
            $productdata['Product']['dcop_user_id'] = $this->Auth->user('id');
            if(empty($productdata['Product']['description'])) {
                $productdata['Product']['description'] = $productdata['Product']['name'];
            }
            if(empty($productdata['Product']['imageurl'])) {
                $productdata['Product']['imageurl'] = "https://delivrd.com/image_missing.jpg";
            }
            // We need new map for UOMs
            /*if(!empty($productdata['Product']['uom'])) {
                $uomvalid = array_search($productdata['Product']['uom'],$this->uoms);
                if(!$uomvalid) {
                    $this->showimporterror("UOM ".$productdata['Product']['uom']." is not valid.",$file); 
                }
            }*/
        
            if(!empty($productdata['Product']['group']))
            {
                $this->loadModel('Group');
                $group = $this->Group->find('first',array('conditions' => array('Group.name' => $productdata['Product']['group'])));
                
                if(!empty($group)) {
                    $productdata['Product']['group_id'] = $group['Group']['id'];
                } else {
                    $productdata['Product']['group_id'] = '99';
                }
            }
            
            if(!empty($productdata['Product']['category']))
            {
                $category = $this->Category->find('first',array('conditions' => array('Category.name' => $productdata['Product']['category'],'Category.user_id' => $this->Auth->user('id'))));
                if(!empty($category)) {
                    $productdata['Product']['category_id'] = $category['Category']['id'];
                } else {
                    // Add Category:
                    $this->Category->create();
                    $cat['Category']['name'] = $productdata['Product']['category'];
                    $cat['Category']['user_id'] = $this->Auth->user('id');
                    $this->Category->save($cat);
                    $productdata['Product']['category_id'] = $this->Category->id;
                }
                $skuexists = $this->Product->find('first',array('fields' => 'Product.sku','conditions' => array('Product.sku' =>$productdata['Product']['sku'], 'Product.user_id' => $this->Auth->user('id'))));
                
                if(sizeof($skuexists) == 0){
                    $productdata['Product']['status_id'] = 1;
                    $productdata['Product']['deleted'] = 0;
                    $this->Product->create();
                    if($this->Product->saveAssociated($productdata)) {
                        $added++;
                    } else {
                        $errors[$productdata['Product']['parentid']]['title'] = $productdata['Product']['name'];
                        $errors[$productdata['Product']['parentid']]['error'] = 'Can\'t save new product';
                        $errors[$productdata['Product']['parentid']]['details'] = $this->Product->validationErrors;
                    }
                } else {
                    $productdata['Product']['id'] = $skuexists['Product']['id'];
                    $this->Inventory->deleteAll(array('Inventory.product_id' => $skuexists['Product']['id']), false);
                    if($this->Product->saveAll($productdata, array('deep'=>true))) {
                        $updated++;
                    } else {
                        $errors[$productdata['Product']['parentid']]['title'] = $productdata['Product']['name'];
                        $errors[$productdata['Product']['parentid']]['error'] = 'Can\'t update product';
                        $errors[$productdata['Product']['parentid']]['details'] = $this->Product->validationErrors;
                    }
                }
                
            } else {
                // think we need have category 'Uncategorized' for such product
                $skiped++;
                $errors[$productdata['Product']['parentid']]['title'] = $productdata['Product']['name'];
                $errors[$productdata['Product']['parentid']]['error'] = 'Category can\'t be empty';
            }
        }
        return ['found'=>count($products), 'added'=>$added, 'updated'=>$updated, 'skiped'=>$skiped, 'errors'=>$errors];
    }

    public function getProducts($data) {
        $imgs = array();
        foreach($data->images as $img){
            $imgs[] = $img->src;
        }

        $options = array();
        foreach($data->options as $opt){
            $nm = strtolower($opt->name);
            $options[$nm] = $opt->position;
        }

        $products = array();
        foreach($data->variants as $var){
            if(!empty($var->sku)) {
                $color = $size = '';
                if(isset($options['size']) && $options['size'] == 1 ){
                    $size = $var->option1;
                }
                if(isset($options['size']) && $options['size'] == 2){
                    $size = $var->option2;
                }
                if(isset($options['size']) && $options['size'] == 3){
                    $size = $var->option3;
                }

                if(isset($options['color']) && $options['color'] == 1 ){
                    $color = $var->option1;
                }
                if(isset($options['color']) && $options['color'] == 2){
                    $color = $var->option2;
                }
                if(isset($options['color']) && $options['color'] == 3){
                    $color = $var->option3;
                }

                $product['Product']['name']                 = $data->title.': '.$var->title;
                $product['Product']['product_id']           = $data->id;
                $product['Product']['parentid']             = $var->id;
                $product['Product']['description']          = $this->prepare_desc($data->body_html);
                $product['Product']['group_id']             = 0;
                $product['Product']['uom']                  = $var->weight_unit; //Weight Units ex: oz
                $product['Product']['weight']               = $var->weight;
                $product['Product']['width']                = '';
                $product['Product']['height']               = '';
                $product['Product']['depth']                = '';
                $product['Product']['barcode']              = $var->barcode;
                $product['Product']['sku']                  = $var->sku;
                $product['Product']['consumption']          = '';
                $product['Product']['bin']                  = '';
                $product['Product']['value']                = number_format((float)$var->price, 2, '.', '');;
                $product['Product']['imageurl']             = (isset($data->image->src)?$data->image->src:'');
                $product['Product']['pageurl']              = '';
                $product['Product']['color_id']             = '';
                $product['Product']['size_id']              = '';
                $product['Product']['category']             = $data->product_type;
                $product['Product']['category_id']          = '';
                $product['Product']['publish']              = '';
                $product['Product']['createdinsource']      = $var->created_at;
                $product['Product']['modifiedinsource']     = $var->updated_at;
                $product['Product']['catalog']              = '';
                $product['Product']['created']              = date('Y-m-d H:i:s');
                $product['Product']['modified']             = date('Y-m-d H:i:s');
                // Question: for some products shopify return negative quantity, what it mean? What we need to do with it?
                $product['Inventory'][0]['quantity']        = abs($var->inventory_quantity);
                $product['Inventory'][0]['user_id']         = $this->Auth->user('id');
                $product['Inventory'][0]['dcop_user_id']    = $this->Auth->user('id');
                $product['Inventory'][0]['warehouse_id']    = $this->Session->read('default_warehouse');

                $products[] = $product;
            }
       }
       return $products;
    }

    public function prepare_desc($html) {
        return preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", strip_tags($html));
    }

    /*
     *
     *
     *
    **/
    public function getJson($url = '/admin/products/count.json',$username = '',$password = '', $url_store = '') {
 $this->settings['api'] = $username;
        $this->settings['pwd'] = $password;
        $this->settings['store'] = $url_store;


        $url = "https://".$this->settings['api'].":".$this->settings['pwd']."@".$this->settings['store'].$url;
        $result = $this->send_request($url);
        return $result;
    }

    public function send_request($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'QA server');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($ch);

        if ($output === false)
            throw new \Exception(curl_error($ch));

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseCode == 200) {
            try {
                $jo = json_decode($output);
                if ($jo === false)
                    throw new \Exception("Failed to parse JSON");

                $response = $jo;
                $response->status = 'OK';
            } catch (\Exception $e) {
                $response->status = 'REQUEST_FAILED';
                throw $e;
            }
        } else {
            try {
                $jo = json_decode($output);
                if ($jo === false)
                    throw new \Exception("Failed to parse JSON");
                $response = $jo;

                switch ($responseCode) {
                    case 400:
                        $response->status = 'REQUEST_REJECTED';
                        break;
                    case 402:
                        $response->status = 'METHOD_NOT_ALLOWED';
                        break;
                    case 405:
                        $response->status = 'METHOD_NOT_ALLOWED';
                        break;
                    default:
                        $response->status = 'REQUEST_FAILED';
                        break;
                }

                return $response;
            } catch (\Exception $e) {
                $response->status = 'REQUEST_FAILED';
                throw $e;
            }
        }
        return $response;
    }
}
