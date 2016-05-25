<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with'); 

class mysuggestions extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Suggestions_model');
    }
    
    public function index_options()
    {
        $this->response(200);
    }
    
    public function index_get()
    {
        $schoolID = $this->get('id');
        $mySuggestions = $this->Suggestions_model->getMySuggestions($schoolID);
        $this->response($mySuggestions, 200);
    }
    
}