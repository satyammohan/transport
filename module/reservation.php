<?php
class reservation extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $_SESSION['msg'] = "This option not used any more.";
        $this->redirect("index.php?module=reservation&func=listing");
        // $data = $_REQUEST['t'];
        // $data['id_create'] = $_SESSION['id_user'];
        // $data['create_date'] = date("Y-m-d");
        // $sql = $this->create_insert("{$this->prefix}reservation", $data);
        // $this->m->query($sql);
        // $_SESSION['msg'] = "Room Booking Added Successfully.";
        // $this->redirect("index.php?module=reservation&func=listing");
    }
    function update() {
        $_SESSION['msg'] = "This option not used any more.";
        $this->redirect("index.php?module=reservation&func=listing");        $data = $_REQUEST['t'];
        // $data['id_modify'] = $_SESSION['id_user'];
        // $data['modify_date'] = date("Y-m-d");
        // $sql = $this->create_update("{$this->prefix}reservation", $data, "id_reservation='{$_REQUEST['id']}'");
        // $this->m->query($sql);
        // $_SESSION['msg'] = "Room Booking Updated Successfully.";
        // $this->redirect("index.php?module=reservation&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT id_taxmaster AS id, tax_per AS name FROM {$this->prefix}taxmaster";
        $tax = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("tax", $tax);
        $sql = "SELECT id_roomtype AS id, name FROM {$this->prefix}roomtype ORDER BY name";
        $rt = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("rt", $rt);
        $sql = "SELECT id_corporate AS id, name FROM {$this->prefix}corporate ORDER BY name";
        $rt = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("cor", $rt);
        $sql = $this->create_select("{$this->prefix}reservation", "id_reservation='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function editinfo() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select("{$this->prefix}reservation", "id_reservation='{$id}'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function listing() {
        $sql = "SELECT * FROM {$this->prefix}reservation WHERE cancel_by=0 ORDER BY no DESC, name";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $data);
    }
    function cancellist() {
        $sql = "SELECT r.*, u.name AS uname FROM {$this->prefix}reservation r, user u
            WHERE r.cancel_by=u.id_user AND r.cancel_by ORDER BY cancel_date, cin, name";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $data);
    }
    function mrlist() {
        $sql = "SELECT r.roomnumber, r.name, m.* FROM {$this->prefix}mr m, {$this->prefix}reservation r 
                WHERE m.id_reservation=r.id_reservation ORDER BY m.date DESC";
        $sql = "SELECT r.roomnumber, r.name, m.* FROM {$this->prefix}mr m, {$this->prefix}reservation r WHERE m.id_reservation=r.id_reservation AND m.mrtype!='B'
                UNION ALL
                SELECT r.roomnumber, r.name, m.* FROM {$this->prefix}mr m, {$this->prefix}banquet r WHERE m.id_reservation=r.id_banquet AND m.mrtype='B'
                ORDER BY date DESC";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("mr", $data);
    }
    function addmr() {
    }
    function savemr() {
        $data = $_REQUEST['mr'];
        $data['id_user'] = $_SESSION['id_user'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->m->query($this->create_insert("{$this->prefix}mr", $data));
        $_SESSION['msg'] = "MR Successfully Saved.";
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function printmr() {
        $id = $_REQUEST['id'];
        $sql = "SELECT r.roomnumber, r.name, r.address, r.phone, r.email, m.*, u.name AS username FROM {$this->prefix}mr m LEFT JOIN user u ON m.id_user=u.id_user, {$this->prefix}reservation r 
            WHERE m.id_reservation=r.id_reservation AND m.id='$id' ORDER BY m.date DESC";
        $data = $this->m->getall($this->m->query($sql));
        $data[0]['w'] = $this->convert_number($data[0]['amount']);
        $this->sm->assign("data", $data[0]);
    }
    function addfood() {
        $sql = "SELECT id_taxmaster AS id, tax_per AS name FROM {$this->prefix}taxmaster";
        $tax = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("tax", $tax);
    }
    function savefood() {
        $data = $_REQUEST['food'];
        $data['id_user'] = $_SESSION['id_user'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->m->query($this->create_insert("{$this->prefix}food", $data));
        $_SESSION['msg'] = "Food Bill Successfully Saved.";
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function foodlist() {
        $sql = "SELECT r.roomnumber, r.name, m.* FROM {$this->prefix}food m, {$this->prefix}reservation r 
                WHERE m.id_reservation=r.id_reservation ORDER BY m.date DESC";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("food", $data);
    }
    function addother() {
        $sql = "SELECT id_taxmaster AS id, tax_per AS name FROM {$this->prefix}taxmaster";
        $tax = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("tax", $tax);
    }
    function saveother() {
        $data = $_REQUEST['other'];
        $data['id_user'] = $_SESSION['id_user'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $this->m->query($this->create_insert("{$this->prefix}other", $data));
        $_SESSION['msg'] = "Other Bill Successfully Saved.";
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function otherlist() {
        $sql = "SELECT r.roomnumber, r.name, m.* FROM {$this->prefix}other m, {$this->prefix}reservation r 
                WHERE m.id_reservation=r.id_reservation ORDER BY m.date DESC";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("other", $data);
    }
    function dashboard() {
        if (isset($_REQUEST['date'])) {
            $date = $_REQUEST['date'];
        } else {
            $date = $_REQUEST['date'] = date('Y-m-d');
        }
        $sql = "SELECT * FROM {$this->prefix}rooms ORDER BY name";
        $rooms = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $rooms);

        $sql = "SELECT group_concat(roomnumber) AS roomnumber FROM {$this->prefix}reservation 
                WHERE date='{$date}' AND (time='' OR time IS NULL) AND !cancel_by";
        $data = $this->m->fetch_assoc($sql);
        $data = $this->m->fetch_assoc($sql);
        $data['roomnumber'] = str_replace(".", ",", $data['roomnumber']);
        $data['roomnumber'] = str_replace(";", ",", $data['roomnumber']);
        $data['roomnumber'] = str_replace(":", ",", $data['roomnumber']);
        $rroom =  explode(',', $data['roomnumber']);
        $this->sm->assign("reserve", array_flip($rroom));

        $sql = "SELECT group_concat(roomnumber) AS roomnumber FROM {$this->prefix}reservation 
                WHERE (('{$date}' BETWEEN date AND depature_date) OR (date <= '{$date}' AND depature_date IS NULL)) AND !cancel_by";
        $data = $this->m->fetch_assoc($sql);
        $data['roomnumber'] = str_replace(".", ",", $data['roomnumber']);
        $data['roomnumber'] = str_replace(";", ",", $data['roomnumber']);
        $data['roomnumber'] = str_replace(":", ",", $data['roomnumber']);
        $aroom =  explode(',', $data['roomnumber']);
        $this->sm->assign("data", array_flip($aroom));
    }
    function changeroom() {
        $id = $_REQUEST['id'];
        $sql = "SELECT * FROM {$this->prefix}rooms ORDER BY name";
        $rooms = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $rooms);
        $sql = "SELECT * FROM {$this->prefix}reservation WHERE id_reservation='{$id}'";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("guest", $data);
        $data['roomnumber'] = str_replace(".", ",", $data['roomnumber']);
        $data['roomnumber'] = str_replace(";", ",", $data['roomnumber']);
        $data['roomnumber'] = str_replace(":", ",", $data['roomnumber']);
        $aroom =  explode(',', $data['roomnumber']);
        $this->sm->assign("roomnumber", $data['roomnumber'].",");
        $this->sm->assign("data", array_flip($aroom));
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
    function checkin() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT id_taxmaster AS id, tax_per AS name FROM {$this->prefix}taxmaster";
        $tax = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("tax", $tax);
        $sql = "SELECT id_roomtype AS id, name FROM {$this->prefix}roomtype ORDER BY name";
        $rt = $this->m->getall($this->m->query($sql), 2, "name", "id");
        $this->sm->assign("rt", $rt);
        if ($id) {
            $sql = $this->create_select("{$this->prefix}reservation", "id_reservation='{$id}'");
            $data = $this->m->fetch_assoc($sql);
        } else {
            $sql = "SELECT max(no) AS no, max(grcno) AS grcno FROM {$this->prefix}reservation";
            $data = $this->m->fetch_assoc($sql);
            $data['no'] = $data['no'] + 1;
            $data['grcno'] = $data['grcno'] + 1;
        }
        $this->sm->assign("data", $data);
        if ($_SESSION['is_admin']!=1 && $id) {
            $this->sm->assign("page", "reservation/checkinsmall.tpl.html");
        }
    }
    function getroomprice() {
        $rooms = $_REQUEST['rooms'];
        $rooms = str_replace(".", ",", $rooms);
        $rooms = str_replace(";", ",", $rooms);
        $rooms = str_replace(":", ",", $rooms);
        $sql = "SELECT r.name, t.id_taxmaster, t.price, x.tax_per 
                FROM {$this->prefix}rooms r, {$this->prefix}roomtype t, {$this->prefix}taxmaster x
                WHERE r.status=0 AND r.id_roomtype=t.id_roomtype AND t.id_taxmaster=x.id_taxmaster AND r.name in ($rooms) ORDER BY r.id_rooms";
        $data = $this->m->getall($this->m->query($sql));
        ob_clean();
        echo json_encode($data);
        exit;
    }
    function checkinupdateinsert() {
        $data = $_REQUEST['t'];
        $room = $_REQUEST['room'];
        $data['id_create'] = $_SESSION['id_user'];
        $data['create_date'] = date("Y-m-d");
        $data['json'] = json_encode($room);
        foreach ($room as $v) {
            $rid = $v['name'];
            $sql = "UPDATE {$this->prefix}rooms SET status=3 WHERE name='$rid'";
            $this->m->query($sql);
        }
        $sql = $this->create_insert("{$this->prefix}reservation", $data);
        $this->m->query($sql);
        $_SESSION['msg'] = "Guest Registration Added Successfully.";
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function checkinupdate() {
        $data = $_REQUEST['t'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['modify_date'] = date("Y-m-d");
        $sql = $this->create_update("{$this->prefix}reservation", $data, "id_reservation='{$_REQUEST['id']}'");
        $this->m->query($sql);
        $_SESSION['msg'] = "Guest Registration Updated Successfully.";
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function checkout() {
        $sql = "SELECT * FROM {$this->prefix}reservation WHERE cancel_by=0 AND depature_date IS NULL ORDER BY no DESC, name";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("rooms", $data);
    }
    function checkoute() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT * FROM {$this->prefix}reservation WHERE cancel_by=0 AND depature_date IS NULL AND id_reservation='{$id}'";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }
    function savecheckout() {
        $data = $_REQUEST['t'];
        $id=$_REQUEST['id'];
        $sql = "SELECT roomnumber FROM {$this->prefix}reservation WHERE cancel_by=0 AND depature_date IS NULL AND id_reservation='{$id}'";
        $roomnumbers = $this->m->fetch_assoc($sql);
        $room = explode(',', $roomnumbers['roomnumber']);
        foreach ($room as $rname) {
            $sql = "UPDATE {$this->prefix}rooms SET status=1 WHERE name='$rname'";
            $this->m->query($sql);
        }
        $data['id_modify'] = $_SESSION['id_user'];
        $data['modify_date'] = date("Y-m-d");
        $sql = "SELECT max(billno) AS billno FROM {$this->prefix}reservation";
        $bill = $this->m->fetch_assoc($sql);
        $data['billno'] = $bill['billno'] + 1;

        $sql = $this->create_update("{$this->prefix}reservation", $data, "id_reservation='{$id}'");
        $this->m->query($sql);
        $_SESSION['msg'] = "Room Checkout Updated Successfully.";
        $this->redirect("index.php?module=reservation&func=checkout");
    }
    function cancel() {
        if (isset($_REQUEST['id'])) {
            if (!isset($_REQUEST['save'])) {
                return;
            }
            $id = $_REQUEST['id'];
            $sql = "SELECT roomnumber FROM {$this->prefix}reservation WHERE cancel_by=0 AND depature_date IS NULL AND id_reservation='{$id}'";
            $roomnumbers = $this->m->fetch_assoc($sql);
            $room = explode(',', $roomnumbers['roomnumber']);
            foreach ($room as $rname) {
                $sql = "UPDATE {$this->prefix}rooms SET status=0 WHERE name='$rname'";
                $this->m->query($sql);
            }    
            $data['cancel_by'] = $_SESSION['id_user'];
            $data['cancel_date'] = date("Y-m-d");
            $data['cancel_reason'] = $_REQUEST['reason'];
            $sql = $this->create_update("{$this->prefix}reservation", $data, "id_reservation='$id'");
            //$this->pr($sql);exit;
            $this->m->query($sql);
            $_SESSION['msg'] = "Booking Cancellation Successfully.";
        } else {
            $_SESSION['msg'] = "Booking not found for Cancellation.";
        }
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function cancelmr() {
        if (isset($_REQUEST['id'])) {
            if (!isset($_REQUEST['save'])) {
                return;
            } else {
                $id = $_REQUEST['id'];
                $data['cancel_reason'] = $_REQUEST['reason'];
                $data['cancel_by'] = $_SESSION['id_user'];
                $data['cancel_date'] = date("Y-m-d");
                $sql = $this->create_update("{$this->prefix}mr", $data, "id='$id'");
                $this->m->query($sql);
                $_SESSION['msg'] = "MR Cancellation Successfully.";
            }
        } else {
            $_SESSION['msg'] = "MR not found for Cancellation.";
        }
        $this->redirect("index.php?module=reservation&func=mrlist");
    }
    function printroom() {
        $id = $_REQUEST['id'];
        $noother = isset($_REQUEST['noother']) ?  $_REQUEST['noother'] : 0;
        if ($noother!=1) {
            $sql = "SELECT id_reservation, SUM(goodsvalue) AS ogoodsvalue, MAX(gstper) AS ogstpep, SUM(gstamount) AS ogstamount, SUM(amount) AS oamount
                    FROM {$this->prefix}other WHERE id_reservation='{$id}'";
            $data = $this->m->fetch_assoc($sql);
            $this->sm->assign("other", $data);
            $sql = "SELECT id_reservation, SUM(goodsvalue) AS fgoodsvalue, MAX(gstper) AS fgstpep, SUM(gstamount) AS fgstamount, SUM(amount) AS famount
                    FROM {$this->prefix}food WHERE id_reservation='{$id}'";
            $data = $this->m->fetch_assoc($sql);
            $this->sm->assign("food", $data);
        }
        $sql = "SELECT id_reservation, GROUP_CONCAT(no) AS nos, SUM(amount) AS total  FROM {$this->prefix}mr WHERE id_reservation='{$id}' AND cancel_date IS NULL ";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("mr", $data);

        $sql = "SELECT r.*, u.name AS username FROM {$this->prefix}reservation r LEFT JOIN user u ON r.id_create=u.id_user  WHERE r.id_reservation='{$id}'";
        $data = $this->m->fetch_assoc($sql);
        $json = json_decode($data['json']);
        $t = array();
        foreach ($json as $value) {
            $gst = intval($value->gst_per);
            @$t[$gst]['gst_per'] += $value->gst_per;
            @$t[$gst]['withtax'] += ($value->price - $value->discount) * $data['daysstay'];
	    $pr = round((($value->price - $value->discount)*$data['daysstay']));
	    $p = round((($value->price - $value->discount)*$data['daysstay']) * $gst / 100,2);

            //@$t[$gst]['gstamt'] += $value->gstamt;
            @$t[$gst]['gstamt'] += $p;
            //@$t[$gst]['total'] += $value->total;
            @$t[$gst]['total'] += $pr + $p;
        }
        $data['json'] = $t;
//$this->pr($data);exit;
        $data['w'] = $this->convert_number($data['total']);
        $this->sm->assign("data", $data);
    }
    function savechangedroom() {
        $id = $_REQUEST['id'];

        $sql = "SELECT roomnumber FROM {$this->prefix}reservation WHERE cancel_by=0 AND depature_date IS NULL AND id_reservation='{$id}'";
        $roomnumbers = $this->m->fetch_assoc($sql);
        $room = explode(',', $roomnumbers['roomnumber']);
        foreach ($room as $rname) {
            $sql = "UPDATE {$this->prefix}rooms SET status=0 WHERE name='$rname'";
            $this->m->query($sql);
        }

        $data['roomnumber'] = trim($_REQUEST['newid'], ",");
        $room = explode(',', $data['roomnumber']);
        foreach ($room as $rname) {
            $sql = "UPDATE {$this->prefix}rooms SET status=3 WHERE name='$rname'";
            $this->m->query($sql);
        }
        $data['id_modify'] = $_SESSION['id_user'];
        $data['modify_date'] = date("Y-m-d");
        $sql = $this->create_update("{$this->prefix}reservation", $data, "id_reservation='{$id}'");
        $this->m->query($sql);
        $_SESSION['msg'] = "Change Room Updated Successfully.";
        $this->redirect("index.php?module=reservation&func=listing");
    }
    function updatejson() {
        $sql = "SELECT * FROM {$this->prefix}reservation WHERE json='' OR json IS NULL ORDER BY id_reservation";
        $this->pr($sql);
        $reservation = $this->m->getall($this->m->query($sql));
        $this->pr($reservation);

        foreach ($reservation as $v) {
            $id = $v['id_reservation'];
            $days = $v['daysstay'] ? $v['daysstay'] : 1;
            $t[0]['name'] = $v['roomnumber'];
            $t[0]['tax_per'] = $t[0]['gst_per'] = $v['withtax'] ? 12.00 : 0;
            $t[0]['price'] = round($v['withtax']/$days,2);
            $t[0]['discount'] = 0;
            $t[0]['gstamt'] = $v['gstamt'];;
            $t[0]['total'] = $v['total'];
            $json = json_encode($t);
            $sql = "UPDATE {$this->prefix}reservation SET json='{$json}' WHERE id_reservation='{$id}'";
            $this->pr($sql);
            $this->m->query($sql);
        }
        exit;
    }
    function showguest() {
        ob_clean();
        $room = $_REQUEST['room'];
        $date = $_REQUEST['date'] ? $_REQUEST['date'] : date("Y-m-d");
        $sql = "SELECT no, roomnumber, person, daysstay, name, mobile, address, discount, gstamt, total FROM {$this->prefix}reservation 
                WHERE FIND_IN_SET({$room}, roomnumber) AND !cancel_by AND depature_date IS NULL";
        $data = $this->m->fetch_assoc($sql);
        echo json_encode($data);
        exit;
    }
    function getguestdetails() {
        $mobile = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : "";
        $sql = "SELECT * FROM {$this->prefix}reservation WHERE mobile='$mobile'";
        $data = $this->m->fetch_assoc($sql);
        ob_clean();
        echo json_encode($data);
        exit;
    }
}
?>
