<?php
/* Smarty version 3.1.39, created on 2022-06-08 05:37:41
  from 'C:\xampp\htdocs\transport\templates\area\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629fe84deb6258_42096809',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b350011a1108a52bd7337aae08e151e8e922f2e3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\area\\listing.tpl.html',
      1 => 1653795937,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629fe84deb6258_42096809 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '462226723629fe84de589d1_62127019';
?>
<h3>Area Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="4">
                 <select  id="list_status" onchange="update_listing('area', this.value);">
                     <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                 </select>
            </th>
            <th><a title="Add Area" href="index.php?module=area&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr>
            <th>Area Name</th><th>Distance</th><th>Freight</th><th>Status</th><th>&nbsp;</th>
        </tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['area']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['distance'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['freight'];?>
</td>
        <td ><select onchange="update_status('area', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_area'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select></td>
        <td>
            <a title="Edit Area" href="index.php?module=area&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_area'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Area" href="index.php?module=area&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_area'];?>
" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
