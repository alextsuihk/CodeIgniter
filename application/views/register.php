<?php if ( ($action == 'add' || $action =='edit') && (strstr($this->session->userdata['priv'], 'REG1') ) ): ?>
<?php echo form_open('register/edit'); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  
<?php echo form_hidden('id', $id); ?>  
<?php echo form_hidden('disti_id', $disti_id); ?>  

<table class="EditBox">
    <tr><td colspan="4"><center>Customer Registration</center></td></tr>
    <tr>
        <?php if (strstr($this->session->userdata['priv'], 'REG12')): ?>
            <td colspan="2">Disti: <?php echo form_dropdown("disti_id", $disti_list, $edit['0']['DistiID'], 'id="disti_id"'); ?></td>
        <?php else: ?>
            <td colspan="2">Disti: <?php echo $disti_list[$disti_id]; ?></td>
        <?php endif; ?>
        <td colspan="2">客人： <?php echo form_input('customer', $edit['0']['Customer'], array('maxlength'=>'32', 'size'=>'32')); ?> </td>  
    </tr>
</table>

<table class="EditBox">
    <tr>
        <td><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
        <td><div align="center">
            <button title="<?php echo $submit_button;?>" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
    </tr>
</table>
<?php echo form_close(); ?>

<?php else: ?>

<table class="EditBox">
    <?php echo form_open('register/search'); ?>
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <tr>
        <th width="40%" rowspan="2">
            <?php $i=0; ?>
            <div align="left"> &nbsp;&nbsp;&nbsp;&nbsp; Search By:
            &nbsp;&nbsp;&nbsp;<button title="Add more Search Criteria" type="submit" name="submit" value=add:'.$i.'>+</button> </div>
            <?php foreach ($query as $key=>$value): ?>
            <?php echo form_dropdown("searchby[$i]", $searchby_options, $key, "id=\"searchby[$i]\"") ?>
            <?php echo form_input("search_value[$i]", ($key=='ID')?'':$value, array('maxlength'=>'32', 'size'=>'25')); ?>
            <button title="Remove this Criteria" type="submit" name="submit" value=delete:'.$i.'>-</button>
            <br>
            <?php $i++; ?>
            <?php endforeach; ?>
        </th>
        <th width="53%" rowspan="2"><div align="left"> </div></th>
        <th width="7%">
            <?php if ($query != ''):?>
                <button title="Clear All Query" type="button" name="clear" onclick="location.href='<?php echo base_url().$cancel_url;?>'">Clear</button>
            <?php endif; ?>
        </th>
    </tr>  
    <tr><th>
            <button title="Query" type="submit" name="submit" value=search:'.$i.'>Search</button>
    </th></tr>
    <?php echo form_close(); ?>
</table>
<?php endif; ?>

<br>
<div>
    Matched Records: <?php echo $matched_records; ?> of <?php echo $total_records; ?>
</div>
      
<table class="ListBox">
    <thead>
        <th width="15%"<?php if ($sort_by == "RequestDate") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("register?action=$action&sb=RequestDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "申请日"); ?></th>
        
        <th width="20%"<?php if ($sort_by == "Customer") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("register?action=$action&sb=Customer&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "客人"); ?></th>
        
        <th width="25%"<?php if ($sort_by == "CompanyCode") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("register?action=$action&sb=CompanyCode&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "代理"); ?></th>
        
        <th width="25%">Status</th>
        <th width="15%">
           <button title="注册客人" type="button" onclick="window.open('<?php echo base_url();?>register?action=add')">
             <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
        </th>
    </thead>

    <tbody>         
        <?php foreach($customers as $i=>$customer): ?>
        <tr>
            <?php switch ($customer['StatusID']) {
                case "0": $status="Requesting"; break;
                case "1": $status="Approved"; break;
                case "2": $status="Rejected"; break;
            } ?>
            <?php $approve_date = ($customer['ApproveDate'] == "1900-01-01")?"":$customer['ApproveDate']; ?>
            
            <td><div align="center"><?php echo $customer['RequestDate']; ?></div></td>
            <td><div align="left"><?php echo "&nbsp;&nbsp;&nbsp;".$customer['Customer']; ?></div></td>
            <td><div align="center"><?php echo $customer['CompanyCode']; ?></div></td>
            <td><div align="center"><?php echo $approve_date."&nbsp;&nbsp;&nbsp;&nbsp;".$status; ?></div></td>

            <td><div align="center">
            <?php if (strstr($this->session->userdata['priv'], 'REG12')) : ?>
                <?php if ($customer['StatusID'] !=1 ) : ?>
                    <button title="Approve" data-toggle="modal" data-target="#modalApprove" class="open-Dialog" 
                    data-id="<?php echo $customer['ID'];?>" data-title="Approve" data-customer="<?php echo $customer['Customer'];?>" 
                    data-disti="<?php echo $customer['CompanyCode'];?>" ><img src="<?php echo site_url('img/approve.png'); ?>"</button>
                <?php else: ?>
                    <button title="Reject" data-toggle="modal" data-target="#modalReject" class="open-Dialog" 
                    data-id="<?php echo $customer['ID'];?>" data-title="Reject" data-customer="<?php echo $customer['Customer'];?>" 
                    data-disti="<?php echo $customer['CompanyCode'];?>" ><img src="<?php echo site_url('img/reject.png'); ?>"</button>
                <?php endif; ?>
            <?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>


</table>
<br>


<?php if (strlen($pagination)): ?>
<div>
    Pages:  <?php echo $pagination; ?>
</div>
<br>
<?php endif; ?>

<table class="NoteBox">
    <tbody>
        <tr><td><div align="left"><small>
        Note: <br>
              
        </small></div></td></tr>
    </tbody>
</table>

<!-- approve -->
<!--Pop Up Dialog-->
<div class="modal fade" id="modalApprove" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <form action="register/process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <?php echo form_hidden('query', $query); ?>
    <?php echo form_hidden('offset', $offset); ?> 
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true">    </span></button>
        <h4 class="modal-title custom_align" id="Heading"><input style="border: none; background: transparent; text-align:center" id="title_approve" disabled ></h4>
    </div>
    <div class="modal-body">
        <input type="hidden" name="id" id='id' />
        <input style="border: none; background: transparent; text-align:center" id="disti" disabled />
        成功注册
        <input style="border: none; background: transparent; text-align:center" id="customer" disabled />
        
    </div>
    <div class="modal-footer ">
        <button type="submit" id="btnYES" name="submit" class="btn btn-success" value="approved" ><span class="glyphicon glyphicon-ok-sign"></span> 提交</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> 取消</button>
    </div>
    </form>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>

<!-- Reject  -->
<!--Pop Up Dialog-->
<div class="modal fade" id="modalReject" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <form action="register/process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <?php echo form_hidden('query', $query); ?>
    <?php echo form_hidden('offset', $offset); ?> 
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true">    </span></button>
        <h4 class="modal-title custom_align" id="Heading"><input style="border: none; background: transparent; text-align:center" id="title_reject" disabled ></h4>
    </div>
    <div class="modal-body">
        <input type="hidden" name="id" id='id' />
        否决
        <input style="border: none; background: transparent; text-align:center" id="disti" disabled />
        注册
        <input style="border: none; background: transparent; text-align:center" id="customer" disabled />
    </div>
    <div class="modal-footer ">
      <button type="submit" id="btnNO" name="submit" class="btn btn-danger" value="rejected"><span class="glyphicon glyphicon-ok-sign"></span> 拒绝</button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> 取消</button>
    </div>
    </form>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>