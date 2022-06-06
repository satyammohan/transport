<?php

class zone extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check_zone() {
        $data = $_REQUEST['zone'];
        $name = trim($data['name']);
        $sql = $this->create_select("{$this->prefix}zone", "name='$name' AND id_zone!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function getzone() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT id_zone AS id, name FROM {$this->prefix}zone WHERE name LIKE '%{$filt}%' AND status=0 ORDER BY name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function insert() {
        $this->get_permission("zone", "INSERT");
        $data = $_REQUEST['zone'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert("{$this->prefix}zone", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=zone&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("zone", "INSERT");
        else
            $this->get_permission("zone", "UPDATE");

        $this->sm->assign("data", $this->m->fetch_assoc($this->create_select("{$this->prefix}zone", "id_zone='{$id}'")));
        $this->sm->assign("page", "zone/add.tpl.html");
    }

    function update() {
        $this->get_permission("area", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}zone", $_REQUEST['zone'], "id_zone='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=zone&func=listing");
    }

    function delete() {
        $this->get_permission("zone", "DELETE");
        $res1 = $this->m->query($this->create_select("{$this->prefix}area", "id_zone='{$_REQUEST['id']}'"));
        if ($this->m->num_rows($res1) > 0) {
            $_SESSION['msg'] = "Zone can not be Deleted<br>Some Area belong to this Zone Exist ";
        } else {
            $res = $this->m->query($this->create_delete("{$this->prefix}zone", "id_zone='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=zone&func=listing");
    }

    function listing() {
        $this->get_permission("zone", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " WHERE status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $profile = $this->m->getall($this->m->query("SELECT * FROM {$this->prefix}zone $wcond ORDER BY name"));
        $this->sm->assign("zone", $profile);
        $this->sm->assign("page", "zone/listing.tpl.html");
    }

}

?>