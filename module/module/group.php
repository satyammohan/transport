<?php

class group extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function getgroup() {
        $res = $this->m->query("SELECT id_group AS id,name,id_parent FROM {$this->prefix}group GROUP BY id");
        $data = $this->m->getall($res);
        echo(json_encode($data));
        exit;
    }

    function check_name() {
        $data = $_REQUEST['group'];
        $name = trim($data['name']);
        $sql = $this->create_select("{$this->prefix}group", "name='$name' AND id_group!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function getparent() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT id_group AS id, name FROM {$this->prefix}group  WHERE name LIKE '{$filt}%' ORDER BY name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function insert() {
        $this->get_permission("group", "INSERT");
        $data = $_REQUEST['group'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['id_create'] = $_SESSION['id_user'];
        $res = $this->m->query($this->create_insert("{$this->prefix}group", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=group&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("group", "INSERT");
        else
            $this->get_permission("group", "UPDATE");
        $sql = "SELECT g.*, g1.name AS gname FROM {$this->prefix}group g LEFT JOIN {$this->prefix}group g1 ON g.id_parent=g1.id_group WHERE g.id_group='{$id}'";
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
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

    function group($wcond) {
        $sql = $this->create_select("{$this->prefix}group", "id_parent='0'$wcond ");
        $profile = $this->m->getall($this->m->query($sql));
        return $profile;
    }

    function listing() {
        $this->get_permission("group", "REPORT");
        $sql = "SELECT g.*, g1.name AS pname FROM {$this->prefix}group g LEFT JOIN {$this->prefix}group g1 ON g.id_parent=g1.id_group";
        $this->sm->assign("group", $this->m->getall($this->m->query($sql)));
        $this->sm->assign("page", "group/listing.tpl.html");
    }

    function tree() {
        $this->sm->assign("page", "group/list.tpl.html");
    }

}

?>