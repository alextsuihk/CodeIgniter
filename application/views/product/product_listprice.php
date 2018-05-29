<?php if ( $action == 'update' && (strstr($this->session->userdata['priv'], 'PRO32')) ): ?>
<table class="ListBox">
     <tr>
         <td colspan="3"><div align="left"> Part Number: <b><?php echo $products['0']['PartNumber']; ?></b></div></td>
        <td colspan="3"><div align="left"> Description: <b><?php echo $products['0']['Description']; ?></b></div></td>
     </tr>
</table>
<br>
<table class="EditBox">
     <?php echo form_open('product_listprice/edit'); ?>
     <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('product_id', $product_id); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?>
     <?php echo form_hidden('sort_by', $sort_by); ?>
     <?php echo form_hidden('sort_order', $sort_order); ?>
     <?php echo form_hidden('query', $query); ?>
     <?php echo form_hidden('offset', $offset); ?>   
    <tr>
        <td colspan='2'>
            <?php echo form_label('Effective Date (YYYY-MM-DD):', 'effective_date'); ?>
            <input type="text" name="effective_date" value="" placeholder="YYYY-MM-DD" id="effective_date">
        </td>
        <td colspan='2'>
            <?php echo form_label('List Price (USD):', 'list_price'); ?>
            <?php echo form_input('list_price', ''); ?>  
        </td>
        <td colspan='2'>
            <?php echo form_label('Margin %:', 'commission1'); ?>
            <input type="text" name="commission1" value="" placeholder="2%" id="commission1">
            <br>
            <?php echo form_label('Margin $:', 'commission2'); ?>
            <input type="text" name="commission2" value="" placeholder="US$0.04" id="commission2">
        </td>
        <td colspan='2'>
            <?php echo form_label('Disti Lead Time (day):', 'disti_lead_time'); ?>
            <?php echo form_input('disti_lead_time', ''); ?>
        </td>
    </tr>

    <tr>
        <td colspan="3"><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td colspan="2"><div align="center">
            <button title="<?php echo $submit_button;?> Record" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="3"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
    </tr>
    <?php echo form_close(); ?>

</table>

<?php elseif ($action == 'history'): ?>
<table class="ListBox">
    <tr>
        <td colspan="3"><div align="left"> Part Number: <b><?php echo $products['0']['PartNumber']; ?></b></div></td>
        <td colspan="3"><div align="left"> Description: <b><?php echo $products['0']['Description'];?></b></div></td>
     </tr>
</table>
<br>
<table class="ViewBox">
     <tr>
        <th colspan="2">Effective Date</th>
        <th colspan="2">List Price (USD)</th>
        <th colspan="2">Commission</th>
        <th colspan="2">Lead Time (days)</th>
        <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
            <th colspan="6">Change Log</th>
            <th colspan="1">Valid</th>
        <?php endif; ?>
     </tr> 

     <?php foreach($listprices as $listprice): ?>
     <tr>
        <td colspan="2"><div align="center"><?php echo $listprice['EffectiveDate']; ?></div></td>
        <td colspan="2"><div align="center"><?php echo $listprice['ListPrice']; ?></div></td>
        <td colspan="2"><div align="center">
            <?php if ($this->session->userdata['user_id'] == '0'): ?>
                <?php echo 'hidden'; ?>
            <?php else: ?>
                <?php echo $listprice['Commission1'].'%'; ?>
                <?php if ($listprice['Commission2'] > 0): echo " + $".$listprice['Commission2']; endif;?>
            <?php endif; ?>
        </div></td>
        <td colspan="2"><div align="center"><?php echo $listprice['DistiLeadTime']; ?></div></td>        
        <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
            <td colspan="6"><?php echo substr($listprice['ChangeLog'], 0, 100); ?></td>
            <td colspan="1"><?php echo ($listprice['Expired']?"Expired":"Valid"); ?></td>
        <?php endif; ?>
     </tr>
    <?php endforeach; ?>
    <tr><?php if ($this->session->userdata['view_changelog'] == '1'): ?><td colspan="15"><?php else:?><td colspan="8"><?php endif;?>
            <div align="center"><input title="Close this Window" type="button" value="CLOSE" onclick="self.close()"></div></td>
    </tr>
