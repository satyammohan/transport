<?php

class adjust extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $sql = "SELECT p.name as `value`, p.id_product AS col0, p.mrp AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3, p.closing_stock AS col4 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster {$id}  ORDER BY p.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $price = ' b.seller_price ';
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2  FROM {$this->prefix}batch b WHERE b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function check() {
        $no = trim($_REQUEST['no']);
        $sql = $this->create_select("{$this->prefix}adjust", "no='$no'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function getslno() {
        $sql = "SELECT MAX(CAST(no as decimal(11))) as maxid FROM {$this->prefix}adjust";
        $data = $this->m->fetch_assoc($sql);
        $slno = $data['maxid'] + 1;
        return $slno;
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0)
            $this->get_permission("adjust", "INSERT");
        else
            $this->get_permission("adjust", "UPDATE");
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}adjustdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_adjust='$id' ORDER BY id_adjustdetail";
        $this->sm->assign("data", $this->m->sql_getall($sql));
        $sql = "SELECT * FROM {$this->prefix}adjust WHERE id_adjust='$id'";
        $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        $company = $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company  WHERE status=0  ORDER BY name", 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("no", $this->getslno());
        $this->sm->assign("page", "adjust/add.tpl.html");
    }

    function insert() {
        $this->get_permission("adjust", "INSERT");
        $data1 = $_REQUEST['adjust'];
        $data1['pending'] = $data1['total'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        if ($data1['bill_date'] != '') {
            $data1['bill_date'] = $this->format_date($data1['bill_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}adjust", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] - $_REQUEST['tax_amount2'][$i] + $_REQUEST['discount_amount4'][$i];
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "no" => "{$data1['no']}", "date" => "{$data1['date']}", "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}",
                    "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}", "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_adjust" => "{$id}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}adjustdetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $this->redirect("index.php?module=adjust&func=listing");
    }

    function update() {
        $this->get_permission("adjust", "UPDATE");
        $data1 = $_REQUEST['adjust'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['bill_date'] = $this->format_date($data1['bill_date']);
        $this->m->query($this->create_update("{$this->prefix}adjust", $data1, "id_adjust='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}adjustdetail", "id_adjust='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] - $_REQUEST['tax_amount2'][$i] + $_REQUEST['discount_amount4'][$i];
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "no" => "{$data1['no']}", "date" => "{$data1['date']}", "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}",
                    "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}", "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_adjust" => "{$id}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}adjustdetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $this->redirect("index.php?module=adjust&func=listing");
    }

    function delete() {
        $this->get_permission("adjust", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}adjust", "id_adjust='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}adjustdetail", "id_adjust='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=adjust&func=listing");
    }

    function listing() {
        $this->get_permission("adjust", "REPORT");
        $company = $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name", 2, "name", "id");
        $this->sm->assign("company", $company);
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "AND s.id_company='$id_company'";
        } else {
            $wcond = "";
        }
        $sql = "SELECT s.* FROM {$this->prefix}adjust s  WHERE s.date >= '$sdate' AND s.date <= '$edate' {$wcond} ORDER BY `date`, `no` DESC";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
    }

    function prsale($id = "") {
        $this->get_permission("adjust", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.address1,  h.address2, h.address3
                FROM {$this->prefix}adjust s  LEFT JOIN
                {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_adjust IN ($id)";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("adjust", $res1);
        $sql = "SELECT s.*, p.name AS item, p.hsncode, p.case FROM {$this->prefix}adjustdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_adjust IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_adjust", "id_adjustdetail");
        $this->sm->assign("adjustdetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
    }

}

?>
