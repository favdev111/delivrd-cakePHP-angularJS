<?php
App::uses('AppController', 'Controller');
/**
 * Bins Controller
 *
 */
class BinsController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Search.Prg', 'Csv.Csv');
	public $theme = 'Mtro';

	public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to add Bins.'), 'admin/danger');
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
		$title = 'Add Bin';
		$conditions = array();
		$conditions = array('Bin.user_id' => $this->Session->read('Auth.User.id'));
		if (!empty($this->request->data['Bin']['title'])) {
            $conditions[] = 'Bin.title like "%' . $this->request->data['Bin']['title'] . '%"';
        }
        if (!empty($this->request->data['Bin']['location_id'])) {
            $conditions[] = array('Bin.location_id' => $this->request->data['Bin']['location_id']);
        }
        if (isset($this->request->data['Bin']['status']) && $this->request->data['Bin']['status'] != null) {
            $conditions[] = array('Bin.status' => $this->request->data['Bin']['status']);
        }

        $limit = $this->Auth->user('list_limit');
		$this->paginate = array(
            'conditions' => $conditions,
            'limit' => $limit
            );
		$data = $this->paginate('Bin');

        /*try {
            $data = $this->paginate('Bin');
        } catch (NotFoundException $e) {
            $this->outOfPageRangeRedirect(array('action' => 'index'));
        }*/
        $locations = $this->Bin->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));
        $status = $this->Bin->status;
       
		$this->set(compact('data','locations','status', 'title'));
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
		
		$bins = $this->Bin->find('all',array('conditions' => array('Bin.user_id' => $this->Auth->user('id'))));
	    $_serialize = 'bins';
	    $_header = array('title', 'sort_sequence', 'location');
	    $_extract = array('Bin.title', 'Bin.sort_sequence', 'Warehouse.name');

		$file_name = "Delivrd_".date('Y-m-d-His')."_bins.csv";
		$this->response->download($file_name);
	    $this->viewClass = 'CsvView.Csv';
	    $this->set(compact('bins', '_serialize', '_header', '_extract'));

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
	   

		while ($line = fgetcsv($file)) {
			$numcols = count($line);
	        if($numcols != 3) {
	            $this->showimporterror("CSV file should have 3 columns, but line ".$line[0]." has ".$numcols." columns");
	            fclose($file);
	        }
		}
	   	fclose($file);

	
		$filedata = $this->Csv->import($content, array('title', 'sort_sequence', 'location'));
	
		$errorstr = ".";
		$numinvs = count($filedata);
		
		foreach ($filedata as $key=>$bindata)
		{
			
			if($key > 0)
			{
				
				if(empty($bindata['Bin']['title'] || empty($bindata['Bin']['sort_sequence'] || empty($bindata['Bin']['location']))))
				{
					$this->showimporterror("Title or order number are missing in line ".$key);	
				}
		
		        $warhouseid = $this->Bin->Warehouse->find('list', array('conditions' => array('Warehouse.name' => $bindata['Bin']['location'],'Warehouse.user_id' => $this->Auth->user('id'))));
		       
		      	if(empty($warhouseid))
		        {
		            $this->showimporterror("Location " .$bindata['Bin']['location']. "in wholesale" .$key. " does not exist");
		        }	
				
				$this->createbinrecord($bindata['Bin']['title'],key($warhouseid),$bindata['Bin']['sort_sequence']);
				
	  		}
    	}

	    $this->Session->setFlash(__('Bin were created successfully'), 'admin/success', array());
	  	return $this->redirect(array('controller' => 'bins', 'action' => 'index'));
	}

	public function showimporterror($errstr = null)
    {

	$gotoimport = '<a href="/bins/uploadcsv" class="btn blue-hoki fileinput-button"><i class="fa fa-cloud-upload"></i> Go to upload page</a>';
		$this->Session->setFlash(__('Bin could not be updated. %s',$errstr), 'admin/danger', array());
		return $this->redirect(array('controller' => 'bins', 'action' => 'index'));
        
    }

    public function createbinrecord($title = null,$warehouseid = null, $order = null)
	{
		
		$this->Bin->create();
                    
        $this->Bin->set('user_id',$this->Auth->user('id'));
		$this->Bin->set('title',$title);
		$this->Bin->set('sort_sequence',$order);
		$this->Bin->set('location_id',$warehouseid);
		$this->Bin->set('status',1);

        $binrecordcreated = $this->Bin->save($this->request->data);

        if ($binrecordcreated) {
        	$this->Session->setFlash(__('The Bin record saved.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Bin record could not be saved. Please, try again.'), 'admin/danger', array());
		}		
		
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Supplier->exists($id)) {
			throw new NotFoundException(__('Invalid supplier'));
		}
		$options = array('conditions' => array('Supplier.' . $this->Supplier->primaryKey => $id));
		$this->set('supplier', $this->Supplier->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void

	 */
	public function add() { 
		if ($this->request->is('ajax')) {
			if(!empty($this->request->data)) {
				$this->request->data['Bin']['user_id'] = $this->Session->read('Auth.User.id');
				if ($this->Bin->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Bin has been saved.'; 
	            } else {
	               $response['status'] = false;
	               $Bin = $this->Bin->invalidFields();
	               $response['data']=compact('Bin');
	               $response['message']='The Bin could not be saved. Please, try again.';
	            }
	            echo json_encode($response);
	            die;
			}
		} 
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->layout = false;
		$status = $this->Bin->status;
		$locations = $this->Bin->Warehouse->find('list',array('conditions' => array('Warehouse.user_id' => $this->Auth->user('id'))));
		$this->request->data = $this->Bin->read(null, $id);
		$this->set(compact('status', 'locations'));
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
            $this->Session->setFlash(__('Invalid id for Bin'), 'default', array('class' => 'alert alert-danger'));
        }
        if ($this->Bin->delete($id)) {
        	$this->Session->setFlash(__('The Bin has been deleted successfully.'), 'admin/success', array()); 
        } else {
        	$this->Session->setFlash(__('The Bin could not be deleted. Please, try again.'), 'admin/danger', array());
        }
		
		return $this->redirect(array('action' => 'index'));
	}

	public function create() {
 	  //if ($this->request->is('ajax')) 
            if(1 == 1){
            // Instialize varriables
            $bin = array();
            $errors = array();

            // Get current loggged in user id to map cateotry with user
            $userId = $this->Auth->user('id');

            // Set user id 
            $this->request->data[$this->modelClass]['user_id'] = $userId;
          
            // Save new bin in database
            if ($this->{$this->modelClass}->save($this->request->data)) {
                // Load categories
                $bin = $this->{$this->modelClass}->find('list', array('conditions' => array('Bin.user_id' => $userId, 'Bin.status' => 1)));
                $id =$this->{$this->modelClass}->id;

            } else {
                // Get validation errors
                $errors = (!$userId) ? array('auth' => 'You have logged out. Please login again.') : $this->Bin->validationErrors;
            }
        } else {
            throw new NotFoundException('404 error.');
        }
       
        $this->set(compact('bin', 'errors', 'id'));
    }
	
	public function beforeRender() {
        $this->response->disableCache();
    }

}
