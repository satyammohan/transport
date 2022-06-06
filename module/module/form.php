<?php

class form extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check_name() {
        $data = $_REQUEST['frm'];
        $name = trim($data['name']);
        $sql = $this->create_select($this->prefix . "form", "name='$name' AND id_form!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function insert() {
        $this->get_permission("form", "INSERT");
        $data = $_REQUEST['frm'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert($this->prefix . "form", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=form&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("form", "INSERT");
        else
            $this->get_permission("form", "UPDATE");
        $sql = $this->create_select($this->prefix . "form", "id_form='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "form/add.tpl.html");
    }

    function update() {
        $this->get_permission("form", "UPDATE");
        $res = $this->m->query($this->create_update($this->prefix . "form", $_REQUEST['frm'], "id_form='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=form&func=listing");
    }

    function delete() {
        $this->get_permission("form", "DELETE");
        $res = $this->m->query($this->create_delete($this->prefix . "form", "id_form='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=form&func=listing");
    }

    function listing() {
        $this->get_permission("form", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = $this->create_select($this->prefix . "form", "$wcond");
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("frm", $profile);
        $this->sm->assign("page", "form/listing.tpl.html");
    }

}

?>