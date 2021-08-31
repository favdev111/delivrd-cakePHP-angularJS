<?php
App::uses('AppController', 'Controller');
/**
 * Colors Controller
 *
 * @property Color $Color
 * @property PaginatorComponent $Paginator
 */
class FieldsController extends AppController {

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

        $this->Field->recursive = 0;
        $this->Paginator->settings = array(
            'conditions' => array('Field.user_id' => $this->Auth->user('id')),
            'contain' => array('FieldsValue' => array('id', 'value')),
            'limit' => 10
        );
        $fields = $this->Paginator->paginate('Field');
        $this->set('fields', $fields);
    }

    /**
     * used method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function usage($id = null) {
        $this->layout = false;
        if (!$this->Field->exists($id)) {
            throw new NotFoundException(__('Invalid field'));
        }
        $this->Field->recursive = -1;
        $field = $this->Field->findById($id);

        $this->set(compact('field'));
    }

    /**
     * used method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function usage_ajax($id = null) {
        $this->layout = false;
        if (!$this->Field->exists($id)) {
            throw new NotFoundException(__('Invalid field'));
        }
        $this->loadModel('FieldsData');

        $conditions = ['FieldsData.object_type' => 1, 'FieldsData.value !=' => '', 'FieldsData.field_id' => $id];
        if($this->request->query('q')) {
            $conditions['OR']['Product.name LIKE'] = '%'. $this->request->query('q') .'%';
            $conditions['OR']['Product.sku LIKE'] = '%'. $this->request->query('q') .'%';
        }

        $page = 1;
        if($this->request->query('page')) {
            $page = $this->request->query('page');
        }

        $fieldData = $this->FieldsData->find('all', array(
            'fields'=>array('FieldsData.object_id', 'FieldsData.value', 'Product.id', 'Product.name', 'Product.sku', 'Product.imageurl'),
            'conditions' => $conditions,
            'joins' => [
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'Product.id = FieldsData.object_id',
                    )
                ),
            ],
            'page' => $page
        ));

        $this->loadModel('FieldsValue');
        $field_values = $this->FieldsValue->find('list', [
            'conditions' => ['FieldsValue.field_id' => $id]
        ]);

        if($field_values) {
            foreach ($fieldData as &$data) {
                if(isset($field_values[$data['FieldsData']['value']])) {
                    $data['FieldsData']['value'] = $field_values[$data['FieldsData']['value']];
                }
            }
        }

        $count_total = $this->FieldsData->find('count', array(
            'conditions' => $conditions,
            'contain' => false,
            'joins' => [
                array('table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'Product.id = FieldsData.object_id',
                    )
                ),
            ]
        ));
        
        $response['recordsTotal'] = $count_total;
        $response['rows_count'] = count($fieldData);
        $response['rows'] = $fieldData;

        echo json_encode($response);
        exit;
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Field->exists($id)) {
            throw new NotFoundException(__('Invalid field'));
        }
        $options = array('conditions' => array('Field.id' => $id));
        $this->set('field', $this->Field->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {

        $this->loadModel('FieldsValue');
        $this->FieldsValue->recursive = -1;

        if ($this->request->is('post')) {
            $this->Field->create();
            $this->request->data('Field.user_id', $this->Auth->user('id'));
            $this->request->data('Field.created', date('Y-m-d H:i:s'));
            if ($this->Field->save($this->request->data)) {
                $field_id = $this->Field->id;
                if(!empty($this->request->data['FieldsValue'])) {
                    $this->FieldsValue->add_options($field_id, $this->request->data['FieldsValue']);
                }
                $this->Session->setFlash(__('The Field has been added.'), 'admin/success');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Field could not be added. Please, try again.'), 'admin/danger');
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
        if (!$this->Field->exists($id)) {
            throw new NotFoundException(__('Invalid field'));
        }

        $this->loadModel('FieldsValue');
        $this->FieldsValue->recursive = -1;

        if ($this->request->is(array('post', 'put'))) {
            if ($this->Field->save($this->request->data)) {
                if(!empty($this->request->data['FieldsValue'])) {
                    $this->FieldsValue->update_options($id, $this->request->data['FieldsValue']);
                }
                $this->Session->setFlash(__('The Field has been saved.'), 'admin/success', array());
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Field could not be saved. Please, try again.'), 'admin/danger', array());
            }
        } else {
            $options = array(
                'conditions' => array('Field.id' => $id),
                'contain' => array('FieldsValue'),
            );
            $field = $this->Field->find('first', $options);
            $field_options = $this->FieldsValue->find('all', array('conditions' => array('FieldsValue.field_id' => $id)));
            
            $this->request->data = $field;
            $this->set(compact('field', 'field_options'));
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
        $this->Field->id = $id;
        $field = $this->Field->findById($id);
        if (!$field) {
            throw new NotFoundException(__('Invalid field'));
        }
        if($field['Field']['values_count'] > 0) {
            $this->Session->setFlash(__('Custom field '. $field['Field']['name'] .' has at least one product with data, it cannot be deleted'), 'admin/danger', array());
        } else {
            $this->request->allowMethod('post', 'delete');
            if ($this->Field->delete()) {
                $this->Session->setFlash(__('The Field has been deleted.'), 'admin/success', array());
            } else {
                $this->Session->setFlash(__('The Field could not be deleted. Please, try again.'), 'admin/danger', array());
            }
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function add_option() {
        if ($this->request->is('post')) {
            $this->loadModel('FieldsValue');
            $this->FieldsValue->recursive = -1;
            
            $option['FieldsValue'] = $this->request->data;
            $option['FieldsValue']['created'] = date('Y-m-d H:i:s');
            $option['FieldsValue']['user_id'] = $this->Auth->user('id');
            

            //pr($price);
            if($this->FieldsValue->save($option)) {
                $option['FieldsValue']['id'] = $this->FieldsValue->id;
                $response['action'] = 'success';
                $response['option'] = $option['FieldsValue'];
            } else {
                $response['action'] = 'error';
                $response['errors'] = $this->FieldsValue->validationErrors;
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    public function delete_option() {
        if ($this->request->is('post')) {
            $this->loadModel('FieldsValue');
            $this->loadModel('FieldsData');

            $fieldOption = $this->FieldsValue->findById($this->request->data['id']);
            // Check is any products have such option
            $used = $this->FieldsData->find('count', array('conditions' => array('FieldsData.field_id' => $fieldOption['FieldsValue']['field_id'], 'FieldsData.value' => $fieldOption['FieldsValue']['id'] )));
            if($used) {
                $response['action'] = 'error';
                $response['msg'] = 'You can\'t delete this option. It set like value for '. $used .' product(s)';
            } else {
                $this->FieldsValue->id = $this->request->data['id'];
                $this->FieldsValue->delete();
                $response['action'] = 'success';
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }
}
