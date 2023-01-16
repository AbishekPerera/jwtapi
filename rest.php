<?php
require_once('constants.php');
    class Rest
    {
    protected $request;
    protected $serviceName;
    protected $param;

        public function __construct(){
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->throwError(REQUEST_METHOD_NOT_VALID, 'Request Method is not Valid.');
                # code...
            echo "method is not post";
            }
        $handler = fopen('php://input', 'r');
        $this->request = stream_get_contents($handler);
        $this->validateRequest();

        }

        // validate api name and params::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        public function validateRequest(){
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
            $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID, 'Request Content type is not valid');
            }

        $data = json_decode($this->request, true);
        
        if (!isset($data['name']) || $data['name']=="") {
            # code...
            $this->throwError(API_NAME_REQUIRED, "API name is required");
        }
        $this->serviceName = $data['name'];

        if (!is_array($data['param'])) {
            # code...
            $this->throwError(API_PARAM_REQUIRED, "API Param required");
        }
        $this->param = $data['param'];


        }

        // call API acording to name ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        public function processApi(){
            try {
				$api = new API;
				$rMethod = new reflectionMethod('API', $this->serviceName);
				if(!method_exists($api, $this->serviceName)) {
					$this->throwError(API_DOST_NOT_EXIST, "API does not exist.");
				}
				$rMethod->invoke($api);
			} catch (Exception $e) {
				$this->throwError(API_DOST_NOT_EXIST, "API does not exist.");
			}
        }

        public function validateParameter($fieldName, $value, $dataType, $requred=true){

            if($requred==true && empty($value)==true){
            $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldName . "Parameter is required.");
            }

        }

        public function throwError($code, $message){
            header("content-type: application/json");
            $errorMsg=json_encode(['error' => ['state' => $code, 'message' => $message]]);
            echo $errorMsg;
            exit;

        }

        public function returnResponse(){

        }
        
    }
    

?>