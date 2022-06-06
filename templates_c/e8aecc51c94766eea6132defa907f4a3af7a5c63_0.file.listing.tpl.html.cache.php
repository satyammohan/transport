<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:44:59
  from 'C:\xampp\htdocs\transport\templates\item\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629301533e9629_79141080',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e8aecc51c94766eea6132defa907f4a3af7a5c63' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\item\\listing.tpl.html',
      1 => 1653800872,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629301533e9629_79141080 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '2046269346629301533b3bb8_78854593';
?>
<h3>Item Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="6">
                <select onchange="update_listing('item', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Item" href="index.php?module=item&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Name</th><th>Short</th><th>Weight</th><th>Product</th><th>Company</th><th>Status</th><th>Action</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['short'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['weight'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['product']->value[$_smarty_tpl->tpl_vars['mod']->value['id_product']];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['company']->value[$_smarty_tpl->tpl_vars['mod']->value['id_company']];?>
</td>
        <td ><select  onchange="update_status('item', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_item'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Item" href="index.php?module=item&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_item'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Item" href="index.php?module=item&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_item'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
