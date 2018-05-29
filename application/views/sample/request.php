<?php if ( ($action == 'request_add')  && (strstr($this->session->userdata['priv'], 'SAM1')) ): ?> 
<?php echo form_open('sample/request_add'); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  


<table class="EditBox">
    <tr><td colspan="6"><center>样品申请</center></td></tr>
    <tr>
        <td colspan="2">申请人：<input style="max-width:100px;" type="text" name="Requester" value="<?php echo $requester;?>" readonly="true"/></td>
        <td colspan="2">Project Name: <input type="text" name="project" maxlength="32"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">客人：<?php echo form_dropdown("final_customer_id", $customer_list, "", 'id="final_customerid"') ?></td>
        <td colspan="2">料号：<?php echo form_dropdown("product_id", $product_list, "" , 'id="product_id"') ?></td>
        <td colspan="2">申请数量：<input style="max-width:50px;" type="number" name="request_qty" id="request_qty" value="5" step="any" min="1" max="50"/></td>   
    </tr>
    <tr><td colspan="6">
        <b>Comment: (可填)</b><br>
        <?php echo form_textarea(array('name'=>'comment', 'id'=>'comment', 'value'=>"", 'cols'=>'40', 'rows'=>'3')); ?>
    </td></tr>
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

<?php elseif ( ($action == 'request_add2')  && (strstr($this->session->userdata['priv'], 'SAM1')) ): ?> 
<?php echo form_open('sample/request_add2'); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  
<?php echo form_hidden('final_customer_id', $final_customer_id); ?>
<?php echo form_hidden('project', $project); ?>
<?php echo form_hidden('product_id', $product_id); ?>
<?php echo form_hidden('part_number', $part_number); ?>
<?php echo form_hidden('requester_id', $requester_id); ?>
<?php echo form_hidden('request_date', $request_date); ?>
<?php echo form_hidden('request_time', $request_time); ?>
<?php echo form_hidden('request_qty', $request_qty); ?>
<?php echo form_hidden('comment', $comment); ?>

<table class="EditBox">
    <tr><td colspan="6"><center>样品申请</center></td></tr>
    <tr>
        <td colspan="2">申请人：<?php echo $requester; ?></td>
        <td colspan="2">Project Name: <?php echo $project; ?></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">客人：<?php echo $final_customer; ?></td>
        <td colspan="2">料号：<?php echo $part_number ?></td>
        <td colspan="2">申请数量：<?php echo $request_qty ?></td>   
    </tr>
    <tr><td colspan="6">
        <b>Comment:</b><br><textarea readonly><?php echo $comment; ?></textarea></td></tr>
    </td></tr>
    
    <tr><td colspan="6">
        <BR>送货地址：  <?php echo form_dropdown("recipient_id", $recipient_list, "", 'id="recipient_id"') ?>
    </td></tr>

</table>

<table class="EditBox">
    <tr>
        <td colspan="1"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
        <td colspan="1"><div align="center">
            <button title="<?php echo $submit_button;?>" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
    </tr>

</table>
<?php echo form_close(); ?>


