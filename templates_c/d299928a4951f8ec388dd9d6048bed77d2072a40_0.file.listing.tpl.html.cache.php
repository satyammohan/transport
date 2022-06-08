<?php
/* Smarty version 3.1.39, created on 2022-06-08 05:38:38
  from 'C:\xampp\htdocs\transport\templates\company\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629fe886043df1_49827724',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd299928a4951f8ec388dd9d6048bed77d2072a40' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\company\\listing.tpl.html',
      1 => 1652629933,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629fe886043df1_49827724 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '794407667629fe885f2e184_50644016';
?>
<h3>Company Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>    
        <tr> 
            <th colspan="5">
                <select id="list_status" onchange="update_listing('company', this.value);">
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status_all'],'selected'=>$_REQUEST['status']),$_smarty_tpl);?>

                </select>
            </th>
            <th><a title="Add Company" href="index.php?module=company&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr>
            <th>Name</th><th>Code</th><th>Description</th><th>Address</th><th>Status</th><th>&nbsp;</th>
        </tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['comp']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>			
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['code'];?>
</td>			
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['description'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['address'];?>
</td>
        <td><select onchange="update_status('company', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_company'];?>
', this.value, $('#list_status').val());">
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

            </select></td>
        <td><a title="Edit Company" href="index.php?module=company&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_company'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Company" href="index.php?module=company&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_company'];?>
" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
