<?php
class product extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("product", "INSERT");
        $data = $_REQUEST['product'];
        $res = $this->m->query($this->create_insert("{$this->prefix}product", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=product&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("product", "INSERT");
        else
            $this->get_permission("product", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}product WHERE id_product='$id'";
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}vowner WHERE status=0 ORDER BY name");
        $this->sm->assign("vowner", $this->m->getall($res1, 2, "name", "id_vowner"));
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("product", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}product", $_REQUEST['product'], "id_product='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=product&func=listing");
    }
    function delete() {
        $this->get_permission("product", "DELETE");
        // $res = $this->m->query($this->create_delete("{$this->prefix}product", "id_product='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=product&func=listing");
    }
    function listing() {
        $this->get_permission("product", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE status={$_REQUEST['status']}" : "";
        $sql = "SELECT * FROM {$this->prefix}product $wcond ";
        $this->sm->assign("product", $this->m->getall($this->m->query($sql)));
    }
}
?>