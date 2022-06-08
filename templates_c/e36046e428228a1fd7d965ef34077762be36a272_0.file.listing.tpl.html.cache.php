<?php
/* Smarty version 3.1.39, created on 2022-06-08 05:39:57
  from 'C:\xampp\htdocs\transport\templates\vowner\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629fe8d5435a94_82615989',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e36046e428228a1fd7d965ef34077762be36a272' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\vowner\\listing.tpl.html',
      1 => 1653798360,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629fe8d5435a94_82615989 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '87664049629fe8d541e740_61920514';
?>
<h3>Vehicle Owner Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>    
        <tr> 
            <th colspan="5"></th>
            <th><a title="Add Vehicle Owner" href="index.php?module=vowner&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
        <tr>
            <th>Name</th><th>Address</th><th>Address1</th><th>Regi.Date</th><th>Pan</th><th>&nbsp;</th>
        </tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['vowner']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>			
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['address'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['address1'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['registration_date'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['pan'];?>
</td>
        <td><a title="Edit Vehicle Owner" href="index.php?module=vowner&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_vowner'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Vehicle Owner" href="index.php?module=vowner&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_vowner'];?>
" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
