<?php

class preturn extends common {

    function __construct() {
        $this->template = 'preturn';
        $this->table = 'preturn';
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getslno() {
        $sql = "SELECT MAX(slno) as maxid FROM {$this->prefix}{$this->table}";
        $data = $this->m->fetch_assoc($sql);
        return $data['maxid'] + 1;
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $preturntock = (isset($_SESSION['config']['showitemshavingstockinpreturn']) && $_SESSION['config']['showitemshavingstockinpreturn'] == 1) ? 1 : 0;
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        $price = ($dealer == "1") ? ' p.distributor_price ' : ' p.seller_price ';
        if ($preturntock == 1) {
            $sql = "SELECT p.name as `value`, p.id_product AS col0, $price AS col1, t.tax_per AS col2, p.id_taxmaster_preturn AS col3, p.cess AS col4, s.balance AS col5 
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

    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        //$price = ($dealer == "1") ? ' b.distributor_price ' : ' b.seller_price ';
        $price = ' b.purchase_price ';
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2  FROM {$this->prefix}batch b WHERE b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
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
        $sql = "SELECT concat(h.name, ',', h.address1, ',', h.address2, ',', IFNULL(address3, '')) as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.vatno AS col5, h.id_transport AS col6, h.dealer AS col7 FROM {$this->prefix}head h WHERE h.name LIKE '{$filt}%' ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getsuffix() {
        $series = mysql_real_escape_string($_REQUEST['series']);
        if ($series) {
            $pos = strlen($series) + 1;
            $sql = "SELECT MAX(CAST(substring(slno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}preturn WHERE slno LIKE '$series%'";
            $pstr = $series;
        } else {
            $taxbill = $_REQUEST['taxbill'];
            $sql = "SELECT MAX(CAST(SUBSTRING(slno, 2) AS UNSIGNED)) AS maxid FROM {$this->prefix}preturn WHERE taxbill=$taxbill";
            $pstr = $taxbill ? "T" : "R";
        }
        if ($_SESSION['name'] == "ODI RAY INDUSTRIES LIMITED") {
            $sql = "SELECT MAX(CAST(substring(slno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}preturn";
        }
        $data = $this->m->fetch_assoc($sql);
        echo $pstr . ($data['maxid'] + 1);
        exit;
    }

    function check() {
        $slno = trim($_REQUEST['slno']);
        $sdate = $_SESSION['sdate'];
        $edate = $_SESSION['edate'];
        $sql = $this->create_select("{$this->prefix}preturn", "slno='$slno' AND `date`>='$sdate' AND `date`<='$edate'");
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
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company  WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $preturnman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("preturnman", $preturnman);
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0)
            $this->get_permission("preturn", "INSERT");
        else
            $this->get_permission("preturn", "UPDATE");
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}preturndetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_preturn='$id' ORDER BY id_preturndetail";
        $det = $this->m->sql_getall($sql);
        $this->sm->assign("data", $det);
        $sql = "SELECT * FROM {$this->prefix}preturn WHERE id_preturn='$id'";
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
        $this->get_permission("preturn", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.address1,  h.address2, h.address3
                FROM {$this->prefix}preturn s
                LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                WHERE s.id_preturn IN ($id) GROUP BY s.id_preturn ";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("preturn", $res1);
        $sql = "SELECT s.*, b.expiry_date, p.name AS item, p.hsncode, p.case FROM {$this->prefix}preturndetail s LEFT JOIN {$this->prefix}batch b ON s.id_batch=b.id_batch, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_preturn IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_preturn", "id_preturndetail");
        $this->sm->assign("preturndetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "preturn/print.tpl.html");
    }

    function insert() {
        $this->get_permission("preturn", "INSERT");
        $data1 = $_REQUEST['preturn'];
        $data1['pending'] = $data1['total'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        if ($data1['challan_date'] != '') {
            $data1['challan_date'] = $this->format_date($data1['challan_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}preturn", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i] - $_REQUEST['discount_amount1'][$i] - $_REQUEST['discount_amount2'][$i] - $_REQUEST['discount_amount3'][$i] - $_REQUEST['discount_amount4'][$i];
                $data = array("slno" => "{$data1['slno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "id_preturn" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}preturndetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
            }
        }

        $_SESSION['msg'] = "Sales Return Successfully Inserted";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=preturn&func=listing");
        }
    }

    function update() {
        $this->get_permission("preturn", "UPDATE");
        $data1 = $_REQUEST['preturn'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['challan_date'] = $this->format_date($data1['challan_date']);
        $this->m->query($this->create_update("{$this->prefix}preturn", $data1, "id_preturn='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}preturndetail", "id_preturn='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i] - $_REQUEST['discount_amount1'][$i] - $_REQUEST['discount_amount2'][$i] - $_REQUEST['discount_amount3'][$i] - $_REQUEST['discount_amount4'][$i];
                $data = array("slno" => "{$data1['slno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "id_preturn" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}preturndetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $_SESSION['msg'] = "Sales Return Successfully Updated";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=preturn&func=listing");
        }
    }

    function updprint() {
        $idarr = $_REQUEST['id'];
        for ($i = 0; $i < count($idarr); $i++) {
            $sql = "UPDATE {$this->prefix}preturn SET `printed` = NOW() WHERE id={$idarr[$i]}";
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
        $sql = "SELECT s.* , h.name AS pname FROM {$this->prefix}{$this->table} s , {$this->prefix}head h WHERE s.id_head=h.id_head AND s.date >= '$sdate' AND s.date <= '$edate' {$wcond}";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
        $this->sm->assign("page", "$this->template/list.tpl.html");
    }

    function history() {
        $this->get_permission("preturn", "REPORT");
        $sql = "SELECT id_head AS id,name FROM {$this->prefix}head ORDER BY name";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));
        if (isset($_REQUEST['party'])) {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}preturndetail s, {$this->prefix}product p
                WHERE s.id_product=p.id_product AND s.id_product='{$_REQUEST['id']}' AND s.id_head='{$_REQUEST['party']}' ORDER BY s.`date` DESC ";
            $this->sm->assign("page", "preturn/party.tpl.html");
        } else {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}preturndetail s, {$this->prefix}product p
                WHERE s.id_product=p.id_product AND s.id_product='{$_REQUEST['id']}' ORDER BY s.`date` DESC ";
            $this->sm->assign("page", "preturn/history.tpl.html");
        }
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
    }

    function delete() {
        $this->get_permission("preturn", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}preturn", "id_preturn='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}preturndetail", "id_preturn='{$_REQUEST['id']}'"));

        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=preturn&func=listing");
    }

}

?>
