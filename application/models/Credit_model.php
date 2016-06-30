<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function redeem($sid,$amount){
        $this->db->select('amount');
        $this->db->from('credits');
        $this->db->where('sid',$sid);
        $query = $this->db->get();
        $credits = $query->row();
        
        $final = $credits->amount-$amount;
        
        $data = array(
            'amount'=>$final
        );
        
        $this->db->where('sid',$sid);
        $this->db->update('credits',$data);
    }
    
    
    
}