<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');

class suggestions extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Suggestions_model');
        $this->load->model('Jwt_model');
    }
    
    
    public function index_options()
    {
        $this->response(200);
    }
    
    public function index_get()
    {
        // AUTHENTICATE AND VALIDATE JWT
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );
        
        $suggestions = $this->Suggestions_model->getSuggestions();
        foreach($suggestions as $suggestion)
        {
            $suggestion->likes = intVal($suggestion->likes);
        }
        $this->response($suggestions,200);
    }
    
}