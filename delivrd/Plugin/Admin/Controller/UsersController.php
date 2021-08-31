<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Invalert $Invalert
 */
class UsersController extends AppController {

    //public $theme = 'Mtro';

	/**
     * Components
     *
     * @var array
     */
    public $components = array('Access', 'Paginator');

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array();

    /**
     * If the controller is a plugin controller set the plugin name
     *
     * @var mixed
     */
    public $plugin = 'Admin';

    public $uses = array('Txreport', 'Inventory', 'Product', 'User', 'Order', 'OrdersLine');

    public $paginate = array();

    public function beforeFilter() {
       parent::beforeFilter();
    }
    
    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        if($user['email'] == 'fordenis@ukr.net' || $user['email'] == 'technoyos@gmail.com') {
            return true;
        } else {
            return false;
        }
    }

    public function index() {
        $limit = 20;
        $this->set(compact('limit'));
    }

    public function index_js() {
        $this->layout = false;
        $limit = 20;
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        $orderBy = 'User.created';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'DESC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }
        $page = 1;
        if(isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $users = $this->User->find('all', [
            'conditions' => [],
            'contain' => [],
            'fields' => [
                'User.id',
                'User.email',
                'User.firstname',
                'User.lastname',
                'User.created',
                'User.last_login'
                
            ],
            'limit' => $limit,
            'page' => $page,
            'order' => array($orderBy => $orderDir)
        ]);

        //$tx_reports = $this->User->query('SELECT count(*) as total FROM users WHERE 1');
        $recordsCount = $this->User->find('count');

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($users);
        $response['rows'] = $users;

        //header('Content-Type: application/json');

        echo json_encode($response);
        exit;
    }

    public function import_product($user_id) {
        $user = $this->User->find('first', ['contain' => false, 'conditions' => ['id' => $user_id]]);

        $matchColumnDisplay = false;
        $show_trial = false;
        if($this->request->is('post')) {
            $uploadPath = WWW_ROOT.'uploads/';
            $fileName = $this->request->data['fileupload']['photo'];
            $target_file = WWW_ROOT.'uploads/'. basename($fileName["name"]);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            $html = '';
            $max_file_size = Configure::read('FileuploadSize') ;
            if($imageFileType != "csv") {
                $html .=  '<div class="alert alert-danger">only csv format is allowed</div>';
            } elseif($fileName["size"] > $max_file_size){
                $html .=  '<div class="alert alert-danger">file size is '.$fileName["size"] .' bytes that is too larger Please try other file</div>';
            } else {
                
                $f = explode('.',$fileName['name']);
                $fname = $f[0].time().'.'.$imageFileType;
                $file = $uploadPath.$fname;
                if(move_uploaded_file($fileName['tmp_name'],$file)) {
                    $product_limit = $user['User']['paid'] == 1 ? Configure::read('Productlimit.paiduser') : Configure::read('Productlimit.freeuser');
                    $fp = file($uploadPath.$fname);
                    $csvrecords = count($fp)-1;
                    $rowcount = $this->Product->find('count', array('conditions' => array('Product.user_id' => $user_id)));
                    $productlimit = $product_limit - $rowcount;
                    if($csvrecords < $productlimit){
                        //  to insert new record in transfer
                        $this->loadModel('Transfer');
                        $this->Transfer->create();
                        $this->request->data('Transfer.user_id', $user_id);
                        $this->request->data('Transfer.type',1);
                        $this->request->data('Transfer.direction',1);
                        $this->request->data('Transfer.source',1);
                        $this->request->data('Transfer.status',1);
                        $this->request->data('Transfer.recordscount',0);
                        if ($res = $this->Transfer->save($this->request->data)) {
                            $transfer_id = $res['Transfer']['id'];
                            $matchColumnDisplay = true;
                            $file = fopen($file,"r");
                            $headers = fgetcsv($file);
                            //echo "<pre>"; print_r($headers); die;
                            if(count($headers) < 2){
                                $html .=  '<div class="alert alert-danger">You are uploading the blank CSV.</div>';
                                $matchColumnDisplay = false;
                            } else {
                                $headerCols = array();
                                $q = 0;
                                foreach($headers as $header){
                                    $headerCols[$q] = $header;
                                    $q++;
                                }

                                $this->loadModel('Fields_value');
                                $Columns = $this->Fields_value->find('all',array('conditions' => array('field_for'=>1)));
                                $fields = array();
                                foreach($Columns as $key =>$Column) {
                                    $fields [$Column['Fields_value']['id']]['is_mandatory'] = $Column['Fields_value']['is_mandatory'];
                                    $fields [$Column['Fields_value']['id']]['database_value'] = $Column['Fields_value']['database_value'];
                                    $fields [$Column['Fields_value']['id']]['display_value'] = $Column['Fields_value']['display_value'];
                                }
                                                                
                                $this->set('file', $uploadPath . $fname);
                                $this->set('transfer_id', $transfer_id);
                                $this->set('headerCols', $headerCols);
                                $this->set('fields', $fields);
                                $this->set('matchColumnDisplay', $matchColumnDisplay);
                                #$this->layout = 'mtrd';
                                $this->set('fields', $fields);
                            }
                        } else {
                            $html .=  '<div class="alert alert-danger">â€˜Could not upload file. Please try againâ€™</div>';
                        }
                    } else {
                        $html .=  '<div class="alert alert-danger">You have already uploaded '.$rowcount .' products. The maximum limit for your account is '.$product_limit.'</div>';
                        $show_trial = true; 
                    }
                } else {
                    $html .=  '<div class="alert alert-danger">Error while uploading file. Please try again.</div>';
                }
            }
            $this->set('html', $html);
        }

        //$this->layout = 'mtrd';
        //$this->render('add_products_csv');
        $this->set(compact('user', 'matchColumnDisplay'));
    }


    public function validate_csv($user_id) {
        $csvcol = array_filter($_POST['csvcol']);
        $dbcol = array_filter($_POST['dbcol']);
        $completeArr = [];
        for($i=0; $i <= count($csvcol); $i++) {
            if(isset($dbcol[$i]) && isset($csvcol[$i])) {
                $completeArr[$csvcol[$i]] = $dbcol[$i];
            }
        }

        $filename = $_POST['file_name'];
        $file = fopen($filename,"r");
        $title = fgetcsv($file);
        $data = fgetcsv($file, 1000, ",");
        $header = array();
        $values = array();
        $sizes = array();
        $file = fopen($filename,"r");
        $fp = file($filename, FILE_SKIP_EMPTY_LINES);
        $title = fgetcsv($file);
        $data = fgetcsv($file, 1000, ",");
        $row = 1;
        if (($handle = fopen($filename, "r")) !== false) {
            for($t=0; $t < count($title); $t++) {
                $header[$t] = $title[$t];
            }
            while (($data = fgetcsv($handle, 1000, ",")) !== false) { 
                $num = count($data);
                $values[$row] = $data; 
                $row++;
            } 
            fclose($handle);
        }

        $indexarr = [];
        $indexarr = $values[1];
        $j = 1; 
        for($t=2; $t <= count($values); $t++) {
            $index = [];
            $index = $values[$t];
            for($i=0; $i < count($index); $i++) {
                $valarray[$j][$indexarr[$i]] = $index[$i];
            }
            $j++;
        }

        $products = [];
        for($q=1; $q<=count($fp)-1;$q++) {
            $r=1;
            foreach($completeArr as $c => $key) {
                $products[$q][$key] = $valarray[$q][$c];
                $r++;
            }
        }

        $this->loadModel('Category');
        $categories = $this->Category->find('all',array('conditions' => array('Category.user_id' => $user_id)));
        $cat = array();
        foreach($categories as $cate) {
            $cat[$cate['Category']['id']] = $cate['Category']['name'];
        }
        
        $this->loadModel('Colors');
        $Colors = $this->Colors->find('all',array('conditions' =>array('Colors.user_id' => $user_id)));
        $col = array();
        foreach($Colors as $Color) {
            $col[$Color['Colors']['id']] = $Color['Colors']['name'];
        }
        
        $this->loadModel('Size');
        $Size = $this->Size->find('all',array('conditions' =>array('Size.user_id' => $user_id)));

        if(count($Size)){
            foreach($Size as $si){
                $sizes[$si['Size']['id']] = $si['Size']['name'];
            }
        }
        
        $catarray = array();
        $colorarray = array();
        $sizearray = array();
        foreach($products as $product){
            foreach($product as $key => $p){
                if($key == 'category_id') {
                    if (!in_array($p, $cat)) {
                        array_push($catarray,$p);
                    }
                }
                if($key == 'color_id'){
                    if (!in_array($p, $col)) {
                        array_push($colorarray,$p);
                    }
                }
                if($key == 'size_id'){
                    if (!in_array($p, $sizes)) {
                        array_push($sizearray,$p);
                    }
                }
            }
        }

        // we need only unique category names, colors and sizes
        $catarray = array_unique($catarray);
        $colorarray = array_unique($colorarray);
        $colorarray = array_values($colorarray);
        $sizearray = array_unique($sizearray);

        $productView ='<table border="2" width="100%">';
        $productView .='<tr><th>Product Name</th><th>Status</th></tr>';
        $is_update = false;
        $is_create = false;
        foreach($products as $key => $p) {
            $a = array();
            $a['Product'] = $p;
            $a['Product']['user_id'] = $user_id;
            $data = $this->Product->set($a);
            $productView .= '<tr><td>';
            $productView .= (isset($a['Product']['name'])) ? $a['Product']['name'] : $key-1;
            $productView .= '</td><td>';
            if ($this->Product->validates()) {
                $productView .= '<span class="text-success"><i class="fa fa-check"></i> Validated succesfully</span>';
                $is_create = true;
            } else {
                $productView .= '';
                $errors = $this->Product->validationErrors;
                $class = 'warning';
                $productViewT = '';
                foreach($errors as $e){
                    if(preg_match('/SKU [a-z0-9]+ already exists/i', $e[0]) || preg_match('/Product name (.?)+ already exists/i', $e[0])) {
                        $productViewT .= '<i class="fa fa-exclamation-circle"></i> '. $e[0].'<br>';
                    } else {
                        $class = 'danger';
                        $productViewT .= '<i class="fa fa-exclamation-circle"></i> '. $e[0].'<br>';
                    }
                }
                $success = 0;
                $productView .= '<span class="text-'. $class .'">'. $productViewT. (($class == 'warning')?' Product will be updated.':'') .'</span>';
                if($class == 'warning') {
                    $is_update = true;
                }
            }
            $productView .= '</td></tr>';
        }
        $productView .= '</table>';
        $newfields = array();
        $newfields['catarray'] = $catarray;
        $newfields['colorarray'] = $colorarray;
        $newfields['sizearray'] = $sizearray;
        
        $response['success'] = 0;
        $response['newfields'] = $newfields;
        $response['products'] = $productView;
        if($is_create && $is_update) {
            $response['btn_title'] = 'Create &amp; Update Products';
        } else if($is_create) {
            $response['btn_title'] = 'Create Products';
        } else {
            $response['btn_title'] = 'Update Products';
        }
        echo json_encode($response);
        exit;
    }


    public function add_product($user_id) {
        $user = $this->User->find('first', ['contain' => false, 'conditions' => ['id' => $user_id]]);

        if($this->request->is('post')) {
            #$completeArr = array_combine(array_filter($_POST['csvcol'], 'strlen'),array_filter($_POST['dbcol']));
            $csvcol = array_filter($_POST['csvcol']);
            $dbcol = array_filter($_POST['dbcol']);
            $completeArr = [];
            for($i=0; $i <= count($csvcol); $i++) {
                if(isset($dbcol[$i]) && isset($csvcol[$i])) {
                    $completeArr[$csvcol[$i]] = $dbcol[$i];
                }
            }

            $filename = $_POST['file_name'];
            $this->layout = 'mtrd';
            $file = fopen($filename,"r");
            $title = fgetcsv($file);
            $data = fgetcsv($file, 1000, ",");
            $header = array();
            $values = array();
            $csvdata = array();
            $file = fopen($filename,"r");
            $fp = file($filename, FILE_SKIP_EMPTY_LINES);
            $title = fgetcsv($file);
            $data = fgetcsv($file, 1000, ",");
            $tdata = fgetcsv($file, 1000, ",");
            $row = 1;
            if (($handle = fopen($filename, "r")) !== FALSE) {
                for($t=0; $t< count($title); $t++) {
                    $header[$t] = $title[$t];
                }
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    $values[$row] = $data;
                    $row++;
                }
                fclose($handle);
            }

            $indexarr = [];
            $indexarr = $values[1];
            $j = 1; 
            for($t=2; $t <= count($values); $t++) {
                $index = [];
                $index = $values[$t];
                for($i=0; $i < count($index); $i++) {
                    $valarray[$j][$indexarr[$i]] = $index[$i];
                }
                $j++;
            }
     
            for($q=1; $q<=count($fp)-1;$q++) {
                $r=1;
                foreach($completeArr as $c => $key) {
                    $product[$q][$key] = $valarray[$q][$c];
                    if($key == 'category_id') {
                        $this->loadModel('Category');
                        $category = $this->Category->find('first',array('conditions' => array('Category.name' => $valarray[$q][$c],'Category.user_id' => $user_id)));

                        if(!empty($category)) {
                            $product[$q]['category_id'] = $category['Category']['id'];
                        }
                    }
                    if($key == 'group_id') {
                        $this->loadModel('Group');
                        $group = $this->Group->find('first',array('conditions' => array('Group.name' => $valarray[$q][$c])));

                        if(!empty($group)) {
                            $product[$q]['group_id'] = $group['Group']['id'];
                        }
                    }
                    if($key == 'size_id') {
                        $this->loadModel('Size');
                        $size = $this->Size->find('first',array('conditions' => array('Size.name' => $valarray[$q][$c],'Size.user_id' => $user_id)));

                        if(!empty($size)) {
                            $product[$q]['size_id'] = $size['Size']['id'];
                        }
                    }
                    if($key == 'color_id') {
                        $this->loadModel('Color');
                        $color = $this->Color->find('first',array('conditions' => array('Color.name' => $valarray[$q][$c],'Color.user_id' => $user_id)));

                        if(!empty($color)) {
                            $product[$q]['color_id'] = $color['Color']['id'];
                        }
                    }

                    $product[$q]['user_id'] = $user_id;
                    $r++;
                }
            }


            $this->loadModel('Category');
            $categories = $this->Category->find('all',array('conditions' =>array('Category.user_id' => $user_id)));
            $cat = array();
            foreach($categories as $cate)
            $cat[$cate['Category']['id']] = $cate['Category']['name'];
            
            $this->loadModel('Group');
            $Groups = $this->Group->find('all');
            $groups = array();
            foreach($Groups as $gro)
            $groups[$gro['Group']['id']] = $gro['Group']['name'];
            
            $this->loadModel('Colors');
            $Colors = $this->Colors->find('all',array('conditions' =>array('Colors.user_id' => $user_id)));
            $col = array();
            foreach($Colors as $Color)
            $col[$Color['Colors']['id']] = $Color['Colors']['name'];

            
            $this->loadModel('Size');
            $Size = $this->Size->find('all',array('conditions' =>array('Size.user_id' => $user_id)));
            if(count($Size)){
                foreach($Size as $si)
                $sizes[$si['Size']['id']] = $si['Size']['name'];
            }
            
            $this->set('colours', $col);
            
            if(count($Size)){
                $this->set('size', $sizes);
            }

            $this->set('group', $groups);
            $this->set('category', $cat);
            $this->set('transfer_id', $_POST['transfer_id']);
            $this->set('products', $product);
            $this->set('user', $user);
            $this->render('response');
        } else {
            $this->layout = 'mtrd';
            $this->set('matchColumnDisplay', false);
            $this->set('user', $user);
            $this->render('import_product');
        }
    }

    public function create_new_fields($user_id){
        if(ISSET($_POST['category_name'])){
            $categories = $_POST['category_name'];
            $this->loadModel('Category');
            $catView ='<table border="2" width="100%">';
            $catView .='<tr><th>Category Name</th><th>Status</th></tr>';
            foreach($categories as $key => $category){
                $this->Category->create();
                $this->request->data('Category.name', $category);
                $this->request->data('Category.description', $category);
                $this->request->data('Category.user_id',$user_id);
                $catView .= '<tr><td>';
                $catView .= $category;
                $catView .= '</td><td>';
                if ($res = $this->Category->save($this->request->data)) {
                    $catView .= 'Validated succesfully';
                    $catSuccess = true;
                }else{
                    $errors = $this->Category->validationErrors;
                    foreach($errors as $e){
                        $catView .= $e[0].'<br>';
                    }
                    $catSuccess = false;
                } 
                $catView .= '</td></tr>'; 
            }
            $catView .= '</table>';
            $response['catView'] = $catView;
            $response['catTable'] = 'Category Validation Status';
            $response['catSuccess'] = $catSuccess;
        }
        if(isset($_POST['colors_name'])){
            $colors = $_POST['colors_name'];
            $this->loadModel('Color');
            $colorView ='<table border="2" width="100%">';
            $colorView .='<tr><th>Size Name</th><th>Status</th></tr>';
            foreach($colors as $key => $color){
                $this->Color->create();
                $this->request->data('Color.user_id',$user_id);
                $this->request->data('Color.name',$color);
                $colorView .= '<tr><td>';
                $colorView .= $color;
                $colorView .= '</td><td>';
                if ($res = $this->Color->save($this->request->data)) {
                    $colorView .= 'Validated succesfully';
                    $colorSuccess = true;
                }else{
                    $errors = $this->Color->validationErrors;
                    foreach($errors as $e){
                        $colorView .= $e[0].'<br>';
                    }
                    $colorSuccess = false;
                }
                $colorView .= '</td></tr>'; 
            }
            $colorView .= '</table>';
            $response['colorView'] = $colorView;
            $response['cTable'] = 'Color Validation Status';
            $response['cSuccess'] = $colorSuccess;
        }
        
        if(isset($_POST['size'])){
            $sizes = $_POST['size'];
            $this->loadModel('Size');
            $sizeView ='<table border="2" width="100%">';
            $sizeView .='<tr><th>Size Name</th><th>Status</th></tr>';
            foreach($sizes as $key => $size){
                $this->Size->create();
                $this->request->data('Size.user_id',$user_id);
                $this->request->data('Size.name',$size);
                $sizeView .= '<tr><td>';
                $sizeView .= $size;
                $sizeView .= '</td><td>';
                if ($res = $this->Size->save($this->request->data)) {
                    $sizeView .= 'Validated succesfully';
                    $sizeSuccess = true;
                }else {
                    $errors = $this->Size->validationErrors;
                    foreach($errors as $e){
                        $sizeView .= $e[0].'<br>';
                    }
                    $sizeSuccess = false;
                }
                $sizeView .= '</td></tr>';
            }
            $sizeView .= '</table>';
            $response['sizeView'] = $sizeView;
            $response['sTable'] = 'Size Validation Status';
            $response['sSuccess'] = $sizeSuccess;
        }
        echo json_encode($response);
        exit;
    }

    public function add_csv_products($user_id){
        $response= array();
        $product ='';
        //echo "<pre>"; print_r($this->request->data); die;
        if(isset($_POST['sku'])){
            $this->Product->recursive = -1;
            $product= $this->Product->find('first',array('conditions' => array('Product.sku'=>$_POST['sku'], 'Product.user_id' => $user_id) ));
        }
        if(!$product){
            $quantity = isset($_POST['quantity']) ? $_POST['quantity']: '0' ;
            
            $this->Product->create();
            $data = array();
            $this->request->data('user_id',$user_id);
            $this->request->data('deleted',0);
            $this->request->data('status_id',1);
        
            foreach($_POST as $c =>$key) {
                $this->request->data($c,$key);
            }
            if(!ISSET($_POST['imageurl']) || $_POST['imageurl'] == '') {
                $this->request->data('imageurl', Configure::read('Product.image_missing'));
            }
            
            if (!$res = $this->Product->save($this->request->data)) {
                $response['success'] = 0;
                $response['status'] = 'Failed';
                $errors = $this->Product->validationErrors;
                foreach($errors as $e){
                    $response['message'] = $_POST['name'] .' - '. ($e[0]);
                }
            } else {

                // Add Inventory
                if(isset($this->request->data['quantity']) && $this->request->data['quantity'] > 0) {
                    $this->createinventoryrecord($user_id, $res['Product']['id'],$this->request->data['quantity']);
                }

                $response['invent'] = $res['Product']['id'];
                $response['success'] = 1;
                $response['status'] = 'Success';
                $response['message']  = $_POST['name'].' - Product Created Successfully';
                $this->createinventoryrecord($user_id, $res['Product']['id'],$quantity);
            }
        
        } else {
            if($product['Product']['deleted'] == 1) {
                $response['success'] = 2;
                $response['status'] = 'You already have this product, but it was deleted.';
                $response['product_id'] = $product['Product']['id'];
                $response['html']  = '<td>'.$product['Product']['name'].'</td>
                <td>'.$product['Product']['sku'].'</td>
                <td>Already Exists, have status deleted</td></tr>';
            } elseif($product['Product']['status_id'] == 13 || $product['Product']['status_id'] == 12) {
                $response['success'] = 2;
                $response['status'] = 'You already have this product, but it was blocked.';
                $response['product_id'] = $product['Product']['id'];
                $response['html']  = '<td>'.$product['Product']['name'].'</td>
                <td>'.$product['Product']['sku'].'</td>
                <td>Already Exists, have status blocked</td></tr>';
            } else {
                $response['success'] = 2;
                $response['status'] = 'already Added';
                $response['product_id'] = $product['Product']['id'];
                $response['html']  = '<td>'.$product['Product']['name'].'</td>
                <td>'.$product['Product']['sku'].'</td>
                <td>Already Exists</td></tr>';
            }
        }
        echo json_encode($response);
        die;
    }

    public function update_products($user_id)
    {
        $product_list = array();
        //$products = $_POST['product_details'];
        $products = $_POST['product_details'];
        $i=0;
        for($i = 0;$i < count($_POST['product_keys']); $i++) {
            $keys[] = explode(',', $_POST['product_keys'][$i]);
        }
        for($i = 0;$i < count($_POST['product_details']); $i++) {
            $vals[] = explode(',', $_POST['product_details'][$i]);
        }
        
       foreach($vals as $key => $product){
            if(isset($_POST['updproduct'][$key])){
                $product_list[$key]['id'] = $_POST['product_id'][$key];
                foreach($product as $k => $list){
                    $product_list[$key][$keys[$key][$k]] = $list;
                }
            }
        }

        //pr($product_list);die;
        foreach($product_list as $key => $plist){
            $product_id = $plist['id'];
            //unset($plist['product_id']);
            
            if(isset($plist['quantity'])){
                $inventory_quantity = $plist['quantity'];
                
                $rowcount = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id)));
                if(isset($rowcount['Inventory'])){
                    $this->loadModel('Inventory');
                    $invres = $this->Inventory->updateAll(
                            array('Inventory.quantity' => $inventory_quantity),
                            array('Inventory.product_id' => $product_id));
                            print_r($rowcount['Inventory'][0]['id']);
                }
                unset($plist['Product.quantity']);
            }

            $res = $this->Product->save($plist);
            if ($res == true){
                $this->Session->setFlash(__('Product are Updated Successfully'),'default',array('class'=>'alert alert-success'));
            } else{
                $this->Session->setFlash(__('Products record could not be saved. Please, try again'),'default',array('class'=>'alert alert-danger'));
            }
        }
        exit;
    }

    public function createinventoryrecord($user_id, $pid = null, $qty = null, $warehouse_id = null)
    {   
        $this->loadModel('Warehouse');
        $this->loadModel('Inventory');
        $this->loadModel('OrdersLine');
        $defaultwarehouse = $this->Warehouse->find('first', array('conditions' => array('Warehouse.user_id' => $user_id), 'recursive' => -1));
        //if default wareouse for user does not exist, create it
        if($defaultwarehouse == null)
        {
            $warehousedata = array(
                'Warehouse' => array(
                    'name' => 'Default',
                    'lat' => 1.111,
                    'long' => 1.11,
                    'user_id' => $user_id
                )
            );
            $this->Warehouse->create();
            // save the data
            $returned_warehouse = $this->Warehouse->save($warehousedata);
            $default_warehouse = $returned_warehouse['Warehouse']['id'];
        } else {
            $default_warehouse = $defaultwarehouse['Warehouse']['id'];
        }

        $this->loadModel('Inventory');
        $sentqty = 0;
        $recqty = 0;
        $qty = (!empty($qty) ? $qty : 0);
        $location = (!empty($warehouse_id) ? $warehouse_id : $default_warehouse);
        $result = $this->Inventory->find('first', array('conditions' => array('product_id' => $pid, 'warehouse_id' => $location)));
        if(empty($result)) {
            $this->Inventory->create();
            $this->Inventory->set('user_id',$user_id);
            $this->Inventory->set('dcop_user_id',$user_id);
            $this->Inventory->set('product_id', $pid);
            $this->Inventory->set('quantity', $qty);
            $this->Inventory->set('warehouse_id', $location);
            if ($this->Inventory->save($this->request->data)) {
                if($qty != 0) {
                    $delta = 0 - $qty;

                    if($delta > 0)
                    {
                    $sentqty = abs($delta);
                    }
                    if($delta < 0)
                    {
                    $recqty = abs($delta);
                    }
                    $this->loadModel('OrdersLine');
                    $data = array(
                    'OrdersLine' => array(
                        'order_id' => 4294967294,
                        'line_number' => 1,
                        'type' => 3,
                        'product_id'  => $pid,
                        'quantity' => $qty,
                        'receivedqty' => $recqty,
                        'damagedqty' => 0,
                        'sentqty' => $sentqty,
                        'foc' => '',
                        'warehouse_id' => $default_warehouse,
                        'return' => '',
                        'user_id' => $user_id
                    )
                    );
                    $this->OrdersLine->create();
                    $this->OrdersLine->save($data);
                }

                return 0;
            } else {
                $this->Session->setFlash(__('The Product inventory record could not be saved. Please, try again.'), 'admin/danger', array());
            }
        }

    }





    public function noactive() {
        $exp_period = intval(Configure::read('cleanup.expire_period'));
        if(!$exp_period) {
            $exp_period = 100;
        }

        $exp_date = date('Y-m-d', strtotime('today - '. $exp_period .' days'));
        

        /* this is to slow varian
        $this->User->recursive = -1;
        $this->User->virtualFields['product_conut'] = 'count(DISTINCT Product.id)';
        $this->User->virtualFields['order_count'] = 'count(DISTINCT Order.id)';
        $this->User->virtualFields['orderlines_count'] = 'count(DISTINCT OrdersLine.id)';
        $data = $this->User->find('all', [
            'conditions' => ['User.last_login <' => $exp_date ], //, 'User.role !=' => 'paid'
            'fields' => ['User.id', 'User.email', 'User.last_login', 'User.product_conut', 'User.order_count', 'User.orderlines_count'],
            'contain' => false,
            'joins' => array(
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Product.user_id = User.id',
                    )
                ),array('table' => 'orders',
                    'alias' => 'Order',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Order.user_id = User.id',
                    )
                ),array('table' => 'orders_lines',
                    'alias' => 'OrdersLine',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'OrdersLine.user_id = User.id',
                    )
                ),
            ),
            'group' => 'User.id',
            'limit' => 20
        ]);

        pr($data);
        exit;*/

        $this->User->recursive = -1;
        $this->Product->recursive = -1;
        $this->Order->recursive = -1;
        $this->OrdersLine->recursive = -1;
        $this->paginate['User'] = [
            'conditions' => ['User.last_login <' => $exp_date, 'User.role !=' => 'paid' ],
            'fields' => ['User.id', 'User.email', 'User.firstname', 'User.lastname', 'User.last_login', 'User.role'],
            'contain' => false,
            'limit' => 20
        ];
        $this->Paginator->settings = $this->paginate;
        $data = $this->Paginator->paginate('User');
        foreach ($data as &$user) {
            $user['User']['product_conut'] = $this->Product->find('count', ['conditions' => array('Product.user_id' => $user['User']['id'])]);
            $user['User']['order_count'] = $this->Order->find('count', ['conditions' => array('Order.user_id' => $user['User']['id'])]);
            $user['User']['orderlines_count'] = $this->OrdersLine->find('count', ['conditions' => array('OrdersLine.user_id' => $user['User']['id'])]);
        }
        $this->set(compact('data'));
    }

    function remove($user_id) {
        $this->User->id = $user_id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->allowMethod('post', 'delete');

        $user['User']['id'] = $user_id;
            $sql1 = '
                DELETE w, wc FROM `waves` as w
                LEFT JOIN `waves_countries` as wc
                    ON wc.`wave_id` = w.`id`
                WHERE w.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql1);

            // 1 table: `users_mailchimp` `transfers`, `user_shortcut_links`
            $sql2 = '
                DELETE FROM `users_mailchimp` WHERE `users_id` = "'. $user['User']['id'] .'";
                DELETE FROM `transfers` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `user_shortcut_links` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `user_details` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `sizes` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `events` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `subscriptions` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `suppliers` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `supplysources` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `bins` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `categories` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `colors` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `couriers` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `addresses` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `activities` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `payments` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `integrations` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `ads` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `asns` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `batch_picks` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `csv_mappings` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `documents` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `packstations` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `resources` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `schannels` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `shipments` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `warehouses` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `custom_data` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `custom_values` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `custom_fields` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `invalerts` WHERE `user_id` = "'. $user['User']['id'] .'";
            ';
            $r[$user['User']['id']][] = $this->User->query($sql2);

            // 4 tables: networks networks_invites networks_access networks_users
            // Delete user networks
            $sql3 = '
                DELETE nt.*, na.*, ni.*, nu.* 
                FROM `networks` as nt
                LEFT JOIN `networks_access` as na
                    ON na.`network_id` = nt.`id`
                LEFT JOIN `networks_invites` as ni
                    ON ni.`network_id` = nt.`id`
                LEFT JOIN `networks_users` as nu
                    ON nu.`network_id` = nt.`id`
                WHERE nt.`created_by_user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql3);
            // Delete user from other networks
            $sql4 = '
                DELETE FROM networks_users WHERE user_id = "'. $user['User']['id'] .'";
                DELETE FROM networks_invites WHERE user_id = "'. $user['User']['id'] .'";
                DELETE FROM networks_access WHERE user_id = "'. $user['User']['id'] .'";
            ';
            $r[$user['User']['id']][] = $this->User->query($sql4);


            // Products
            // 7 tables: products, forecasts, products_bins, products_prices, productsuppliers, kits, serials
            // Delete user networks
            $sql5 = '
                DELETE p.*, f.*, pb.*, pp.*, ps.*, k.*, s.*
                FROM `products` as p
                LEFT JOIN `forecasts` as f
                    ON f.`product_id` = p.`id`
                LEFT JOIN `products_bins` as pb
                    ON pb.`product_id` = p.`id`
                LEFT JOIN `products_prices` as pp
                    ON pp.`product_id` = p.`id`
                LEFT JOIN `productsuppliers` as ps
                    ON ps.`product_id` = p.`id`
                LEFT JOIN `kits` as k
                    ON k.`product_id` = p.`id`
                LEFT JOIN `serials` as s
                    ON s.`product_id` = p.`id`
                WHERE p.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql5);

            // Inventory
            // 2 tables: inventories txreport
            $sql6 = '
                DELETE i.*, tx.*
                FROM `inventories` as i
                LEFT JOIN `txreport` as tx
                    ON tx.`inventory_id` = i.`id`
                WHERE i.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql6);
            #pr($sql6);

            // Orders
            // 6 tables: orders, orders_lines, orders_schedule, orderslines_waves, orders_blanket, orders_costs
            $sql7 = '
                DELETE o.*, ol.*, os.*, olw.*, ob.*, oc.*
                FROM `orders` as o
                LEFT JOIN `orders_lines` as ol
                    ON ol.`order_id` = o.`id`
                LEFT JOIN `orderslines_waves` as olw
                    ON olw.`ordersline_id` = ol.`id`
                LEFT JOIN `orders_schedule` as os
                    ON os.`order_id` = o.`id`
                LEFT JOIN `orders_blanket` as ob
                    ON ob.`order_id` = o.`id`
                LEFT JOIN `orders_costs` as oc
                    ON oc.`order_id` = o.`id`
                WHERE o.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql7);
            #pr($sql7);

            // DELETE USER
            $sql8 = '
                DELETE FROM `users` WHERE `id` = "'. $user['User']['id'] .'";
            ';
            $r[$user['User']['id']][] = $this->User->query($sql8);

        $this->Session->setFlash(__('The user has been deleted.'),'admin/danger');
        return $this->redirect(array('action' => 'noactive'));
    }
}