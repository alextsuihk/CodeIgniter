<?php

class M_Crm extends CI_Model {
    function company_query($limit, $sort_by, $sort_order,$query, $offset, $option) {
        $cond = " WHERE";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL") 
            {
                if ($key == 'ID') {
                    $cond = $cond." ID = $value AND";
                } elseif ($key == 'CompanyName') {
                    $cond = $cond." ( CompanyName LIKE '%$value%' OR CompanyName2 LIKE '%$value%')AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." Active = 1 AND"; } 
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";
        
        
        $strSQL  = "SELECT *, tb_Company.ID, tb_Company.ChangeLog, tb_Company.Note, tb_Company.Active, "

                . " (SELECT CONCAT(Nickname, ' (', ChineseName, ')') FROM tb_CompanyUser "
                . " WHERE tb_CompanyUser.ID = BillToContactID) AS BillToContact, "
                . " (SELECT CONCAT(Address1, ', ', Address2, ', ', Address3, ', ', City, ', ', County, ', ', Country, ' ', PostalCode) "
                . " FROM tb_CompanyAddress WHERE tb_CompanyAddress.ID = BillToAddressID) AS BillToAddress,"

                . " (SELECT CONCAT(Nickname, ' (', ChineseName, ') ') FROM tb_CompanyUser "
                . " WHERE tb_CompanyUser.ID = ShipToContactID) AS ShipToContact, "
                . " (SELECT CONCAT(Address1, ', ', Address2, ', ', Address3, ', ', City, ', ', County, ', ', Country, ' ', PostalCode) "
                . " FROM tb_CompanyAddress WHERE tb_CompanyAddress.ID = ShipToAddressID) AS ShipToAddress "
                
                . " FROM tb_Company $cond $order_by";
     
        $strSQL1 = "SELECT * FROM tb_Company $cond";     
        
        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_Company";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    function company_searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['CompanyCode']    = 'Company Code';
        $searchby_options['CompanyName']    = 'Company Name';
        return $searchby_options;
    }

    function company_list() {
        $company_list = array();
        $company_list['NULL'] = '';
        $strSQL = "SELECT ID, CompanyCode FROM tb_Company WHERE Active=1 ORDER BY CompanyCode asc";    
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $company_list[$row->ID] = $row->CompanyCode;
        }
        return $company_list;
    }
    
    function disti_list() {
        $company_list = array();
        $strSQL = "SELECT ID, CompanyCode FROM tb_Company WHERE IsDisti=1 AND Active=1 ORDER BY CompanyCode asc";    
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $company_list[$row->ID] = $row->CompanyCode;
        }
        return $company_list;
    }
    
    function entity_list() {
        $company_list = array();
        $company_list['LK-HK'] = 'LK-HK';
        $company_list['LK-TW'] = 'LK-TW';
        $company_list['ATM']   = 'ATM';
        return $company_list;
    }
    
    function update_company($id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_Company')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        // append ChangeLog in the front
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $data['ChangeLog'] = $change_log;        
        
        $this->db->where('ID', $id);
        $this->db->update('tb_Company', $data);
    }
    
    function add_company($data) {
        $this->load->helper('date');
        
        // check if PartNumber already exists
        $company_code = $data['CompanyCode'];
        $strSQL = "SELECT ID, COUNT(*) as count FROM tb_Company WHERE CompanyCode LIKE '$company_code'";
        $result = $this->db->query($strSQL);
        if (($result->row()->count) != '0') {
            return (-1);                             // return 1 if CompanyCode exists
        }

        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_Company', $data);
        $this->db->trans_complete();
        $company_id =$this->db->insert_id();
           
        return ($company_id);             // added successfully
    }
    
