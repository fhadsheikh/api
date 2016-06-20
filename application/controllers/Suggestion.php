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

        $sid = $this->Jwt_model->user['sid'];
        $suggestionID = $this->get('id');

        $suggestion = $this->Suggestions_model->getSuggestion($suggestionID);

        if(!$suggestion){$this->response('Suggestion not found',404);}

        $suggestion->id = $suggestionID;
        $suggestion->hasVoted = $this->Suggestions_model->hasVoted($sid,$suggestionID);
        $suggestion->votes = $this->Suggestions_model->getVotes($suggestionID);
        $this->response($suggestion,200);
    }

    public function index_post()
    {
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $user = $this->Jwt_model->user;

        $title = $this->post('title');
        $summary = $this->post('summary');
        $sid = $user['sid'];
        $pid = $user['pid'];

        $insertID = $this->Suggestions_model->createSuggestion(
            $title,
            $summary,
            $sid,
            $pid
        );

        $data = array(
            'pid'=>18,
            'message'=>'Suggestion submitted',
            'suggestion_id'=>$insertID,
            'techsonly'=>false
        );

        $this->Suggestions_model->submitMessage($data);

        $this->response('Suggestion was created', 200);
    }

    public function vote_options()
    {
        $this->response(200);
    }

    public function vote_post()
    {
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $user = $this->Jwt_model->user;

        $sid = $user['sid'];
        $pid = $user['pid'];
        $suggestionID = $this->post('id');

        $votes = $this->Suggestions_model->submitVote($sid,$pid,$suggestionID);
        $this->response($votes,200);
    }

    public function messages_options()
    {
        $this->response(200);
    }

    public function messages_get()
    {

        $this->load->library('gravatar');
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $suggestionID =  $this->get('id');

        $suggestion = $this->Suggestions_model->getSuggestion($suggestionID);

        $user = $this->Jwt_model->user;

        if($user['permissions']['admin'] == 0) {
            if($suggestion->sid != $user['sid'])
            {
                $this->response(403);
            }
        }


        $messages = $this->Suggestions_model->getMessages($suggestionID);

        foreach($messages as $message){
            $message->gravatar = $this->gravatar->getUrl($message->email);
        }

        $this->response(array_reverse($messages),200);
    }

    public function message_options()
    {
        $this->response(200);
    }

    public function message_post()
    {
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $user = $this->Jwt_model->user;

        $pid = $user['pid'];
        $message = $this->post('message');
        $suggestionID = $this->post('id');
        $techsonly = true;

        $data = array(
            'pid'=>$pid,
            'message'=>$message,
            'suggestion_id'=>$suggestionID,
            'techsonly'=>$techsonly
        );

        $test = $this->Suggestions_model->submitMessage($data);

        $this->response($test,200);

    }

    public function message_get()
    {

        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $id = $this->get('id');

        $this->load->library('gravatar');

        $message = $this->Suggestions_model->getMessage($id);
        $message->gravatar = $this->gravatar->getUrl($message->email);

        $this->response($message,200);
    }

    public function checkpending_options()
    {
        $this->response(200);
    }

    public function checkpending_get()
    {

        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $id = $this->get('id');

        if(!$id)
        {
            $this->response('No ID supplied', 400);
        }

        $suggestion = $this->Suggestions_model->getSuggestion($id);

        if($suggestion->status == 0)
        {
            $this->response(true,200);
        } else {
            $this->response(false, 404);
        }
    }

    public function checkapproved_options()
    {
        $this->response(200);
    }

    public function checkapproved_get()
    {
        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $id = $this->get('id');

        if(!$id)
        {
            $this->response('No ID supplied', 400);
        }

        $suggestion = $this->Suggestions_model->getSuggestion($id);

        if(!$suggestion)
        {
            $this->response('Suggestion not found', 404);
        }

        if($suggestion->status == 1)
        {
            $this->response(true,200);
        } else {
            $this->response(false, 404);
        }
    }

    public function isowner_options()
    {
        $this->response(200);
    }

    public function isowner_get()
    {

        (!$this->Jwt_model->authenticate(getallheaders()) ? $this->response($this->Jwt_model->error,403) : false );

        $id = $this->get('id');

        if(!$id)
        {
            $this->response('No ID supplied', 400);
        }

        $suggestion = $this->Suggestions_model->getSuggestion($id);

        $user = $this->Jwt_model->user;

        if($suggestion->sid == $user['sid'])
        {
            $this->response(true,200);
        } else {
            $this->response(false, 404);
        }
    }




}
