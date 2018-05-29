<?php

class Crm extends CI_Controller {

    function __construct()
    {
        parent::__construct();       
        date_default_timezone_set("Asia/Taipei");
        
        // redirect if do not have privilege
        $required_priv = array('CRM'); 
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->model('m_crm');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
    }
    
    function generate_tab()
    {
        $tab = array(
            "User"=>"crm/user",
            "Company"=>"crm/company",
            "Address"=>"crm/address"
            );
        return ($tab);
    }
    
    function index() 
    {
    redirect('crm/company');
    }

    function company()
    {  
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
        
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'Company';
        $sort_by = in_array($sort_by, ['ID', 'CompanyCode', 'CompanyName'])? $sort_by:'CompanyCode';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        
        $option    = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $data['cancel_url']    = "crm/company";
        $data['download_url']  = "crm/company_download";
        if ($action == 'edit' || $action == 'view')
        {
            $company_id = intval($query['ID']);
            $results = $this->m_crm->company_query(1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$company_id)));
            $data['clear_url'] = "crm/company?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $company_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'CompanyCode'   => '',
                'Active'        => '1',
                'CompanyName'   => '',
                'CompanyName2'  => '',
                'Website'       => '',
                'VendorID'      => '0',
                'CustomerID'    => '0',
                'SubconID'      => '0',
                'BillToContactID' => '0',
                'BillToAddressID' => '0',
                'ShipToContactID' => '0',
                'ShipToAddressID' => '0',
                'Note'          => ''
            ));
            $data['submit_button'] = "ADD";
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "crm/company?action=add";
        } else {                                // if Listing
            $company_id = 0;
        }
        $results = $this->m_crm->company_query($limit, $sort_by, $sort_order, $query, $offset, $option);
        
        $data['companies']          = $results['rows'];
        $data['matched_records']    = $results['matched_records'];     // $ of matched record (where)
        $data['total_records']      = $results['total_records'];       //
        
        $data['company_id']         = $company_id;                      // pass data to view, and to next controller
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['option']             = urlencode($option);
        
        $data['searchby_options']   = $this->m_crm->company_searchby_options();
        $data['contact_list']       = $this->m_crm->contact_list();
        $data['address_list']       = $this->m_crm->address_list();

        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("crm/company?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List Company"; break;
            case 'edit':    $data['title'] = "Edit Company"; break;
            case 'add':     $data['title'] = "Add Company";  break;
            case 'view':    $data['title'] = "View Company Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content'] = '/crm/company';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['tab'] = $this->generate_tab();
        $data['html_title'] = 'Company';
        $this->load->view('includes/template',$data);
        
        $this->session->set_flashdata('message',"");                //clear "displayed" message
    }
    
    function company_search() 
    {
        $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
        $active_only  = $this->input->post('active_only');
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
        redirect("crm/company?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");  
    }
    
    function company_edit()             // edit or add company
    {
        $action      = $this->input->post('action');
        $company_id  = $this->input->post('company_id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query_field = $this->input->post('query_field');
        $query_value = $this->input->post('query_value');
        
        $data = array(
            'Active'        => ($this->input->post('active'))?'1':'0',
            'CompanyCode'   => strtoupper($this->input->post('company_code')),
            'CompanyName'   => $this->input->post('company_name'),
            'CompanyName2'  => $this->input->post('company_name2'),
            'Website'       => $this->input->post('website'),
            'VendorID'      => $this->input->post('vendor_id'),
            'CustomerID'    => $this->input->post('customer_id'),
            'SubconID'      => $this->input->post('subcon_id'),
            'BillToContactID' => $this->input->post('bill_to_contact_id'),
            'BillToAddressID' => $this->input->post('bill_to_address_id'),
            'ShipToContactID' => $this->input->post('ship_to_contact_id'),
            'ShipToAddressID' => $this->input->post('ship_to_address_id'),
            'Note'          => $this->input->post('note')
        );
        
        if ($action=='edit') {
            $result = $this->m_crm->update_company($company_id, $data);
            $message = "Record Updated Sucessfully";
        } elseif ($action == 'add') {
            $company_id = $this->m_crm->add_company($data);
            switch ($company_id) {
                case -1: $message = "Duplicated Part Number, fail to add"; break;
                default: $message = "Record Added Sucessfully";
            }
        }
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode(array("ID"=>$company_id)));
        redirect("crm/company?action=view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option"); 
    }
    
    function company_download()
    {
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'Company';
        $sort_by = in_array($sort_by, ['ID', 'CompanyCode', 'CompanyName'])? $sort_by:'CompanyCode';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        if (sizeof($query) == 1 && array_key_exists('NULL', $query) && $option=="") {
            $filename = 'company_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'company_'.date("Y-m-d").'_filtered.xls';
        }
        
        // fetch data from database
        $results = $this->m_crm->company_query(100000, $sort_by, $sort_order, $query, 0, $option);
        $companies   = $results['rows'];

        $excel = new PHPExcel();

        /*$excel->getProperties()->setCreator("Leahkinn ERP System")
                ->setLastModifiedBy("Alex")
		->setTitle("title here")
		->setSubject("Subject here")
		->setDescription("comment here")
		->setKeywords("tags")
		->setCategory("Category"); */
        
        $excel->getProperties()->setCreator("Leahkinn ERP System");
        $excel->getActiveSheet()->setTitle('Company');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Active')
            ->setCellValue('C1', 'Code')
            ->setCellValue('D1', 'Company Name')
            ->setCellValue('E1', 'Company Name')
            ->setCellValue('F1', 'Web Site')
            ->setCellValue('G1', 'VendorID')
            ->setCellValue('H1', 'CustomerID')
            ->setCellValue('I1', 'SubconID')
            ->setCellValue('J1', 'BillToContact')
            ->setCellValue('K1', 'BillToShip')
            ->setCellValue('L1', 'ShipToContact')
            ->setCellValue('M1', 'ShipToShip')
            ->setCellValue('N1', 'Note');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('12');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('35');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth('40');
        $excel->getActiveSheet()->getStyle("A1:N1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:N1');

        $row = 2;
        foreach($companies as $company) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $company['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, ($company['Active'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $company['CompanyCode']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $company['CompanyName']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $company['CompanyName2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $company['Website']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $company['VendorID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $company['CustomerID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $company['SubconID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $company['BillToContact']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10,$row, $company['BillToAddress']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(11,$row, $company['ShipToContact']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(12,$row, $company['ShipToAddress']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(13,$row, $company['Note']);
            $row++;
        }
        
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');

        die('should never come here');       
    }
    
    
    function address()
    {     
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'ID';
        $sort_by = in_array($sort_by, ['ID', 'Address', 'City', 'Country', 'PostalCode'])? $sort_by:'ID';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;

        $data['cancel_url']   = "crm/address";
        $data['download_url'] = "crm/address_download";
        if ($action == 'edit' || $action == 'view')
        {            
            $results = $this->m_crm->address_query(1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            $address_id = intval($query['ID']);
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$address_id)));
            $data['clear_url'] = "crm/address?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $address_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'Address1'      => '',
                'Address2'      => '',
                'Address3'      => '',
                'City'          => '',
                'County'        => '',
                'Country'       => '',
                'PostalCode'    => '',
                'Note'          => ''
            ));
            $data['submit_button'] = "ADD";
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "crm/address?action=add";
        } else {                                // if Listing
            $address_id = 0;
        }
        
        $results = $this->m_crm->address_query($limit, $sort_by, $sort_order, $query, $offset, $option);
        
        $data['addresses']          = $results['rows'];
        $data['matched_records']    = $results['matched_records'];     // $ of matched record (where)
        $data['total_records']      = $results['total_records'];       //
        $data['address_id']         = $address_id;
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['option']             = urlencode($option);
        $data['searchby_options']   = $this->m_crm->address_searchby_options();
          
        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("crm/address?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List Address"; break;
            case 'edit':    $data['title'] = "Edit Address"; break;
            case 'add':     $data['title'] = "Add Address";  break;
            case 'view':    $data['title'] = "View Address Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content'] = '/crm/address';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['tab'] = $this->generate_tab();
        $data['html_title'] = 'Address';
        $this->load->view('includes/template',$data);
    }
    
    function address_search() 
    {
       $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
        $active_only  = $this->input->post('active_only');
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
        redirect("crm/address?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function address_edit()             // edit or add company
    {
        $action      = $this->input->post('action');
        $address_id  = $this->input->post('address_id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query_field = $this->input->post('query_field');
        $query_value = $this->input->post('query_value');
        $data = array(
            'Address1'      => $this->input->post('address1'),
            'Address2'      => $this->input->post('address2'),
            'Address3'      => $this->input->post('address3'),
            'City'          => $this->input->post('city'),
            'County'        => $this->input->post('county'),
            'Country'       => $this->input->post('country'),
            'PostalCode'    => $this->input->post('postal_code'),
            'Note'          => $this->input->post('note')
        );

        if ($action=='edit') {
            $result = $this->m_crm->update_address($address_id, $data);
            $message = "Record Updated Sucessfully";
        } elseif ($action == 'add') {
            $address_id = $this->m_crm->add_address($data);
            switch ($address_id) {
                case -1: $message = "Duplicated Part Number, fail to add"; break;
                default: $message = "Record Added Sucessfully";
            };
        }
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode(array("ID"=>$address_id)));
        redirect("crm/address?action=view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }

    function address_download()
    {
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'ID';
        $sort_by = in_array($sort_by, ['ID', 'Address', 'City', 'Country', 'PostalCode'])? $sort_by:'ID';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        if (sizeof($query) == 1 && array_key_exists('NULL', $query) && $option=="") {
            $filename = 'address_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'address_'.date("Y-m-d").'_filtered.xls';
        }
        
        // fetch data from database
        $results = $this->m_crm->address_query(100000, $sort_by, $sort_order, $query, 0, $option);
        $addresses   = $results['rows'];
        
        $excel = new PHPExcel();

        /*$excel->getProperties()->setCreator("Leahkinn ERP System")
                ->setLastModifiedBy("Alex")
		->setTitle("title here")
		->setSubject("Subject here")
		->setDescription("comment here")
		->setKeywords("tags")
		->setCategory("Category"); */
        
        $excel->getProperties()->setCreator("Leahkinn ERP System");
        $excel->getActiveSheet()->setTitle('Address');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Address 1')
            ->setCellValue('C1', 'Address 2')
            ->setCellValue('D1', 'Address 3')
            ->setCellValue('E1', 'City')
            ->setCellValue('F1', 'County')
            ->setCellValue('G1', 'Country')
            ->setCellValue('H1', 'Postal Code')
            ->setCellValue('I1', 'Note');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('45');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('45');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('45');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('40');
        $excel->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:G1');

        $row = 2;
        foreach($addresses as $address) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $address['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $address['Address1']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $address['Address2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $address['Address3']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $address['City']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $address['County']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $address['Country']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $address['PostalCode']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $address['Note']);
            $row++;
        }
        
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');

        die('should never come here');       
    }
    
    function user()
    {    
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'Nickname';
        $sort_by = in_array($sort_by, ['ID', 'Nickname', 'ChineseName', 'Phone', 'Mobile'])? $sort_by:'Nickname';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $data['cancel_url']   = "crm/user";
        $data['download_url'] = "crm/user_download";
        if ($action == 'edit' || $action == 'view')
        {
            $user_id = intval($query['ID']);
            $results = $this->m_crm->user_query(1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$user_id)));
            $data['clear_url'] = "crm/user?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $user_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'Email'             => '',
                'PasswordExpiryDate'=> date("Y-m-d" , strtotime('+1 month')),
                'Nickname'          => '',
                'ChineseName'       => '',
                'Active'            => '1',
                'AllowLogin'        => '',
                'CompanyID'         => '',
                'Phone'             => '',
                'Mobile'            => '',
                'Fax'               => '',
                'AddressID'         => '',
                'Setting'           => '',
                'Favorite'          => '',
                'Privilege'         => '',
                'ViewMatCost'       => '0',
                'ViewManCost'       => '0',
                'ViewFgCost'        => '0',
                'Note'              =>''
            ));
            $data['submit_button'] = "ADD";
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "crm/user?action=add";
        } else {                                // if Listing
            $user_id = 0;
        }
        
        $results = $this->m_crm->user_query($limit, $sort_by, $sort_order, $query, $offset, $option);
        $data['users']              = $results['rows'];
        $data['matched_records']    = $results['matched_records'];     // $ of matched record (where)
        $data['total_records']      = $results['total_records'];       //
        
        $data['user_id']            = $user_id;
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['option']             = urlencode($option);
        $data['searchby_options']   = $this->m_crm->user_searchby_options();
        $data['company_list']       = $this->m_crm->company_list();
        $data['address_list']       = $this->m_crm->address_list();

        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("crm/user?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List User"; break;
            case 'edit':    $data['title'] = "Edit User"; break;
            case 'add':     $data['title'] = "Add User";  break;
            case 'view':    $data['title'] = "View User Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content'] = '/crm/user';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['tab'] = $this->generate_tab();
        $data['html_title'] = 'User';
        $this->load->view('includes/template',$data);
    }
    
    function user_search() 
    {
        $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
        $active_only  = $this->input->post('active_only');
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
        redirect("crm/user?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function user_edit()             // edit or add company
    {
        $action      = $this->input->post('action');
        $user_id     = $this->input->post('user_id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query_field = $this->input->post('query_field');
        $query_value = $this->input->post('query_value');
       
        if ($this->input->post('password') != $this->input->post('password2'))
        {
            $message = "ABORT: Password NOT matched";
            $this->session->set_flashdata('message',$message);
            redirect("crm/user?action=list?sb=$sort_by?so=$sort_order?qf=ID?qv=$user_id"); 
            
        } elseif ($this->input->post('password') == '') {           // if password = blank
            $data = array();                                        // don't update       
        } else {
            $data = array(                                          // populate password
                'Password'              => md5($this->input->post('password')),
                'PasswordExpiryDate'    => $this->input->post('password_expiry_date') );
        }
            

        $data = array_merge($data, array(
            'Email'                 => $this->input->post('email'),
            'Nickname'              => $this->input->post('nickname'),
            'ChineseName'           => $this->input->post('chinese_name'),
            'Active'                => ($this->input->post('active'))?'1':'0',
            'AllowLogin'            => ($this->input->post('allow_login'))?'1':'0',
            'CompanyID'             => $this->input->post('company_id'),
            'Phone'                 => $this->input->post('phone'),
            'Mobile'                => $this->input->post('mobile'),
            'Fax'                   => $this->input->post('fax'),
            'AddressID'             => $this->input->post('address_id'),
            'Setting'               => $this->input->post('setting'),
            'Favorite'              => $this->input->post('favorite'),
            'Privilege'             => $this->input->post('privilege'),
            'ViewMatCost'           => ($this->input->post('view_mat_cost'))?'1':'0',
            'ViewManCost'           => ($this->input->post('view_man_cost'))?'1':'0',
            'ViewFgCost'            => ($this->input->post('view_fg_cost'))?'1':'0',
            'Note'                  => $this->input->post('note')
        ));
                
        if ($action=='edit') {
            $result = $this->m_crm->update_user($user_id, $data);
            $message = "Record Updated Sucessfully";
            $this->session->set_flashdata('message',$message);
        } elseif ($action == 'add') {
            $user_id = $this->m_crm->add_user($data);
            switch ($user_id) {
                case -1: $message = "Duplicated Part Number, fail to add"; break;
                default: $message = "Record Added Sucessfully";
            }
        }
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode(array("ID"=>$user_id)));
        redirect("crm/user?action=view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function user_download()
    {
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'Nickname';
        $sort_by = in_array($sort_by, ['ID', 'Nickname', 'ChineseName', 'Phone', 'Mobile'])? $sort_by:'Nickname';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        if (sizeof($query) == 1 && array_key_exists('NULL', $query) && $option=="") {
            $filename = 'user_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'user_'.date("Y-m-d").'_filtered.xls';
        }
        
        // fetch data from database
        $results = $this->m_crm->user_query(100000, $sort_by, $sort_order, $query, 0, $option);
        $users   = $results['rows'];

        $excel = new PHPExcel();

        /*$excel->getProperties()->setCreator("Leahkinn ERP System")
                ->setLastModifiedBy("Alex")
		->setTitle("title here")
		->setSubject("Subject here")
		->setDescription("comment here")
		->setKeywords("tags")
		->setCategory("Category"); */
        
        $excel->getProperties()->setCreator("Leahkinn ERP System");
        $excel->getActiveSheet()->setTitle('User');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Active')
            ->setCellValue('C1', 'Log-In')
            ->setCellValue('D1', 'Name')
            ->setCellValue('E1', '中文名')
            ->setCellValue('F1', 'Email')
            ->setCellValue('G1', 'Company Code')
            ->setCellValue('H1', 'Company Name')
            ->setCellValue('I1', 'Company Name')
            ->setCellValue('J1', 'Phone')
            ->setCellValue('K1', 'Mobile')
            ->setCellValue('L1', 'Fax')
            ->setCellValue('M1', 'Note');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth('40');
        $excel->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:G1');

        $row = 2;
        foreach($users as $user) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $user['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, ($user['Active'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, ($user['AllowLogin'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $user['Nickname']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $user['ChineseName']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $user['Email']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $user['CompanyCode']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $user['CompanyName']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $user['CompanyName2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $user['Phone']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $user['Mobile']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $user['Fax']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $user['Note']);
            $row++;
        }
        
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');

        die('should never come here');       
    }
    
}    

?>
