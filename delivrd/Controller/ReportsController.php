<?php
App::uses('AppController', 'Controller');

/**
 * Reports Controller
 *
 */
class ReportsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    public $helpers = array('Product');
    public $paginate = array();
    public $uses = array('Txreport', 'Inventory', 'Product', 'User');
    public $theme = 'Mtro';


    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {

        if($user['email'] == 'fordenis@ukr.net' || $user['email'] == 'technoyos@gmail.com') {
            return true;
        } else {
            return false;
        }
        return parent::isAuthorized($user);
    }

    public function index() {

        /*$tx_reports_all = $this->Txreport->find('count');
        if($tx_reports_all) {
            $tx_errors = $this->Txreport->find('count', ['conditions' => ['inv_quantity != tx_quantity']]);
        } else {
            $tx_errors = 'N/A';
        }

        $sql_count = "
            SELECT CONCAT_WS(',', user_id, sku) as gfield
            FROM `products`
            WHERE 1
            GROUP BY gfield
            HAVING COUNT(gfield) > 1";
        $recordsCount = $this->Product->query($sql_count);
        $duplicateCount = count($recordsCount);

        $this->set(compact('duplicateCount', 'tx_errors', 'tx_reports_all'));*/

        return $this->redirect('/admin');
    }

    public function collect_data() {
        set_time_limit(0);
        $this->Txreport->query('TRUNCATE txreport');

        $limit = 4000;
        for($i = 0; $i < 50; $i++) {

            $sql = "SELECT 
                `Inventory`.`id` as inventory_id,
                `Inventory`.`product_id`,
                `Inventory`.`warehouse_id`,
                `Inventory`.`user_id`,
                `Inventory`.`quantity` as inv_quantity,
                `Inventory`.`modified` as inv_modified,
                SUM((IFNULL(OrdersLine.receivedqty, 0) - IFNULL(OrdersLine.sentqty, 0) - IFNULL(OrdersLine.damagedqty, 0))) AS tx_quantity,
                MAX(`OrdersLine`.`modified`) as tx_last
            FROM `inventories` AS `Inventory` 
            
            LEFT JOIN `orders_lines` AS `OrdersLine` ON (`OrdersLine`.`product_id` = `Inventory`.`product_id` AND `OrdersLine`.`warehouse_id` = `Inventory`.`warehouse_id`) 
            WHERE 
                `Inventory`.`deleted` = '0'
            GROUP BY `Inventory`.`id`
            LIMIT ". $i*$limit .", ". $limit;

            $results = $this->Txreport->query($sql);
            if($results) {
                $txs = [];
                foreach ($results as $res) {
                    $tx = [];
                    $tx['inventory_id'] = $res['Inventory']['inventory_id'];
                    $tx['product_id'] = $res['Inventory']['product_id'];
                    $tx['warehouse_id'] = $res['Inventory']['warehouse_id'];
                    $tx['user_id'] = $res['Inventory']['user_id'];
                    $tx['inv_quantity'] = $res['Inventory']['inv_quantity'];
                    $tx['inv_modified'] = $res['Inventory']['inv_modified'];
                    $tx['tx_quantity'] = $res[0]['tx_quantity'];
                    $tx['tx_last'] = $res[0]['tx_last'];
                    $txs[] = $tx;
                }
                $this->Txreport->saveMany($txs);
            } else {
                break;
            }
        }

        $tx_reports_all = $this->Txreport->find('count');
        $this->Session->setFlash(__('Data generated, total added %s rows.', $tx_reports_all),'admin/success');
        return $this->redirect(array('action' => 'index'));
    }

    public function transactions() {
        $limit = 20;
        $tx_reports_all = $this->Txreport->find('count');
        $this->set(compact('limit', 'tx_reports_all'));
    }

    public function transactions_js() {

        $limit = 20;
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        $orderBy = 'Txreport.inv_modified';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'DESC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }
        $page = 1;
        if(isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $transactions = $this->Txreport->find('all', [
            'conditions' => ['Txreport.inv_quantity != Txreport.tx_quantity'],
            'contain' => ['Product', 'Warehouse', 'User'],
            'joins' => array(
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Product.id = Txreport.product_id'
                    )
                ),
                array(
                    'table' => 'warehouses',
                    'alias' => 'Warehouse',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'Warehouse.id = Txreport.warehouse_id'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'User.id = Txreport.user_id'
                    )
                )
            ),
            'fields' => [
                'Product.name',
                'Product.sku',
                'User.email',
                'Warehouse.name',
                'Txreport.inv_quantity',
                'Txreport.inv_modified',
                'Txreport.tx_quantity',
                'Txreport.tx_last'
            ],
            'limit' => $limit,
            'page' => $page,
            'order' => array($orderBy => $orderDir)
        ]);

        $tx_reports = $this->Txreport->query('SELECT count(*) as total FROM txreport WHERE inv_quantity != tx_quantity');
        $recordsCount = $tx_reports[0][0]['total'];

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($transactions);
        $response['rows'] = $transactions;

        //header('Content-Type: application/json');

        echo json_encode($response);
        exit;
    }

    public function duplicate() {
        $limit = 20;
        $users = $this->User->find('list', ['fields' => ['User.id', 'User.email']]);
        
        $this->set(compact('limit', 'users'));
    }

    public function duplicate_js() {
        $limit = 20;
        if($this->request->query('limit')) {
            $limit = $this->request->query('limit');
        }
        $orderBy = 'p.sku';
        if($this->request->query('sortby')) {
            $orderBy = $this->request->query('sortby');
        }
        $orderDir = 'DESC';
        if($this->request->query('sortdir')) {
            $orderDir = $this->request->query('sortdir');
        }
        $page = 1;
        if(!empty($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $sql = "
            SELECT p.id, p.sku, p.user_id, CONCAT_WS(',', p.user_id, p.sku) as gfield, count(p.id) as product_count, p.created
            FROM `products` as p
            WHERE p.deleted != 1
            GROUP BY gfield
            HAVING COUNT(gfield) > 1
            ORDER BY ". $orderBy ." ". $orderDir ."
            LIMIT ". ($page-1)*$limit .", ". $limit;
        $products = $this->Product->query($sql);

        $sql_count = "
            SELECT CONCAT_WS(',', user_id, sku) as gfield
            FROM `products`
            WHERE 1
            GROUP BY gfield
            HAVING COUNT(gfield) > 1";
        $recordsCount = $this->Product->query($sql_count);
        $recordsCount = count($recordsCount);

        $response['recordsTotal'] = $recordsCount;
        $response['rows_count'] = count($products);
        $response['rows'] = $products;

        header('Content-Type: application/json');

        echo json_encode($response);
        exit;
    }

    function product_by_sku($sku, $user_id) {
        $this->Product->recursive = -1;
        $products = $this->Product->find('all', [
            'conditions' => ['Product.sku' => $sku, 'Product.user_id' => $user_id],
            'fields' => ['Product.id', 'Product.name', 'Product.deleted', 'Product.status_id', 'COUNT(OrdersLine.id) as tx_count'],
            'joins' => array(
                array(
                    'table' => 'orders_lines',
                    'alias' => 'OrdersLine',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'OrdersLine.product_id = Product.id'
                    )
                )
            ),
            'group' => 'Product.id'
        ]);

        $response['rows'] = $products;

        header('Content-Type: application/json');

        echo json_encode($response);
        exit;
    }
}