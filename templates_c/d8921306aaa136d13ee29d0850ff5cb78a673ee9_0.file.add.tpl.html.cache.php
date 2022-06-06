<?php
/* Smarty version 3.1.39, created on 2022-05-16 05:17:17
  from 'C:\xampp\htdocs\transport\templates\head\add.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_62819105677a38_96831597',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd8921306aaa136d13ee29d0850ff5cb78a673ee9' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\head\\add.tpl.html',
      1 => 1630646412,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_62819105677a38_96831597 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),1=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
$_smarty_tpl->compiled->nocache_hash = '712696690628191055e00c5_05558899';
echo '<script'; ?>
 type="text/javascript">
    var fvalid;
    function updatetcs(myapp) {
        if (myapp==0) {
            $("#tcsper").val("").attr("readonly", true);
        } else {
            $("#tcsper").attr("readonly", false);
        }
    }
	function showdet() {
		id = $("#gstin").val();
		url = "index.php?module=head&func=getgstdetails&gstin="+id;
		if (id) {
			$.post( url, function( res ) {
				$("#detail").html(res);
			});
		}
	}
    function form_valid() {
        var fvalid=$("#add_head").validate({
            rules:{
                'head[code]': { required: true },
                gname: { required: true },
                'head[name]': { required: true }
            },
            messages: { 
                'head[code]': { required: "<br/>This Field is Required" },
                gname: { required: "<br/>This Field is Required" },
                'head[name]': { required: "<br/>This Field is Required" }  
            }
        });
        var x = fvalid.form();
        return x;
    }
    function show_detail() {
            if ($("#debtor").attr('checked') || $("#creditor").attr('checked')){
                    $("#sh").show();
            } else {
                    $("#sh").hide();
            }
    }
    $(document).ready(function() {
        callauto("gname", "index.php?module=head&func=getgroup&ce=0", "id_group");
        $(".cst_date").datepicker({
            dateFormat:js_date
        });
        show_detail();
        $("#name").focus();
    });
<?php echo '</script'; ?>
>
<form name="head" id="add_head" method="post" action="index.php?module=head&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_head']) {?>update<?php } else { ?>insert<?php }?>" onsubmit="return form_valid();">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_head']) {?>Edit<?php } else { ?>Add<?php }?> Head</legend>
        <table  border="0" cellspacing="2" cellpadding="2">
            <tr>
                <td valign="top">
                    <fieldset>
                        <table>
                            <tr>
                                <td><b>Code:</b></td>
                                <td><input type="text" name="head[code]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['code'];?>
"/>
                                    <label for="code"></label></td>
                            </tr>
                            <tr>
                                <td><b>Name:</b></td>
                                <td><input type="text" name="head[name]" id="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="30"/>
                                    <label for="name"></label></td>
                            </tr>
                            <tr>
                                <td><b>Group:</b></td>
                                <td> <select name="head[id_group]" id="group">
                                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['group']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_group']),$_smarty_tpl);?>
 
                                    </select></td>
                            </tr>
                            <tr>
                                <td><b>Debtor:</b></td>
                                <td><input type="checkbox" value="1" name="head[debtor]" id="debtor" <?php if ($_smarty_tpl->tpl_vars['data']->value['debtor'] == 1) {?>checked="checked"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['data']->value['debtor'];?>
" onchange="show_detail()" /></td>
                            </tr>
                            <tr>
                                <td><b>Creditor:</b></td>
                                <td><input type="checkbox" value="1" name="head[creditor]" id="creditor" <?php if ($_smarty_tpl->tpl_vars['data']->value['creditor'] == 1) {?>checked="checked"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['data']->value['creditor'];?>
" onchange="show_detail()" /></td>
                            </tr>
                            <tr>
                                <td><b>Address1:</b></td>
                                <td><input type="text"  name="head[address1]" value='<?php echo $_smarty_tpl->tpl_vars['data']->value['address1'];?>
'></td>
                            </tr>
                            <tr>
                                <td><b>Address2:</b></td>
                                <td><input type="text"  name="head[address2]" value='<?php echo $_smarty_tpl->tpl_vars['data']->value['address2'];?>
'></td>
                            </tr>
                            <tr>
                                <td><b>Address3:</b></td>
                                <td><input type="text"  name="head[address3]" value='<?php echo $_smarty_tpl->tpl_vars['data']->value['address3'];?>
'></td>
                            </tr>
                            <tr>
                                <td><b>Opening Balance:</b></td>
                                <td><spam><input type="text" size="12" name="head[opening_balance]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['opening_balance'];?>
"/></spam>
                                    <spam><select name="head[otype]" >
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['balance_type'],'selected'=>$_smarty_tpl->tpl_vars['data']->value['otype']),$_smarty_tpl);?>
 
                                </select></spam>
                            </tr>
<!--
                            <tr>
                                <td><b>Second Opening Balance:</b></td>
                                <td><spam><input type="text" size="12" name="head[sopening_balance]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['sopening_balance'];?>
"/></spam>
                                    <spam><select name="head[sotype]" >
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['balance_type'],'selected'=>$_smarty_tpl->tpl_vars['data']->value['sotype']),$_smarty_tpl);?>
 
                                </select></spam>
                            </tr>-->
                            <tr>
                                <td><b style="color: red">State Code:</b></td>
                                <td><input type="text" name="head[statecode]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['statecode'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b style="color: red">State :</b></td>
                                <td><input type="text" name="head[state]"  value="<?php echo $_smarty_tpl->tpl_vars['data']->value['state'];?>
" /></td>
                            </tr>
                            <tr>
                                <td><b style="color: red">Pin Code:</b></td>
                                <td><input type="text" name="head[pincode]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pincode'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b style="color: red">Location:</b></td>
                                <td><input type="text" name="head[location]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['location'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b style="color: red">GSTIN :</b></td>
                                <td><input type="text" name="head[gstin]" id="gstin"  value="<?php echo $_smarty_tpl->tpl_vars['data']->value['gstin'];?>
"  style="color: red"/><input type="button" onclick="showdet();" value="Check GSTIN"></td>
                            </tr>
                            <tr>
                                <td><b>Pan No:</b></td>
                                <td><input type="text" name="head[panno]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['panno'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Adhar Card:</b></td>
                                <td><input type="text" name="head[adhar]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['adhar'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Tan No:</b></td>
                                <td><input type="text" name="head[tanno]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['tanno'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b style="color: red">TCS Applicable:</b></td>
                                <td><select name="head[tcs]" onchange="updatetcs(this.value)">
                                        <option <?php if ($_smarty_tpl->tpl_vars['data']->value['tcs'] != 1) {?>selected="selected"<?php }?> value="0">No</option>
                                        <option <?php if ($_smarty_tpl->tpl_vars['data']->value['tcs'] == 1) {?>selected="selected"<?php }?> value="1">Yes</option>
                                    </select>
                                    <input <?php if ($_smarty_tpl->tpl_vars['data']->value['tcs'] != 1) {?>readonly="readonly"<?php }?> type="text" style="text-align: right;" name="head[tcsper]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['tcsper'];?>
" size="3" id="tcsper" />%
                                </td>
                            </tr>
                            <tr>
                                <td><b>Date of Birth:</b></td>
                                <td><input type="text" name="head[dob]" class="dtpickno" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['dob'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Date of Anniversary:</b></td>
                                <td><input type="text" name="head[doa]" class="dtpickno" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['doa'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>         
                <?php if (($_smarty_tpl->tpl_vars['data']->value['id_group'] == 18 || $_smarty_tpl->tpl_vars['data']->value['id_group'] == 20)) {?>
                <td><?php } else { ?>
                <td id="sh" style="display: none; float: right">
                    <?php }?> <fieldset>
                        <table>
                            <tr>
                                <td><b>Dealer Type:</b></td>
                                <td><select id="dealer" name="head[dealer]" >
                                     <option value="0">Distributor/Retailer</option>
                                     <option value="1">Super-Distributor</option>
                                     <option value="2">Modern Trade</option>
                                    </select>
                                </td>
                                <?php echo '<script'; ?>
>
                                    $("#dealer").val(<?php echo $_smarty_tpl->tpl_vars['data']->value['dealer'];?>
);
                                <?php echo '</script'; ?>
>
                            </tr>     
                            <tr>
                                <td><b>Vat :</b></td>
                                <td><select name="head[vattype]" id="vattype">
                                        <option name="Tin">Tin</option>
                                        <option name="Srin">Srin</option>
                                    </select>
                                    <?php echo '<script'; ?>
 type="text/javascript">
                                        $("#vattype").val("<?php echo $_smarty_tpl->tpl_vars['data']->value['vattype'];?>
");
                                    <?php echo '</script'; ?>
>
                                    <input type="text" name="head[vatno]"  value="<?php echo $_smarty_tpl->tpl_vars['data']->value['vatno'];?>
"/>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Area:</b></td>
                                <td>  <select name="head[id_area]" id="area">
                                        <option>Select</option>
                                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['area']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_area']),$_smarty_tpl);?>
 
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Transport:</b></td>
                                <td>
                                    <input type="text" name="head[transport]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['transport'];?>
"/>
				    <!--<select name="head[id_transport]" id="area">
                                        <option> Select</option>
                                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['transport']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_transport']),$_smarty_tpl);?>
 
                                    </select>-->
                                </td>
                            </tr>
                            <tr>
                                <td><b>Station:</b></td>
                                <td><input type="text" name="head[station]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['station'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Banker:</b></td>
                                <td><input type="text" name="head[banker]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['banker'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>A/C No:</b></td>
                                <td><input type="text" name="head[acno]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['acno'];?>
"/>
                            </tr>
                            <tr>
                                <td><b>IFSC code:</b></td>
                                <td><input type="text" name="head[acifsc]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['acifsc'];?>
"/>
                            </tr>
                            <tr>
                                <td><b>Name of Account Holder:</b></td>
                                <td><input type="text" name="head[acname]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['acname'];?>
"/>
                            </tr>
                            <tr>
                                <td><b>Account Type:</b></td>
                                <td><input type="text" name="head[actype]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['actype'];?>
"/>
                            </tr>
                            <tr>
                                <td><b>Drug Licence #:</b></td>
                                <td><input type="text" name="head[dlicence]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['dlicence'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Food Licence #:</b></td>
                                <td><input type="text" name="head[flicence]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['flicence'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Contact Person:</b></td>
                                <td><input type="text" name="head[contact_person]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['contact_person'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Phone:</b></td>
                                <td><input type="text" name="head[phone]"  value="<?php echo $_smarty_tpl->tpl_vars['data']->value['phone'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Mobile:</b></td>
                                <td><input type="text" name="head[mobile]"  value="<?php echo $_smarty_tpl->tpl_vars['data']->value['mobile'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>E-mail:</b></td>
                                <td><input type="text" name="head[email]"  value="<?php echo $_smarty_tpl->tpl_vars['data']->value['email'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Credit Limits:</b></td>
                                <td><input type="text" name="head[credit_limit]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['credit_limit'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Credit Days:</b></td>
                                <td><input type="text" name="head[credit_days]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['credit_days'];?>
"/></td>
                            </tr>
                            <tr>
                                <td><b>Local:</b></td>
                                <td> <select name="head[local]" id="area">
                                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['local'],'selected'=>$_smarty_tpl->tpl_vars['data']->value['local']),$_smarty_tpl);?>
 
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
		<td><div id="detail"></div></td>
            </tr>

            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_head'];?>
">
                    <input type="submit" id="submit" value="Submit" />
                    <input type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form>
<?php }
}
