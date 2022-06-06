<?php
class item extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("item", "INSERT");
        $data = $_REQUEST['item'];
        $data['weight'] = $data['weight'] ? $data['weight'] : 0;
        $sql = $this->create_insert("{$this->prefix}item", $data);
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=item&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("item", "INSERT");
        else
            $this->get_permission("item", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}item WHERE id_item='$id'";
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}product WHERE status=0 ORDER BY name");
        $this->sm->assign("product", $this->m->getall($res1, 2, "name", "id_product"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("item", "UPDATE");
        $data = $_REQUEST['item'];
        $data['weight'] = $data['weight'] ? $data['weight'] : 0;
        $res = $this->m->query($this->create_update("{$this->prefix}item", $data, "id_item='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=item&func=listing");
    }
    function delete() {
        $this->get_permission("item", "DELETE");
        // $res = $this->m->query($this->create_delete("{$this->prefix}item", "id_item='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=item&func=listing");
    }
    function listing() {
        $this->get_permission("item", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE status={$_REQUEST['status']}" : "";
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}product WHERE status=0 ORDER BY name");
        $this->sm->assign("product", $this->m->getall($res1, 2, "name", "id_product"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));

        $sql = "SELECT * FROM {$this->prefix}item $wcond ";
        $this->sm->assign("item", $this->m->getall($this->m->query($sql)));
    }
}
?>