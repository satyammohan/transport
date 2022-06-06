<?php
class banquet extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $data = $_REQUEST['t'];
        $data['id_create'] = $_SESSION['id_user'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $sql = $this->create_insert("{$this->prefix}banquet", $data);
        $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=banquet&func=listing");
    }
    function update() {
        $data = $_REQUEST['t'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['modify_date'] = date("Y-m-d");
        $sql = $this->create_update("{$this->prefix}banquet", $data, "id_banquet='{$_REQUEST['id']}'");
        $this->m->query($sql);
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=banquet&func=listing");
    }
    function edit() {
        $sql = "SELECT id_taxmaster AS id, tax_per AS name FROM {$this->prefix}taxmaster";
        $tax = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("tax", $tax);
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id) {
            $sql = $this->create_select("{$this->prefix}banquet", "id_banquet='{$id}'");
            $data = $this->m->fetch_assoc($sql);
        } else {
            $sql = "SELECT max(no) AS no, max(bill) AS bill FROM {$this->prefix}banquet WHERE cancel_by=0";
            $data = $this->m->fetch_assoc($sql);
            $data['no'] = $data['no'] + 1;
            $data['bill'] = $data['bill'] + 1;
        }
        $this->sm->assign("data", $data);
      
    }
    function listing() {
        $sql = "SELECT b.*, SUM(m.amount) AS received FROM {$this->prefix}banquet b LEFT JOIN {$this->prefix}mr m
            ON m.id_reservation=b.id_banquet AND m.cancel_date IS NULL AND m.mrtype='B' WHERE b.cancel_by=0 GROUP BY b.id_banquet ORDER BY b.date";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $data);
    }
    function cancel() {
        if (isset($_REQUEST['id'])) {
            if (!isset($_REQUEST['save'])) {
                return;
            }
            $id = $_REQUEST['id'];
            $data['cancel_by'] = $_SESSION['id_user'];
            $data['cancel_date'] = date("Y-m-d");
            $data['cancel_reason'] = $_REQUEST['reason'];
            $sql = $this->create_update("{$this->prefix}banquet", $data, "id_banquet='$id'");
            $this->m->query($sql);
            $_SESSION['msg'] = "Banquet Booking Cancellation Successfully.";
        } else {
            $_SESSION['msg'] = "Banquet Booking not found for Cancellation.";
        }
        $this->redirect("index.php?module=banquet&func=listing");
    }
    function cancellist() {
        $sql = "SELECT r.*, u.name AS uname FROM {$this->prefix}banquet r, user u
            WHERE r.cancel_by=u.id_user AND r.cancel_by ORDER BY cancel_date, date";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $data);
    }
    function printbanquet() {
        $id = $_REQUEST['id'];

        $sql = "SELECT id_reservation, GROUP_CONCAT(no) AS nos, SUM(amount) AS total  FROM {$this->prefix}mr WHERE id_reservation='{$id}' AND cancel_date IS NULL AND mrtype='B'";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("mr", $data);

        $sql = "SELECT r.*, u.name AS username FROM {$this->prefix}banquet r LEFT JOIN user u ON r.id_create=u.id_user  WHERE r.id_banquet='{$id}'";
        $data = $this->m->fetch_assoc($sql);
        $data['w'] = $this->convert_number($data['total']);
        $this->sm->assign("data", $data);
    }
    function printmr() {
        $id = $_REQUEST['id'];
        $sql = "SELECT r.roomnumber, r.name, r.address, m.*, u.name AS username 
            FROM {$this->prefix}mr m LEFT JOIN user u ON m.id_user=u.id_user, {$this->prefix}banquet r 
            WHERE m.id_reservation=r.id_banquet AND m.id='$id' ORDER BY m.date DESC";
        $data = $this->m->getall($this->m->query($sql));
        $data[0]['w'] = $this->convert_number($data[0]['amount']);
        $this->sm->assign("data", $data[0]);
        $this->sm->assign("page", "reservation/printmr.tpl.html");
    }
    function checkno() {
        ob_clean();
        $no = isset($_REQUEST['no']) ? trim($_REQUEST['no']) : "";
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "reservation";
        $sql = "SELECT COUNT(*) AS cnt FROM {$this->prefix}{$type} WHERE no='$no'";
        $data = $this->m->fetch_assoc($sql);
        echo ($no=='' || $data['cnt']);
        exit;
    }
    function addmr() {
    }
    function savemr() {
        $data = $_REQUEST['mr'];
        $data['id_user'] = $_SESSION['id_user'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['mrtype'] = "B";
        $sql = $this->create_insert("{$this->prefix}mr", $data);
        $this->m->query($sql);
        $_SESSION['msg'] = "MR Successfully Saved.";
        $this->redirect("index.php?module=banquet&func=listing");
    }
}
?>
