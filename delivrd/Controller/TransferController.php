<?php
class TransferController extends AppController {

    // Will paginate all published articles
    public function index() {

		$res = $this->Transfer->find();
		 echo'<pre>';print_r($res); echo'</pre>';
		die('we are here');
		$this->Transfer->create();
		$this->request->data('Transfer.username','aaaa');
		$this->request->data('Transfer.type',1);
		$this->request->data('Transfer.direction',1);
		$this->request->data('Transfer.source',1);
		die;
    }
	
	
	
	public function add_product() 
	{
		$this->loadModel('Product');
		$response= array();
		$product= $this->Product->find('first', array('conditions' => array('sku'=>$_POST['sku']),'user_id'=>$this->Auth->user('id')));
		if(!$product){
			$quantity = ISSET($_POST['quantity']) ? $_POST['quantity']: '0' ;
			if(!ISSET($_POST['imageurl'])){
				$this->request->data('imageurl','https://delivrd.com/image_missing.jpg');
			}
		
			$this->Product->create();
			$data = array();
			$this->request->data('deleted',0);
			$this->request->data('status_id',1);
			foreach($_POST as $c =>$key)
			{
				$this->request->data($c,$key);
			}
			
			if (!$res = $this->Product->save($this->request->data)) {
				$response['success'] = 0;
				$response['status'] = 'Failed';
				$errors = $this->Product->validationErrors;
					foreach($errors as $e){
						$response['message'] =($e[0]);
					}
			}else{
				$response['invent'] = $res['Product']['id'];
				$response['success'] = 1;
				$response['status'] = 'Success';
				$response['message']  = $_POST['name'].' Product Added Successfully';
				$this->createinventoryrecord($res['Product']['id'],$quantity);
			}
		
		}else{
			$response['success'] = 2;
			$response['status'] = 'already Added';
			$response['product_id'] = $product['Product']['id'];
			$response['html']  = '<td>'.$product['Product']['name'].'</td>
			<td>'.$product['Product']['sku'].'</td>
			<td>Already Exists</td></tr>';
		}
		echo json_encode($response);
		die;
    }
	
	public function compare_products(){
		$this->loadModel('Product');
		$rowcount = $this->Product->find('first', array('conditions' => array('Product.id'=>2464)));
		if(isset($rowcount['Inventory']))
			print_r($rowcount['Inventory'][0]['quantity']);
			print_r($rowcount['Inventory'][0]['id']);
		
		echo'<pre>';
		echo'</pre>';
		die;
	}
	
	public function check_validations(){
		$this->loadModel('Product');
		$postdata = $this->request->data;
		$completeArr = array_combine($_POST['dbcol'],$_POST['csvcol']);
		$filename = $this->request->data('filename');
		$auth = $this->Auth->user();
		$user_id = $auth['id'];
		$username = $auth['username'];
		$file = fopen($filename,"r");
		$title = fgetcsv($file);
		$data = fgetcsv($file, 1000, ",");
		
		$header = array();
		$values = array();
		$csvdata = array();
		$file = fopen($filename,"r");
		$fp = file($filename, FILE_SKIP_EMPTY_LINES);
		$title = fgetcsv($file);
		$data = fgetcsv($file, 1000, ",");
		$tdata = fgetcsv($file, 1000, ",");
		$row = 1;
		if (($handle = fopen($filename, "r")) !== FALSE) 
		{
			for($t=0; $t< count($title); $t++)
			{
				$header[$t] = $title[$t];
			}
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
			{
				$num = count($data);
				$values[$row] = $data;
				$row++;
			}
			fclose($handle);
		}
		$product = array();
		$l=0;
		foreach($values as $val){
			$k=0;
			foreach($completeArr as $t =>$key){
				//echo $t.'<br>';
				$product[$l][$t]= $val[$k];
				$k++;
			}
			unset($product[0]);
			$l++;
		}
		$response = array();
		$html  ='';
		$success = 1;
		$k=1;
		foreach($product as $p){
			$a = array();
			$a['Product'] = $p;
			//echo'<pre>';print_r($p);echo'</pre>';
			$data = $this->Product->set($a);
			if ($this->Product->validates()) 
			{
				
			} 
			else {
			$html .='<div class="alert alert-danger">';
				$errors = $this->Product->validationErrors;
				$html .='<strong>'.$a['Product']['name'].'</strong> &nbsp; &nbsp;';
				foreach($errors as $e){
					$html .=($e[0]).'&nbsp;&nbsp;&nbsp;';
				}
				$success = 0;
			$html .='</div>';
				
			}
		$k++;			
		}
		$response['html'] = $html;
		$response['success'] = $success;
		echo json_encode($response);
		$this->Session->write('Product.matchArray',array_flip($completeArr));
		exit;
	}
	
	public function update_transfer(){
		$status = $this->request->data('status');
		$recordscount = $this->request->data('recordscount');
		$transfer_id = $this->request->data('transfer_id');
		$res = $this->Transfer->updateAll(
			array('recordscount' => $recordscount,'status' => $status),
			array('id' => $transfer_id)
		);
		if ($res == true){
			echo'Products Updated succesfully';
		} else{
			echo'Error while updating records';
		}
		exit();
	}
	
	public function createinventoryrecord($pid,$quantity)
	{	
		$this->loadModel('Inventory');
		$this->Inventory->create();
		$this->Inventory->set('user_id',$this->Auth->user('id'));
		$this->Inventory->set('dcop_user_id',$this->Auth->user('id'));
		$this->Inventory->set('product_id', $pid);
		$this->Inventory->set('quantity', $quantity);
		$this->Inventory->set('warehouse_id', $this->Session->read('default_warehouse'));
		if ($this->Inventory->save($this->request->data)) {			
			return 0;
		} else {
			$errors = $this->Inventory->validationErrors;
			 $this->Session->setFlash(__('The product inventory record could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
		}		
	
	}
}