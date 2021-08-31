<?php
App::uses('AppController', 'Controller');
/**
 * Plans Controller
 
 * @property Plan $Plan
 * @property PaginatorComponent $Paginator
 */
class PlanViewController extends Controller {
/**
 * Components
 *
 * @var array
 */
	public $components = array('PaypalPro','Session');
	public $paginate = array();
	public $theme = 'Mtro';
	
/**
 * index method
 *
 * @return void
 */
	public function index($id = false) {
		
			if($id == 1)
			{
				$plan_name = 'ENTERPRISE';
				$amount = 0;
				$description = 'Custom solutions for any need';
			}
			elseif($id = 2){
				$plan_name = 'PRO';
				$amount = 120;
				$description = 'All you re ever need';
			}
			elseif($id = 3){
				$plan_name = 'PLUS';
				$amount = 499;
				$description = 'Most popular';
			}
			elseif($id = 4){
				$plan_name = 'BASIC';
				$amount = 0;
				$description = 'up to 25 products';
			}
			$this->set('plan_id', $id);
			$this->set('plan_name', $plan_name);
			$this->set('amount', $amount);
			$this->set('description', $description);
			
		$this->layout = 'mtrd-front';
		$this->render('index');
					
	
	}
	
}