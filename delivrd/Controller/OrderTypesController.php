<?php
App::uses('AppController', 'Controller');
/**
 * Ordertypes Controller
 *
 * @property Ordertype $Ordertype
 * @property PaginatorComponent $Paginator
 */
class OrdertypesController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
                   
    public function beforeFilter() {
        $this->redirect($this->referer());
    }

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->Ordertype->recursive = 0;
		$this->set('ordertypes', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Ordertype->exists($id)) {
			throw new NotFoundException(__('Invalid ordertype'));
		}
		$options = array('conditions' => array('Ordertype.' . $this->Ordertype->primaryKey => $id));
		$this->set('ordertype', $this->Ordertype->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Ordertype->create();
			if ($this->Ordertype->save($this->request->data)) {
				$this->Session->setFlash(__('The ordertype has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ordertype could not be saved. Please, try again.'));
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
		if (!$this->Ordertype->exists($id)) {
			throw new NotFoundException(__('Invalid ordertype'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Ordertype->save($this->request->data)) {
				$this->Session->setFlash(__('The ordertype has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ordertype could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Ordertype.' . $this->Ordertype->primaryKey => $id));
			$this->request->data = $this->Ordertype->find('first', $options);
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
		$this->Ordertype->id = $id;
		if (!$this->Ordertype->exists()) {
			throw new NotFoundException(__('Invalid ordertype'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Ordertype->delete()) {
			$this->Session->setFlash(__('The ordertype has been deleted.'));
		} else {
			$this->Session->setFlash(__('The ordertype could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->Ordertype->recursive = 0;
		$this->set('ordertypes', $this->Paginator->paginate());
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		if (!$this->Ordertype->exists($id)) {
			throw new NotFoundException(__('Invalid ordertype'));
		}
		$options = array('conditions' => array('Ordertype.' . $this->Ordertype->primaryKey => $id));
		$this->set('ordertype', $this->Ordertype->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Ordertype->create();
			if ($this->Ordertype->save($this->request->data)) {
				$this->Session->setFlash(__('The ordertype has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ordertype could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		if (!$this->Ordertype->exists($id)) {
			throw new NotFoundException(__('Invalid ordertype'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Ordertype->save($this->request->data)) {
				$this->Session->setFlash(__('The ordertype has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ordertype could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Ordertype.' . $this->Ordertype->primaryKey => $id));
			$this->request->data = $this->Ordertype->find('first', $options);
		}
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		$this->Ordertype->id = $id;
		if (!$this->Ordertype->exists()) {
			throw new NotFoundException(__('Invalid ordertype'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Ordertype->delete()) {
			$this->Session->setFlash(__('The ordertype has been deleted.'));
		} else {
			$this->Session->setFlash(__('The ordertype could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
