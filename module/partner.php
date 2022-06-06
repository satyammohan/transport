<?php
class partner extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $data = $_REQUEST['c'];
        $res = $this->m->query($this->create_insert("{$this->prefix}partner", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=partner&func=listing");
    }
    function update() {
        $data = $_REQUEST['c'];
        $sql = $this->create_update("{$this->prefix}partner", $data, "id_partner='{$_REQUEST['id']}'");
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=partner&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select("{$this->prefix}partner", "id_partner='{$id}'");
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function delete() {
        $_SESSION['msg'] = "For Security Reasons this optio n is Disabled";
        $this->redirect("index.php?module=partner&func=listing");
    }
    function listing() {
        $sql = "SELECT * FROM {$this->prefix}partner ORDER BY id_partner";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("partner", $profile);
    }
}
?>