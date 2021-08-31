<?php
App::uses('AppController', 'Controller');
/**
 * Supplysources Controller
 *
 * @property Supplysource $Supplysource
 * @property PaginatorComponent $Paginator
 */
class SupplysourcesController extends AppController {

/**
 * Components
 *
 * @var array 
 */
	public $components = array('Paginator','Csv.Csv','Search.Prg');
	public $theme = 'Mtro';
//No access to  controller, it is no longer relevant                    
     public function beforeFilter() {
       
        $this->redirect($this->referer());
    }
/**
 * index method
 *
 * @return void
 */
	public function index() {

	$this->layout = 'mtrd';
		$this->Paginator->settings = array(
        'limit' => 10
    );
		$this->Supplysource->recursive = 1;
		if ($this->Auth->user('id')) {
       $conditions['Supplysource.user_id'] = $this->Auth->user('id');
	    }
	    //$conditions['Serial.instock'] = 1;
		$this->set('supplysources', $this->Paginator->paginate($conditions));
		
		$this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Supplysource->parseCriteria($this->Prg->parsedParams());
		// If we are in search mode, paginator should be search results. else, we display all results
		if(isset($this->viewVars['isSearch']))
		{
		if ($this->Auth->user('id')) {
        $conditions['Supplysource.user_id'] = $this->Auth->user('id');
		}
		$this->set('supplysources', $this->Paginator->paginate($conditions));
		}
		$stypes = $this->Supplysource->Stype->find('list');
        $this->set(compact('stypes'));

	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->layout = 'mtrd';
		if (!$this->Supplysource->exists($id)) {
			throw new NotFoundException(__('Invalid supplysource'));
		}
		$options = array('conditions' => array('Supplysource.' . $this->Supplysource->primaryKey => $id));
		$this->set('supplysource', $this->Supplysource->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->layout = 'mtrd';
		if ($this->request->is('post')) {
			$this->Supplysource->create();
			$this->request->data('Supplysource.user_id',$this->Auth->user('id'));
			//$this->request->data('Supplysource.email',$this->request->data('email'));
		
			if ($this->Supplysource->save($this->request->data)) {
				$this->Session->setFlash(__('The supply source has been saved.'),'default',array('class'=>'alert alert-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The supply source could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
			}
		}
		
		$stypes = $this->Supplysource->Stype->find('list');
        $this->set(compact('stypes'));
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
		if (!$this->Supplysource->exists($id)) {
			throw new NotFoundException(__('Invalid supply source'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Supplysource->save($this->request->data)) {
				$this->Session->setFlash(__('The supply source has been saved.'),'default',array('class'=>'alert alert-success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The supply source could not be saved. Please, try again.','default',array('class'=>'alert alert-danger')));
			}
		} else {
			$options = array('conditions' => array('Supplysource.' . $this->Supplysource->primaryKey => $id));
			$this->request->data = $this->Supplysource->find('first', $options);
		}
		$stypes = $this->Supplysource->Stype->find('list');
        $this->set(compact('stypes'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
	
		$this->Supplysource->id = $id;
		if (!$this->Supplysource->exists()) {
			throw new NotFoundException(__('Invalid supplysource'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Supplysource->delete()) {
			$this->Session->setFlash(__('Supply source has been deleted.'),'default',array('class'=>'alert alert-success'));
		} else {
			$this->Session->setFlash(__('Supply source could not be deleted. Please, try again.'),'default',array('class'=>'alert alert-danger'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	public function exportcsv() {	
	  	$supplysources = $this->Supplysource->find('all',array('conditions' => array('user_id' => $this->Auth->user('id'))));
	    $_serialize = 'supplysources';
	    $_header = array('Id', 'Name', 'Type', 'Email','URL');
	    $_extract = array('Supplysource.id', 'Stype.name', 'Supplysource.name', 'Supplysource.email', 'Supplysource.url');

		$file_name = "Delivrd_".date('Y-m-d-His')."_supplysources.csv";
		$this->response->download($file_name);
	    $this->viewClass = 'CsvView.Csv';
	    $this->set(compact('supplysources', '_serialize', '_header', '_extract'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }
}
