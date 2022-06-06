<?php
class group extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("group", "INSERT");
        $data = $_REQUEST['group'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['id_create'] = $_SESSION['id_user'];
        $data['status'] = 0;
        $sql = $this->create_insert("{$this->prefix}group", $data);
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=group&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("group", "INSERT");
        else
            $this->get_permission("group", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}group g WHERE g.id_group='{$id}'";
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}group WHERE status=0 ORDER BY name");
        $this->sm->assign("group", $this->m->getall($res1, 2, "name", "id_group"));
        $this->sm->assign("page", "group/add.tpl.html");
    }
    function update() {
        $this->get_permission("group", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}group", $_REQUEST['group'], "id_group='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=group&func=listing");
    }
    function delete() {
        $this->get_permission("group", "DELETE");
        $res1 = $this->m->query($this->create_select("{$this->prefix}head", "id_group='{$_REQUEST['id']}'"));
        if ($this->m->num_rows($res1) > 0) {
            $_SESSION['msg'] = "Group Can't be Deleted!";
        } else {
            $res = $this->m->query($this->create_delete("{$this->prefix}group", "id_group='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=group&func=listing");
    }
    function listing() {
        $this->get_permission("group", "REPORT");
        $wcond = isset($_REQUEST['status']) ?  "WHERE g.status={$_REQUEST['status']}" : "";
        $sql = "SELECT g.*, g1.name AS pname FROM {$this->prefix}group g LEFT JOIN {$this->prefix}group g1 ON g.id_parent=g1.id_group $wcond ";
        $this->sm->assign("group", $this->m->getall($this->m->query($sql)));
    }
}
?>