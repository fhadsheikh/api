<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: accept, authorization,x-requested-with');
header('Access-Control-Allow-Methods: DELETE');

class user extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jwt_model');
        $this->load->model('User_model');
        $this->load->model('Credits_model');
    }

    public function delete_options()
    {
        $this->response(200);
    }

    public function delete_post()
    {
        $pid = $this->post('pid');
        $this->User_model->deleteUser($pid);
        $this->response($pid, 200);
    }
    
    public function permissions_options()
    {
        $this->response(200);
    }
    
    public function permissions_post()
    {
        $pid = $this->post('pid');
        $admin = $this->post('admin');
        $client = $this->post('client');
        
        $this->User_model->updatePermissions($pid, $admin, $client);
        $this->response('Permissions updated');
    }
    
    public function credits_options()
    {
        $this->response(200);
    }
    
    public function credits_get()
    {
        
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $user = $this->Jwt_model->user;
        
        $credits = $this->Credits_model->getCredits($user['sid']);
        
        $this->response($credits,200);
    }


}