<?php elseif (($action == 'request_view') || ($action=='request_addcomment')): ?>
<?php if ($action=='request_addcomment'): ?>
<?php echo form_open('sample/request_addcomment'); ?>
<?php echo form_hidden('ID', $id); ?>
<?php endif; ?>
<table class="ViewBox">
    <tr>
        <td colspan="15"><center><big><b>申请状况: 
            <?php if ($edit[0]['Status'] ==0 || $edit[0]['Status'] >=99): ?>
            <div style="color:red" class="SlowBlink"><?php echo $status[$edit[0]['Status']]; ?></div>
            <?php else: ?>
               <?php echo $status[$edit[0]['Status']]; ?>
            <?php endif; ?>
            </big></center></b></td>
    </tr>

    <tr>
        <td colspan="2"><b>申请明细: </b></td>
        <td colspan="2">代理商: <br><?php echo $disti_list[$edit[0]['DistiID']]; ?></td>
        <td colspan="2">申请人: <br><?php echo $edit[0]['Requester']; ?></td>
        <td colspan="3">申请时间: <br><?php echo $edit[0]['RequestDate']." ".$edit[0]['RequestTime']; ?></td>
        <td colspan="4">Part Number: <br><?php echo $edit[0]['PartNumber']; ?></td> 
        <td colspan="2">申请数量: <br><?php echo $edit[0]['RequestQty']; ?></td>
    </tr>
    
    <tr>
        <td colspan="2"></td>
        <td colspan="6">客人：<BR><?php echo $edit[0]['FinalCustomer']; ?></td>
        <td colspan="7">项目名称：<BR><?php echo $edit[0]['Project']; ?>  </td>
    </td></tr>

    <tr><td colspan="15">
           送货地址：<BR><?php echo $edit[0]['Recipient']; ?>
    </td></tr>
    <?php if ($action=='request_addcomment'): ?>
        <tr><td colspan="15">
            <b>Enter your Comment:</b><br>
            <?php echo form_textarea(array('name'=>'newcomment', 'id'=>'newcomment', 'value'=>"", 'cols'=>'40', 'rows'=>'3')); ?>
        </td></tr>
    <?php endif; ?>
    <?php if (($edit[0]['Status'] >=100)): ?>
        <tr><td colspan="15"><?php echo $edit[0]['Approver']." ".$edit[0]['ApproveDate']." ".$edit[0]['ApproveTime'].
                "<BR> 不Approve原因: ".$status[$edit[0]['Status']],"<BR><BR>"; ?></td><tr>
    <?php else: ?>

       <?php if ($edit[0]['RequesterID'] != $edit[0]['PmApproverID']): ?>
            <tr><td colspan="15"><center>
                <?php if (($edit[0]['Status'] >=2)): ?>
                    <BR>PM: <?php echo $edit[0]['PmApprover']." ".$edit[0]['PmApproveDate']." ".$edit[0]['PmApproveTime']; ?> 已 Approved <BR><BR>
                <?php else: ?>
                    <div style="color:red" class="SlowBlink"><BR>请通知 PM Approve<BR><BR></div>
                <?php endif; ?>  
            </center></td></tr>
        <?php endif; ?>
            
        <?php if ($edit[0]['Status'] >=3): ?>
        <tr>
            <td colspan="2"><b>批准明细: </b></td>
            <td colspan="4">批准人: <br> <?php echo $edit[0]['Approver']; ?></td>
            <td colspan="5">批准时间: <br><?php echo $edit[0]['ApproveDate']." ".$edit[0]['ApproveTime']; ?></td>
            <td colspan="4">批准数量: <br><?php echo $edit[0]['ApproveQty']; ?></td>
        </tr>
        <?php endif; ?>

        <?php if ($edit[0]['Status'] ==4): ?>
        <tr><td colspan="15" style="text-align:center">
            <BR><?php echo $edit[0]['DockDate']." ".$edit[0]['DockTime']." ".$edit[0]['Docker']."安排寄送"; ?><BR><BR>
        </td></tr>
        <?php endif; ?>
        
        <?php if ($edit[0]['Status'] ==5): ?>
        <tr><td colspan="15" style="text-align:center"><BR>
            <?php echo $edit[0]['ShipDate']." ".$edit[0]['Shipper']."已寄送 ".$edit[0]['ShipQty']." ".$edit[0]['PartNumber']; ?>
            <?php echo $edit[0]['Courier']." ".$edit[0]['AWB']; ?><BR><BR>
        </td></tr>
        
        <?php endif; ?>
    <?php endif; ?>
        
    <?php foreach ($comments as $comment): ?>
        <tr><td colspan="15"><?php echo "<BR>Comment:  ".$comment['TimeStamp']," by ".$comment['Commenter']; ?><br>
                <textarea readonly><?php echo $comment['Comment']; ?></textarea></td></tr>
    <?php endforeach; ?>
        
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='15'><div align='left'><small><b>ChangeLot:</b><br><?php echo $edit[0]['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
</table>

<?php if ($action=='request_addcomment'): ?>
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
    form_close()
<?php else: ?>
    <table class="ViewBox">
        <tr><td colspan="15"><div align="center">
            <input title="Close this Window" type="button" value="CLOSE" onclick="location.href='<?php echo base_url()."sample?action=request_list";?>'">
        </div></td></tr>    </tr>
    </table>
<?php endif;?>

<?php else: ?>

<table class="EditBox">
    <?php echo form_open('sample/request_search'); ?>
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
        <th width="53%" rowspan="2"><div align="left">
                <!--
            <?php $show_team = ((strpos($option, 'showall') !== FALSE))?TRUE:FALSE; ?>
            <?php if (strstr($this->session->userdata['priv'], 'SAM12')): ?>
               <?php echo form_checkbox('show_allm', '1', $show_team); ?> Show All Disti Request
               <BR>
            <?php endif; ?>
            <?php $show_team = ((strpos($option, 'showteam') !== FALSE))?TRUE:FALSE; ?>
            <?php if (strstr($this->session->userdata['priv'], 'SAM11')): ?>
               <?php echo form_checkbox('show_team', '1', $show_team); ?> Show My Team Request
               <BR> 
            <?php endif; ?>
                -->

            <?php $hide_cancel = ((strpos($option, 'hidecancel') !== FALSE))?TRUE:FALSE; ?>
            <?php echo form_checkbox('hide_cancel', '1', $hide_cancel); ?> Hide Canceled & Reject Request
            <BR>
            <?php $hide_completed = (strpos($option, 'hidecompleted') !== FALSE)?TRUE:FALSE; ?>
            <?php echo form_checkbox('hide_completed', '1', $hide_completed); ?> Hide Shipped Request
        </div></th>
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
        <th width="7%"><center>状态</center></th>   
        <th width="13%"<?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Part Number"); ?></th>
        
        <th width="12%"<?php if ($sort_by == "Customer") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=Customer&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "最终客人/项目"); ?></th>
        
        <th width="7%"<?php if ($sort_by == "RequestDate") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=RequestDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "申请日期"); ?></th>
        
        <th width="7%"><center>申请人</center></th>
        <th width="4%"><center>申请<BR>数量</center></th>
        <th width="7%"><center>批准日期</center></th>
        <th width="7%"><center>审批人</center></th>
        <th width="4%"><center>批准<BR>数量</center></th>
        <th width="7%"><center>寄送日期</center></th>
        <th width="7%"><center>寄送人</center></th>
        <th width="4%"><center>寄送<BR>数量</center></th>
        <th width="10%">
           <button title="申请样品" type="button" onclick="window.open('<?php echo base_url();?>sample?action=request_add')">
             <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
        </th>
    </thead>

    <tbody>         
        <?php foreach($requests as $i=>$request): ?>
        <?php switch($request['Status']):
            case 1: $wording="Approve 数量"; $qty=$request['RequestQty']; break;
            case 2: $wording="Approve 数量"; $qty=$request['PmApproveQty']; break;
            case 3: $wording="出库 数量";    $qty=$request['ApproveQty']; break;
            case 4: $wording="寄送 数量";    $qty=$request['ApproveQty']; break;
            default: $qty=0;
        endswitch; ?>
        <?php echo form_hidden('status', $request['Status']); ?> 
        <tr>
            <td>
                <?php if ($request['Status'] ==0 || $request['Status'] >=99): ?>
                    <div style="color:red" class="SlowBlink"><?php echo $status[$request['Status']]; ?></div>
                <?php elseif ($request['Status'] == 5): ?>
                   <?php echo $status[$request['Status']]; ?>
                <?php else: ?>
                   <div style="color:red" class="FastBlink"><?php echo $status[$request['Status']]; ?></div>
                <?php endif; ?>
            </td>
            <td><div align="left"><?php echo $request['PartNumber']; ?></div></td>
            <td><div align="left"><?php echo ($request['Customer'] !="")?($request['Customer']):($request['CompanyCode']);?>
                    <?php echo "<BR><small>".$request['Project'];?></small></div></td>
            <?php if ($request['Status'] >= 3) : ?>
                <td><div style="color:green"><?php echo $request['RequestDate']; ?></div></td>
            <?php elseif ($request['RequestDate'] > date("Y-m-d", strtotime('-2 day'))) : ?>
                <td><div style="color:navy" class="SlowBlink"><?php echo $request['RequestDate']; ?></div></td>
            <?php else: ?>
                <td><div style="color:red" class="SlowBlink"><?php echo $request['RequestDate']; ?></div></td>
            <?php endif; ?>
            <td><?php echo $request['Requester']; ?><BR><?php echo $request['CompanyCode']; ?></td>
            <td><div align="right"><?php echo ($request['RequestQty'] != "0")?(number_format($request['RequestQty'])."&nbsp"): ("--&nbsp") ?></div></td>

            <?php if ($request['ApproveDate'] < $request['RequestDate']):  $request['ApproveDate']=""; endif; ?>
            <?php if ($request['Status'] >= 5) : ?>
                <td><div style="color:green"><?php echo $request['ApproveDate']; ?></div></td>
            <?php elseif ($request['ApproveDate'] > date("Y-m-d", strtotime('-2 day'))) : ?>
                <td><div style="color:navy" class="SlowBlink"><?php echo $request['ApproveDate']; ?></div></td>
            <?php else: ?>
                <td><div style="color:red" class="SlowBlink"><?php echo $request['ApproveDate']; ?></div></td>
            <?php endif; ?>
            <td><?php echo $request['Approver']; ?></td>
            <td><div align="right"><?php echo ($request['ApproveQty'] != "0")?(number_format($request['ApproveQty'])."&nbsp"):("--&nbsp") ?></div></td>

            <?php if ($request['ShipDate'] < $request['RequestDate']): $request['ShipDate']=""; endif; ?>
            <td><?php echo $request['ShipDate']; ?></td>
            <td><?php echo $request['Shipper']; ?></td>
            <td><div align="right"><?php echo ($request['ShipQty'] != "0")? (number_format($request['ShipQty'])."&nbsp"):("--&nbsp") ?></div></td>

            <td><?php if ($action != "request_add" && $action != "request_add2"): ?>
                <?php if ($request['Status']<=2 && (strstr($this->session->userdata['priv'], 'SAM41'))) : ?>
                    <button title="Approve" data-toggle="modal" data-target="#modalApprove" class="open-RequestDialog" 
                    data-id="<?php echo $request['ID'];?>" data-title="批准样品申请(力勤)" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>" 
                    data-wording="Approve 数量" data-address="<?php echo $recipient[$i] ;?>" ><img src="<?php echo site_url('img/approve.png'); ?>"</button>
                <?php elseif ($request['Status']==1 && (strstr($this->session->userdata['priv'], 'SAM40'))) : ?>
                    <button title="Approve" data-toggle="modal" data-target="#modalApprove" class="open-RequestDialog" 
                    data-id="<?php echo $request['ID'];?>" data-title="批准样品申请(PM)" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>" 
                    data-wording="Approve 数量" data-address="<?php echo $recipient[$i] ;?>" ><img src="<?php echo site_url('img/approve.png'); ?>"</button>
                <?php endif;?>
                        
                <?php if ($request['Status']<=2 && $request['RequesterID'] == $this->session->userdata['user_id']): ?>
                    <button title="Cancel" data-toggle="modal" data-target="#modalRejectCancel" class="open-RequestDialog" 
                    data-id="<?php echo $request['ID'];?>" data-title="取消样品申请" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>" 
                    data-wording="取消原因 (可不填)" data-address="<?php echo $recipient[$i] ;?>" ><img src="<?php echo site_url('img/reject.png'); ?>"</button>
                <?php elseif ($request['Status']<=2 && $request['RequesterID'] != $this->session->userdata['user_id']) : ?>   <!-- requester could cancel his request -->
                    <button title="Reject" data-toggle="modal" data-target="#modalRejectCancel" class="open-RequestDialog" 
                    data-id="<?php echo $request['ID'];?>" data-title="拒绝样品申请" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>" 
                    data-wording="不批原因 (必填)" data-address="<?php echo $recipient[$i] ;?>" ><img src="<?php echo site_url('img/reject.png'); ?>"</button>
                <?php endif; ?>
   
                <?php if ($request['Status'] >=1 && $request['Status'] <=4 && (strstr($this->session->userdata['priv'], 'SAM12'))): ?>
                    <button title="Inventory" data-toggle="modal" data-target="#modalViewInventory" class="open-InventoryCommentDialog" 
                    data-title="Inventory on Hand" data-comment="<?php echo $inventory[$i];?>" ><img src="<?php echo site_url('img/inventory.png'); ?>"</button>
                <?php endif; ?>
 
                <?php if ($request['Status'] == 3 && (strstr($this->session->userdata['priv'], 'SAM51'))): ?>
                    <?php if ($my_available[$i] >= $request['ApproveQty']) : ?>
                        <button title="抢单" data-toggle="modal" data-target="#modalApprove" class="open-RequestDialog" 
                        data-id="<?php echo $request['ID'];?>" data-title="<?php echo $status[$request['Status']];?>" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>"
                        data-wording="出库 数量" data-address="<?php echo $recipient[$i] ;?>" ><img src="<?php echo site_url('img/grab.png'); ?>"</button>
                    <?php else: ?>
                        <button title="抢单：库存不足" data-toggle="modal" data-target="#modalViewInventory" class="open-InventoryCommentDialog" 
                        data-title="[你]库存不足" data-comment="<?php echo $inventory[$i];?>" > 
                        <img src="<?php echo site_url('img/grab-no.png'); ?>"</button>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($request['Status'] == 4 && $request['DockerID'] == $this->session->userdata['user_id'] && (strstr($this->session->userdata['priv'], 'SAM5'))): ?>
                    <button title="寄出" data-toggle="modal" data-target="#modalShipped" class="open-RequestDialog" 
                    data-id="<?php echo $request['ID'];?>" data-title="<?php echo $status[$request['Status']];?>" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>"
                    data-wording="寄送 数量" data-address="<?php echo $recipient[$i] ;?>" ><img src="<?php echo site_url('img/ship.png'); ?>"</button>
                <?php endif; ?>       
                
                <?php $query_url = urlencode(json_encode(array("ID"=>$request['ID']))); ?>
                <button title="Add Comment" onclick="window.open('<?php echo base_url();?>sample?action=request_addcomment&query=<?php echo $query_url;?>&option=<?php echo $option;?>')" 
                        type="button"><img src="<?php echo site_url('img/addcomment.png'); ?>"/></button>
                <button title="View Detail" onclick="window.open('<?php echo base_url();?>sample?action=request_view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')" 
                        type="button"><img src="<?php echo site_url('img/view.png'); ?>"/></button>  
