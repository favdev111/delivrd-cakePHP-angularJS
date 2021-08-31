<?php
App::uses('Helper', 'View');

class NetworkHelper extends Helper 
{

    public $helpers = array('Form');

    public $components = array('Access');

    /**
     * Render Invitation status
     */
    public function iStatus($status) {
        APP::import('Model', 'NetworksInvite');
        $this->NetworksInvite = new NetworksInvite();
        $statuses = $this->NetworksInvite->getStatuses();

        $statuses = array_flip($statuses);
        if($status == 1) {
            $span = '<span class="label label-primary">'. ucfirst($statuses[$status]) .'</span>';
        } elseif($status == 2) {
            $span = '<span class="label label-success">'. ucfirst($statuses[$status]) .'</span>';
        } elseif($status == 3) {
            $span = '<span class="label label-danger">'. ucfirst($statuses[$status]) .'</span>';
        } elseif($status == 4) {
            $span = '<span class="label label-info">'. ucfirst($statuses[$status]) .'</span>';
        }
        echo $span;
    }

    /**
     * Render Network User status
     */
    public function status($status) {
        
        if($status == 1) {
            $span = '<span class="label label-success">Active</span>';
        } else {
            $span = '<span class="label label-warning">Disabled</span>';
        }
        echo $span;
    }

    public function channels($json_ids, $channels) {
        if($json_ids == 'all') { 
            $res = array_values($channels);
        } elseif($ids = json_decode($json_ids)) {
            $ids = json_decode($json_ids);
            $res = [];
            foreach ($ids as $id) {
                $res[] = $channels[$id];
            }
        } else {
            $res[] = 'No channels';
        }
        echo implode(', ', $res);
    }

    /**
     * Render Network User status
     */
    public function nStatus($status) {
        APP::import('Model', 'NetworksUser');
        $this->NetworksUser = new NetworksUser();
        $statuses = $this->NetworksUser->getStatuses();

        $statuses = array_flip($statuses);
        if($status == 1) {
            $span = '<span class="label label-success">'. ucfirst($statuses[$status]) .'</span>';
        } elseif($status == 2) {
            $span = '<span class="label label-warning">'. ucfirst($statuses[$status]) .'</span>';
        } elseif($status == 3) {
            $span = '<span class="label label-danger">'. ucfirst($statuses[$status]) .'</span>';
        }
        echo $span;
    }

    /**
     * Render Network User status
     */
    public function nType($type) {
        if($type == 'private') {
            $span = '<span class="label label-success">'. ucfirst($type) .'</span>';
        } else {
            $span = '<span class="label label-danger">'. ucfirst($type) .'</span>';
        }
        echo $span;
    }

    /**
     * Render Network User status
     */
    public function role($role) {
        APP::import('Model', 'Network');
        $this->Network = new Network();
        $roles = $this->Network->getRoles();
        if(isset($roles[$role])) {
            echo $roles[$role];
        } else {
            echo __('Custom role');
        }
    }

    /**
     * Render Network User status
     */
    public function productCount($str) {
        
        if($str == 'all') {
            echo __('All Products');
        } else {
            echo  count(json_decode($str)) .' '.__('Products');
        }
    }

    /**
     * Render product select with user available products
     */
    public function productSelect() {
        echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select Product','class' => 'form-control input-large select2me','div' =>false,'empty' => 'Select...'));
    }
}