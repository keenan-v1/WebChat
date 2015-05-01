<?php
require_once("conf/config.php");
require_once("core/class.ajaxRequest.php");

if(ENABLE_CROSS_SITE) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
    ob_flush();
    }

// If we're not an obvious AJAX request, then redirect to REDIRECT_URL
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest';) {
        http_redirect(REDIRECT_URL);
    }


?>