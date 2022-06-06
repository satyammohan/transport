<?php

class sreturn extends common {

    function __construct() {
        $this->template = 'sreturn';
        $this->table = 'sreturn';
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getslno() {
        $sql = "SELECT MAX(slno) as maxid FROM {$this->prefix}{$this->table}";
        $data = $this->m->fetch_assoc($sql);
        return $data['maxid'] + 1;
    }

    function item() {
        $opt4 = "SELECT id_company AS id, name FROM {$this->prefix}company ORDER BY name";
        $comp = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $comp);
    }

    function itemrep() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $wcond = '';
        if ($id_company != 0) {
            $wcond .= " AND s.id_company='$id_company' ";
            $id_item = isset($_REQUEST['id_item']) ? $_REQUEST['id_item'] : '0';
        }
        if (isset($_REQUEST['itemids'])) {
            $itemids = implode(',', $_REQUEST['itemids']);
            $wcond .= " AND p.id_product IN ($itemids) ";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT sd.*, s.party_name AS party, p.name AS iname,  p.short, challan_no AS myinvno, s.challan_date
                        FROM {$this->prefix}sreturn s, {$this->prefix}sreturndetail sd, {$this->prefix}product p
                        WHERE s.id_sreturn=sd.id_sreturn AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY iname, sd.date,myinvno";
                break;
            case 2:
                $sql = "SELECT sd.id_sreturndetail, sd.date, p.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.name AS iname, p.short AS short
                        FROM {$this->prefix}sreturndetail sd, {$this->prefix}product p
                        WHERE sd.id_product=p.id_product AND sd.date>='$sdate' AND sd.date<='$edate' {$wcond} GROUP BY sd.id_product ORDER BY iname";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, "", "iname", "id_sreturndetail");
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreturn/item{$_REQUEST['option']}.tpl.html");
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $sreturntock = (isset($_SESSION['config']['showitemshavingstockinsreturn']) && $_SESSION['config']['showitemshavingstockinsreturn'] == 1) ? 1 : 0;
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        $price = ($dealer == "1") ? ' p.distributor_price ' : ' p.seller_price ';
        if ($sreturntock == 1) {
            $sql = "SELECT p.name as `value`, p.id_product AS col0, $price AS col1, t.tax_per AS col2, p.id_taxmaster_sreturn AS col3, p.cess AS col4, s.balance AS col5 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s 
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product AND s.balance>0 {$id} AND !p.status 
            ORDER BY p.name";
        } else {
            $sql = "SELECT p.name as `value`, p.id_product AS col0, $price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3, p.cess AS col4, s.balance AS col5 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s 
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product {$id} AND !p.status
            ORDER BY p.name";
        }
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function oldgetbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        //$price = ($dealer == "1") ? ' b.distributor_price ' : ' b.seller_price ';
        if ($dealer == "1") {
            $price = ' b.distributor_price '; 
        } else {
            $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '0';
            //0-seller, 1-purc, 2-mrp
            switch ($type) {
                case '1':
                    $price = ' b.purchase_price '; 
                    break;
                case '2':
                    $price = ' b.mrp_without_tax '; 
                    break;
                default:
                    $price = ' b.seller_price '; 
            }
        }
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2  FROM {$this->prefix}batch b WHERE b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $wcond = ($filt) ? " AND b.batch_no LIKE '%{$filt}%' " : "";
        $wcond .= " AND b.id_product='{$_REQUEST['id']}' ";
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        if ($dealer == "1") {
            $price = ' b.distributor_price '; 
        } else {
            $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '0';
            switch ($type) {
                case '1':
                    $price = ' b.purchase_price '; 
                    break;
                case '2':
                    $price = ' b.mrp_without_tax '; 
                    break;
                default:
                    $price = ' b.seller_price '; 
            }
        }
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2, 
            b.mfg_date AS col3, 0 AS col4, 'value,col1,col2,col3,col4' AS filter
            FROM {$this->prefix}batch b
            WHERE b.status=0 {$wcond} ORDER BY b.batch_no";
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
        $sql = "SELECT concat(h.name, ',', h.address1, ',', h.address2, ',', IFNULL(address3, '')) as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.vatno AS col5, h.id_transport AS col6, h.dealer AS col7 FROM {$this->prefix}head h WHERE h.name LIKE '{$filt}%' AND h.debtor ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getsuffix() {
        $series = mysql_real_escape_string($_REQUEST['series']);
        if ($series) {
            $pos = strlen($series) + 1;
            $sql = "SELECT MAX(CAST(substring(slno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}sreturn WHERE slno LIKE '$series%'";
            $pstr = $series;
        } else {
            $taxbill = $_REQUEST['taxbill'];
            $sql = "SELECT MAX(CAST(SUBSTRING(slno, 2) AS UNSIGNED)) AS maxid FROM {$this->prefix}sreturn WHERE taxbill=$taxbill";
            $pstr = $taxbill ? "T" : "R";
        }
        if ($_SESSION['name'] == "ODI RAY INDUSTRIES LIMITED") {
            $sql = "SELECT MAX(CAST(substring(slno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}sreturn";
        }
        $data = $this->m->fetch_assoc($sql);
        echo $pstr . ($data['maxid'] + 1);
        exit;
    }

