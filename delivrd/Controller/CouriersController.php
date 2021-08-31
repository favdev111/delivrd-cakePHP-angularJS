<?php
App::uses('AppController', 'Controller');
/**
 * Couriers Controller
 *
 * @property Schannel $Schannel
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CouriersController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Search.Prg');
	public $theme = 'Mtro';

    public function beforeFilter() {
       parent::beforeFilter();

    }

    public function beforeRender() {
        parent::beforeRender();

        $this->response->disableCache();
    }
    
    public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to add couriers.'), 'admin/danger');
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

		$limit = $this->Auth->user('list_limit');
		$this->Paginator->settings = array(
        	'limit' => $limit
    	);

		$this->Courier->recursive = 0;		
        $conditions['Courier.user_id'] = $this->Auth->user('id');
     
		$this->set('couriers', $this->Paginator->paginate($conditions));

		if($this->request->is('post')) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }

        $conditions = [];
        $conditions['Courier.user_id'] = $this->Auth->user('id');
        if($this->request->query('name')) {
        	$conditions['Courier.name LIKE'] = '%'. $this->request->query('name') .'%';
        }
		$this->set('couriers', $this->Paginator->paginate($conditions));
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Courier->exists($id)) {
			throw new NotFoundException(__('Invalid Courier'));
		}
		$options = array('conditions' => array('Courier.' . $this->Courier->primaryKey => $id));
		$this->set('courier', $this->Courier->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void

	 */
	public function add() {
		if ($this->request->is('ajax')) {
			if(!empty($this->request->data)) {
				$this->request->data['Courier']['user_id'] = $this->Session->read('Auth.User.id');
				if ($this->Courier->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Courier has been saved.';
	            } else {
	               $response['status'] = false;
	               $Courier = $this->Courier->invalidFields();
	               $response['data']=compact('Courier');
	               $response['message']='The Courier could not be saved. Please, try again.';
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
		$this->request->data = $this->Courier->read(null, $id);
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Courier->id = $id;
		if (!$this->Courier->exists()) {
			throw new NotFoundException(__('Invalid schannel'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Courier->delete()) {
			$this->Session->setFlash(__('The Courier has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Courier could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function exportcsv() {

	  	$couriers = $this->Courier->find('all',array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));
	    $_serialize = 'couriers';
	    $_header = array('Id', 'Name');
	    $_extract = array('Courier.id', 'Courier.name');

		$file_name = "Delivrd_".date('Y-m-d-His')."_couriers.csv";
		$this->response->download($file_name);
	    $this->viewClass = 'CsvView.Csv';
	    $this->set(compact('couriers', '_serialize', '_header', '_extract'));

	}

	/**
	 * create method
	 *
	 * @return void
	 */
	public function create() { 
		if($this->request->data) { 
            $courier = array();
            $errors = array();

            $this->request->data[$this->modelClass]['user_id'] = $this->Auth->user('id');
          
            // Save new courier in database
            if ($this->{$this->modelClass}->save($this->request->data)) {
              $courier = $this->{$this->modelClass}->find('list', array('conditions' => array('Courier.user_id' => $this->Auth->user('id'))));
              $id =$this->{$this->modelClass}->id;
            } else {
                // Get validation errors
                $errors = (!$this->Auth->user('id')) ? array('auth' => 'You have logged out. Please login again.') : $this->{$this->modelClass}->validationErrors;
            }
        } else {
            throw new NotFoundException('404 error.');
        }
       
        $this->set(compact('courier', 'errors', 'id'));
	}
}
