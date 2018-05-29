<?php

class Backlog extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        
        // redirect if do not have privilege
        $required_priv = array('BLG');
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->model('m_backlog');
        $this->load->model('m_product');
        $this->load->model('m_crm');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        }
      
    function index()
    { 
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'RequestDate';
        $sort_by = in_array($sort_by, ['BacklogID', 'PartNumber', 'CompanyCode', 'CustomerPO', 'Item', 'Status', 'OrderDate', 'RequestDate'])? $sort_by:'RequestDate';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $data['cancel_url']   = "backlog";
        $data['download_url'] = "backlog/download";
        if ($action == 'edit' || $action == 'view')
        {  
            $backlog_id = $query['ID'];
            $results = $this->m_backlog->get_single_backlog($backlog_id);
            $data['backlog']        = $results['backlog']['0'];          // get original data
            $data['backlog_items']  = $results['items'];
           
// 2016-02-15; Temp work-around; forcing at least 5 items (if there is only one record, appending 5 empty record, IN CASE WE WANT TO ADD NEW ITEM)
            $tmp = array(
                'Item'              => '',
                'ProductID'         => '',
                'BookingPrice'      => '',
                'RequestDate'       =>  date("Y-m-d", strtotime("+1 month")),
                'OrderedQty'        => '0',
                'ShippedQty'        => '0',
                'Record'            => '',
                'Status'            => 'open',
                'Note'              => ''
            ); 
            if ($action == 'edit') {
                for ($i=sizeof($data['backlog_items']); $i<5; $i++) {
                    $data['backlog_items'] = array_merge($data['backlog_items'], array($tmp)); 
                    $data['backlog_items'][$i]['Item'] = $i+1;
                }
            }

// END 2016-02-15; Temp work-around; forcing at least 5 items
// 
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$backlog_id)));
            $data['clear_url'] = "backlog?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $backlog_id = 0;
            $data['backlog'] = array (     // empty data for "NEW" record  
                'Submitter'         => $this->session->userdata['user_id'],
                'Revision'          => '1',
                'Entity'            => 'LK-HK',
                'CompanyID'         => $this->session->userdata['company_id'],
                'ShipToContactID'   => '',
                'CustomerPO'        => '',
                'OrderDate'         => date("Y-m-d"),
                'DocPath'           => '',
                'Note'         => ''
            );
            
            $tmp = array(
                'Item'              => '',
                'ProductID'         => '',
                'BookingPrice'      => '',
                'RequestDate'       =>  date("Y-m-d", strtotime("+1 month")),
                'OrderedQty'        => '0',
                'ShippedQty'        => '0',
                'Record'            => '',
                'Status'            => 'open',
                'Note'              => ''
            );
            
            $data['backlog_items'][0] = $tmp;               // 1st item
            $data['backlog_items'][1] = $tmp;               // 2nd item
            $data['backlog_items'][2] = $tmp;               // 3rd item
            $data['backlog_items'][3] = $tmp;               // 4th item
            $data['backlog_items'][4] = $tmp;               // 5th item            
            $data['backlog_items'][0]['Item'] = '1';
            $data['backlog_items'][1]['Item'] = '2';
            $data['backlog_items'][2]['Item'] = '3';
            $data['backlog_items'][3]['Item'] = '4';
            $data['backlog_items'][4]['Item'] = '5';
            
            $data['submit_button'] = "SUBMIT";