<!-- disable qty modify for now 
                <?php if ($request['Status'] <=2 && $request['RequesterID'] == $this->session->userdata['user_id']) : ?>
                    <button title="更改数量" data-toggle="modal" data-target="#modalChangeQty" class="open-RequestDialog" 
                    data-id="<?php echo $request['ID'];?>" data-title="<?php echo $status[$request['Status']];?>" data-status="<?php echo $request['Status'];?>" data-qty="<?php echo $qty;?>"
                    data-wording="<?php echo $wording;?>" ><img src="<?php echo site_url('img/edit.png'); ?>"</button>

                <?php endif; ?>
-->
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
        <div style="color:red">超过两天没有处理 </div>        
        </small></div></td></tr>
    </tbody>
</table>

<!-- approve -->
<!--Pop Up Dialog-->
<div class="modal fade" id="modalApprove" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <form action="sample/request_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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
        <input type="hidden" name="status" id='status' />
        <input type="hidden" name="id" id='id' />
        收件人        
        <textarea name="address" rows="2"  id="address" class="form-control" disabled></textarea>
        <BR><BR>
        <input style="border: none; background: transparent; text-align:center" id="wording" disabled />
        <input style="max-width:50px;" type="number" name='qty' id='qty' step="any" min="1" max="50" />
        <BR><BR>
        <label for="comment">Add Comment:(可填)</label><br>
        <textarea name="comment" rows="2" placeholder="comment here"  id="" class="form-control" ></textarea>
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


