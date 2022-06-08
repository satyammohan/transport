<?php
/* Smarty version 3.1.39, created on 2022-06-08 06:28:22
  from 'C:\xampp\htdocs\transport\templates\common\container.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629ff42e5f5252_53676161',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '598138f4762750406538f73c71317c77dbe8c864' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\common\\container.tpl.html',
      1 => 1651977349,
      2 => 'file',
    ),
    'c211f5a13250937877ec126086fb96cc13d53b77' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\common\\header.tpl.html',
      1 => 1630891650,
      2 => 'file',
    ),
    'fe42a3d5694a4c025a70161a4dc9f09aa0d46308' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\common\\menu.tpl.html',
      1 => 1654445531,
      2 => 'file',
    ),
    'bafc8cbcc1df1d19eb6a23fc75231d3bfa5e1192' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\util\\check.tpl.html',
      1 => 1630646412,
      2 => 'file',
    ),
    '18a79272cdce391e9d69255398d88e6abf9728de' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\common\\footer.tpl.html',
      1 => 1630646412,
      2 => 'file',
    ),
  ),
  'cache_lifetime' => 3600,
),true)) {
function content_629ff42e5f5252_53676161 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
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
                <div class="err_msg">Software Upgraded Successful.</div>
            </div>
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
  <button class="dropdown-btn banquet"><i class="fa fa-bar-chart-o"></i> Accounts
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=consignment&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=consignment&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>
  <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Reports
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">  
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Bill
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=companywise&func=detail"><i class="fa fa-bed"></i> Companywise Detail</a>
      <a class="group" href="index.php?module=companywise&func=acwise"><i class="fa fa-bed"></i> Companywise Ac-wise</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Consignment
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> +++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Excel
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> ++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Shortage/Damage
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> ++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Own Vehicle
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> +++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Accounts
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=accounts&func=listing"><i class="fa fa-bed"></i> Group</a>
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
var showactive = "util";
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
</style>                </div>
                <div class="col-10 w-100 h-100"  style="left: 20px !important;">
                    <br><br><br>
                    
                    <br><br>
                </div>
                        <div class="footer">
    <div class="float-left">Copyright &copy; 2021 - All Rights Reserved </div>
    <div class="float-right">Powered by : @ Solutions</div>
</div>        </div>
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
<?php }
}
