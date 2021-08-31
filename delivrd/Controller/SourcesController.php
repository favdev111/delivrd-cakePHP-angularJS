<?php
App::uses('AppController', 'Controller');
/**
 * Sources Controller
 *
 * @property Source $Source
 * @property PaginatorComponent $Paginator
 */
class SourcesController extends AppController {

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
		$this->Source->recursive = 0;
		$this->set('sources', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Source->exists($id)) {
			throw new NotFoundException(__('Invalid source'));
		}
		$options = array('conditions' => array('Source.' . $this->Source->primaryKey => $id));
		$this->set('source', $this->Source->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Source->create();
			if ($this->Source->save($this->request->data)) {
				$this->Session->setFlash(__('The source has been saved.'),'default',array('class'=>'flash-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The source could not be saved. Please, try again.'),'default',array('class'=>'flash-error'));
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
		if (!$this->Source->exists($id)) {
			throw new NotFoundException(__('Invalid source'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Source->save($this->request->data)) {
				$this->Session->setFlash(__('The source has been saved.'),'default',array('class'=>'flash-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The source could not be saved. Please, try again.'),'default',array('class'=>'flash-error'));
			}
		} else {
			$options = array('conditions' => array('Source.' . $this->Source->primaryKey => $id));
			$this->request->data = $this->Source->find('first', $options);
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
		$this->Source->id = $id;
		if (!$this->Source->exists()) {
			throw new NotFoundException(__('Invalid source'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Source->delete()) {
			$this->Session->setFlash(__('The source has been deleted.'),'default',array('class'=>'flash-success'));
		} else {
			$this->Session->setFlash(__('The source could not be deleted. Please, try again.'),'default',array('class'=>'flash-error'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
