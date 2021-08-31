<?php

$response = null;
$success = $success;
$current_inv = $current_inv;
$status = $status;

echo json_encode(array('response' => $response, 'status' => $status, 'success' => $success, 'current_inv' => $current_inv ));
exit;

?>