</table>

<?php else: ?>                                  <!-- listing data -->

<table class="EditBox">
     <?php echo form_open('product_listprice/search'); ?>
    <!-- pass parameters from previous controller to next controller -->
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <tr>
        <th width="40%" rowspan="2">
            <?php $i=0; ?>
            <div align="left"> &nbsp;&nbsp;&nbsp;&nbsp; Search By:
            <!-- &nbsp;&nbsp;&nbsp;<button title="Add more Search Criteria" type="submit" name="submit" value=add:'.$i.'>+</button> --> </div>
            <?php foreach ($query as $key=>$value): ?>
            <?php echo form_dropdown("searchby[$i]", $searchby_options, $key, "id=\"searchby[$i]\"") ?>
            <?php echo form_input("search_value[$i]", ($key=='ID')?'':$value, array('maxlength'=>'32', 'size'=>'25')); ?>
            <!-- <button title="Remove this Criteria" type="submit" name="submit" value=delete:'.$i.'>-</button>  -->
            <br>
            <?php $i++; ?>
            <?php endforeach; ?>
        </th>
        <th width="53%" rowspan="2"><div align="left">
            <!--
            <?php $active_only = ((strpos($option, 'activeonly') !== FALSE))?TRUE:FALSE; ?>
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active Product Only
            -->
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
        <th  width="15%" <?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_listprice?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Part Number"); ?></th>
        <th width="45%"<?php if ($sort_by == "Description") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_listprice?action=$action&sb=Description&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Description"); ?></th>
        <th width="4%" <?php if ($sort_by == "BallType") echo "class=\"sort_$sort_order\"" ?>> 
            <?php echo anchor("product_listprice?action=$action&sb=BallType&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Ball Type"); ?></th>
        <th width="7%" <?php if ($sort_by == "Effective Day") echo "class=\"sort_$sort_order\"" ?>> 
            <?php /*echo anchor("product_pricelist/top/$action/$limit/EffectiveDate/" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."/$query_field/$query_value/$offset" , "Effective Date"); */?> Effective Date</th>
        <th width="6%">List Price (USD)</th>
        <th width="9%">Margin</th>
        <th width="4%">Lead Time (Day)</th>
        <th width="3%"></th>
        <th width="7%"></th>
    </thead>

    <tbody>  
        <?php foreach($products as $product): ?>
        <tr>
            <?php $query_url = urlencode(json_encode(array("ID"=>$product['ID']))); ?>
            <td><div align="left"><?php echo $product['PartNumber']; ?></div></td>
            <td><div align="left"><?php echo $product['Description']."; ".$product['Description2'] ; ?></small></div></td>
                    </div></td>
            <td><?php echo $product['BallType']; ?></td>
            <td><?php echo $product['EffectiveDate']; ?></td>
            <td><div align="right"><b><?php echo $product['ListPrice']; ?></b></div></td>
            <td><?php if ($this->session->userdata['user_id'] == '0'): ?>
                    <?php echo 'hidden'; ?>
                <?php else: ?>
                    <?php echo $product['Commission1'].'%'; ?>
                    <?php if ($product['Commission2'] > 0): echo "<br> + $".$product['Commission2']; endif;?>
                <?php endif; ?></td>
            <td><?php echo $product['DistiLeadTime']; ?></td>
            <td><?php echo ($product['Active'])?"A":"I"; ?></td>
             <td><button title="View History" type="button"  onclick="window.open('<?php echo base_url();?>product_listprice?action=history&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                     <img src="<?php echo site_url('img/database.png'); ?>"/></button>              
                <?php if (strstr($this->session->userdata['priv'], 'PRO32')): ?>
                <button title="Update Booking Price" type="button" onclick="window.open('<?php echo base_url();?>product_listprice?action=update&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
            </td>
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
        ONLY Active Products have list price (booking price)<br><br>
        </small></div></td></tr>
    </tbody>
</table>
