<?php

class series extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check() {
        $data = $_REQUEST['series'];
        $name = trim($data['name']);
        $sql = $this->create_select("{$this->prefix}series", "name='$name' AND id_series!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function insert() {
        $data = $_REQUEST['series'];
        $data['name'] = strtoupper($data['name']);
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert("{$this->prefix}series", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=series&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select("{$this->prefix}series", "id_series='$id'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "series/add.tpl.html");
    }

    function update() {
        $data = $_REQUEST['series'];
        $data['name'] = strtoupper($data['name']);
        $res = $this->m->query($this->create_update("{$this->prefix}series", $data, "id_series='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=series&func=listing");
    }

    function delete() {
        $res = $this->m->query($this->create_delete("{$this->prefix}series", "id_series='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=series&func=listing");
    }

    function listing() {
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " WHERE status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = "SELECT * FROM {$this->prefix}series $wcond ";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("series", $profile);
        $this->sm->assign("page", "series/listing.tpl.html");
    }

}

?>
