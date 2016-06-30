<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Techs_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getTechs()
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('permissions', 'permissions.pid = users.id');
        $this->db->join('credits', 'credits.sid = users.sid');
        $this->db->where('users.sid', 1);
        $query = $this->db->get();
        return $query->result();
    }
    
}