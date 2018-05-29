<?php if ( ($action == 'quote_add')  && (strstr($this->session->userdata['priv'], 'DPA11')) ): ?> 
<?php echo form_open('dpa/quote_add'); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  


<table class="EditBox">
    <tr><td colspan="6"><center>Price Quote Request</center></td></tr>
    <tr>
        <td colspan="2">申请人：<input style="max-width:100px;" type="text" name="Requester" value="<?php echo $requester;?>" readonly="true"/></td>
        <td colspan="2">客人：<?php echo form_dropdown("final_customer_id", $customer_list, "", 'id="final_customerid"') ?></td>
        <td colspan="2">Project Name: <input type="text" name="project" maxlength="32"></td>
    </tr>
    <tr>
        <td colspan="2">料号：<?php echo form_dropdown("product_id", $product_list, "" , 'id="product_id"') ?></td>
        <td colspan="2">Price:<input type="float" name="quote_price">

    </tr>
    <tr>
        <td colspan="2">生效日:<input type="date" name="quote_start_date" value="<?php echo date("Y-m-d");?>">
        <td colspan="2">失效日:<input type="date" name="quote_expiry_date" value="<?php echo date("Y-m-d", strtotime('+60 day')); ?>" min="<?php echo date("Y-m-d");?>">
        <td colspan="2">Max数量：<input style="max-width:80px;" type="number" name="quote_max_qty" value="100000" step="1000" min="1000" max="500000"/></td>   
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

