<?php

class index extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
    }

    function index() 
    {
        $is_logged_in = $this->session->userdata('is_logged_in');        
        if ($is_logged_in == true)
        {
            redirect('main_menu');
        } else {
            redirect('login');
        }      
    }
    
}

