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
        $this->load->model('School_model');
        $this->load->library('email');
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
                'permissions'=> $dbUserPermission,
                'credits' => $dbUser->amount
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
        
        if($username == null || $password == null){
            $this->response('No Credentials Supplied', 403);
        }
        
        $user = $this->Helpdesk_model->authenticate($username,$password);
        
        $helpdesk_id = $user->UserID;
        $firstname = $this->post('firstname');
        $lastname = $this->post('lastname');
        $sid = $user->CompanyId;
        $title = $this->post('title');
        $email = $this->post('email');
        
        $dbUser = $this->User_model->getUser($user->UserID);
        
        if($dbUser){
            $this->response('User already exists', 200);
        }
        
        // If user doesn't belong to a company, create their acccount without permissions
        if($sid == null){
            
            $sid = 0;
            
            if($firstname && $lastname && $title && $email){
                
                $this->User_model->createUser($helpdesk_id,$firstname,$lastname,$title,$sid,$email);
                
            } else {
                
                $this->response('Invalid parameters', 400);
                
            }
                    
            $this->email->from('fhad@clockworks.ca');
            $this->email->to('fhad@clockworks.ca');
            $this->email->subject('Action Required: New sign up without company');
            $this->email->message("$firstname $lastname signed up to the online portal. $firstname does not belong to a company. Please verify if this user is our client then create a company for their school");
            $this->email->send();
            
            $this->response('Account was created but cannot be used until Company is set up', 200);
        }
        
        // If company doesn't exist, add it, and create user with client permissions
        $company = $this->School_model->getSchool($user->CompanyId);
        
        
        if($company == null){
            
            $this->School_model->createSchool($sid, $user->CompanyName);
            
        }
        
        // Create User
        
        $this->User_model->createUser($helpdesk_id,$firstname,$lastname,$title,$sid,$email);
        
        $dbUser = $this->User_model->getUser($user->UserID);
        
        $admin = false; 
        $client = true;
        
        $this->User_model->updatePermissions($dbUser->id, $admin, $client);
        
        $this->email->from('fhad@clockworks.ca');
        $this->email->to($email);
        $this->email->subject('Welcome to ClockWork Portal');
        $this->email->message("Hi $firstname, An account for you has been successfully created.");
        $this->email->send();
        
        $this->email->from('fhad@clockworks.ca');
        $this->email->to('fhad@clockworks.ca');
        $this->email->subject('New user signed up to the portal');
        $this->email->message("$firstname $lastname signed up online");
        $this->email->send();
        
        $this->response('User Created', 201);
        
    }
    
    
    
}