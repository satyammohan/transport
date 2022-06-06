<?php
class so extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function insert() {
        $data = $_REQUEST['so'];
        $data['status'] = 0;
        $res = $this->m->query($this->create_insert("{$this->prefix}so", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=so&func=listing");
    }
    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = $this->create_select("{$this->prefix}so", "id_so='$id'");
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
      	$myid = $data['type'];
        $wcond = " 1 ";
        if($myid) {
            $wcond = " type < $myid ";
        }
        $res2 = $this->m->query("SELECT * FROM {$this->prefix}so WHERE status=0 AND (type=4 OR type=3 OR type=2 OR type=1) AND $wcond ORDER BY name");
        $this->sm->assign("so", $this->m->getall($res2, 2, "name", "id_so"));
        $this->sm->assign("page", "so/add.tpl.html");
    }
    function mark() {
        $res2 = $this->m->query("SELECT * FROM {$this->prefix}so WHERE status=0 AND type=5  ORDER BY name");
        $this->sm->assign("so", $this->m->getall($res2, 2, "name", "id_so"));

        $sql = "SELECT id_so, id_head, name, address1, address2, thisyear_target, lastyear_target FROM {$this->prefix}head WHERE debtor=1 ORDER BY id_so DESC, name";
        $list = $this->m->getall($this->m->query($sql));
        $this->sm->assign("list", $list);
    }
    function update_so() {
        ob_clean();
        $id = $_REQUEST['id_head'];
        $id_so = $_REQUEST['id_so'];
        $t = $_REQUEST['this'] ? $_REQUEST['this'] : 0;
        $l = $_REQUEST['last'] ? $_REQUEST['last'] : 0;
        $sql = "UPDATE {$this->prefix}head SET id_so='$id_so', thisyear_target='$t', lastyear_target='$l' WHERE id_head='$id'";
        $this->m->query($sql);
        echo $sql;
        exit;
    }
    function update() {
        $data = $_REQUEST['so'];
        $res = $this->m->query($this->create_update("{$this->prefix}so", $data, "id_so='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=so&func=listing");
    }
    function delete() {
        $id = $_REQUEST['id'];
        $res = $this->m->getall($this->m->query("SELECT COUNT(*) AS cnt FROM {$this->prefix}so WHERE id_parent='$id'"));
        $res1 = $this->m->getall($this->m->query("SELECT COUNT(*) AS cnt FROM {$this->prefix}head WHERE id_so='$id'"));
        if ($res[0]['cnt']==0 && $res1[0]['cnt']==0) {
            $this->m->query($this->create_delete("{$this->prefix}so", "id_so='{$id}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        } else {
            $_SESSION['msg'] = "Children exists can't Deleted";
        }
        $this->redirect("index.php?module=so&func=listing");
    }
    function listing() {
        $res2 = $this->m->query("SELECT * FROM {$this->prefix}so WHERE status=0 AND (type=4 OR type=3 OR type=2 OR type=1) ORDER BY name");
        $this->sm->assign("det", $this->m->getall($res2, 2, "name", "id_so"));
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " WHERE status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "";
        }
        $sql = "SELECT * FROM {$this->prefix}so $wcond ORDER BY type, name";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("so", $profile);
    }
    function user() {
        $this->addfield('username', $this->prefix . 'so', 'ADD `username` varchar(20)');
        $this->addfield('password', $this->prefix . 'so', 'ADD `password` varchar(20)');
        $this->addfield('email', $this->prefix . 'so', 'ADD `email` varchar(40)');
        $sql = "SELECT * FROM {$this->prefix}so ORDER BY name";
        $so = $this->m->sql_getall($sql, 1, "", "id_so");
        $this->sm->assign("so", $so);
    }
    function setfieldvalue() {
        $this->get_permission("so", "UPDATE");
        $data[$_REQUEST['field']] = $_REQUEST['fvalue'];
        $this->m->query($this->create_update("{$this->prefix}so", $data, "id_so='{$_REQUEST['id']}'"));
        echo 1;
        exit;
    }
    function sobill() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        switch ($_REQUEST['option']) {
        case 1:
            $fld = " day(s.date) ";
            $ocond = " day(s.date) ";
            break;
        case 2:
            $fld = " week(s.date)+1 ";
            $ocond = " s.date ";
            break;
        case 3:
            $fld = " month(s.date) ";
            $ocond = " s.date ";
            break;
        }
        $da = array();
        $sql = "SELECT DISTINCT $fld AS day FROM {$this->prefix}sale s WHERE s.date BETWEEN '$sdate' AND '$edate' GROUP BY day ORDER BY $ocond ";
        $rs = $this->m->query($sql);
        while ($row = $this->m->sql_fetch_assoc()) {
            $da[] = $row['day'];
        }
        
        $sql = "SELECT id_so, name, id_parent, type FROM {$this->prefix}so ORDER BY id_so";
        $rs = $this->m->query($sql);
        while ($row = $this->m->sql_fetch_assoc()) {
            $s[$row['id_so']] = ["type"=>$row['type'], "id_parent"=>$row['id_parent'], "name"=>$row['name']];
        }
        $sql = "SELECT h.name, h.thisyear_target, h.lastyear_target, so.id_parent, so.name AS soname, s.id_head, $fld AS day, YEAR(date) AS year, SUM(s.total) AS total 
                FROM {$this->prefix}sale s, {$this->prefix}head h LEFT JOIN {$this->prefix}so so ON h.id_so=so.id_so
                WHERE s.date BETWEEN '$sdate' AND '$edate' AND s.id_head=h.id_head
                GROUP BY s.id_head, day ORDER BY so.id_parent, so.name, h.name, year, day";
        $rs = $this->m->query($sql);
        $list = array();
        while ($row = $this->m->sql_fetch_assoc()) {
            $h = $row['id_parent'];
            if ($h) {
                $l['aname'] = $s[$h]['name'];
                $l['rname'] = $s[$s[$h]['id_parent']]['name'];
            } else {
                $l['aname'] = $l['rname'] = "";
            }
            $l['name'] = $row['name'];
            $l['soname'] = $row['soname'];
            switch ($_REQUEST['group']) {
                case 1:
                    $id = $l['rname']."_".$l['aname']."-".$l['soname']."-".$l['name']."-".$row['id_head'];
                    break;
                case 2:
                    $id = $l['rname']."_".$l['aname']."-".$l['soname'];
                    break;
                case 3:
                    $id = $l['rname']."_".$l['aname'];
                    break;
            }
            $list[$id]['det'] = $l;
            $list[$id][$row['day']] = @$list[$id][$row['day']] + $row['total'];
        }
        $this->sm->assign("maxdays", count($da));
        $this->sm->assign("header", $da);
        ksort($list);
        $this->sm->assign("data", $list);
    }
    function socollection() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['group'] = isset($_REQUEST['group']) ? $_REQUEST['group'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $sql = "SELECT h.id_head, h.name FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.name='cash' AND p.content=h.code";
        $cash = $this->m->sql_getall($sql, 2, "name", "id_head");
        $sql = "SELECT h.* FROM {$this->prefix}head h, {$this->prefix}group g WHERE h.id_group=g.id_group AND g.name='BANK ACCOUNTS' ORDER BY 1";
        $data = $this->m->sql_getall($sql, 2, "name", "id_head");
        $acs = $cash + $data;
        $ac = implode(",", array_keys($acs));
        $filtercond = " s.id_head_debit IN ($ac) ";

        $sql = "SELECT id_so, name, id_parent, type FROM {$this->prefix}so ORDER BY id_so";
        $rs = $this->m->query($sql);
        while ($row = $this->m->sql_fetch_assoc()) {
            $s[$row['id_so']] = ["type"=>$row['type'], "id_parent"=>$row['id_parent'], "name"=>$row['name']];
        }
        switch ($_REQUEST['option']) {
        case 1:
            $fld = " day(s.date) ";
            $ocond = " day(s.date) ";
            break;
        case 2:
            $fld = " week(s.date)+1 ";
            $ocond = " s.date ";
            break;
        case 3:
            $fld = " month(s.date) ";
            $ocond = " s.date ";
            break;
        }
        $da = array();
        $sql = "SELECT DISTINCT $fld AS day FROM {$this->prefix}voucher s WHERE s.date BETWEEN '$sdate' AND '$edate' GROUP BY 1 ORDER BY $ocond ";
        $rs = $this->m->query($sql);
        while ($row = $this->m->sql_fetch_assoc()) {
            $da[] = $row['day'];
        }
        $sql = "SELECT h.name, h.thisyear_target, h.lastyear_target, so.id_parent, so.name AS soname, s.id_head_credit AS id_head, $fld AS day, YEAR(date) AS year, SUM(s.total) AS total 
                FROM {$this->prefix}voucher s, {$this->prefix}head h LEFT JOIN {$this->prefix}so so ON h.id_so=so.id_so
                WHERE s.type='V' AND h.debtor AND s.date BETWEEN '$sdate' AND '$edate' AND s.id_head_credit=h.id_head AND $filtercond
                GROUP BY s.id_head_credit, day ORDER BY so.id_parent, so.name, h.name, year, day";
        $rs = $this->m->query($sql);
        $list = array();
        while ($row = $this->m->sql_fetch_assoc()) {
            $h = $row['id_parent'];
            if ($h) {
                $l['aname'] = $s[$h]['name'];
                $l['rname'] = $s[$s[$h]['id_parent']]['name'];
            } else {
                $l['aname'] = $l['rname'] = "";
            }
            $l['soname'] = $row['soname'];
            $l['name'] = $row['name'];
            switch ($_REQUEST['group']) {
                case 1:
                    $id = $l['rname']."_".$l['aname']."-".$l['soname']."-".$l['name']."-".$row['id_head'];
                    break;
                case 2:
                    $id = $l['rname']."_".$l['aname']."-".$l['soname'];
                    break;
                case 3:
                    $id = $l['rname']."_".$l['aname'];
                    break;
            }
            $list[$id]['det'] = $l;
            $list[$id][$row['day']] = @$list[$id][$row['day']] + $row['total'];
        }
        $this->sm->assign("maxdays", count($da));
        $this->sm->assign("header", $da);
        ksort($list);
        $this->sm->assign("data", $list);
    }
    function representInvoiceDetail() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $sql = "SELECT id_so, name, id_parent, type FROM {$this->prefix}so ORDER BY id_so";
        $rs = $this->m->query($sql);
        while ($row = $this->m->sql_fetch_assoc()) {
            $s[$row['id_so']] = ["type"=>$row['type'], "id_parent"=>$row['id_parent'], "name"=>$row['name']];
        }
        $sql = "SELECT sd.id_saledetail, r.name AS rname, sd.invno, sd.date, h.id_so, so.id_parent, so.name AS soname, h.name AS pname, h.address1, p.name, sd.*, s.tcsper 
            FROM {$this->prefix}product p, {$this->prefix}represent r, {$this->prefix}sale s, {$this->prefix}saledetail sd 
                LEFT JOIN {$this->prefix}head h ON sd.id_head=h.id_head LEFT JOIN {$this->prefix}so so ON h.id_so=so.id_so
            WHERE sd.id_sale=s.id_sale AND sd.id_product=p.id_product AND sd.id_represent=r.id_represent AND sd.date >= '$sdate' AND sd.date <= '$edate' 
            ORDER BY sd.date, sd.invno, p.name";
        $rs = $this->m->query($sql);
        $list = array();
        while ($row = $this->m->sql_fetch_assoc()) {
            $l = $row;
            $h = $row['id_parent'];
            if ($h) {
                $l['aname'] = $s[$h]['name'];
                $l['rname'] = $s[$s[$h]['id_parent']]['name'];
            } else {
                $l['aname'] = $l['rname'] = "";
            }
            $l['name'] = $row['name'];
            $l['soname'] = $row['soname'];
            $id = $l['rname']."_".$l['aname']."-".$l['soname']."-".$l['name']."-".$row['id_head']."-".$row['id_saledetail'];
            $list[$id]['det'] = $l;
        }
        $this->sm->assign("data", $list);
    }
}
?>
