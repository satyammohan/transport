<?php

class batch extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check_batch() {
        $bn = trim($_REQUEST['batch_no']);
        $id_item = $_REQUEST['id_item'];
        $id = $_REQUEST['id'];
        $sql = "SELECT * FROM {$this->prefix}batch WHERE batch_no='$bn' AND id_product='$id_item' AND id_batch!='$id'";
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? 1 : 0);
        exit;
    }
    function insert() {
        $this->get_permission("batch", "INSERT");
        $data = $_REQUEST['batch'];
        $data['expiry_date'] = $this->format_date($data['expiry_date']);
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert($this->prefix . "batch", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=batch&func=listing");
    }
    function getprevrate() {
	$id = $_REQUEST["id_product"];
	$sql = "SELECT purchase_price, seller_price, mrp_without_tax, mrp FROM {$this->prefix}batch WHERE id_product='$id' ORDER BY id_batch DESC LIMIT 1";
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data);
        exit;
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("batch", "INSERT");
        else
            $this->get_permission("batch", "UPDATE");
        if ($id) {
            $sql = $this->create_select("{$this->prefix}batch", "id_batch='{$id}'");
            $data = $this->m->fetch_assoc($sql);
	    $_REQUEST["id_product"] = $data['id_product'];
	}
        if (@$_REQUEST["id_product"]) {
            $res1 = $this->m->query("SELECT id_company AS id FROM {$this->prefix}product WHERE id_product={$_REQUEST["id_product"]}");
            $c = $this->m->getall($res1);
	    $_REQUEST["id_company"] = $c[0]['id'];
	}
        $id=@$_REQUEST["id_company"] ;
        $res1 = $this->m->query("SELECT id_product AS id, name FROM {$this->prefix}product WHERE id_company='$id' ORDER BY name");
        $this->sm->assign("product", $this->m->getall($res1, 2, "name", "id"));
        $this->sm->assign("data", $data);
        $sql = $this->m->query("SELECT id_company AS id, name FROM {$this->prefix}company ORDER BY name");
        $this->sm->assign("company", $this->m->getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "batch/add.tpl.html");
    }

    function update() {
        $this->get_permission("batch", "UPDATE");
        $data = $_REQUEST['batch'];
        $data['expiry_date'] = $this->format_date($data['expiry_date']);
        $res = $this->m->query($this->create_update($this->prefix . "batch", $data, "id_batch='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=batch&func=listing");
    }

    function delete() {
        $this->get_permission("batch", "DELETE");
        //check here form saledeatils before delete
        //$res = $this->m->query($this->create_delete($this->prefix . "batch", "id_batch='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=batch&func=listing");
    }

    function listing() {
        $this->get_permission("batch", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " AND status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $res1 = $this->m->query("SELECT id_company AS id, name FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id"));
	$id = $_REQUEST['id_company'] ? $_REQUEST['id_company'] : 0;

        $sql = "SELECT b.*, p.name FROM {$this->prefix}batch b, {$this->prefix}product p WHERE p.id_product=b.id_product AND id_company='$id' $wcond";

        $res1 = $this->m->query($this->create_select($this->prefix . "product", " id_company='$id'"));
        $this->sm->assign("product", $this->m->getall($res1, 2, "name", "id_product"));
        $this->sm->assign("batch", $this->m->getall($this->m->query($sql)));
        $this->sm->assign("page", "batch/listing.tpl.html");
    }
    function listing1() {
        $this->get_permission("batch", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " AND status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }

        $sql = "SELECT b.*, p.name FROM {$this->prefix}batch b, {$this->prefix}product p WHERE p.id_product=b.id_product ORDER BY id_batch DESC LIMIT 200";
        $res1 = $this->m->query($this->create_select($this->prefix . "product", " id_company='$id'"));
        $this->sm->assign("product", $this->m->getall($res1, 2, "name", "id_product"));
        $this->sm->assign("batch", $this->m->getall($this->m->query($sql)));
        $this->sm->assign("page", "batch/listing.tpl.html");
    }

    function expiry() {
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $start = $_SESSION['sdate'];
        $start = '2010-01-01';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "AND c.id_company='$id_company'";
        } else {
            $wcond = "";
        }
        $sql = "SELECT c.name AS cname, i.name, b.*, l.qty1
            FROM {$this->prefix}batch b, {$this->prefix}product i, {$this->prefix}company c, 
                (SELECT id_batch, SUM(qty+free) AS qty1 FROM {$this->prefix}product_ledger GROUP BY 1 HAVING qty1!=0) l
            WHERE c.id_company=i.id_company AND i.id_product=b.id_product AND b.id_batch=l.id_batch AND (expiry_date>='$start' AND expiry_date<='$sdate') $wcond 
            ORDER BY cname, i.name, b.expiry_date";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }
    function opening() {
        $this->get_permission("batch", "UPDATE");
        $sql = $this->m->query("SELECT id_company AS id, name FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($sql, 2, "name", "id"));
        $id = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id) {
        	$sql = "SELECT b.id_product, b.id_batch, b.batch_no, b.expiry_date, b.opening_stock, p.name
                FROM {$this->prefix}batch b, {$this->prefix}product p WHERE b.id_product=p.id_product AND p.id_company='{$id}'
                ORDER BY name, batch_no";
	        $batch = $this->m->getall($this->m->query($sql));
//$this->pr($batch);
	}
        $this->sm->assign("batch", $batch);
    }
    function savebatch() {
        $this->get_permission("batch", "UPDATE");
        $data[$_REQUEST['field']] = $_REQUEST['fvalue'];
        $this->m->query($this->create_update("{$this->prefix}batch", $data, "id_batch='{$_REQUEST['id']}'"));
        echo 1;
        exit;
    }

}

?> 
