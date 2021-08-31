<?php
class OrderHelper  extends AppHelper {
 
    /**
     * Render Order status
     */
    public function status($status_id) {
        if($status_id == 14) {
            $status_text = "<span class='label label-default'>Draft</span>";
        } else if($status_id == 2) {
            $status_text = "<span class='label label-info'>Released</span>";
        } else if($status_id == 3) {
            $status_text = "<span class='label bg-yellow'>Shipping Processing</span>";
        } else if($status_id == 8) {
            $status_text = "<span class='label label-success'>Shipped</span>";
        } else if($status_id == 4) {
            $status_text = "<span class='label label-success'>Completed</span>";
        } else if($status_id == 50) {
            $status_text = "<span class='label label-default'>Canceled</span>";
        } else if($status_id == 55) {
            $status_text = "<span class='label label-success'>Paid</span>";
        } else if($status_id == 60) {
            $status_text = "<span class='label label-info'>In Wave</span>";
        } else {
            $status_text = "<span class='label label-warning'>Shipped</span>";
        }
        echo $status_text;
    }

    /**
     * Render Event status
     */
    public function eventShipmentStatus($objectevent) {

        switch ($objectevent['Event']['status_id']) {
            case "1":
                $status_label = "label label-default";
                break;
            case "6":
                $status_label = "label bg-yellow-gold";
                break;
            case "16":
                $status_label = "label bg-yellow";
                break;
            case "7":
                $status_label = "label label-success";
                break;
            case "8":
                $status_label = "label label-success";
                break;
            default:
                $status_label = "label label-default";
        }
        $status_text = '<span class="'. $status_label .'">'. h($objectevent['Status']['name']) .'</span>';
        echo $status_text;
    }

    /**
     * Render Event status
     */
    public function shipmentStatus($shipment) {

        switch ($shipment['Status']['id']) {
            case "1":
                $status_label = "label label-default";
                break;
            case "6":
                $status_label = "label bg-yellow-gold";
                break;
            case "16":
                $status_label = "label bg-yellow";
                break;
            case "7":
                $status_label = "label label-success";
                break;
            case "8":
                $status_label = "label label-success";
                break;
            case "31":
                $status_label = "label bg-blue";
                break;
            case "32":
                $status_label = "label bg-purple";
                break;
            default:
                $status_label = "label label-default";
        }
        $status_text = '<span class="'. $status_label .'">'. h($shipment['Status']['name']) .'</span>';
        echo $status_text;
    }
}
