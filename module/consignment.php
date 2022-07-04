<?php
class consignment extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $this->get_permission("bill", "INSERT");
        $data = $_REQUEST['bill'];        
        $data['id_create'] = $_SESSION['id_user'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['create_date'] = date("Y-m-d h:i:s");
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert("{$this->prefix}bill", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=bill&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("bill", "INSERT");
        else
            $this->get_permission("bill", "UPDATE");
        $sql = "SELECT * FROM {$this->prefix}bill WHERE id_bill='$id'";
        $this->sm->assign("data", $this->m->fetch_assoc($sql));
    }
    function update() {
        $this->get_permission("bill", "UPDATE");
        $res = $this->m->query($this->create_update("{$this->prefix}bill", $_REQUEST['bill'], "id_bill='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=bill&func=listing");
    }
    function delete() {
        $this->get_permission("bill", "DELETE");
        // $res = $this->m->query($this->create_delete("{$this->prefix}bill", "id_bill='{$_REQUEST['id']}'"));
        // $_SESSION['msg'] = "Record Successfully Deleted";
        $_SESSION['msg'] = "Delete disabled. Action not Successful";
        $this->redirect("index.php?module=bill&func=listing");
    }
    function listing() {
        $this->get_permission("bill", "REPORT");
        $sdate = $_REQUEST['start_date'] = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d",strtotime('-7 day'));
        $edate = $_REQUEST['end_date'] = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $wcond = @$_REQUEST['vehno'] ? " vehno = '".trim($_REQUEST['vehno'])."'" : " 1 ";
        $wcond .= $sdate ? " AND (date >= '$sdate' AND date <= '$edate')" : "";
        $sql = "SELECT * FROM {$this->prefix}bill WHERE $wcond ORDER BY date, invno LIMIT 100 ";
        $this->sm->assign("bill", $this->m->getall($this->m->query($sql)));
    }
    function printbal() {
        echo "printbal";
        exit;
    }
    function printadv() {
        echo "printadv";
        exit;
    }
}
?>