<?php
App::uses('AppController', 'Controller');
/**
 * Warehouses Controller
 *
 * @property Size $Size
 * @property PaginatorComponent $Paginator
 */
class WarehousesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	public $theme = 'Mtro';

	public function beforeRender() {
        $this->response->disableCache();
    }

	public function isAuthorized($user) {
        if($user['is_limited']) {
        	if (in_array($this->action, array('index', 'view', 'add', 'edit', 'delete'))) {
	            $this->Session->setFlash(__('Authorization failed. You have no access to supplier product.'), 'admin/danger');
	            return $this->redirect('/');
	        }
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
		$this->Warehouse->recursive = 0;
		 $conditions = array($type = null);
	
	    if ($this->Auth->user('id')) {
	       $conditions['Warehouse.user_id'] = $this->Auth->user('id');
		} 
		$this->set('warehouses', $this->Paginator->paginate($conditions));
		
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Warehouse->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse'));
		}
		$options = array('conditions' => array('Warehouse.' . $this->Warehouse->primaryKey => $id));
		$this->set('warehouse', $this->Warehouse->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		$this->layout = 'mtrd';
		if ($this->request->is('post')) {
			$this->Warehouse->create();
			$this->request->data('Warehouse.user_id',$this->Auth->user('id'));
			$this->request->data('Address.user_id',$this->Auth->user('id'));
			if(empty($this->request->data['Warehouse']['status'])) {
				$this->request->data['Warehouse']['status'] = 'Inactive';
			}
			if ($this->Warehouse->save($this->request->data)) {
				$this->Session->setFlash(__('The Location has been saved.'), 'admin/success', array());
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Location could not be saved. Please, try again.'), 'admin/danger', array());
			}
		}
		$countries = $this->Warehouse->Address->Country->find('list');
		$states = $this->Warehouse->Address->State->find('list');
		$this->set(compact('countries','states'));
		
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->layout = 'mtrd';
		if (!$this->Warehouse->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if(empty($this->request->data['Warehouse']['status'])) {
				$this->request->data['Warehouse']['status'] = 'Inactive';
			}
			if ($this->Warehouse->save($this->request->data)) {				
				$this->Session->setFlash(__('The Location has been saved.'), 'admin/success');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Location could not be saved. Please, try again.'), 'admin/danger');
			}
		} else {
			$options = array('conditions' => array('Warehouse.' . $this->Warehouse->primaryKey => $id), 'contain' => array('Address'));
			$this->request->data = $this->Warehouse->find('first', $options);
		}
		$countries = $this->Warehouse->Address->Country->find('list');
		$states = $this->Warehouse->Address->State->find('list');
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
				if ($this->Warehouse->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Address has been saved.'; 
	            } else {
	               $response['status'] = false;
	               $Warehouse = $this->Warehouse->invalidFields();
	               $response['data']=compact('Warehouse');
	               $response['message']='The Address could not be saved. Please, try again.';
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
	public function editAddress($id = null) {
		$this->layout = false;
		$this->request->data = $this->Warehouse->read(null, $id);
		$countries = $this->Warehouse->Address->Country->find('list');
		$states = $this->Warehouse->Address->State->find('list');
		$this->set(compact('countries','states'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Warehouse->id = $id;
		if (!$this->Warehouse->exists()) {
			throw new NotFoundException(__('Invalid warehouse'));
		}
		//If location has inventory record exists
		$this->loadModel('Inventory');
		$locationhasinv = $this->Inventory->find('count', array('conditions' => array('Inventory.warehouse_id' => $id, 'Inventory.user_id' =>$this->Auth->user('id'))));
		if($locationhasinv > 0)
		    {
		    	$this->Session->setFlash(__('The Location could not be deleted because inventory records exist for it.'), 'admin/danger', array());
				return $this->redirect(array('action' => 'index'));
			}
		$this->request->allowMethod('post', 'delete');
		if ($this->Warehouse->delete()) {
			$this->Session->setFlash(__('The Location has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Location could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function isactive() {
		if ($this->request->is('ajax')) {
			$this->Warehouse->id = $this->request->data['id'];
			if($this->request->data['status'] == 1) {
				$this->request->data('Warehouse.status', 'active');
				$st_mess = 'enabled';
			} else {
				$this->request->data('Warehouse.status', 'inactive');
				$st_mess = 'disabled';
			}
			
			if ($this->Warehouse->save($this->request->data)) {
                $response['status'] = 'success';
                $response['message'] = 'Location was '. $st_mess; 
            } else {
               $response['status'] = 'error';
               $response['message'] = 'Location could not be saved. Please, try again.';
            }
            echo json_encode($response);
            die;
		} 
	}

	
}
