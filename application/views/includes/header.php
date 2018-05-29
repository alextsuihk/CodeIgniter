
</head>
<body>

<?php if (!empty($main_top)): $this->load->view($main_top); endif;?>
    
<div id="hintbox"></div>


<a name="Header"></a>
<table class="header">
    <tr>
        <td width="25%">
            <button title="Return to Main Menu" type="button" onclick="location.href='<?php echo base_url();?>'">
                <img src="<?php echo site_url('img/home.png'); ?>"/></button>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button title="Refresh this Page" type="button" onclick="window.location.reload();">
                <img src="<?php echo site_url('img/refresh.png'); ?>"/></button>
            &nbsp;&nbsp;&nbsp;&nbsp; 
            <button title="Open a new Tab" type="button" onclick="window.open('<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>')">
                <img src="<?php echo site_url('img/new-tab.png'); ?>"/></button>
            <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
                &nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url('img/changelog.png'); ?>"/>
            <?php endif; ?>
                
            <!--<a href="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" target="_blank">Click Me!</a>-->
        </td>
        <td width="50%"><div style="color:blue; font-size:24px;"><?php echo $title; ?></div></td>
        
        <td width="25%" align="right"><img src="<?php echo base_url();?>img/login-logo.png" alt='Leahkinn Logo' height='20' width='100'>
            <br><?php echo $this->session->userdata['nickname']." @ ".$this->session->userdata['company_code'];?>
        </td>
    </tr>
    <tr>
        <td>Last Update: &nbsp;&nbsp; <?php date_default_timezone_set("Asia/Taipei"); echo date("Y-m-d")."&nbsp;&nbsp;&nbsp;".date("H:i"); ?></td>
        <td><div class="SlowBlink" style="color:red; display:block"><b><?php echo $message; ?></b></td>
        <td width="25%"><button onclick="location.href='<?php echo base_url();?>logout'">Logout</button> &nbsp;
        <?php if ($this->session->userdata['passwd_expiry'] < date("Y-m-d", strtotime('+17 day'))):  ?>
          <div style="color:red" class="SlowBlink"><button title="Expire Soon" onclick="location.href='<?php echo base_url();?>change_passwd'">Change Password</button></div>
        <?php else: ?>
          <button onclick="location.href='<?php echo base_url();?>change_passwd'">Change Password</button>
        <?php endif; ?></td>
    </tr>
</table>
<br>

<a name="Top"></a>
<!--
<table class="TitleBox">
    <tr><td><center><h3><?php echo $title; ?></h3></center></td></tr>
</table>
<br>
-->

<table class="FavBox">
    <tr><td><div align ="right">
        <?php if ($favorite): ?><?php foreach ($favorite as $key=>$value): ?>
        <button style="background: pink;" onclick="location.href='<?php echo base_url().$value;?>'"><small><?php echo $key;?></small></button>
        <?php endforeach; ?><?php endif; ?>
        <?php if ($title != "Main Menu" && $download_url !="NULL"): ?>
        <button title="Download as Excel" type="button" name="download" onclick="location.href='<?php echo base_url().$download_url."?sb=$sort_by&so=$sort_order&query=$query_url&option=$option";?>'">
            <small>Download</small></button>
        <?php endif; ?>
    </div></td></tr>
    <?php if (isset($tab)): ?><tr><td><div align="right"><?php foreach ($tab as $key=>$value): ?>
        <?php if (strpos($_SERVER['REQUEST_URI'], $value) !==false) : ?> 
                <button  style="background: red;" onclick="location.href='<?php echo base_url().$value;?>'"><small><b><?php echo $key;?><b></small></button>
        <?php else: ?>
            <button  style="background: yellow;" onclick="location.href='<?php echo base_url().$value;?>'"><small><?php echo $key;?></small></button>
        <?php endif; ?>

    <?php endforeach; ?></div></td></tr><?php endif; ?>

</table>
<br>
