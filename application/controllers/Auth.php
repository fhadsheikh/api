<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');


class Auth extends REST_Controller {

    public function __construct(){
        parent::__construct();
    }
    
    public function index_options()
    {
        $this->response(200);
    }
    
    // Get logged in user (used for checking login status and permissions)
    public function index_get(){
        echo "hello";
    }
    
    // Log In
    public function index_post(){
        
        $secret = 'fhadsheikh';
        
        $now = time();
        $expire = strtotime('tomorrow');
        
        $headerArray = array(
            'alg'=>'HS256',
            'typ'=>'JWT'
        );
        
        $payloadArray = array(
            'iat'=>$now,
            'jti'=>base64_encode(mcrypt_create_iv(32)),
            'iss'=>'clockworks.ca',
            'nbf'=>$now,
            'exp'=>$expire,
            'data'=>array(
                'name'=>'Fhad Sheikh',
                'permissions'=> array('read','admin')
            )
        );
        
        $header = base64_encode(json_encode($headerArray));
        $payload = base64_encode(json_encode($payloadArray));        
        
        $hash = base64_encode(hash_hmac('sha256',$header.".".$payload,$secret,true));
        
        $jwt = $header.".".$payload.".".$hash;
        
        $this->response($jwt,200);
    }
    
    // Log out
    public function index_delete(){

    }
    
}