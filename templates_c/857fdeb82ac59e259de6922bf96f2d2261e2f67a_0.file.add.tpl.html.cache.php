<?php
/* Smarty version 3.1.39, created on 2022-05-29 09:14:33
  from 'C:\xampp\htdocs\transport\templates\group\add.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292ec214e9251_76153440',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '857fdeb82ac59e259de6922bf96f2d2261e2f67a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\group\\add.tpl.html',
      1 => 1653795410,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292ec214e9251_76153440 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '11962088676292ec214c1709_23498991';
?>
<form method="post" action="index.php?module=group&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_group']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_group']) {?>Edit<?php } else { ?>Add<?php }?> Group</legend>
        <table>
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" required class="form-control" name="group[name]" id="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Parent:</b></td>
                <td><select name="group[id_parent]" required class="form-control">
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['group']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_parent']),$_smarty_tpl);?>
 
                    </select>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id ="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_group'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form><?php }
}
