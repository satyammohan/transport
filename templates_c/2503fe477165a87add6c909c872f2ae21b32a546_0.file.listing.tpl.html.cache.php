<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:11:28
  from 'C:\xampp\htdocs\transport\templates\vehicle\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292f9784c2e30_54606605',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2503fe477165a87add6c909c872f2ae21b32a546' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\vehicle\\listing.tpl.html',
      1 => 1653798990,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292f9784c2e30_54606605 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '12151299476292f97848d5b5_71521096';
?>
<h3>Vehicle Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="4">
                <select onchange="update_listing('vehicle', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Vehicle" href="index.php?module=vehicle&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Vehicle No.</th><th>Type</th><th>Owner</th><th>Status</th><th>Action</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['vehicle']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['vehno'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['vehtype'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['id_vowner'];?>
</td>
        <td ><select  onchange="update_status('vehicle', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_vehicle'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Vehicle" href="index.php?module=vehicle&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_vehicle'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Vehicle" href="index.php?module=vehicle&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_vehicle'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
