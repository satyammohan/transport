<?php
class vowner extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function listing() {
        $this->get_permission("vowner", "REPORT");
        if (isset($_REQUEST['status'])) {
            $wcond = ($_REQUEST['status'] == 2) ?  "" : " WHERE status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = "SELECT * FROM {$this->prefix}vowner $wcond ORDER BY name";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("vowner", $data);
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $this->get_permission("vowner", ($id ? "INSERT" : "UPDATE"));
        $sql = $this->create_select($this->prefix . "vowner", "id_vowner='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function insert() {
        $this->get_permission("vowner", "INSERT");
        $data = $_REQUEST['comp'];
        $sql = $this->create_insert($this->prefix . "vowner", $data);
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=vowner&func=listing");
    }
    function update() {
        $this->get_permission("vowner", "UPDATE");
        $res = $this->m->query($this->create_update($this->prefix . "vowner", $_REQUEST['comp'], "id_vowner='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=vowner&func=listing");
    }
    function delete() {
        $this->get_permission("vowner", "DELETE");
        // $res = $this->m->query($this->create_delete($this->prefix . "vowner", "id_vowner='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=vowner&func=listing");
    }
}
?>