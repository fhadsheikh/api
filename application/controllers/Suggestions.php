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

        $user = $this->Jwt_model->user;

        $suggestions = $this->Suggestions_model->getApprovedSuggestions();

        foreach($suggestions as $key => $suggestion)
        {
            $suggestion->votes = $this->Suggestions_model->getVotes($suggestion->id);
            $suggestion->hasVoted = $this->Suggestions_model->hasVoted($user['sid'],$suggestion->id);
        }

        $this->response($suggestions,200);
    }

    public function recent_options()
    {
        $this->response(200);
    }

    public function recent_get()
    {
        $recent = $this->Suggestions_model->getRecent(3);
        $this->response($recent,200);

    }

}