    function address_query($limit, $sort_by, $sort_order,$query, $offset, $option) {
        $cond = " WHERE";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL") 
            {
                if ($key == 'ID') {
                    $cond = $cond." ID = $value AND";
                } elseif ($key == 'Address') {
                    $cond = $cond." ( Address1 LIKE '%$value%' OR Address2 LIKE '%$value%' OR Address3 LIKE '%$value%') AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." Active = 1 AND"; } 
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";
        
        $strSQL  = "SELECT * FROM tb_CompanyAddress $cond $order_by";
        $strSQL1 = "SELECT * FROM tb_CompanyAddress $cond";     

        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_CompanyAddress";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    function address_searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']       = '';
        $searchby_options['Address']    = 'Address';     // any address (address1,address2,adress3)
        $searchby_options['City']       = 'City';
        $searchby_options['Country']    = 'Country';
        $searchby_options['PostalCode'] = 'Postal Code';
        return $searchby_options;
    }
    
    function update_address($id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_CompanyAddress')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        // append ChangeLog in the front
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $data['ChangeLog'] = $change_log;        
        
        $this->db->where('id', $id);
        $this->db->update('tb_CompanyAddress', $data);
    }
    
    function add_address($data) {
        $this->load->helper('date');

        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_CompanyAddress', $data);
        $this->db->trans_complete();
        $address_id =$this->db->insert_id();
        return ($address_id);             // added successfully
    }

    function address_list() {
        $address_list = array();
        $address_list['NULL'] = '';
        $strSQL = "SELECT ID, Address1, Address2, Address3, City FROM tb_CompanyAddress WHERE Hidden=0 ORDER BY Address1 asc";    
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $address_list[$row->ID] = $row->Address1." ".$row->Address2." ".$row->Address3." ".$row->City;
        }
        return $address_list;
    }
    
    function user_query($limit, $sort_by, $sort_order,$query, $offset, $option) {
        $cond = " WHERE";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL") 
            {
                if ($key == 'ID') {
                    $cond = $cond." tb_CompanyUser.ID = $value AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." tb_CompanyUser.Active = 1 AND"; } 
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";

        $strSQL  = "SELECT *, tb_CompanyUser.ID, tb_CompanyUser.ChangeLog, tb_CompanyUser.Note, tb_CompanyUser.Active, "
                    . "concat(Address1, ' ', Address2, ' ', Address3, ' ', City, ', ',Country) as CombinedAddress  "
                    . "FROM tb_CompanyUser "
                    . "LEFT JOIN tb_Company ON tb_Company.ID=tb_CompanyUser.CompanyID " 
                    . "LEFT JOIN tb_CompanyAddress ON tb_CompanyAddress.ID=tb_CompanyUser.AddressID $cond $order_by";

        $strSQL1  = "SELECT *, tb_CompanyUser.ID, tb_CompanyUser.ChangeLog, tb_CompanyUser.Note, tb_CompanyUser.Active, "
                    . "concat(Address1, ' ', Address2, ' ', Address3, ' ', City, ', ',Country) as CombinedAddress  "
                    . "FROM tb_CompanyUser "
                    . "LEFT JOIN tb_Company ON tb_Company.ID=tb_CompanyUser.CompanyID " 
                    . "LEFT JOIN tb_CompanyAddress ON tb_CompanyAddress.ID=tb_CompanyUser.AddressID $cond";
     
        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_CompanyUser";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    function user_searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['CompanyCode']    = 'Company Code';
        $searchby_options['Nickname']       = 'Name';
        $searchby_options['ChineseName']    = '中文名';       // str-replace, remove non standard A-Z
        $searchby_options['Phone']          = 'Phone';
        $searchby_options['Mobile']         = 'Mobile';
        return $searchby_options;
    }
    
    function update_user($id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_CompanyUser')
                ->where('ID', $id)
                ->limit(1);
        
        foreach ($q->get()->result() as $row)
        {
            $change_log = $row->ChangeLog;               //get original ChangeLog
        }
 
        // append ChangeLog in the front
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $data['ChangeLog'] = $change_log;        
        $this->db->where('ID', $id);
        $this->db->update('tb_CompanyUser', $data);
    }
    
    function add_user($data) {
        $this->load->helper('date');

        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_CompanyUser', $data);
           
        return (0);             // added successfully
    }
    
    function contact_list() {
        $contact_list = array();
        $contact_list['NULL'] = '';
        $strSQL = "SELECT tb_CompanyUser.ID, Nickname, ChineseName FROM tb_CompanyUser "
                . " WHERE Active=1 ORDER BY Nickname asc";    
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $contact_list[$row->ID] = $row->Nickname." (".$row->ChineseName.") ";
        }
        return $contact_list;
    }
    
    function sample_email_list($priv, $disti_id) {
        $email_list = array();
        if ($disti_id == 0) {
            $strSQL = "SELECT Email, Nickname FROM tb_CompanyUser "
                    . "WHERE Privilege LIKE '%$priv%' ORDER BY Nickname asc";        
        } else {
            $strSQL = "SELECT Email, Nickname FROM tb_CompanyUser "
                    . "WHERE CompanyID LIKE $disti_id AND Privilege LIKE '%$priv%' ORDER BY Nickname asc";    
        }
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $email_list[] = $row->Email;
        }
        return $email_list;
    }
    function contact_address_list_DELETING() {
        $contact_list = array();
        $contact_list['NULL'] = '';
        $strSQL = "SELECT tb_CompanyUser.ID, Nickname, Address1, Address2, Address3, City FROM tb_CompanyUser "
                . " LEFT JOIN tb_CompanyAddress ON tb_CompanyAddress.ID = tb_CompanyUser.AddressID "
                . " WHERE Active=1 ORDER BY Nickname asc";    
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $contact_list[$row->ID] = $row->Nickname." ".$row->Address1." ".$row->Address2." ".$row->Address3." ".$row->City;
        }
        return $contact_list;
    }
    
}
?>