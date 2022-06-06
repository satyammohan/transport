<?php
/* Smarty version 3.1.39, created on 2022-06-01 07:06:44
  from 'C:\xampp\htdocs\transport\templates\challan\add.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6296c2ac53c731_06286364',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd6d8ae17823d963942c1b160d333e2ed5147c783' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\challan\\add.tpl.html',
      1 => 1616986690,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:common/generaljs.tpl.html' => 1,
  ),
),false)) {
function content_6296c2ac53c731_06286364 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),1=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),2=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_radios.php','function'=>'smarty_function_html_radios',),));
$_smarty_tpl->compiled->nocache_hash = '1419131666296c2abe3f6c2_03793290';
$_smarty_tpl->_assignInScope('jsurl', ((string)$_smarty_tpl->tpl_vars['source']->value)."jsmodule/sale.js?".((string)time()));
echo '<script'; ?>
>
    var taxrates = <?php echo $_smarty_tpl->tpl_vars['taxrates']->value;?>
;
<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jsurl']->value;?>
"><?php echo '</script'; ?>
>
<?php $_smarty_tpl->_subTemplateRender('file:common/generaljs.tpl.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div id="salemessage"></div>
<?php if ($_SESSION['url']) {?>
    <iframe src="<?php echo htmlspecialchars_decode($_SESSION['url'], ENT_QUOTES);?>
" height="0px" width="0px" style="display:none"></iframe>
<?php }?>
<form  method="post" action="" name="sales" id="sales" autocomplete="off">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['sdata']->value['id_sale']) {?>Edit<?php } else { ?>Add<?php }?> Sales Bill</legend>
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="25%"><b>Type&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Invoice No</b></td>
                            <td><b>Date</b></td>
                            <td><b>Challan No</b></td>
                            <td><b>Challan Date</b></td>
                        </tr>
                        <tr>
                            <td>
                                <select id="taxtype" name="sales[taxbill]" <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['id_sale']) {?> onchange="getlastbill()"<?php }?>>
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['tax_type'],'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['taxbill']),$_smarty_tpl);?>

                                </select>
                                <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['id_sale']) {?><select name="series" id="series" ><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['series']->value,'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['id_series']),$_smarty_tpl);?>
</select><?php }?>
				<input type="text" name="sales[invno]" size="12" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['invno'];?>
"  <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['id_sale']) {?> id="inv" <?php }?>/><br><span id="msgbox"></span>
			    </td>
                            <td>
                                <?php if ((isset($_smarty_tpl->tpl_vars['sdata']->value['date']))) {?>
                                    <?php $_smarty_tpl->_assignInScope('date', $_smarty_tpl->tpl_vars['sdata']->value['date']);?>
                                <?php } else { ?>
                                    <?php if (smarty_modifier_date_format(time(),'%y/%m/%d') >= smarty_modifier_date_format($_SESSION['sdate'],'%y/%m/%d') && smarty_modifier_date_format(time(),'%y/%m/%d') <= smarty_modifier_date_format($_SESSION['edate'],'%y/%m/%d')) {?>
                                        <?php if ($_SESSION['current_sale_date']) {?>
                                            <?php $_smarty_tpl->_assignInScope('date', $_SESSION['current_sale_date']);?>
                                        <?php } else { ?>
                                            <?php $_smarty_tpl->_assignInScope('date', time());?>
                                        <?php }?>
                                    <?php } else { ?>
                                        <?php $_smarty_tpl->_assignInScope('date', $_SESSION['edate']);?>
                                    <?php }?>
                                <?php }?>

                                <input type="text" name='sales[date]' class="dtpick" id="date" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date']->value,$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/>
                            </td>
                            <td><input type="text" name="sales[challan_no]"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['challan_no'];?>
"/></td>
                            <td><input type="text" name="sales[challan_date]" class="dtpick"  value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value['challan_date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/></td>
                        </tr>
                        <tr>
                            <td><b>Company Name</b></td>
                            <td><b>Party</b></td>
                            <td><b>Area</b></td>
                            <td><b>Representative</b></td>
                        </tr>
                        <tr>
                            <td><select name="sales[id_company]" id="id_company">
                                    <option value="">All Company</option>
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['company']->value,'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['id_company']),$_smarty_tpl);?>

                                </select></td>
                            <td><input type="text" name="sales[party_name]"  id="party" size="50"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['party_name'];?>
" onblur="checkpartybalance();" />
				<input type="button" onclick="editparty()" value="Edit">
                                <br><span id="valid_party"></span>
                                <input type="hidden" name="sales[id_head]" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['id_head'];?>
" id="id_head"/>
                            </td>
                            <td><select name="sales[id_area]" id="id_area" onblur="changerep(this.id)">
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['area']->value,'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['id_area']),$_smarty_tpl);?>

                                </select></td>
                            <td><select name="sales[id_represent]" id="id_represent">
                                    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['salesman']->value,'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['id_represent']),$_smarty_tpl);?>

                                </select></td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($_smarty_tpl->tpl_vars['sdata']->value) {
$_smarty_tpl->_assignInScope('select', $_smarty_tpl->tpl_vars['sdata']->value['cash']);?> <?php } else { ?> <?php $_smarty_tpl->_assignInScope('select', 1);?> <?php }?>
                                <?php echo smarty_function_html_radios(array('name'=>'sales[cash]','options'=>$_smarty_tpl->tpl_vars['ini']->value['tran_type'],'selected'=>$_smarty_tpl->tpl_vars['select']->value),$_smarty_tpl);?>

                            </td>
                            <td colspan="3">
                                <table id="partydetail">
                                    <tr><td><b>Address1</b></td><td><b>Address2</b></td><td><b>GST No</b></td></tr>
                                    <tr>
                                        <td><input type="text" name="sales[party_address]" style="border:none;" tabindex="-1" id="paddr" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['party_address'];?>
"></td>
                                        <td><input type="text" name="sales[party_address1]"  style="border:none;" tabindex="-1" id="paddr2" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['party_address1'];?>
"></td>
                                    <input type="hidden" name="sales[party_vattype]" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['party_vattype'];?>
" id="vattype"/>
                                    <td valign="top">
                                        <input type="text" name="sales[party_vatno]"  style="border:none;"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['party_vatno'];?>
" id="vatno"/>
                                        <input type="hidden" id="dealer" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['dealer'];?>
" />
                                    </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <input type="button" onclick="checkpartyos();" href="#" tabindex="-1" value="Party Outstanding" >
                                <!-- <input type="button" onclick="checkpartybalance();" href="#" tabindex="-1" value="Check Party Balance" > -->
                                <span id="party_message" style="color: red"></span>
                                &nbsp;&nbsp;<blink><b><span id="message"></span></b></blink>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>  
        </table>
        </td>
        </tr>
        <tr>
            <td colspan="2">
		<div style="height:210px; overflow:auto;">
		    <table width="100%" class="btable">
			<thead>
			    <tr>
                                <th>&nbsp;</th><th>Item Details</th><th>Batch</th><th>Rate</th><th>Quantity</th><th>Free</th><th>Amount</th><th>Discount</th><th>Spl.Disc.</th><th>C.D.</th><th>GST</th><th>CESS %</th><th>Report</th>
			    </tr>
			</thead>
			<tbody id="mtable">
			    <tr class="tabRow">
                                <td><a onclick="removeRow(this);" href="#" tabindex="-1" ><img src="<?php echo $_smarty_tpl->tpl_vars['source']->value;?>
images/close.png" /></a></td>
                                <td>
				    <input class="drop" type="text" name="items[]" id="item__1" style="width:210px" size="35" onblur="getValues();getbatch(this.id);" required="required"/>
                                    <br>Stock : <input type="text" readonly="readonly" onfocus="jumpnext(this.id, 'batch_no__');" tabindex="-1" size="10" name="balance[]" id="balance__1" />
				    <input type="hidden" name="id_product[]" id="id_product__1" />
				    <input type="hidden" name="discount_amount1[]" id="discount_amount1__1" />
				    <input type="hidden" name="discount_amount2[]" id="discount_amount2__1" />
				    <input type="hidden" name="discount_amount3[]" id="discount_amount3__1" />
				    <input type="hidden" name="discount_amount4[]" id="discount_amount4__1" />
                                    <input type="hidden" id="itemcase__1"  />
				</td>
				<td><input class="drop" type="text" name="batch_no[]" id="batch_no__1" size="10" onfocus='$("#" + this.id).autocomplete("search", "a")' />
				    <input type="hidden" name="id_batch[]" id="id_batch__1" />
				    <input type="text" name="exp_date[]" onfocus="jumpnext(this.id, 'rate__');" tabindex="-1" id="exp_date__1" size="6" />
				</td>
				<td><input type="text" size="5" name="rate[]" id="rate__1"  tabindex="-1" class="ra" onblur="getValues();" /></td>
				<td><input type="text" size="5" name="quantity[]" onblur="getbonus(this.id); getValues(); getdiscount();" id="qty__1" required="required" class="ra"/></td>
				<td><input type="text" size="5" name="free[]" id="free__1" class="ra"/></td>
				<td><input type="text" size="5" name="amount[]" tabindex="-1" readonly="readonly" id="amount__1" class="ra"/></td>
				<td><input type="text" size="5" name="discount1[]" onblur="getValues()" id="discount1__1" class="ra"/>
				    <select name="discount_type1[]" onchange="getValues()" id="discount_type1__1" class="select" tabindex=-1>
					<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['discount']),$_smarty_tpl);?>

				    </select>
				</td>
				<td><input type="text" size="5" name="discount2[]" onblur="getValues()" id="discount2__1" class="ra"/>
				    <select name="discount_type2[]" onchange="getValues()"  id="discount_type2__1" class="select" tabindex=-1>
					<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['discount']),$_smarty_tpl);?>

				    </select>
				</td>
				<td><input type="text" size="5" name="discount3[]" id="discount3__1" onblur="getValues()" class="ra" />
				    <select name="discount_type3[]" onchange="getValues()" id="discount_type3__1" class="select" tabindex=-1>
					<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ini']->value['discount']),$_smarty_tpl);?>

				    </select>
				</td>
    				<td>
                                    <select name="id_taxmaster[]" id="id_taxmaster__1" onchange="getTaxRates(this.id);getValues()"  class="select" tabindex=-1>
					<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['tax']->value),$_smarty_tpl);?>

				    </select>
                                    <input type="hidden" name="tax_per[]" id="tax_per__1"  />
				    <input type="hidden" name="goods_amount[]" id="goods_amount__1" />
				    <input type="text"  name="tax_amount[]" tabindex="-1" id="taxamt__1" readonly="readonly" size="4" class="ra" />
				</td>
				<td>
                                    <input type="text" size="4" name="cess[]" id="cess__1" onblur="getValues();rowadd(this, 'mtable');" class="ra" />
				    <input type="text"  name="cessamt[]" tabindex="-1" id="cessamt__1" readonly="readonly" size="4" class="ra" />
				</td>
                                <td width="60px">
				    <a href="#" id="history__1" onclick="showid(this.id);"  title="Item Sale History"><img src="<?php echo $_smarty_tpl->tpl_vars['source']->value;?>
images/report1.png" alt="Sale" width="15" height="15"></a>
				    <a href="#" id="phistory__1" onclick="showparty(this.id);" title="Item Party History" ><img src="<?php echo $_smarty_tpl->tpl_vars['source']->value;?>
images/report1.png" alt="Sale" width="15" height="15"></a>
				    <a href="#" id="purhistory__1" onclick="purchase(this.id);"  title="Item Purchase History"><img src="<?php echo $_smarty_tpl->tpl_vars['source']->value;?>
images/report1.png" alt="Sale" width="15" height="15"></a>
				</td>
			    </tr>
			</tbody>
		    </table>
    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td  valign="top" width="70%" align="left">
                <label><input type="checkbox" name="sales[transport]" value="transport" id="tran"   onclick="doInputs(this, this.value)" <?php if ($_smarty_tpl->tpl_vars['sdata']->value['is_transport'] && $_smarty_tpl->tpl_vars['sdata']->value['is_transport'] == '1') {?> checked="checked" <?php }?>/>Transport</label><br>
                <table id="transport" <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['is_transport'] || $_smarty_tpl->tpl_vars['sdata']->value['is_transport'] == '0') {?> style="display: none" <?php }?>>
                       <tr>
                        <th>LR No<br><input type="text" name="sales[lr_no]"  size="10" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['lr_no'];?>
" id="lrno"/></th>
                        <th>LR Date<br><input type="text" name="sales[lr_date]" class="dtpick"  size="10" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value['lr_date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/></th>
                        <th>Cases<br><input type="text" name="sales[cases]"  size="10" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['cases'];?>
"/></th>
                        <th>Weight<br><input type="text" name="sales[weight]"  size="10" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['weight'];?>
"/></th>
                        <th>Transport<br><select name="sales[id_transport]" >
                                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['transport']->value,'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['id_transport']),$_smarty_tpl);?>

                            </select></th>
                        <th>Station<br><input type="text" name="sales[station]"  size="10" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['station'];?>
"/></th>
                    </tr>
                </table>
                <br />
                <label><input type="checkbox" name="sales[payment]" value="payment"  id="pay"  onclick="doInputs(this, this.value)" <?php if ($_smarty_tpl->tpl_vars['sdata']->value['is_payment'] && $_smarty_tpl->tpl_vars['sdata']->value['is_payment'] == '1') {?> checked="checked" <?php }?>/>Payment</label>
                <br/>
                <table id="payment" <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['is_payment'] || $_smarty_tpl->tpl_vars['sdata']->value['is_payment'] == '0') {?> style="display: none" <?php }?> >
                       <tr>
                        <th>Cheque No<br><input type="text" name="sales[cheque_no]"  size="10" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['cheque_no'];?>
"/></th>
                        <th>Date<br><input type="text" name="sales[cheque_date]"  size="10" class="dtpick" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value['cheque_date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/></th>
                        <th>Bank<br><input type="text" name="sales[bank]"  size="20" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['bank'];?>
"/></th>
                        <th>Amount<br><input type="text" name="sales[cheque_amount]" size="10" class="ra"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['cheque_amount'];?>
"/></th>
                    </tr>
                </table>
                <br />
                <label><input type="checkbox" name="sales[form]" value="frm1"  id="frm" onclick="doInputs(this, this.value)" <?php if ($_smarty_tpl->tpl_vars['sdata']->value['is_form'] && $_smarty_tpl->tpl_vars['sdata']->value['is_form'] == '1') {?> checked="checked" <?php }?>/>Form</label>
                <br/>
                <table id="frm1"  <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['is_form'] || $_smarty_tpl->tpl_vars['sdata']->value['is_form'] == '0') {?> style="display: none" <?php }?>>
                       <tr>
                        <th>Form Type<br>
                            <select name="sales[id_form]" id="id_form">
                                <option value="">-Select Form-</option>
                                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['frm']->value,'selected'=>$_smarty_tpl->tpl_vars['sdata']->value['id_form']),$_smarty_tpl);?>

                            </select>
			    <input type="hidden" name="sales[form_type]" id="form_type" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['form_type'];?>
"/>
			</th>
			<th>Form No<br><input type="text" name="sales[form_no]"  size="20"  maxlength="15" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['form_no'];?>
"/></th>
			<th>Form Date<br><input type="text" name="sales[form_date]"  size="20" class="dtpick"  value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value['form_date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"/></th>
			<th>Form Amount<br><input type="text" name="sales[form_amount]" size="10" class="ra" id="frm_amt"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['form_amount'];?>
"/></th>
		    </tr>
		</table>
		<br/>
		<label><input type="checkbox" name="sales[waybill]" value="wbill"  id="wb" onclick="doInputs(this, this.value)" <?php if ($_smarty_tpl->tpl_vars['sdata']->value['is_waybill'] && $_smarty_tpl->tpl_vars['sdata']->value['is_waybill'] == '1') {?> checked="checked" <?php }?>/>E-Waybill</label>
		<br/>
                <table id="wbill"  <?php if (!$_smarty_tpl->tpl_vars['sdata']->value['is_waybill'] || $_smarty_tpl->tpl_vars['sdata']->value['is_waybill'] == '0') {?> style="display: none" <?php }?>>
		       <tr>
			<th>E-Waybill No<br><input type="text" name="sales[waybill_no]"  size="10" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['waybill_no'];?>
"/></th>
			<th>E-Waybill Date<br><input type="text" name="sales[waybill_date]"  size="20" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['sdata']->value['waybill_date'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
"  class="dtpick"/></th>
			<th>E-Waybill Amount<br><input type="text" name="sales[waybill_amount]" size="10" class="ra"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['waybill_amount'];?>
"/></th>
		    </tr>
		</table>  
                <br />
                <label><input type="checkbox" name="sales[vehicle]" value="1" onclick="doInputs(this, 'vehicle')" <?php if ($_smarty_tpl->tpl_vars['sdata']->value['vehicle'] == '1') {?> checked="checked" <?php }?> />Vehicle Details</label>
                <br/>
                <table id="vehicle" <?php if ($_smarty_tpl->tpl_vars['sdata']->value['vehicle'] != '1') {?> style="display: none" <?php }?>>
                       <tr>
                        <th>Contact Person<br><input type="text" name="sales[vehicle_contact]" size="30" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['vehicle_contact'];?>
"/></th>
                        <th>Vehicle Number<br><input type="text" name="sales[vehicle_number]" size="20" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['vehicle_number'];?>
"/></th>
                        <th>Amount<br><input type="text" name="sales[vehicle_amount]" size="10" class="ra"  value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['vehicle_amount'];?>
"/></th>
                    </tr>
                </table>
	    </td>
	    <td valign="top" width="70%" >
		<table>
		    <tr>
			<td  align="right" ><b>Goods Value:</b></td>
			<td><input type="text" name="sales[totalamt]" id="totalamt" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['totalamt'];?>
" readonly/></td>
		    </tr>
		    <tr>
			<td  align="right" ><b>Discount:</b></td>
			<td><input type="text" name="sales[discount]" id="tdiscount" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['discount'];?>
" readonly/></td>
		    </tr>
		    <tr>
			<td  align="right"><b>GST:</b></td>
			<td><input type="text" name="sales[vat]" id="vat" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['vat'];?>
" readonly/></td>
		    </tr>
		    <tr>
			<td  align="right"><b>CESS Amount:</b></td>
                        <td><input type="text" name="sales[totalcess]" id="totalcess" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['totalcess'];?>
" readonly/></td>
		    </tr>
		    <tr>
			<td  align="right"><b>Less:</b></td>
			<td><input type="text" name="sales[less]" id="less" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['less'];?>
" onblur="getValues()"/></td>
		    </tr>
		    <tr>
			<td  align="right"><b>Packing & Forwarding:</b></td>
			<td><input type="text" name="sales[packing]" id="pfrd" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['packing'];?>
" onblur="getValues()"/></td>
		    </tr>
		    <tr>
                        <td  align="right"><b><input type="checkbox" onclick="roundbill()">Round off:</b></td>
			<td><input type="text" name="sales[round]" id="roundof" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['round'];?>
" onblur="getValues()" /></td>
		    </tr>
	    <tr>
                <td align="right">
                    <input type="button" onclick="findtcs()" value="Get TCS">
                    <b>TCS:<input type="text" name="sales[tcsper]" id="tcsper" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['tcsper'];?>
" size="3" onblur="getValues()"/>%</td></b></td>
                <td><input type="text" name="sales[tcsamt]" id="tcsamt" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['tcsamt'];?>
" onblur="getValues()" tabindex="-1" readonly/></td>
            </tr>
            <tr>
   		<td align="right"><b>Total:</b></td>
                <td>
                    <input type="text" name="sales[total]" id="total" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['total'];?>
" readonly/>
                    <input type="text" id="prev_total" class="ra" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['total'];?>
" style="display: none;"/>
                </td>
		    </tr>
		    <tr>
			    <td align="right"><b>Description:</b></td>
			    <td><textarea name="sales[memo]" cols="40" rows="4"><?php echo $_smarty_tpl->tpl_vars['sdata']->value['memo'];?>
</textarea></td>
		    </tr>  
        </table>
        <div id="sales_till_now" style="color: red;font: 18px sans-serif;"></div>
	</td>
        </tr>
        <tr>
            <td align="center" colspan="3">
		<div style="color: red;font: 18px sans-serif;">Click on Get TCS to enable Save this Bill.</div>
                <br>
                <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['sdata']->value['id_sale'];?>
" id="sale_id" />
                <input type="hidden" name="order_id" value="<?php echo $_REQUEST['order_id'];?>
" id='order_id' />
                <input type="button" value="Save" disabled id="sub" style="cursor: pointer" name="save"/>
                <input type="button" value="Save & Print" disabled id="print" style=" cursor: pointer" name="print"/>
            </td>
        </tr>
        </table>
    </fieldset>
</form>
<?php }
}
