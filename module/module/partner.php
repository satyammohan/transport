<?php
class partner extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function _default() {
        if (isset($_SESSION['id_user'])) {
            $this->home();
        } else {
            $this->sm->assign("page", "login.tpl.html");
        }
    }
    function checklogin() {
        $f = @$_REQUEST['func'];
        if (!isset($_SESSION['id_user']) && !($f=='login' || $f=="")) {
            $this->redirect("index.php");
        }
    }
    function logout() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $id = $_SESSION['id_user'];
        $this->savelogininfo($_SESSION['type'], $ip, $id, "Logout");
        session_destroy();
        foreach ($_SESSION as $k => $v) {
            $_SESSION[$k] = '';
            unset($_SESSION[$k]);
        }
        session_start();
        $_SESSION['msg'] = "Successfully logout.";
        $this->redirect("index.php");
    }
    function login() {
        $this->data = $_REQUEST['user'];
        $this->selectcompany();
        $un = $this->data['uname'];
        $up = $this->data['pass'];
        $sql = "SELECT id_head AS id_user, partyuser AS user, name, email AS pemail, 0 AS is_admin, address1 AS paddress, address2 AS paddress1, phone AS pphone
                FROM {$this->prefix}head WHERE partyuser='$un' AND partyuser!='' AND partypass='$up'";
        $this->data1 = $this->m->fetch_assoc($sql);
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($this->data1) {
            $_SESSION['type'] = "party";
            $this->set_session($this->data1);
            $this->set_permission();
            $this->config();
            $this->savelogininfo($_SESSION['type'], $ip, $this->data1['id_user'], "Login");
            $_SESSION['msg'] = "Successfully Logged as Partner.";
            $this->redirect("index.php?module=partner&func=home");
        } else {
            $sql = "SELECT id_so AS id_user, type, name AS user, name, 0 AS is_admin, address AS paddress, phone AS pphone, email as pemail
                    FROM {$this->prefix}so WHERE username='$un' AND username!='' AND password='$up'";
            $this->data1 = $this->m->fetch_assoc($sql);
            if ($this->data1) {
                $this->set_session($this->data1);
                $ty = $this->data1['type'];
                $_SESSION['team_type'] = $this->data1['type'];
                $_SESSION['type'] = $this->ini["salesteam"][$ty];
                $this->set_permission();
                $this->config();
                $this->savelogininfo($_SESSION['type'], $ip, $this->data1['id_user'], "Login");
                $_SESSION['msg'] = "Successfully Logged as {$this->ini["salesteam"][$ty]}.";
                $this->redirect("index.php?module=partner&func=home");
            } else {
                $_SESSION['msg'] = "Invalid Username or Password.";
                $this->redirect("index.php");        
            }
        }
    }
    function savelogininfo($type, $ip, $id_user, $logintype) {
        $sql = "INSERT INTO login_details (type, ip, id_user, logintype, date) values ('$type', '$ip', '$id_user', '$logintype', NOW())";
        $this->m->query($sql);
    }
    function set_session($arr) {
        foreach ($arr as $k => $v) {
            $_SESSION[$k] = $v;
        }
    }
    function selectcompany() {
        $sql = "SELECT value FROM configuration WHERE name='PARTNERLOGIN' LIMIT 1";
        $con = $this->m->fetch_assoc($sql);
        $pre = @$con['value'];
        if ($pre) {
            $sql = "SELECT * FROM info WHERE prefix='{$pre}' LIMIT 1";
            $info = $this->m->fetch_assoc($sql);
            foreach ($info as $k =>$v) {
                $_SESSION[$k] = $v;
            }
            $this->table_prefix();
        }
    }
    function home() {
        $this->sm->assign("page", "home.tpl.html");
    }
    function config() {
        $sql = "SELECT * FROM `configuration`";
        $data = array();
        $rs = $this->m->query($sql);
        while ($row = $this->m->movenexta($rs)) {
            $name = strtoupper($row['name']);
            $value = strtoupper($row['value']);
            $data[$name] = $value;
        }
        $_SESSION['config'] = $data;
    }
    function ledger() {
        $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : $_SESSION['start_date'];
        $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-t");
        $_REQUEST['id'] = isset($_REQUEST['id']) ? $_REQUEST['id'] : $_SESSION['id_user'];
        $sdate = $_REQUEST['start_date'];
        $edate = $_REQUEST['end_date'];
        $_REQUEST['option'] = 1;
        include_once("accounts.php");
        $acc = new accounts();
        $res = $acc->fetchdata($_REQUEST['id'], $sdate, $edate);
        $sql = "SELECT * FROM {$this->prefix}head WHERE id_head='{$_REQUEST['id']}'";
        $this->sm->assign("party", $this->m->getall($this->m->query($sql)));
        $this->sm->assign("page", "ledger.tpl.html");
    }
    function access($type) {
        if ($_SESSION['type']!=$type) {
            $_SESSION['msg'] = "You are not authorized to access this Menu";
            $this->redirect("index.php");
        }
    }
    function updateall() {
        $sql = "UPDATE {$this->prefix}so SET id_heads=NULL";
        $this->m->query($sql);
        $sql = "UPDATE {$this->prefix}so s, (SELECT id_so, GROUP_CONCAT(id_head) AS id_heads FROM {$this->prefix}head WHERE id_so GROUP BY id_so) h
                SET s.id_heads=h.id_heads WHERE s.id_so=h.id_so";
        $this->m->query($sql);
        for ($i=5; $i>1; $i--) {
            $sql = "UPDATE {$this->prefix}so s, (SELECT id_parent, GROUP_CONCAT(id_heads) AS id_heads FROM {$this->prefix}so WHERE id_heads IS NOT NULL AND type=$i GROUP BY id_parent) p SET s.id_heads=p.id_heads  WHERE s.id_so=p.id_parent";
            $this->m->query($sql);
        }
        exit;
    }
    function get_represent($id) {
        $sql = "SELECT a.id_represent FROM {$this->prefix}head h, {$this->prefix}area a WHERE h.id_area=a.id_area AND h.id_head='{$id}'";
        $pids = $this->m->sql_getall($sql);
        return $pids[0]['id_represent'];
    }
    function getpartyids() {
        //$this->updateall();
        $id = $_SESSION['id_user'];
        $sql = "SELECT id_heads FROM {$this->prefix}so WHERE id_so='$id'";
        $pids = $this->m->sql_getall($sql);
        return $pids[0]['id_heads'];
    }
    function myparty() {
        $ids = $this->getpartyids();
        $wcond = isset($_REQUEST['id_so']) ? " AND h.id_so='{$_REQUEST['id_so']}' " : "";

        $sql = "SELECT h.*, s.name AS soname FROM {$this->prefix}head h, {$this->prefix}so s
                WHERE h.id_head IN ($ids) AND h.id_so=s.id_so $wcond ORDER BY h.name";
        $parties = $this->m->sql_getall($sql);
        $this->sm->assign("parties", $parties);
        $this->sm->assign("page", "myparty.tpl.html");
    }
    function approveOrder() {
        $ids = $this->getpartyids();
        $wcond = " o.id_head IN ($ids) " . (isset($_REQUEST['approve']) ? " AND o.approve= '{$_REQUEST['approve']}' " : "");
        $sql = "SELECT concat(o.id_head,o.date,o.time) AS time, SUM(o.amount) AS amount FROM {$this->prefix}partner o WHERE $wcond GROUP BY 1";
        $this->sm->assign("value", $this->m->sql_getall($sql, 2, "amount", "time"));
        $sql = "SELECT o.*, p.name, h.name AS pname, h.address1, h.address2, h.address3, c.name AS cname
                FROM {$this->prefix}partner o, {$this->prefix}product p, {$this->prefix}company c, {$this->prefix}head h, {$this->prefix}area a
                WHERE (o.approve_qty<>0 OR o.approve_free<>0) AND o.id_head=h.id_head AND o.id_product=p.id_product AND p.id_company=c.id_company AND h.id_area=a.id_area AND $wcond
                ORDER BY h.name, o.date, o.time, c.name, p.name";
        $ord = $this->m->sql_getall($sql);
        $this->sm->assign("order", $ord);
        $this->sm->assign("page", "approveOrder.tpl.html");
    }
    function approvethisOrder() {
        $id_head = $_REQUEST['id_head'];
        $type = $_REQUEST['type'];
        $time = $_REQUEST['time'];
        $date = $_REQUEST['date'];
        $dm = date("Y-m-d h:i:s");
        $dateenter =  date("Y-m-d");
        $id = $this->get_represent($id_head);
        ob_clean();
        if (!$id) {
            echo "Error";
            exit;
        }
        $sql = "UPDATE {$this->prefix}partner SET approve=1, approve_time='{$dm}', id_represent='{$id}' 
                WHERE id_head='{$id_head}' AND date='{$date}' AND time='{$time}'";
        $this->m->query($sql);
        if ($type==1) {
            $sql = "SELECT t.id_head, '{$dateenter}' AS date, {$id} AS id_represent, t.id_product, t.approve_qty AS qty, t.approve_free AS free, p.id_company, 
                        p.cess, t.rate, p.id_taxmaster_sale AS id_taxmaster, x.tax_per, h.id_area
                    FROM {$this->prefix}partner t, {$this->prefix}product p, {$this->prefix}taxmaster x, {$this->prefix}head h
                    WHERE t.id_product=p.id_product AND p.id_taxmaster_sale=x.id_taxmaster AND t.id_head=h.id_head AND t.id_head='{$id_head}' AND date='{$date}' AND time='{$time}'";
            $ord = $this->m->sql_getall($sql);
            foreach ($ord as $k => $v) {
                $ord[$k]['amount'] = $v['rate']*$v['qty'];
                $ord[$k]['goods_amount'] = $ord[$k]['amount'];
                $ord[$k]['tax_amount'] = round($ord[$k]['goods_amount'] * $v['tax_per'] / 100,2);
                $ord[$k]['cessamt'] = round($ord[$k]['goods_amount'] * $v['cess'] / 100,2);
                $ord[$k]['net_amount'] = $ord[$k]['goods_amount'] + $ord[$k]['tax_amount'] + $ord[$k]['cessamt'];
                @$comp[$v['id_company']]['date'] = $v['date'];
                @$comp[$v['id_company']]['id_company'] = $v['id_company'];
                @$comp[$v['id_company']]['id_head'] = $v['id_head'];
                @$comp[$v['id_company']]['id_area'] = $v['id_area'];
                @$comp[$v['id_company']]['id_represent'] = $v['id_represent'];
                @$comp[$v['id_company']]['totalamt'] = @$comp[$v['id_company']]['totalamt'] + $ord[$k]['goods_amount'];
                @$comp[$v['id_company']]['vat'] = @$comp[$v['id_company']]['vat'] + $ord[$k]['tax_amount'];
                @$comp[$v['id_company']]['totalcess'] = @$comp[$v['id_company']]['totalcess'] + $ord[$k]['cessamt'];
                @$comp[$v['id_company']]['total'] = @$comp[$v['id_company']]['total'] + $ord[$k]['net_amount'];
                @$compitem[$v['id_company']][] = $ord[$k];
            }
            foreach ($comp as $k => $v) {
                $sorder = $v;
                $maxno = $this->m->fetch_assoc("SELECT MAX(CAST(invno as decimal(11))) AS invno FROM {$this->prefix}salesorder");
                $sorder['invno'] =  $maxno['invno'] + 1;
                $sql = $this->create_insert("{$this->prefix}salesorder", $sorder);
                $rs = $this->m->query($sql);
                $id = $this->m->getinsertID($rs);
                foreach ($compitem[$k] as $ck => $cv) {
                    $data = $cv;
                    $data["id_salesorder"] = $id;
                    $data['order_approve_time'] = $dm;
                    $sql1 = $this->create_insert("{$this->prefix}salesorderdetail", $data);
                    $this->m->query($sql1);
                }

            }
        }
        echo "Ok";
        exit;
    }
    function editthisOrder() {
        $id_head = $_REQUEST['id_head'];
        $this->getLedgerBalance($id_head);
        $this->getLedgerDetails($id_head);
        $time = $_REQUEST['time'];
        $date = $_REQUEST['date'];
        $sql = "SELECT p.*, o.qty, '{$date}' as date, '{$id_head}' as id_head, '{$time}' AS time, o.approve_qty, o.qty, o.approve_free, o.free, o.id_partner FROM 
                (SELECT p.name, p.id_product, p.seller_price AS mrp, c.name AS cname FROM {$this->prefix}product p, {$this->prefix}company c WHERE p.showtoparty='YES' AND p.id_company=c.id_company) p
            LEFT JOIN (SELECT * FROM {$this->prefix}partner WHERE id_head='{$id_head}' AND date='{$date}' AND time='{$time}') o ON p.id_product=o.id_product ORDER BY p.cname, p.name";
        $items = $this->m->sql_getall($sql, 1, "", "cname", "id_product");
        $this->sm->assign("items", $items);
        $this->sm->assign("page", "editOrder.tpl.html");
    }
    function saveEditOrder() {
        $items = $_REQUEST['item'];
        $approve_free = $_REQUEST['approve_free'];
        $qty = $_REQUEST['qty'];
        $free = $_REQUEST['free'];
        $price = $_REQUEST['price'];
        $partner = $_REQUEST['partner'];
        $id_head = $_REQUEST['id_head'];
        $date = $_REQUEST['date'];
        $time = $_REQUEST['time'];
        foreach ($items as $id_product => $aqty) {
            $afree = $approve_free[$id_product];
            $rate = $price[$id_product];
            $amt = $aqty * $rate;
            if ($aqty || $afree) {
                if ( $partner[$id_product] ) {
                    $sql = "UPDATE {$this->prefix}partner SET approve_qty='{$aqty}', approve_free='{$afree}', amount='{$amt}' WHERE id_product='{$id_product}' AND id_head='{$id_head}' AND time='{$time}' ";
                } else {
                    $sql = "INSERT INTO {$this->prefix}partner (date, time, id_head, id_product, rate, amount, qty, approve_qty, free, approve_free) VALUES 
                        ('{$date}', '{$time}', '{$id_head}', '{$id_product}', '{$rate}', '{$amt}', 0, '{$aqty}', 0, '{$afree}')";
                }
            } else {
                if ( $partner[$id_product] && $qty[$id_product]==0 && $free[$id_product]==0) {
                    $sql = "DELETE FROM {$this->prefix}partner WHERE id_product='{$id_product}' AND id_head='{$id_head}' AND time='{$time}' ";
                } else {
                    $sql = "UPDATE {$this->prefix}partner SET approve_qty=0, approve_free=0, amount=0 WHERE id_product='{$id_product}' AND id_head='{$id_head}' AND time='{$time}' ";
                }
            }
            $this->m->query($sql);
        }
        $_SESSION['msg'] = "Edit Order Save Successfully.";
        $this->redirect("index.php?module=partner&func=approveOrder&approve=0");
    }
    function getLedgerDetails($lid) {
        $sql = "SELECT * FROM {$this->prefix}head WHERE id_head='$lid'";
        $this->sm->assign("party", $this->m->fetch_assoc($sql));
    }
    function getLedgerBalance($lid) {
        $sql = "SELECT SUM(debit) AS debit, SUM(credit) AS credit FROM {$this->prefix}tb WHERE id_head='$lid'";
        $res = $this->m->sql_getall($sql);
        $balance = -$res[0]['debit'] + $res[0]['credit'];
        $this->sm->assign("balance", $balance);
    }
    function unbilledOrder() {
        $ids = $this->getpartyids();
        $sdate = $_SESSION['start_date'];
        $edate = $_SESSION['end_date'];
        $wcond = "";
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
            $wcond = " AND s.is_approve= '{$type}' ";
        }
        $sql = "SELECT s.*, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address1, c.name AS cname, sd.rate, sd.qty, sd.free, sd.rate, sd.net_amount, p.name
            FROM `{$this->prefix}salesorder` s, `{$this->prefix}head` h, `{$this->prefix}company` c, `{$this->prefix}salesorderdetail` sd, `{$this->prefix}product` p
            WHERE s.id_company=c.id_company AND s.id_head=h.id_head AND s.date>='$sdate' AND s.date<='$edate' AND h.id_head IN ($ids) AND s.is_billed=0 {$wcond}
                AND s.id_salesorder=sd.id_salesorder AND sd.id_product=p.id_product
            ORDER BY party_name, `date`, s.invno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "unbilledOrder.tpl.html");
    }
    function orderHistory() {
        $this->access("party");
        include_once("salesorder.php");
        $so = new salesorder();
        $_REQUEST['id'] = $_SESSION['id_user'];
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $res = $so->getmyorder($_SESSION['id_user'], $sdate, $edate);
        $this->sm->assign("page", "orderHistory.tpl.html");
    }
    function myOrders() {
        $this->access("party");
        $id = $_SESSION['id_user'];
        $wcond = "";
        if (isset($_REQUEST['approve'])) {
            $approve = $_REQUEST['approve'];
            $wcond = " AND o.approve= '{$approve}' ";
        }
        $sql = "SELECT concat(o.date,o.time) AS time, SUM(o.amount) AS amount FROM {$this->prefix}partner o WHERE o.id_head='{$id}' $wcond";
        $value = $this->m->sql_getall($sql, 2, "amount", "time");
        $this->sm->assign("value", $value);
        $sql = "SELECT o.*, p.name FROM {$this->prefix}partner o, {$this->prefix}product p
                WHERE o.id_head='{$id}' AND o.id_product=p.id_product  $wcond ORDER BY date DESC, time";
        $ord = $this->m->sql_getall($sql);
        $this->sm->assign("order", $ord);
        $this->sm->assign("page", "myOrders.tpl.html");
    }
    function createOrder() {
        $this->access("party");
        $id = $_SESSION['id_user'];
        $this->getLedgerBalance($id);
        $sql = "SELECT dealer FROM {$this->prefix}head WHERE id_head='$id'";
        $res = $this->m->sql_getall($sql);
        $dealer = $res[0]['dealer'];
        $this->sm->assign("dealer", $dealer);

        $sql = "SELECT p.*, c.name AS cname FROM {$this->prefix}product p, {$this->prefix}company c 
		    WHERE p.showtoparty='YES' AND p.id_company=c.id_company ORDER BY c.name, p.name";
        $items = $this->m->sql_getall($sql, 1, "", "cname", "id_product");
        $this->sm->assign("items", $items);
        $this->sm->assign("page", "createOrder.tpl.html");
    }
    function saveOrder() {
        $this->access("party");
        $items = $_REQUEST['item'];
        $free = $_REQUEST['free'];
        $price = $_REQUEST['price'];
        $id = $_SESSION['id_user'];
        $date =  date("Y-m-d");
        $time = date("h:i:s");
        foreach ($items as $k => $v) {
            if ($v) {
                $rate = $price[$k];
                $amt = $v * $rate;
                $fqty = $free[$k];
                $sql = "INSERT INTO {$this->prefix}partner (date, time, id_head, id_product, qty, rate, amount, approve_qty, free, approve_free) VALUES 
                        ('{$date}', '{$time}', '{$id}', '{$k}', '{$v}', '{$rate}', '{$amt}', '{$v}', '{$fqty}', '{$fqty}')";
                $this->m->query($sql);
            }
        }
        $_SESSION['msg'] = "Order Save Successfully.";
        $this->redirect("index.php");
    }
    function invoice() {
        $this->access("party");
        $id = $_SESSION['id_user'];
        $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : $_SESSION['start_date'];
        $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-t");
        $sdate = $_REQUEST['start_date'];
        $edate = $_REQUEST['end_date'];
        $sql = "SELECT s.*, sd.qty, sd.free, sd.rate, sd.tax_amount+sd.cessamt AS taxes, sd.net_amount, p.name
                FROM {$this->prefix}sale s,  {$this->prefix}saledetail sd, `{$this->prefix}product` p
                WHERE s.id_head={$id} AND s.id_sale=sd.id_sale AND (s.date BETWEEN '{$sdate}' AND '{$edate}') AND sd.id_product=p.id_product
                ORDER BY s.date, s.invno, p.name";
        $bills = $this->m->sql_getall($sql);
        $this->sm->assign("data", $bills);
        $this->sm->assign("page", "invoice.tpl.html");
    }
    function myStock() {
        $this->checkpartnerstock();
        $date = @$_REQUEST['date'] ? $_REQUEST['date'] : date('Y-m-d');
        $id = @$_REQUEST['id_head'] ? $_REQUEST['id_head'] : $_SESSION['id_user'];
        $sql = "SELECT * FROM {$this->prefix}head WHERE id_head='{$id}'";
        $this->sm->assign("head", $this->m->getall($this->m->query($sql)));

        $sql = "SELECT a.id_product, SUM(a.qty) AS qty FROM (SELECT id_product, SUM(qty+free) AS qty FROM {$this->prefix}saledetail WHERE id_head='{$id}' AND date<'$date' GROUP BY 1 UNION ALL
                SELECT id_product, SUM(-qty) AS qty FROM {$this->prefix}partner_stock WHERE id_head='{$id}' AND date<'$date' GROUP BY 1) a GROUP BY 1";
        $open = $this->m->getall($this->m->query($sql), 2, "qty", "id_product");

        $sql = "SELECT id_product, SUM(qty+free) AS qty FROM {$this->prefix}saledetail WHERE id_head='{$id}' AND date='$date' GROUP BY 1";
        $purc = $this->m->getall($this->m->query($sql), 2, "qty", "id_product");

        $sql = "SELECT id_product, SUM(qty) AS qty FROM {$this->prefix}partner_stock WHERE id_head='{$id}' AND date='$date' GROUP BY 1";
        $sale = $this->m->getall($this->m->query($sql), 2, "qty", "id_product");
        $sql = "SELECT p.*, c.name AS cname FROM {$this->prefix}product p, {$this->prefix}company c WHERE p.showtoparty='YES' AND p.id_company=c.id_company ORDER BY c.name, p.name";
        $items = $this->m->sql_getall($sql, 1, "", "cname", "id_product");
        foreach ($items as $ck => $cv) {
            foreach ($cv as $k => $v) {
                $items[$ck][$k]['o'] = @$open[$k] ? $open[$k] : 0;
                $items[$ck][$k]['s'] = @$sale[$k] ? $sale[$k] : 0;
                $items[$ck][$k]['p'] = @$purc[$k] ? $purc[$k] : 0;
                $items[$ck][$k]['c'] = $items[$ck][$k]['o'] + $items[$ck][$k]['p'] - $items[$ck][$k]['s'];
            }
        }
        $this->sm->assign("items", $items);
        $sql = "SELECT max(date) AS date FROM {$this->prefix}partner_stock WHERE id_head='{$id}199'";
        $maxdate = $this->m->getall($this->m->query($sql));
        $this->sm->assign("maxdate", $maxdate[0]['date']);
        if (isset($_REQUEST['date']) || empty($maxdate[0]['date'])) {
            $_REQUEST['date'] = $date;
        } else {
            $_REQUEST['date'] = date('Y-m-d', strtotime("+1 day", strtotime($maxdate[0]['date'])));
        }
        $this->sm->assign("page", "myStock.tpl.html");
    }
    function checkpartnerstock() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}partner_stock (id_partner_stock int(11) NOT NULL AUTO_INCREMENT, 
                date date, id_head int, id_product int, qty float(12,4), entrydate date, PRIMARY KEY (id_partner_stock))";
        $this->m->query($sql);
    }
    function saveTodaysSales() {
        $items = $_REQUEST['item'];
        $id = $_SESSION['id_user'];
        $dateenter =  date("Y-m-d");
        $date =  $_REQUEST['date'];
        $this->checkpartnerstock();
        foreach ($items as $k => $v) {
            if ($v) {
                $sql = "INSERT INTO {$this->prefix}partner_stock (date, id_head, id_product, qty, entrydate) VALUES 
                        ('{$date}', '{$id}', '{$k}', '{$v}', '{$dateenter}')";
                $this->m->query($sql);
            }
        }
        $_SESSION['msg'] = "Partner Stock updation Successful.";
        $this->redirect("index.php");
    }
    function report() {
        $this->sm->assign("page", "report.tpl.html");
    }
    function markorder() {
        $wcond = isset($_REQUEST['id_head']) ? " p.id_head='{$_REQUEST['id_head']}' AND " : "";
        $sql = "SELECT p.id_head, i.id_company, p.date, p.approve_time, group_concat(DISTINCT p.id_product ORDER BY p.id_product ASC) as id_product 
                    FROM `{$this->prefix}partner` p, `{$this->prefix}product` i, `{$this->prefix}company` c 
                WHERE $wcond p.approve_qty+p.approve_free>0 AND p.approve=1 AND p.id_product=i.id_product AND i.id_company=c.id_company AND c.status=0 AND c.name LIKE '%.'
                GROUP BY p.id_head, i.id_company, p.date ORDER BY p.id_head, p.approve_time, i.id_company";
        $orders = $this->m->sql_getall($sql);
        //pr($orders);
        $sql = "SELECT p.id_head, i.id_company, id_salesorder, order_approve_time, date, group_concat(p.id_product ORDER BY p.id_product ASC) as id_product 
                FROM `{$this->prefix}salesorderdetail` p, `{$this->prefix}product` i, `{$this->prefix}company` c 
                WHERE $wcond p.id_product=i.id_product AND i.id_company=c.id_company AND c.status=0 AND c.name LIKE '%.'
                GROUP BY p.id_head, i.id_company, id_salesorder ORDER BY p.id_head, date, i.id_company, order_approve_time";
        $aorders = $this->m->sql_getall($sql);
        //pr($aorders);
        foreach ($orders as $k => $v) {
            $key = $v['id_company']."_".$v['approve_time'];
            $final[$v['id_head']]['o'][$key]['product'] = $v['id_product'];
            $final[$v['id_head']]['o'][$key]['approve_time'] = $v['approve_time'];
        }
        foreach ($aorders as $k => $v) {
            $final[$v['id_head']]['a'][$v['id_salesorder']]['product'] = $v['id_product'];
            $final[$v['id_head']]['a'][$v['id_salesorder']]['date'] = $v['date'];
            if ($v['order_approve_time']) {
                $key = $v['id_company']."_".$v['order_approve_time'];
                if (@$_REQUEST['all']) {
                    $final[$v['id_head']]['a'][$v['id_salesorder']]['order_approve_time'] =  $v['order_approve_time'];
                    $final[$v['id_head']]['o'][$key]['order_approve_time'] = 1;
                } else {
                    unset($final[$v['id_head']]['a'][$v['id_salesorder']]);
                    unset($final[$v['id_head']]['o'][$key]);
                }
            }
        }
        //pr($final);exit;
        $this->sm->assign("orders", $final);
        $this->sm->assign("heads", $this->m->sql_getall("SELECT id_head, name FROM `{$this->prefix}head`", 2, "name", "id_head"));
        $this->sm->assign("page", "markorder.tpl.html");
    }
    function savetime() {
        $id_head = $_REQUEST['id_head'];
        $id_salesorder = $_REQUEST['id_salesorder'];
        $t = $_REQUEST['t'];
        if ($t) {
            $sql = "UPDATE `{$this->prefix}salesorderdetail` SET order_approve_time='$t' WHERE id_salesorder='$id_salesorder' AND id_head='$id_head' ";
        } else {
            $sql = "UPDATE `{$this->prefix}salesorderdetail` SET order_approve_time=NULL WHERE id_salesorder='$id_salesorder' AND id_head='$id_head' ";
        }
        pr($sql);
        $this->m->query($sql);
        echo 1;
        exit;
    }
    function historydet() {
	$id_head = isset($_REQUEST['id_head']) ? $_REQUEST['id_head'] : "";
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";

        $sql = "SELECT p.*, i.name FROM `{$this->prefix}partner` p, `{$this->prefix}product` i
		WHERE p.id_head='$id_head' AND p.id_product=i.id_product AND concat(p.date,' ',p.time)='$id' ORDER BY i.name";
        $order = $this->m->sql_getall($sql);
        $this->sm->assign("order", $order);
        $this->sm->assign("page", "historydet.tpl.html");
    }
    function historyd() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
        $wcond = " p.id_head IN ($id) ";
        $sql = "SELECT h.name, s.name AS sname, p.id_head, concat(p.date,' ',p.time) as add_time, approve_time, s.id_so,
                SUM(p.approve_qty+p.approve_free) AS qty, 
                group_concat(if(p.approve_qty+p.approve_free>0, p.id_product, '')  ORDER BY p.id_product ASC) AS id_product
                FROM `{$this->prefix}partner` p, `{$this->prefix}head` h, `{$this->prefix}so` s
                WHERE p.id_head=h.id_head AND h.id_so=s.id_so AND $wcond
                GROUP BY p.id_head,add_time,approve_time ORDER BY s.name,h.name,4,p.id_product";
        $orders = $this->m->sql_getall($sql);
        $sql = "SELECT p.date, p.order_approve_time, m.id_sale, group_concat(p.id_product ORDER BY p.id_product ASC) as id_product, SUM(p.qty+p.free) AS qty 
                FROM `{$this->prefix}salesorderdetail` p, `{$this->prefix}salesorder` m
                WHERE $wcond AND p.date>={$_SESSION['start_date']} AND m.id_salesorder=p.id_salesorder AND order_approve_time IS NOT NULL
                GROUP BY m.id_sale ORDER BY p.date";
        $billapprove = $this->m->sql_getall($sql,1,"","order_approve_time");
        $sql = "SELECT p.date, p.id_sale, p.invno, group_concat(p.id_product ORDER BY p.id_product ASC) as id_product
                FROM `{$this->prefix}saledetail` p
                WHERE $wcond AND p.date>={$_SESSION['start_date']}
                GROUP BY p.id_sale ORDER BY p.date";
        $bill = $this->m->sql_getall($sql,1,"","id_sale");
        foreach($orders as $orderk => $orderv) {
            if ($orderv['approve_time']) {
                $t = $orderv['approve_time'];
                $orders[$orderk]['account_date'] = @$billapprove[$t]['date'];
                $b = @$billapprove[$t]['id_sale'];
                $orders[$orderk]['despatch_date'] = @$bill[$b]['date'];
                $orders[$orderk]['despatch_no'] = @$bill[$b]['invno'];
            }
        }
        $this->sm->assign("orders", $orders);
        $this->sm->assign("page", "historyd.tpl.html");
    }
    function history() {
	$ids = $this->getpartyids();
        $id_so = isset($_REQUEST['id_so']) ? $_REQUEST['id_so'] : "";
        $wcond = ($id_so) ? " AND h.id_so=$id_so " : "";
        $sql = "SELECT s.name AS sname, h.name, p.id_head, s.id_so, COUNT(*) AS orders, SUM(IF(p.approve_time!='',1,0)) AS approve, SUM(IF(p.qty=0,1,0)) AS cancel 
                FROM ( SELECT id_head, concat(date,' ',time) AS add_time, approve_time, SUM(approve_qty+approve_free) AS qty FROM `{$this->prefix}partner` 
                    WHERE id_head IN ($ids) GROUP BY id_head, add_time) p, `{$this->prefix}head` h, `{$this->prefix}so` s
                WHERE p.id_head=h.id_head AND h.id_so=s.id_so {$wcond}
                GROUP BY s.id_so,p.id_head ORDER BY s.name,h.name";
        $orders = $this->m->sql_getall($sql);
        $this->sm->assign("orders", $orders);
        $this->sm->assign("page", "history.tpl.html");
    }
    function itemreport() {
        $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("Y-m-d", strtotime('-30 days'));
        $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-d");

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $_SESSION['id_user'];
        $sql = "SELECT id_so, name, type, id_heads FROM {$this->prefix}so WHERE id_parent='$id' ORDER BY name";
        $level = $this->m->sql_getall($sql,1,"","id_so");
        
        $ids = $this->getpartyids($id);
        $sql = "SELECT DISTINCT p.id_product,  p.name
            FROM {$this->prefix}saledetail s, {$this->prefix}head h, {$this->prefix}product p
            WHERE h.id_head IN ($ids) AND s.id_head=h.id_head AND (s.date >= '" . $_REQUEST['start_date'] . "' AND s.date <= '" . $_REQUEST['end_date'] . "') AND s.id_product=p.id_product
            ORDER BY p.name";
        $products = $this->m->sql_getall($sql,2,"name","id_product");

        $sql = "SELECT concat(h.id_head,'_',s.id_product) AS id, SUM(s.qty+s.free) AS qty
            FROM {$this->prefix}saledetail s, {$this->prefix}head h
            WHERE h.id_head IN ($ids) AND s.id_head=h.id_head AND (s.date >= '" . $_REQUEST['start_date'] . "' AND s.date <= '" . $_REQUEST['end_date'] . "')
            GROUP BY 1";
        $salesdet = $this->m->sql_getall($sql,1,"","id");
        $this->sm->assign("level", $level);
        $this->sm->assign("products", $products);
        $this->sm->assign("salesdet", $salesdet);
        $this->sm->assign("page", "itemreport.tpl.html");
    }
    function salesreport() {
        $ids = $this->getpartyids();
        $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("Y-m-d", strtotime('-30 days'));
        $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-d");
        // $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("Y-m-01");
        // $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-t");
        if (@$_REQUEST['item']) {
            $sql = "SELECT s.rate, s.qty, s.free, p.name AS pname, h.id_head, h.name, h.address1, h.address2, h.address3
                FROM {$this->prefix}saledetail s, {$this->prefix}product p, {$this->prefix}head h, {$this->prefix}area a
                WHERE h.debtor AND h.id_area=a.id_area AND s.id_product=p.id_product AND h.id_head IN ($ids) AND s.id_head=h.id_head AND 
                    (s.date >= '" . $_REQUEST['start_date'] . "' AND s.date <= '" . $_REQUEST['end_date'] . "') 
                ORDER BY h.name, h.address1, p.name";
        } else {
            $sql = "SELECT s.id_sale, s.invno, s.date, s.vat+s.totalcess+s.tcsamt AS tax, s.total, h.id_head, h.name, h.address1, h.address2, h.address3
                FROM {$this->prefix}sale s, {$this->prefix}head h, {$this->prefix}area a
                WHERE h.debtor AND h.id_area=a.id_area AND h.id_head IN ($ids) AND s.id_head=h.id_head AND 
                    (s.date >= '" . $_REQUEST['start_date'] . "' AND s.date <= '" . $_REQUEST['end_date'] . "') 
                ORDER BY h.name, h.address1, s.date";
        }
        $sales = $this->m->sql_getall($sql);
        $this->sm->assign("sales", $sales);
        $this->sm->assign("page", "salesreport.tpl.html");      
    }
    function levelreport() {
        $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("Y-m-d", strtotime('-30 days'));
        $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-d");

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $_SESSION['id_user'];
        $sql = "SELECT id_so, name, type, id_heads FROM {$this->prefix}so WHERE id_parent='$id' ORDER BY name";
        $level = $this->m->sql_getall($sql,1,"","id_so");
        
        $ids = $this->getpartyids($id);
        $sql = "SELECT h.id_head, COUNT(*) as cnt, SUM(s.vat+s.totalcess+s.tcsamt) AS tax, SUM(s.total) as total
            FROM {$this->prefix}sale s, {$this->prefix}head h
            WHERE h.id_head IN ($ids) AND s.id_head=h.id_head AND (s.date >= '" . $_REQUEST['start_date'] . "' AND s.date <= '" . $_REQUEST['end_date'] . "') 
            GROUP BY h.id_head ORDER BY h.id_head";
        $sales = $this->m->sql_getall($sql,1,"","id_head");
        $this->sm->assign("level", $level);
        $this->sm->assign("sales", $sales);
        $this->sm->assign("page", "levelreport.tpl.html");
    }
    function adminstock() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "AND p.id_company='$id_company'";
        } else {
            $wcond = "";
        }
        $sql = "SELECT h.name AS hname, h.address1, p.name, c.name AS cname, b.* FROM 
                (SELECT a.id_head, a.id_product, SUM(a.oqty) AS oqty, SUM(a.sqty) AS sqty, SUM(a.pqty) AS pqty FROM 
                 (SELECT id_head, id_product, SUM(IF(date<'$sdate',1,0)*(qty+free)) AS oqty, SUM(IF(date>='$sdate' AND date<='$edate',1,0)*(qty+free)) AS pqty, 0 AS sqty
                    FROM {$this->prefix}saledetail WHERE date<='$edate' GROUP BY 1,2
                  UNION ALL
                  SELECT id_head, id_product, SUM(IF(date<'$sdate',1,0)*-qty) AS oqty, 0 AS pqty, SUM(IF(date>='$sdate' AND date<='$edate',1,0)*qty) AS sqty 
                    FROM {$this->prefix}partner_stock WHERE date<='$edate' GROUP BY 1,2) a GROUP BY 1,2) 
                    b, {$this->prefix}product p, {$this->prefix}company c, {$this->prefix}head h
                WHERE b.id_product=p.id_product AND b.id_head=h.id_head AND p.id_company=c.id_company {$wcond}
                ORDER BY hname, address1, cname, p.name";
        $stock = $this->m->sql_getall($sql);
        $this->sm->assign("stock", $stock);
        
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);

        $this->sm->assign("page", "partner/admin/adminstock.tpl.html");
    }
    function login_partner() {
        $type = "party";
        $sql = "SELECT l.*, h.name, h.address1 FROM login_details l, {$this->prefix}head h
                WHERE l.type='$type' AND l.id_user=h.id_head ORDER BY h.name, h.address1, l.id";
        $user1 = $this->m->sql_getall($sql);
        $user = array();
        foreach ($user1 as $k => $v) {
            $id = $v['id_user'];
            $lt = $v['logintype'];
            $user[$id]['name'] = $v['name'];
            $user[$id]['address1'] = $v['address1'];
            $user[$id]['ip'] = $v['ip'];
            if ($lt=="Login") {
                $user[$id]['logintime'] = $v['date'];
                $user[$id]['logouttime'] = "";
            } else {
                $user[$id]['logouttime'] = $v['date'];
            }

            $user[$id]['status'] = $user[$id]['logouttime'] ? "OffLine" : "Online";
        }
        $this->sm->assign("user", $user);
        $this->sm->assign("page", "partner/admin/login_partner.tpl.html");
    }
    function login_represent() {
        $type = "representative";
        $sql = "SELECT l.*, h.name FROM login_details l, {$this->prefix}represent h
                WHERE l.type='$type' AND l.id_user=h.id_represent ORDER BY h.name, l.id";
        $user1 = $this->m->sql_getall($sql);
        $user = array();
        foreach ($user1 as $k => $v) {
            $id = $v['id_user'];
            $lt = $v['logintype'];
            $user[$id]['name'] = $v['name'];
            $user[$id]['ip'] = $v['ip'];
            if ($lt=="Login") {
                $user[$id]['logintime'] = $v['date'];
                $user[$id]['logouttime'] = "";
            } else {
                $user[$id]['logouttime'] = $v['date'];
            }
            $user[$id]['status'] = $user[$id]['logouttime'] ? "OffLine" : "Online";
        }
        $this->sm->assign("user", $user);
        $this->sm->assign("page", "partner/admin/login_represent.tpl.html");
    }
    function adminsales() {
        //$_REQUEST['start_date'] = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y", strtotime('-30 days'));
        //$_REQUEST['end_date'] = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y");
        $_REQUEST['start_date'] = @$_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("Y-m-d", strtotime('-30 days'));
        $_REQUEST['end_date'] = @$_REQUEST['end_date'] ? $_REQUEST['end_date'] : date("Y-m-d");
        $sdate = $_REQUEST['start_date'];
        $edate = $_REQUEST['end_date'];
        //$sdate = $this->format_date($_REQUEST['start_date']);
        //$edate = $this->format_date($_REQUEST['end_date']);
        $wcond = @$_REQUEST['company'] ? " AND p.id_company='{$_REQUEST['company']}' " : "";
        if ($_SESSION['is_admin']!=1) {
            $ids = $this->getpartyids();
            $wcond .= " AND h.id_head IN ($ids) ";
            $this->sm->assign("page", "admin/adminsales.tpl.html");
        } else {
            $this->sm->assign("page", "partner/admin/adminsales.tpl.html");
        }
        $sql = "SELECT h.name AS hname, h.address1, p.name, c.name AS cname, SUM(b.qty) AS tqty
                FROM {$this->prefix}partner_stock b, {$this->prefix}product p, {$this->prefix}company c, {$this->prefix}head h, {$this->prefix}area a
                WHERE b.date>='$sdate' AND b.date<='$edate' AND b.id_product=p.id_product AND b.id_head=h.id_head 
                        AND p.id_company=c.id_company AND h.id_area=a.id_area {$wcond}
                GROUP BY b.id_head, b.id_product
                ORDER BY hname, address1, c.name, p.name";
        $sales = $this->m->sql_getall($sql);
        $this->sm->assign("sales", $sales);

        $comp = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 AND name LIKE '%.' ORDER BY name";
        $company = $this->m->sql_getall($comp, 2, "name", "id");
        $this->sm->assign("company", $company);
    }
    function adminorder() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "AND p.id_company='$id_company'";
        } else {
            $wcond = "";
        }
        $unapproved = isset($_REQUEST['unapproved']) ? $_REQUEST['unapproved'] : '0';
        if ($unapproved == 1) {
            $wcond .= " AND b.approve='0' ";
        }
        $sql = "SELECT h.name AS hname, h.address1, p.name, c.name AS cname, b.*, r.name AS rname
                FROM {$this->prefix}partner b LEFT JOIN {$this->prefix}represent r ON b.id_represent=r.id_represent, {$this->prefix}product p, {$this->prefix}company c, {$this->prefix}head h
                WHERE b.date>='$sdate' AND b.date<='$edate' AND b.id_product=p.id_product AND b.id_head=h.id_head AND p.id_company=c.id_company {$wcond}
                ORDER BY b.date, hname, address1, c.name, p.name";
        $order = $this->m->sql_getall($sql);
        $this->sm->assign("order", $order);

        $comp = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($comp, 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("page", "partner/admin/adminorder.tpl.html");
    }
    function notification() {
        $type = $_SESSION['type'];
        $id = $_SESSION['id_user'];
        $wcond = " WHERE type='$type' AND id_head='$id' ";
        $all = isset($_REQUEST['all']) ? $_REQUEST['all'] : '0';
        if ($all == 0) {
            $wcond .= " AND date_read IS NULL";
        }
        $sql = "SELECT * FROM {$this->prefix}notification {$wcond} ORDER BY date DESC";
        $notification = $this->m->sql_getall($sql);
        $this->pr($notification);
        $this->sm->assign("notification", $notification);
        $this->sm->assign("page", "notification.tpl.html");
    }
    function readnotification() {
        $id = $_REQUEST['id'];
        $sql = "UPDATE {$this->prefix}notification SET date_read=NOW() WHERE id='{$id}'";
        $this->m->query($sql);
        $this->redirect("index.php?module=partner&func=notification");      
    }
    function savenotification($table, $id_head, $message, $date, $from_id, $from_name, $type) {
        $sql = "INSERT INTO {$table} (id_head, message, date, from_id, from_name, type) VALUES 
                ('$id_head', '$message', '$date', '$from_id', '$from_name', '$type'";
        $this->m->query($sql);
        exit;
    }
    function sms() {
        $res = $this->sendsms("9437317469", "This is a test message");
        $this->pr($res);
        exit;
    }
    function firenotification() {
        define('API_ACCESS_KEY','AAAA1Dmic9o:APA91bHttW_8ODget_JzHACGdyWuPqQUptuXt2DA0DJDdOysbnA-PecLh08iZT8lMjtYoplV4sR5S6NnVhsFW-RxzcX6iNab662rBBq3kmqv21rCPGEEgiDWZtnZVcr8JjNZnMLoCyYw');
        $token='235zgagasd634sdgds46436';
        $notification = [
            'title' =>'title',
            'body' => 'body of message.',
            'icon' =>'myIcon', 
            'sound' => 'mySound'
        ];
        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
        $fcmNotification = [
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];
        $headers = ['Authorization: key=' . API_ACCESS_KEY,  'Content-Type: application/json' ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        exit;
    }
    function _send_notification($gid = '', $userId = '', $message_data = 'Hello There',$notification_type='',$noti_title='Title',$noti_body='Body') {
        $apikey         = "AAAAW542UKE:APA91bH92OWVInp7731suaD-dhGZX-FcIl2fU97aK9amHOe48g5Y4Yq0FH2n3GbWUrqsbg3uT8iB_yQowib-oDHhuLN6iiAoXrz6V0MRkduZvYD4d26dAzlOjpHKqI_oazdLCN8Qf2X2";
        $message        = $message_data;
        $msg            = array();
        $msg            = array('message' => $message, 'userId' => $userId, 'notification_type'=>$notification_type);
        //$gid          = "cOuVEHKuQbWjiz0KHYu2PA:APA91bHDQYbpzildw4co1IfMC8-MIBaXlzCOc0UvtjZt4bcSnmYyd84VCWfUHHAKj5kPceA-tPlkr0972sOs3Blb08lt8BduarPled0UyDOsd27plO_rpxk6wqNaJkfhqPeUNagP30Qd";
        $reg_id         = array($gid);
        $fields = array(
                'registration_ids' => $reg_id,
                'data' => $msg,
                //'notification' => array('body' => $noti_body,'title' => $noti_title,'sound' => 'default','icon' => 'icon')
        );
        $headers = array( 'Authorization: key=' . $apikey, 'Content-Type: application/json' );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        echo $result; exit;
    }
}
?>
