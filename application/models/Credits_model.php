<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Credits_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getCredits($sid)
    {
        $this->db->select('*');
        $this->db->from('credits');
        $this->db->where('sid',$sid);
        $query = $this->db->get();
        
        return $query->row()->amount;
    }
    
}