<!-- Reject & Cancel -->
<!--Pop Up Dialog-->
<div class="modal fade" id="modalRejectCancel" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <form action="sample/request_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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
        <input type="hidden" name="status" id='status' />
        <input type="hidden" name="id" id='id' />
        收件人        
        <textarea name="address" rows="2"  id="address" class="form-control" disabled ></textarea> 
        <BR><BR>
        <label for="comment"> <input style="border: none; background: transparent; text-align:left" id="wording" disabled ></label><br>
        <textarea name="comment" rows="2" placeholder="comment here"  id="" class="form-control" ></textarea>
    </div>
    <div class="modal-footer ">
      <button type="submit" id="btnNO" name="submit" class="btn btn-danger" value="rejected"><span class="glyphicon glyphicon-ok-sign"></span> 拒绝／取消申请</button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> 取消</button>
    </div>
    </form>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>

<!-- Reject & Cancel -->
<!--Pop Up Dialog-->
<div class="modal fade" id="modalChangeQty" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <form action="sample/request_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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
        <input type="hidden" name="status" id='status' />
        <input type="hidden" name="id" id='id' />
        <BR><BR>
        <label for="comment"> <input style="border: none; background: transparent; text-align:left" id="wording" disabled ></label><br>
        <textarea name="comment" rows="2" placeholder="comment here"  id="" class="form-control" ></textarea>
    </div>
    <div class="modal-footer ">
      <button type="submit" id="btnNO" name="submit" class="btn btn-danger" value="rejected"><span class="glyphicon glyphicon-ok-sign"></span> 更新</button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> 取消</button>
    </div>
    </form>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>

