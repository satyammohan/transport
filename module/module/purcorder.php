<?php
class purcorder extends common {
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
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
    }
    function prsale($id = "") {
        //$this->get_permission("purcorder", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.name, h.address1, h.address2, h.phone, h.mobile FROM {$this->prefix}purcorder s  LEFT JOIN
                {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_purcorder IN ($id)";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $this->sm->assign("purcorder", $res1);
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}purcorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_purcorder IN ($id)";

        $res = $this->m->sql_getall($sql, 1, "", "id_purcorder", "id_purcorderdetail");
        $this->sm->assign("purcorderdetail", $res);
        $this->sm->assign("page", "purcorder/print.tpl.html");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0) {
            //$this->get_permission("purcorder", "INSERT");
            $sql = "SELECT MAX(CAST(invno as decimal(11))) AS invno FROM {$this->prefix}purcorder";
            $data = $this->m->fetch_assoc($sql);
            $this->sm->assign("invno", $data['invno'] + 1);
        } else {
            //$this->get_permission("purcorder", "UPDATE");
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}purcorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_purcorder='$id'";
            $data = $this->m->sql_getall($sql);
            $this->sm->assign("data", $data);
            $sql = "SELECT s.*, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address2, h.vattype AS party_vattype, h.vatno AS party_vatno
                FROM {$this->prefix}purcorder s, {$this->prefix}head h WHERE s.id_purcorder='$id' AND s.id_head=h.id_head";
            $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        }
        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $this->option_val();
    }
    function insert() {
        //$this->get_permission("purcorder", "INSERT");
        $data1 = $_REQUEST['purcorder'];
        $maxno = $this->m->fetch_assoc("SELECT MAX(CAST(invno as decimal(11))) AS invno FROM {$this->prefix}purcorder");
        $data1['invno'] =  $maxno['invno'] + 1;
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['ref_date'] = $this->format_date($data1['ref_date']);
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}purcorder", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] - $_REQUEST['tax_amount'][$i];
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "date" => "{$data1['date']}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}", "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "tax_per"=>"{$_REQUEST['tax_per'][$i]}", "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}", 
                    "amount" => "{$_REQUEST['amount'][$i]}", "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_purcorder" => "{$id}", "id_head" => "{$data1['id_head']}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}", "net_amount" => $net_amount);
                $this->m->query($this->create_insert("{$this->prefix}purcorderdetail", $data));
            }
        }
        $this->redirect("index.php?module=purcorder&func=edit");
    }
    function update() {
        //$this->get_permission("purcorder", "UPDATE");
        $data1 = $_REQUEST['purcorder'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['modify_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['ref_date'] = $this->format_date($data1['ref_date']);
        $this->m->query($this->create_update("{$this->prefix}purcorder", $data1, "id_purcorder='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}purcorderdetail", "id_purcorder='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] - $_REQUEST['tax_amount'][$i];
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "date" => "{$data1['date']}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}", "goods_amount" => "{$_REQUEST['goods_amount'][$i]}",
                    "tax_per"=>"{$_REQUEST['tax_per'][$i]}", "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}", 
                    "amount" => "{$_REQUEST['amount'][$i]}", "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_purcorder" => "{$id}", "id_head" => "{$data1['id_head']}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}", "net_amount" => $net_amount);
                $this->m->query($this->create_insert("{$this->prefix}purcorderdetail", $data));
            }
        }
        $this->redirect("index.php?module=purcorder&func=listing");
    }
    function listing() {
        //$this->get_permission("purcorder", "REPORT");
        $this->option_val();
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "AND s.id_company='$id_company'";
        } else {
            $wcond = "";
        }
        $limit = isset($_REQUEST['start_date']) ? "" : " LIMIT 30";
        $sql = "SELECT s.*, h.name AS  party_name, h.address1, h.address2 FROM {$this->prefix}purcorder s , {$this->prefix}head h
            WHERE s.id_head=h.id_head AND s.date >= '$sdate' AND s.date <= '$edate' {$wcond} ORDER BY `date` DESC, `invno` DESC {$limit} ";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("sdata", $data);
    }
    function delete() {
        //$this->get_permission("purcorder", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}purcorder", "id_purcorder='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}purcorderdetail", "id_purcorder='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Purchase Order Successfully Deleted";
        $this->redirect("index.php?module=purcorder&func=listing");
    }
    function register() {
    }
    function pending() {
    }
    function minstock() {
    }
}
?>
