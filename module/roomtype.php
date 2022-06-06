<?php
class roomtype extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->image();
        $data = $_REQUEST['rt'];
        $res = $this->m->query($this->create_insert("{$this->prefix}roomtype", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=roomtype&func=listing");
    }
    function update() {
        $this->image();
        $data = $_REQUEST['rt'];
        $sql = $this->create_update("{$this->prefix}roomtype", $data, "id_roomtype='{$_REQUEST['id']}'");
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=roomtype&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT id_taxmaster AS id, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $tax = $this->m->getall($this->m->query($sql), 2, "tax_per", "id");
        $this->sm->assign("tax", $tax);

        $sql = $this->create_select("{$this->prefix}roomtype", "id_roomtype='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function image() {
        if ($_FILES['image']['tmp_name'] != '') {
            $exts = explode(".", $_FILES['image']['name']);
            $ext = $exts[count($exts)-1];
            $fname = strtotime(date('y-m-d H:i:s')).'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'],'./assets/img/'. $fname);
            $_REQUEST['rt']['image'] = $fname;
        }
    }
    function delete() {
        $_SESSION['msg'] = "For Security Reasons this optio n is Disabled";
        $this->redirect("index.php?module=roomtype&func=listing");
    }
    function listing() {
        $sql = "SELECT r.*, t.tax_per FROM {$this->prefix}roomtype r LEFT JOIN {$this->prefix}taxmaster t ON r.id_taxmaster=t.id_taxmaster ORDER BY id_roomtype";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("roomtype", $profile);
    }
}
?>