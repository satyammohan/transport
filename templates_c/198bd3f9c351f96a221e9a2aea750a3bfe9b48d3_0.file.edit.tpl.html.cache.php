<?php
/* Smarty version 3.1.39, created on 2022-05-29 09:25:25
  from 'C:\xampp\htdocs\transport\templates\mode\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292eead001ef3_81290409',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '198bd3f9c351f96a221e9a2aea750a3bfe9b48d3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\mode\\edit.tpl.html',
      1 => 1652607285,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292eead001ef3_81290409 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '11184562446292eeacf365b2_62862550';
?>
<form method="post" action="index.php?module=mode&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_mode']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_mode']) {?>Edit<?php } else { ?>Add<?php }?> Mode</legend>
        <table>
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" class="form-control" name="mode[name]" id="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="40"/>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id ="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_mode'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form> 
<?php }
}
