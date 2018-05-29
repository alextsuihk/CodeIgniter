<?php

class Product extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        
        // redirect if do not have privilege
        $required_priv = array('PRO','IPN');
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->model('m_product');
    }

    function index()
    {
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['ID', 'PartNumber', 'Description', 'BallType', 'PackageSize'])? $sort_by:'PartNumber';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $data['cancel_url']   = "product";
        $data['download_url'] = "product/download";
        
        if ($action == 'edit' || $action == 'view')
        {
            $product_id = intval($query['ID']);
            $results = $this->m_product->query(1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$product_id)));
            $data['clear_url'] = "product?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $product_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'PartNumber'    => '',
                'Active'        => '1',
                'Description'   => '',
                'Description2'  => '',
                'BallType'      => '',
                'PackageSize'   => '',
                'Datasheet'     => '',
                'Weight'        => '',
                'AllowPurchase' => '0',
                'AllowSell'     => '0',
                'AllowSample'   => '0',
                'Note'          => ''
            ));
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "product?action=add";
            $data['submit_button'] = "ADD";
        } else {                                // if Listing
            $product_id = 0;
        }
        
        $results = $this->m_product->query($limit, $sort_by, $sort_order, $query, $offset, $option);
        
        $data['products']           = $results['rows'];
        $data['matched_records']    = $results['matched_records'];      // $ of matched record (where)
        $data['total_records']      = $results['total_records'];        //
        $data['product_id']         = $product_id;                      // pass data to view, and to next controller
        $data['action']             = $action;
        $data['limit']              = $limit;
        $data['sort_by']            = $sort_by;
        $data['sort_order']         = $sort_order;
        $data['offset']             = $offset;
        $data['query']              = $query;
        $query_url                  = urlencode(json_encode($query));
        $data['query_url']          = $query_url;
        $data['option']             = urlencode($option);
        
        $data['searchby_options']   = $this->m_product->searchby_options();
          
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
            case 'list':    $data['title'] = "List Part Number"; break;
            case 'edit':    $data['title'] = "Edit Part Number"; break;
            case 'add':     $data['title'] = "Add Part Number";  break;
            case 'view':    $data['title'] = "View Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
 
        $data['message']        = $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content']   = '/product/product';
        $data['favorite']       = $this->session->userdata('favorite');
        $data['html_title']     = 'Product';
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
        var_dump($submit);
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
        redirect("product?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
 
    function edit()             // top-level Part Number edit
    {
        $action      = $this->input->post('action');
        $product_id  = $this->input->post('product_id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');
        
        $data = array(
            'PartNumber'    => $this->input->post('part_number'),
            'Active'        => ($this->input->post('active'))?'1':'0',
            'Description'   => $this->input->post('description'),
            'Description2'  => $this->input->post('description2'),
            'BallType'      => $this->input->post('ball_type'),
            'PackageSize'   => $this->input->post('package_size'),
            'Datasheet'     => $this->input->post('datasheet'),
            'Weight'        => $this->input->post('weight'),
            'AllowPurchase' => ($this->input->post('allow_purchase'))?'1':'0',
            'AllowSell'     => ($this->input->post('allow_sell'))?'1':'0',
            'AllowSample'   => ($this->input->post('allow_sample'))?'1':'0',
            'Note'          => $this->input->post('note')
        );

        if ($action=='edit') {
            $result     = $this->m_product->update_record($product_id, $data);
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
 
    function download()
    {
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['ID', 'PartNumber', 'Description', 'BallType', 'PackageSize'])? $sort_by:'PartNumber';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        if (sizeof($query) == 1 && array_key_exists('NULL', $query) && $option=="") {
            $filename = 'product_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'product_'.date("Y-m-d").'_filtered.xls';
        }
        
        // fetch data from database
        $results     = $this->m_product->query(100000, $sort_by, $sort_order, $query, 0, $option);
        $products   = $results['rows'];

        $excel = new PHPExcel();

        /*$excel->getProperties()->setCreator("Leahkinn ERP System")
                ->setLastModifiedBy("Alex")
		->setTitle("title here")
		->setSubject("Subject here")
		->setDescription("comment here")
		->setKeywords("tags")
		->setCategory("Category"); */
        
        $excel->getProperties()->setCreator("Leahkinn ERP System");
        $excel->getActiveSheet()->setTitle('Part Number');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Active')
            ->setCellValue('C1', 'PartNumber')
            ->setCellValue('D1', 'Description 1')
            ->setCellValue('E1', 'Description 2')
            ->setCellValue('F1', 'Ball Type')
            ->setCellValue('G1', 'Package Size')
            ->setCellValue('H1', 'Datasheet')
            ->setCellValue('I1', 'Weight (mg)')
            ->setCellValue('J1', 'Purchase')
            ->setCellValue('K1', 'Sell')
            ->setCellValue('L1', 'Note');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('45');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('35');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('30');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth('40');
        $excel->getActiveSheet()->getStyle("A1:M1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:L1');

        $row = 2;
        foreach($products as $product) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0,  $row, $product['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1,  $row, ($product['Active'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2,  $row, $product['PartNumber']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3,  $row, $product['Description']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4,  $row, $product['Description2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5,  $row, $product['BallType']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6,  $row, $product['PackageSize']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7,  $row, $product['Datasheet']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8,  $row, $product['Weight']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9,  $row, ($product['AllowPurchase'])?"Allow":"Disallow");
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, ($product['AllowSell'])?"Allow":"Disallow");
            $excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, ($product['AllowSample'])?"Allow":"Disallow");
            $excel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $product['Note']);
            $row++;
        }
        
//        $excel->getActiveSheet()->getStyle('K2:M100')->getNumberFormat()->setFormatCode('#,###');
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');   
    }
    
}

?>