<?php

$response = null;
$status = false;
if (empty($errors)) {
    $status = true;
    // Create Bin drop down
    $response = $this->Form->input('Bin.Bin', array('label' => false, 'placeholder' => 'Select Bin', 'class' => 'form-control input-large multiple', 'options' => $bin,'value' => $id, 'div' => false, 'multiple','empty' => 'Select...'));
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

