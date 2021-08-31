<?php
App::uses('AppController', 'Controller');
/**
 * Suppliers Controller
 *
 * @property Supplier $Supplier
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SuppliersController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Search.Prg');
	public $theme = 'Mtro';

	public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to add supplier.'), 'admin/danger', array());
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
		$this->layout = 'mtrd';
    	$conditions = array();
		$conditions = array('Supplier.user_id' => $this->Session->read('Auth.User.id'));
		if (!empty($this->request->data['Supplier']['search'])) {
           $conditions[] = 'Supplier.name like "%' . $this->request->data['Supplier']['search'] . '%"';
        }

        $limit = $this->Auth->user('list_limit');
        $this->paginate = array(
            'conditions' => $conditions,
            'limit' => $limit
        );
        $this->set('suppliers', $this->Paginator->paginate($conditions));

        /*try {
            $this->set('suppliers', $this->Paginator->paginate($conditions));
        } catch (NotFoundException $e) {
            $this->outOfPageRangeRedirect(array('action' => 'index'));
        }*/

		$supplysources = $this->Supplier->Supplysource->find('list',array('conditions' => array('user_id' => $this->Auth->user('id'))));
		$this->set(compact('supplysources'));
		
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
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function editAddress($id = null) {
		$this->layout = false;
		$this->request->data = $this->Supplier->read(null, $id);
		$countries = $this->Supplier->Address->Country->find('list');
		$states = $this->Supplier->Address->State->find('list');
		$this->set(compact('countries','states'));
	}

	/**
	 * saveAddress method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function saveAddress() {
		if ($this->request->is('ajax')) { 
			if(!empty($this->request->data)) {
				$this->request->data('Address.street',htmlspecialchars($this->request->data['Address']['street']));
	            $this->request->data('Address.city',htmlspecialchars($this->request->data['Address']['city']));
	            $this->request->data('Address.stateprovince',htmlspecialchars($this->request->data['Address']['stateprovince']));
	            $this->request->data('Address.zip',htmlspecialchars($this->request->data['Address']['zip']));
	            $this->request->data('Address.phone',htmlspecialchars($this->request->data['Address']['phone']));
				if ($this->Supplier->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Address has been saved.'; 
	            } else {
	               $response['status'] = false;
	               $Supplier = $this->Supplier->invalidFields();
	               $response['data']=compact('Supplier');
	               $response['message']='The Address could not be saved. Please, try again.';
	            }
	            echo json_encode($response);
	            die;
			}
		} 
	}

	/**
	 * add method
	 *
	 * @return void

	 */
	public function add() { 
		if ($this->request->is('ajax')) {
			if(!empty($this->request->data)) {
				$this->request->data['Supplier']['user_id'] = $this->Session->read('Auth.User.id');
				if ($this->Supplier->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Supplier has been saved.'; 
	            } else {
	               $response['status'] = false;
	               $Supplier = $this->Supplier->invalidFields();
	               $response['data']=compact('Supplier');
	               $response['message']='The Supplier could not be saved. Please, try again.';
	            }
	            echo json_encode($response);
	            die;
			}
		} 
	}

	/**
	 * create method
	 *
	 * @return void
	 */
	public function create() { 
		if ($this->request->is('ajax')) { 
            // Instialize varriables
            $supplier = array();
            $errors = array();

            // Get current loggged in user id to map supplier with user
            $userId = $this->Auth->user('id');

            // Set user id 
            $this->request->data[$this->modelClass]['user_id'] = $userId;
          
            // Save new supplier in database
            if ($this->{$this->modelClass}->save($this->request->data)) {
                // Load supplier
              $supplier = $this->{$this->modelClass}->find('list', array('conditions' => array('Supplier.user_id' => $userId)));
              $id =$this->{$this->modelClass}->id;

            } else {
                // Get validation errors
                $errors = (!$userId) ? array('auth' => 'You have logged out. Please login again.') : $this->{$this->modelClass}->validationErrors;
            }
        } else {
            throw new NotFoundException('404 error.');
        }
       
        // Set varriables for use on view ctp file
        $this->set(compact('supplier', 'errors', 'id'));
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
		$this->request->data = $this->Supplier->read(null, $id);
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Supplier->id = $id;
		if (!$this->Supplier->exists()) {
			throw new NotFoundException(__('Invalid supplier'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Supplier->delete()) {
			$this->Session->setFlash(__('The Supplier has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Supplier could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function exportcsv() {	
	  	$suppliers = $this->Supplier->find('all',array('conditions' => array('Supplier.user_id' => $this->Auth->user('id'))));
	    $_serialize = 'suppliers';
	    $_header = array('Id', 'Name', 'Supplysource', 'Email','URL');
	    $_extract = array('Supplier.id', 'Supplier.name', 'Supplysource.name', 'Supplier.email', 'Supplier.url');

		$file_name = "Delivrd_".date('Y-m-d-His')."_suppliers.csv";
		$this->response->download($file_name);
	    $this->viewClass = 'CsvView.Csv';
	    $this->set(compact('suppliers', '_serialize', '_header', '_extract'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
