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
        $this->load->model('Jwt_model');
        $this->load->library('email');
    }

    public function suggestions_options(){
        $this->response(200);
    }

    public function suggestions_get(){
        $suggestions = $this->Suggestions_model->getAllSuggestions();

        $this->response($suggestions);
    }

    public function updatestatus_options(){
        $this->response(200);
    }

    public function updatestatus_post(){

        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $user = $this->Jwt_model->user;

        $id = $this->post('suggestionID');
        $status = $this->post('status');
        $whoApproved = $user['pid'];

        $suggestion = $this->Suggestions_model->getSuggestion($id);

        if($suggestion->status == $status){
            $this->response('Suggestion is already set to '.$status,400);
        }

        $this->Suggestions_model->updateStatus($id, $status, $whoApproved);

        if($status == 1){
            $message = "Suggestion Approved";
        } else if ($status == 2){
            $message = "Suggestion Denied";
        }

        $data = array(
            'pid'=>18,
            'message'=>$message,
            'suggestion_id'=>$id,
            'techsonly'=>false
        );

        $test = $this->Suggestions_model->submitMessage($data);

        $this->response($test, 200);

    }
    
    public function test_post(){
        
        $this->email->from('support@clockworks.ca');
        $this->email->to('azim@clockworks.ca');
        $this->email->subject('Test Email from Angular');
        $this->email->message('Hello son, how are you?');
        $this->email->send();
        
        print_r($this->email);
        
//        $this->response($this->email,200);
        
    }


}
