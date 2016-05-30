<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Suggestions_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getSuggestions()
    {
        $this->db->select('suggestions.id,title,sid,likes,name');
        $this->db->where('status',1);
        $this->db->from('suggestions');
        $this->db->join('schools','schools.id = suggestions.sid');
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
        $this->db->select('suggestions.id,title,sid,likes,name');
        $this->db->where('sid',$schoolID);
        $this->db->from('suggestions');
        $this->db->join('schools','schools.id = suggestions.sid');
        
        $query = $this->db->get();
        
        return $query->result();
    }
    
}