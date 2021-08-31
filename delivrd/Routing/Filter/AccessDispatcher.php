<?php

App::uses('DispatcherFilter', 'Routing');
App::uses('ClassRegistry', 'Utility');

class AccessDispatcher extends DispatcherFilter {

    public $priority = 9;

    public function beforeDispatch(CakeEvent $event) {
        $request = $event->data['request'];
        $response = $event->data['response'];

        /*if ($request->url === 'inventories/saveQuantity') {
            $response->body('Hello World');
            $event->stopPropagation();
            return $response;
        }*/
    }
}