//            $data['add_item_button'] = "ADD";
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "backlog?action=add";
            $data['cancel_url'] = "backlog?action=list";
        } else {                                // if Listing
            $backlog_id = 0;
        }
        
        $results = $this->m_backlog->query($limit, $sort_by, $sort_order, $query, $offset, $option);
        
        $data['items']              = $results['rows'];
        $data['matched_records']    = $results['matched_records'];     // $ of matched record (where)
        $data['total_records']      = $results['total_records'];       //

        $data['backlog_id']         = $backlog_id;
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['option']             = urlencode($option);
        
        $data['searchby_options']   = $this->m_backlog->searchby_options();
      
        $data['product_list']       = $this->m_product->product_list();
        $data['company_list']       = $this->m_crm->company_list();
        $data['entity_list']        = $this->m_crm->entity_list();
        $data['status_list']        = $this->m_backlog->status_list();
          
        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("backlog?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List Backlog"; break;
            case 'edit':    $data['title'] = "Edit Backlog"; break;
            case 'add':     $data['title'] = "Add Backlog";  break;
            case 'view':    $data['title'] = "View Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content'] = '/backlog';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['html_title'] = 'Backlog';
        $this->load->view('includes/template',$data);
    }
    
    function search()               
    {
        $limit          = $this->input->post('limit');
        $sort_by        = $this->input->post('sort_by');
        $sort_order     = $this->input->post('sort_order');
        $hide_cancel    = $this->input->post('hide_cancel');
        $hide_completed = $this->input->post('hide_completed');
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
        if ($hide_cancel ==1)    {$option = $option."hidecancel "; }
        if ($hide_completed ==1) {$option = $option."hidecompleted ";}
        
        $message = "search completed";
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode($query));
        redirect("backlog?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
 
    function edit()             // top-level Part Number edit
    {   
        $action      = $this->input->post('action');
        $backlog_id  = $this->input->post('backlog_id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');
        
        $backlog = array(
            'Submitter'         => $this->input->post('submitter'),
        //    'Revision'          => $this->input->post('revision'),
            'Entity'            => $this->input->post('entity'),
            'CompanyID'         => $this->input->post('company_id'),
            'ShipToContactID'   => $this->input->post('ship_to_contact_id'),
            'CustomerPO'        => $this->input->post('customer_po'),
            'OrderDate'         => $this->input->post('order_date'),
            'Note'         => $this->input->post('order_note'),
        );
        
        $items = array(array());
        for ($i=0; $i<5; $i++) {
            if ($this->input->post("product_id[$i]")== "NULL") { break; }
            $items[$i]['Item']          = ($i+1);
            $items[$i]['BacklogID']     = $backlog_id;
            $items[$i]['ProductID']     = $this->input->post("product_id[$i]");
            $items[$i]['PartNumber']    = $this->m_product->get_part_number($items[$i]['ProductID']);
            var_dump($items[$i]['PartNumber']);
            halt; 
            $items[$i]['CompanyID']     = $this->input->post("company_id");
            $items[$i]['BookingPrice']  = $this->input->post("booking_price[$i]");
            $items[$i]['RequestDate']   = $this->input->post("request_date[$i]");
            $items[$i]['OrderedQty']    = $this->input->post("ordered_qty[$i]");
            $items[$i]['ShippedQty']    = $this->input->post("shipped_qty[$i]");
            $items[$i]['Record']        = $this->input->post("record[$i]");
            $items[$i]['Status']        = $this->input->post("status[$i]");
            $items[$i]['Note']          = $this->input->post("item_note[$i]");
        }
        
        if ($action=='edit') {
            $result = $this->m_backlog->update_record($backlog_id, $backlog, $items);
            $message = "Record Updated Sucessfully";
            $this->session->set_flashdata('message',$message);
        } elseif ($action == 'add') {
            $backlog_id = $this->m_backlog->add_record($backlog, $items);
            switch ($backlog_id) {
                case -1: $message = "Something is Wrong (backlog-edit/add)"; break;
                default: $message = "Record Added Sucessfully";
            }
            $this->session->set_flashdata('message',$message);
        }
        $query_url = urlencode(json_encode(array("ID"=>$backlog_id)));
        redirect("backlog?action=view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
 
    function download()
    {
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['BacklogID', 'PartNumber', 'CompanyCode', 'CustomerPO', 'Item', 'Status', 'OrderDate', 'RequestDate'])? $sort_by:'RequestDate';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        if (sizeof($query) == 1 && array_key_exists('NULL', $query) && $option=="") {
            $filename = 'backlog_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'backlog_'.date("Y-m-d").'_filtered.xls';
        }
        
        // fetch data from database
        $results = $this->m_backlog->query(100000, $sort_by, $sort_order, $query, 0, $option);
        $items   = $results['rows'];
        
        $excel = new PHPExcel();

        /*$excel->getProperties()->setCreator("Leahkinn ERP System")
                ->setLastModifiedBy("Alex")
		->setTitle("title here")
		->setSubject("Subject here")
		->setDescription("comment here")
		->setKeywords("tags")
		->setCategory("Category"); */
        
        $excel->getProperties()->setCreator("Leahkinn ERP System");
        $excel->getActiveSheet()->setTitle('Backlog');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'BacklogID')
            ->setCellValue('B1', 'ID')
            ->setCellValue('C1', 'Entity')
            ->setCellValue('D1', 'Code')
            ->setCellValue('E1', 'Order Date')
            ->setCellValue('F1', 'Customer PO')
            ->setCellValue('G1', 'Item')
            ->setCellValue('H1', 'Part Number')
            ->setCellValue('I1', 'Request Date')
            ->setCellValue('J1', 'Booking Price')
            ->setCellValue('K1', 'Ordered Qty')
            ->setCellValue('L1', 'Shipped Qty')
            ->setCellValue('M1', 'Outstanding Qty')
            ->setCellValue('N1', 'Status')
            ->setCellValue('O1', 'Order Note')
            ->setCellValue('P1', 'Item Note')
            ->setCellValue('Q1', 'Record');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('7');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('12');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('12');
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth('30');
        $excel->getActiveSheet()->getStyle("A1:Q1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:N1');

        $row = 2;
        foreach($items as $item) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $item['BacklogID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $item['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $item['Entity']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $item['CompanyCode']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $item['OrderDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $item['CustomerPO']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $item['Item']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $item['PartNumber']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $item['RequestDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $item['BookingPrice']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $item['OrderedQty']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $item['ShippedQty']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, ($item['OrderedQty']-$item['ShippedQty']));
            $excel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $item['Status']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $item['OrderNote']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $item['ItemNote']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $item['Record']);
            $row++;
        }
        $excel->getActiveSheet()->getStyle('K2:M100')->getNumberFormat()->setFormatCode('#,###');
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');

        die('should never come here');       
    }
    
}

?>