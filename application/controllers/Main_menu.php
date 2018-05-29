<?php

class Main_Menu extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        
        $required_priv = '';                        // no priv required
        $this->load->library('privilege');
        $this->privilege->permission($required_priv);
    }
    
    function index()
    {
        $priv = $this->session->userdata['priv'];
        
        $button = array();                          // generate menu based on privileges
        if (strstr($priv, 'ADM')) {
            if (!isset($this->session->userdata['hide_menu_admin'])) { $this->session->userdata['hide_menu_admin'] = TRUE;}
            if ($this->session->userdata['hide_menu_admin'] == TRUE) {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/admin\'">Show Menu</button>';
            } else {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/admin\'">Hide Menu</button>';
                if (!isset($this->session->userdata['view_changelog'])) { $this->session->userdata['view_changelog'] = FALSE;}
                if ($this->session->userdata['view_changelog'] == TRUE) {
                    $button[] = '<button onclick="location.href=\'./main_menu/toggle_view_changelog\'">Hide ChangeLog</button>';
                } else {
                    $button[] = '<button onclick="location.href=\'./main_menu/toggle_view_changelog\'">Show ChangeLog</button>';
                }
                $button[] = '<button onclick="location.href=\'./admin/term/PaymentTerms\'">Payment Terms<br>[Beta]</button>';
                $button[] = '<button onclick="location.href=\'./admin/term/ShipmentTerms\'">Shipment Terms<br>[Beta]</button>';
                $button[] = '<button onclick="location.href=\'./admin/favorite\'">Favorite<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./admin/update_seq_number\'">Update<br>Sequence Number</button>';
            }
            $filling_req = ceil( (count($button)-1)/6 )*6 - (count($button)-1);         // must be multiple of 5,
            for($i = 0; $i < $filling_req; $i++) { $button[] = ''; }
            $data['button_adm'] = $button;
        }

        $button = array();                          // generate menu based on privileges
        if (strstr($priv, 'CRM')) { 
            if (!isset($this->session->userdata['hide_menu_crm'])) { $this->session->userdata['hide_menu_crm'] = TRUE;}
            if ($this->session->userdata['hide_menu_crm'] == TRUE) {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/crm\'">Show Menu</button>';
            } else {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/crm\'">Hide Menu</button>';
                $button[] = '<button onclick="location.href=\'./crm/company\'">Manage<br>Company</button>';
                $button[] = '<button onclick="location.href=\'./crm/address\'">Manage<br>Address</button>';
                $button[] = '<button onclick="location.href=\'./crm/user\'">Manage<br>User</button>';
            }
            $filling_req = ceil( (count($button)-1)/6 )*6 - (count($button)-1);         // must be multiple of 5,
            for($i = 0; $i < $filling_req; $i++) { $button[] = ''; }
            $data['button_crm'] = $button;
        }

        $button = array();                          // generate menu based on privileges
        if (strstr($priv, 'PRO') || strstr($priv, 'BLG') || strstr($priv, 'IPN') || strstr($priv, 'DSO') || strstr($priv, 'FGI')) { 
            if (!isset($this->session->userdata['hide_menu_pro'])) { $this->session->userdata['hide_menu_pro'] = FALSE;}
            if ($this->session->userdata['hide_menu_pro'] == TRUE) {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/pro\'">Show Menu</button>';
            } else {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/pro\'">Hide Menu</button>';
                $button[] = '<button onclick="location.href=\'./product\'">Product<br>Part Number</button>';
                if (strstr($priv, 'IPN')) {
                    $button[] = '<button onclick="location.href=\'./product_ipn\'">IPN</button>';
                }
                $button[] = '<button onclick="location.href=\'./product_listprice\'">Product<br>List Price</button>';
                if (strstr($priv, 'BLG')) {
                    $button[] = '<button onclick="location.href=\'./backlog\'">BackLog<br>[Beta]</button>';
                }
                if (strstr($priv, 'DSO')) {
                    $button[] = '<button onclick="location.href=\'./???\'">Disti Sales Out<br>[TBD]</button>';
                }
                if (strstr($priv, 'FGI')) {
                    $button[] = '<button onclick="location.href=\'./???\'">Inventory<br>[TBD]</button>';
                }
            }
            $filling_req = ceil( (count($button)-1)/6 )*6 - (count($button)-1);         // must be multiple of 5,
            for($i = 0; $i < $filling_req; $i++) { $button[] = ''; }
            $data['button_pro'] = $button;
        }        

        $button = array();                          // generate menu based on privileges
        if (strstr($priv, 'MAN') || strstr($priv, 'WAF')) { 
            if (!isset($this->session->userdata['hide_menu_man'])) { $this->session->userdata['hide_menu_man'] = FALSE;}
            if ($this->session->userdata['hide_menu_man'] == TRUE) {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/man\'">Show Menu</button>';
            } else {
                if (strstr($priv, 'MAN1')|| strstr($priv, 'MAN2')) { 
                    $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/man\'">Hide Menu</button>';
                    $button[] = '<button onclick="location.href=\'./wafer\'">Wafer<br>Part Number<br>[beta]</button>';
                    $button[] = '<button onclick="location.href=\'./wafer/substrate\'">Substrate Inventory<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./wafer/wafer_lot\'">Wafer Mapping<br>by Excel or Manual<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./wafer/inventory\'">Wafer Inventory<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./wafer/???\'">Check Mapping<br>Integrity<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./wafer/???\'">Split<br>Wafer Lot<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./work\'">Work Order<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./work\'">Estimate<br>output plan<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./work\'">Work Order<br>Output<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'.inventory/???\'">FG Inventory<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'.mapping/download\'">Download Mapping<br> WO<br>[TBD]</button>';
                    $button[] = '<button onclick="location.href=\'./???\'">WIP<br>[TBD]</button>';
                }                                     // domex only see this menu
                $button[] = '<button onclick="location.href=\'./wafer/carton\'">Carton Box<br>[TBD]</button>';
                
            }
            $filling_req = ceil( (count($button)-1)/6 )*6 - (count($button)-1);         // must be multiple of 5,
            for($i = 0; $i < $filling_req; $i++) { $button[] = ''; }
            $data['button_man'] = $button;
        }      
        
        $button = array();                          // generate menu based on privileges
        if (strstr($priv, 'PUR')) { 
            if (!isset($this->session->userdata['hide_menu_pur'])) { $this->session->userdata['hide_menu_pur'] = FALSE;}
            if ($this->session->userdata['hide_menu_pur'] == TRUE) {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/pur\'">Show Menu</button>';
            } else {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/pur\'">Hide Menu</button>';
                $button[] = '<button onclick="location.href=\'./purchase?entity=LK-HK\'">Purchase Order<br>HK<br>[Beta]</button>';
                $button[] = '<button onclick="location.href=\'./purchase?entity=LK-TW\'">Purchase Order<br>TW<br>[Beta]</button>';
                $button[] = '<button onclick="location.href=\'./purchase/receiving\'">Receiving<br>[TBD]</button>';
                if (strstr($priv, 'AC')) { 
                    $button[] = '<button onclick="location.href=\'./account/payable\'">Account Payable<br>[TBD]</button>';
                }
                $button[] = '<button onclick="location.href=\'./???\'">Inventory<br>[TBD]</button>';
            }
            $filling_req = ceil( (count($button)-1)/6 )*6 - (count($button)-1);         // must be multiple of 5,
            for($i = 0; $i < $filling_req; $i++) { $button[] = ''; }
            $data['button_pur'] = $button;
        }

        $button = array();                          // generate menu based on privileges
        if (strstr($priv, 'SAL')) { 
            if (!isset($this->session->userdata['hide_menu_sal'])) { $this->session->userdata['hide_menu_sal'] = FALSE;}
            if ($this->session->userdata['hide_menu_sal'] == TRUE) {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/sal\'">Show Menu</button>';
            } else {
                $button[] = '<button onclick="location.href=\'./main_menu/toggle_menu/sal\'">Hide Menu</button>';
                $button[] = '<button onclick="location.href=\'./salesout?entity="3set"\'">List Sales Out<br>(TW-Nubes-HK)<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./salesout?entity="LK-HK"\'">List Sales Out<br>(Leahkinn HK)<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./salesout?entity="LK-TW"\'">List Sales Out<br>(Leahkinn TW)<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./salesout?entity="NUBES"\'">List Sales Out<br>(Nubes TW)<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./salesout/release\'">Release SalesOut<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./salesout/packing\'">Packing List<br>[TBD]</button>';
                $button[] = '<button onclick="location.href=\'./salesout/shipped\'">shipped<br>[TBD]</button>';
                if (strstr($priv, 'AC')) { 
                    $button[] = '<button onclick="location.href=\'./account/payable\'">Account Payable<br>[TBD]</button>';
                }
                $button[] = '<button onclick="location.href=\'./???\'">Inventory<br>[TBD]</button>';
            }
            $filling_req = ceil( (count($button)-1)/6 )*6 - (count($button)-1);         // must be multiple of 5,
            for($i = 0; $i < $filling_req; $i++) { $button[] = ''; }
            $data['button_sal'] = $button;
        }
        
        $data['title'] = "Main Menu";
        $data['message']= $this->session->flashdata('message');  
        $data['main_content'] = 'main_menu';
        $data['favorite'] = $this->session->userdata('favorite');
        $data['html_title'] = 'Main Menu';
        $this->load->view('includes/template',$data);
    }
    
    function toggle_menu($menu)
    {
        $key = 'hide_menu_'.$menu;
        if ($this->session->userdata[$key] == TRUE) {
            $this->session->userdata[$key] = FALSE;  
        } else {
            $this->session->userdata[$key] = TRUE;
        }
        redirect('.');
    }

    function toggle_view_changelog()
    {
        $key = 'view_changelog';
        if ($this->session->userdata[$key] == TRUE) {
            $this->session->userdata[$key] = FALSE;  
        } else {
            $this->session->userdata[$key] = TRUE;
        }
        redirect('.');
    }

}
?>