<?php
session_start();

$_SESSION = array();

session_destroy();

$response = array('success' => true);
echo json_encode($response);
?>
