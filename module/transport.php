<?php
class transport extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("transport", "INSERT");
        $data = $_REQUEST['transport'];
        $sql = $this->create_insert("{$this->prefix}transport", $data);
        echo $sql;exit;
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=transport&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("transport", "INSERT");
        else
            $this->get_permission("transport", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}transport WHERE id_transport='$id'";
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("transport", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}transport", $_REQUEST['transport'], "id_transport='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=transport&func=listing");
    }
    function delete() {
        $this->get_permission("transport", "DELETE");
        // $res = $this->m->query($this->create_delete("{$this->prefix}transport", "id_transport='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=transport&func=listing");
    }
    function listing() {
        $this->get_permission("transport", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE status={$_REQUEST['status']}" : "";
        $sql = "SELECT * FROM {$this->prefix}transport $wcond ";
        $this->sm->assign("transport", $this->m->getall($this->m->query($sql)));
    }
}

?>