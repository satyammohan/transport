<?php

class salesorder extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getcomp() {
        //"id_product__1", "rate__1", "tax__1", "id_tax__1", "balance__1"
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $sql = "SELECT p.name as `value`, p.id_product AS col0, p.seller_price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3, p.cess AS col4, s.balance AS col5 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s 
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product {$id} AND !p.status
            ORDER BY p.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getparty() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT concat(h.name, ',', h.address1, ',', h.address2, ',', IFNULL(address3, '')) as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.vatno AS col5, h.id_transport AS col6, h.dealer AS col7 FROM {$this->prefix}head h WHERE h.name LIKE '{$filt}%' AND h.debtor AND status=0  ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getmyorder() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id = $_REQUEST['id'];
        $sql = "SELECT s.*, sd.qty, sd.free, sd.rate, sd.tax_amount+sd.cessamt AS taxes, sd.net_amount, p.name 
            FROM `{$this->prefix}salesorder` s, `{$this->prefix}salesorderdetail` sd, `{$this->prefix}product` p
            WHERE s.date>='$sdate' AND s.date<='$edate' AND s.id_head='{$id}' AND s.id_salesorder=sd.id_salesorder AND sd.id_product=p.id_product
            ORDER BY s.date, s.invno, p.name";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function getpartybalance() {
        $id = $_REQUEST['id_head'];
        $sql = "SELECT SUM(-debit+credit) AS balance FROM {$this->prefix}tb WHERE id_head=$id";
        $data = $this->m->sql_getall($sql);
        echo $data[0]['balance'];
        exit;
    }

    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        $price = ($dealer == "1") ? ' b.distributor_price ' : ' b.seller_price ';
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2  FROM {$this->prefix}batch b WHERE b.status=0 AND b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function option_val() {
        $opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $area = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("area", $area);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0) {
            $this->get_permission("salesorder", "INSERT");

	    $sdate = $_SESSION['sdate'];
            $edate = $_SESSION['edate'];
            $sql = "SELECT MAX(CAST(invno as decimal(11))) AS invno FROM {$this->prefix}salesorder WHERE date>='$sdate' AND date<='$edate'";
            $data = $this->m->fetch_assoc($sql);
            $this->sm->assign("invno", $data['invno'] + 1);
        } else {
            $this->get_permission("salesorder", "UPDATE");
        }
        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $sql = "SELECT id_series,name FROM {$this->prefix}series ORDER BY name";
        $this->sm->assign("series", $this->m->sql_getall($sql, 2, "name", "id_series"));
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_salesorder='$id'";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
        $sql = "SELECT s.*, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address2, h.vattype AS party_vattype, h.vatno AS party_vatno
            FROM {$this->prefix}salesorder s, {$this->prefix}head h WHERE s.id_salesorder='$id' AND s.id_head=h.id_head";
        $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        $this->option_val();
        $this->sm->assign("page", "salesorder/add.tpl.html");
    }

    function prsale($id = "") {
        $this->get_permission("salesorder", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.name, h.address1, h.address2, h.phone, h.mobile FROM {$this->prefix}salesorder s  LEFT JOIN
                {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_salesorder IN ($id)";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("salesorder", $res1);
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_salesorder IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_salesorder", "id_salesorderdetail");
        $this->sm->assign("salesorderdetail", $res);
        $this->sm->assign("page", "salesorder/print.tpl.html");
    }

    function insert() {
        $this->get_permission("salesorder", "INSERT");
        $data1 = $_REQUEST['salesorder'];
        $maxno = $this->m->fetch_assoc("SELECT MAX(CAST(invno as decimal(11))) AS invno FROM {$this->prefix}salesorder");
        $data1['invno'] =  $maxno['invno'] + 1;
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}salesorder", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] - $_REQUEST['tax_amount2'][$i];
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "date" => "{$data1['date']}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}", "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "tax_per"=>"{$_REQUEST['tax_per'][$i]}", "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}", 
                    "amount" => "{$_REQUEST['amount'][$i]}", "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_salesorder" => "{$id}", "id_head" => "{$data1['id_head']}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}", "net_amount" => $net_amount);
                $this->m->query($this->create_insert("{$this->prefix}salesorderdetail", $data));
            }
        }
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=salesorder&func=edit");
        }
    }

    function update() {
        $this->get_permission("salesorder", "UPDATE");
        $data1 = $_REQUEST['salesorder'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $this->m->query($this->create_update("{$this->prefix}salesorder", $data1, "id_salesorder='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}salesorderdetail", "id_salesorder='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] - $_REQUEST['tax_amount2'][$i];
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "date" => "{$data1['date']}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}", "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "tax_per"=>"{$_REQUEST['tax_per'][$i]}", "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}", 
                    "amount" => "{$_REQUEST['amount'][$i]}", "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_salesorder" => "{$id}", "id_head" => "{$data1['id_head']}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}", "net_amount" => $net_amount);
                $this->m->query($this->create_insert("{$this->prefix}salesorderdetail", $data));
            }
        }
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            if (isset($data1['scheme'])) {
                $this->redirect("index.php?module=salesorder&func=order");
            } else {
                $this->redirect("index.php?module=salesorder&func=listing");
            }
        }
    }

    function listing() {
        $this->get_permission("salesorder", "REPORT");
        $this->option_val();
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $id_represent = isset($_REQUEST['represent']) ? $_REQUEST['represent'] : '0';
        if ($id_company != 0 && $id_represent != 0) {
            $wcond = "AND s.id_company='$id_company' AND s.id_represent='$id_represent'";
        } elseif ($id_company != 0 && $id_represent == 0) {
            $wcond = "AND s.id_company='$id_company'";
        } elseif ($id_company == 0 && $id_represent != 0) {
            $wcond = "AND s.id_represent='$id_represent'";
        } else {
            $wcond = "";
        }
        $limit = isset($_REQUEST['start_date']) ? "" : " LIMIT 30";
        $sql = "SELECT s.id_salesorder, SUM(p.weight*(s.qty+s.free)) AS weight
				FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p
				WHERE s.id_product=p.id_product GROUP BY 1";
	$weight = $this->m->sql_getall($sql,2,"weight","id_salesorder");
        $this->sm->assign("weight", $weight);

        $sql = "SELECT s.*, cast(invno AS SIGNED) as myno, h.name AS  party_name, h.address1, h.address2, r.name AS rname FROM {$this->prefix}salesorder s, {$this->prefix}represent r, {$this->prefix}head h
            WHERE s.id_represent=r.id_represent AND s.id_head=h.id_head AND s.date >= '$sdate' AND s.date <= '$edate' {$wcond}
                ORDER BY `date` DESC, myno DESC {$limit} ";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
        $this->sm->assign("page", "salesorder/list.tpl.html");
    }

    function delete() {
        $this->get_permission("salesorder", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}salesorder", "id_salesorder='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}salesorderdetail", "id_salesorder='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=salesorder&func=listing");
    }
    function order() {
        $this->get_permission("salesorder", "REPORT");
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "AND p.id_company='$id_company'";
        } else {
            $wcond = "";
        }
        $sql = "SELECT s.id_salesorder, SUM(p.weight*(s.qty+s.free)) AS weight
               FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product {$wcond} GROUP BY 1";
        $weight = $this->m->sql_getall($sql,2,"weight","id_salesorder");
        $this->sm->assign("weight", $weight);

        $sql = "SELECT id_head, SUM(total) AS total FROM `{$this->prefix}salesorder` WHERE is_approve AND is_billed=0 GROUP BY id_head";
        $approved = $this->m->sql_getall($sql,2,"total","id_head");

        $sql = "SELECT id_head, SUM(debit-credit) AS balance FROM `{$this->prefix}tb` GROUP BY 1";
        $ledger = $this->m->sql_getall($sql,2,"balance","id_head");
        foreach ($approved as $k => $v) {
            $ledger[$k] = $ledger[$k] + $v;
        }
        $this->sm->assign("ledger", $ledger);

        $sql = "SELECT sd.id_product, SUM(sd.qty) AS quantity, SUM(sd.free) AS free, p.name
        	FROM `{$this->prefix}salesorder` s, `{$this->prefix}salesorderdetail` sd, `{$this->prefix}product` p
		    WHERE s.id_salesorder=sd.id_salesorder AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate'  AND 
			(s.is_approve=0 OR s.is_approve IS NULL) AND (s.is_billed=0 OR s.is_billed IS NULL) 
		    GROUP BY sd.id_product ORDER BY p.name";
        $this->sm->assign("isum", $this->m->sql_getall($sql));

        $sql = "SELECT s.*, cast(invno AS SIGNED) as myno, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address1
            FROM `{$this->prefix}salesorder` s, `{$this->prefix}head` h
            WHERE s.id_head=h.id_head AND `date`>='$sdate' AND `date`<='$edate' AND (s.is_approve=0 OR s.is_approve IS NULL) AND (s.is_billed=0 OR s.is_billed IS NULL)
            ORDER BY `date`, myno";

        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);

        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent  ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);

        $comp = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($comp, 2, "name", "id");
        $this->sm->assign("company", $company);
    }
    function cancelorder() {
        $this->get_permission("salesorder", "DELETE");
        $id = $_REQUEST['id'];
        $sid = $_SESSION['id_user'];
        $sql = "UPDATE {$this->prefix}salesorder SET is_cancel=1, cancel_date=NOW(), cancel_id='$sid' WHERE id_salesorder='$id'";
        $this->m->query($sql);
        $sql = "UPDATE {$this->prefix}salesorderdetail SET is_cancel=1, cancel_date=NOW(), cancel_id='$sid' WHERE id_salesorder='$id'";
        $this->m->query($sql);
        $_SESSION['msg'] = "Order Cancellation Successfully.";
        $this->redirect("index.php?module=salesorder&func=order");
    }
    function orderold() {
        $this->get_permission("salesorder", "REPORT");
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $sql = "SELECT s.id_salesorder, SUM(p.weight*(s.qty+s.free)) AS weight
                                FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p
                                WHERE s.id_product=p.id_product GROUP BY 1";
        $weight = $this->m->sql_getall($sql,2,"weight","id_salesorder");
        $this->sm->assign("weight", $weight);



        $sql = "SELECT s.*, cast(invno AS SIGNED) as myno, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address1, t.balance
            FROM `{$this->prefix}salesorder` s, `{$this->prefix}head` h, (SELECT id_head, SUM(debit-credit) AS balance FROM `{$this->prefix}tb` GROUP BY 1) t
            WHERE s.id_head=h.id_head AND h.id_head=t.id_head AND `date`>='$sdate' AND `date`<='$edate' AND (s.is_approve=0 OR s.is_approve IS NULL) AND (s.is_billed=0 OR s.is_billed IS NULL)
            ORDER BY `date`, myno";
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent  ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function aorder() {
        $this->get_permission("salesorder", "REPORT");
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $wcond = "";
        $id_represent = isset($_REQUEST['represent']) ? $_REQUEST['represent'] : '0';
        if ($id_represent != 0) $wcond = " AND s.id_represent='$id_represent' ";
        
        $sql = "SELECT s.id_salesorder, SUM(p.weight*(s.qty+s.free)) AS weight
            FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p
            WHERE s.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' $wcond GROUP BY 1";
        $this->sm->assign("weight", $this->m->sql_getall($sql,2,"weight","id_salesorder"));

        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent  ORDER BY name";
        $this->sm->assign("salesman", $this->m->sql_getall($opt6, 2, "name", "id"));
        
        $sql = "SELECT s.*, cast(invno AS SIGNED) as myno, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address1, c.name AS cname
            FROM `{$this->prefix}salesorder` s, `{$this->prefix}head` h, `{$this->prefix}company` c WHERE s.id_company=c.id_company AND
             s.id_head=h.id_head AND `date`>='$sdate' AND `date`<='$edate' AND s.is_approve AND s.is_billed=0 $wcond ORDER BY `date`, myno";
        $this->sm->assign("data", $this->m->sql_getall($sql));

        $sql = "SELECT sd.id_product, SUM(sd.qty) AS quantity, SUM(sd.free) AS free, p.name
        	FROM `{$this->prefix}salesorder` s, `{$this->prefix}salesorderdetail` sd, `{$this->prefix}product` p
		    WHERE s.id_salesorder=sd.id_salesorder AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' AND s.is_approve AND s.is_billed=0 $wcond
		    GROUP BY sd.id_product ORDER BY p.name";
        $this->sm->assign("isum", $this->m->sql_getall($sql));
    }

    function approvescheme() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT s.*, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address2, h.vattype AS party_vattype, h.vatno AS party_vatno
            FROM {$this->prefix}salesorder s, {$this->prefix}head h WHERE s.id_salesorder='$id' AND s.id_head=h.id_head";
        $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
    }

    function saveappprove() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $data1 = $_REQUEST['salesorder'];
        $data1['schemeapprove'] = addslashes($data1['schemeapprove']);
        $data1['id_approve'] = $_SESSION['id_user'];
        $data1['approve_date'] = date("Y-m-d h:i:s");
        $this->m->query($this->create_update("{$this->prefix}salesorder", $data1, "id_salesorder='$id'"));
        $this->redirect("index.php?module=salesorder&func=order");
    }

    function cancelapprove() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $data1['is_approve'] = 0;
        $this->m->query($this->create_update("{$this->prefix}salesorder", $data1, "id_salesorder='$id'"));
        $this->redirect("index.php?module=salesorder&func=aorder");
    }

    function pending() {
        $this->get_permission("salesorder", "REPORT");
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address1,
                    p.name AS itemname, s1.id_product, SUM(s1.qty) AS qty, SUM(s1.sales) AS sales
                    FROM `{$this->prefix}head` h, (
                        SELECT s.id_head, s.id_product, SUM(s.qty+s.free) AS qty, 0 AS sales FROM `{$this->prefix}salesorderdetail` s WHERE `date`>='$sdate' AND `date`<='$edate' GROUP BY s.id_head, s.id_product
                        UNION ALL SELECT s.id_head, s.id_product, 0 AS qty, SUM(s.qty+s.free) AS sales FROM `{$this->prefix}saledetail` s WHERE `date`>='$sdate' AND `date`<='$edate' GROUP BY s.id_head, s.id_product
                        ) s1, `{$this->prefix}product` p
                    WHERE s1.id_head=h.id_head AND s1.id_product=p.id_product
                    GROUP BY s1.id_head, s1.id_product
                    ORDER BY party_name, itemname ";
                break;
            case 2:
                $sql = "SELECT h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address1,
                    p.name AS itemname, s1.id_product, SUM(s1.qty) AS qty, SUM(s1.sales) AS sales
                    FROM `{$this->prefix}head` h, (
                        SELECT s.id_head, s.id_product, SUM(s.qty+s.free) AS qty, 0 AS sales FROM `{$this->prefix}salesorderdetail` s WHERE `date`>='$sdate' AND `date`<='$edate' GROUP BY s.id_head, s.id_product
                        UNION ALL SELECT s.id_head, s.id_product, 0 AS qty, SUM(s.qty+s.free) AS sales FROM `{$this->prefix}saledetail` s WHERE `date`>='$sdate' AND `date`<='$edate' GROUP BY s.id_head, s.id_product
                        ) s1, `{$this->prefix}product` p
                    WHERE s1.id_head=h.id_head AND s1.id_product=p.id_product
                    GROUP BY s1.id_head, s1.id_product HAVING qty-sales!=0
                    ORDER BY party_name, itemname ";
                break;
            case 3:
                $sql = "SELECT p.name AS itemname, s1.id_product, SUM(s1.qty) AS qty, SUM(s1.sales) AS sales
                    FROM (
                        SELECT s.id_head, s.id_product, SUM(s.qty+s.free) AS qty, 0 AS sales FROM `{$this->prefix}salesorderdetail` s WHERE `date`>='$sdate' AND `date`<='$edate' GROUP BY s.id_head, s.id_product
                        UNION ALL SELECT s.id_head, s.id_product, 0 AS qty, SUM(s.qty+s.free) AS sales FROM `{$this->prefix}saledetail` s WHERE `date`>='$sdate' AND `date`<='$edate' GROUP BY s.id_head, s.id_product
                        ) s1, `{$this->prefix}product` p
                    WHERE s1.id_product=p.id_product
                    GROUP BY s1.id_product HAVING qty-sales!=0
                    ORDER BY itemname ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

}

?>
