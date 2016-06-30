<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class School_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getSchool($id){
        $query = $this->db->get_where('schools', array('id'=>$id));
        return $query->row();
    }
    
    public function createSchool($sid, $name){
        
        $data = array(
            'id' => $sid,
            'name' => $name
        );
        
        $this->db->insert('schools', $data);
    }
    
}