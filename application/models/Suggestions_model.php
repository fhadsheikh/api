<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Suggestions_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getApprovedSuggestions()
    {
        $this->db->select('suggestions.id,title,sid,name,status,statusname,submissiondate');
        $this->db->where('status',1);
        $this->db->from('suggestions');
        $this->db->join('schools','schools.id = suggestions.sid');
        $this->db->join('status','status.id = suggestions.status');
        $query = $this->db->get();

        return $query->result();
    }

    public function getAllSuggestions()
    {
        $this->db->select('suggestions.id,title,sid,name,status,statusname,submissiondate');
        $this->db->from('suggestions');
        $this->db->join('schools','schools.id = suggestions.sid');
        $this->db->join('status','status.id = suggestions.status');
        $query = $this->db->get();

        return $query->result();
    }

    public function getSuggestion($id)
    {
        $this->db->select('*');
        $this->db->where('suggestions.id',$id);
        $this->db->from('suggestions');
        $this->db->join('schools','schools.id = suggestions.sid');
        $query = $this->db->get();

        return $query->row();
    }

    public function getMySuggestions($schoolID)
    {
        $this->db->select('suggestions.id,title,sid,name,status,submissiondate');
        $this->db->where('sid',$schoolID);
        $this->db->where('status != 2');
        $this->db->from('suggestions');
        $this->db->join('schools','schools.id = suggestions.sid');

        $query = $this->db->get();

        return $query->result();
    }

    public function createSuggestion($title,$summary,$sid,$pid)
    {
        $data = array(
            'title'=>$title,
            'summary'=>$summary,
            'sid'=>$sid,
            'pid'=>$pid
        );

        $this->db->insert('suggestions',$data);

        return $this->db->insert_id();
    }

    public function getVotes($suggestionID)
    {
        $this->db->select('*');
        $this->db->from('sb_votes');
        $this->db->where('suggestion_id',$suggestionID);
        return $this->db->count_all_results();
    }

    public function submitVote($sid,$pid,$suggestionID)
    {

        // Check if this suggestion is approved before submitted vote
        $this->db->select('status');
        $this->db->from('suggestions');
        $this->db->where('id',$suggestionID);
        $query = $this->db->get();
        $result = $query->row();

        if($result->status != 1){
            return 'Not approved for voting yet';
        }

        // Check if someone with this $sid has already voted
        $query = $this->db->query('SELECT * FROM sb_votes WHERE sid='.$sid.' AND suggestion_id='.$suggestionID);
        $votes = $query->num_rows();

        if(!$votes)
        {
            $data = array(
                'sid'=>$sid,
                'pid'=>$pid,
                'suggestion_id'=>$suggestionID
            );

            $this->db->insert('sb_votes',$data);
        } else {
            return false;
        }

    }

    public function hasVoted($sid,$suggestionID)
    {
        $this->db->select('*');
        $this->db->from('sb_votes');
        $this->db->join('users','users.id = sb_votes.pid');
        $this->db->join('schools','schools.id = users.sid');
        $this->db->where('sb_votes.sid',$sid);
        $this->db->where('sb_votes.suggestion_id',$suggestionID);
        $query = $this->db->get();

        return $query->row();

    }


    public function getMessages($suggestionID)
    {
//        $query = $this->db->get_where('sb_messages',array('suggestion_id'=>$suggestionID));
//        return $query->result();

        $this->db->select('*');
        $this->db->where('sb_messages.suggestion_id',$suggestionID);
        $this->db->from('sb_messages');
        $this->db->join('users','users.id = sb_messages.pid');
        $query = $this->db->get();

        return $query->result();
    }

    public function submitMessage($data)
    {
        $this->db->insert('sb_messages',$data);

        $query = $this->db->get_where('sb_messages',array('message'=>$data['message']));
        return $query->row();
    }

    public function getMessage($id)
    {
//        $query = $this->db->get_where('sb_messages',array('id'=>$id));
//        return $query->row();

        $this->db->select('*');
        $this->db->where('sb_messages.id',$id);
        $this->db->from('sb_messages');
        $this->db->join('users','users.id = sb_messages.pid');
        $query = $this->db->get();

        return $query->row();

    }

    public function getRecent($limit)
    {
        $this->db->select('id,title');
        $this->db->where('status',1);
        $this->db->from('suggestions');
        $this->db->order_by('submissiondate', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }

    public function updateStatus($id,$status,$whoApproved)
    {
        $data = array(
            'status' => $status,
            'who_approved' => $whoApproved
        );

        $this->db->where('id',$id);
        $this->db->update('suggestions',$data);

        return $data;


    }
    
        
    public function updateSuggestion($id, $summary)
    {
        $data = array(
            'summary'=>$summary
        );
        
        $this->db->where('id',$id);
        $this->db->update('suggestions',$data);
    }


}
