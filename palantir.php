<?php
require_once("conf/config.php");
require_once("core/class.ajaxRequest.php");

if(ENABLE_CROSS_SITE) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
    ob_flush();
}

// Instance our AJAX request and pass the data
$ajax = new AjaxRequest($_SERVER['REQUEST_METHOD'], $_REQUEST, $_SERVER);

// Process the request
$ajax->processRequest();

?>
