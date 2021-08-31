<?php
App::uses('AppController', 'Controller');
/**
 * OrderslinesWaves Controller
 *
 * @property OrderslinesWave $OrderslinesWave
 * @property PaginatorComponent $Paginator
 */
class OrderslinesWavesController extends AppController {

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
		$this->OrderslinesWave->recursive = 0;
		$this->set('orderslinesWaves', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->OrderslinesWave->exists($id)) {
			throw new NotFoundException(__('Invalid orderslines wave'));
		}
		$options = array('conditions' => array('OrderslinesWave.' . $this->OrderslinesWave->primaryKey => $id));
		$this->set('orderslinesWave', $this->OrderslinesWave->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->OrderslinesWave->create();
			if ($this->OrderslinesWave->save($this->request->data)) {
				$this->Session->setFlash(__('The orderslines wave has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The orderslines wave could not be saved. Please, try again.'));
			}
		}
		$waves = $this->OrderslinesWave->Wave->find('list');
		$orderslines = $this->OrderslinesWave->Ordersline->find('list');
		$this->set(compact('waves', 'orderslines'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		if (!$this->OrderslinesWave->exists($id)) {
			throw new NotFoundException(__('Invalid orderslines wave'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->OrderslinesWave->save($this->request->data)) {
				$this->Session->setFlash(__('The orderslines wave has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The orderslines wave could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('OrderslinesWave.' . $this->OrderslinesWave->primaryKey => $id));
			$this->request->data = $this->OrderslinesWave->find('first', $options);
		}
		$waves = $this->OrderslinesWave->Wave->find('list');
		$orderslines = $this->OrderslinesWave->Ordersline->find('list');
		$this->set(compact('waves', 'orderslines'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->OrderslinesWave->id = $id;
		if (!$this->OrderslinesWave->exists()) {
			throw new NotFoundException(__('Invalid orderslines wave'));
		}

		if ($this->OrderslinesWave->delete()) {
			$this->Session->setFlash(__('The orderslines wave has been deleted.'));
		} else {
			$this->Session->setFlash(__('The orderslines wave could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
