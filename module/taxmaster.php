<?php

class taxmaster extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
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
    }
    function update() {
        $res = $this->m->query($this->create_update("{$this->prefix}taxmaster", $_REQUEST['tax'], "id_taxmaster='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=taxmaster&func=listing");
    }
    function delete() {
        $_SESSION['msg'] = "For Security Reasons this optin is Disabled";
        $this->redirect("index.php?module=taxmaster&func=listing");
    }
    function listing() {
        $sql = "SELECT * FROM {$this->prefix}taxmaster ORDER BY id_taxmaster";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("tax", $profile);
    }
}
?>