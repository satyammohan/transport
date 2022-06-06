<?php
class company extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("company", "INSERT");
        $data = $_REQUEST['comp'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert($this->prefix . "company", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=company&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("company", "INSERT");
        else
            $this->get_permission("company", "UPDATE");

        $sql = $this->create_select($this->prefix . "company", "id_company='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function update() {
        $this->get_permission("company", "UPDATE");
        $res = $this->m->query($this->create_update($this->prefix . "company", $_REQUEST['comp'], "id_company='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=company&func=listing");
    }
    function delete() {
        $this->get_permission("company", "DELETE");
        // $res = $this->m->query($this->create_delete($this->prefix . "company", "id_company='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=company&func=listing");
    }
    function listing() {
        $this->get_permission("company", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " WHERE status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = "SELECT * FROM {$this->prefix}company $wcond ORDER BY name";
        $this->sm->assign("comp", $this->m->getall($this->m->query($sql)));
    }
}
?>