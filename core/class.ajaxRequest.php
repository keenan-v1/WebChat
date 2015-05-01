<?php
// class.ajaxRequest.php

/*
 * AjaxRequest: A class handling specific AJAX requests.
 */
class AjaxRequest {
    // These are our actions and their handlers
    // Note: handlers should be methods of this class
    public static $actions = array(
        "test" => "test_function",
        "echo" => "echo_function"
    );
    
    // Reply Types
    const TYPE_JSON = "JSON";
    
    // Request Types
    const TYPE_GET = "GET";
    const TYPE_POST = "POST";
    
    // Reply Data
    private $replyData = array();
    private $replyType = self::TYPE_JSON;
    
    // Request Data
    private $requestData = array();
    private $serverData = array();
    private $requestType = self::TYPE_POST;
    
    public function __construct($requestType, $requestData, $serverData = null) {
        switch($requestType) {
            case self::TYPE_GET:
                $this->requestType = self::TYPE_GET;
                break;
            case self::TYPE_POST:
                $this->requestType = self::TYPE_POST;
                break;
            default:
                throw new Exception("Invalid Request Type: {$requestType}");
        }
        $this->requestData = $requestData;
        if(is_array($serverData) && !empty($serverData))
            $this->serverData = $serverData;
        else if(is_array(($serverData = $_SERVER)) && !empty($serverData))
            $this->serverData = $serverData;
        else
            throw new Exception("No Server Data or invalid data: ", $serverData);
    }
    
    public function processRequest($requestData) {
        var $action = "", $handler = "";
        
        //Check to see if the action is supported
        if(array_key_exists(($action = $requestData['action'])))
            var $handler = self::$actions[$action];
        else
            throw new Exception("No handler for action: {$action}");
        
        // Handle it!
        $this->$handler();
        
        // Write output back to requester
        $this->write();
    }
       
    // Sets the "keyword" (arg1) with the "value" (arg2)
    // If arg1 is an array of key-value pairs, we just append it.
    public function setData($arg1, $arg2 = NULL) {
        if(is_array($arg1))
            $this->replyData = array_merge($this->replyData, $arg1);
        else if(isset($arg2) && $arg2 != NULL)
            $this->replyData[$arg1] = $arg2;
        else
            throw new Exception("Invalid arguments for setData: arg1: {$arg1} arg2: {$arg2}");       
    }
    
    // Encode our data as JSON and send it.
    public function writeJson() {
        var $replyJson = json_encode($this->replyData);
        echo $replyJson;
        return;        
    }
    
    // Wrapper for different data types (future proofing)
    public function write() {
        switch($this->replyType) {
            case self::TYPE_JSON:
                $this->writeJson();
                break
            default:
                throw new Exception("Unsupported data type: ".$this->replyType);
        }
    }
    
    // Handlers
    
    public function test_function() {
        $this->setData("test-result", "passed");
        $this->setData("requestType", $this->requestType);
        $this->setData("requestData", $this->requestData);
    }
    
    public function echo_function() {
        $this->setData($this->requestData);
    }
}

?>