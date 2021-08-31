<?php

$response = null;
$success = $success;
$current_inv = $inv_data;
$status = $status;

echo json_encode(array('response' => $response, 'status' => $status, 'success' => $success, 'current_inv' => $inv_data ));
exit;

?>

