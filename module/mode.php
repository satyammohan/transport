<?php
class mode extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("mode", "INSERT");
        $data = $_REQUEST['mode'];
        $res = $this->m->query($this->create_insert("{$this->prefix}mode", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=mode&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("mode", "INSERT");
        else
            $this->get_permission("mode", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}mode WHERE id_mode='$id'";
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("mode", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}mode", $_REQUEST['mode'], "id_mode='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=mode&func=listing");
    }
    function delete() {
        $this->get_permission("mode", "DELETE");
        // $res = $this->m->query($this->create_delete("{$this->prefix}mode", "id_mode='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=mode&func=listing");
    }
    function listing() {
        $this->get_permission("mode", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE status={$_REQUEST['status']}" : "";
        $sql = "SELECT * FROM {$this->prefix}mode $wcond ";
        $this->sm->assign("mode", $this->m->getall($this->m->query($sql)));
    }
}

?>