<?php

$response = null;
$status = false;
if (empty($errors)) {
    $status = true;  
    // Create Supplier drop down
    $response = $this->Form->input('supplier_id', array('label' => false, 'class' => 'form-control input-medium select2me', 'options' => $supplier,'empty'=>'Select...','value' => $id,'required' => true));
} else {
// Setup erros
    $errosList = array();
    foreach ($errors as $key => $error) {
        $errosList[] = $error[0];
    }

    $response = implode('</br>', $errosList);
    $response = '<div class="alert alert-danger alert-dismissible fade in" role="alert"> 
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button> 
                                ' . $response . '
                            </div>';
}
echo json_encode(array('response' => $response, 'status' => $status));
exit;

?>

