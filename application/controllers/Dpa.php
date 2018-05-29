<?php

class Dpa extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        // redirect if do not have privilege
        $required_priv = array('DPA');
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->model('m_dpa');
        $this->load->model('m_product');
        $this->load->model('m_crm');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->library('mobiledetect');
        $this->load->library('sendmail');
    }
    function quote_email($id, $type) {}
    
    function quote_email2($id, $type)
    {
        //$type= "approved", "shipped"
        $results   = $this->m_dpa->quote_query(1, "", "", array("ID"=>$id), 0, "", 0);
        $request   = $results['rows'][0];          // get original data
        $disti_id  = intval($request['DistiID']);
        
        if ($type == "requested") {
            $email_list = $this->m_crm->sample_email_list('SAM11', $disti_id);

            $html_body = "";
            $results   = $this->m_dpa->quote_query(50, 'RequestDate', 'desc', array("NULL"=>""), 0, 'requested', $disti_id);
            $requests  = $results['rows'];
            
            print_r("<BR><BR>confused <BR><BR>");
            var_dump($requests); die();
            var_dump($email_list);

            print_r("<BR><BR> LK Email: <BR>");
            $email_list = $this->m_crm->sample_email_list('SAM12', 0);
                    var_dump($email_list); die("<BR><BR> STOP");
        } 
        
        if ($type == "requested")
        {
        //$request['CompanyCode'];
        // send to
        } else {    // send summary
            
        }
        
        
        print_r("<BR>????? <BR><BR>");
        var_dump($request); die();
        $this->sendmail->send($mailto, $type, 'MailBody');
    }

    function generate_tab()
    {
        $tab = array(
            "Quote"=>"dpa?action=quote_list",
            "Claim"=>"dpa?action=claim_list"
            );
        return ($tab);
    }
    function index()
    { 
        $action = (isset($_GET['action'])) ? $_GET['action']:'request_list';
        $action = in_array($action, ['quote_list','quote_view','quote_add', 'quote_addcomment',
            'claim_list','claim_edit','claim_add'
            ])? $action:'quote_list';

        if (strpos($action, 'quote') !== false ) {
            $this->quote();
        } elseif (strpos($action, 'claim') !== false ) {
            $this->claim();
        } else { 
            redirect('dpa?action=quorte_list');
            $this->quote();
        }
    }
    
    function quote()
    { 
      $is_mobile = $this->mobiledetect->is_mobile();      // accessing from iPhone, iPOD, Android, mobile devices

      $action = (isset($_GET['action'])) ? $_GET['action']:'quote_list';
      $action = in_array($action, ['quote_list','quote_view','quote_add','quote_addcomment'])? $action:'quote_list';

      $setting = $this->session->userdata('setting');
      (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;

      $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'RequestDate';
      $sort_by = in_array($sort_by, ['RequestDate', 'PartNumber', 'Customer', 'QuotePrice','QuoteStartDate','QuoteExpiryDate'])? $sort_by:'RequestDate';

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

      if (strstr($this->session->userdata['priv'], 'DPA12')) {
          $disti_id = 0;
      } else {
          $disti_id = intval($this->session->userdata['company_id']);
      }

      $data['status'] = $this->m_dpa->get_quote_status();     // translate status code to human readable

      $data['cancel_url']   = "dpa?action=quote";
      if (strstr($this->session->userdata['priv'], 'DPA0')) {
        $data['download_url'] = "dpa/quote_download"; 
      } else {
        $data['download_url'] = "NULL";
      }
      if ($action == 'quote_view' || $action=='quote_addcomment')
      {
          $id = intval($query['ID']);
          $results = $this->m_dpa->quote_query(1, $sort_by, $sort_order, $query, 0, $option, $disti_id);
          $data['edit']  = $results['rows'];          // get original data
          //
          // pass current URL, for top/edit CLEAR button
          $newoption = urlencode($option);
          $query_url = urlencode(json_encode(array("ID"=>$id)));
          $data['comments'] = $this->m_dpa->get_quote_comment($id);           // get associated sample request comment
          //
          //
          //$data['clear_url']     = "sample?action=request_view&query=$query_url&option=$newoption";
          //$data['cancel_url']    = "sample?action=request_list&query=$query_url&option=$newoption";
          $data['submit_button'] = "UPDATE";

          if ($action=='quote_addcomment') {
              $data['id'] = $id;
              $data['clear_url']     = "dpa?action=quote_addcomment&query=$query_url&option=$newoption";
              //$data['cancel_url']    = "dpa?action=quote_addcomment&query=$query_url&option=$newoption";
              $data['submit_button'] = "提交";
          }

      } elseif ($action == 'quote_add') {
          $id = 0;
          $data['requester'] = $this->session->userdata('nickname');

          $data['clear_url'] = "dpa?action=quote_add";
          $data['submit_button'] = "SUBMIT";

      } elseif ($action == 'request_list') {           // if adding
          $id = 0;
      }

      $results = $this->m_dpa->quote_query($limit, $sort_by, $sort_order, $query, $offset, $option, $disti_id);
      $data['quotes']             = $results['rows'];
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

      $data['searchby_options']   = $this->m_dpa->quote_searchby_options();

      $data['product_list']       = $this->m_product->product_list("sell");
      $data['customer_list']      = $this->m_dpa->final_customer_list($disti_id);
      $data['disti_list']         = $this->m_crm-> company_list();

      //pagination
      $config = array();
      $config['page_query_string'] = TRUE;
      $config['enable_query_strings'] = TRUE;
      $config['query_string_segment'] = 'os';
      $config['base_url']    = site_url("dpa?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
          case 'quote_list':    $data['title'] = "Quote List"; break;
          case 'quote_edit':    $data['title'] = "Edit Quote"; break;
          case 'quote_add':     $data['title'] = "Request Price Quote";  break;
          case 'quote_view':    $data['title'] = "View Request";  break;
          case 'quote_addcomment': $data['title'] = "Add Comment"; break;
          default:        $data['title'] = "ERROR"; break;
      }

      $this->pagination->initialize($config);
      $data['pagination']  = $this->pagination->create_links();

      $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
      $data['scripts'] = '/dpa/quote_scripts';
      $data['main_content'] = '/dpa/quote';
      $data['favorite'] = $this->session->userdata('favorite');
      $data['tab'] = $this->generate_tab();
      $data['html_title'] = 'DPA Price Quote';
      $this->load->view('includes/template',$data);
  }

    function quote_search()               
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
        if (($hide_team ==1) && (strstr($this->session->userdata['priv'], 'SAM11')))      {$option = $option."hideteam ";}

        $message = "search completed";
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode($query));
        redirect("dpa?action=quote_list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
      }

    function quote_addcomment() 
    {
        $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
        $query        = $this->input->post('query');
        $option       = $this->input->post('option');
        $id           = $this->input->post('ID');
        $user_id      = $this->session->userdata('user_id');
        $comment      = $this->input->post('newcomment');

        if (!empty($comment))
        { 
            $this->m_dpa->quote_addcomment($user_id, $id, $comment);
            $message = "Record Added Sucessfully";
        } else {
            $message = "No comment is added";
        }
        $this->session->set_flashdata('message',$message);

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("dpa?action=quote_view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
  
    function quote_add()             // top-level Part Number edit
    {
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');
        
        $product_list    = $this->m_product->product_list("sell");
        $requester    = $this->session->userdata('nickname');
        $comment  = $this->input->post('comment');
                
        $info = array(
            'Status'            => 1,
            'RequesterID'       => $this->session->userdata('user_id'),
            'RequestDate'       => date("Y-m-d"),
            'RequestTime'       => date("H:i:s"),
            'FinalCustomerID'   => $this->input->post('final_customer_id'),
            'Project'           => $this->input->post('project'),
            'ProductID'         => $this->input->post('product_id'),
            'PartNumber'        => $product_list[$this->input->post('product_id')],
            'QuotePrice'        => $this->input->post('quote_price'),
            'QuoteMaxQty'       => $this->input->post('quote_max_qty'),
            'QuoteStartDate'    => $this->input->post('quote_start_date'),
            'QuoteExpiryDate'   => $this->input->post('quote_expiry_date'),
            'ClosureQty'        => 0
        );


        $id = $this->m_dpa->quote_add($info, $requester, $comment);
        switch ($id) {
            case -1: $message = "Something is Wrong (sample/request-add)"; break;
            default: $message = "Record Added Sucessfully";
        }
        $this->session->set_flashdata('message',$message);

        $this->quote_email($id, "requested");
        
        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("dpa?action=quote_view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }

    function quote_process()
    {
        $limit        = $this->input->post('limit');
        $sort_by      = $this->input->post('sort_by');
        $sort_order   = $this->input->post('sort_order');
        $query        = $this->input->post('query');
        $option       = $this->input->post('option');

        $id           = intval($this->input->post('id'));
        $status       = intval($this->input->post('status'));
        $qty          = $this->input->post('qty');
        $comment      = $this->input->post('comment');
        $submit       = $this->input->post('submit');
        $this->quote_process2($limit,$sort_by,$sort_order,$query,$option,$id,$status,$qty,$comment,$submit);
    }
    
    function quote_process2($limit=0,$sort_by="",$sort_order="",$query="",$option="",$id=0,$status=0,$qty=0,$comment="",$submit="")
    {
        
        $results   = $this->m_dpa->quote_query(1, $sort_by, $sort_order, array("ID"=>$id), 0, $option, 0);
        $data      = $results['rows'][0];          // get original data
  
        $info = array();
        if ($submit == "approved") {
            if ($status == 1 && intval($data['Status'])==1) {
                $info['Status']         = 2;
                $info['ApproverID']   = $this->session->userdata('user_id');
                $info['ApproveDate']  = date("Y-m-d");
                $info['ApproveTime']  = date("H:i:s");
                $sys_comment = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." approved ";
                $message = "申请 Approved";
                $this->m_dpa->quote_update($id, $info, $comment, $sys_comment);
                $this->quote_email($id, "approved");
            } else {
                $message = "操作失败";
            }

        } elseif (($submit == "rejected" || $submit == "canceled") && intval($data['Status'])<=1) {
            if ($data['RequesterID'] == $this->session->userdata('user_id')) {
                $info['Status'] = 99;
                $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 取消样品申请 ";
                $message        = "取消样品申请";
            } else {
                $info['Status'] = 100;
                $info['ApproverID']     = $this->session->userdata('user_id');
                $info['ApproveDate']    = date("Y-m-d");
                $info['ApproveTime']    = date("H:i:s");
                $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 拒绝申请 ";
                $message        = "申请被拒绝";
            }
            $this->m_dpa->quote_update($id, $info, $comment, $sys_comment);
            
        } else {
           $message        = "操作失败2";
           // die("ERROR in sample request modal");
        }
                
        $this->session->set_flashdata('message',$message);

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("dpa?action=quote_list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }


    function quote_download()
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
        $results = $this->m_dpa->query(100000, $sort_by, $sort_order, $query, 0, $option); die("STOP");
        $requests   = $results['rows'];

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
        foreach($requests as $request) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $request['BacklogID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $request['ID']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $request['Entity']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $request['CompanyCode']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $request['OrderDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $request['CustomerPO']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $request['Item']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $request['PartNumber']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $request['RequestDate']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $request['BookingPrice']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $request['OrderedQty']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $request['ShippedQty']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, ($request['OrderedQty']-$request['ShippedQty']));
            $excel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $request['Status']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $request['OrderNote']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $request['ItemNote']);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $request['Record']);
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