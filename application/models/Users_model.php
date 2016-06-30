<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getUsers(){
        $this->db->select('users.*, schools.*, users.id AS pid, credits.*');
        $this->db->from('users');
        $this->db->join('permissions', 'permissions.pid = users.id');
        $this->db->join('schools', 'schools.id = users.sid');
        $this->db->join('credits', 'credits.sid = users.sid');
        $this->db->where('users.sid != 1');
        $query = $this->db->get();

        return $query->result();
    }

}
