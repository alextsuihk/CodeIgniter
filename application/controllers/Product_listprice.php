<?php

class Product_ListPrice extends CI_Controller {

    function __construct()
    {
        parent::__construct();   
        date_default_timezone_set("Asia/Taipei");
        $is_logged_in = $this->session->userdata('is_logged_in');   
        if (!isset($is_logged_in) || $is_logged_in != true)
        {
            $message = "Please Login again";
            $this->session->set_flashdata('message',$message);
            redirect('.');
        } else {

        // redirect if do not have privilege
        $required_priv = array('PRO3','IPN'); 
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        }
        
        $this->load->model('m_product_listprice');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
    }

    
    function index()
    {
        $action = (isset($_GET['action'])) ? $_GET['action']:'list';
        $action = in_array($action, ['list','history','update'])? $action:'list';
        
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
        
        $data['cancel_url']   = "product_listprice";
        $data['download_url'] = "product_listprice/download";
        if ($action == 'update')
        {            
            $product_id = intval($query['ID']);
            $query_url = urlencode(json_encode(array("ID"=>$product_id)));
            $data['submit_button'] = "UPDATE";
            $data['clear_url'] = "product_listprice?action=update&query=$query_url";
        } elseif ($action == 'history') {
            $product_id = intval($query['ID']);
            $results = $this->m_product_listprice->query('history',$limit, $sort_by, $sort_order, $query, $offset, $option);
            $data['listprices']     = $results['rows'];
            $product_id = 0;
        } else {                                // if Listing
            $product_id = 0;
        }
        
        $results = $this->m_product_listprice->query('list',$limit, $sort_by, $sort_order, $query, $offset, $option);
        
        $data['products']           = $results['rows'];        
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
        $data['product_id']         = $product_id;
        $data['searchby_options']   = $this->m_product_listprice->searchby_options();
          
        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("product_listprice?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'list':    $data['title'] = "Product List Price"; break;
            case 'update':  $data['title'] = "Update Product List Price"; break;
            case 'history': $data['title'] = "Product List Price History"; break;
            default:        $data['title'] = "ERROR"; break;
        }
        
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

          // backup uri infor
//        $uri = json_encode(array($action,$limit,$sort_by,$sort_order,$query_field,$query_value,$offset));
//        $this->session->set_flashdata('uri',$uri);
//        $this->session->set_flashdata('product_id', $product_id);
//        $this->session->set_flashdata('action', $action);       // for top_edit() for identifying "edit" or "add"
////        $this->session->set_flashdata('part_number', $data['products']['0']['PartNumber']); // save P/N

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['main_content'] = '/product/product_listprice';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['html_title'] = 'Booking Price';
        $this->load->view('includes/template',$data);
    }
       
     function search()               // top-level Part Number search
    {
        $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
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

        $message = "search completed";
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode($query));
        redirect("product_listprice?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            'ProductID'     => $product_id,
            'EffectiveDate' => $this->input->post('effective_date'),
            'Commission1'   => $this->input->post('commission1'),
            'Commission2'   => $this->input->post('commission2'),
            'ListPrice'     => $this->input->post('list_price'),
            'DistiLeadTime' => $this->input->post('disti_lead_time')
        );
        $id = $this->m_product_listprice->update_record( $product_id, $data);
            switch ($id) {
                case -1: $message = "Update Fail: New Effective Date must be newer"; break;
                default: $message = "Record Update Sucessfully";
            }

        $this->session->set_flashdata('message',$message);

        $query_url = urlencode(json_encode(array("ID"=>$product_id)));
        redirect("product_listprice?action=list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option"); 
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
            $filename = 'BookingPrice_'.date("Y-m-d").'.xls';
        } else {
            $filename = 'BookingPrice_'.date("Y-m-d").'_filtered.xls';
        }
                
        // fetch data from database
        $results = $this->m_product_listprice->query('list', 100000, $sort_by, $sort_order, $query, 0, $option);
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
        $excel->getActiveSheet()->setTitle('Booking Price');
        $excel->getActiveSheet()
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Active')
            ->setCellValue('C1', 'Part Number')
            ->setCellValue('D1', 'Effective Date')
            ->setCellValue('E1', 'Booking Price (USD)')
            ->setCellValue('F1', 'Disti Mrgin')
            ->setCellValue('G1', 'Disti Lead Time (Days)')
            ->setCellValue('H1', 'Description 1')
            ->setCellValue('I1', 'Description 2')
            ->setCellValue('J1', 'Ball Type')
            ->setCellValue('K1', 'Package Size');
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth('5');
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth('25');
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth('10');
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth('35');
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth('35');
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth('15');
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth('15');
        $excel->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
        $excel->getActiveSheet()->setAutoFilter('A1:K1');

        $row = 2;
        foreach($products as $product) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $product['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, ($product['Active'])?'A':'I');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $product['PartNumber']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $product['EffectiveDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $product['ListPrice']);
            $margin = $product['Commission1'].'%';
            if ($product['Commission2'] > 0)  
            { $margin += " + $".$product['Commission2']; }
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $margin);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $product['DistiLeadTime']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $product['Description']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $product['Description2']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $product['BallType']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $product['PackageSize']);
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