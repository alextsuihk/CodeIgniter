<?php

    
class Change_Passwd extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true)
        {
            echo 'You need to login first <a href=".">Login</a>';
            die();
        }
        $this->load->model('m_change_passwd');
    }
    
    function index()
    {
        $data['email']  = $this->session->userdata['email'];
        $data['message']= $this->session->flashdata('message');
        $data['html_title'] = 'Change Password';
        $this->load->view('change_passwd_form', $data);
    }

    function validate()
    {
        if (($this->input->post('password')) == '') {
            $message = "No blank password";
            $this->session->set_flashdata('message',$message);
            redirect('change_passwd'); 
        }
        if (($this->input->post('password')) != ($this->input->post('password2'))) {
            $message = "Passwords NOT match";
            $this->session->set_flashdata('message',$message);
            redirect('change_passwd'); 
        }
        if (strlen($this->input->post('password')) < 6) {
            $message = "Passwords must be at least 6 characters";
            $this->session->set_flashdata('message',$message);
            redirect('change_passwd');             
        }
        
        $data = array('Password' => md5($this->input->post('password')));
        $id = $this->session->userdata['user_id'];
        
        $query = $this->m_change_passwd->update_record($id, $data);

        $message = "Password updated";
        $this->session->set_flashdata('message',$message);
        redirect('main_menu'); 
    }
    

}

?>
