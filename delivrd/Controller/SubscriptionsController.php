<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Invalert $Invalert
 */
class SubscriptionsController extends AppController {

	/**
     * Components
     *
     * @var array
     */
    public $components = array('Access', 'Paginator');
    public $helpers = array();
    public $paginate = array();
    public $theme = 'Mtro';

    public function beforeFilter() {
       parent::beforeFilter();
    }
    
    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        if(in_array($this->action, array('index', 'add', 'edit', 'userlist'))) {
            if($user['email'] == 'fordenis@ukr.net' || $user['email'] == 'technoyos@gmail.com') {
                return true;
            } else {
                return false;
            }
        } else {
            return parent::isAuthorized($user);
        }
    }

    public function signin() {
        $this->layout = 'mtrl';
    }

    public function presignin() {
        $this->layout = 'mtrl';
    }

    public function index() {
        $this->Paginator->settings = array(
            'order' => array('Subscription.modified' => 'DESC')
        );
        $subscriptions = $this->paginate();
        $this->set(compact('subscriptions'));
    }

    public function add() {
        if($this->request->is(array('post', 'put'))) {
            $this->Subscription->create();
            $this->request->data['Subscription']['create'] = $this->request->data['Subscription']['modified'] = date('Y-m-d H:i:s');
            if($this->Subscription->save($this->request->data)) {
                $this->Session->setFlash(__('New Subscription successfully added'), 'admin/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Subscription couldn\'t be saved. Please check errors and try again.'), 'admin/danger');
            }
        }

        $this->loadModel('User');
        $users = $this->User->find('list', array('fields' =>['User.id', 'User.email']));
        $this->set(compact('users'));
    }

    public function memo($id) {
        if(!$this->Subscription->exists($id)) {
            throw new NotFoundException(__('Subscription Not Found'));
        }
        $this->layout = false;

        $subscription = $this->Subscription->find('first', ['conditions' => array('Subscription.id' => $id)]);
        $this->set(compact('subscription'));
    }

    public function edit($id) {
        if(!$this->Subscription->exists($id)) {
            throw new NotFoundException(__('Subscription Not Found'));
        }

        if($this->request->is(array('post', 'put'))) {
            $this->request->data['Subscription']['modified'] = date('Y-m-d H:i:s');
            if($this->Subscription->save($this->request->data)) {
                $this->Session->setFlash(__('Subscription successfully updated'), 'admin/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Subscription couldn\'t be saved. Please check errors and try again.'), 'admin/danger');
            }
        } else {
            $this->request->data = $this->Subscription->find('first', ['conditions' => array('Subscription.id' => $id)]);

        }

        $this->loadModel('User');
        $users = $this->User->find('list', array('fields' =>['User.id', 'User.email']));
        $this->set(compact('users'));
    }

    public function delete($id) {
        if(!$this->Subscription->exists($id)) {
            throw new NotFoundException(__('Subscription Not Found'));
        }

        if($this->request->is(array('post', 'put'))) {
            $subscription = $this->Subscription->findById($id);
            $this->loadModel('User');
            $this->User->id = $subscription['User']['id'];
            $this->User->saveField('role', 'free');

            if($this->Subscription->delete($id)) {
                $this->Session->setFlash(__('Subscription successfully updated'), 'admin/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Subscription couldn\'t be saved. Please check errors and try again.'), 'admin/danger');
            }
        } else {
            $this->request->data = $this->Subscription->find('first', ['conditions' => array('Subscription.id' => $id)]);

        }

        $this->loadModel('User');
        $users = $this->User->find('list', array('fields' =>['User.id', 'User.email']));
        $this->set(compact('users'));
    }

    public function userlist() {
        $this->layout = false;
        $this->loadModel('User');
        $conditions['OR'] = ['User.email like' => '%'. $this->request->query('search') .'%', 'User.id like' => '%'. $this->request->query('search') .'%', 'User.firstname like' => '%'. $this->request->query('search') .'%', 'User.lastname like' => '%'. $this->request->query('search') .'%'];
        $users = $this->User->find('list', array('fields' =>['User.id', 'User.email'], 'conditions' => $conditions));
        $results['results'] = [];
        foreach ($users as $key => $value) {
            $result['id'] = $key;
            $result['text'] = ($value);
            $results['results'][] = $result;
        }
        echo (json_encode($results));
        exit;
    }

    public function payment_list($subscription=null) {
        $this->loadModel('Payment');
        $conditions = [];
        if($subscription) {
            $conditions['Payment.subscription'] = $subscription;
        }
        $payments = $this->paginate('Payment', $conditions);
        $this->set(compact('payments'));
    }
}