<!--Pop Up Dialog-->
<div class="modal fade" id="modalViewInventory" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true">    </span></button>
        <h4 class="modal-title custom_align" id="Heading"><input style="border: none; background: transparent; text-align:center" id="title" disabled ></h4>
    </div>
    <div class="modal-body">
        <textarea name="comment" rows="10" id="comment" class="form-control" disabled ></textarea>
    </div>
    <div class="modal-footer ">
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> CLOSE</button>
    </div>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>

<!--Pop Up Dialog-->
<div class="modal fade" id="modalShipped" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <form action="sample/request_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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
        <input type="hidden" name="status" id='status' />
        <input type="hidden" name="id" id='id' />
        收件人        
        <textarea name="address" rows="2"  id="address" class="form-control" disabled /></textarea>
        <BR><BR>
        <input style="border: none; background: transparent; text-align:center" id="wording" disabled ><input style="max-width:50px;" type="number" name='qty' id='qty' step="any" min="1" max="50" />
        <BR><BR>
        <?php echo form_dropdown("courier_id", $courier_list, "" , 'id="courier_id"') ?>
        <BR><BR>
        <label for="AWB">快递单号</label>
        <input type="text" size="12" name="AWB" rows="1" placeholder="单号"  />
        <BR><BR>
        <label for="comment">Add Comment:(可填)</label><br>
        <textarea name="comment" rows="2" placeholder="comment here"  id="" class="form-control" ></textarea>
    </div>
    <div class="modal-footer ">
        <button type="submit" id="btnYES" name="submit" class="btn btn-success" value="shipped" ><span class="glyphicon glyphicon-ok-sign"></span> 提交</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> 取消</button>
    </div>
    </form>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>

<!--Pop Up Dialog-->
<div class="modal fade" id="modalInsufficientInventory" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true">    </span></button>
        <h4 class="modal-title custom_align" id="Heading"><input style="border: none; background: transparent; text-align:center" id="title" disabled ></h4>
    </div>
    <div class="modal-body">
        <textarea name="comment" rows="10" id="comment" class="form-control" disabled ></textarea>
    </div>
    <div class="modal-footer ">
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> CLOSE</button>
    </div>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>