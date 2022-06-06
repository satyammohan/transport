<?php
/* Smarty version 3.1.39, created on 2022-05-29 09:27:25
  from 'C:\xampp\htdocs\transport\templates\rate\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292ef2561d691_10125224',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fcbfbb39a481267ffbf60a0717036b52f99b59fb' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\rate\\edit.tpl.html',
      1 => 1653796643,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292ef2561d691_10125224 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '16124503916292ef255e49f7_48275702';
?>
<form method="post" action="index.php?module=rate&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_rate']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_rate']) {?>Edit<?php } else { ?>Add<?php }?> Rate</legend>
        <table id="dataTable" class="table table-striped table-bordered" width="100%">
            <tr>
                <td><b>Party Name:</b></td>
                <td><select class="form-control" name="rate[id_head]" required>
                        <option>Select Party</option>
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['head']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_head']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td><b>From Area:</b></td>
                <td><select class="form-control" name="rate[id_from_area]" required>
                    <option>Select From Area</option>
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['area']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_from_area']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td><b>To Area:</b></td>
                <td><select class="form-control" name="rate[id_to_area]" required>
                    <option>Select To Area</option>
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['area']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_to_area']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Mode:</b></td>
                <td><select class="form-control" name="rate[id_mode]" required>
                    <option>Select Mode</option>
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['mode']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_mode']),$_smarty_tpl);?>

                </select>
            </td>
            </tr>
            <tr>
                <td><b>Rate:</b></td>
                <td><input type="text" required class="form-control" name="rate[rate]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['rate'];?>
"/>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id ="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_rate'];?>
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
