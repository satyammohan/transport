<?php

class purchase extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
        $this->addfield('purchase_type', $this->prefix . 'purchase', 'ADD purchase_type VARCHAR(10) NOT NULL DEFAULT "Purchase"');
        $this->addfield('id_account', $this->prefix . 'purchase', 'ADD `id_account` INT(11)');
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $sql = "SELECT p.name as `value`, p.id_product AS col0, p.purchase_price AS col1, t.tax_per AS col2, p.id_taxmaster_purchase AS col3, p.cess AS col4, 0 AS col5 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster {$id} AND !p.status
            ORDER BY p.name";
        $sql = "SELECT p.name as `value`, p.id_product AS col0, p.purchase_price AS col1, t.tax_per AS col2, p.id_taxmaster_purchase AS col3, p.cess AS col4, 0 AS col5 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster {$id} AND !p.status
            ORDER BY p.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function check() {
        $invno = trim($_REQUEST['invno']);
        $sdate = $_SESSION['sdate'];
        $edate = $_SESSION['edate'];
        $sql = $this->create_select("{$this->prefix}purchase", "no='$invno' AND `date`>='$sdate' AND `date`<='$edate'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }
    function getlastbatch() {
        $id = trim($_REQUEST['id_product']);
        $sql = "SELECT batch_no FROM {$this->prefix}batch WHERE id_product='$id' ORDER BY id_batch DESC LIMIT 1";
        $data = $this->m->sql_getall($sql);
        echo $data[0]['batch_no'];
        exit;
    }
    function checkbillno() {
        $invno = trim($_REQUEST['bill_no']);
        $sdate = $_SESSION['sdate'];
        $edate = $_SESSION['edate'];
        $sql = "SELECT p.bill_no, DATE_FORMAT(date,'%d/%m/%Y') AS date, h.name, p.total FROM {$this->prefix}purchase p LEFT JOIN {$this->prefix}head h on p.id_head=h.id_head 
                WHERE bill_no='$invno' AND `date`>='$sdate' AND `date`<='$edate' LIMIT 5";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function getparty() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT h.name as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.gstin AS col5, h.id_transport AS col6 
                FROM {$this->prefix}head h WHERE h.name LIKE '%{$filt}%' AND creditor  ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function getpartynoparty() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT h.name as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.gstin AS col5, h.id_transport AS col6 
                FROM {$this->prefix}head h WHERE h.name LIKE '%{$filt}%' ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function getbatch() {
        /*$filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1,b.purchase_price AS col2  FROM {$this->prefix}batch b WHERE b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);*/

        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $wcond = ($filt) ? " AND b.batch_no LIKE '%{$filt}%' " : "";
        $wcond .= " AND b.id_product='{$_REQUEST['id']}' ";
    	$sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, purchase_price AS col2, 
                b.mfg_date AS col3, SUM(s.qty+s.free) AS col4, 'value,col1,col2,col3,col4' AS filter
                FROM {$this->prefix}batch b, {$this->prefix}product_ledger s
                WHERE b.status=0 AND b.id_batch=s.id_batch AND b.id_product=s.id_product {$wcond} 
                GROUP BY b.id_batch ORDER BY col1";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getslno() {
        $sql = "SELECT MAX(CAST(no as decimal(11))) as maxid FROM {$this->prefix}purchase";
        $data = $this->m->fetch_assoc($sql);
        $slno = $data['maxid'] + 1;
        return $slno;
    }

    function getdiscount() {
        $id_head = mysql_real_escape_string($_REQUEST['id_head']);
        $id_product = mysql_real_escape_string($_REQUEST['id_product']);
        $sql = "SELECT d.discount AS dis1, p.id_company AS company FROM {$this->prefix}discount d  , {$this->prefix}product p WHERE (d.id_company=p.id_company AND d.id_head='$id_head') AND p.id='$id_product'";
        $data = $this->m->fetch_assoc($sql);
        echo $data['dis1'];
        exit;
    }

    function getform() {
        $id_form = mysql_real_escape_string($_REQUEST['id_form']);
        $sql = "SELECT name FROM {$this->prefix}form WHERE id='$id_form'";
        $data = $this->m->fetch_assoc($sql);
        echo $data['name'];
        exit;
    }

    function option_val() {
        $opt = "SELECT id_transport AS id, name FROM {$this->prefix}transport ORDER BY name";
        $transport = $this->m->sql_getall($opt, 2, "name", "id");
        $this->sm->assign("transport", $transport);
        $opt2 = "SELECT id_area AS id, name FROM {$this->prefix}area ORDER BY name";
        $area = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("area", $area);
        $opt4 = "SELECT id_company AS id, name FROM {$this->prefix}company  WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt6 = "SELECT id_represent AS id, name FROM {$this->prefix}represent ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0) {
            $this->get_permission("purchase", "INSERT");
	        $res = array();
        } else {
            $this->get_permission("purchase", "UPDATE");
	        $sql1 = "SELECT pd.*, p.name AS item FROM {$this->prefix}purchasedetail pd LEFT JOIN {$this->prefix}product p 
        	    ON pd.id_product=p.id_product WHERE pd.id_purchase='$id'";
	        $res = $this->m->sql_getall($sql1);
	    }
        $sql = "SELECT h.name, h.id_head FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.content=h.code AND UPPER(p.name)='PURCAC'";
        $param = $this->m->sql_getall($sql);
        $this->sm->assign("purcac", $param);

        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $opt = "SELECT id_form AS id, name FROM {$this->prefix}form ORDER BY name";
        $this->sm->assign("frm", $this->m->sql_getall($opt, 2, "name", "id"));

        $this->sm->assign("data", $res);
        $res = $this->m->sql_getall("SELECT * FROM {$this->prefix}purchase WHERE id_purchase='$id'");
        $this->sm->assign("sdata", $res[0]);

        $accode = $res[0]['id_account'] ? $res[0]['id_account'] : $param[0]['id_head'];
        $sql = "SELECT id_head, name FROM {$this->prefix}head WHERE id_head='$accode'";
        $this->sm->assign("account", $this->m->fetch_assoc($sql));

        $this->sm->assign("no", $this->getslno());
        $this->option_val();
        $this->sm->assign("page", "purchase/add.tpl.html");
    }

    function editold() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0) {
            $this->get_permission("purchase", "INSERT");
	    $res = array();
        } else {
            $this->get_permission("purchase", "UPDATE");
	    $sql1 = "SELECT pd.*, p.name AS item FROM {$this->prefix}purchasedetail pd LEFT JOIN {$this->prefix}product p 
        	    ON pd.id_product=p.id_product WHERE pd.id_purchase='$id'";
	    $res = $this->m->sql_getall($sql1);
	}

        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));

        $sql = "SELECT * FROM {$this->prefix}purchase WHERE id_purchase='$id'";
        $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        $this->sm->assign("data", $res);
        $opt = "SELECT id_form AS id, name FROM {$this->prefix}form ORDER BY name";
        $this->sm->assign("frm", $this->m->sql_getall($opt, 2, "name", "id"));
        $this->sm->assign("no", $this->getslno());
        $this->option_val();
        $this->sm->assign("page", "purchase/add.tpl.html");
    }

    function printpurchase($id = "") {
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql1 = "SELECT * FROM {$this->prefix}purchase  WHERE  id IN ($id)";
        $res1 = $this->m->sql_getall($sql1);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $sql = "SELECT pd.*, p.name AS item FROM {$this->prefix}purchasedetail pd, {$this->prefix}product p WHERE pd.id_product=p.id AND pd.id_purchase IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_purchase", "id");
        $this->sm->assign("purchase", $res1);
        $this->sm->assign("purchasedetail", $res);
        $opt6 = "SELECT id,name FROM {$this->prefix}represent ORDER BY name";

        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
        $this->sm->assign("page", "purchase/print.tpl.html");
    }

    function insert() {
        $this->get_permission("purchase", "INSERT"); //INSERT, UPDATE, DELETE, SELECT
        $data1 = $_REQUEST['purchase'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['date'] = $this->format_date($data1['date']);
        if ($data1['bill_date'] != '') {
            $data1['bill_date'] = $this->format_date($data1['bill_date']);
        }
        if ($data1['lr_date'] != '') {
            $data1['lr_date'] = $this->format_date($data1['lr_date']);
        }
        if ($data1['transport_mr_date'] != '') {
            $data1['transport_mr_date'] = $this->format_date($data1['transport_mr_date']);
        }
        if ($data1['form_date'] != '') {
            $data1['form_date'] = $this->format_date($data1['frm_date']);
        }
        if ($data1['waybill_date'] != '') {
            $data1['waybill_date'] = $this->format_date($data1['waybill_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}purchase", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i] - $_REQUEST['discount_amount1'][$i] - $_REQUEST['discount_amount2'][$i] - $_REQUEST['discount_amount3'][$i] - $_REQUEST['discount_amount4'][$i];
                $data = array("no" => "{$data1['no']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "id_purchase" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}purchasedetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        if (isset($_REQUEST['ce'])) {
            $this->printpurchase($id);
        } else {
            $this->redirect("index.php?module=purchase&func=listing");
        }
    }

    function update() {
        $data1 = $_REQUEST['purchase'];
        if ($id)
            $this->get_permission("purchase", "INSERT");
        else
            $this->get_permission("purchase", "UPDATE");
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['bill_date'] = $this->format_date($data1['bill_date']);
        $data1['lr_date'] = $this->format_date($data1['lr_date']);
        $data1['transport_mr_date'] = $this->format_date($data1['transport_mr_date']);
        $data1['form_date'] = $this->format_date($data1['form_date']);
        $data1['waybill_date'] = $this->format_date($data1['waybill_date']);
        $this->m->query($this->create_update("{$this->prefix}purchase", $data1, "id_purchase='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}purchasedetail", "id_purchase='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i] - $_REQUEST['discount_amount1'][$i] - $_REQUEST['discount_amount2'][$i] - $_REQUEST['discount_amount3'][$i] - $_REQUEST['discount_amount4'][$i];
                $data = array("no" => "{$data1['no']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "id_purchase" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}purchasedetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        if (isset($_REQUEST['ce'])) {
            $this->printpurchase($id);
        } else {
            $this->redirect("index.php?module=purchase&func=listing");
        }
    }

    function updprint() {
        $idarr = $_REQUEST['id'];
        for ($i = 0; $i < count($idarr); $i++) {
            $sql = "UPDATE {$this->prefix}purchase SET `printed` = NOW() WHERE id={$idarr[$i]}";
            $this->m->query($sql);
        }
        exit;
    }

    function listing() {
        $this->get_permission("purchase", "REPORT");
        $this->option_val();
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $id_represent = isset($_REQUEST['represent']) ? $_REQUEST['represent'] : '0';
        if ($id_company != 0 && $id_represent != 0) {
            $wcond = "AND id_company='$id_company' AND id_represent='$id_represent'";
        } elseif ($id_company != 0 && $id_represent == 0) {
            $wcond = "AND id_company='$id_company'";
        } elseif ($id_company == 0 && $id_represent != 0) {
            $wcond = "AND id_represent='$id_represent'";
        } else {
            $wcond = "";
        }
        $limit = isset($_REQUEST['start_date']) ? "" : " LIMIT 30";
        $sql = "SELECT * FROM {$this->prefix}purchase WHERE date >= '$sdate' AND date <= '$edate' {$wcond} ORDER BY date DESC, no DESC {$limit}";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
        $this->sm->assign("page", "purchase/list.tpl.html");
    }

    function history() {
        $sql = "SELECT pd.*, p.name AS item FROM {$this->prefix}purchasedetail pd, {$this->prefix}product p WHERE pd.id_product=p.id AND pd.id_product='{$_REQUEST['id']}'";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
        $opt4 = "SELECT id,name FROM {$this->prefix}head ORDER BY name";
        $this->sm->assign("head", $this->m->sql_getall($opt4, 2, "name", "id"));

        if (isset($_REQUEST['party'])) {
            $this->sm->assign("page", "purchase/party.tpl.html");
        } else {
            $this->sm->assign("page", "purchase/history.tpl.html");
        }
    }

    function delete() {
        $this->get_permission("purchase", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}purchase", "id_purchase='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}purchasedetail", "id_purchase='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=purchase&func=listing");
    }

    function prsale($id = "") {
        $this->get_permission("purchase", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.name, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.address1,  h.address2, h.address3
                FROM {$this->prefix}purchase s  LEFT JOIN
                {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_purchase IN ($id)";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("purchase", $res1);
        $sql = "SELECT s.*, p.name AS item, p.hsncode, p.case FROM {$this->prefix}purchasedetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_purchase IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_purchase", "id_purchasedetail");
        $this->sm->assign("purchasedetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "purchase/print.tpl.html");
    }

}

?>
