<?php
App::uses('AppController', 'Controller');
/**
 * PackagingMaterials Controller
 *
 * @property PackagingMaterial $PackagingMaterial
 * @property PaginatorComponent $Paginator
 */
class PackagingMaterialsController extends AppController {

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
		$this->PackagingMaterial->recursive = 0;
		$this->set('packagingMaterials', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->PackagingMaterial->exists($id)) {
			throw new NotFoundException(__('Invalid packaging material'));
		}
		$options = array('conditions' => array('PackagingMaterial.' . $this->PackagingMaterial->primaryKey => $id));
		$this->set('packagingMaterial', $this->PackagingMaterial->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PackagingMaterial->create();
			if ($this->PackagingMaterial->save($this->request->data)) {
				$this->Session->setFlash(__('The packaging material has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The packaging material could not be saved. Please, try again.'));
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
		if (!$this->PackagingMaterial->exists($id)) {
			throw new NotFoundException(__('Invalid packaging material'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->PackagingMaterial->save($this->request->data)) {
				$this->Session->setFlash(__('The packaging material has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The packaging material could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('PackagingMaterial.' . $this->PackagingMaterial->primaryKey => $id));
			$this->request->data = $this->PackagingMaterial->find('first', $options);
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
		$this->PackagingMaterial->id = $id;
		if (!$this->PackagingMaterial->exists()) {
			throw new NotFoundException(__('Invalid packaging material'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->PackagingMaterial->delete()) {
			$this->Session->setFlash(__('The packaging material has been deleted.'));
		} else {
			$this->Session->setFlash(__('The packaging material could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
