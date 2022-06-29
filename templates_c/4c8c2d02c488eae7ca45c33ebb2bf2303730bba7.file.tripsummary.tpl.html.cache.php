<?php /* Smarty version Smarty-3.1.21-dev, created on 2022-06-11 09:05:26
         compiled from "templates/report/tripsummary.tpl.html" */ ?>
<?php /*%%SmartyHeaderCode:20336989162a40d7eb7a666-88416007%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4c8c2d02c488eae7ca45c33ebb2bf2303730bba7' => 
    array (
      0 => 'templates/report/tripsummary.tpl.html',
      1 => 1654918146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20336989162a40d7eb7a666-88416007',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'company' => 0,
    'area' => 0,
    'data' => 0,
    's' => 0,
    'x' => 0,
    'weight' => 0,
    'qty' => 0,
    'freight' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d7ebbf039_63118821',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d7ebbf039_63118821')) {function content_62a40d7ebbf039_63118821($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/var/www/html/Smarty-3/libs/plugins/function.html_options.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/html/Smarty-3/libs/plugins/modifier.date_format.php';
?><link href="js/jquery.multi-select.css" media="screen" rel="stylesheet" type="text/css">
<?php echo '<script'; ?>
 src="js/jquery.multi-select.js" type="text/javascript"><?php echo '</script'; ?>
>
<!-- http://loudev.com/#project -->
<fieldset>
    <legend><h3>Trip Summary<hr></h3></legend>
    <form method="post">
        <table>
            <tr>
                <td>Start</td>
                <td><input type="date" name="start_date" size="9" placeholder="dd-mm-yyyy" value='<?php echo $_REQUEST['start_date'];?>
' /></td>
                <td>End</td>
                <td><input type="date" name="end_date" size="9"  placeholder="dd-mm-yyyy" value='<?php echo $_REQUEST['end_date'];?>
' /></td>
                <td>Vehicle Type</td>
                <td><input type="text" name="type" size="1" value='<?php echo $_REQUEST['type'];?>
' /></td>
                <td>Loading Charges</td>
                <td><input type="text" name="charge" size="1" value='<?php if ($_REQUEST['charge']) {
echo $_REQUEST['charge'];
} else { ?>25<?php }?>' /></td>
                <td>Ton Between</td>
                <td><input type="text" name="start_ton" size="2" value='<?php echo $_REQUEST['start_ton'];?>
' /></td>
                <td><input type="text" name="end_ton" size="2" value='<?php echo $_REQUEST['end_ton'];?>
' /></td>
                <td><input type="submit" value="Go" />
                    <input type="button" class="print" value="Print" />
                    <input type="hidden" id="excelfile" value="tripsummary" />
                    <input type="button" class="excel" value="Download" title="Download as Excel" />
                </td>
            </tr>
            <tr>
                <td>Company</td>
                <td><select id="company" name="company" multiple="multiple" size='4'>
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['company']->value),$_smarty_tpl);?>
 
                    </select>
                </td>
                <td>Area</td>
                <td><select id="area" name="area" multiple="multiple" size='4'>
                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['area']->value),$_smarty_tpl);?>

                </select>
                </td>
                <td>Type</td>
                <td><select name="type">
                    <option value="actual">Actual</option>
                    <option value="freight">Freight-wise</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
<br />
<div class="print_content">
    <?php echo $_SESSION['cname'];?>
 <?php echo $_SESSION['fyear'];?>
<br>
    Trip Summary Period <?php echo smarty_modifier_date_format($_REQUEST['start_date'],"%d-%m-%Y");?>
 - <?php echo smarty_modifier_date_format($_REQUEST['end_date'],"%d-%m-%Y");?>
<br />
    <table id='report' border="1">
        <tr>
            <th>Sl</th><th>Date</th><th>Company</th><th>LC No.</th><th>Destination</th><th>Lrno</th><th>Inv. No.</th><th>Date</th>
            <th>Weight</th><th>Qty</th><th>Freight</th><th colspan="2">Del. Ack.</th>
        </tr>
        <?php $_smarty_tpl->tpl_vars['weight'] = new Smarty_variable(0, null, 0);
$_smarty_tpl->tpl_vars['qty'] = new Smarty_variable(0, null, 0);
$_smarty_tpl->tpl_vars['freight'] = new Smarty_variable(0, null, 0);?>
        <?php $_smarty_tpl->tpl_vars['s'] = new Smarty_variable(1, null, 0);?>
        <?php  $_smarty_tpl->tpl_vars['x'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['x']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['x']->key => $_smarty_tpl->tpl_vars['x']->value) {
$_smarty_tpl->tpl_vars['x']->_loop = true;
?>
            <tr>
                <td><?php echo $_smarty_tpl->tpl_vars['s']->value;
$_smarty_tpl->tpl_vars['s'] = new Smarty_variable($_smarty_tpl->tpl_vars['s']->value+1, null, 0);?></td>
                <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['x']->value['date'],"%d-%m-%Y");?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['x']->value['cname'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['x']->value['no'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['x']->value['aname'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['x']->value['lrno'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['x']->value['bno'];?>
</td>
                <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['x']->value['bnodate'],"%d-%m-%Y");?>
</td>
                <td align="right"><?php echo $_smarty_tpl->tpl_vars['x']->value['weight'];?>
</td>
                <td align="right"><?php echo sprintf("%.0f",$_smarty_tpl->tpl_vars['x']->value['qty']);?>
</td>
                <td align="right"><?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['x']->value['freight']);?>
</td>
                <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['x']->value['ddate'],"%d-%m-%Y");?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['x']->value['ack'];?>
</td>
                <?php $_smarty_tpl->tpl_vars['weight'] = new Smarty_variable($_smarty_tpl->tpl_vars['weight']->value+$_smarty_tpl->tpl_vars['x']->value['weight'], null, 0);?>
                <?php $_smarty_tpl->tpl_vars['qty'] = new Smarty_variable($_smarty_tpl->tpl_vars['qty']->value+$_smarty_tpl->tpl_vars['x']->value['qty'], null, 0);?>
                <?php $_smarty_tpl->tpl_vars['freight'] = new Smarty_variable($_smarty_tpl->tpl_vars['freight']->value+$_smarty_tpl->tpl_vars['x']->value['freight'], null, 0);?>
            </tr>
        <?php } ?>
        <tr>
            <th colspan="8">Total :</th>
            <td align="right"><b><?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['weight']->value);?>
</b></td>
            <td align="right"><b><?php echo sprintf("%.0f",$_smarty_tpl->tpl_vars['qty']->value);?>
</b></td>
            <td align="right"><b><?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['freight']->value);?>
</b></td>
        </tr>
    </table>
</div>
<?php echo '<script'; ?>
>
$(document).ready(function () {
    $('#area').multiSelect();
    $('#company').multiSelect();
});
<?php echo '</script'; ?>
>
<style>
.ms-container .ms-selectable
{
   height: 50px !important;
}
</style><?php }} ?>
