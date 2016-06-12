<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jwt_model extends CI_Model {
    
    private $jwtToken;
    
    private $jwtArray;
    
    public $jwt;
    
    public $error;
    
    public $user;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function authenticate($headers)
    {
        $this->jwtToken = $this->getJwtFromHeader($headers);
                
        if(!$this->jwtToken || $this->jwtToken == null){
            $this->error = 'No Authorization header Found';
            return false;
        }
        
        $this->jwtArray = $this->parseJwt($this->jwtToken);
        
        if(!$this->jwtArray){
            $this->error = 'Authorization header was not formatted correctly';
            return false;
        }
        
        if($this->validateJwt($this->jwtArray)){
            return $this->validateJwt($this->jwtArray);
        } else {
            $this->error = 'Authorization token has been altered';
            return false;
        }
    }
    
    public function getJwtFromHeader($headers)
    {
        foreach($headers as $key => $header){
                    
            if($key == 'Authorization'){
                return $header;
            } 
        }
    }
    
    public function parseJwt($jwt)
    {
        $jwtSplit = explode('.', $jwt);
        
        if(count($jwtSplit) != 3){
            return false;
        }
        
        $header = json_decode(base64_decode($jwtSplit[0]),true);
        $payload = json_decode(base64_decode($jwtSplit[1]),true);
        
        $this->user = $payload['data'];
        
        return $this->jwt = array(
            'header' => $header,
            'payload' => $payload,
            'hash' => base64_decode($jwtSplit[2])
        );
    }
    
    public function validateJwt($jwt)
    {
        $secret = 'fhadsheikh';       
        
        $header = base64_encode(json_encode($jwt['header']));
        $payload = base64_encode(json_encode($jwt['payload']));  
        
        $hash = base64_encode($jwt['hash']);
        $newHash = base64_encode(hash_hmac('sha256',$header.".".$payload,$secret,true));
        
        return $hash == $newHash;
        
    }
    
    
    
}