<?php elseif (($action == 'quote_view') || ($action=='quote_addcomment')): ?>
<?php if ($action=='quote_addcomment'): ?>
<?php echo form_open('dpa/quote_addcomment'); ?>
<?php echo form_hidden('ID', $id); ?>
<?php endif; ?>
<table class="ViewBox">
    <tr>
        <td colspan="12"><center><big><b>申请状况: 
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
        <td colspan="3"></td> 
    </tr>
    
    <tr>
        <td colspan="2"></td>
        <td colspan="5">客人：<BR><?php echo $edit[0]['FinalCustomer']; ?></td>
        <td colspan="5">项目名称：<BR><?php echo $edit[0]['Project']; ?>  </td>
    </tr>

    <tr>
        <td colspan="2"></td>
        <td colspan="2">Part Number: <br><?php echo $edit[0]['PartNumber']; ?></td> 
        <td colspan="2">单价: <br>$<?php echo $edit[0]['QuotePrice']; ?></td>
        <td colspan="2">最多数量: <br><?php echo number_format($edit[0]['QuoteMaxQty']); ?></td>
        <td colspan="2">生效日: <br><?php echo$edit[0]['QuoteStartDate']; ?></td>
        <td colspan="2">失效日: <br><?php echo$edit[0]['QuoteExpiryDate']; ?></td>
    </tr>

    <?php if ($action=='quote_addcomment'): ?>
        <tr><td colspan="12">
            <b>Enter your Comment:</b><br>
            <?php echo form_textarea(array('name'=>'newcomment', 'id'=>'newcomment', 'value'=>"", 'cols'=>'40', 'rows'=>'3')); ?>
        </td></tr>
    <?php endif; ?>
    <?php if (($edit[0]['Status'] >=100)): ?>
        <tr><td colspan="12"><?php echo $edit[0]['Approver']." ".$edit[0]['ApproveDate']." ".$edit[0]['ApproveTime'].
                "<BR> 不Approve原因: ".$status[$edit[0]['Status']],"<BR><BR>"; ?></td><tr>
    <?php elseif ($edit[0]['Status'] ==2): ?>
        <tr>
            <td colspan="2"><b>批准明细: </b></td>
            <td colspan="5">批准人: <br> <?php echo $edit[0]['Approver']; ?></td>
            <td colspan="5">批准时间: <br><?php echo $edit[0]['ApproveDate']." ".$edit[0]['ApproveTime']; ?></td>
        </tr>
    <?php endif; ?>
        
    <?php foreach ($comments as $comment): ?>
        <tr><td colspan="12"><?php echo "<BR>Comment:  ".$comment['TimeStamp']," by ".$comment['Commenter']; ?><br>
                <textarea readonly><?php echo $comment['Comment']; ?></textarea></td></tr>
    <?php endforeach; ?>
        
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='12'><div align='left'><small><b>ChangeLot:</b><br><?php echo $edit[0]['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
</table>

<?php if ($action=='quote_addcomment'): ?>
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
            <input title="Close this Window" type="button" value="CLOSE" onclick="location.href='<?php echo base_url()."dpa?action=quote_list";?>'">
        </div></td></tr>    </tr>
    </table>
<?php endif;?>

<?php else: ?>

<table class="EditBox">
    <?php echo form_open('dpa/quote_search'); ?>
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
        <th width="7%"><center>代理</center></th>

        <th width="12%"<?php if ($sort_by == "Customer") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("dpa?action=$action&sb=Customer&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "最终客人/项目"); ?></th>
        <th width="8%"<?php if ($sort_by == "RequestDate") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("dpa?action=$action&sb=RequestDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "申请日期"); ?></th>
        
        <th width="13%"<?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("dpa?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Part Number"); ?></th>

        <th width="7%"<?php if ($sort_by == "QuotePrice") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("dpa?action=$action&sb=QuotePrice&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "单价"); ?></th>
        <th width="7%"><center>Max Qty</center></th>
        <th width="8%"<?php if ($sort_by == "QuoteStartDate") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("dpa?action=$action&sb=QuoteStartDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "生效日"); ?></th>
        <th width="8%"<?php if ($sort_by == "QuoteExpiryDate") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("dpa?action=$action&sb=QuoteExpiryDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "失效日"); ?></th>
        <th width="8%"><center>Approve Date</center></th>
        <th width="7%"><center>Closure<br>Qty</center></th>
        <th width="8%">
           <button title="申请样品" type="button" onclick="window.open('<?php echo base_url();?>dpa?action=quote_add')">
             <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
        </th>
    </thead>

    <tbody>         
        <?php foreach($quotes as $i=>$quote): ?>
        <tr>
            <td>
                <?php if ($quote['Status'] ==0 || $quote['Status'] >=99): ?>
                    <div style="color:red" class="SlowBlink"><?php echo $status[$quote['Status']]; ?></div>
                <?php elseif ($quote['Status'] == 2): ?>
                   <?php echo $status[$quote['Status']]; ?>
                <?php else: ?>
                   <div style="color:red" class="FastBlink"><?php echo $status[$quote['Status']]; ?></div>
                <?php endif; ?>
            </td>
            <td><?php echo $disti_list[$quote['DistiID']]; ?></td>
            
            <td><?php echo $quote['FinalCustomer']."<BR><small>".$quote['Project']."</small>";?></td>            
            
            <?php if ($quote['Status'] >= 2) : ?>
                <td><div style="color:green"><?php echo $quote['RequestDate']."<BR><small>".$quote['Requester']."</small>"; ?></div></td>
            <?php elseif ($quote['RequestDate'] > date("Y-m-d", strtotime('-2 day'))) : ?>
                <td><div style="color:navy" class="SlowBlink"><?php echo $quote['RequestDate']."<BR><small>".$quote['Requester']."</small>"; ?></div></td>
            <?php else: ?>
                <td><div style="color:red" class="SlowBlink"><?php echo $quote['RequestDate']."<BR><small>".$quote['Requester']."</small>"; ?></div></td>
            <?php endif; ?>

                
            <td><div align="left"><?php echo $quote['PartNumber']; ?></div></td>
            <td><div align="right">$<?php echo number_format((float)$quote['QuotePrice'], 3, '.', '');?></div></td>
            <td><div align="right"><?php echo number_format($quote['QuoteMaxQty']); ?></div></td>
            
            <td><?php echo $quote['QuoteStartDate']; ?></div></td>
            <td><?php echo $quote['QuoteExpiryDate']; ?></div></td>
            
            <?php if ($quote['ApproveDate'] < $quote['RequestDate']):  $quote['ApproveDate']=""; endif; ?>
            <?php if ($quote['Status'] >= 2) : ?>
                <td><div style="color:green"><?php echo $quote['ApproveDate']."<BR><small>".$quote['Approver']."</small>"; ?></div></td>
            <?php else: ?>
                <td>-</div></td>
            <?php endif; ?>
                
            <td><?php echo $quote['ClosureQty']; ?></div></td>

            <td><?php if ($action != "quote_add"): ?>
                <?php if ($quote['Status']<=1 && $quote['RequesterID'] == $this->session->userdata['user_id']): ?>
                    <button title="Cancel" data-toggle="modal" data-target="#modalRejectCancel" class="open-RequestDialog" 
                    data-id="<?php echo $quote['ID'];?>" data-title="取消申请" data-status="<?php echo $quote['Status'];?>" 
                    data-wording="取消原因 (必填)" ><img src="<?php echo site_url('img/reject.png'); ?>"</button>
                <?php endif; ?>
                <?php if ($quote['Status']==1 && (strstr($this->session->userdata['priv'], 'DPA12'))) : ?>
                    <button title="Approve" data-toggle="modal" data-target="#modalApprove" class="open-RequestDialog" 
                    data-id="<?php echo $quote['ID'];?>" data-title="批准申请" data-status="<?php echo $quote['Status'];?>" 
                    data-wording="批准原因 (可不填)" ><img src="<?php echo site_url('img/approve.png'); ?>"</button>

                    <button title="Reject" data-toggle="modal" data-target="#modalRejectCancel" class="open-RequestDialog" 
                    data-id="<?php echo $quote['ID'];?>" data-title="拒绝申请" data-status="<?php echo $quote['Status'];?>" 
                    data-wording="不批原因 (必填)" ><img src="<?php echo site_url('img/reject.png'); ?>"</button>
                <?php endif; ?>
                <?php $query_url = urlencode(json_encode(array("ID"=>$quote['ID']))); ?>
                <button title="Add Comment" onclick="window.open('<?php echo base_url();?>dpa?action=quote_addcomment&query=<?php echo $query_url;?>&option=<?php echo $option;?>')" 
                        type="button"><img src="<?php echo site_url('img/addcomment.png'); ?>"/></button>
                <button title="View Detail" onclick="window.open('<?php echo base_url();?>dpa?action=quote_view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')" 
                        type="button"><img src="<?php echo site_url('img/view.png'); ?>"/></button>  
                
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
    <form action="dpa/quote_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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

        <label for="comment"> <input style="border: none; background: transparent; text-align:left" id="wording" disabled ></label><br>
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
    <form action="dpa/quote_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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
    <form action="dpa/quote_process" name="" id="login-form" class="form-horizontal" enctype="multipart/form-data" method="POST">
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

