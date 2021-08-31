<?php
App::uses('AppController', 'Controller');
/**
 * States Controller
 *
 * @property State $State
 * @property PaginatorComponent $Paginator
 */
class StatesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->State->recursive = 0;
		$this->set('states', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->State->exists($id)) {
			throw new NotFoundException(__('Invalid state'));
		}
		$options = array('conditions' => array('State.' . $this->State->primaryKey => $id));
		$this->set('state', $this->State->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->State->create();
			if ($this->State->save($this->request->data)) {
				$this->Session->setFlash(__('The state has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The state could not be saved. Please, try again.'));
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
		if (!$this->State->exists($id)) {
			throw new NotFoundException(__('Invalid state'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->State->save($this->request->data)) {
				$this->Session->setFlash(__('The state has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The state could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('State.' . $this->State->primaryKey => $id));
			$this->request->data = $this->State->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->State->id = $id;
		if (!$this->State->exists()) {
			throw new NotFoundException(__('Invalid state'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->State->delete()) {
			$this->Session->setFlash(__('The state has been deleted.'));
		} else {
			$this->Session->setFlash(__('The state could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function stateList()
    {
        $country_id = $this->request->data['country_id'];
        $response['states'] = $this->State->statesList($country_id);
        
        echo json_encode($response);
        exit;
    }

    public function beforeRender() {
        $this->response->disableCache();
    }
}