    function check() {
        $slno = trim($_REQUEST['slno']);
        $sdate = $_SESSION['sdate'];
        $edate = $_SESSION['edate'];
        $sql = $this->create_select("{$this->prefix}sreturn", "slno='$slno' AND `date`>='$sdate' AND `date`<='$edate'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function option_val() {
        $opt = "SELECT id_transport AS id,name FROM {$this->prefix}transport ORDER BY name";
        $transport = $this->m->sql_getall($opt, 2, "name", "id");
        $this->sm->assign("transport", $transport);
        $opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $area = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("area", $area);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company  WHERE status=0  ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $sreturnman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("sreturnman", $sreturnman);
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0)
            $this->get_permission("sreturn", "INSERT");
        else
            $this->get_permission("sreturn", "UPDATE");
        $this->addfield('party_ref', $this->prefix . 'sreturn', 'ADD `party_ref` varchar(15)');
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}sreturndetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sreturn='$id' ORDER BY id_sreturndetail";
        $det = $this->m->sql_getall($sql);
        $this->sm->assign("data", $det);
        $sql = "SELECT s.*, h.dealer FROM {$this->prefix}sreturn s LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head WHERE id_sreturn='$id'";
        $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $sql = "SELECT id_series, name FROM {$this->prefix}series ORDER BY name";
        $this->sm->assign("series", $this->m->sql_getall($sql, 2, "name", "id_series"));
        $sql = "SELECT id_form AS id, name FROM {$this->prefix}form ORDER BY name";
        $this->sm->assign("frm", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("slno", $this->getslno());
        $this->option_val();
    }

