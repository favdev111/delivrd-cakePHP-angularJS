<?php
App::uses('AppController', 'Controller');
/**
 * Resources Controller
 *
 */
class ResourcesController extends AppController {

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


		$conditions = array('Resource.user_id' => $this->Session->read('Auth.User.id'));
		if (!empty($this->request->data['Resource']['name'])) {
            $conditions[] = 'Resource.name like "%' . $this->request->data['Resource']['name'] . '%"';
        }

		$this->paginate = array(
            'conditions' => $conditions,
            );
		$data = $this->paginate('Resource');
        /*try {
            $data = $this->paginate('Resource');
        } catch (NotFoundException $e) {
            $this->outOfPageRangeRedirect(array('action' => 'index'));
        }*/
		$this->set(compact('data'));
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Supplier->exists($id)) {
			throw new NotFoundException(__('Invalid supplier'));
		}
		$options = array('conditions' => array('Supplier.' . $this->Supplier->primaryKey => $id));
		$this->set('supplier', $this->Supplier->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void

	 */
	public function add() { 
		if ($this->request->is('ajax')) {
			if(!empty($this->request->data)) {
				$this->request->data['Resource']['user_id'] = $this->Session->read('Auth.User.id');
				if ($this->Resource->saveAll($this->request->data)) {
	                $response['status'] = true;
	                $response['message'] = 'The Resource has been saved.'; 
	            } else {
	               $response['status'] = false;
	               $Resource = $this->Resource->invalidFields();
	               $response['data']=compact('Resource');
	               $response['message']='The Resource could not be saved. Please, try again.';
	            }
	            echo json_encode($response);
	            die;
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
		$this->layout = false;
		$this->request->data = $this->Resource->read(null, $id);
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$waves = $this->Resource->findById($id);

		if(!empty($waves['Wave'])) {
			$this->Session->setFlash(__('The Resource have waves. This Resource is not deleted.'), 'admin/danger', array());
		}
		else{
			$this->Resource->id = $id;
			if (!$this->Resource->exists()) {
				throw new NotFoundException(__('Invalid supplier'));
			}
			$this->request->allowMethod('post', 'delete');
			if ($this->Resource->delete()) {
				$this->Session->setFlash(__('The Resource has been deleted.'), 'admin/success', array());
			} else {
				$this->Session->setFlash(__('The Resource could not be deleted. Please, try again.'), 'admin/danger', array());
			}
		}

		return $this->redirect(array('action' => 'index'));
	}

	public function beforeRender() {
        $this->response->disableCache();
    }


}
