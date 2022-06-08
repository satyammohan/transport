<?php
/* Smarty version 3.1.39, created on 2022-06-08 05:38:29
  from 'C:\xampp\htdocs\transport\templates\product\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629fe87d7abd22_50523259',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2f7c6d61341a1cf06e0a3f601dc09d5570600289' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\product\\listing.tpl.html',
      1 => 1653799776,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629fe87d7abd22_50523259 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '193177895629fe87d73d834_93515195';
?>
<h3>Product Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr> 
            <th colspan="3">
                <select onchange="update_listing('product', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Product" href="index.php?module=product&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr><th>Name</th><th>Converstion</th><th>Status</th><th>Action</th></tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['convert'];?>
</td>
        <td ><select  onchange="update_status('product', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_product'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select>
        </td>
        <td><a title="Edit Product" href="index.php?module=product&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_product'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Product" href="index.php?module=product&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_product'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
