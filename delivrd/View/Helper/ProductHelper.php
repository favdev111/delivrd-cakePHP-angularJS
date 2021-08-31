<?php
class ProductHelper  extends AppHelper {
 
    /**
     * Render Product status
     */
    public function status($product) {
        switch ($product['Status']['id']) {
            case "1":
                $status_label = "label label-default";
                break;
            case "12":
                $status_label = "label label-warning";
                break;
            case "13":
                $status_label = "label label-danger";
                break;
            default:
                $status_label = "label label-default";
        }

        echo '<span class="label label-sm '. $status_label .'">'. h($product['Status']['name']) .'</span>';
    }

    /**
     * Render Product status
     */
    public function inv_status($inventory) {
        $reorder_point = $inventory['Product']['reorder_point'];
        if($inventory['Invalert']['reorder_point']) {
            $reorder_point = $inventory['Invalert']['reorder_point'];
        }
        $safety_stock = $inventory['Product']['safety_stock'];
        if($inventory['Invalert']['safety_stock']) {
            $safety_stock = $inventory['Invalert']['safety_stock'];
        }

        $btnclass = 'green';
        if($inventory['Inventory']['quantity'] <= $reorder_point && $inventory['Inventory']['quantity'] > $safety_stock) {
            $btnclass = 'yellow-crusta';
        }
        if($inventory['Inventory']['quantity'] <= $safety_stock) {
            $btnclass = 'red';
        }

        return $btnclass;
    }

    /**
     * Render Product status
     */
    public function locCount($product) {
        if(1 || $product[0]['quantity'] > 0 || $product[0]['quantity'] < 0) {
            $text = (int)$product[0]['quantity'];
            if(isset($this->_View->viewVars['uoms_display'][$product['Product']['uom']])) {
                $k = 'o';
                if($product[0]['quantity'] > 1) {
                    $k = 'm';
                }
                $text .= ' '. $this->_View->viewVars['uoms_display'][$product['Product']['uom']][$k];
            }
            if($product[0]['product_locs'] > 0) {
                $text .= ' in '. $product[0]['product_locs'];
                if($product[0]['product_locs'] > 1) {
                    $text .= ' locations';
                } else {
                    $text .= ' location';
                }
            }

        } else {
            $text = $product[0]['quantity'] .' '. $product['Product']['uom'];
        }

        if($product[0]['quantity'] <= $product['Product']['reorder_point']) {
            echo '<span class="text-danger">'. $text .'</span>';
        } else {
            echo $text;
        }
        
    }
}