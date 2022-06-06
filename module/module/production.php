<?php

class production extends common {

    function __construct() {
        $this->template = 'production';
        $this->table = 'production';
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $sql = "SELECT p.name as `value`, p.id_product AS col0, p.seller_price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3, p.closing_stock AS col4 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster {$id}  ORDER BY p.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1,b.seller_price AS col2  FROM {$this->prefix}batch b WHERE b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getslno() {
        $sql = "SELECT MAX(CAST(slno as decimal(11))) as maxid FROM {$this->prefix}{$this->table}";
        $data = $this->m->fetch_assoc($sql);
        return $data['maxid'] + 1;
    }

    function check() {
        $slno = trim($_REQUEST['slno']);
        $sql = $this->create_select("{$this->prefix}{$this->table}", "slno='$slno'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function option_val() {
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company  WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
    }

    function edit() {
        if ($id == 0)
            $this->get_permission("production", "INSERT");
        else
            $this->get_permission("production", "UPDATE");
        $this->option_val();
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id) {
            $sql1 = "SELECT s.*, p.name AS item FROM {$this->prefix}{$this->table}detail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_production='$id' ORDER BY id_productiondetail";
            $this->sm->assign("data", $this->m->sql_getall($sql1));
            $sql = "SELECT * FROM {$this->prefix}{$this->table} WHERE id_production='$id'";
            $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        }
        $this->sm->assign("slno", $this->getslno());
        $this->sm->assign("page", "$this->template/add.tpl.html");
    }

    function insert() {
        $this->get_permission("production", "INSERT");
        $data1 = $_REQUEST['production'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['memo'] = addslashes($data1['memo']);
        if ($data1['reference_date'] != '') {
            $data1['reference_date'] = $this->format_date($data1['reference_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}{$this->table}", $data1));
        $id = $this->m->getinsertID($sql2);
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "slno" => "{$data1['slno']}", "date" => "{$data1['date']}",
                    "free" => "{$_REQUEST['free'][$i]}", "type" => "{$_REQUEST['type'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_production" => "{$id}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_name" => "{$_REQUEST['batch_name'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}productiondetail", $data));
            }
        }
        $this->redirect("index.php?module=$this->template&func=edit");
    }

    function update() {
        $this->get_permission("production", "UPDATE");
        $data1 = $_REQUEST['production'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['reference_date'] = $this->format_date($data1['reference_date']);
        $data1['memo'] = addslashes($data1['memo']);
        $this->m->query($this->create_update("{$this->prefix}{$this->table}", $data1, "id_production='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}productiondetail", "id_production='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $data = array("qty" => "{$_REQUEST['quantity'][$i]}", "slno" => "{$data1['slno']}", "date" => "{$data1['date']}",
                    "free" => "{$_REQUEST['free'][$i]}", "type" => "{$_REQUEST['type'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "id_product" => "{$_REQUEST['id_product'][$i]}", "rate" => "{$_REQUEST['rate'][$i]}", "id_production" => "{$id}",
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_name" => "{$_REQUEST['batch_name'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                $this->m->query($this->create_insert("{$this->prefix}productiondetail", $data));
            }
        }
        $this->redirect("index.php?module={$this->template}&func=listing");
    }

    function listing() {
        $this->get_permission("production", "REPORT");
        $this->option_val();
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $wcond = ($id_company != 0) ? "AND s.id_company='$id_company'" : "";
        $sql = "SELECT s.* FROM {$this->prefix}{$this->table} s  WHERE s.date >= '$sdate' AND s.date <= '$edate' {$wcond}";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
    }

    function delete() {
        $this->get_permission("production", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}{$this->table}", "id_production='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}{$this->table}detail", "id_production='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module={$this->template}&func=listing");
    }

}

?>
