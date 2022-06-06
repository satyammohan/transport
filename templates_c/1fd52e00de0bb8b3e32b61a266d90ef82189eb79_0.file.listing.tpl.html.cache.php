<?php
/* Smarty version 3.1.39, created on 2022-06-05 07:45:05
  from 'C:\xampp\htdocs\transport\templates\challan\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629c11a951ece5_16257251',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1fd52e00de0bb8b3e32b61a266d90ef82189eb79' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\challan\\listing.tpl.html',
      1 => 1654395292,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629c11a951ece5_16257251 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
$_smarty_tpl->compiled->nocache_hash = '1356617763629c11a9502cf4_25591600';
echo '<script'; ?>
 type="text/javascript">
    $(function(){
        $('#id_sale').click(function(){
            var val = [];
            $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });
            if(val==''){
                alert('There is no record selected for Report!');
            }else{
                //window.open('index.php?module=sales&func=prsale&id='+val+'&ce=0','_new','scrollbars=yes,resizable=yes,width=720,height=450,top=50,left=250');
                this.href='index.php?module=sales&func=prsale&id='+val+'&ce=0';
            }
        });
    });
<?php echo '</script'; ?>
>    
<h3>Challan cum Consignment Note<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>    
        <tr>
            <td colspan="18" valign="top">
                <form method="post">
                    Start Date:<input type="date" name="start_date" value='<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value[$_smarty_tpl->tpl_vars['rec']->value]['date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
' />
                    End Date:<input type="date" name="end_date" value='<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value[0]['date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
' />
                    <input type="submit" value="Go" />
                </form>
            </td>
        </tr>
        <tr>
            <th>Sl.</th><th>No.</th><th>Date</th><th>Items</th><th>Party</th><th>Truck Number.</th>
            <th>
                Manage
                <a title="Add Challan" href="index.php?module=challan&func=edit"></a>                
            </th>
        </tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sdata']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <!--<td><?php echo $_smarty_tpl->tpl_vars['ini']->value['tax_type'][$_smarty_tpl->tpl_vars['mod']->value['taxbill']];?>
</td>-->
        <td><?php if ($_smarty_tpl->tpl_vars['mod']->value['ack']) {?>*<?php } else { ?>--<?php }?></td>
    	<td><?php echo $_smarty_tpl->tpl_vars['mod']->value['no'];?>
</td>
        <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['mod']->value['date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['items'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['party_name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['truck'];?>
</td>
        <td>
            <a href="index.php?module=sales&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_sale'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><div title="Delete" class="delete_bg">&nbsp;</div></a>
            <a href="index.php?module=sales&func=prsale&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_sale'];?>
&ce=0"><div title="Print Bill" class="report_bg">&nbsp;</div></a>&nbsp;
            <a href="index.php?module=sales&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_sale'];?>
"><div title="Edit" class="edit_bg">&nbsp;</div></a>
            <input class="checkbox" type="checkbox" name="id_sale[]" value="<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_sale'];?>
" />
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
