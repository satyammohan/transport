<?php

class represent extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
	$this->addfield('email', $this->prefix . 'represent', 'ADD COLUMN `email` text');
    }

    function check_reps() {
        $data = $_REQUEST['reps'];
        $name = trim($data['name']);
        $sql = $this->create_select("{$this->prefix}represent", "name='$name' AND id_represent!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function insert() {
        $this->get_permission("represent", "INSERT");
        $data = $_REQUEST['reps'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert("{$this->prefix}represent", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=represent&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("represent", "INSERT");
        else
            $this->get_permission("represent", "UPDATE");
        $sql = $this->create_select("{$this->prefix}represent", "id_represent='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "represent/add.tpl.html");
    }

    function update() {
        $this->get_permission("represent", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}represent", $_REQUEST['reps'], "id_represent='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=represent&func=listing");
    }

    function delete() {
        $this->get_permission("represent", "DELETE");
        $res = $this->m->query($this->create_delete("{$this->prefix}represent", "id_represent='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=represent&func=listing");
    }

    function listing() {
        $this->get_permission("represent", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = $this->create_select("{$this->prefix}represent", "$wcond");
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("reps", $profile);
        $this->sm->assign("page", "represent/listing.tpl.html");
    }
    function user() {
        $this->addfield('username', $this->prefix . 'represent', 'ADD `username` varchar(20)');
        $this->addfield('password', $this->prefix . 'represent', 'ADD `password` varchar(20)');
        $sql = "SELECT * FROM {$this->prefix}represent ORDER BY name";
        $represent = $this->m->sql_getall($sql, 1, "", "id_represent");
        $this->sm->assign("represent", $represent);
        $this->sm->assign("page", "represent/user.tpl.html");
    }
    function setfieldvalue() {
        $this->get_permission("represent", "UPDATE");
        $data[$_REQUEST['field']] = $_REQUEST['fvalue'];
        $this->m->query($this->create_update("{$this->prefix}represent", $data, "id_represent='{$_REQUEST['id']}'"));
        echo 1;
        exit;
    }

}

?>
