<?php
class rate extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("rate", "INSERT");
        $data = $_REQUEST['rate'];
        $sql = $this->create_insert("{$this->prefix}rate", $data);
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=rate&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("rate", "INSERT");
        else
            $this->get_permission("rate", "UPDATE");

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}head WHERE status=0 ORDER BY name");
        $this->sm->assign("head", $this->m->getall($res1, 2, "name", "id_head"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $res3 = $this->m->query("SELECT * FROM `{$this->prefix}mode` WHERE status=0  ORDER BY name");
        $this->sm->assign("mode", $this->m->getall($res3, 2, "name", "id_mode"));

        $sql = "SELECT * FROM {$this->prefix}rate WHERE id_rate='$id'";
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("rate", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}rate", $_REQUEST['rate'], "id_rate='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=rate&func=listing");
    }
    function delete() {
        $this->get_permission("rate", "DELETE");
        //$res = $this->m->query($this->create_delete("{$this->prefix}rate", "id_rate='{$_REQUEST['id']}'"));
        //$_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=rate&func=listing");
    }
    function listing() {
        $this->get_permission("rate", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE status={$_REQUEST['status']}" : "";
        $sql = "SELECT * FROM {$this->prefix}rate $wcond ";

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}head WHERE status=0 ORDER BY name");
        $this->sm->assign("head", $this->m->getall($res1, 2, "name", "id_head"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $res3 = $this->m->query("SELECT * FROM `{$this->prefix}mode` WHERE status=0  ORDER BY name");
        $this->sm->assign("mode", $this->m->getall($res3, 2, "name", "id_mode"));

        $this->sm->assign("rate", $this->m->getall($this->m->query($sql)));
    }
}
?>