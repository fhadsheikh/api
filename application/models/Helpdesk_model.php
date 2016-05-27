<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Helpdesk_model extends CI_Model {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function authenticate($username,$password){
        
        // Prepare credentials
        $credentials = "Basic ".base64_encode($username.":".$password);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://clockworks.ca/support/helpdesk/api/authorization",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: $credentials",
            "cache-control: no-cache",
            "postman-token: 770d948e-f3d8-bd0d-24f0-ceb3b811a960"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        if(curl_getinfo($curl)['http_code'] != 200){
            return false;
        } else {
            return $response;
        }
        
        curl_close($curl);
        
    }
    
}