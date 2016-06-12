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
        $this->load->model('Jwt_model');
        $this->load->model('Helpdesk_model');
    }
    
    public function index_options()
    {
        $this->response(200);
    }
    
    public function index_get()
    {
        // AUTHENTICATE AND VALIDATE JWT
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );
        
        $user = $this->Jwt_model->user;
        
        $mySuggestions = $this->Suggestions_model->getMySuggestions($user['sid']);
        foreach($mySuggestions as $key => $suggestion)
        {
            $suggestion->votes = $this->Suggestions_model->getVotes($suggestion->id);
            $suggestion->hasVoted = $this->Suggestions_model->hasVoted($user['sid'],$suggestion->id);
        }
        $this->response($mySuggestions, 200);
    }
    
    
}