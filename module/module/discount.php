<?php

class discount extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function check_discount() {
        $data = $_REQUEST['discount'];
        $id_company = trim($data['id_company']);
        $id_head = $_REQUEST['id_head'];
        $sql = $this->create_select($this->prefix . "discount", "id_company='$id_company' AND id_head='$id_head' AND id!='{$_REQUEST['id']}'");
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data ? false : true);
        exit;
    }

    function insert() {
        $data = $_REQUEST['discount'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['id_create'] = $_SESSION['id_user'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['create_date'] = date("Y-m-d h:i:s");
        $res = $this->m->query($this->create_insert($this->prefix . "discount", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=discount&func=listing&ack=1");
    }

    function edit() {
        $id_group = array_search("Sundry debtor", $this->ini['id_group'], true);
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select($this->prefix . "discount", "id_discount='{$id}'");
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
        $sql = "SELECT id_company AS id, name FROM " . $this->prefix . "company WHERE status=0";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
        $sql = "SELECT id_head AS id, name FROM " . $this->prefix . "head WHERE id_group='{$id_group}' AND status=0";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));

        $this->sm->assign("page", "discount/add.tpl.html");
    }

    function update() {
        $dis = $_REQUEST['discount'];
        $dis['id_modify'] = $_SESSION['id_user'];
        $sql = "UPDATE {$this->prefix}discount SET discount={$dis['discount']} WHERE id_discount='{$_REQUEST['id']}'";
        $res = $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=discount&func=listing&ack=1");
    }

    function listing() {
        $id_group = array_search("Sundry debtor", $this->ini['id_group'], true);
        if (isset($_REQUEST['ack'])) {
            $id_company = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : "";
            $id_head = isset($_REQUEST['id_head']) ? $_REQUEST['id_head'] : "";
            if ($id_head == '' && $id_company == '') {
                $sql = "SELECT * FROM {$this->prefix}discount";
            } else if ($id_company == '' || $id_head == '') {
                if ($id_company == '' && $id_head != '') {
                    $num = $this->m->num_rows($this->m->query($this->create_select($this->prefix . "discount", "id_head='$id_head'")));
                    if ($num == 0) {
                        $sql = "INSERT INTO `{$this->prefix}discount` (id_head,id_company,discount)
                            (SELECT $id_head as id_head, id_company, 0 as discount FROM {$this->prefix}company WHERE status=0)";
                        $this->m->query($sql);
                    }
                } else if ($id_head == '' && $id_company != '') {
                    $num = $this->m->num_rows($this->m->query($this->create_select($this->prefix . "discount", "id_company='$id_company'")));
                    if ($num == 0) {
                        $sql = "INSERT INTO `{$this->prefix}discount` (id_company,id_head,discount)
                            (SELECT $id_company as id_company, id_head, 0 as discount FROM {$this->prefix}head WHERE id_group='{$id_group}' AND status=0)";
                        $this->m->query($sql);
                    }
                }
                $sql = "SELECT * FROM {$this->prefix}discount WHERE id_head='$id_head' OR id_company='$id_company'";
            } else {
                $sql = "SELECT * FROM {$this->prefix}discount  WHERE id_head='$id_head' AND id_company='$id_company'";
            }
            $data = $this->m->sql_getall($sql);
            $this->sm->assign("sdata", $data);
        }
        $sql = "SELECT id_head AS id, name FROM " . $this->prefix . "head WHERE id_group='{$id_group}' AND status=0";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));
        $sql = "SELECT id_company AS id, name FROM " . $this->prefix . "company WHERE status=0";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "discount/listing.tpl.html");
    }

    function delete() {
        $res = $this->m->query($this->create_delete($this->prefix . "discount", "id_discount='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=discount&func=listing&ack=1");
    }

}

?>
