<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:57:46
  from 'C:\xampp\htdocs\transport\templates\transport\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629304521c1391_32548313',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0070946529b5ecb28cad7a447fb4b1fd0769e66b' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\transport\\listing.tpl.html',
      1 => 1653802027,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629304521c1391_32548313 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '8473290226293045219ec42_79602640';
?>
<h3>Transport Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="4">
                <select onchange="update_listing('transport', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Transport" href="index.php?module=transport&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Name</th><th>Address</th><th>Phone</th><th>Status</th><th>Action</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['transport']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['address'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['phone'];?>
</td>
        <td ><select  onchange="update_status('transport', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_transport'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Transport" href="index.php?module=transport&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_transport'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Transport" href="index.php?module=transport&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_transport'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
