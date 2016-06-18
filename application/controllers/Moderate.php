<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');

class moderate extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Suggestions_model');
    }
    
    public function suggestions_options(){
        $this->response(200);
    }
    
    public function suggestions_get(){
        $suggestions = $this->Suggestions_model->getAllSuggestions();
        
        $this->response($suggestions);
    }
    
    
}