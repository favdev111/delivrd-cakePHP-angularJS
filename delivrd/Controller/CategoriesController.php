<?php
App::uses('AppController', 'Controller');
/**
 * Categories Controller
 *
 * @property Size $Size
 * @property PaginatorComponent $Paginator
 */
class CategoriesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    public $theme = 'Mtro';


    public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to categories.'), 'admin/danger');
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
        $this->Category->recursive = 0;
         $conditions = array($type = null);
    
        if ($this->Auth->user('id')) {
            $conditions['Category.user_id'] = $this->Auth->user('id');
        }
        $this->set('categories', $this->Paginator->paginate($conditions));
        
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Category->exists($id)) {
            throw new NotFoundException(__('Invalid category'));
        }
        $options = array('conditions' => array('Category.' . $this->Category->primaryKey => $id));
        $this->set('category', $this->Category->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->layout = 'mtrd';
        if ($this->request->is('post')) {
            $this->Category->create();
            $this->request->data('Category.user_id',$this->Auth->user('id'));
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash(__('The Category has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Category could not be saved. Please, try again.'), 'admin/danger', array());
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
        if (!$this->Category->exists($id)) {
            throw new NotFoundException(__('Invalid category'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash(__('The Category has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Category could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Category.' . $this->Category->primaryKey => $id));
            $this->request->data = $this->Category->find('first', $options);
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
        $this->Category->id = $id;
        if (!$this->Category->exists()) {
            throw new NotFoundException(__('Invalid category'));
        }
        //If any product has category define, we cannot delete category
        $this->loadModel('Product');
        $productwithcategory = $this->Product->find('count', array('conditions' => array('Product.category_id' => $id, 'Product.user_id' =>$this->Auth->user('id'))));
        if($productwithcategory > 0)
            {
                $this->Session->setFlash(__('The Category could not be deleted. Please, try again.'), 'admin/danger', array());
                return $this->redirect(array('action' => 'index'));
            }
        $this->request->allowMethod('post', 'delete');
        if ($this->Category->delete()) {
            $this->Session->setFlash(__('The Category has been deleted.'), 'admin/success', array());
        } else {
            $this->Session->setFlash(__('The Category could not be deleted. Please, try again.'), 'admin/danger', array());
        }
        return $this->redirect(array('action' => 'index'));
    }
        
    public function create() {
 //       if ($this->request->is('ajax')) 
            if(1 == 1){
            // Instialize varriables
            $categories = array();
            $errors = array();

            // Get current loggged in user id to map cateotry with user
            $userId = $this->Auth->user('id');

            // Set user id 
            $this->request->data[$this->modelClass]['user_id'] = $userId;

            // Save new catetory in database
            if ($this->{$this->modelClass}->save($this->request->data)) {
                // Load categories
                $categories = $this->{$this->modelClass}->find('list', array('conditions' => array('Category.user_id' => $userId)));
                $id =$this->{$this->modelClass}->id;
            } else {
                // Get validation errors
                $errors = (!$userId) ? array('auth' => 'You have logged out. Please login again.') : $this->Category->validationErrors;
            }
        } else {
            throw new NotFoundException('404 error.');
        }

        // Set varriables for use on view ctp file
        $this->set(compact('categories', 'errors', 'id'));
     
        
    }
}
