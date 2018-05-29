<?php $grp_comp = FALSE; ?>
<?php if ( ($action == 'inv_move')  && (strstr($this->session->userdata['priv'], 'SAM62')) ): ?> 
<?php echo form_open('sample/inventory_move'); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  

<table class="EditBox">
    <tr><td colspan="6"><center>Inventory Movement</center></td></tr>
    <tr>
        <td colspan="2">料号：<?php echo form_dropdown("product_id", $product_list, "" , 'id="product_id"') ?></td>
        <td colspan="2">料号：<?php echo form_dropdown("warehouse_id", $warehouse_list, "" , 'id="warehouse_id"') ?></td>
        <td colspan="2">数量：<input title="負數=出庫，正數=入庫" style="max-width:50px;" type="number" name="qty" id="qty" value="-5" step="any" min="-100" max="2000"/></td>   
    </tr>
    <tr><td colspan="6">
        <b>Comment: (必填)</b><br>
        <?php echo form_textarea(array('name'=>'comment', 'id'=>'comment', 'value'=>"", 'cols'=>'40', 'rows'=>'3')); ?>
    </td></tr>
</table>

<table class="EditBox">
    <tr>
        <td><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url()."sample?action=inv_move";?>'"></div></td>
        <td><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
        <td><div align="center">
            <button title="Submit" type="submit" name="submit">Submit</button></div></td>
    </tr>
</table>
<?php echo form_close(); ?>

<?php else: ?>
 
<table class="EditBox">
    <?php echo form_open('sample/inventory_search'); ?>
    <?php echo form_hidden('action', $action); ?>
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
            <?php if ($type=="summary"): ?>
                <?php $grp_comp = (strpos($option, 'grpcomp') !== FALSE)?TRUE:FALSE; ?>
                <?php echo form_checkbox('grp_comp', '1', $grp_comp); ?> Group by Company <BR>
                <?php if (strpos($option, 'grpcomp') !== FALSE && strstr($this->session->userdata['priv'], 'SAM61')): ?>
                    <input type="checkbox" name="show_rma" value="0" disabled /> Show RMA Inventory
                <?php elseif (strstr($this->session->userdata['priv'], 'SAM61')): ?>
                    <?php $show_rma = ((strpos($option, 'showrma') !== FALSE))?TRUE:FALSE; ?>
                    <?php echo form_checkbox('show_rma', '1', $show_rma); ?> Show RMA Inventory
                <?php endif; ?>
            <?php elseif (strstr($this->session->userdata['priv'], 'SAM61')): ?>
                <?php $show_rma = ((strpos($option, 'showrma') !== FALSE))?TRUE:FALSE; ?>
                <?php echo form_checkbox('show_rma', '1', $show_rma); ?> Show RMA Inventory       
            <?php endif; ?>
            
            <BR>
            <?php if ($type == 'detail'):?>
                <button title="View Summary" type="button" name="Summary" onclick="location.href='<?php echo base_url()."sample?action=inv_summary";?>'">View Summary</button>
            <?php else: ?>
                <button title="View Detail" type="button" name="Detail" onclick="location.href='<?php echo base_url()."sample?action=inv_detail"?>'">View Detail</button>
            <?php endif; ?>
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
        <?php if ($type=="detail"): ?>
            <th width="25%" <?php if ($sort_by == "Date") echo "class=\"sort_$sort_order\"" ?>>
                <?php echo anchor("sample?action=$action&sb=Date&so=" . 
                    (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Transaction Date"); ?></th>
        <?php endif; ?>
            
        <th width="20%" <?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Part Number"); ?></th>
        
        <th width="20%"<?php if ($sort_by == "Company") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=Customer&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "力勤/代理"); ?></th>
        
        <?php if (!$grp_comp): ?>
        <th width="20%"<?php if ($sort_by == "Warehouse") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=Warehouse&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "仓库"); ?></th>    
        <?php endif;?>
        
        <th width="15%"><center>数量</center></th>
        <?php if ($type=="detail"): ?><th width="20%">
           <?php if (strstr($this->session->userdata['priv'], 'SAM62')): ?>
            <?php $url = base_url()."sample?action=inv_move&sb=$sort_by&so=$sort_order&query=$query_url&option=$option&os=$offset"; ?>
            <button title="Inventory Movement" type="button" onclick="window.open('<?php echo $url;?>')">
              <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th><?php endif; ?>
    </thead>

    <tbody>
        <?php $total=0; ?>
        <?php foreach($transactions as $i=>$transaction): ?>
        <tr>
            <?php if ($type=="detail"): ?>
                <td><?php echo $transaction['Date']."&nbsp;&nbsp;&nbsp;".$transaction['Time']; ?></td>
            <?php endif; ?>
            <td><?php echo $transaction['PartNumber']; ?></td>
            <td><?php echo $transaction['CompanyCode'];?></td>
            <?php if (!$grp_comp): ?><td><?php echo $transaction['Warehouse'];?></td><?php endif;?>
            <td><?php echo number_format($transaction['Qty']);?></td>
            <?php $total+=$transaction['Qty'];?>
            
            <?php if ($type=="detail"): ?>
                <td>
                <?php if ($transaction['Comment'] != ""): ?>
                    <button data-toggle="modal" data-target="#modalComment" class="open-InventoryCommentDialog" 
                    data-title="Transaction Detail" data-comment="<?php echo $transaction['Comment'];?>">
                    <img src="<?php echo site_url('img/view.png'); ?>"</button>
                <?php endif; ?>
                    
                <?php if ($transaction['RequestID']!=0): ?>
                    <?php $query_url = urlencode(json_encode(array("ID"=>$transaction['RequestID']))); ?>
                    <button title="View Detail" type="button" onclick="window.open('<?php echo base_url();?>sample?action=request_view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                        <img src="<?php echo site_url('img/view.png'); ?>"/></button>
                <?php endif; ?>

                </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <tr bgcolor="#00BF00">
            <?php if ($type=="detail"): ?>
                <td colspan="2"></td>
            <?php endif; ?>
                
            <?php if ($type=="summary" && !$grp_comp): ?>  
                <td colspan="1"></td>
            <?php endif; ?> 
            <td colspan="2"><b>TOTAL</b></td>
            <td><b><?php echo number_format($total);?></b></td>
            <?php if ($type=="detail"): ?><td></td><?php endif;?>
        </tr>
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
        <div style="color:red"> </div>        
        </small></div></td></tr>
    </tbody>
</table>

<!-- approve -->
<!--Pop Up Dialog-->
<div class="modal fade" id="modalComment" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true">    </span></button>
        <h4 class="modal-title custom_align" id="Heading"><input style="border: none; background: transparent; text-align:center" id="title" disabled ></h4>
    </div>
    <div class="modal-body">
        <textarea name="comment" rows="4" id="comment" class="form-control" disabled ></textarea>
    </div>
    <div class="modal-footer ">
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> CLOSE</button>
    </div>
</div>
<!-- /.modal-content --> 
</div>
<!-- /.modal-dialog --> 
</div>


