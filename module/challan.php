<?php
class challan extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function getparty() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT name as `value`, id_head AS col0, address1 AS col1, address2 AS col2, id_area AS col3, 
            vattype AS col4, gstin AS col5, id_transport AS col6, dealer AS col7, 'value,col1,col2,col5'  AS filter
            FROM {$this->prefix}head h WHERE h.name LIKE '{$filt}%' AND h.debtor AND status=0 ORDER BY h.name"; 
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function option_val() {
        $opt = "SELECT id_transport AS id,name FROM {$this->prefix}transport ORDER BY name";
        $transport = $this->m->sql_getall($opt, 2, "name", "id");
        $this->sm->assign("transport", $transport);
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
        if ($id == 0)
            $this->get_permission("sales", "INSERT");
        else
            $this->get_permission("sales", "UPDATE");
        if (@$_REQUEST['order_id']) {
            $oid = $_REQUEST['order_id'];
            $sql = "SELECT s.*, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address2, h.vattype AS party_vattype, h.vatno AS party_vatno, h.dealer FROM {$this->prefix}salesorder s, {$this->prefix}head h WHERE s.is_billed!=1 AND s.id_salesorder='$oid' AND s.id_head=h.id_head";
            $sdata = $this->m->fetch_assoc($sql);
            $sdata['id_sale'] = '';
            if ($sdata) {
                $this->sm->assign("sdata", $sdata);
                $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_salesorder='$oid'";
                $this->sm->assign("data", $this->m->sql_getall($sql));
            }
        } else {
            $sql = "SELECT s.*, p.name AS item, p.case AS itemcase FROM {$this->prefix}saledetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sale='$id' ORDER BY id_saledetail";
            $this->sm->assign("data", $this->m->sql_getall($sql));
            $sql = "SELECT s.*, h.dealer FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_sale='$id' ";
            $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        }
        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $sql = "SELECT id_series, name FROM {$this->prefix}series ORDER BY name";
        $this->sm->assign("series", $this->m->sql_getall($sql, 2, "name", "id_series"));
        $sql = "SELECT id_form AS id, name FROM {$this->prefix}form ORDER BY name";
        $this->sm->assign("frm", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->option_val();
        $nobatch = (isset($_SESSION['config']['NOBATCHINSALES']) && $_SESSION['config']['NOBATCHINSALES'] == 1) ? 1 : 0;
        if ($nobatch) {
            $this->sm->assign("page", "sales/add.nobatch.tpl.html");
        } else {
            $this->sm->assign("page", "sales/add.tpl.html");
        }
    }
    function prsale($id = "") {
        unset($_SESSION['url']);
        $this->get_permission("sales", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.dlicence, h.station, h.transport, h.pincode, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.name AS hname, h.address1,  h.address2, h.address3, h.email, SUM(IF(b.credit, -b.credit, b.debit)) AS balance
                FROM {$this->prefix}sale s
                LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                LEFT JOIN {$this->prefix}tb b ON b.id_head=h.id_head
                WHERE s.id_sale IN ($id) GROUP BY s.id_sale ";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
            $res1[$key]['balance'] = (float) $res1[$key]['balance'];
        }
        $this->sm->assign("sale", $res1);
        $sql = "SELECT s.*, b.expiry_date, b.mrp_without_tax, b.distributor_price, b.mfg_date, p.name AS item, p.hsncode, p.case, p.pack, p.unit FROM {$this->prefix}saledetail s LEFT JOIN {$this->prefix}batch b ON s.id_batch=b.id_batch, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sale IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_sale", "id_saledetail");
        $this->sm->assign("saledetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        if ((strtotime($res1[0]['date']) >= strtotime($_SESSION['gstdate'])) AND $_SESSION['gstdate'] != "") {
            if (@$_SESSION['config']['SALEBILLFORMAT']) {
                $format = $_SESSION['config']['SALEBILLFORMAT'];
                $bformat =  "salesbill/{$format}.tpl.html";
                $this->sm->assign("page", "salesbill/{$format}.tpl.html");
            } else {
                $this->sm->assign("page", "salesbill/print.tpl.html");
            }
        } else {
            $this->sm->assign("page", "salesbill/oldprint.tpl.html");
        }
    }
    function insert() {
        $this->get_permission("sales", "INSERT");
        $data1 = $_REQUEST['sales'];
        $data1['pending'] = $data1['total'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $_SESSION['current_sale_date'] = $data1['date'];
        if ($data1['challan_date'] != '') {
            $data1['challan_date'] = $this->format_date($data1['challan_date']);
        }
        if ($data1['lr_date'] != '') {
            $data1['lr_date'] = $this->format_date($data1['lr_date']);
        }
        if ($data1['cheque_date'] != '') {
            $data1['cheque_date'] = $this->format_date($data1['cheque_date']);
        }
        if ($data1['form_date'] != '') {
            $data1['form_date'] = $this->format_date($data1['form_date']);
        }
        if ($data1['waybill_date'] != '') {
            $data1['waybill_date'] = $this->format_date($data1['waybill_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}sale", $data1));
        $id = $this->m->getinsertID($sql2);
        if (@$_REQUEST['order_id']) {
            $sql = "UPDATE {$this->prefix}salesorder SET is_billed=1, id_sale={$id} WHERE id_salesorder=" . $_REQUEST['order_id'];
            $this->m->query($sql);
        }
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $data = array("invno" => "{$data1['invno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}", "id_area" =>  "{$data1['id_area']}", "id_represent" =>  "{$data1['id_represent']}",
                    "id_sale" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                if (isset($_REQUEST['case'][$i])) {
                    $data['case'] = $_REQUEST['case'][$i];
                }
                $this->m->query($this->create_insert("{$this->prefix}saledetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $_SESSION['msg'] = "Sales Successfully Inserted";
        if (isset($_REQUEST['ce'])) {
            //$this->prsale($id);
	    echo $id;
            exit;
        } else {
            //$this->redirect("index.php?module=sales&func=listing");
            $this->redirect("index.php?module=sales&func=edit");
        }
    }
    function update() {
        $this->get_permission("sales", "UPDATE");
        $data1 = $_REQUEST['sales'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['challan_date'] = $this->format_date($data1['challan_date']);
        $data1['lr_date'] = $this->format_date($data1['lr_date']);
        $data1['cheque_date'] = $this->format_date($data1['cheque_date']);
        $data1['form_date'] = $this->format_date($data1['form_date']);
        $data1['waybill_date'] = $this->format_date($data1['waybill_date']);
        $sql = "SELECT SUM(amt) AS amt FROM `{$this->prefix}voucher_details` WHERE id_sale='$id'";
        $sp = $this->m->sql_getall($sql);
        $data1['pending'] = @$data1['total'] - @$sp[0]['amt'];

        $this->m->query($this->create_update("{$this->prefix}sale", $data1, "id_sale='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}saledetail", "id_sale='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $data = array("invno" => "{$data1['invno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}", "id_area" =>  "{$data1['id_area']}", "id_represent" =>  "{$data1['id_represent']}",
                    "id_sale" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                if (isset($_REQUEST['case'][$i])) {
                    $data['case'] = $_REQUEST['case'][$i];
                }
                $this->m->query($this->create_insert("{$this->prefix}saledetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $_SESSION['msg'] = "Sales Successfully Updated";
        if (isset($_REQUEST['ce'])) {
            //$this->prsale($id);
	    echo $id;
            exit;
        } else {
            $this->redirect("index.php?module=sales&func=listing");
        }
    }
    function updprint() {
        $idarr = $_REQUEST['id'];
        for ($i = 0; $i < count($idarr); $i++) {
            $sql = "UPDATE {$this->prefix}sale SET `printed` = NOW() WHERE id={$idarr[$i]}";
            $this->m->query($sql);
        }
        exit;
    }
    function listing() {
        $this->get_permission("challan", "REPORT");
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $wc = @$_REQUEST['start_date'] ? " AND (c.date >= '$sdate' AND c.date <= '$edate') " : "";
        $limit = @$_REQUEST['start_date'] ? "" : " LIMIT 60";
        $sql = "SELECT c.*, h.name FROM {$this->prefix}challan c, {$this->prefix}head h WHERE c.id_head=h.id_head {$wc} ORDER BY date DESC, no DESC {$limit}";
        pr($sql);exit;
        $this->sm->assign("challan", $this->m->sql_getall($sql));
    }
    function delete() {
        $this->get_permission("challan", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}challan", "id_challan='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}challandetail", "id_challan='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=challan&func=listing");
    }
}
?>