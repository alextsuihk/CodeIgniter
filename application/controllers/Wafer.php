<?php

class Wafer extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        
        // redirect if do not have privilege
        $required_priv = array('WAF');
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->model('m_wafer');
    }
    
    function generate_reference() 
    {
        $key = array();
        for ($i=0; $i<10; $i++) {
            $key[] = $i;  
        }
            
        for ($i=65; $i<(65+26); $i++) {
            $key[] = chr($i);
        }
        for ($i=97; $i<(97+26); $i++) {
            $key[] = chr($i);
        }

        $dt[] = intval(date("Y")-2016+10);
        $dt[] = intval(date("n"));      // month
        $dt[] = intval(date("j"));      // day
        $dt[] = intval(date("G"));      // hour 0-23
        $dt[] = intval(date("i"));      // minutes
        $dt[] = intval(date("s"));      // seconds
        $reference = $key[$dt[0]].$key[$dt[1]].$key[$dt[2]].$key[$dt[4]].$key[$dt[5]].$key[$this->session->userdata('user_id')];
        
        return($reference);
    }
    
    function index()
    {
//       $reference = $this->generate_reference();
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['ID', 'PartNumber', 'Vendor', 'Design', 'Description'])? $sort_by:'PartNumber';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));

        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");

        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $data['cancel_url']   = "wafer";
        $data['download_url'] = "wafer/download";
        if ($action == 'edit' || $action == 'view')
        {
            $product_id = intval($query['ID']);
            $results = $this->m_wafer->query(1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$product_id)));
            $data['clear_url'] = "wafer?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $product_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'VendorID'      => '',
                'Active'        => '1',
                'DesignID'      => '',
                'PartNumber'    => '',
                'Description'   => '',
                'DescriptionInternal'   => '',
                'Note'          => ''
            ));
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "wafer?action=add";
            $data['cancel_url'] = "wafer";
            $data['submit_button'] = "ADD";
        } else {                                // if Listing
            $product_id = 0;
        }
        
        $results = $this->m_wafer->query($limit, $sort_by, $sort_order, $query, $offset, $option);
        
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
        
        $data['searchby_options']   = $this->m_wafer->searchby_options();
        $data['vendor_list']        = $this->m_wafer->wafer_vendor_list();
        $data['design_list']        = $this->m_wafer->wafer_design_list();
          
        //pagination
        $config                     = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']         = site_url("wafer?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List Wafer Part Number"; break;
            case 'edit':    $data['title'] = "Edit WaferPart Number"; break;
            case 'add':     $data['title'] = "Add Part Number";  break;
            case 'view':    $data['title'] = "View Wafer Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
 
        $data['message']        = $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content']   = '/wafer/wafer';
        $data['favorite']       = $this->session->userdata('favorite');
        $data['html_title']     = 'Wafer PartNumber';
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
        redirect("wafer?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            'VendorID'      => $this->input->post('vendor_id'),
            'Active'        => ($this->input->post('active'))?'1':'0',
            'DesignID'      => $this->input->post('design_id'),
            'PartNumber'    => $this->input->post('part_number'),
            'Description'   => $this->input->post('description'),
            'DescriptionInternal'   => $this->input->post('description_internal'),
            'Note'          => $this->input->post('note')
        );

        if ($action=='edit') {
            $result     = $this->m_wafer->update_record($product_id, $data);
            $message    = "Record Updated Sucessfully";
            $this->session->set_flashdata('message',$message);
        } elseif ($action == 'add') {
            $product_id = $this->m_wafer->add_record($data);
            switch ($product_id) {
                case -1:    $message = "Duplicated Part Number, fail to add"; break;
                default:    $message = "Record Added Sucessfully";
            }
            $this->session->set_flashdata('message',$message);
        }

        $query_url = urlencode(json_encode(array("ID"=>$product_id)));
        redirect("wafer?action=view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
 
    function download()
    {
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['ID', 'PartNumber', 'Description', 'BallType', 'PackageSize'])? $sort_by:'PartNumber';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query      = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        
        if (sizeof($query) == 1 && array_key_exists('NULL', $query)) {
            $filename = 'waferPN_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'waferPN_'.date("Y-m-d").'_filtered.xls';
        }
        
        // fetch data from database
        $results     = $this->m_wafer->query(100000, $sort_by, $sort_order, $query, 0 ,$option);
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
        $excel->getActiveSheet()->setTitle('Wafer PN');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Active')
            ->setCellValue('C1', 'Vendor')
            ->setCellValue('D1', 'Design')
            ->setCellValue('E1', 'PartNumber')
            ->setCellValue('F1', 'Description Design')
            ->setCellValue('G1', 'Description')
            ->setCellValue('H1', 'Description (Internal)')
            ->setCellValue('I1', 'Note');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('35');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('40');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('40');
        $excel->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:H1');

        $row = 2;
        foreach($products as $product) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $product['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, ($product['Active'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $product['Vendor']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $product['Design']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $product['PartNumber']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $product['DescriptionDesign']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $product['Description']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $product['DescriptionInternal']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $product['Note']);
            $row++;
        }
        
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');

        die('should never come here');       
    }

    function lot()
    {
//       $reference = $this->generate_reference();
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','view','edit','add'])? $action:'list';
        
        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;
 
        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'PartNumber';
        $sort_by = in_array($sort_by, ['ID', 'PartNumber', 'Vendor', 'Design', 'Description'])? $sort_by:'PartNumber';
        
        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';  
        
        $query       = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));

        $option     = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");

        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;
        
        $data['cancel_url']   = "wafer";
        $data['download_url'] = "wafer/download";
        if ($action == 'edit' || $action == 'view')
        {
            $product_id = intval($query['ID']);
            $results = $this->m_wafer->query(1, $sort_by, $sort_order, $query, 0, $option);
            $data['edit']  = $results['rows'];          // get original data
            // pass current URL, for top/edit CLEAR button
            $query_url = urlencode(json_encode(array("ID"=>$product_id)));
            $data['clear_url'] = "wafer?action=edit&query=$query_url";
            $data['submit_button'] = "UPDATE";
        } elseif ($action == 'add') {           // if adding
            $product_id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'VendorID'      => '',
                'Active'        => '1',
                'DesignID'      => '',
                'PartNumber'    => '',
                'Description'   => '',
                'DescriptionInternal'   => '',
                'Note'          => ''
            ));
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "wafer?action=add";
            $data['cancel_url'] = "wafer";
            $data['submit_button'] = "ADD";
        } else {                                // if Listing
            $product_id = 0;
        }
        
        $results = $this->m_wafer->query($limit, $sort_by, $sort_order, $query, $offset, $option);
        
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
        
        $data['searchby_options']   = $this->m_wafer->searchby_options();
        $data['vendor_list']        = $this->m_wafer->wafer_vendor_list();
        $data['design_list']        = $this->m_wafer->wafer_design_list();
          
        //pagination
        $config                     = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']         = site_url("wafer?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "List Wafer Part Number"; break;
            case 'edit':    $data['title'] = "Edit WaferPart Number"; break;
            case 'add':     $data['title'] = "Add Part Number";  break;
            case 'view':    $data['title'] = "View Wafer Detail";  break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
 
        $data['message']        = $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content']   = '/wafer/waferlot';
        $data['favorite']       = $this->session->userdata('favorite');
        $data['html_title']     = 'Wafer PartNumber';
        $this->load->view('includes/template',$data);
    }

    function upload_lot()               // top-level Part Number search
    {
        var_dump($_FILES);
        $filename=$_FILES["fileToUpload"]["tmp_name"];
        $storagename = "discussdesk.xlsx";
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $storagename);
        
        $inputFileName = 'discussdesk.xlsx';
        try { $objPHPExcel = PHPExcel_IOFactory::load($inputFileName); } 
        catch(Exception $e) { die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage()); }
        
        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true); 
        $arrayCount = count($allDataInSheet); // Here get total count of row in that Excel sheet 
        
        var_dump($allDataInSheet);
        die();
        
        for($i=2;$i<=$arrayCount;$i++){ 
            $userName = trim($allDataInSheet[$i]["A"]); 
            $userMobile = trim($allDataInSheet[$i]["B"]);
        }
        
        
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
        redirect("wafer?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }    
}

?>