<?php

class Sample extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        
        // redirect if do not have privilege
        $required_priv = array('SAM');
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
        
        $this->load->model('m_sample');
        $this->load->model('m_product');
        $this->load->model('m_crm');
        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->library('mobiledetect');
        $this->load->library('sendmail');
    }
    function sample_email($id, $type) {}
    
    function sample_email2($id, $type)
    {
        //$type= "approved", "shipped"
        $results   = $this->m_sample->request_query(1, "", "", array("ID"=>$id), 0, "", 0, 0);
        $request   = $results['rows'][0];          // get original data
        $disti_id  = intval($request['DistiID']);
        
        if ($type == "requested") {
            $email_list = $this->m_crm->sample_email_list('SAM11', $disti_id);

            $html_body = "";
            $results   = $this->m_sample->request_query(50, 'RequestDate', 'desc', array("NULL"=>""), 0, 'requested', $disti_id, 0);
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
            "样品申请"=>"sample?action=request_list",
            "客人地址"=>"sample?action=addr_list",
            "库存"=>"sample?action=inv_summary"
            );
        return ($tab);
    }
    function index()
    { 
        $action = (isset($_GET['action'])) ? $_GET['action']:'request_list';
        $action = in_array($action, ['request_list','request_view','request_add', 'request_add2','request_addcomment',
            'team_list', 'team_edit', 'team_add', 'ctm_list','ctm_edit', 'ctm_register',
            'addr_list','addr_edit','addr_add','addr_addr2', 'inv_summary', 'inv_detail', 'inv_move'
            ])? $action:'request_list';


        if (strpos($action, 'request') !== false ) {
            $this->request();
        } elseif (strpos($action, 'ctm') !== false ) {
            $this->ctm();
        } elseif (strpos($action, 'addr') !== false) {
            $this->addr();
        } elseif (strpos($action, 'inv') !== false ) {
            $this->inventory();
        } else { 
            redirect('sample?action=request_list');
            $this->request();
        }
    }
    
    function request()
    { 
      $is_mobile = $this->mobiledetect->is_mobile();      // accessing from iPhone, iPOD, Android, mobile devices

      $action = (isset($_GET['action'])) ? $_GET['action']:'request_list';
      $action = in_array($action, ['request_list','request_view','request_add','request_add2','request_addcomment'])? $action:'request_list';

      $setting = $this->session->userdata('setting');
      (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;

      $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'RequestDate';
      $sort_by = in_array($sort_by, ['RequestDate', 'PartNumber', 'Customer'])? $sort_by:'RequestDate';

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

      if (strstr($this->session->userdata['priv'], 'SAM12')) {
          $disti_id = 0;
      } else {
          $disti_id = intval($this->session->userdata['company_id']);
      }
      if (strstr($this->session->userdata['priv'], 'SAM11')) { 
          $user_id = 0;
      } else {
          $user_id = $this->session->userdata['user_id'];
      }

      $data['status'] = $this->m_sample->get_request_status();     // translate status code to human readable

      $data['cancel_url']   = "sample?action=request";
      if (strstr($this->session->userdata['priv'], 'SAM0')) {
        $data['download_url'] = "sample/request_download"; 
      } else {
        $data['download_url'] = "NULL";
      }
      if ($action == 'request_view' || $action=='request_addcomment')
      {
          $id = intval($query['ID']);
          $results = $this->m_sample->request_query(1, $sort_by, $sort_order, $query, 0, $option, $disti_id, $user_id);
          $data['edit']  = $results['rows'];          // get original data
          //
          // pass current URL, for top/edit CLEAR button
          $newoption = urlencode($option);
          $query_url = urlencode(json_encode(array("ID"=>$id)));
          $data['comments'] = $this->m_sample->get_request_comment($id);           // get associated sample request comment
          //
          //
          //$data['clear_url']     = "sample?action=request_view&query=$query_url&option=$newoption";
          //$data['cancel_url']    = "sample?action=request_list&query=$query_url&option=$newoption";
          $data['submit_button'] = "UPDATE";

          if ($action=='request_addcomment') {
              $data['id'] = $id;
              $data['clear_url']     = "sample?action=request_addcomment&query=$query_url&option=$newoption";
              //$data['cancel_url']    = "sample?action=request_addcomment&query=$query_url&option=$newoption";
              $data['submit_button'] = "更新";
          }

      } elseif ($action == 'request_add') {
          $id = 0;
          $data['requester'] = $this->session->userdata('nickname');

          // pass current URL, for top/edit CLEAR button
          $data['clear_url'] = "sample?action=request_add";
          $data['submit_button'] = "下一步";

      } elseif ($action == 'request_add2') {
          $info = ((array) json_decode(urldecode($_GET['info'])));
          $data['edit'] = array();
          foreach ($info as $key => $value)
          {
              $data[$key] = $value;
          }
          $data['recipient_list']     = $this->m_sample->recipient_list($info['final_customer_id']);
          $data['submit_button'] = "完成";

      } elseif ($action == 'request_list') {           // if adding
          $id = 0;
      }

      $results = $this->m_sample->request_query($limit, $sort_by, $sort_order, $query, $offset, $option, $disti_id, $user_id);

      // prepare inventory list
      $inventory = array();
      $my_available = array();
      $recipient = array();
      for ($i=0; $i<sizeof($results['rows']); $i++)
      {
          $total = 0;
          $r = $this->m_sample->inventory_query(1, "", "", array("PartNumber"=>$results['rows'][$i]['PartNumber']), 0, "", "", 0, "");
          $text = "";
          for ($j=0; $j<sizeof($r['rows']); $j++) {
              $text = $text.$r['rows'][$j]['Warehouse']."\t\t\t".$r['rows'][$j]["Qty"]."pcs \n";
              $total += $r['rows'][$j]["Qty"];
          }
          $inventory[$i] = $results['rows'][$i]['PartNumber']." Inventory \n\n Total Available: \t\t".$total."\n\n".$text;

          $t = $this->m_sample->inventory_query(1, "", "", array("PartNumber"=>$results['rows'][$i]['PartNumber']), 0, "", "", intval($this->session->userdata['user_id']), "");         
          $my_available[$i] = (empty($t['rows']))?0:(intval($t['rows'][0]['Qty']));
          $recipient[$i]    = $this->m_sample->recipient($results['rows'][$i]['RecipientID']);
      }

      $data['inventory']          = $inventory;
      $data['my_available']       = $my_available;
      $data['recipient']          = $recipient;
      $data['requests']           = $results['rows'];
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

      $data['searchby_options']   = $this->m_sample->request_searchby_options();

      $data['product_list']       = $this->m_product->product_list("sample");
      $data['customer_list']      = $this->m_sample->final_customer_list($disti_id);
      $data['courier_list']       = $this->m_sample->courier_list();
      $data['disti_list']         = $this->m_crm-> company_list();
      
      //pagination
      $config = array();
      $config['page_query_string'] = TRUE;
      $config['enable_query_strings'] = TRUE;
      $config['query_string_segment'] = 'os';
      $config['base_url']    = site_url("sample?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
          case 'request_list':    $data['title'] = "Request List"; break;
          case 'request_edit':    $data['title'] = "Edit Request"; break;
          case 'request_add':     $data['title'] = "Request Sample";  break;
          case 'request_add2':    $data['title'] = "选地址Request";  break;
          case 'request_view':    $data['title'] = "View Request";  break;
          case 'request_addcomment': $data['title'] = "Add Comment"; break;
          default:        $data['title'] = "ERROR"; break;
      }

      $this->pagination->initialize($config);
      $data['pagination']  = $this->pagination->create_links();

      $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
      $data['scripts'] = '/sample/request_scripts';
      $data['main_content'] = '/sample/request';
      $data['favorite'] = $this->session->userdata('favorite');
      $data['tab'] = $this->generate_tab();
      $data['html_title'] = 'Sample Request';
      $this->load->view('includes/template',$data);
  }

    function request_search()               
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
        redirect("sample?action=request_list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
      }

    function request_addcomment() 
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
            $this->m_sample->request_addcomment($user_id, $id, $comment);
            $message = "Record Added Sucessfully";
        } else {
            $message = "No comment is added";
        }
        $this->session->set_flashdata('message',$message);

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("sample?action=request_view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
  
    function request_add()             // top-level Part Number edit
    {
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');

        $product_list    = $this->m_product->product_list("sample");
        
        if (strstr($this->session->userdata['priv'], 'SAM12')) {
            $disti_id = 0;
        } else {
            $disti_id = intval($this->session->userdata['company_id']);
        }
        $customer_list   = $this->m_sample->final_customer_list($disti_id);
        $info = array(
            'status'            => 1,
            'final_customer_id' => $this->input->post('final_customer_id'),
            'final_customer'    => $customer_list[$this->input->post('final_customer_id')],
            'project'           => $this->input->post('project'),
            'product_id'        => $this->input->post('product_id'),
            'part_number'       => $product_list[$this->input->post('product_id')],
            'requester'         => $this->session->userdata('nickname'),
            'requester_id'      => $this->session->userdata('user_id'),
            'request_date'      => date("Y-m-d"),
            'request_time'      => date("H:i:s"), 
            'request_qty'       => $this->input->post('request_qty'),
            'comment'         => $this->input->post('comment')
        );

        $info = urlencode(json_encode($info));
        redirect("sample?action=request_add2&info=$info"); 
    }

    function request_add2()             // top-level Part Number edit
    {
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');

        $product_list    = $this->m_product->product_list("sample");

        $requester_id = $this->session->userdata('user_id');
        $requester    = $this->session->userdata('nickname');
        $comment  = $this->input->post('comment');
                
        $info = array(
            'Status'            => 1,
            'FinalCustomerID'   => $this->input->post('final_customer_id'),
            'Project'           => $this->input->post('project'),
            'ProductID'         => $this->input->post('product_id'),
            'PartNumber'        => $product_list[$this->input->post('product_id')],
            'RequesterID'       => $this->session->userdata('user_id'),
            'RequestDate'       => $this->input->post('request_date'),
            'RequestTime'       => $this->input->post('request_time'),
            'RequestQty'        => $this->input->post('request_qty'),
            'RecipientID'       => $this->input->post('recipient_id')
        );

        if (strstr($this->session->userdata['priv'], 'SAM11'))
        {
            $info['Status']     = 2;
            $info['PmApproveDate'] = $info['RequestDate'];
            $info['PmApproveTime'] = $info['RequestTime'];
            $info['PmApproverID']  = $info['RequesterID'];
            $info['PmApproveQty']  = $info['RequestQty'];
        }

        if (strstr($this->session->userdata['priv'], 'SAM12'))
        {
            $info['Status']     = 3;
            $info['ApproveDate'] = $info['RequestDate'];
            $info['ApproveTime'] = $info['RequestTime'];
            $info['ApproverID']  = $info['RequesterID'];
            $info['ApproveQty']  = $info['RequestQty'];
        }
        
        $id = $this->m_sample->request_add($info, $requester, $comment);
        switch ($id) {
            case -1: $message = "Something is Wrong (sample/request-add)"; break;
            default: $message = "Record Added Sucessfully";
        }
        $this->session->set_flashdata('message',$message);

        $this->sample_email($id, "requested");
        
        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("sample?action=request_view&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }

    function request_process()
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
        $this->request_process2($limit,$sort_by,$sort_order,$query,$option,$id,$status,$qty,$comment,$submit);
    }
    
    function request_process2($limit=0,$sort_by="",$sort_order="",$query="",$option="",$id=0,$status=0,$qty=0,$comment="",$submit="")
    {
        
        $results   = $this->m_sample->request_query(1, $sort_by, $sort_order, array("ID"=>$id), 0, $option, 0, 0);
        $data      = $results['rows'][0];          // get original data
  
        $info = array();
        if ($submit == "approved") {
            if ($status == 3 && intval($data['Status'])==3 ) {
                $info['Status']     = 4;
                $info['DockerID']   = $this->session->userdata('user_id');
                $info['DockDate']   = date("Y-m-d");
                $info['DockTime']   = date("H:i:s");
                $info['DockQty']    = $this->input->post('qty'); 
                $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 安排寄送 ";
                $message        = "已抢单";
                $this->m_sample->request_update($id, $info, $comment, $sys_comment);
//                $this->sample_email($id, "shipping");
            } elseif ($status == 2 && intval($data['Status'])==2 && strstr($this->session->userdata['priv'], 'SAM12')) {
                $info['Status']         = 3;
                $info['ApproverID']     = $this->session->userdata('user_id');
                $info['ApproveDate']    = date("Y-m-d");
                $info['ApproveTime']    = date("H:i:s");
                $info['ApproveQty']     = $this->input->post('qty');
                $sys_comment = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." approved ".$qty."pcs";
                $message = "申请 Approved";
                $this->m_sample->request_update($id, $info, $comment, $sys_comment);
                $this->sample_email($id, "approved");
            } elseif ($status == 1 && intval($data['Status'])==1) {
                $info['Status']         = 2;
                $info['PmApproverID']   = $this->session->userdata('user_id');
                $info['PmApproveDate']  = date("Y-m-d");
                $info['PmApproveTime']  = date("H:i:s");
                $info['PmApproveQty']   = $this->input->post('qty');  
                $sys_comment = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." approved ".$qty."pcs";
                $message = "申请 Approved";
                $this->m_sample->request_update($id, $info, $comment, $sys_comment);
                $this->sample_email($id, "approved");
            } else {
                $message = "操作失败";
            }

        } elseif (($submit == "rejected" || $submit == "canceled") && intval($data['Status'])<=3) {
            if ($data['RequesterID'] == $this->session->userdata('user_id')) {
                $info['Status'] = 99;
                $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 取消样品申请 ";
                $message        = "取消样品申请";
            } else {
                $info['Status'] = 100;
                $info['ApproverID']     = $this->session->userdata('user_id');
                $info['ApproveDate']    = date("Y-m-d");
                $info['ApproveTime']    = date("H:i:s");
                $info['ApproveQty']     = 0;    
                $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 拒绝申请 ";
                $message        = "申请被拒绝";
            }
            $this->m_sample->request_update($id, $info, $comment, $sys_comment);
            
        } elseif ($submit == "shipped" && intval($data['Status'])==4) {
            $info['CreatorID']      = $this->session->userdata('user_id');
            $info['Date']           = date("Y-m-d");
            $info['Time']           = date("H:i:s");
            $info['RequestID']      = $id;
            $results                = $this->m_sample->request_query(1, "", "", array("ID"=>$id), 0, 0, 0, 0);
            $info['ProductID']      = $results['rows'][0]['ProductID'];
            $info['PartNumber']     = $results['rows'][0]['PartNumber'];
            $info['WarehouseID']    = $this->m_sample->warehouse_list($this->session->userdata('user_id'));
            $info['Qty']            = -1*$this->input->post('qty');
            $info['Comment']        = "auto generated";
            $this->m_sample->inventory_move($info);     // update Inventory Record
            
            $info = array();                // clear $info array
            $info['Status']     = 5;
            $info['ShipperID']   = $this->session->userdata('user_id');
            $info['ShipDate']   = date("Y-m-d");
            $info['ShipTime']   = date("H:i:s");
            $info['ShipQty']    = $this->input->post('qty');
            $info['CourierID']  = $this->input->post('courier_id');
            $info['AWB']        = $this->input->post('AWB');
            $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 已寄送 ";
            $message        = "已寄出";
            $this->m_sample->request_update($id, $info, $comment, $sys_comment);
            $this->sample_email($id, "shipped");
        } elseif ($submit == "revise") {
            $new_status = 0;
            $sys_comment    = "Status:".$info['Status']."  ".$this->session->userdata('nickname')." 要求更改数量 ";
            $message        = "updated";
            $this->m_sample->request_update($id, $info, $comment, $sys_comment);
        } else {
           $message        = "操作失败2";
           // die("ERROR in sample request modal");
        }
                
        $this->session->set_flashdata('message',$message);

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("sample?action=request_list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }

    function addr()
    { 
        $is_mobile = $this->mobiledetect->is_mobile();      // accessing from iPhone, iPOD, Android, mobile devices

        $action = (isset($_GET['action'])) ? $_GET['action']:'addr_list';
        $action = in_array($action, ['addr_list','addr_edit','addr_add'])? $action:'addr_list';

        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;

        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'Customer';
        $sort_by = in_array($sort_by, ['Customer', 'Name', 'Phone','Address'])? $sort_by:'Customer';

        $sort_order = (isset($_GET['so'])) ? $_GET['so']:'asc';
        $sort_order = in_array($sort_order, ['asc', 'desc'])? $sort_order:'asc';

        $query  = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");

        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;

        if (strstr($this->session->userdata['priv'], 'SAM32')) {
            $disti_id = 0;
        } else {
            $disti_id = intval($this->session->userdata['company_id']);
        }

        $data['cancel_url']   = "sample?action=addr_list";
        if (strstr($this->session->userdata['priv'], 'SAM0')) {
          $data['download_url'] = "sample/addr_download"; 
        } else {
          $data['download_url'] = "NULL";
        }
        if ($action == 'addr_edit')
        {
            $id = intval($query['ID']);
            $results = $this->m_sample->addr_query(1, $sort_by, $sort_order, $query, 0, $option, $disti_id);
            $data['edit']  = $results['rows'];          // get original data
            //
            // pass current URL, for top/edit CLEAR button
            $newoption = urlencode($option);
            $query_url = urlencode(json_encode(array("ID"=>$id)));
            $data['clear_url']     = "sample?action=addr_edit&query=$query_url&option=$newoption";
            //$data['cancel_url']    = "sample?action=request_list&query=$query_url&option=$newoption";
            $data['submit_button'] = "UPDATE";

        } elseif ($action == 'addr_add') {           // if adding (OEM)
            $id = 0;
            $data['edit'] = array ( array(    // empty data for "NEW" record  
                'Active'            => '1',
                'FinalCustomerID'   => '',
                'Name'              => '',
                'Phone'             => '',
                'Address'           => ''
            ));
            // pass current URL, for top/edit CLEAR button
            $data['clear_url'] = "sample?action=addr_add";
            $data['submit_button'] = "ADD";
        } else {
            $id=0;
        }

        $results = $this->m_sample->addr_query($limit, $sort_by, $sort_order, $query, $offset, $option, $disti_id);

        $data['id']                 = $id;
        $data['addresses']          = $results['rows'];
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

        $data['searchby_options']   = $this->m_sample->addr_searchby_options();
        $data['customer_list']      = $this->m_sample->final_customer_list($disti_id);
      
        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("sample?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'addr_list': $data['title'] = "样品收件人"; break;
            case 'addr_edit': $data['title'] = "编辑样品收件人"; break;
            case 'addr_add':  $data['title'] = "新增样品收件人";  break;
            default:          $data['title'] = "ERROR"; break;
        }

        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['scripts'] = '/sample/request_scripts';
        $data['main_content'] = '/sample/address';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['tab'] = $this->generate_tab();
        $data['html_title'] = 'Sample Reciptent';
        $this->load->view('includes/template',$data);
  }

    function addr_search()               
    {
        $limit          = $this->input->post('limit');
        $sort_by        = $this->input->post('sort_by');
        $sort_order     = $this->input->post('sort_order');
        $active_only    = $this->input->post('active_only');
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
        redirect("sample?action=addr_list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
      }

    function addr_edit()             // top-level Part Number edit
    {
        $action      = $this->input->post('action');
        $id          = $this->input->post('id');
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');

        $data = array(
            'Active'            => ($this->input->post('active'))?'1':'0',
            'FinalCustomerID'   => $this->input->post('final_customer_id'),
            'Address'           => $this->input->post('address'),
            'Name'              => $this->input->post('name'),
            'Phone'             => $this->input->post('phone')
        );

        if ($action=='addr_edit') {
            $result     = $this->m_sample->addr_update($id, $data);
            $message    = "Record Updated Sucessfully";
            $this->session->set_flashdata('message',$message);
        } elseif ($action == 'addr_add') {
            $id = $this->m_sample->addr_add($data);
            switch ($id) {
                case -1:    $message = "Something is wrong"; break;
                default:    $message = "Record Added Sucessfully";
            }
            $this->session->set_flashdata('message',$message);
        }

        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("sample?action=addr_list&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function inventory()
    {
        if (!strstr($this->session->userdata['priv'], 'SAM6')) {
            $message = "You do NOT have privilege to access, return to MainMenu";
            $this->session->set_flashdata('message',$message);
            redirect('main_menu');             // no privilege, redirect to main menu    
        }
        
        $is_mobile = $this->mobiledetect->is_mobile();      // accessing from iPhone, iPOD, Android, mobile devices

        $action = (isset($_GET['action'])) ? $_GET['action']:'request_list';
        $action = in_array($action, ['inv_summary','inv_detail','inv_move','inv_adjust'])? $action:'inv_summary';

        $setting = $this->session->userdata('setting');
        (isset($setting['limit'])) ? $limit = $setting['limit'] : $limit = 20;

        $sort_by = (isset($_GET['sb'])) ? $_GET['sb']:'RequestDate';
        $sort_by = in_array($sort_by, ['Date', 'PartNumber', 'Warehouse'])? $sort_by:'Date';

        if (isset($_GET['so'])) {
            $sort_order =  $_GET['so'];
        } elseif ($sort_by =="Date") {
            $sort_order = "desc";
        } else {
            $sort_order = "asc";
        }

        $query  = (isset($_GET['query']) ? ((array) json_decode(urldecode($_GET['query']))) : array("NULL" => ""));
        $option = (isset($_GET['option']) ? (urldecode($_GET['option'])) : "");
        $newoption = urlencode($option);

        $offset = (isset($_GET['os'])) ? $_GET['os']:0;
        $offset = (is_numeric($offset))? $offset:0;

        if (strstr($this->session->userdata['priv'], 'SAM61')) {
            $disti_id = 0;
        } else {
            $disti_id = $this->session->userdata['company_id'];
        }

        $data['cancel_url']   = "sample?action=$action";
        if (strstr($this->session->userdata['priv'], 'SAM0')) {
          $data['download_url'] = "sample/inventory_download"; 
        } else {
          $data['download_url'] = "NULL";
        }
        if ($action == 'inv_unknown' || $action=='inv_unknown')
        {
            $id = intval($query['ID']);
            $results = $this->m_sample->request_query(1, $sort_by, $sort_order, $query, 0, $option, $disti_id, 0, "");
            $data['edit']  = $results['rows'];          // get original data
            //
            // pass current URL, for top/edit CLEAR button
            $newoption = urlencode($option);
            $query_url = urlencode(json_encode(array("ID"=>$id)));
            $data['comments'] = $this->m_sample->get_request_comment($id);           // get associated sample request comment
            //
            //
            $data['clear_url']     = "sample?action=$actionn&sb=$sort_by&so=$sort_order&query=$query_url&option=$newoption";
            $data['cancel_url']    = "sample?action=$action&n&sb=$sort_by&so=$sort_order&query=$query_url&option=$newoption";
            $data['submit_button'] = "UPDATE";

            if ($action=='request_addcomment') {
                $data['id'] = $id;
                $data['clear_url']     = "sample?action=request_addcomment&query=$query_url&option=$newoption";
                //$data['cancel_url']    = "sample?action=request_addcomment&query=$query_url&option=$newoption";
                $data['submit_button'] = "更新";
            }
            
        } elseif ($action == 'inv_summary') {           // if adding
            $id   = 0;
            $type = "summary";
            $data['type'] = $type;

        } elseif ($action == "inv_detail" || $action == "inv_move") {
            $id   = 0;
            $type = "detail";
            $data['type'] = $type;
        }

        $results = $this->m_sample->inventory_query($limit, $sort_by, $sort_order, $query, $offset, $option, $disti_id, 0, $type);

        $data['transactions']       = $results['rows'];
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

        $data['searchby_options']   = $this->m_sample->inventory_searchby_options($type);
        $data['product_list']       = $this->m_product->product_list("sample");
        $data['warehouse_list']     = $this->m_sample->warehouse_list();
        
        //pagination
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
        $config['query_string_segment'] = 'os';
        $config['base_url']    = site_url("sample?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
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
            case 'inv_summary':     $data['title'] = "Inventory Summary"; break;
            case 'inv_detail':      $data['title'] = "Inventory Transaction"; break;
            case 'inv_move':        $data['title'] = "Inventory Movement"; break;
            case 'request_add':     $data['title'] = "Request Sample";  break;
            case 'request_add2':    $data['title'] = "选收件人地址";  break;
            case 'request_view':    $data['title'] = "View Request";  break;
            case 'request_addcomment': $data['title'] = "Add Comment"; break;
            default:        $data['title'] = "ERROR"; break;
        }

        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();

        $data['message']= $this->session->flashdata('message');     //read in "previous message, e,g, INSERT ok"
        $data['scripts'] = '/sample/request_scripts';
        $data['main_content'] = '/sample/inventory';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['tab'] = $this->generate_tab();
        $data['html_title'] = 'Sample Inventory';
        $this->load->view('includes/template',$data);
    }

    function inventory_search()               
    {
        $action         = $this->input->post('action');
        $limit          = $this->input->post('limit');
        $sort_by        = $this->input->post('sort_by');
        $sort_order     = $this->input->post('sort_order');
        $show_rma       = $this->input->post('show_rma');
        $grp_comp       = $this->input->post('grp_comp');
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
        if ($grp_comp ==1) {$option = $option."grpcomp ";}
        if (($show_rma==1) && ($grp_comp!=1) && (strstr($this->session->userdata['priv'], 'SAM61')))      {$option = $option."showrma ";}


        $message = "search completed";
        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode($query));
        redirect("sample?action=$action&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
      }

    function inventory_move()             // top-level Part Number edit
    {
        $limit       = $this->input->post('limit');
        $sort_by     = $this->input->post('sort_by');
        $sort_order  = $this->input->post('sort_order');
        $query       = $this->input->post('query');
        $option      = $this->input->post('option');

        $product_list    = $this->m_product->product_list("sample");
               
        $info = array(
            'CreatorID'         => $this->session->userdata('user_id'),
            'Date'              => date("Y-m-d"),
            'Time'              => date("H:i:s"),
            'RequestID'         => 0,
            'ProductID'         => $this->input->post('product_id'),
            'PartNumber'        => $product_list[$this->input->post('product_id')],
            'WarehouseID'       => $this->input->post('warehouse_id'),
            'Qty'               => $this->input->post('qty'),
            'Comment'           => $this->input->post('comment')
        );

        $id = $this->m_sample->inventory_move($info);
        $message = "Record Added Sucessfully";

        $this->session->set_flashdata('message',$message);
        $query_url = urlencode(json_encode(array("ID"=>$id)));
        redirect("sample?action=inv_detail&sb=$sort_by&so=$sort_order&query=$query_url&option=$option");
    }
    
    function request_download()
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