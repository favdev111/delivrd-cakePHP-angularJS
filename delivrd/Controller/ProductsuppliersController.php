<?php
App::uses('AppController', 'Controller');
/**
 * Bins Controller
 *
 */
class ProductsuppliersController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Search.Prg', 'Csv.Csv');
    public $theme = 'Mtro';

    public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to supplier product.'), 'admin/danger');
            return $this->redirect('/');
        }
        return parent::isAuthorized($user);
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() { 
        $conditions = array();
        $conditions = array('Product.user_id' => $this->Auth->user('id'), 'Supplier.user_id' => $this->Auth->user('id'));

        if (!empty($this->request->data['Productsupplier']['searchby'])) {
           $conditions['OR'] = array('Product.name' => $this->request->data['Productsupplier']['searchby'], 'Product.sku' => $this->request->data['Productsupplier']['searchby']);
        }
        if (!empty($this->request->data['Productsupplier']['supplier_id'])) {
           $conditions['Productsupplier.supplier_id'] =  $this->request->data['Productsupplier']['supplier_id'];
        }
        $this->paginate = array(
          'conditions' => $conditions,
          'fields' => array('Product.id', 'Product.name', 'Product.sku', 'Supplier.id', 'Supplier.name', 'Productsupplier.id', 'Productsupplier.part_number', 'Productsupplier.status', 'Productsupplier.created'),
          'limit' => 10
        );
        $data = $this->paginate('Productsupplier');

        /*try {
            $data = $this->paginate('Productsupplier');
        } catch (NotFoundException $e) {
            $this->outOfPageRangeRedirect(array('action' => 'index'));
        }*/
        $suppliers = $this->Productsupplier->Supplier->find('list',array('conditions' => array('Supplier.user_id' => $this->Auth->user('id'))));
        $this->set(compact('data', 'suppliers'));
    }

    public function uploadcsv() {
        $this->layout = 'mtru';
        
        $target_path = '/var/www/html/uploads/';
        if ($this->request->is('post')) {
            $target_path = WWW_ROOT."uploads/";
            $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            
            $filesg = array( 'files' => array(array(
                "name" => $_FILES['uploadedfile']['name'],
                "size" => $_FILES['uploadedfile']['size'],
                "thumbnailUrl" => "/theme/Mtro/assets/admin/layout/img/csv.png"
            )));
            header('Content-Type: application/json');
            echo json_encode($filesg,JSON_PRETTY_PRINT);
            exit(); 
            
            //$this->importcsv($target_path,$_FILES['uploadedfile']['name'],$_FILES['uploadedfile']['size']);
                
            } else{
            $filesg = array( 'files' => array(array(
                "name" => $_FILES['uploadedfile']['name'],
                "size" => $_FILES['uploadedfile']['size'],
                "error" => "Could not upload file. Please try again",
            )));
            header('Content-Type: application/json');
            echo json_encode($filesg,JSON_PRETTY_PRINT);
            exit(); 
            }
        }
    }

    public function downloadsamplefile() {
        $conditions = array();
        $conditions = array('Product.user_id' => $this->Auth->user('id'), 'Supplier.user_id' => $this->Auth->user('id'));
        $productsuppliers = $this->Productsupplier->find('all',array('conditions' => $conditions,'fields' => array('Product.id', 'Product.sku', 'Supplier.id', 'Supplier.name', 'Productsupplier.id', 'Productsupplier.status')));
        $_serialize = 'productsuppliers';
        $_header = array('SKU', 'SupplierName', 'Status');
        $_extract = array('Product.sku', 'Supplier.name', 'Productsupplier.status');

        $file_name = "Delivrd_".date('Y-m-d-His')."_productsuppliers.csv";
        $this->response->download($file_name);
        $this->viewClass = 'CsvView.Csv';
        $this->set(compact('productsuppliers', '_serialize', '_header', '_extract'));

    }

    public function importcsv($filename = null) {

        ini_set("auto_detect_line_endings", "1");
        $this->layout = 'mtru';
        $options = array(
        // Refer to php.net fgetcsv for more information
        'length' => 0,
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\',
        // Generates a Model.field headings row from the csv file
        'headers' => false, 
        // If true, String $content is the data, not a path to the file
        'text' => false,
        );
        $newproducts = array();
        $newinventory = array();
        
        $content = WWW_ROOT."uploads/".$filename;
       
       //first, get number of columns to see if this is a basic file or full file
        $numcols = 0;
        $file = fopen($content, "r");
       

          while ($line = fgetcsv($file))
          {
                $numcols = count($line);
                 if($numcols != 3)
                    {
                        $this->showimporterror("CSV file should have 3 columns, but line ".$line[0]." has ".$numcols." columns");
                        fclose($file);
                    }
          }
        fclose($file);
        
            $filedata = $this->Csv->import($content, array('SKU', 'SupplierName', 'Status'));
        
            $errorstr = ".";
            $numinvs = count($filedata);
            
            foreach ($filedata as $key=>$psupplierdata)
            {
                
                if($key > 0)
                {
                    
                    if(empty($psupplierdata['Productsupplier']['SKU'] || empty($psupplierdata['Productsupplier']['SupplierName'] || empty($psupplierdata['Productsupplier']['Status']))))
                    {
                        $this->showimporterror("One column is missing in line ".$key);  
                    }
            
                    $productid = $this->Productsupplier->Product->find('list', array('conditions' => array('Product.sku' => $psupplierdata['Productsupplier']['SKU'],'Product.user_id' => $this->Auth->user('id'))));
                    
                    if(empty($productid))
                    {
                        $this->showimporterror("Product sku" .$psupplierdata['Productsupplier']['SKU']. "in sku" .$key. " does not exist");
                    }   

                    $supplierid = $this->Productsupplier->Supplier->find('list', array('conditions' => array('Supplier.name' => $psupplierdata['Productsupplier']['SupplierName'],'Supplier.user_id' => $this->Auth->user('id'))));
                    //pr();die;
                    if(empty($supplierid))
                    {
                        $this->showimporterror("Supplier name" .$psupplierdata['Productsupplier']['SupplierName']. "in name" .$key. " does not exist");
                    }

                    if(!empty($productid) && !empty($supplierid))
                    {

                        $count = $this->Productsupplier->find('count', array('conditions' => array('Productsupplier.product_id' => key($productid), 'Productsupplier.supplier_id' => key($supplierid))));
                        if(!empty($count))
                            $this->showimporterror("Supplier " .$supplierid[key($supplierid)]. " for product " .$productid[key($productid)]. " already exist");
                    }

                    if (!in_array($psupplierdata['Productsupplier']['Status'], array('yes','no','Yes','No', 'YES', 'NO'))) {
                        $this->showimporterror("Productsupplier status value is in 'yes' or 'no'");
                    }

                    $this->createsupplierrecord(key($productid),key($supplierid),$psupplierdata['Productsupplier']['Status']);
                    
                }
            }

        $this->Session->setFlash('Product-Supplier were created successfully','default',array('class'=>'alert alert-success'));
        return $this->redirect(array('controller' => 'productsuppliers', 'action' => 'index'));
    }

    public function showimporterror($errstr = null)
    {

        $gotoimport = '<a href="/productsuppliers/uploadcsv" class="btn blue-hoki fileinput-button"><i class="fa fa-cloud-upload"></i> Go to upload page</a>';
                $this->Session->setFlash(__('Product-Supplier could not be updated. %s<BR /><BR /> %s',$errstr, $gotoimport),'default',array('class'=>'alert alert-danger'));
        return $this->redirect(array('controller' => 'productsuppliers', 'action' => 'index'));
        
    }

    public function createsupplierrecord($productid = null,$supplier_id = null, $status = null)
    {
        
        $this->Productsupplier->create();
                    
        $this->Productsupplier->set('product_id',$productid);
        $this->Productsupplier->set('supplier_id',$supplier_id);
        $this->Productsupplier->set('status',$status);

        $recordcreated = $this->Productsupplier->save($this->request->data);

        if ($recordcreated) {
           $this->Session->setFlash(__('The Product-Supplier record saved.'),'default',array('class'=>'alert alert-success'));
        } else {
             $this->Session->setFlash(__('The Product-Supplier record could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
        }       
        
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $title = 'Add Product-Supplier Assignment';
        if (!empty($this->request->data)) {
            if ($this->Productsupplier->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The Product-Supplier has been saved.'),'default',array('class'=>'alert alert-info'));
                $this->redirect(array('action' => 'index'));
            } else {
               $this->Session->setFlash(__('The Product-Supplier could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
            }
        }

        $products = $this->Productsupplier->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
        $suppliers = $this->Productsupplier->Supplier->find('list', array('conditions' => array('Supplier.user_id' => $this->Auth->user('id'))));
        $status = $this->Productsupplier->status;
        $this->set(compact('status', 'title', 'products', 'suppliers'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) { 
       $title = 'Edit Product-Supplier Assignment'; 
       if (!empty($this->request->data)) {
            if ($this->Productsupplier->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The Product-Supplier has been saved.'),'default',array('class'=>'alert alert-success'));
                $this->redirect(array('action' => 'index'));
            } else {
               $this->Session->setFlash(__('The Product-Supplier could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
            }
        } else {
            $this->request->data = $this->Productsupplier->read(null, $id);
        }

       $products = $this->Productsupplier->Product->find('list', array('conditions' => array('Product.user_id' => $this->Auth->user('id'),'Product.status_id' => 1)));
       $suppliers = $this->Productsupplier->Supplier->find('list', array('conditions' => array('Supplier.user_id' => $this->Auth->user('id'))));
       $status = $this->Productsupplier->status;
       $this->set(compact('status', 'title', 'products', 'suppliers'));
        
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Product-Supplier'), 'default', array('class' => 'alert alert-danger'));
        }
        if ($this->Productsupplier->delete($id)) {
            $this->Session->setFlash(__('The Product-Supplier has been deleted successfully.'), 'default', array('class'=>'alert alert-success'));
        } else {
            $this->Session->setFlash(__('The Product-Supplier could not be deleted. Please, try again.'), 'default', array('class'=>'alert alert-danger'));
        }
        
        return $this->redirect(array('action' => 'index'));
    }

    public function getsupplierPrice($id = null, $orderId = null) {
        $response['price'] = '';
        if(!empty($id) && !empty($orderId)) {
            $this->loadModel('Order');
            $supplierId = $this->Order->find('first', array('conditions' => array('Order.id' => $orderId), 'fields' => array('supplier_id'),'recursive' => -1));
            if(!empty($supplierId)) {
             $products = $this->Productsupplier->find('first', array('conditions' => array('Productsupplier.supplier_id' => $supplierId['Order']['supplier_id'],'Productsupplier.product_id' => $id), 'fields' => array('Productsupplier.price')));
             if(!empty($products))
                $response['price'] = $products['Productsupplier']['price'];
            }
        }
                
        echo json_encode($response);
        exit;
    } 

    public function beforeRender() {
        $this->response->disableCache();
    }

}
