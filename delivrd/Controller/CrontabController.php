<?php
App::uses('AppController', 'Controller');
/**
 * Crontab Controller
 *
 */
class CrontabController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array();
	public $theme = 'Mtro';

    /**
    * Models
    *
    * @var array
    */
    public $uses = array('User');

	public function beforeFilter() {
       parent::beforeFilter();
       $this->Auth->allow('cleardb');
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function cleardb() {
        exit;
        // Get Expired Users
        $exp_period = intval(Configure::read('cleanup.expire_period'));
        if(!$exp_period) {
            $exp_period = 100;
        }

        $exp_date = date('Y-m-d', strtotime('today - '. $exp_period .' days'));
        $users = $this->User->find('all', [
            'fields' => ['User.id', 'User.email'],
            'contain' => false,
            'conditions' => ['User.last_login <' => $exp_date, 'User.role !=' => 'paid' ],
            'limit' => 1
        ]);
        
        // We have total 73 tables
        // Skeep 20 tables which have no user data:
        // barcode_standards, carriers, countries, currencies, direction, fields_values, groups, msystems, object_type, ordertypes, 
        // packaging_materials, phinxlog, shortcut_links, sources, states, statuses, stores, stypes, supply_sources, zones
        $r = [];
        foreach ($users as $user) {
            pr($user);
            // Remove info from linked tables which have no direct association:
            // 2 tables: `waves`, `waves_countries`
            $sql1 = '
                DELETE w, wc FROM `waves` as w
                LEFT JOIN `waves_countries` as wc
                    ON wc.`wave_id` = w.`id`
                WHERE w.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql1);
            #pr($sql1);

            // 1 table: `users_mailchimp` `transfers`, `user_shortcut_links`
            $sql2 = '
                DELETE FROM `users_mailchimp` WHERE `users_id` = "'. $user['User']['id'] .'";
                DELETE FROM `transfers` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `user_shortcut_links` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `user_details` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `sizes` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `events` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `subscriptions` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `suppliers` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `supplysources` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `bins` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `categories` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `colors` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `couriers` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `addresses` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `activities` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `payments` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `integrations` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `ads` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `asns` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `batch_picks` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `csv_mappings` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `documents` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `packstations` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `resources` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `schannels` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `shipments` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `warehouses` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `custom_data` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `custom_values` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `custom_fields` WHERE `user_id` = "'. $user['User']['id'] .'";
                DELETE FROM `invalerts` WHERE `user_id` = "'. $user['User']['id'] .'";
            ';
            $r[$user['User']['id']][] = $this->User->query($sql2);
            #pr($sql2);

            // 4 tables: networks networks_invites networks_access networks_users
            // Delete user networks
            $sql3 = '
                DELETE nt.*, na.*, ni.*, nu.* 
                FROM `networks` as nt
                LEFT JOIN `networks_access` as na
                    ON na.`network_id` = nt.`id`
                LEFT JOIN `networks_invites` as ni
                    ON ni.`network_id` = nt.`id`
                LEFT JOIN `networks_users` as nu
                    ON nu.`network_id` = nt.`id`
                WHERE nt.`created_by_user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql3);
            #pr($sql3);
            // Delete user from other networks
            $sql4 = '
                DELETE FROM networks_users WHERE user_id = "'. $user['User']['id'] .'";
                DELETE FROM networks_invites WHERE user_id = "'. $user['User']['id'] .'";
                DELETE FROM networks_access WHERE user_id = "'. $user['User']['id'] .'";
            ';
            $r[$user['User']['id']][] = $this->User->query($sql4);
            #pr($sql4);


            // Products
            // 7 tables: products, forecasts, products_bins, products_prices, productsuppliers, kits, serials
            // Delete user networks
            $sql5 = '
                DELETE p.*, f.*, pb.*, pp.*, ps.*, k.*, s.*
                FROM `products` as p
                LEFT JOIN `forecasts` as f
                    ON f.`product_id` = p.`id`
                LEFT JOIN `products_bins` as pb
                    ON pb.`product_id` = p.`id`
                LEFT JOIN `products_prices` as pp
                    ON pp.`product_id` = p.`id`
                LEFT JOIN `productsuppliers` as ps
                    ON ps.`product_id` = p.`id`
                LEFT JOIN `kits` as k
                    ON k.`product_id` = p.`id`
                LEFT JOIN `serials` as s
                    ON s.`product_id` = p.`id`
                WHERE p.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql5);
            #pr($sql5);

            // Inventory
            // 2 tables: inventories txreport
            $sql6 = '
                DELETE i.*, tx.*
                FROM `inventories` as i
                LEFT JOIN `txreport` as tx
                    ON tx.`inventory_id` = i.`id`
                WHERE i.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql6);
            #pr($sql6);

            // Orders
            // 6 tables: orders, orders_lines, orders_schedule, orderslines_waves, orders_blanket, orders_costs
            $sql7 = '
                DELETE o.*, ol.*, os.*, olw.*, ob.*, oc.*
                FROM `orders` as o
                LEFT JOIN `orders_lines` as ol
                    ON ol.`order_id` = o.`id`
                LEFT JOIN `orderslines_waves` as olw
                    ON olw.`ordersline_id` = ol.`id`
                LEFT JOIN `orders_schedule` as os
                    ON os.`order_id` = o.`id`
                LEFT JOIN `orders_blanket` as ob
                    ON ob.`order_id` = o.`id`
                LEFT JOIN `orders_costs` as oc
                    ON oc.`order_id` = o.`id`
                WHERE o.`user_id` = "'. $user['User']['id'] .'"
            ';
            $r[$user['User']['id']][] = $this->User->query($sql7);
            #pr($sql7);

            // DELETE USER
            $sql8 = '
                DELETE FROM `users` WHERE `id` = "'. $user['User']['id'] .'";
            ';
            $r[$user['User']['id']][] = $this->User->query($sql8);
            #pr($sql7);
        }
        
        pr($r);
        exit;

        /*
        // Tables which must be ignored (20):
        $exclude = ['barcode_standards', 'carriers', 'countries', 'currencies', 'direction', 'fields_values', 'groups', 'msystems', 'object_type', 'ordertypes', 
            'packaging_materials', 'phinxlog', 'shortcut_links', 'sources', 'states', 'statuses', 'stores', 'stypes', 'supply_sources', 'zones'];

        $pocessed = ['users_mailchimp', 'waves', 'waves_countries', 'networks', 'networks_invites', 'networks_access', 'networks_users', 'transfers', 'user_shortcut_links', 'user_details',
            'events', 'sizes', 'subscriptions', 'suppliers', 'supplysources', 'bins', 'categories', 'colors', 'couriers', 'addresses', 'activities', 'payments', 'integrations',
            'ads', 'asns', 'batch_picks', 'csv_mappings', 'documents', 'packstations', 'resources', 'schannels', 'shipments', 'warehouses', 'custom_data', 'custom_values', 'custom_fields',
            'products', 'forecasts', 'products_bins', 'products_prices', 'productsuppliers', 'kits', 'serials', 'invalerts', 'inventories', 'txreport',
            'orders', 'orders_lines', 'orders_schedule', 'orderslines_waves', 'orders_blanket', 'orders_costs'
        ];

        $exclude  = array_merge($exclude, $pocessed);
        
        $res = $this->User->query('show tables');
        
        pr(count($res));
        foreach ($res as $tbl) {
            if(!in_array($tbl['TABLE_NAMES']['Tables_in_ys_prod'], $exclude)) {
                $linked = $this->User->query('SELECT TABLE_NAME
                  FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE 
                    TABLE_NAME = "'. $tbl['TABLE_NAMES']['Tables_in_ys_prod'] .'" AND
                    COLUMN_NAME = "user_id"
                    GROUP BY TABLE_NAME');
                if($linked) {
                    $lnk_tbls[] = $tbl['TABLE_NAMES']['Tables_in_ys_prod'];
                } else {
                    $unlnk_tbls[] = $tbl['TABLE_NAMES']['Tables_in_ys_prod'];
                }
            }
        }
        pr($lnk_tbls);
        pr($unlnk_tbls);
        // Zone Currency Msystem Address Subscription Product Warehouse
        // txreport users_mailchimp
        #pr($users);
        exit;
        */
    }

}