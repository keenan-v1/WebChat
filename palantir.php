<?php
require_once("conf/config.php");
require_once("core/class.ajaxRequest.php");

if(ENABLE_CROSS_SITE) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
    ob_flush();
}

echo "Start ";

// If we're not an obvious AJAX request, then redirect to REDIRECT_URL
/*
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        http_redirect(REDIRECT_URL);
}
*/
$requestType = "";
$requestData = array();

echo "Getting Request";
if(isset($_GET) && !empty($_GET)) {
    $requestType = AjaxRequest::TYPE_GET;
    $requestData = $_GET;
}

if(isset($_POST) && !empty($_POST)) {
    $requestType = AjaxRequest::TYPE_POST;
    $requestData = $_POST;
}

echo "Creating Object";

// Instance our AJAX request and pass the data
$ajax = new AjaxRequest($requestType, $requestData, $_SERVER);

// Process the request
$ajax->processRequest();

?>
