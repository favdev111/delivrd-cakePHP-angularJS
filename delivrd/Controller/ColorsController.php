<?php
App::uses('AppController', 'Controller');
/**
 * Colors Controller
 *
 * @property Color $Color
 * @property PaginatorComponent $Paginator
 */
class ColorsController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
	public $theme = 'Mtro';
	
	public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to add colors.'), 'admin/danger');
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
		$this->Color->recursive = 0;
		 $conditions = array($type = null);
	
    	if ($this->Auth->user('id')) {
       		$conditions['Color.user_id'] = $this->Auth->user('id');
	    }
		$this->set('colors', $this->Paginator->paginate($conditions));
	
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Color->exists($id)) {
			throw new NotFoundException(__('Invalid color'));
		}
		$options = array('conditions' => array('Color.' . $this->Color->primaryKey => $id));
		$this->set('color', $this->Color->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		$this->layout = 'mtrd';
		if ($this->request->is('post')) {
			if($this->request->data['Color']['htmlcode'][0] == '#')
				$this->request->data['Color']['htmlcode'] = substr($this->request->data['Color']['htmlcode'],1);
			$this->Color->create();
			$this->request->data('Color.user_id',$this->Auth->user('id'));
			if ($this->Color->save($this->request->data)) {
				$this->Session->setFlash(__('The Color has been saved.'), 'admin/success', array());
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Color could not be saved. Please, try again.'), 'admin/danger', array());
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
		if (!$this->Color->exists($id)) {
			throw new NotFoundException(__('Invalid color'));
		}

		if ($this->request->is(array('post', 'put'))) {
			if($this->request->data['Color']['htmlcode'][0] == '#')
				$this->request->data['Color']['htmlcode'] = substr($this->request->data['Color']['htmlcode'],1);
			if ($this->Color->save($this->request->data)) {
				$this->Session->setFlash(__('The Color has been saved.'), 'admin/success', array());
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Color could not be saved. Please, try again.'), 'admin/danger', array());
			}
		} else {
			$options = array('conditions' => array('Color.' . $this->Color->primaryKey => $id));
			$this->request->data = $this->Color->find('first', $options);
		}

		$this->set(compact('curhtmlcode','554433'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Color->id = $id;
		if (!$this->Color->exists()) {
			throw new NotFoundException(__('Invalid color'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Color->delete()) {
			$this->Session->setFlash(__('The Color has been deleted.'), 'admin/success', array());
		} else {
			$this->Session->setFlash(__('The Color could not be deleted. Please, try again.'), 'admin/danger', array());
		}
		return $this->redirect(array('action' => 'index'));
	}
}
