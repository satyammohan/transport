<?php
class payment extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->image();
        $data = $_REQUEST['rt'];
        $res = $this->m->query($this->create_insert("{$this->prefix}roomtype", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=roomtype&func=listing");
    }
    function update() {
        $this->image();
        $data = $_REQUEST['rt'];
        $sql = $this->create_update("{$this->prefix}roomtype", $data, "id_roomtype='{$_REQUEST['id']}'");
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=roomtype&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select("{$this->prefix}roomtype", "id_roomtype='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function delete() {
        $_SESSION['msg'] = "For Security Reasons this optio n is Disabled";
        $this->redirect("index.php?module=roomtype&func=listing");
    }
    function listing() {
        $sql = "SELECT * FROM {$this->prefix}roomtype ORDER BY id_roomtype";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("roomtype", $profile);
    }
}
?>