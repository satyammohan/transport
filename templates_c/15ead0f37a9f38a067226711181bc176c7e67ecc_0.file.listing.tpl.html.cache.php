<?php
/* Smarty version 3.1.39, created on 2022-06-01 07:28:43
  from 'C:\xampp\htdocs\transport\templates\group\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6296c7d30b5519_44407012',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '15ead0f37a9f38a067226711181bc176c7e67ecc' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\group\\listing.tpl.html',
      1 => 1652624248,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6296c7d30b5519_44407012 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '8766672886296c7d3091949_33843077';
?>
<h3>Group Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="3">
                <select onchange="update_listing('group', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Group" href="index.php?module=group&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Name</th><th>Parent Name</th><th>Status</th><th>&nbsp;</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['group']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['pname'];?>
</td>
        <td ><select  onchange="update_status('group', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_group'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Group" href="index.php?module=group&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_group'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Group" href="index.php?module=group&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_group'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
