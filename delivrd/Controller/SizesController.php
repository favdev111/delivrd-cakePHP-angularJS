<?php
App::uses('AppController', 'Controller');
/**
 * Sizes Controller
 *
 * @property Size $Size
 * @property PaginatorComponent $Paginator
 */
class SizesController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
	public $theme = 'Mtro';

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->layout = 'mtrd';
		$this->Size->recursive = 0;
		 $conditions = array($type = null);
	
	    if ($this->Auth->user('id')) {
	       $conditions['Size.user_id'] = $this->Auth->user('id');
	    }
		$this->set('sizes', $this->Paginator->paginate($conditions));
		
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Size->exists($id)) {
			throw new NotFoundException(__('Invalid size'));
		}
		$options = array('conditions' => array('Size.' . $this->Size->primaryKey => $id));
		$this->set('size', $this->Size->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		$this->layout = 'mtrd';
		if ($this->request->is('post')) {
			$this->Size->create();
			$this->request->data('Size.user_id',$this->Auth->user('id'));
			if ($this->Size->save($this->request->data)) {
				$this->Session->setFlash(__('The Size has been saved.'), 'admin/success', array());
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Size could not be saved. Please, try again.'), 'admin/danger', array());
			}
		}
		$users = $this->Size->User->find('list');
		$this->set(compact('users'));
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
		if (!$this->Size->exists($id)) {
			throw new NotFoundException(__('Invalid size'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Size->save($this->request->data)) {
				$this->Session->setFlash(__('The Size has been saved.'), 'admin/success', array());
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Size could not be saved. Please, try again.'), 'admin/danger', array());
			}
		} else {
			$options = array('conditions' => array('Size.' . $this->Size->primaryKey => $id));
			$this->request->data = $this->Size->find('first', $options);
		}
		$users = $this->Size->User->find('list');
		$this->set(compact('users'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Size->id = $id;
		if (!$this->Size->exists()) {
			throw new NotFoundException(__('Invalid size'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Size->delete()) {
			$this->Session->setFlash(__('The Size has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Size could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
