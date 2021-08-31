<?php
App::uses('AppController', 'Controller');
/**
 * Schannels Controller
 *
 * @property Schannel $Schannel
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class AdsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Search.Prg');
	public $theme = 'Mtro';
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->layout = 'mtrd';
				$this->layout = 'mtrd';
		$this->Paginator->settings = array(
        'limit' => 10
    );
		$this->Ad->recursive = 0;		
       $conditions['Ad.user_id'] = $this->Auth->user('id');

	    //$conditions['Serial.instock'] = 1;
		$this->set('ads', $this->Paginator->paginate($conditions));
		
		$this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Ad->parseCriteria($this->Prg->parsedParams());
		// If we are in search mode, paginator should be search results. else, we display all results
		if(isset($this->viewVars['isSearch']))
		{

        $conditions['Ad.user_id'] = $this->Auth->user('id');
		$this->set('ads', $this->Paginator->paginate($conditions));
		}
		
		

	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Ad->exists($id)) {
			throw new NotFoundException(__('Invalid Courier'));
		}
		$options = array('conditions' => array('Ad.' . $this->Ad->primaryKey => $id));
		$this->set('courier', $this->Ad->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->layout = 'mtrd';
		if ($this->request->is('post')) {
			$this->Ad->create();
			$this->request->data('Ad.user_id',$this->Auth->user('id'));
			if ($this->Ad->save($this->request->data)) {
				$this->Session->setFlash(__('The ad has been saved.'),'default',array('class'=>'alert alert-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ad could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
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
		$this->layout = 'mtrd';
		if (!$this->Ad->exists($id)) {
			throw new NotFoundException(__('Invalid schannel'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Ad->save($this->request->data)) {
				$this->Session->setFlash(__('The ad has been saved.'),'default',array('class'=>'alert alert-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The courier could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
			}
		} else {
			$options = array('conditions' => array('Ad.' . $this->Ad->primaryKey => $id));
			$this->request->data = $this->Ad->find('first', $options);
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
		$this->Ad->id = $id;
		if (!$this->Ad->exists()) {
			throw new NotFoundException(__('Invalid schannel'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Ad->delete()) {
			$this->Session->setFlash(__('The courier has been deleted.'),'default',array('class'=>'alert alert-success'));
		} else {
			$this->Session->setFlash(__('The courier could not be deleted. Please, try again.'),'default',array('class'=>'alert alert-danger'));
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
    public function beforeRender()
    {
        if($this->Auth->user('is_admin') != 1)
             return $this->redirect('/');
        $this->response->disableCache();
    }
}
