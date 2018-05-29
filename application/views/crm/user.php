<?php if (($action == 'add' || $action =='edit') && strstr($this->session->userdata['priv'], 'CRM30')): ?>
<table class="EditBox">
    <?php echo form_open('crm/user_edit'); ?>
     <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('user_id', $user_id); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?>
     <?php echo form_hidden('sort_by', $sort_by); ?>
     <?php echo form_hidden('sort_order', $sort_order); ?>
     <?php echo form_hidden('query', $query); ?>
     <?php echo form_hidden('offset', $offset); ?>     
    <tr>
        <td colspan="5"><div align="left">
            <?php echo form_label('Email: ', 'email'); ?>
            <?php echo form_input('email', $edit['0']['Email'], array('maxlength'=>'64', 'size'=>'30') ); ?>
        </div></td>
        <td colspan="4"><div align="left">
            <?php echo form_label('Name: ', 'nickname'); ?>
            <?php echo form_input('nickname', $edit['0']['Nickname'], array('maxlength'=>'20', 'size'=>'18') ); ?>
            </div></td>
        <td colspan="3"><div align="left">
            <?php echo form_label('中文名：', 'chinese_name'); ?>
            <?php echo form_input('chinese_name', $edit['0']['ChineseName'], array('maxlength'=>'10', 'size'=>'10') ); ?>
        </div></td>
        <td colspan="2"><?php echo form_label('Active:', 'active'); ?>
            <?php echo form_checkbox('active', '1', (!$edit['0']['Active'])? FALSE:TRUE); ?></td>
                <td colspan="2"><?php echo form_label('Allow Login:', 'allow_login'); ?>
            <?php echo form_checkbox('allow_login', '1', (!$edit['0']['AllowLogin'])? FALSE:TRUE); ?></td>
    </tr>
    <tr>
        <td colspan="4"><div align="left">
            <?php echo form_label('Company ID:', 'company_id'); ?>
                <?php echo  $edit['0']['CompanyID'];?> 
            <?php echo form_dropdown("company_id", $company_list, intval($edit['0']['CompanyID']), 'id="company_id"') ?>
        </div></td>
        <td colspan="4"><div align="left">
            <?php echo form_label('Phone:', 'phone'); ?>
            <?php echo form_input('phone', $edit['0']['Phone'], array('maxlength'=>'32', 'size'=>'22') ); ?>
        </div></td>
        <td colspan="4"><div align="left">
            <?php echo form_label('Mobile:', 'mobile'); ?>
            <?php echo form_input('mobile', $edit['0']['Mobile'], array('maxlength'=>'32', 'size'=>'22') ); ?>
        </div></td>
        <td colspan="4"><div align="left">
            <?php echo form_label('Fax:', 'fax'); ?>
            <?php echo form_input('fax', $edit['0']['Fax'], array('maxlength'=>'32', 'size'=>'22') ); ?>
        </div></td>
    </tr>
    <tr>
        <td colspan="16"><div align="left">
            <?php echo form_label('Address (ID):', 'address_id'); ?>
            <?php echo form_dropdown("address_id", $address_list, $edit['0']['AddressID'], 'id="address_id"') ?>
        </div></td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo form_label('Mat Cost:', 'view_mat_cost'); ?>
            <?php echo form_checkbox('view_mat_cost', '1', (!$edit['0']['ViewMatCost'])? FALSE:TRUE); ?>
        </td>
        <td colspan="2">
            <?php echo form_label('Man Cost:', 'view_man_cost'); ?>
            <?php echo form_checkbox('view_man_cost', '1', (!$edit['0']['ViewManCost'])? FALSE:TRUE); ?>
        </td>
        <td colspan="2">
            <?php echo form_label('FG Cost:', 'view_fg_cost'); ?>
            <?php echo form_checkbox('view_fg_cost', '1', (!$edit['0']['ViewFgCost'])? FALSE:TRUE); ?>
        </td>
        <td colspan="10"></td>   
    </tr>
    <tr>
        <td colspan="5"><div align="left">
            <?php echo form_label('Password: ', 'password'); ?>
            <?php echo form_input('password', '', array('maxlength'=>'16', 'size'=>'16') ); ?>
        </div></td>
        <td colspan="5"><div align="left">
            <?php echo form_label('Password Again: ', 'password2'); ?>
            <?php echo form_input('password2', '', array('maxlength'=>'64', 'size'=>'16') ); ?>
        </div></td>
        <td colspan="6"><div align="left">
            <?php echo form_label('Password Expiry (YYYY-MM-DD):', 'password_expiry_date'); ?>
            <?php echo form_input('password_expiry_date', $edit['0']['PasswordExpiryDate'], array('maxlength'=>'10', 'size'=>'10') ); ?>
        </div></td>
    </tr>
    <tr><td colspan=16><?php echo form_label('Setting:', 'setting'); ?>
        <?php echo form_textarea(array('name'=>'setting', 'id'=>'setting', 'value'=>$edit['0']['Setting'], 'cols'=>'40', 'rows'=>'3')); ?>
    </td></tr>
    <tr><td colspan="16"><?php echo form_label('Favorite:', 'favorite'); ?>
        <?php echo form_textarea(array('name'=>'favorite', 'id'=>'favorite', 'value'=>$edit['0']['Favorite'], 'cols'=>'40', 'rows'=>'3')); ?>
    </td></tr>
    <tr><td colspan="16"><?php echo form_label('Privileges:', 'privilege'); ?>
        <?php echo form_textarea(array('name'=>'privilege', 'id'=>'privilege', 'value'=>$edit['0']['Privilege'], 'cols'=>'40', 'rows'=>'3')); ?>
    </td> </tr>
    <tr><td colspan="16"><?php echo form_label('Note:', 'note'); ?><br>
        <?php echo form_textarea(array('name'=>'note', 'id'=>'note', 'value'=>$edit['0']['Note'], 'cols'=>'40', 'rows'=>'3')); ?>
    </td></tr>
    <tr>
        <td colspan="5"><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td colspan="6"><div align="center">
            <button title="<?php echo $submit_button;?> Record" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="5"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
    </tr>
    <?php echo form_close(); ?>

