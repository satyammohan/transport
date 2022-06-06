<?php

class product extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE name LIKE '{$filt}%' AND status=0 ORDER BY name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function insert() {
        $this->get_permission("product", "INSERT"); //INSERT, UPDATE, DELETE, SELECT
        foreach ($_REQUEST['product'] as $k => $v) {
		$data[$k] = addslashes($v);
        }
	//$data = $_REQUEST['product'];
        //$data['name'] = addslashes($data['name']);
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert($this->prefix . "product", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=product&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id) {
            $this->get_permission("product", "INSERT");
            $data = $this->m->fetch_assoc($this->create_select($this->prefix . "product", "id_product='{$id}'"));
        } else {
            $this->get_permission("product", "UPDATE");
            $data['id_company'] = @$_REQUEST['id_company'];
        }
        $this->sm->assign("data", $data);
        $sql1 = "SELECT name FROM {$this->prefix}company WHERE id_company='{$data['id_company']}'";
        $this->sm->assign("data1", $this->m->fetch_assoc($sql1));
        $sql = "SELECT id_taxmaster AS id, name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "product/add.tpl.html");
    }

    function update() {
        foreach ($_REQUEST['product'] as $k => $v) {
		$data[$k] = addslashes($v);
        }
        $this->get_permission("product", "UPDATE"); //INSERT, UPDATE, DELETE, SELECT
        $res = $this->m->query($this->create_update($this->prefix . "product", $data, "id_product='{$_REQUEST['id']}'"));
	if (@$_REQUEST['frombatch']!="0") {
            $_SESSION['msg'] = "Record Successfully Updated";
            $this->redirect("index.php?module=product&func=listing");
        } else {
            $this->redirect("index.php?module=product&func=gethsn&id_product={$_REQUEST['id']}&frombatch=0&ce=0");
        }
    }

    function delete() {
        $this->get_permission("product", "DELETE"); //INSERT, UPDATE, DELETE, SELECT
        $res1 = $this->m->query($this->create_select($this->prefix . "saledetail ", "id_product='{$_REQUEST['id']}'"));
        if ($this->m->num_rows($res1) > 0) {
            $_SESSION['msg'] = "Product Can not be Deleted <br>It is Present in Sale Details!";
        } else {
            //$this->m->query($this->create_delete($this->prefix . "product", "id_product='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=product&func=listing");
    }

    function check() {
        $tname = trim($_REQUEST['tname']);
        $sql = $this->create_select("template", "name='{$tname}' AND `type`='{$_REQUEST['module']}'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function insert_template() {
        $name = $_REQUEST['name'];
        $nfield = $_REQUEST['fields'];
        $value = array("name" => "$name", "type" => "product", "values" => "$nfield");
        $sql = $this->create_insert("template", $value);
        $res = $this->m->query($sql);
        $this->redirect("index.php?module=product&func=listing&tpl={$name}");
    }

    function manage() {
        $pf = $this->m->sql_getall("SHOW FULL COLUMNS FROM {$this->prefix}product");
        $row = array();
        foreach ($pf as $k => $v) {
            if ($v['Comment'] != "")
                $row[$v['Field']] = $v['Comment'] ? $v['Comment'] : $v['Field'];
        }
        $this->sm->assign("field", $row);
        $sql = "SELECT id_template AS id, name FROM template WHERE `type`='{$_REQUEST['module']}' ORDER BY name";
        $this->sm->assign("template", $this->m->sql_getall($sql, 2, "name", "name"));
        $this->sm->assign("page", "product/template.tpl.html");
    }
    function hsndetails() {
        $this->get_permission("product", "UPDATE");
        $this->addfield('maximum_stock', $this->prefix . 'product', 'ADD `maximum_stock`  decimal(12,3)');
        $this->addfield('minimum_stock', $this->prefix . 'product', 'ADD `minimum_stock`  decimal(12,3)');
        $this->addfield('discounted_seller_price', $this->prefix . 'product', 'ADD `discounted_seller_price`  decimal(16,4)');
        $id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : "0";
        if ($id) {
            $sql = "SELECT * FROM {$this->prefix}product WHERE id_company='$id' ORDER BY name";
            $data =  $this->m->getall($this->m->query($sql));
            $this->sm->assign("data", $data);
        }
        $company = $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name", 2, "name", "id");
        $this->sm->assign("company", $company);
    }
    function saveproduct() {
        $this->get_permission("product", "UPDATE");
        $data[$_REQUEST['field']] = $_REQUEST['fvalue'];
        $this->m->query($this->create_update("{$this->prefix}product", $data, "id_product='{$_REQUEST['id']}'"));
        echo 1;
        exit;
    }
    function listing() {
        $this->get_permission("product", "REPORT"); //INSERT, UPDATE, DELETE, SELECT
        $this->addfield('maximum_stock', $this->prefix . 'product', 'ADD `maximum_stock`  decimal(12,3)');
        $this->addfield('minimum_stock', $this->prefix . 'product', 'ADD `minimum_stock`  decimal(12,3)');
        $id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : 0;
        if (isset($_REQUEST['tpl'])) {
            $sql = "SELECT `values` FROM template WHERE name='{$_REQUEST['tpl']}' AND type='product'";
            $res = $this->m->fetch_assoc($sql);
            $nfield = $res['values'];
            $result = $this->list_temp($id, $nfield);
        } else {
            $result = $this->list_temp($id, '');
        }
        $fld = array();
        foreach ($result as $key => $value) {
            if ($key == 'fld') {
                $fld = $result['fld'];
            }
        }
        unset($result['fld']);
        $this->sm->assign("column", $fld);
        $this->sm->assign("data", $result);
        $sql1 = "SELECT id_taxmaster AS id, name FROM {$this->prefix}taxmaster  ORDER BY name";
        $this->sm->assign("tax", $this->m->sql_getall($sql1, 2, "name", "id"));
        $sql = "SELECT id_template AS id, name FROM template WHERE `type`='{$_REQUEST['module']}' ORDER BY name";
        $this->sm->assign("template", $this->m->sql_getall($sql, 2, "name", "name"));
        $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE !status ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "product/listing.tpl.html");
    }

    function oldprice() {
        $this->get_permission("product", "REPORT"); //INSERT, UPDATE, DELETE, SELECT
        $id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : 0;
        if (isset($_REQUEST['tpl'])) {
            $sql = "SELECT `values` FROM template WHERE name='{$_REQUEST['tpl']}' AND type='product'";
            $res = $this->m->fetch_assoc($sql);
            $nfield = $res['values'];
            $result = $this->list_temp($id, $nfield);
        } else {
            $result = $this->list_temp($id, '');
        }
        $fld = array();
        foreach ($result as $key => $value) {
            if ($key == 'fld') {
                $fld = $result['fld'];
            }
        }
        unset($result['fld']);
        $this->sm->assign("column", $fld);
        $this->sm->assign("data", $result);
        $sql1 = "SELECT id_taxmaster AS id, name FROM {$this->prefix}taxmaster  ORDER BY name";
        $this->sm->assign("tax", $this->m->sql_getall($sql1, 2, "name", "id"));
        $sql = "SELECT id_template AS id, name FROM template WHERE `type`='{$_REQUEST['module']}' ORDER BY name";
        $this->sm->assign("template", $this->m->sql_getall($sql, 2, "name", "name"));
        $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE !status ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
    }
    
    function list_temp($id, $arr) {
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = " 1 " : $wcond = " status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = " 1 ";
        }
        if ($arr != '') {
            if (strpos($arr, 'id_product') == false) {
                $arr .= ",id_product";
            }
            $arr .= ",status";
            $nfield = "`" . implode("`, `", explode(",", $arr)) . "`";
        } else {
            $nfield = '*';
        }
        if ($id) {
            $sql = "SELECT $nfield FROM {$this->prefix}product WHERE id_company=$id AND {$wcond} ORDER BY id_company, name";
        } else {
            $sql = "SELECT $nfield FROM {$this->prefix}product WHERE {$wcond} ORDER BY id_company, name";
        }
        $res = $this->m->query($sql);
        $data = $this->m->getall($res);
        $numfields = mysql_num_fields($res);
        $narr = array();
        for ($i = 0; $i < $numfields; $i += 1) {
            $field = mysql_fetch_field($res, $i);
            $narr['fld'][$i] = $field->name;
        }
        $result = array_merge($data, $narr);
        return $result;
    }

    function deletetemp() {
        $this->m->query($this->create_delete("template", "name='{$_REQUEST['tname']}' AND `type`='product'"));
        exit;
    }

    function scroll_listing() {
        $sql = "SELECT p.*, c.name AS cname, t.name AS tname 
            FROM {$this->prefix}product p, {$this->prefix}company c,  {$this->prefix}taxmaster t
            WHERE p.id_company=c.id_company AND p.id_taxmaster_sale=t.id_taxmaster ORDER BY p.id_product DESC LIMIT 25 ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "product/scroll_list.tpl.html");
    }

    function scroll_next() {
        if (isset($_GET['lastID']) && is_numeric($_GET['lastID'])) {
            $lastID = intval($_GET['lastID']);
            $sql = "SELECT p.*, c.name AS cname, t.name AS tname 
                    FROM {$this->prefix}product p, {$this->prefix}company c,  {$this->prefix}taxmaster t
                    WHERE p.id_company=c.id_company AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product < '$lastID'  
                        ORDER BY p.id_product DESC LIMIT 2 ";
            $res = $this->m->sql_getall($sql);
            echo json_encode($res);
            exit;
        }
    }

    function price() {
        $this->get_permission("product", "REPORT");
        $id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : 0;
		if (isset($_REQUEST['status'])) {
            $wcond = ($_REQUEST['status'] == 2) ? " 1 " : " status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = " 1 ";
        }
		if ($id) {
			$sql = "SELECT * FROM {$this->prefix}product WHERE {$wcond} AND id_company='$id' ORDER BY id_company, name";
			$res = $this->m->query($sql);
			$data = $this->m->getall($res);
			$this->sm->assign("data", $data);
		}
        $sql1 = "SELECT id_taxmaster AS id, name FROM {$this->prefix}taxmaster  ORDER BY name";
        $this->sm->assign("tax", $this->m->sql_getall($sql1, 2, "name", "id"));
        $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE !status ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
    }
    function openingstock() {
        $this->get_permission("product", "REPORT");
        $id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : 0;
		if (isset($_REQUEST['status'])) {
            $wcond = ($_REQUEST['status'] == 2) ? " 1 " : " status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = " 1 ";
        }
		if ($id) {
			$sql = "SELECT * FROM {$this->prefix}product WHERE {$wcond} AND id_company='$id' ORDER BY id_company, name";
			$res = $this->m->query($sql);
			$data = $this->m->getall($res);
			$this->sm->assign("data", $data);
		}
        $sql1 = "SELECT id_taxmaster AS id, name FROM {$this->prefix}taxmaster  ORDER BY name";
        $this->sm->assign("tax", $this->m->sql_getall($sql1, 2, "name", "id"));
        $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE !status ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
    }
    function saveprice() {
	$id = $_REQUEST['id'];
	$col = $_REQUEST['cid'];
	$value = $_REQUEST['value'];
	$sql = "UPDATE {$this->prefix}product SET $col = '$value' WHERE id_product='$id'";
	$this->m->query($sql);
    }
    function itemuser() {
        $this->addfield('showtoparty', $this->prefix . 'product', 'ADD `showtoparty` varchar(3)');
	$id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : "0";
        if ($id) {
            $sql = "SELECT * FROM {$this->prefix}product WHERE id_company='$id' ORDER BY name";
            $data =  $this->m->getall($this->m->query($sql));
            $this->sm->assign("data", $data);
        }
        $company = $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name", 2, "name", "id");
        $this->sm->assign("company", $company);
    }
    function gethsn() {
        $this->get_permission("product", "UPDATE");
        $product = $_REQUEST['id_product'];
	    $sql = "SELECT id_product, hsncode FROM {$this->prefix}product WHERE id_product='$product'";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
}
?>
