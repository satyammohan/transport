<?php
/* Smarty version 3.1.39, created on 2022-05-29 09:56:53
  from 'C:\xampp\htdocs\transport\templates\vowner\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292f60d37b964_68150188',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '900d0f385eafdb6d8bc57df82073455e7a4d8bdc' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\vowner\\edit.tpl.html',
      1 => 1653798406,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292f60d37b964_68150188 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '4081036756292f60d344519_98846593';
?>
<form method="post" action="index.php?module=vowner&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_vowner']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_vowner']) {?>Edit<?php } else { ?>Add<?php }?> Vehicle Owner</legend>
        <table border="0" cellspacing="2" cellpadding="2">
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" required name="comp[name]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="40"/></td>
            </tr>
            <tr>
                <td><b>Address:</b></td>
                <td><input type="text" name="comp[address]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address'];?>
" size="40"/></td>
            </tr>
            <tr>
                <td><b>Address1:</b></td>
                <td><input type="text" name="comp[address]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address1'];?>
" size="40"/></td>
            </tr>
            <tr>
                <td><b>Regi.Date:</b></td>
                <td><input type="date" name="comp[registration_date]" class="form-control" required value="<?php echo $_smarty_tpl->tpl_vars['data']->value['registration_date'];?>
"/></td>
            </tr>
            <tr>
                <td><b>Pan:</b></td>
                <td><input type="text" name="comp[pan]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pan'];?>
" size="40"/></td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_vowner'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form><?php }
}
