<?php
App::uses('AppController', 'Controller');
/**
 * BarcodeStandards Controller
 *
 * @property BarcodeStandard $BarcodeStandard
 * @property PaginatorComponent $Paginator
 */
class BarcodeStandardsController extends AppController {

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
		$this->BarcodeStandard->recursive = 0;
		$this->set('barcodeStandards', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->BarcodeStandard->exists($id)) {
			throw new NotFoundException(__('Invalid barcode standard'));
		}
		$options = array('conditions' => array('BarcodeStandard.' . $this->BarcodeStandard->primaryKey => $id));
		$this->set('barcodeStandard', $this->BarcodeStandard->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->BarcodeStandard->create();
			if ($this->BarcodeStandard->save($this->request->data)) {
				$this->Session->setFlash(__('The barcode standard has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The barcode standard could not be saved. Please, try again.'));
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
		if (!$this->BarcodeStandard->exists($id)) {
			throw new NotFoundException(__('Invalid barcode standard'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->BarcodeStandard->save($this->request->data)) {
				$this->Session->setFlash(__('The barcode standard has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The barcode standard could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('BarcodeStandard.' . $this->BarcodeStandard->primaryKey => $id));
			$this->request->data = $this->BarcodeStandard->find('first', $options);
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
		$this->BarcodeStandard->id = $id;
		if (!$this->BarcodeStandard->exists()) {
			throw new NotFoundException(__('Invalid barcode standard'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->BarcodeStandard->delete()) {
			$this->Session->setFlash(__('The barcode standard has been deleted.'));
		} else {
			$this->Session->setFlash(__('The barcode standard could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
