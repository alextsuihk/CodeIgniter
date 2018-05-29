<?php

class Logout extends CI_Controller {
    function index()
    {
        if ($this->session->userdata('is_logged_in') == TRUE)
        {
            date_default_timezone_set("Asia/Taipei");
            $this->session->unset_userdata('is_logged_in');
            $this->load->model('m_login');
            $data = array(
                'UserID'    => $this->session->userdata['user_id'],
                'Event' => 'logout: '.$this->session->userdata['email'],
                'RemoteIP' => (isset($_SERVER['HTTP_X_REAL_IP']))?$_SERVER['HTTP_X_REAL_IP']:"unknown",
                'UserAgent'=> $_SERVER['HTTP_USER_AGENT'] 
            );
            $this->m_login->login_log($data);
        }
        $this->session->sess_destroy();
        redirect('.');    
    }
}
?>