<?php

class taxmaster extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check_name() {
        $data = $_REQUEST['tax'];
        $name = trim($data['name']);
        $sql = $this->create_select("{$this->prefix}taxmaster", "name='$name' AND id!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function insert() {
        $data = $_REQUEST['tax'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert("{$this->prefix}taxmaster", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=taxmaster&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select("{$this->prefix}taxmaster", "id_taxmaster='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "taxmaster/add.tpl.html");
    }

    function update() {
        $res = $this->m->query($this->create_update("{$this->prefix}taxmaster", $_REQUEST['tax'], "id_taxmaster='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=taxmaster&func=listing");
    }

    function delete() {
        $_SESSION['msg'] = "For Security Reasons this optin is Disabled";
        $this->redirect("index.php?module=taxmaster&func=listing");
        exit;
        $res = $this->m->query("SELECT tax_per FROM {$this->prefix}taxmaster WHERE id_taxmaster='{$_REQUEST['id']}'");
        $row = $this->m->fetch_array($res);
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}product WHERE id_taxmaster_sale={$_REQUEST['id']} OR id_taxmaster_purchase={$_REQUEST['id']}");
        $res2 = $this->m->query("SELECT tax FROM  {$this->prefix}saledetail WHERE tax={$row['tax_per']}");
        // mohan to change 
        // check in purchase detail / debit note / credit note / sale return / purchase return also
        if ($this->m->num_rows($res1)>0 || $this->m->num_rows($res2)>0) {
            $_SESSION['msg'] = "Tax is Found to be Associated with some other Informations<br>It Cant't be Deleted!";
        } else {
            $res = $this->m->query($this->create_delete("{$this->prefix}taxmaster", "id_taxmaster='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=taxmaster&func=listing");
    }

    function listing() {
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = $this->create_select("{$this->prefix}taxmaster", "$wcond"). " ORDER BY id_taxmaster";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("tax", $profile);
        $this->sm->assign("page", "taxmaster/listing.tpl.html");
    }

}

?>