<?php

class Product_Ipn extends CI_Controller {
    
    //public $sort_by = 'PartNumber';
    function __construct()
    {
        parent::__construct();   
        date_default_timezone_set("Asia/Taipei");

        // redirect if do not have privilege
        $required_priv = array('IPN','IPN'); 
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->model('m_product_ipn');
        $this->load->model('m_wafer');
        $this->load->model('m_product');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
    }
    
    function index()
    {
        $action = (isset($_GET['action'])) ? $_GET['action']:'all';
        $action = in_array($action, ['all','list','view','edit','add'])? $action:'all';
       
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
        
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['ID', 'IPN', 'SubCon', 'Description', 'PackageSize', 'SubstrateID', 'BondingDiagram'])? $sort_by:'IPN';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $product_id = (isset($_GET['pid'])) ? intval($_GET['pid']):0;
        $part_number = "";
        $ipn_id = (isset($_GET['ipn'])) ? intval($_GET['ipn']):0;
        
        if ($action == 'all' && strpos($option, 'listall')== FALSE )
        { $option = $option."listall"; }
        
        if ($action == 'list' && strpos($option, 'listall') !== FALSE)
        { $action = 'all'; }
        
        $data['download_url'] = "product_ipn/download";
        if ($action == 'edit' || $action == 'view')
        {
            $results = $this->m_product_ipn->ipn_search($action, $product_id, $ipn_id, 0, "", "", "", "", "");
            $data['edit']  = $results['ipn'];          // get original data
            
            // pass current URL, for top/edit CLEAR button
            $newoption = urlencode($option);
            $data['clear_url']     = "product_ipn?action=edit&pid=$product_id&ipn=$ipn_id&option=$newoption";
            $data['cancel_url']    = "product_ipn?action=list&pid=$product_id&option=$newoption";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'Active'            => '1',
                'IPN'               => '',
                'PartNumnber'       => '',
                'PackageSize'       => '',           
                'Description'       => '',
                'SubCon'            => '',
                'SubstrateID'       => '',
                'BondingDiagram'    => '',
                'LeadTime'          => '',
                'DiePartNumberID_1' => '',
                'DiePartNumber_1'   => '',      /// retiring
                'DieGrind_1'        => '',
                'DiePartNumberID_2' => '',
                'DiePartNumber_2'   => '',
                'DieGrind_2'        => '',
                'DiePartNumberID_3' => '',
                'DiePartNumber_3'   => '',
                'DieGrind_3'        => '',
                'DiePartNumberID_4' => '',
                'DiePartNumber_4'   => '',
                'DieGrind_4'        => '',
                'DiePartNumberID_5' => '',
                'DiePartNumber_5'   => '',
                'DieGrind_5'        => '',
                'PurchasedItemID'   => '',
                'PackageQualDate'   => '',
                'PreCondDate'       => '',
                'EngineeringNote'   => '',
                'Note'              => ''
            ));
  
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "product_ipn?action=add&pid=$product_id";
            $data['cancel_url'] = "product_ipn?action=list&pid=$product_id";
            $data['submit_button'] = "ADD";
        } else {                                // if Listing
            $data['cancel_url']    = "product_ipn";
            $ipn_id = 0;
        }
        
        if ($action == 'all') {
            $results = $this->m_product_ipn->ipn_search('all', 0 , 0, $limit, $sort_by, $sort_order, $query, $offset, $option);
        } else {
            $results = $this->m_product_ipn->ipn_search('list', $product_id, 0, 0, "", "", "", "", "");
        }
        
        $data['product']            = $results['product'];
        $data['ipns']               = $results['ipn'];
        $data['matched_records']    = $results['matched_records'];      // $ of matched record (where)
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
        $data['searchby_options']   = $this->m_product_ipn->searchby_options();
        $data['wafer_list']         = $this->m_wafer->wafer_partnumber_list();
        $data['purchased_item_list'] = $this->m_product->product_list("purchased");

        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("product_ipn?action=$action&pid=$product_id&ipn=$ipn_id&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List IPN"; break;
            case 'edit':    $data['title'] = "Edit IPN"; break;
            case 'add':     $data['title'] = "Add IPN"; break;
            case 'view':    $data['title'] = "View Detail"; break;
            case 'all':     $data['title'] = "List All IPN"; break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        // for top_edit() for identifying "edit" or "add"
        $data['product_id'] = $product_id;
        $data['part_number'] = $part_number;
        $data['ipn_id']     = $ipn_id;
        $data['action']     = $action;
        $data['limit']      = $limit;

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content'] = '/product/product_ipn';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['html_title'] = 'IPN';
        $this->load->view('includes/template',$data);
    }

    function search()               // top-level Part Number search
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
        redirect("product_ipn?action=all&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function edit()             // top-level Part Number edit
    {   
        $action     = $this->input->post('action');
        $product_id = $this->input->post('product_id');
        $part_number= $this->input->post('part_number');
        $ipn_id     = $this->input->post('ipn_id');
        $option     = $this->input->post('option');

        $data = array(
            'ProductID'         => $product_id,
            'PartNumber'        => $product_number,
            'Active'            => ($this->input->post('active'))?'1':'0',
            'IPN'               => $this->input->post('ipn'),
            'PackageSize'       => $this->input->post('package_size'),
            'Description'       => $this->input->post('description'),
            'SubCon'            => $this->input->post('sub_con'),
            'SubstrateID'       => $this->input->post('substrate_id'),
            'BondingDiagram'    => $this->input->post('bonding_diagram'),
            'LeadTime'          => $this->input->post('lead_time'),
            'DiePartNumberID_1' => $this->input->post('die_part_number_id_1'),
            'DieGrind_1'        => $this->input->post('die_grind_1'),
            'DiePartNumberID_2' => $this->input->post('die_part_number_id_2'),
            'DieGrind_2'        => $this->input->post('die_grind_2'),
            'DiePartNumberID_3' => $this->input->post('die_part_number_id_3'),
            'DieGrind_3'        => $this->input->post('die_grind_3'),
            'DiePartNumberID_4' => $this->input->post('die_part_number_id_4'),
            'DieGrind_4'        => $this->input->post('die_grind_4'),
            'DiePartNumberID_5' => $this->input->post('die_part_number_id_5'),
            'DieGrind_5'        => $this->input->post('die_grind_5'),
            'PurchasedItemID'   => $this->input->post('purchased_item_id'),
            'PackageQualDate'   => $this->input->post('package_qual_date'),
            'PreCondDate'       => $this->input->post('pre_cond_date'),
            'EngineeringNote'   => $this->input->post('engineering_note'),
            'Note'              => $this->input->post('note')
        );
        
        if ($action=='edit') {
            $this->m_product_ipn->update_record($ipn_id, $data);
            $message = "Record Updated Sucessfully";
        } elseif ($action == 'add') {
            $ipn_id = $this->m_product_ipn->add_record($data);
            switch ($ipn_id) {
                case -1: $message = "Duplicated IPN, fail to add"; break;
                default: $message = "Record Added Sucessfully"; break;
            }
        }
        $this->session->set_flashdata('message',$message);
        redirect("product_ipn?action=view&pid=$product_id&ipn=$ipn_id&option=$option");
    }
    
    function download()
    {
        $filename = 'ipn_'.date("Y-m-d").'_all(confidential-A).xls';
        // if priv allow, Confidential-B

        // fetch data from database
        $results = $this->m_product_ipn->ipn_get_all();
        $ipns  = $results['ipn'];

        $excel = new PHPExcel();

        /*$excel->getProperties()->setCreator("Leahkinn ERP System")
                ->setLastModifiedBy("Alex")
		->setTitle("title here")
		->setSubject("Subject here")
		->setDescription("comment here")
		->setKeywords("tags")
		->setCategory("Category"); */

        $excel->getProperties()->setCreator("Leahkinn ERP System");
        $excel->getActiveSheet()->setTitle('IPN');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'IPN-ID')
            ->setCellValue('C1', 'P/N Active')
            ->setCellValue('D1', 'IPN Active')
            ->setCellValue('E1', 'PartNumber')
            ->setCellValue('F1', 'Description 1')
            ->setCellValue('G1', 'Description 2')
            ->setCellValue('H1', 'IPN')
            ->setCellValue('I1', 'Subcon')       
            ->setCellValue('J1', 'IPN Description')    
            ->setCellValue('K1', 'Ball Type')
            ->setCellValue('L1', 'Package (IPN)')
            ->setCellValue('M1', 'SubstrateID')
            ->setCellValue('N1', 'Bonding Diagram')
            ->setCellValue('O1', 'Lead-Time')
            ->setCellValue('P1', 'Die 1 P/N')
            ->setCellValue('Q1', 'Die 1 Description')
            ->setCellValue('R1', 'Die 1 Grind')
            ->setCellValue('S1', 'Die 2 P/N')
            ->setCellValue('T1', 'Die 2 Description')
            ->setCellValue('U1', 'Die 2 Grind')
            ->setCellValue('V1', 'Die 3 P/N')
            ->setCellValue('W1', 'Die 3 Description')
            ->setCellValue('X1', 'Die 3 Grind')
            ->setCellValue('Y1', 'Die 4 P/N')
            ->setCellValue('Z1', 'Die 4 Description')
            ->setCellValue('AA1', 'Die 4 Grind')
            ->setCellValue('AB1', 'Die 5 P/N')
            ->setCellValue('AC1', 'Die 5 Description')
            ->setCellValue('AD1', 'Die 5 Grind')
            ->setCellValue('AE1', 'Purchased P/N')
            ->setCellValue('AF1', 'Purchased Description')
            ->setCellValue('AG1',  'Package Qual Date')
            ->setCellValue('AH1', 'PreCond Date')
            ->setCellValue('AI1', 'Note (IPN)')
            ->setCellValue('AJ1', 'Engineering Note (IPN)');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('12');
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('R')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('S')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('T')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('U')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('V')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('W')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('X')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('Y')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('Z')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('AA')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('AB')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('AC')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('AD')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('AE')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('AF')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('AG')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('AH')->setWidth('15');        
        $excel->getActiveSheet()->getColumnDimension('AI')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('AJ')->setWidth('40');
        
        $excel->getActiveSheet()->getColumnDimension('A')->setVisible(false);
        $excel->getActiveSheet()->getColumnDimension('B')->setVisible(false);
        $excel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
        $excel->getActiveSheet()->getColumnDimension('F')->setVisible(false);
        $excel->getActiveSheet()->getColumnDimension('G')->setVisible(false);
        $excel->getActiveSheet()->getColumnDimension('J')->setVisible(false);
        $excel->getActiveSheet()->getStyle("A1:AJ1")->getFont()->setBold(true); 
        $excel->getActiveSheet()->setAutoFilter('A1:AI1');

        $row = 2;
        foreach($ipns as $ipn) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $ipn['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $ipn['IpnID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, ($ipn['Active'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, ($ipn['IpnActive'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $ipn['PartNumber']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $ipn['Description']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $ipn['Description2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $ipn['IPN']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $ipn['SubCon']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $ipn['IpnDescription']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $ipn['BallType']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $ipn['IpnPackageSize']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $ipn['SubstrateID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $ipn['BondingDiagram']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $ipn['LeadTime']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $ipn['DiePN_1']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $ipn['DieDesc_1']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $ipn['DieGrind_1']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $ipn['DiePN_2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $ipn['DieDesc_2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $ipn['DieGrind_2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $ipn['DiePN_3']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $ipn['DieDesc_3']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $ipn['DieGrind_3']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $ipn['DiePN_4']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $ipn['DieDesc_4']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $ipn['DieGrind_4']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $ipn['DiePN_5']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $ipn['DieDesc_5']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $ipn['DieGrind_5']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $ipn['PurchasedItemPN']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(31, $row, $ipn['PurchasedItemDesc']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(32, $row, $ipn['PackageQualDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(33, $row, $ipn['PreCondDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(34, $row, $ipn['IpnNote']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(35, $row, $ipn['EngineeringNote']);
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