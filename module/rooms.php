<?php
class rooms extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $data = $_REQUEST['room'];
        $this->m->query($this->create_insert("{$this->prefix}rooms", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=rooms&func=listing");
    }
    function update() {
        $data = $_REQUEST['room'];
        $sql = $this->create_update("{$this->prefix}rooms", $data, "id_rooms='{$_REQUEST['id']}'");
        $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=rooms&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT id_roomtype AS id, name FROM {$this->prefix}roomtype ORDER BY name";
        $rt = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("rt", $rt);
        $sql = $this->create_select("{$this->prefix}rooms", "id_rooms='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function delete() {
        $_SESSION['msg'] = "For Security Reasons this optio n is Disabled";
        $this->redirect("index.php?module=rooms&func=listing");
    }
    function listing() {
        $sql = "SELECT r.*, rt.name AS rtname FROM {$this->prefix}rooms r, {$this->prefix}roomtype rt WHERE rt.id_roomtype=r.id_roomtype ORDER BY rt.name, r.name";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $profile);
    }
}
?>