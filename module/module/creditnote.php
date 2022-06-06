<?php

class creditnote extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getcompnot() {
        $filt = isset($_REQUEST['filter']) ? addslashes($_REQUEST['filter']) : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $price = ' p.seller_price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3  ';
	if (isset($_REQUEST['price'])) {
		switch ($_REQUEST['price']) {
		case "1":
			$price = ' p.purchase_price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3 ';
			break;
		case "2":
			$price = ' p.mrp AS col1, 0 AS col2, 1 AS col3 ';
			break;
		}
	}
        $sql = "SELECT p.name as `value`, p.id_product AS col0, $price, p.cess AS col4, s.balance AS col5, p.case AS col6
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s 
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product {$id} AND !p.status
            ORDER BY p.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function getbonus() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        $res = $this->m->sql_getall("SELECT qty, free FROM {$this->prefix}bonus WHERE id_product='$id'");
        $free = isset($res[0]['free']) ? $res[0]['free'] : 0;
        $qty = isset($res[0]['qty']) ? $res[0]['qty'] : 1;
        $free = $qty / ($qty + $free);
        echo $free;
        exit;
    }
    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? addslashes($_REQUEST['filter']) : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
        $price = isset($_REQUEST['price']) ? $_REQUEST['price'] : 1;
	if ($price==0) {
            $tx = ' t.tax_per AS col2, p.id_taxmaster_sale AS col3 ';
        } else {
            $tx = ' 0 AS col2, 1 AS col3 ';
        }
        $sql = "SELECT p.name as `value`, p.id_product AS col0, p.seller_price AS col1, $tx, p.cess AS col4, s.balance AS col5
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product {$id} AND !p.status
            ORDER BY p.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        $price = ($dealer == "1") ? ' b.distributor_price ' : ' b.seller_price ';
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2, b.mfg_date AS col3  FROM {$this->prefix}batch b WHERE b.status=0 AND b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getrepresent() {
        $id = $_REQUEST['id'];
        $sql = "SELECT id_represent FROM {$this->prefix}area a WHERE a.id_area=$id";
        $data = $this->m->sql_getall($sql);
        ob_clean();
        echo $data[0]['id_represent'];
        exit;
    }

    function getparty() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT concat(h.name, ',', h.address1, ',', h.address2, ',', IFNULL(address3, '')) as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.gstin AS col5, h.id_transport AS col6, h.dealer AS col7 
				FROM {$this->prefix}head h WHERE h.name LIKE '{$filt}%' AND (h.debtor OR h.creditor) AND status=0 ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function check() {
        $no = trim($_REQUEST['no']);
        $sdate = $_SESSION['sdate'];
        $edate = $_SESSION['edate'];
        $sql = $this->create_select("{$this->prefix}creditnote", "no='$no' AND `date`>='$sdate' AND `date`<='$edate'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function option_val() {
        $opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $this->sm->assign("area", $this->m->sql_getall($opt2, 2, "name", "id"));
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company  WHERE status=0 ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($opt4, 2, "name", "id"));
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $this->sm->assign("salesman", $this->m->sql_getall($opt6, 2, "name", "id"));
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0) {
            $this->get_permission("creditnote", "INSERT");
	    $sql = "SELECT MAX(CAST(no AS UNSIGNED)) AS maxid FROM {$this->prefix}creditnote";
	    $data = $this->m->fetch_assoc($sql);
	    $data['no'] = $data['maxid'] + 1;
	    $this->sm->assign("sdata", $data);
	} else {
            $this->get_permission("creditnote", "UPDATE");
	    $sql = "SELECT s.*, p.name AS item, p.case AS itemcase FROM {$this->prefix}creditnotedetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_creditnote='$id' ORDER BY id_creditnotedetail";
	    $this->sm->assign("data", $this->m->sql_getall($sql));
	    $sql = "SELECT s.*, concat(h.name, ',', h.address1, ',', h.address2, ',', IFNULL(address3, '')) AS name, h.address1, h.address2, h.gstin, h.dealer FROM {$this->prefix}creditnote s LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_creditnote='$id' ";
	    $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
	}
        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $this->option_val();
        $this->sm->assign("page", "creditnote/add.tpl.html");
    }

    function prsale($id = "") {
        unset($_SESSION['url']);
        $this->get_permission("creditnote", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.pincode, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.name AS hname, h.address1,  h.address2, h.address3, h.email
                FROM {$this->prefix}creditnote s
                LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                WHERE s.id_creditnote IN ($id) GROUP BY s.id_creditnote ";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("creditnote", $res1);
        $sql = "SELECT s.*, b.expiry_date, p.name AS item, p.hsncode, p.case 
		FROM {$this->prefix}creditnotedetail s LEFT JOIN {$this->prefix}batch b ON s.id_batch=b.id_batch, {$this->prefix}product p 
		WHERE s.id_product=p.id_product AND s.id_creditnote IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_creditnote", "id_creditnotedetail");

        $this->sm->assign("creditnotedetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "creditnote/print.tpl.html");
    }
    function insert() {
        $this->get_permission("creditnote", "INSERT");
        $data1 = $_REQUEST['creditnote'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        if ($data1['refdate'] != '') {
            $data1['refdate'] = $this->format_date($data1['refdate']);
        }
        if ($data1['billdate'] != '') {
            $data1['billdate'] = $this->format_date($data1['billdate']);
        }
        $sql = "SELECT MAX(CAST(no AS UNSIGNED)) AS maxid FROM {$this->prefix}creditnote";
        $last = $this->m->fetch_assoc($sql);
        $data1['no'] = $last['maxid'] + 1;

        $sql = $this->create_insert("{$this->prefix}creditnote", $data1);
        $sql2 = $this->m->query($sql);
        $id = $this->m->getinsertID($sql2);
        $this->insertdetail($data1, $id);
        $_SESSION['msg'] = "Credit Note Successfully Inserted";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=creditnote&func=listing");
        }
    }
    function insertdetail($data1, $id) {
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $mode = ($data1['saletype']=="1") ? "S" : "N";
                $data = array("no" => "{$data1['no']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
		    "saletype" => "{$data1['saletype']}", "mode" => "{$mode}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}", "id_area" =>  "{$data1['id_area']}", "id_represent" =>  "{$data1['id_represent']}",
                    "id_creditnote" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $sql = $this->create_insert("{$this->prefix}creditnotedetail", $data);
                $this->m->query($sql);
            }
        }
    }
    function update() {
        $this->get_permission("creditnote", "UPDATE");
        $data1 = $_REQUEST['creditnote'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['refdate'] = $this->format_date($data1['refdate']);
        $data1['billdate'] = $this->format_date($data1['billdate']);
        $this->m->query($this->create_update("{$this->prefix}creditnote", $data1, "id_creditnote='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}creditnotedetail", "id_creditnote='$id'"));
		$this->insertdetail($data1, $id);
        $_SESSION['msg'] = "Credit Note Successfully Updated";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=creditnote&func=listing");
        }
    }
    function listing() {
        $this->get_permission("creditnote", "REPORT");
	    $sql = "SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
	    $wcond = '';
	    if ($id_company != 0) {
            $wcond .= "AND s.id_company='$id_company'";
        }
        $saletype = isset($_REQUEST['saletype']) ? $_REQUEST['saletype'] : '0';
    	if ($saletype != 0) {
            $wcond .= "AND s.saletype='$saletype'";
        }
        $limit = isset($_REQUEST['start_date']) ? "" : " LIMIT 30";
        $sql = "SELECT s.*, h.name, h.address1, h.address2, h.gstin, r.name AS rname 
            FROM {$this->prefix}creditnote s, {$this->prefix}head h, {$this->prefix}represent r
            WHERE s.id_head=h.id_head AND s.id_represent=r.id_represent AND (s.date >= '$sdate' AND s.date <= '$edate') {$wcond}
            ORDER BY `date` DESC, no DESC {$limit} ";
        $tpldata = $this->m->sql_getall($sql);
        $this->sm->assign("tpldata", $tpldata);
    }
    function delete() {
        $this->get_permission("creditnote", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}creditnote", "id_creditnote='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}creditnotedetail", "id_creditnote='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Credit Note Successfully Deleted";
        $this->redirect("index.php?module=creditnote&func=listing");
    }
}
?>