    function prsale($id = "") {
        unset($_SESSION['url']);
        $this->get_permission("sreturn", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.address1,  h.address2, h.address3
                FROM {$this->prefix}sreturn s
                LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                WHERE s.id_sreturn IN ($id) GROUP BY s.id_sreturn ";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("sreturn", $res1);
        $sql = "SELECT s.*, p.name AS item, p.hsncode, p.case, b.expiry_date FROM {$this->prefix}sreturndetail s LEFT JOIN  {$this->prefix}batch b ON s.id_batch=b.id_batch
		, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sreturn IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_sreturn", "id_sreturndetail");
        $this->sm->assign("sreturndetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "sreturn/print.tpl.html");
    }

    function insert() {
        $this->get_permission("sreturn", "INSERT");
        $data1 = $_REQUEST['sreturn'];
        $data1['pending'] = $data1['total'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        if ($data1['challan_date'] != '') {
            $data1['challan_date'] = $this->format_date($data1['challan_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}sreturn", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                //$net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i] - $_REQUEST['discount_amount1'][$i] - $_REQUEST['discount_amount2'][$i] - $_REQUEST['discount_amount3'][$i] - $_REQUEST['discount_amount4'][$i];
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $data = array("slno" => "{$data1['slno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "id_sreturn" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}sreturndetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
            }
        }

        $_SESSION['msg'] = "Sales Return Successfully Inserted";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=sreturn&func=listing");
        }
    }

    function update() {
        $this->get_permission("sreturn", "UPDATE");
        $data1 = $_REQUEST['sreturn'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['challan_date'] = $this->format_date($data1['challan_date']);
        $this->m->query($this->create_update("{$this->prefix}sreturn", $data1, "id_sreturn='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}sreturndetail", "id_sreturn='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                //$net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i] - $_REQUEST['discount_amount1'][$i] - $_REQUEST['discount_amount2'][$i] - $_REQUEST['discount_amount3'][$i] - $_REQUEST['discount_amount4'][$i];
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $data = array("slno" => "{$data1['slno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "id_sreturn" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}sreturndetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $_SESSION['msg'] = "Sales Return Successfully Updated";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=sreturn&func=listing");
        }
    }

    function updprint() {
        $idarr = $_REQUEST['id'];
        for ($i = 0; $i < count($idarr); $i++) {
            $sql = "UPDATE {$this->prefix}sreturn SET `printed` = NOW() WHERE id={$idarr[$i]}";
            $this->m->query($sql);
        }
        exit;
    }

    function listing() {
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
        //$sql = "SELECT s.* , h.name AS pname FROM {$this->prefix}{$this->table} s , {$this->prefix}head h WHERE s.id_head=h.id_head AND s.date >= '$sdate' AND s.date <= '$edate' {$wcond}";
	$sql = "SELECT s.* , h.name AS pname FROM {$this->prefix}{$this->table} s LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head WHERE s.date >= '$sdate' AND s.date <= '$edate' {$wcond}";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
        $this->sm->assign("page", "$this->template/list.tpl.html");
    }

    function history() {
        $this->get_permission("sreturn", "REPORT");
        $sql = "SELECT id_head AS id,name FROM {$this->prefix}head ORDER BY name";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));
        if (isset($_REQUEST['party'])) {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}sreturndetail s, {$this->prefix}product p
                WHERE s.id_product=p.id_product AND s.id_product='{$_REQUEST['id']}' AND s.id_head='{$_REQUEST['party']}' ORDER BY s.`date` DESC ";
            $this->sm->assign("page", "sreturn/party.tpl.html");
        } else {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}sreturndetail s, {$this->prefix}product p
                WHERE s.id_product=p.id_product AND s.id_product='{$_REQUEST['id']}' ORDER BY s.`date` DESC ";
            $this->sm->assign("page", "sreturn/history.tpl.html");
        }
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
    }

    function delete() {
        $this->get_permission("sreturn", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}sreturn", "id_sreturn='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}sreturndetail", "id_sreturn='{$_REQUEST['id']}'"));

        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=sreturn&func=listing");
    }


    function register() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
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
        switch ($_REQUEST['option']) {
            case 1:
		if (isset($_REQUEST['itemdetails'])) {
                    $sql_detail = "SELECT sd.*, p.name FROM `{$this->prefix}sreturndetail` sd, `{$this->prefix}product` p
                        WHERE sd.id_product=p.id_product AND `date` >= '$sdate' AND `date` <= '$edate' ORDER BY date";
                    $detail = $this->m->sql_getall($sql_detail, 1, "", "id_sreturn", "id_sreturndetail");
                    $this->sm->assign("detail", $detail);
                }
                $sql = "SELECT s.*, sd.tax_per AS per, SUM(goods_amount) AS gv, SUM(sd.tax_amount) AS ta, h.name, h.gstin FROM `{$this->prefix}sreturn` s
			LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head 
			, `{$this->prefix}sreturndetail` sd 
			WHERE s.date >= '$sdate' AND s.date <= '$edate' AND s.id_sreturn=sd.id_sreturn $wcond 
			GROUP BY s.id_sreturn, sd.tax_per ORDER BY `date` ";
                break;
            case 2:
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`, COUNT(*) AS bills FROM `{$this->prefix}sreturn` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY `date` ORDER BY `date`";
                break;
            case 3:
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`, COUNT(*) AS bills FROM `{$this->prefix}sreturn` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent  ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreturn/register.tpl.html");
    }

    function taxregister() {
        $type = $_REQUEST['type'] = isset($_REQUEST['type']) ? $_REQUEST['type'] : '1';
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $wcond = " (s.date >= '$sdate' AND s.date <= '$edate') ";
        $tcond = ($type==1) ? '' : ($type==2 ? " AND length(h.gstin)=15 " : " AND length(h.gstin)!=15 ");

        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}taxmaster` t, `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
            WHERE $wcond $tcond AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
		case 1:
			$sql = "SELECT s.id_sreturn AS id_sale, s.slno AS invno, s.id_taxmaster, SUM(s.amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount,
				SUM(s.cessamt) AS cessamt 
				FROM `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond GROUP BY s.id_sreturn, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "id_sale", "id_taxmaster"));
			
			$sql = "SELECT s.id_sreturn AS id_sale, s.slno AS invno, s.date, s.party_name, s.party_address, s.totalamt, s.vat, s.totalcess, s.add, s.less,
				s.round, 0 AS packing, s.total, h.local, h.gstin 
				FROM `{$this->prefix}sreturn` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond ORDER BY s.date, s.slno";
			break;
		case 2:
			$sql = "SELECT s.date, s.id_taxmaster, SUM(s.amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount, 
				SUM(s.cessamt) AS cessamt 
				FROM `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond GROUP BY s.date, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "date", "id_taxmaster"));

			$sql = "SELECT s.date, SUM(s.totalamt) AS totalamt, SUM(s.vat) AS vat, SUM(s.total) AS `total`,
				SUM(s.add) AS `add`,  SUM(s.less) AS less, SUM(s.round) AS round, SUM(0) AS packing
				FROM `{$this->prefix}sreturn` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
				WHERE $wcond $tcond  GROUP BY s.date ORDER BY s.date ";
			break;
		case 3:
			$sql = "SELECT MONTHNAME(s.date) AS month, s.id_taxmaster, SUM(s.amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount,
				SUM(s.cessamt) AS cessamt 
				FROM `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond GROUP BY 1, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "month", "id_taxmaster"));

			$sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.totalamt) AS totalamt, 
				SUM(s.vat) AS vat, SUM(s.totalcess) AS totalcess, SUM(s.total) AS `total`,
				SUM(s.add) AS `add`,  SUM(s.less) AS less, SUM(s.round) AS round, SUM(0) AS packing
				FROM `{$this->prefix}sreturn` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond  GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
			break;
		}
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreturn/grntax.tpl.html");
    }
}
?>
