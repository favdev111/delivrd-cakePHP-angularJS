<?php
App::uses('AppController', 'Controller');
/**
 * Schannels Controller
 *
 * @property Schannel $Schannel
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SchannelsController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Search.Prg');
	public $theme = 'Mtro';

	public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to add Sales Channels.'), 'admin/danger');
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
		$title = 'Add Sales Channel';
		$limit = $this->Auth->user('list_limit');
		$this->Paginator->settings = array(
        	'limit' => $limit
    	);
		$this->Schannel->recursive = 0;		
        $conditions['Schannel.user_id'] = $this->Auth->user('id');

	    //$conditions['Serial.instock'] = 1;
		$this->set('schannels', $this->Paginator->paginate($conditions));
		
		if($this->request->is('post')) {
            $this->Prg->commonProcess(null, ['paramType'=>'query']);
        }

        $conditions = [];
        $conditions['Schannel.user_id'] = $this->Auth->user('id');
        if($this->request->query('name')) {
        	$conditions['Schannel.name LIKE'] = '%'. $this->request->query('name') .'%';
        }

        $this->set('schannels', $this->Paginator->paginate($conditions));
		$this->set(compact('title'));

	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Schannel->exists($id)) {
			throw new NotFoundException(__('Invalid schannel'));
		}
		$options = array('conditions' => array('Schannel.' . $this->Schannel->primaryKey => $id));
		$this->set('schannel', $this->Schannel->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() { 
		if ($this->request->is('ajax')) {
			if(!empty($this->request->data)) {
				$this->request->data['Schannel']['user_id'] = $this->Session->read('Auth.User.id');
				if ($this->Schannel->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Sales channel has been added.'; 
	            } else {
	               $response['status'] = false;
	               $Schannel = $this->Schannel->invalidFields();
	               $response['data']=compact('Schannel');
	               $response['message']='The Sales channel could not be saved. Please, try again.';
	            }
	            echo json_encode($response);
	            die;
			}
		} 
	}

	/**
	 * add_channel method
	 *
	 * @return void
	 */
	public function add_channel() {
		$this->layout = false;
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Schannel']['user_id'] = $this->Session->read('Auth.User.id');
			if ($this->Schannel->saveAll($this->request->data)) {
                $response['action'] = 'success';
                $response['message'] = 'The Sales channel has been saved.';
                $this->Schannel->recursive = -1;
                $response['schannel'] = $this->Schannel->find('first',array('fields' => ['id', 'name'], 'conditions' => array('Schannel.id' => $this->Schannel->id)));
            } else {
               $response['action'] = 'error';
               $response['errors'] = $this->Schannel->validationErrors;
               $response['message']='The Sales channel could not be saved. Please, try again.';
            }
            echo json_encode($response);
            die;
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
            $schannel = array();
            $errors = array();

            // Get current loggged in user id to map schannel with user
            $userId = $this->Auth->user('id');

            // Set user id 
            $this->request->data[$this->modelClass]['user_id'] = $userId;
          
            // Save new schannel in database
            if ($this->{$this->modelClass}->save($this->request->data)) {
                // Load schannel
              $schannel = $this->{$this->modelClass}->find('list', array('conditions' => array('Schannel.user_id' => $userId)));
              $id =$this->{$this->modelClass}->id;

            } else {
                // Get validation errors
                $errors = (!$userId) ? array('auth' => 'You have looged out. Please login again.') : $this->{$this->modelClass}->validationErrors;
            }
        } else {
            throw new NotFoundException('404 error.');
        }
       
        // Set varriables for use on view ctp file
        $this->set(compact('schannel', 'errors', 'id'));
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
		$this->request->data = $this->Schannel->read(null, $id);
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Schannel->id = $id;
		if (!$this->Schannel->exists()) {
			throw new NotFoundException(__('Invalid schannel'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Schannel->delete($id)) {
			$this->Session->setFlash(__('The Sales channel has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Sales channel not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function exportcsv() {
		
	    $schannels = $this->Schannel->find('all',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
	    $_serialize = 'schannels';
	    $_header = array('Id', 'Name', 'URL');
	    $_extract = array('Schannel.id', 'Schannel.name', 'Schannel.url');

		$file_name = "Delivrd_".date('Y-m-d-His')."_schannels.csv";
		$this->response->download($file_name);
	    $this->viewClass = 'CsvView.Csv';
	    $this->set(compact('schannels', '_serialize', '_header', '_extract'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
