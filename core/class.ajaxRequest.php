<?php
// class.ajaxRequest.php

function logWrite($msg) {
   $fp = fopen("./log.txt", 'a');
   fwrite($fp, $msg."\n");
   fclose($fp);
}

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
    const JSON = "application/json";
    const PLAIN = "text/plain";
    
    // Request Types
    const GET = "GET";
    const POST = "POST";
    const OPTIONS = "OPTIONS";
    
    // Reply Data
    private $replyData = array();
    private $replyType = self::JSON;
    
    // Request Data
    private $requestData = array();
    private $serverData = array();
    private $requestType = self::JSON;
    private $requestMethod = self::POST;
    private $requestAction = "";
    private $output = array();
    
    public function __construct($requestData = null) {
        $raw = file_get_contents("php://input");
        logWrite("Raw: ".print_r($raw,true));
	logWrite("Server: ".print_r($_SERVER,true));
        $this->serverData = $_SERVER;

        // What is our content type?
       	$cType = explode(", ", $_SERVER['HTTP_ACCEPT']);
	switch($cType[0]) {
		case self::JSON:
			$this->requestType = self::JSON;
			$this->requestData = json_decode($raw, true);
			break;
		default:
			$this->requestType = self::PLAIN;
			$this->requestData = $raw;
			break;
	}

        logWrite("cType: ".print_r($cType,true));

        switch($_SERVER['REQUEST_METHOD']) {
            case self::GET:
                $this->requestMethod = self::GET;
                break;
            case self::POST:
                $this->requestMethod = self::POST;
                break;
            case self::OPTIONS:
                $this->requestMethod = self::OPTIONS;
                $this->write();
                return;
            default:
                throw new Exception("Invalid Request Type");
        }
        
        if(is_array($serverData) && !empty($serverData))
            $this->serverData = $serverData;
        else if(is_array(($serverData = $_SERVER)) && !empty($serverData))
            $this->serverData = $serverData;
        else
            throw new Exception("No Server Data or invalid data: ", $serverData);

	foreach($this->requestData as $req){
		if(!isset($req['action']) || empty($req['action']))
	            throw new Exception("No action in request.");
		if(!array_key_exists($req['action'], self::$actions))
	            throw new Exception("Unsupported Action: ".$this->requestAction);
                $action = $req['action'];
		$handler = self::$actions[$action];
		$this->$handler($req['data']);
	}
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
	header('Content-type: '.self::JSON);
        $replyJson = json_encode($this->replyData);
        $this->output[]= $replyJson;
        return;        
    }
    
    // Wrapper for different data types (future proofing)
    public function write() {
        switch($this->replyType) {
            case self::JSON:
                logWrite(print_r($this, true));
                $this->writeJson();
                break;
            default:
                throw new Exception("Unsupported data type: ".$this->replyType);
        }
		$this->replyData = array();
    }
    
	public function flush() {
	echo json_encode($this->output);
}
    // Handlers
    
    public function test_function($data) {
        $this->setData("test-result", "passed");
	foreach($data as $key => $value)
		$this->setData("request-{$key}", $value);
        $this->setData("requestData", json_encode($data));
	$this->setData("status","success");
	$this->write();
    }
    
    public function echo_function($data) {
        $this->setData($data);
	$this->setData("status","success");
	$this->write();
    }
}

?>
