<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');


class Auth extends REST_Controller {
    

    public function __construct(){
        parent::__construct();
        $this->load->model('Helpdesk_model');
        $this->load->model('User_model');
    }
    
    public function index_options()
    {
        $this->response(200);
    }
        
    // Log In
    public function index_post()
    {
        
        $secret = 'fhadsheikh';
        
        $username = $this->post('username');
        $password = $this->post('password');
        
        if($username == null || $password == null){
            $this->response('No Credentials Supplied', 403);
        }
        
        $user = $this->Helpdesk_model->authenticate($username,$password);
        
        if(!$user){
            $this->response('Invalid Credentials', 403);
        }
        
        $dbUser = $this->User_model->getUser($user->UserID);        
        
        if(!$dbUser){
            $this->response('User not registered', 403);
        }
        
        $dbUserPermission = $this->User_model->getPermissions($dbUser->id);
        
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
                'name'=>$dbUser->firstname.' '.$dbUser->lastname,
                'firstname'=>$dbUser->firstname,
                'sid' => $dbUser->sid,
                'pid' => $dbUser->id,
                'permissions'=> $dbUserPermission
            )
        );
        
        $header = base64_encode(json_encode($headerArray));
        $payload = base64_encode(json_encode($payloadArray));        
        
        $hash = base64_encode(hash_hmac('sha256',$header.".".$payload,$secret,true));
        
        $jwt = $header.".".$payload.".".$hash;
        
        $this->response($jwt,200);
    }
    
    public function user_options()
    {
        $this->response(200);
    }
    
    // Create user
    public function user_post()
    {
        
        $username = $this->post('username');
        $password = $this->post('password');
        
        $user = $this->Helpdesk_model->authenticate($username,$password);
        
        $helpdesk_id = $user->UserID;
        $firstname = $this->post('firstname');
        $lastname = $this->post('lastname');
        $sid = $user->CompanyId;
        $title = $this->post('title');
        $email = $this->post('email');
        
        $this->User_model->createUser($helpdesk_id,$firstname,$lastname,$title,$sid,$email);
        
        $this->response(201);
        
    }
    
    
    
}