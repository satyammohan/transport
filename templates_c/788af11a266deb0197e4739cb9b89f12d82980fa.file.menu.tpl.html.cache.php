<?php /* Smarty version Smarty-3.1.21-dev, created on 2022-06-11 09:05:26
         compiled from "templates/common/menu.tpl.html" */ ?>
<?php /*%%SmartyHeaderCode:198285939362a40d7eb6cac6-26812220%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '788af11a266deb0197e4739cb9b89f12d82980fa' => 
    array (
      0 => 'templates/common/menu.tpl.html',
      1 => 1654918146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '198285939362a40d7eb6cac6-26812220',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d7eb77eb7_65110725',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d7eb77eb7_65110725')) {function content_62a40d7eb77eb7_65110725($_smarty_tpl) {?><div class="sidenav col-2">
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


<?php echo '<script'; ?>
>
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
var showactive = "<?php if ($_REQUEST['module']) {
echo $_REQUEST['module'];
} else { ?>home<?php }?>";
$(document).ready(function() {
    $("."+showactive).trigger('click').addClass("active");
});
<?php echo '</script'; ?>
>

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
</style><?php }} ?>
