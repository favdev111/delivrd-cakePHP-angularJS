<?php
App::uses('AppController', 'Controller');
App::uses('AdminHelper', 'View/Helper');
App::uses('CakeTime', 'Utility');
/**
 * OrdersCosts Controller
 *
 * @property OrdersCosts $OrdersCosts
 * @property PaginatorComponent $Paginator
 */
class OrdersCostsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','EventRegister');
    public $helpers = array('Session','Time');
    public $theme = 'Mtro';

    /**
    * Models
    *
    * @var array
    */
    public $uses = array('OrdersCosts', 'Order', 'Currency');

    public $types = [1 => 'S.O.', 2 => 'P.O.'];

    public function __beforeFilter() {
        parent::beforeFilter();

    }

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }

    /**
     * add_costs method
     *
     * @throws NotFoundException
     * @param int $order_id
     * @return void
     */
    public function add_costs($order_id) {
        $this->layout = false;
        $order = $this->Order->find('first', array(
            'fields' => array('Order.id','Order.ordertype_id', 'Order.user_id', 'Order.dcop_user_id', 'Order.external_orderid', 'User.currency_id'),
            'contain' => array('User', 'OrdersLine' => array('order' => array('OrdersLine.created DESC'))),
            'conditions' => array('Order.id' => $order_id)
        ));
        if(!$order) {
            throw new NotFoundException(__('Order not found'));
        }

        if($this->request->is('post')) {
            $this->request->data['OrdersCosts']['user_id'] = $order['Order']['user_id'];
            $this->request->data['OrdersCosts']['order_id'] = $order['Order']['id'];
            if ($this->OrdersCosts->save($this->request->data)) {
                $response['row'] = $this->OrdersCosts->find('first', ['conditions' => ['OrdersCosts.id' => $this->OrdersCosts->id]]);
                $response['action'] = 'success';
                $response['message'] = __('The orders additional costs/discount has been added');
                echo json_encode($response);
                exit;
            } else {
                $response['action'] = 'error';
                $response['errors'] = $this->OrdersCosts->validationErrors;
                $response['message'] = __('The orders additional costs/discount could not be added. Please, try again.');
                echo json_encode($response);
                exit;
            }
        }

        $currency = $this->Currency->find('first', array('conditions' => array('id' => $order['User']['currency_id'])));

        $this->set(compact('order', 'currency'));
    }

    /**
     * delete line method
     *
     * @throws NotFoundException
     * @param in $id
     * @return void
     */
    public function delete($id) {
        $this->OrdersCosts->id = $id;
        $currentOrdersCosts = $this->OrdersCosts->find('first', array('conditions' => array('OrdersCosts.id' => $id)));
        if (!$currentOrdersCosts) {
            throw new NotFoundException(__('Invalid orders line'));
        }

        if ($this->OrdersCosts->delete()) {
            $response['action'] = 'success';
            $response['line_id'] = $id;
            $response['message'] = __('Order additional costs/discount has been deleted');

        } else {
            $response['action'] = 'success';
            $response['message'] = __('The orders additional costs/discount could not be deleted. Please, try again.');
        }
        echo json_encode($response);
        exit;
    }
    
}