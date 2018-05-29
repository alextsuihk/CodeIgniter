<?php

class Register extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");

        // redirect if do not have privilege
        $required_priv = array('REG');
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        $this->load->model('m_crm');
        $this->load->model('m_register');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->library('mobiledetect');
    }

    function generate_tab()
    {
        $tab = array(
            "样品申请"=>"register?action=request_list",
            "客人地址"=>"register?action=addr_list",
            "库存"=>"register?action=inv_summary"
            );
        return ($tab);
    }

    function index()
    { 
        $is_mobile = $this->mobiledetect->is_mobile();      // accessing from iPhone, iPOD, Android, mobile devices

        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','edit','add'])? $action:'list';

        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;

        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'RequestDate';
        $sort_by = in_array($sort_by, ['RequestDate','Customer','CompanyCode'])? $sort_by:'RequestDate';
        
        if (isset($_GET['so'])) {
            $sort_order =  $_GET['so'];
        } elseif ($sort_by =="RequestDate") {
            $sort_order = "desc";
        } else {
            $sort_order = "asc";
        }

        $query  = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");

        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;

        if (strstr($this->session->userdata['priv'], 'REG12')) {
            $disti_id = 0;
        } else {
            $disti_id = intval($this->session->userdata['company_id']);
        }

        $data['cancel_url']   = "register?action=list";
        if (strstr($this->session->userdata['priv'], 'REG0')) {
          $data['download_url'] = "register/download"; 
        } else {
          $data['download_url'] = "NULL";
        }
        if ($action == 'edit')
        {
            $id = intval($query['ID']);
            $results = $this->m_register->query(1, $sort_by, $sort_order, $query, 0, $option, $disti_id);
            $data['edit']  = $results['rows'];          // get original data
            //
            // pass current URL, for top/edit CLEAR button
            $newoption = urlencode($option);
            $query_url = urlencode(json_encode(array("ID"=>$id)));
            $data['clear_url']     = "register?action=edit&query=$query_url&option=$newoption";
            //$data['cancel_url']    = "register?action=list&query=$query_url&option=$newoption";
            $data['submit_button'] = "UPDATE";

        } elseif ($action == 'add') {           // if adding (OEM)
            $id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'RequestDate'       => date("Y-m-d"),
                'Customer'          => '',
                'DistiID'           => '0',
                'StatusID'          => '0',
                'ApproveDate'       => '1900-01-01'
            ));
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "register?action=add";
            $data['submit_button'] = "ADD";
        } else {
            $id=0;
        }
        
        $data['disti_list'] = $this->m_crm->disti_list();
        $data['disti_id']   = $disti_id;

        $results = $this->m_register->query($limit, $sort_by, $sort_order, $query, $offset, $option, $disti_id);

        $data['id']                 = $id;
        $data['customers']          = $results['rows'];
        $data['matched_records']    = $results['matched_records'];     // $ of matched record (where)
        $data['total_records']      = $results['total_records'];       //
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['option']             = urlencode($option);
        
        $data['searchby_options']   = $this->m_register->searchby_options();
        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("register?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
        $config['total_rows']  = $results['matched_records'];
        $config['per_page']    = $limit;
       // $config['uri_segment'] = 7;           // auto detect
        $config['num_links'] = 3;
        $config['first_link'] = 'First ';
        $config['last_link'] = ' Last';
        $config['num_tag_open'] = '&nbsp;&nbsp;';
        $config['num_tag_close'] = '&nbsp;&nbsp;';
        $config['cur_tag_open'] = ' <b> ';
        $config['cur_tag_close'] = ' </b> ';


        switch ($action) {
            case 'list': $data['title'] = "Customer Registration"; break;
            case 'edit': $data['title'] = "TBD"; break;
            case 'add':  $data['title'] = "Register New Customer";  break;
            default:     $data['title'] = "ERROR"; break;
        }

        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['scripts'] = '/register_scripts';
        $data['main_content'] = '/register';
        $data['favorite'] = $this->session->userdata('favorite');
//        $data['tab'] = $this->generate_tab();
        $data['html_title'] = 'Customer Registration';
        $this->load->view('includes/template',$data);
  }

    function search()               
    {
        $limit          = $this->input->post('limit');
        $sort_by        = $this->input->post('sort_by');
        $sort_order     = $this->input->post('sort_order');
//        $active_only    = $this->input->post('active_only');
        $query        = array(); 
        $submit = explode (":",$this->input->post('submit'));
        $submit[1] = (int) $submit[1];
        if ($submit[0] == 'delete') {
            for ($i=0; $i<5; $i++)      // max 5 query
            {
                $key = $this->input->post("searchby[$i]");
                $value = $this->input->post("search_value[$i]");
                if (($i != $submit[1]) && $key != "NULL" && $key != "")
                {
                    $query[$key] = $value;
                }
            }
            if ((sizeof($query)) == 0 ) { $query["NULL"] = ""; }       // if deleted all, add a blank

        } elseif ($submit[0] == 'add' || $submit[0] == 'search') {
            for ($i=0; $i<5; $i++)                  // copy first 5 query if any
            {
                $key = $this->input->post("searchby[$i]");
                $value = $this->input->post("search_value[$i]");
                if ($key != "NULL" && $key != "")
                {
                    $query[$key] = $value;
                }
            }

            if ($submit[0]=='add') { $query["NULL"] = ""; }
        }        

        if (sizeof($query) == 0) { $query["NULL"] = ""; }

        $option = "";
        if ($active_only ==1)    {$option = $option."activeonly "; }
        $message = "search completed";
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode($query));
        redirect("register?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
      }

    function edit()             // top-level Part Number edit
    {
        $action      = $this->input->post('action');
        $id          = $this->input->post('id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');

        $data = array(
            'RequestDate'       => date("Y-m-d"),
            'Customer'          => $this->input->post('customer'),
            'DistiID'           => $this->input->post('disti_id'),
            'StatusID'          => 0,
            'ApproveDate'       => "1900-01-01"
        );

        if ($action=='edit') {
            die("STOP: error"); 
            $result     = $this->m_register->update($id, $data);
            $message    = "Record Updated Sucessfully";
            $this->session->set_flashdata('message',$message);
        } elseif ($action == 'add') {
            $id = $this->m_register->add($data);
            switch ($id) {
                case -1:    $message = "Something is wrong"; break;
                default:    $message = "Record Added Sucessfully";
            }
            $this->session->set_flashdata('message',$message);
        }

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("register?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function process()
    {
        $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
        $query        = $this->input->post('query');
        $option       = $this->input->post('option');

        $id           = intval($this->input->post('id'));
        $submit       = $this->input->post('submit');
        
        $results   = $this->m_register->query(1, $sort_by, $sort_order, array("ID"=>$id), 0, $option, 0, 0);
        $customer  = $results['rows'][0];          // get original data
        
        $update =array();
        $update['ApproveDate'] = date("Y-m-d");
        $update['ChangeLog'] = "{".date("Y-m-d").":".$submit." by ".$this->session->userdata('nickname')."} ".$customer['ChangeLog'];
        if ($submit == "approved") {
            $update['StatusID'] = 1;
            $message = "Approved";
        } else {
            $update['StatusID'] = 2;
            $message = "Reject";
        }
        $this->m_register->update($id, $update);
                
        $this->session->set_flashdata('message',$message);

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("register?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
}

?>