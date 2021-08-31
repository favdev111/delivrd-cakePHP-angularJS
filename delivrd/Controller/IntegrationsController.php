<?php
App::uses('AppController', 'Controller');
/**
 * Schannels Controller
 *
 * @property Schannel $Schannel
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class IntegrationsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Search.Prg', 'Amazon');
    public $theme = 'Mtro';
    
    public function beforeFilter() {
      parent::beforeFilter();
        $controller = $this->params->params['controller'];
        $action = $this->params->params['action'];
        $this->set(compact('controller', 'action', 'plugin', 'index'));
    }   
    
    public function isAuthorized($user) {
        if($user['is_limited']) {
            $this->Session->setFlash(__('Authorization failed. You have no access to integration section.'), 'admin/danger');
            return $this->redirect('/');
        }
        return parent::isAuthorized($user);
    }
        
    public function add() {
        $this->layout = 'mtrd';
        if ($this->request->is('post')) {
            if(isset($this->request->data['schannel_id']))
                $this->request->data['Integration']['schannel_id'] = $this->request->data['schannel_id'];
            $this->Integration->create();
            $this->request->data('Integration.user_id',$this->Auth->user('id'));
            if ($this->Integration->save($this->request->data)) {
                $this->Session->setFlash(__('The Integration has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Integration could not be saved. Please, try again.'), 'admin/danger', array());
            }
        }
        $schannels = $this->Integration->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $marketplaces = array_flip($this->Amazon->_MarketplaceIds);
        $endpoints = $this->Amazon->_endpoints;
        $this->set(compact('schannels', 'marketplaces', 'endpoints'));
    }

    /**
     * edit method
     *
     * @return void
     */
    public function edit($id = null) {
        $this->layout = 'mtrd';
        if (!$this->Integration->exists($id)) {
            throw new NotFoundException(__('Invalid schannel'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Integration->save($this->request->data)) {
                $this->Session->setFlash(__('The Integration has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Integration could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array('conditions' => array('Integration.' . $this->Integration->primaryKey => $id));
            $this->request->data = $this->Integration->find('first', $options);
        }
        $schannels = $this->Integration->Schannel->find('list',array('conditions' => array('Schannel.user_id' => $this->Auth->user('id'))));
        $marketplaces = array_flip($this->Amazon->_MarketplaceIds);
        $endpoints = $this->Amazon->_endpoints;
        $this->set(compact('schannels', 'marketplaces', 'endpoints'));
    }
        
    public function index() {
        $this->layout = 'mtrd';
        $this->Paginator->settings = array(
            'limit' => 10
        );
        $this->Integration->recursive = 1;      
        $conditions['Integration.user_id'] = $this->Auth->user('id');
        $conditions['Integration.backend !='] = 'Amazon';
        
        $this->Integration->bindModel(
            array(
                'hasMany'=>array(
                    'Transfer' =>array(
                        'className' => 'Transfer',
                        'foreignKey' => 'source_id',
                        'conditions' => '',
                        'limit' => 1,
                        'order' => 'Transfer.updated DESC'
                    )         
                )
            )
        );
        
        if ($this->request->is('post')) {
            $this->Prg->commonProcess();
        }

        if (isset($this->request->params['named']['backend']) && $this->request->params['named']['backend']) {
            $conditions['Integration.backend LIKE'] =  '%'. $this->request->params['named']['backend'] .'%';
        }

        $this->set('integrations', $this->Paginator->paginate($conditions));
        
        /*$this->Paginator->settings['conditions'] = $this->Integration->parseCriteria($this->Prg->parsedParams());
        // If we are in search mode, paginator should be search results. else, we display all results
        if(isset($this->viewVars['isSearch'])) {
            $conditions['Integration.user_id'] = $this->Auth->user('id');
            $this->set('integrations', $this->Paginator->paginate($conditions));
        } else {

        }*/
        $this->integrationName();
    }

    public function integrationName() {
        $options = array('Integration.user_id' => $this->Auth->user('id'));
        $integration = $this->Integration->find('first', array('conditions' => $options, 'recursive' => -1));
        if($integration) {
            $this->Session->write('integration', strtolower(substr($integration['Integration']['backend'], 0 , 2)));
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
        $this->Integration->id = $id;
        if (!$this->Integration->exists()) {
            throw new NotFoundException(__('Invalid Integration'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Integration->delete()) {
            $this->Session->setFlash(__('The Integration has been deleted.'), 'admin/success', array());
        } else {
            $this->Session->setFlash(__('The Integration could not be deleted. Please, try again.'), 'admin/danger', array());
            
        }
        return $this->redirect(array('action' => 'index'));
    }   
    
    /**
     * Import shopify method
     *
     * @throws NotFoundException
     * @param 
     * @return void
     */
    public function shopify($id) {
        $this->layout = null;
        $shopify = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Shopify','Integration.user_id' => $this->Auth->user('id'), 'Integration.id' => $id)));
        $this->set('shopify', $shopify);
    }

    /**
     * Import method
     *
     * @throws NotFoundException
     * @param $source string() ex: Shopify | Woocommerce
     * @return void
     */
    public function import($id) {
        $this->layout = null;
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Woocommerce','Integration.user_id' => $this->Auth->user('id'), 'Integration.id' => $id)));
        $this->set('integration', $integration);
    }

    /**
     * Import method
     *
     * @throws NotFoundException
     * @param 
     * @return void
     */
    public function amazon($id) {
        $this->layout = null;
        $integration = $this->Integration->find('first', array('conditions' => array('Integration.backend' => 'Amazon','Integration.user_id' => $this->Auth->user('id'), 'Integration.id' => $id)));
        $this->set('integration', $integration);
    }

    /**
     * Update Ecommerce manage inventory method
     *
     * @throws NotFoundException
     * @param 
     * @return void
     */
    public function isecommerce() { 
        $this->layout = null;
        if ($this->request->is(array('post', 'put'))) {
            $this->Integration->id = $this->request->data['id'];
            if ($this->Integration->exists()) {
                $data['Integration']['id'] = $this->request->data['id'];
                $data['Integration']['is_ecommerce'] = (bool)$this->request->data['isecommerce'];
                $this->Integration->save($data);
            }
        }
        exit;
    }

    public function beforeRender() {
        $this->response->disableCache();
    }
}
