<?php /*%%SmartyHeaderCode:114833110662a40d7eb13292-27708351%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7c79b65fa3287688723eaffbef7a2bab32041f3a' => 
    array (
      0 => 'templates/common/container.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
    '9cb8aa65e50982f4fcf257fe07357a53d779a6d8' => 
    array (
      0 => 'templates/common/header.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
    '788af11a266deb0197e4739cb9b89f12d82980fa' => 
    array (
      0 => 'templates/common/menu.tpl.html',
      1 => 1654918146,
      2 => 'file',
    ),
    '4c8c2d02c488eae7ca45c33ebb2bf2303730bba7' => 
    array (
      0 => 'templates/report/tripsummary.tpl.html',
      1 => 1654918146,
      2 => 'file',
    ),
    'c98be0640752f1bcc7c08e8f493ef7cefee8be69' => 
    array (
      0 => 'templates/common/footer.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '114833110662a40d7eb13292-27708351',
  'variables' => 
  array (
    'smarty_date' => 0,
    'ini' => 0,
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d7ebc9772_32061343',
  'cache_lifetime' => 3600,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d7ebc9772_32061343')) {function content_62a40d7ebc9772_32061343($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <title>Transport Management System
                            :: &nbsp;MAHANADI C H&nbsp;(Apr  1, 2022-Mar 31, 2023)
                    </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
        <link rel="stylesheet" href="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css">

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

		<link rel="stylesheet" href="js/jquery.mobile-1.4.5/demos/_assets/css/jqm-demos.css">
		<script src="js/jquery.mobile-1.4.5/demos/_assets/js/index.js"></script>

        <link rel="stylesheet" href="css/common.css">
        <script type="text/javascript" src="js/jqprint.js"></script>
        <script type="text/javascript" src="js/table2excel.js"></script>
        <script src="js/common.js?time=1"></script>

                <script language="javascript" type="text/javascript">
            var js_date = "dd/mm/yy";
            var sdate = "01/04/2022";
            var edate = "31/03/2023";
        </script>
    </head>
    <body>
                            <div class="row w-100">
                <div class="col-12">
                    <div class="header">
  <nav class="navbar fixed-top" style="padding: 0">
    <div class="container-fluid mt-2 mb-2">
      <div class="col-lg-12 text-white">
        <div class="float-left" >
          <b>Welcome Admin</b>
        </div>
        <div class="float-right">
            <a href="#" class="text-white">
              <span data-target=".bs-logout-modal-sm" data-toggle="modal"><i class="fa fa-power-off"></i></span>
            </a>  
        </div>
        <div class="text-center">
            <font size='+1'>MAHANADI C H</font>&nbsp;
            01/04/2022-31/03/2023
        </div>
      </div>
    </div>
  </nav>
</div>

                </div>
            </div>
                <div class="row grow w-100 h-100">
                            <div class="col-2 h-100">
                    <br><br>
                    <div class="sidenav col-2">
  <a class="home active" href="index.php"><i class="fa fa-home"></i> Home</a>
  <button class="dropdown-btn group head area mode rate company vowner vehicle product item transport"><i class="fa fa-bar-chart-o"></i> Master
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a class="group"     href="index.php?module=group&func=listing"><i class="fa fa-group"></i> Group</a>
    <a class="head"      href="index.php?module=head&func=listing"><i class="fa fa-address-book"></i> Head</a>
    <a class="area"      href="index.php?module=area&func=listing"><i class="fa fa-circle"></i> Area</a>
    <a class="mode"      href="index.php?module=mode&func=listing"><i class="fa fa-circle"></i> Mode</a>
    <a class="rate"      href="index.php?module=rate&func=listing"><i class="fa fa-rupee"></i> Rate</a>
    <a class="company"   href="index.php?module=company&func=listing"><i class="fa fa-circle"></i> Company</a>
    <a class="vowner"    href="index.php?module=vowner&func=listing"><i class="fa fa-truck"></i> Vehicle Owner</a>
    <a class="vehicle"   href="index.php?module=vehicle&func=listing"><i class="fa fa-truck"></i> Vehicle</a>
    <a class="product"   href="index.php?module=product&func=listing"><i class="fa fa-circle"></i> Product</a>
    <a class="item"      href="index.php?module=item&func=listing"><i class="fa fa-circle"></i> Item</a>
    <a class="transport" href="index.php?module=transport&func=listing"><i class="fa fa-truck"></i> Transport</a>
  </div>

  <button class="dropdown-btn banquet"><i class="fa fa-file-o"></i> Bill
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=bill&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=bill&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>

  <button class="dropdown-btn consignment"><i class="fa fa-truck"></i> Consignment
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=consignment&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=consignment&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>
  <button class="dropdown-btn challan"><i class="fa fa-truck"></i> Challan-Consignment Note
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=challan&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=challan&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>

  <button class="dropdown-btn banquet"><i class="fa fa-truck"></i> Freight
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=consignment&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=consignment&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>

  <button class="dropdown-btn report reportexl shortage"><i class="fa fa-bar-chart-o"></i> Reports
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">  
    <button class="dropdown-btn report"><i class="fa fa-bar-chart-o"></i> Consignment
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="report" href="index.php?module=report&func=vehicledetail"><i class="fa fa-bar-chart-o"></i> Vehicle Detail</a>
      <a class="report" href="index.php?module=report&func=companydetail"><i class="fa fa-bar-chart-o"></i> Company Detail</a>
      <a class="report" href="index.php?module=report&func=areadetail"><i class="fa fa-bar-chart-o"></i> Area Detail</a>
      <a class="report" href="index.php?module=report&func=pendingtruckfreight"><i class="fa fa-bar-chart-o"></i> Pending Truck Freight</a>
      <a class="report" href="index.php?module=report&func=pendingfreight"><i class="fa fa-bar-chart-o"></i> Pending Freight</a>
      <a class="report" href="index.php?module=report&func=ackdue"><i class="fa fa-bar-chart-o"></i> Acknowledgement Due</a>
      
      <a class="report" href="index.php?module=report&func=shortagedetail"><i class="fa fa-bar-chart-o"></i> Shorage Detail</a>
      <a class="report" href="index.php?module=report&func=despatchregister"><i class="fa fa-bar-chart-o"></i> Despatch Register</a>
      <a class="report" href="index.php?module=report&func=tripsummary"><i class="fa fa-bar-chart-o"></i> Trip Summary</a>
      
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> Balance Payment Detail</a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> TDS Statement</a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>
      <a class="report" href="index.php?module=report&func="><i class="fa fa-bar-chart-o"></i> </a>


<!---
DEFINE BAR 1 OF _rtj0iam84 PROMPT "\<Vehicle Detail"
DEFINE BAR 2 OF _rtj0iam84 PROMPT "\<Company Detail"
DEFINE BAR 3 OF _rtj0iam84 PROMPT "\<Area Detail"
DEFINE BAR 4 OF _rtj0iam84 PROMPT "Pending \<Truck Freight"
DEFINE BAR 5 OF _rtj0iam84 PROMPT "\<Pending Freight"
DEFINE BAR 6 OF _rtj0iam84 PROMPT "Ac\<knowledgement Due"
DEFINE BAR 7 OF _rtj0iam84 PROMPT "S\<hort Detail"
DEFINE BAR 8 OF _rtj0iam84 PROMPT "\<Despatch Register"
DEFINE BAR 9 OF _rtj0iam84 PROMPT "Trip S\<ummary"
DEFINE BAR 13 OF _rtj0iam84 PROMPT "\<Special"
DEFINE BAR 14 OF _rtj0iam84 PROMPT "\<Balance payment Detail"
DEFINE BAR 15 OF _rtj0iam84 PROMPT "TDS Statement"
DEFINE BAR 16 OF _rtj0iam84 PROMPT "Balance payment (Amit)"
DEFINE BAR 17 OF _rtj0iam84 PROMPT "Advance payment (Amit)"
DEFINE BAR 18 OF _rtj0iam84 PROMPT "Payment Detail"
DEFINE BAR 19 OF _rtj0iam84 PROMPT "Che\<que Statement"
ON SELECTION BAR 1 OF _rtj0iam84 DO vehdet
ON SELECTION BAR 2 OF _rtj0iam84 DO compdet
ON SELECTION BAR 3 OF _rtj0iam84 DO areadet
ON SELECTION BAR 4 OF _rtj0iam84 DO pendfrtr
ON SELECTION BAR 5 OF _rtj0iam84 DO pendfr
ON SELECTION BAR 6 OF _rtj0iam84 DO ackdue
ON SELECTION BAR 7 OF _rtj0iam84 DO shortdet
ON SELECTION BAR 8 OF _rtj0iam84 DO desreg
ON SELECTION BAR 9 OF _rtj0iam84 DO tripsum
ON BAR 13 OF _rtj0iam84 ACTIVATE POPUP special
ON SELECTION BAR 14 OF _rtj0iam84 DO baldet
ON SELECTION BAR 15 OF _rtj0iam84 DO tdsstm
ON SELECTION BAR 16 OF _rtj0iam84 DO balpay WITH "B"
ON SELECTION BAR 17 OF _rtj0iam84 DO balpay WITH "A"
ON BAR 18 OF _rtj0iam84 ACTIVATE POPUP paymentdet
ON SELECTION BAR 19 OF _rtj0iam84 DO chqstm

DEFINE POPUP special MARGIN RELATIVE SHADOW COLOR SCHEME 4
DEFINE BAR 1 OF special PROMPT "\<Trip Report"
DEFINE BAR 2 OF special PROMPT "\<Weight Report"
DEFINE BAR 3 OF special PROMPT "\<Extra Point"
ON SELECTION BAR 1 OF special DO triprep
ON SELECTION BAR 2 OF special DO weigrep
ON SELECTION BAR 3 OF special DO points

DEFINE POPUP paymentdet MARGIN RELATIVE SHADOW COLOR SCHEME 4
DEFINE BAR 1 OF paymentdet PROMPT "\<Advance (Cash)"
DEFINE BAR 2 OF paymentdet PROMPT "Advance (Che\<que)"
DEFINE BAR 3 OF paymentdet PROMPT "\<Balance (Cash)"
DEFINE BAR 4 OF paymentdet PROMPT "Balance (Cheq\<ue)"
DEFINE BAR 5 OF paymentdet PROMPT "\<Fuel"
DEFINE BAR 6 OF paymentdet PROMPT "\<Tankwise"
DEFINE BAR 7 OF paymentdet PROMPT "\<1. Balance Summary"
DEFINE BAR 8 OF paymentdet PROMPT "\<2. Balance Summary Owner"
DEFINE BAR 9 OF paymentdet PROMPT "\<3. Single day"
DEFINE BAR 10 OF paymentdet PROMPT "\<4. No Pancard"
ON SELECTION BAR 1 OF paymentdet DO paydet WITH "A", "C"
ON SELECTION BAR 2 OF paymentdet DO paydet WITH "A", "Q"
ON SELECTION BAR 3 OF paymentdet DO paydet WITH "B", "C"
ON SELECTION BAR 4 OF paymentdet DO paydet WITH "B", "Q"
ON SELECTION BAR 5 OF paymentdet DO paydet WITH "F"
ON SELECTION BAR 6 OF paymentdet DO tankdet
ON SELECTION BAR 7 OF paymentdet DO paydets WITH "B"
ON SELECTION BAR 8 OF paymentdet DO paydets WITH "O"
ON SELECTION BAR 9 OF paymentdet DO repsday
ON SELECTION BAR 10 OF paymentdet DO repnopan

DEFINE POPUP excel MARGIN RELATIVE SHADOW COLOR SCHEME 4
DEFINE BAR 1 OF excel PROMPT "\<Despatch Register"
DEFINE BAR 2 OF excel PROMPT "\<Balance Payment Detail"
ON SELECTION BAR 1 OF excel DO desrege
ON SELECTION BAR 2 OF excel DO paydete

DEFINE POPUP shortageda MARGIN RELATIVE SHADOW COLOR SCHEME 4
DEFINE BAR 1 OF shortageda PROMPT "\<Shortage/Damage Statement"
DEFINE BAR 2 OF shortageda PROMPT "Shortage/Damage Statement \<Reconcile"
ON SELECTION BAR 1 OF shortageda DO shortdar
ON SELECTION BAR 2 OF shortageda DO shortdae

DEFINE POPUP ownvehicle MARGIN RELATIVE SHADOW COLOR SCHEME 4
DEFINE BAR 1 OF ownvehicle PROMPT "\<Vechile Detail"
DEFINE BAR 2 OF ownvehicle PROMPT "\<Datewise Detail"
DEFINE BAR 3 OF ownvehicle PROMPT "\<Typewise Detail"
DEFINE BAR 4 OF ownvehicle PROMPT "\<Areawise Detail"
ON SELECTION BAR 1 OF ownvehicle DO ovvrep
ON SELECTION BAR 2 OF ownvehicle DO ovdrep
ON SELECTION BAR 3 OF ownvehicle DO ovtrep
ON SELECTION BAR 4 OF ownvehicle DO ovarep

DEFINE POPUP account MARGIN RELATIVE SHADOW COLOR SCHEME 4
DEFINE BAR 1 OF account PROMPT "\<Journal"
DEFINE BAR 2 OF account PROMPT "\<Ledger"
DEFINE BAR 3 OF account PROMPT "Bank \<Reconcilation"
DEFINE BAR 4 OF account PROMPT "\<Outstanding"
DEFINE BAR 5 OF account PROMPT "\<Trial Balance"
DEFINE BAR 6 OF account PROMPT "\<Profit & Loss"
DEFINE BAR 7 OF account PROMPT "\<Balance Sheet"
DEFINE BAR 8 OF account PROMPT "R\<eceipt & Payment"
ON SELECTION BAR 2 OF account DO ledger
ON SELECTION BAR 3 OF account DO reconst
ON SELECTION BAR 4 OF account DO bwise
ON SELECTION BAR 5 OF account DO trial
ON SELECTION BAR 6 OF account DO bsheet WITH "P"
ON SELECTION BAR 7 OF account DO bsheet WITH "B"
ON SELECTION BAR 8 OF account DO receipt
-->

    </div>
    <button class="dropdown-btn reportexl"><i class="fa fa-bar-chart-o"></i> Excel
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="reportexl" href="index.php?module=excel&func=depatch"><i class="fa fa-bar-chart-o"></i> Despatch Register</a>
      <a class="reportexl" href="index.php?module=excel&func=balance"><i class="fa fa-bar-chart-o"></i> Balance Payment Detail</a>
    </div>
    <button class="dropdown-btn shortage"><i class="fa fa-bar-chart-o"></i> Shortage/Damage
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="shortage" href="index.php?module=shortage&func=statement"><i class="fa fa-bar-chart-o"></i> Statement</a>
      <a class="shortage" href="index.php?module=shortage&func=reconcile"><i class="fa fa-bar-chart-o"></i> Reconcile</a>
    </div>
  </div>

  <button class="dropdown-btn backup restore"><i class="fa fa-gear"></i> Setting
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a class="group" href="index.php?module=util&func=listing"><i class="fa fa-hdd-o"></i> Backup</a>
    <a class="group" href="index.php?module=util&func=import"><i class="fa fa-window-restore"></i> Restore from Neuron</a>
  </div>

  
  <a href="index.php?module=user&func=selectfy"><i class="fa fa-sign-out"></i> Exit</a>
  <a href="#" class="fa fa-sign-out">
    <span class="menu-text" data-target=".bs-logout-modal-sm" data-toggle="modal">Logout</span>
  </a>
  <br>
  <br>
  <br>
</div>


<script>
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;
for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}
var showactive = "report";
$(document).ready(function() {
    $("."+showactive).trigger('click').addClass("active");
});
</script>

<style>  
.navbar {
  background-color: #09192A !important;
}
.sidenav {
  height: 100%;
  width: 100%;
  font-size: 13px;
  position: fixed;
  z-index: 1;
  top: 30px;
  bottom: 60px;
  left: 0;
  background-color:#09192A;
  overflow-x: hidden;
  padding-top: 20px;
}
.sidenav a, .dropdown-btn {
  padding: 6px 8px 6px 10px;
  text-decoration: none;
  color: #818181;
  display: block;
  border: none;
  background: none;
  width:100%;
  text-align: left;
  cursor: pointer;
  outline: none;
}
.sidenav a:hover, .dropdown-btn:hover {
  color: #f1f1f1;
}
.active {
  background-color: #225081;
  color: white;
}
.dropdown-container {
  display: none;
  padding-left: 8px;
}
.fa-caret-down {
  float: right;
  padding-right: 8px;
}
</style>
                </div>
                <div class="col-10 w-100 h-100"  style="left: 20px !important;">
                    <br><br><br>
                    <link href="js/jquery.multi-select.css" media="screen" rel="stylesheet" type="text/css">
<script src="js/jquery.multi-select.js" type="text/javascript"></script>
<!-- http://loudev.com/#project -->
<fieldset>
    <legend><h3>Trip Summary<hr></h3></legend>
    <form method="post">
        <table>
            <tr>
                <td>Start</td>
                <td><input type="date" name="start_date" size="9" placeholder="dd-mm-yyyy" value='2022-06-01' /></td>
                <td>End</td>
                <td><input type="date" name="end_date" size="9"  placeholder="dd-mm-yyyy" value='2022-06-11' /></td>
                <td>Vehicle Type</td>
                <td><input type="text" name="type" size="1" value='' /></td>
                <td>Loading Charges</td>
                <td><input type="text" name="charge" size="1" value='25' /></td>
                <td>Ton Between</td>
                <td><input type="text" name="start_ton" size="2" value='' /></td>
                <td><input type="text" name="end_ton" size="2" value='' /></td>
                <td><input type="submit" value="Go" />
                    <input type="button" class="print" value="Print" />
                    <input type="hidden" id="excelfile" value="tripsummary" />
                    <input type="button" class="excel" value="Download" title="Download as Excel" />
                </td>
            </tr>
            <tr>
                <td>Company</td>
                <td><select id="company" name="company" multiple="multiple" size='4'>
                    <option value="32">*0</option>
<option value="34">*1</option>
<option value="35">*2</option>
<option value="36">*3</option>
<option value="38">*4</option>
<option value="26">*ATTA</option>
<option value="27">*DALDA &amp; FLORA</option>
<option value="28">*DETERGENT</option>
<option value="29">*FREEGIFT</option>
<option value="30">*KISHAN</option>
<option value="31">*LAKHMY</option>
<option value="33">*MANE LINE</option>
<option value="37">*PERSONAL PROUDUCT</option>
<option value="39">*SALT</option>
<option value="40">*TEA &amp; COFFY</option>
<option value="41">*TEA &amp; FOODS</option>
<option value="42">5</option>
<option value="50">ADANI WILMER LIMITED</option>
<option value="51">ADITIONAL STOCK SIFTING</option>
<option value="54">ASSIAN PAINTS</option>
<option value="4">ATTA</option>
<option value="44">ATTA &amp; FOODS</option>
<option value="5">ATTA,SALT &amp; RICE</option>
<option value="53">AWL MFS FRT</option>
<option value="60">AWL STOCK TRN</option>
<option value="55">BAR&amp;PP GOODS</option>
<option value="7">DALDA</option>
<option value="8">DALDA &amp; FLORA</option>
<option value="49">DE.GENT O.CLEAN DD.</option>
<option value="57">DETERGENT&amp;PP GOODS</option>
<option value="45">EVEREADY INDIA LTD.</option>
<option value="48">FR GIFT CYCLE</option>
<option value="25">FREE GIFT</option>
<option value="59">HORLICKS</option>
<option value="12">KISSAN</option>
<option value="13">LAKME</option>
<option value="14">MAIN LINE</option>
<option value="16">MAIN LINE &amp; FOODS</option>
<option value="15">MAIN LINE &amp; PP</option>
<option value="17">MAIN LINE &amp; TEA</option>
<option value="19">MAIN LINE,PP &amp; FOODS</option>
<option value="18">MAIN LINE,PP &amp; TEA</option>
<option value="20">MAIN LINE,PP,TEA &amp; FOODS</option>
<option value="43">ML,FOODS,TEA</option>
<option value="1">ORICLEAN</option>
<option value="3">ORICLEAN DIRECT DESPATCH</option>
<option value="47">POWER CELL</option>
<option value="21">PP</option>
<option value="23">PP &amp; FOODS</option>
<option value="22">PP &amp; TEA</option>
<option value="24">PP,TEA &amp; FOODS</option>
<option value="6">SALT</option>
<option value="2">SIFTING</option>
<option value="56">SOAP&amp;PP GOODS</option>
<option value="52">TAKE BACK</option>
<option value="9">TEA</option>
<option value="10">TEA &amp; COFFEE</option>
<option value="11">TEA &amp; FOODS</option>
<option value="46">WATER</option>
<option value="58">WATER PURIFIER</option>
 
                    </select>
                </td>
                <td>Area</td>
                <td><select id="area" name="area" multiple="multiple" size='4'>
                    <option value="263"></option>
<option value="97">A.ROAD</option>
<option value="184">ADASPUR</option>
<option value="230">AIGINIA,KHANDAGIRI</option>
<option value="132">AKOLA</option>
<option value="205">AMARDAROAD</option>
<option value="80">ANANDPUR</option>
<option value="45">ANGUL</option>
<option value="73">ASKA</option>
<option value="115">ATABIRA</option>
<option value="46">ATHAGARH</option>
<option value="220">ATHAMALIK</option>
<option value="238">BAGADHARIA</option>
<option value="256">BAGADIA</option>
<option value="186">BALANGA</option>
<option value="21">BALANGIR</option>
<option value="6">BALESORE</option>
<option value="178">BALIAPALA</option>
<option value="179">BALICHANDARPUR</option>
<option value="235">BALICHANDRAPUR</option>
<option value="216">BALIGUDA</option>
<option value="26">BALIKUDA</option>
<option value="8">BALUGAON</option>
<option value="209">BANAMALIPUR</option>
<option value="233">BANDALO</option>
<option value="145">BANGALORE</option>
<option value="102">BANKI</option>
<option value="175">BARAMBA</option>
<option value="192">BARANASI</option>
<option value="87">BARANG</option>
<option value="249">BARANPAL</option>
<option value="89">BARBIL</option>
<option value="267">BARGAON</option>
<option value="59">BARGARH</option>
<option value="244">BARGARH-ACC</option>
<option value="52">BARIPADA</option>
<option value="35">BARPALI</option>
<option value="231">BASANTIA</option>
<option value="129">BASTA</option>
<option value="154">BASUDEV PUR</option>
<option value="250">BBSR-SAHO</option>
<option value="251">BBSR-SONU</option>
<option value="243">BBSR-TAPS</option>
<option value="252">BBSR=DAS</option>
<option value="242">BBSR=HD</option>
<option value="136">BELGACHIA</option>
<option value="197">BELLAGUNTHA</option>
<option value="43">BELPAHAR</option>
<option value="18">BERHAMPUR</option>
<option value="166">BETADA</option>
<option value="24">BH.PATNA</option>
<option value="5">BHADRAK</option>
<option value="100">BHANJANAGAR</option>
<option value="110">BHANPUR</option>
<option value="223">BHATLI</option>
<option value="108">BHOUDRAJ</option>
<option value="183">BHUBAN</option>
<option value="3">BHUBANESWAR</option>
<option value="188">BIDYADHAR PUR</option>
<option value="156">BIRA MAHARAJ PUR</option>
<option value="96">BIRMITRAPUR</option>
<option value="160">BISHNUPUR</option>
<option value="150">BISOI</option>
<option value="34">BISRA</option>
<option value="149">BISSAM CUTTACK</option>
<option value="19">BOINDA</option>
<option value="265">BONAIGARH</option>
<option value="221">BONGOMUNDA</option>
<option value="48">BOUDH</option>
<option value="50">BRAGRAJNAGAR</option>
<option value="190">BUDHARAJ</option>
<option value="61">BUGUDA</option>
<option value="79">BURLA</option>
<option value="146">CDA</option>
<option value="105">CHAMPUA</option>
<option value="135">CHAMRAIL</option>
<option value="210">CHANDAKA</option>
<option value="47">CHANDANPUR</option>
<option value="174">CHANDANSWAR</option>
<option value="69">CHANDBALI</option>
<option value="107">CHANDIKHOL</option>
<option value="214">CHANDPUR</option>
<option value="72">CHANDPUR</option>
<option value="181">CHATIA</option>
<option value="90">CHATRAPUR</option>
<option value="196">CHENNAI</option>
<option value="22">CHERUPALI</option>
<option value="140">CHERUPALI</option>
<option value="67">CHIKITI</option>
<option value="66">CHOUDWAR</option>
<option value="4">CUTTACK</option>
<option value="239">CUTTACK-JD</option>
<option value="114">DAMANJODI</option>
<option value="253">DANKUNI</option>
<option value="63">DASPALLA</option>
<option value="169">DELANG</option>
<option value="255">DELHI</option>
<option value="53">DEOGARH</option>
<option value="193">DHAMNAGAR</option>
<option value="151">DHARMGARH</option>
<option value="93">DHENKANAL</option>
<option value="219">DHULIA,BOMBAY</option>
<option value="139">DIGAPAHANDI</option>
<option value="182">DUBURI</option>
<option value="227">G UDAYAGIRI</option>
<option value="215">GADASILA</option>
<option value="112">GANDARPUR</option>
<option value="137">GANJAM</option>
<option value="264">GHASIPUR</option>
<option value="234">GHATAGAON</option>
<option value="44">GOPALPUR</option>
<option value="148">GUDARI</option>
<option value="82">GUNUPUR</option>
<option value="254">GURGAON</option>
<option value="111">HANDAPA</option>
<option value="163">HARIPUR HAT</option>
<option value="232">HINDOL</option>
<option value="84">HINJILCUT</option>
<option value="212">HIRAKUDA</option>
<option value="172">ITAMATI</option>
<option value="30">J.ROAD</option>
<option value="31">J.TOWN(MANGALPUR)</option>
<option value="240">JABALPUR</option>
<option value="125">JAGATPUR</option>
<option value="71">JAGATSINGPUR</option>
<option value="152">JAIPATNA</option>
<option value="98">JALESWAR</option>
<option value="119">JAMSEDPUR</option>
<option value="236">JANHA</option>
<option value="237">JANHA(J.ROAD)</option>
<option value="128">JARKA</option>
<option value="176">JASHIPUR</option>
<option value="54">JATNI</option>
<option value="25">JEYPORE</option>
<option value="14">JHARSUGUDA</option>
<option value="167">JHUMPURA</option>
<option value="88">JODA</option>
<option value="83">JUNAGARH</option>
<option value="15">K.NAGAR</option>
<option value="130">KABISURYA NAGAR</option>
<option value="258">KALAHANDI</option>
<option value="162">KALAPATHAR</option>
<option value="191">KALYAN SINGNPUR</option>
<option value="200">KALYANISINGH PUR</option>
<option value="101">KANTABANJHI</option>
<option value="28">KANTAPALI</option>
<option value="40">KARANJIA</option>
<option value="2">KENDRAPARA</option>
<option value="9">KEONJHAR</option>
<option value="29">KESHPUR</option>
<option value="57">KESINGA</option>
<option value="194">KHALIKOTE</option>
<option value="229">KHANDAGIRI</option>
<option value="78">KHANDAPARA</option>
<option value="217">KHARIAR</option>
<option value="58">KHARIAR ROAD</option>
<option value="77">KHETRAJPUR</option>
<option value="11">KHURDA</option>
<option value="124">KHURDA REL</option>
<option value="222">KINJIRKELA</option>
<option value="257">KOIRA</option>
<option value="133">KOLKATTA</option>
<option value="85">KONARK</option>
<option value="74">KORAPUT</option>
<option value="226">KOSALA</option>
<option value="104">KOTHAPETI</option>
<option value="138">KUAKHIA</option>
<option value="36">KUCHINDA</option>
<option value="203">KUDALA</option>
<option value="94">KUJANG</option>
<option value="99">KUPARI</option>
<option value="42">KUTRA</option>
<option value="127">LEMALA</option>
<option value="113">LOCAL TRIP</option>
<option value="213">LOISINGHA</option>
<option value="168">MACHHAGAON</option>
<option value="259">MADHYA PRADESH</option>
<option value="134">MALDA</option>
<option value="86">MALKANGIRI</option>
<option value="142">MANGULI</option>
<option value="91">MARSHAGHAI</option>
<option value="248">MERA MUNDALI</option>
<option value="147">MUNIGUDA</option>
<option value="187">NARADA</option>
<option value="204">NARAJ</option>
<option value="51">NAYAGARH</option>
<option value="70">NAYAHAT</option>
<option value="198">NAYASADAK CUTTACK.</option>
<option value="56">NEMAPARA</option>
<option value="106">NIALI</option>
<option value="177">NILAGIRI</option>
<option value="120">NIRAKARPUR</option>
<option value="49">NOWRANGPUR</option>
<option value="55">NUAPATNA</option>
<option value="171">ODAGOAN</option>
<option value="165">OLAVAR</option>
<option value="126">ORANDA</option>
<option value="208">ORICLEAN</option>
<option value="117">PADAMPUR</option>
<option value="224">PAIKAMAL</option>
<option value="33">PALLAHARA</option>
<option value="60">PANIKOILI</option>
<option value="218">PAPADAHANDI</option>
<option value="95">PARADEEP</option>
<option value="81">PARLAKHIMUNDI</option>
<option value="144">PATNA</option>
<option value="37">PATNAGARH</option>
<option value="68">PATTAMUNDAI</option>
<option value="27">PHULBANI</option>
<option value="12">PIPILI</option>
<option value="153">PIRHAT</option>
<option value="195">POLASARA</option>
<option value="7">PURI</option>
<option value="75">PURUSOTTAMPUR</option>
<option value="161">RAGHUNATHPUR</option>
<option value="41">RAHAMA</option>
<option value="260">RAIPUR</option>
<option value="159">RAIRA KHOLE</option>
<option value="64">RAIRANGPUR</option>
<option value="103">RAJAKHARIAR</option>
<option value="32">RAJGANGPUR</option>
<option value="245">RAJKANIKA</option>
<option value="173">RAJRANPUR</option>
<option value="170">RAJSUNAKHALA</option>
<option value="189">RAMBHA</option>
<option value="121">RANCHI</option>
<option value="228">RANGPUR</option>
<option value="65">RAURKELA</option>
<option value="16">RAYAGADA</option>
<option value="199">RENGALI</option>
<option value="211">RUDRAPUR,PAHAL</option>
<option value="155">RUPRA ROAD</option>
<option value="17">S.BALANDA</option>
<option value="122">SAKHIGOPAL</option>
<option value="225">SALEPUR</option>
<option value="10">SALIPUR</option>
<option value="20">SAMBALPUR</option>
<option value="261">SATAMAILI</option>
<option value="180">SATPATNA</option>
<option value="143">SATSANKHA</option>
<option value="206">SERAGADA</option>
<option value="141">SIKHARPUR</option>
<option value="247">SILIGURI</option>
<option value="23">SIMILIGUDA</option>
<option value="158">SINAPALI</option>
<option value="185">SINGHPUR</option>
<option value="1">SOHELA</option>
<option value="207">SONEPUR</option>
<option value="62">SORO</option>
<option value="266">SUKINDA</option>
<option value="241">SUNABEDA</option>
<option value="262">SUNAKHALA</option>
<option value="76">SUNDARGARH</option>
<option value="201">SURADA</option>
<option value="39">TALCHER</option>
<option value="38">TALPATIA</option>
<option value="109">TANGI</option>
<option value="246">TANGI,KHURDA</option>
<option value="164">TARBHA</option>
<option value="92">TIKABALI</option>
<option value="13">TIRTOL</option>
<option value="116">TITILAGARH</option>
<option value="157">TUSHRA</option>
<option value="202">UDALA</option>
<option value="118">UMERKOTE</option>
<option value="131">VAPI</option>
<option value="123">VEDVYASH</option>

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
    <font size='+1'>MAHANADI C H</font> 01-04-2022........... 31-03-2023<br>
    Trip Summary Period 01-06-2022 - 11-06-2022<br />
    <table id='report' border="1">
        <tr>
            <th>Sl</th><th>Date</th><th>Company</th><th>LC No.</th><th>Destination</th><th>Lrno</th><th>Inv. No.</th><th>Date</th>
            <th>Weight</th><th>Qty</th><th>Freight</th><th colspan="2">Del. Ack.</th>
        </tr>
                                <tr>
            <th colspan="8">Total :</th>
            <td align="right"><b>0.00</b></td>
            <td align="right"><b>0</b></td>
            <td align="right"><b>0.00</b></td>
        </tr>
    </table>
</div>
<script>
$(document).ready(function () {
    $('#area').multiSelect();
    $('#company').multiSelect();
});
</script>
<style>
.ms-container .ms-selectable
{
   height: 50px !important;
}
</style>
                    <br><br>
                </div>
                        <div class="footer">
    <div class="float-left">Copyright &copy; 2021 - All Rights Reserved </div>
    <div class="float-right">Powered by : @ Solutions</div>
</div>
        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="btn" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
		<div tabindex="-1" class="modal bs-logout-modal-sm" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-sm">
			  <div class="modal-content">
				<div class="modal-header"><h4>Are you sure you want to logout ?</h4></div>
				<div class="modal-footer">
					<a class="btn btn-danger btn-xl" href="index.php?module=user&func=logout">Logout</a>
					<a class="btn btn-primary btn-xl" data-dismiss="modal">Close</a>
				</div>
			  </div>
			</div>
		</div>
    </body>
</html>
<?php }} ?>
