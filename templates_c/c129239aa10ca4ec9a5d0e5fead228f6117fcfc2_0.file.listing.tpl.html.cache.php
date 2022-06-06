<?php
/* Smarty version 3.1.39, created on 2022-05-29 09:25:17
  from 'C:\xampp\htdocs\transport\templates\mode\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292eea55d82a6_04226437',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c129239aa10ca4ec9a5d0e5fead228f6117fcfc2' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\mode\\listing.tpl.html',
      1 => 1652624304,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292eea55d82a6_04226437 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '6687487576292eea55af010_13716857';
?>
<h3>Mode Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="3">
                <select onchange="update_listing('mode', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Mode" href="index.php?module=mode&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Name</th><th>Parent Name</th><th>Status</th><th>&nbsp;</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['mode']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['pname'];?>
</td>
        <td ><select  onchange="update_status('mode', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_mode'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Mode" href="index.php?module=mode&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_mode'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Mode" href="index.php?module=mode&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_mode'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
