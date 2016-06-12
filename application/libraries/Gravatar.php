<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gravatar {
    
    public function getUrl($email){
        $hash = md5( strtolower( trim($email) ) );
        
        return 'https://www.gravatar.com/avatar/'.$hash.'?d=retro';
    }
    
}

?>