<?php

class area extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check_area() {
        $data = $_REQUEST['area'];
        $name = trim($data['name']);
        $sql = $this->create_select($this->prefix . "area", "name='$name' AND id!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function insert() {
        $this->get_permission("area", "INSERT"); //INSERT, UPDATE, DELETE, SELECT
        $data = $_REQUEST['area'];
        $data['id_create'] = $_SESSION['id_user'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['create_date'] = date("Y-m-d h:i:s");
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert($this->prefix . "area", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=area&func=listing");
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("area", "INSERT");
        else
            $this->get_permission("area", "UPDATE");

        $sql = "SELECT a.*, z.name AS zone_name FROM {$this->prefix}area a LEFT JOIN {$this->prefix}zone z ON a.id_zone=z.id_zone WHERE a.id_area=$id ";
        $sql = "SELECT a.* FROM {$this->prefix}area a WHERE a.id_area=$id ";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
        $opt6 = "SELECT id_represent AS id, name FROM {$this->prefix}represent ORDER BY name";
        $represent = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("represent", $represent);
        $this->sm->assign("page", "area/add.tpl.html");
    }

    function update() {
        $this->get_permission("area", "UPDATE"); //INSERT, UPDATE, DELETE, SELECT
        $data['id_modify'] = $_SESSION['id_user'];
        $res = $this->m->query($this->create_update($this->prefix . "area", $_REQUEST['area'], "id_area='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=area&func=listing");
    }

    function delete() {
        $this->get_permission("area", "DELETE"); //INSERT, UPDATE, DELETE, SELECT
        $res1 = $this->m->query($this->create_select($this->prefix . "sale", "id_area='{$_REQUEST['id']}'"));
        $res2 = $this->m->query($this->create_select($this->prefix . "head", "id_area='{$_REQUEST['id']}'"));
        if ($this->m->num_rows($res1) || $this->m->num_rows($res2)) {
            $_SESSION['msg'] = "There are Some Relevent Information Found With the Area<br>Area can't be Deleted";
        } else {
            $res = $this->m->query($this->create_delete($this->prefix . "area", "id_area='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=area&func=listing");
    }

    function listing() {
        $this->get_permission("area", "REPORT"); //INSERT, UPDATE, DELETE, SELECT
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " WHERE a.status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = "SELECT a.*, z.name AS zone_name FROM (SELECT b.*, r.name AS represent_name FROM {$this->prefix}area b
		LEFT JOIN {$this->prefix}represent r ON r.id_represent=b.id_represent) a
		LEFT JOIN {$this->prefix}zone z ON a.id_zone=z.id_zone $wcond ";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("area", $profile);
        $this->sm->assign("page", "area/listing.tpl.html");
    }

}

?>
