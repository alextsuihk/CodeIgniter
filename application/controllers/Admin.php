<?php

class Admin extends CI_Controller {

    function __construct()
    {
        parent::__construct();       
        date_default_timezone_set("Asia/Taipei");

        // redirect if do not have privilege
        $required_priv = array('ADM'); 
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->model('m_admin');
    }
    
    function index() 
    {
    redirect('');
    }
    
    function update_seq_number()
    {
        $this->load->model('m_admin');
        $record = $this->m_admin->update_seq_number();

        if (empty($record)) {
            $message = "NO Seq.Number is updated";
        } else {
            $message = "Seq.Number is updated: $record";
        }
        $this->session->set_flashdata('message',$message);
        redirect("main_menu"); 
    }

    function term($type="PaymentTerms")
    {
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'Terms';
        $sort_by = in_array($sort_by, ['ID', 'Terms', 'TermsDetail'])? $sort_by:'Terms';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $table = "tb_".$type;
        
//        $data['cancel_url']   = "product";
        $data['download_url'] = "NULL";
        
        if ($action == 'edit' || $action == 'view')
        {
            $term_id = intval($query['ID']);
            $results = $this->m_admin->term_query($table, 1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$term_id)));
            $data['clear_url'] = "admin/payment_term?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $product_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'Terms'         => '',
                'TermsDetail'   => '',
                'AMS'           => 0,
                'Days'          => 0
            ));
            // pass current URL, for top/edit CLEAR button
           $data['clear_url'] = "admin/payment_term?action=add";
            $data['submit_button'] = "ADD";
        } else {                                // if Listing
            $term_id = 0;
        }
        
        $results = $this->m_admin->term_query($table, $limit, $sort_by, $sort_order, $query, $offset, $option);
        
        $data['terms']               = $results['rows'];
        $data['matched_records']    = $results['matched_records'];      // $ of matched record (where)
        $data['total_records']      = $results['total_records'];        //
        $data['term_id']            = $term_id;                      // pass data to view, and to next controller
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['type']               = $type;
        $data['option']             = urlencode($option);
        
//        $data['searchby_options']   = $this->m_product->searchby_options();       // no need to search
          
        //pagination
        $config                     = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']         = site_url("product?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
        $config['total_rows']       = $results['matched_records'];
        $config['per_page']         = $limit;
       // $config['uri_segment'] = 7;           // auto detect
        $config['num_links']        = 3;
        $config['first_link']       = 'First ';
        $config['last_link']        = ' Last';
        $config['num_tag_open']     = '&nbsp;&nbsp;';
        $config['num_tag_close']    = '&nbsp;&nbsp;';
        $config['cur_tag_open']     = ' <b> ';
        $config['cur_tag_close']    = ' </b> ';

        switch ($action) {
            case 'list':    $data['title'] = "List $type"; break;
            case 'edit':    $data['title'] = "Edit $type"; break;
            case 'add':     $data['title'] = "Add $type";  break;
            case 'view':    $data['title'] = "View Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
 
        $data['message']        = $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        if ($type=="PaymentTerms") {
            $data['html_title']     = 'Payment Terms';
            $data['main_content']   = 'admin/payment_term';
        } else {
            $data['html_title']     = 'Shipment Terms';
            $data['main_content']   = 'admin/shipment_term';
        }
        $data['favorite']       = $this->session->userdata('favorite');
        $this->load->view('includes/template',$data);
    }
    
    function term_edit($type="PaymentTerms")
    {
        $action      = $this->input->post('action');
        $term_id     = $this->input->post('term_id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');
        
        $data = array(
            'Terms'         => $this->input->post('terms'),
            'TermsDetail'   => $this->input->post('term_detail'),
            'AMS'           => ($this->input->post('ams'))?'1':'0',
            'Days'          => $this->input->post('days')
        );

        if ($action=='edit') {
            $result     = $this->m_product->update_record($term_id, $data);
            $message    = "Record Updated Sucessfully";
            $this->session->set_flashdata('message',$message);
        } elseif ($action == 'add') {
            $product_id = $this->m_product->add_record($data);
            switch ($product_id) {
                case -1:    $message = "Duplicated Part Number, fail to add"; break;
                default:    $message = "Record Added Sucessfully";
            }
            $this->session->set_flashdata('message',$message);
        }

        $query_url = urlencode(json_encode(array("ID"=>$product_id)));
        redirect("product?action=view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
}
?>