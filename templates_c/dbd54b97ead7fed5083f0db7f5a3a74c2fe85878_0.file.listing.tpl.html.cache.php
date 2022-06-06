<?php
/* Smarty version 3.1.39, created on 2022-05-29 09:28:04
  from 'C:\xampp\htdocs\transport\templates\rate\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292ef4c7fa1e7_91099377',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dbd54b97ead7fed5083f0db7f5a3a74c2fe85878' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\rate\\listing.tpl.html',
      1 => 1653792801,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292ef4c7fa1e7_91099377 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '3464474486292ef4c7c9952_79926993';
?>
<h3>Rate Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="6">
                <select onchange="update_listing('rate', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Rate" href="index.php?module=rate&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Party Name</th><th>From Area</th><th>To Area</th><th>Mode</th><th>Rate</th><th>Status</th><th>Action</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['rate']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['head']->value[$_smarty_tpl->tpl_vars['mod']->value['id_head']];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['area']->value[$_smarty_tpl->tpl_vars['mod']->value['id_from_area']];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['area']->value[$_smarty_tpl->tpl_vars['mod']->value['id_to_area']];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mode']->value[$_smarty_tpl->tpl_vars['mod']->value['id_mode']];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['rate'];?>
</td>
        <td><select  onchange="update_status('rate', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_rate'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Rate" href="index.php?module=rate&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_rate'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Rate" href="index.php?module=rate&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_rate'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
