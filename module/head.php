<?php
class head extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function getgroup() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT id_group AS id, name FROM `{$this->prefix}group` WHERE name LIKE '{$filt}%' AND status=0 ORDER BY name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }
    function insert() {
        $this->get_permission("head", "INSERT");
        $data = $_REQUEST['head'];
        $data['status'] = 0;
        $data['id_create'] = $_SESSION['id_user'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['create_date'] = date("Y-m-d h:i:s");
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['name'] = addslashes($data['name']);
        $data['doa'] = $this->format_date($data['doa']);
        $data['doa'] = $data['doa'] ? $data['doa'] : "0000-00-00";
        $data['dob'] = $this->format_date($data['dob']);
        $data['dob'] = $data['dob'] ? $data['dob'] : "0000-00-00";
        $sql = $this->create_insert("{$this->prefix}head", $data);
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=head&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == "0")
            $this->get_permission("head", "INSERT");
        else
            $this->get_permission("head", "UPDATE");
        $data = $this->m->fetch_assoc($this->create_select("{$this->prefix}head", "id_head='{$id}'"));

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $res3 = $this->m->query("SELECT * FROM `{$this->prefix}group` WHERE status=0  ORDER BY name");
        $this->sm->assign("group", $this->m->getall($res3, 2, "name", "id_group"));

        $this->sm->assign("data", $data);
        $this->sm->assign("page", "head/add.tpl.html");
    }
    function update() {
        $this->get_permission("head", "UPDATE");
        $data = $_REQUEST['head'];
        $data['name'] =  addslashes($data['name']);
        $data['doa'] = $this->format_date($data['doa']);
        $data['doa'] = $data['doa'] ? $data['doa'] : "0000-00-00";
        $data['dob'] = $this->format_date($data['dob']);
        $data['dob'] = $data['dob'] ? $data['dob'] : "0000-00-00";
        $sql = $this->create_update("{$this->prefix}head", $data, "id_head='{$_REQUEST['id']}'");
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=head&func=listing");
    }
    function delete() {
        $this->get_permission("head", "DELETE");
        // $res = $this->m->query($this->create_select("{$this->prefix}sale", "id_head='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=head&func=listing");
    }
    function listing() {
        $this->get_permission("head", "REPORT");
        $sql = "SELECT h.*, g.name AS gname, a.name AS area
                FROM {$this->prefix}head h LEFT JOIN {$this->prefix}group g ON h.id_group=g.id_group  
                    LEFT JOIN {$this->prefix}area a ON h.id_area=a.id_area";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $profile);
    }
}
?>