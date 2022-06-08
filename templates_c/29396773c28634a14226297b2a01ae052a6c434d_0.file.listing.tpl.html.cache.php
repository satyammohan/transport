<?php
/* Smarty version 3.1.39, created on 2022-06-08 05:37:27
  from 'C:\xampp\htdocs\transport\templates\head\listing.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629fe83f66ac37_92905044',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '29396773c28634a14226297b2a01ae052a6c434d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\head\\listing.tpl.html',
      1 => 1652630076,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629fe83f66ac37_92905044 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '80397196629fe83f5d9954_92057520';
?>
<h3>Head Master<hr></h3>
<table id="dataTable" class="table table-striped table-bordered" width="100%">
    <thead>
        <tr>
            <th>Name</th><th>Group</th><th>Address</th><th>GSTin/Tin</th><th>Phone</br>E-mail</th><th>Area</th><th>Opening Balance Type</th><th><a title="Add Head" href="index.php?module=head&func=edit"><i class="fa fa-plus fa-2x"></i></a></th>
        </tr>
    </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['head']->value, 'mod');
$_smarty_tpl->tpl_vars['mod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->do_else = false;
?>
    <tr>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['gname'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['address1'];?>
<br>
            <?php echo $_smarty_tpl->tpl_vars['mod']->value['address2'];?>
<br>
            <?php echo $_smarty_tpl->tpl_vars['mod']->value['address3'];?>
<br><?php echo $_smarty_tpl->tpl_vars['mod']->value['state'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['gstin'];?>
<br><?php echo $_smarty_tpl->tpl_vars['mod']->value['vattype'];?>
-<?php echo $_smarty_tpl->tpl_vars['mod']->value['vatno'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['phone'];?>
</br><?php echo $_smarty_tpl->tpl_vars['mod']->value['mobile'];?>
</br><?php echo $_smarty_tpl->tpl_vars['mod']->value['email'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['mod']->value['area'];?>
</td>
        <td align="right"><?php echo $_smarty_tpl->tpl_vars['mod']->value['opening_balance'];?>
&nbsp;<?php if ($_smarty_tpl->tpl_vars['mod']->value['otype'] == 0) {?>Db<?php } else { ?>Cr<?php }?></td>
        <td><select onchange="update_status('head', '<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_head'];?>
', this.value,$('#list_status').val());">
            <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['status'],'selected'=>$_smarty_tpl->tpl_vars['mod']->value['status']),$_smarty_tpl);?>

        </select>
            <a title="Edit Head" href="index.php?module=head&func=edit&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_head'];?>
"><i class="fa fa-edit fa-2x"></i></a>
            <a title="Delete Head" href="index.php?module=head&func=delete&id=<?php echo $_smarty_tpl->tpl_vars['mod']->value['id_head'];?>
&ce=0" onclick="return confirm('Do you want to delete?');"><i class="fa fa-trash fa-2x"></i></a></td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
