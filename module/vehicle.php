<?php
class vehicle extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("vehicle", "INSERT");
        $data = $_REQUEST['vehicle'];
        $res = $this->m->query($this->create_insert("{$this->prefix}vehicle", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=vehicle&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("vehicle", "INSERT");
        else
            $this->get_permission("vehicle", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}vehicle WHERE id_vehicle='$id'";
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}vowner WHERE status=0 ORDER BY name");
        $this->sm->assign("vowner", $this->m->getall($res1, 2, "name", "id_vowner"));
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("vehicle", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}vehicle", $_REQUEST['vehicle'], "id_vehicle='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=vehicle&func=listing");
    }
    function delete() {
        $this->get_permission("vehicle", "DELETE");
        // $res = $this->m->query($this->create_delete("{$this->prefix}vehicle", "id_vehicle='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=vehicle&func=listing");
    }
    function listing() {
        $this->get_permission("vehicle", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE status={$_REQUEST['status']}" : "";
        $sql = "SELECT * FROM {$this->prefix}vehicle $wcond ";
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}vowner WHERE status=0 ORDER BY name");
        $this->sm->assign("vowner", $this->m->getall($res1, 2, "name", "id_vowner"));
        $this->sm->assign("vehicle", $this->m->getall($this->m->query($sql)));
    }
}

?>