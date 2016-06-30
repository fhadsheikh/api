<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');

class techs extends REST_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Techs_model');
        $this->load->model('Jwt_model');
    }
    
    public function index_options()
    {
        $this->response(200);
    }
    
    public function index_get()
    {
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $user = $this->Jwt_model->user;

        if($user['permissions']['admin'] == 0){
            $this->response(403);
        }
        
        $techs = $this->Techs_model->getTechs();
        
        $this->response($techs, 200);
    }
    
}