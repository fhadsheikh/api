<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');

class suggestion extends REST_Controller {
    
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
        $id = $this->get('id');
        $data = $this->Suggestions_model->getSuggestion($id);
        $this->response($data,200);
    }
    
}