</table>

<?php elseif ($action == 'view'): ?>
<table class="ViewBox">
    <tr>
        <td colspan="5"><div align="left"><b>Email: </b><?php echo $edit['0']['Email'];?></div></td>
        <td colspan="4"><div align="left"><b>Name: </b><?php echo $edit['0']['Nickname'];?></div></td>
        <td colspan="3"><div align="left"><b>中文名：</b><?php echo $edit['0']['ChineseName'];?></div></td>
        <td colspan="2"><b><?php echo (($edit['0']['Active'])? 'Active':'Inactive'); ?></b></td>
        <td colspan="2"><b><?php echo (($edit['0']['AllowLogin'])? 'Allow Login':'No Access'); ?></b></td>
    </tr>
    <tr>
        <td colspan="4"><div align="left"><b>Company Code: </b><?php echo $edit['0']['CompanyCode'];?></div></td>
        <td colspan="4"><div align="left"><b>Phone: </b><?php echo $edit['0']['Phone'];?></div></td>
        <td colspan="4"><div align="left"><b>Mobile: </b><?php echo $edit['0']['Mobile'];?></div></td>
        <td colspan="4"><div align="left"><b>Fax: </b><?php echo$edit['0']['Fax'];?></div></td>
    </tr>
    <tr>
        <td colspan="6"><div align="left"><b>Password Expiry (YYYY-MM-DD): </b><?php echo $edit['0']['PasswordExpiryDate'];?></div></td>
        <td colspan="3"><b>Mat Cost: </b><?php echo (($edit['0']['ViewMatCost'])? 'Allowed':'Disallowed'); ?></b></td>
        <td colspan="3"><b>Man Cost: </b><?php echo (($edit['0']['ViewManCost'])? 'Allowed':'Disallowed'); ?></b></td>
        <td colspan="3"><b>Man Cost: </b><?php echo (($edit['0']['ViewFgCost'])? 'Allowed':'Disallowed'); ?></b></td>
        <td colspan="1"></td>   
    </tr>
    <tr>
        <td colspan="16"><div align="left"><b>Address: </b><?php echo $edit['0']['CombinedAddress']; ?></div></td>
    </tr>
    <tr><td colspan='16'><b>Settings:</b><br><textarea readonly><?php echo $edit['0']['Setting']; ?></textarea></td></tr>
    <tr><td colspan='16'><b>Favorites:</b><br><textarea readonly><?php echo $edit['0']['Favorite']; ?></textarea></td></tr>
    <tr><td colspan='16'><b>Privileges:</b><br><textarea readonly><?php echo $edit['0']['Privilege']; ?></textarea></td></tr>
    <tr><td colspan='16'><b>Note:</b> (<?php echo $edit['0']['ID'];?>) <br><textarea readonly><?php echo $edit['0']['Note']; ?></textarea></td></tr>

    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='16'><div align='left'><small><b>ChangeLot:</b><br><?php echo $edit['0']['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
    <tr>
        <td colspan='16'><div align="center">
            <input title="Close this Window" type="button" value="CLOSE" onclick="self.close()">
        </div></td></tr>
</table>
<?php else: ?>                                  <!-- listing data -->

<table class="EditBox">
    <?php echo form_open('crm/user_search'); ?>
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
            <!-- <button title="Remove this Criteria" type="submit" name="submit" value=delete:'.$i.'>-</button> -->
            <br>
            <?php $i++; ?>
            <?php endforeach; ?>
        </th>
        <th width="53%" rowspan="2"><div align="left">
            <?php $active_only = ((strpos($option, 'activeonly') !== FALSE))?TRUE:FALSE; ?>
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active User Only
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
        <th  width="10%"<?php if ($sort_by == "Nickname") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=Nickname&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Name"); ?></th>
        <th width="7%"<?php if ($sort_by == "ChineseName") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=ChineseName&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "中文名"); ?></th>
        <th width="7%"<?php if ($sort_by == "CompanyCode") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=CompanyCode&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "<small>Company</small>"); ?></th>
        <th width="15%"<?php if ($sort_by == "Email") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=Email&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Email"); ?></th>
        <th width="15%"<?php if ($sort_by == "Phone") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=Phone&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Tel"); ?></th>
        <th width="15%"<?php if ($sort_by == "Mobile") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=Mobile&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Mobile"); ?></th>
        <th width="18%">Other?</th>
        <th width="3%"<?php if ($sort_by == "AllowLogin") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user&action=$action&sb=AllowLogin&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "L"); ?></th>
        <th width="3%"<?php if ($sort_by == "Active") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/user?action=$action&sb=Active&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "A"); ?></th>
        <th width="7%">
            <?php if (strstr($this->session->userdata['priv'], 'CRM20')) :  ?>
                <button title="Add User" type="button" onclick="window.open('<?php echo base_url();?>crm/user?action=add')">
                    <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th>
    </thead>

    <tbody>  
        <?php foreach($users as $user): ?>
        <tr>
            <td><div align="left"><?php echo $user['Nickname']; ?></div></td>
            <td><?php echo $user['ChineseName']; ?></td>
            <td><?php echo $user['CompanyCode']; ?></td>
            <td><?php echo $user['Email']; ?></td>
            <td><?php echo $user['Phone']; ?></td>
            <td><?php echo $user['Mobile']; ?></td>
            <td></td>
            <td><?php echo ($user['AllowLogin'])?"L":""; ?></td>
            <td><?php echo ($user['Active'])?"A":"I"; ?></td>
            <?php $query_url = urlencode(json_encode(array("ID"=>$user['ID']))); ?>
            <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>crm/user?action=view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/view.png'); ?>"/></button>      
            <?php if (strstr($this->session->userdata['priv'], 'CRM30')) :  ?>
                <button title="Modify" type="button" onclick="window.open('<?php echo base_url();?>crm/user?action=edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
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
        A = Active;  I = Active <br>
        L = Allow-Login
        </small></div></td></tr>
    </tbody>
</table>
