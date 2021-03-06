<?php
class report extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function vehicledetail() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $_REQUEST['vehno'] = $vehno = isset($_REQUEST['vehno']) ? $_REQUEST['vehno'] : "";
        $sql = "SELECT b.*, a.name AS aname, c.name AS cname FROM {$this->prefix}billdet b, {$this->prefix}company c, {$this->prefix}area a WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.vehno='$vehno' AND b.id_from_area=a.id_area AND b.id_company=c.id_company ORDER BY date";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function despatchregister() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $_REQUEST['vehno'] = $vehno = isset($_REQUEST['vehno']) ? $_REQUEST['vehno'] : "";

        $wcond = @$_REQUEST['ownveh'] ? " AND s.ownveh = '".$_REQUEST['ownveh']."' " : " " ;
        $wcond .= @$_REQUEST['vehno'] ? " AND sd.vehno = '".$_REQUEST['vehno']."' " : " " ;
        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $wcond .= isset($_REQUEST['area']) ? " AND a.id_area IN (".implode(",", $_REQUEST['area']).") " : " " ;
        $ocond = "sd.date, sd.invno, sd.no";
        switch (@$_REQUEST['order']) {
        case "B":
            $ocond = "sd.bno, sd.bnodate, sd.invno, sd.no";
            break;
        case "C":
            $ocond = "sd.bnodate, sd.invno, sd.no";
            break;
        case "L":
            $ocond = "sd.invno, sd.date, sd.bnodate, sd.no";
            break;
        case "V":
            $ocond = "sd.date, sd.invno, sd.no";
            break;
        case "A":
            $ocond = "sd.date, sd.invno, sd.no";
            break;
        }
        $sql = "SELECT s.vehno, s.tfreight, s.advance, s.vno, s.balance, s.unload+s.detaintion+s.epoint+s.chanda+s.other AS other, s.odate, s.ovno, s.narration, s.tdsamt,
                c.name AS cname, a.name AS aname, sd.date, sd.invno, sd.no, sd.loading, sd.bno, sd.bnodate, sd.lrno, sd.weight, sd.qty, sd.freight
               FROM {$this->prefix}bill s, {$this->prefix}billdet sd, {$this->prefix}area a, {$this->prefix}company c
               WHERE (s.date >= '$sdate' AND s.date <= '$edate') AND s.invno=sd.invno AND sd.id_to_area=a.id_area AND sd.id_company=c.id_company $wcond
               ORDER BY $ocond ";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
    }
    function tripsummary() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $wcond = @$_REQUEST['ownveh'] ? " AND b.ownveh = '".$_REQUEST['ownveh']."' " : " " ;
        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $wcond .= isset($_REQUEST['area']) ? " AND a.id_area IN (".implode(",", $_REQUEST['area']).") " : " " ;

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                    a_bank, a_cheque, bank, cheque, chqdate, b_name,
                    b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, group_concat(DISTINCT a.name) AS aname, c.name AS cname, 
                    c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                    SUM(qty) AS qty, SUM(weight) AS weight
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company $wcond
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function tripsummarynew() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $wcond = @$_REQUEST['ownveh'] ? " AND b.ownveh = '".$_REQUEST['ownveh']."' " : " " ;
        $wcond .= @$_REQUEST['vehno'] ? " AND bd.vehno = '".$_REQUEST['vehno']."' " : " " ;
        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $wcond .= isset($_REQUEST['area']) ? " AND a.id_area IN (".implode(",", $_REQUEST['area']).") " : " " ;

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                    a_bank, a_cheque, bank, cheque, chqdate, b_name,
                    b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, group_concat(DISTINCT a.name) AS aname, c.name AS cname, 
                    c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                    SUM(qty) AS qty, SUM(weight) AS weight
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company $wcond
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function balancepayment() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $_REQUEST['start_date1'] = $sdate1 = isset($_REQUEST['start_date1']) ? $_REQUEST['start_date1'] : date("Y-m-d");
        $_REQUEST['end_date1'] = $edate1 = isset($_REQUEST['end_date1']) ? $_REQUEST['end_date1'] : date("Y-m-d");

        $wcond = @$_REQUEST['type'] ? " AND b.ownveh = '".$_REQUEST['type']."' " : " " ;
        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $wcond .= isset($_REQUEST['area']) ? " AND a.id_area IN (".implode(",", $_REQUEST['area']).") " : " " ;

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                    a_bank, a_cheque, bank, cheque, chqdate, b_name,
                    b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, group_concat(DISTINCT a.name) AS aname, c.name AS cname, c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                    SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18', 1, 0)*qty) AS qty,
                    SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18', 1, 0)*weight) AS twt,
                    SUM(if(bd.company='ST' OR bd.company='03', 1, 0)*weight) AS tsaltwt,
                    SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18' OR bd.company='ST' OR bd.company='03', 0, 1)*weight) AS totherwt
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND (b.odate >= '$sdate1' AND b.odate <= '$edate1') AND 
                        b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company $wcond
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }    
    function balancepaymentnew() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $_REQUEST['start_date1'] = $sdate1 = isset($_REQUEST['start_date1']) ? $_REQUEST['start_date1'] : date("Y-m-d");
        $_REQUEST['end_date1'] = $edate1 = isset($_REQUEST['end_date1']) ? $_REQUEST['end_date1'] : date("Y-m-d");

        $wcond = @$_REQUEST['type'] ? " AND b.ownveh = '".$_REQUEST['type']."' " : " " ;
        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $wcond .= isset($_REQUEST['area']) ? " AND a.id_area IN (".implode(",", $_REQUEST['area']).") " : " " ;

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                    a_bank, a_cheque, bank, cheque, chqdate, b_name,
                    b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, 
                    group_concat(DISTINCT a.name) AS aname, c.name AS cname, c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                    SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18', 1, 0)*qty) AS qty,
                    SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18', 1, 0)*weight) AS twt,
                    SUM(if(bd.company='ST' OR bd.company='03', 1, 0)*weight) AS tsaltwt,
                    SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18' OR bd.company='ST' OR bd.company='03', 0, 1)*weight) AS totherwt
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND (b.odate >= '$sdate1' AND b.odate <= '$edate1') AND 
                        b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company $wcond
                GROUP BY bd.date, bd.invno
                ORDER BY b.a_cheque, bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function tds() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $sql = "SELECT * FROM {$this->prefix}bill WHERE (odate >= '$sdate' AND odate <= '$edate') AND tdsamt<>0 ORDER BY date, invno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function paydet() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate'];
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $_REQUEST['type'] = $type = @$_REQUEST['type'] ? $_REQUEST['type'] : "A";
        $_REQUEST['cash'] = $cash = @$_REQUEST['cash'] ? $_REQUEST['cash'] : "C";
        switch ($type) {
        case "A":
            if ($cash=="Q") {
                $wcond = " s.cadvance<>0 AND (s.date >= '$sdate' AND s.date <= '$edate') ";
            } else {
                $wcond = " s.advance<>0 AND (s.date >= '$sdate' AND s.date <= '$edate') ";
            }
            break;
        case "B":
            if ($cash=="Q") {
                $wcond = " s.balance-s.tdsamt+s.detaintion<>0 AND (s.odate >= '$sdate' AND s.odate <= '$edate') AND (s.cheque!='' OR s.cheque IS NOT NULL) ";
            } else {
                $wcond = " s.balance-s.tdsamt+s.detaintion<>0 AND (s.odate >= '$sdate' AND s.odate <= '$edate') AND (s.cheque='' OR s.cheque IS NULL) ";
            }
            break;
        case "F":
            $wcond = " s.fuel<>0 AND (s.date >= '$sdate' AND s.date <= '$edate') ";
            break;
        }
        $sql = "SELECT s.vehno, s.tfreight, s.advance, s.vno, s.balance-s.tdsamt+s.detaintion AS balance, s.unload+s.epoint+s.chanda+s.other-s.shortage AS other,
                s.treturn, s.fuel, s.odate, s.ovno, s.narration, s.cadvance, s.a_name, s.a_cheque, s.a_chqdate, s.a_bank, s.b_name, s.cheque, s.chqdate, s.bank,
                group_concat(DISTINCT a.name) AS aname, sd.date, sd.invno 
               FROM {$this->prefix}bill s, {$this->prefix}billdet sd, {$this->prefix}area a
               WHERE s.invno=sd.invno AND sd.id_to_area=a.id_area AND $wcond GROUP BY sd.date, sd.invno ORDER BY s.date, sd.invno, s.vehno ";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function pendingfreight() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $sql = "SELECT b.vehno, bd.*, c.name as cname FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c
            WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND bd.freight=0 AND b.id_bill=bd.id_bill  AND bd.id_company=c.id_company
            ORDER BY bd.date, bd.no";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function pendingtruckfreight() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $wcond = @$_REQUEST['ownveh'] ? " AND b.ownveh = '".$_REQUEST['ownveh']."' " : " " ;

        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $sql = "SELECT b.ownveh, b.vehno, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.other, b.odate, b.ovno, b.narration, 
                bd.*, c.name as cname, a.name as aname 
            FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
            WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND (b.ovno='' OR b.ovno IS NULL) AND b.id_bill=bd.id_bill AND 
                    bd.id_to_area=a.id_area AND bd.id_company=c.id_company {$wcond}
            ORDER BY bd.date, bd.invno, bd.no";

        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
    }
    function shortagedetail() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $sql = "SELECT b.date, b.vehno, b.vno, b.odate, b.shortage, b.ovno, b.narration, group_concat(DISTINCT a.name) AS aname
            FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}area a
            WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.shortage!=0 AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area
            GROUP BY bd.date, bd.invno
            ORDER BY b.date, bd.invno, b.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function tankwise() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $wcond = @$_REQUEST['tankname'] ? " AND b.tankname = '".$_REQUEST['tankname']."' " : " " ;

        $sql = "SELECT b.date, b.vehno, b.token, b.tankname, b.fuel, group_concat(DISTINCT a.name) AS aname
            FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}area a
            WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.fuel!=0 AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area $wcond
            GROUP BY bd.date, bd.invno
            ORDER BY b.date, bd.invno, b.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
        
        $res1 = $this->m->query("SELECT DISTINCT tankname FROM {$this->prefix}bill WHERE tankname!=''");
        $this->sm->assign("tankname", $this->m->getall($res1, 2, "tankname", "tankname"));
    }
    function balancepaymentdetail() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");

        $_REQUEST['start_date1'] = $sdate1 = isset($_REQUEST['start_date1']) ? $_REQUEST['start_date1'] : date("Y-m-d");
        $_REQUEST['end_date1'] = $edate1 = isset($_REQUEST['end_date1']) ? $_REQUEST['end_date1'] : date("Y-m-d");

        $wcond = @$_REQUEST['type'] ? " AND b.ownveh = '".$_REQUEST['type']."' " : " " ;
        $wcond .= isset($_REQUEST['company']) ? " AND c.id_company IN (".implode(",", $_REQUEST['company']).") " : " " ;
        $wcond .= isset($_REQUEST['area']) ? " AND a.id_area IN (".implode(",", $_REQUEST['area']).") " : " " ;

        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.vehno, b.tfreight, b.advance, b.cadvance, b.fuel, b.a_cheque, b.a_bank, b.vno, b.balance, b.tdsamt,
                    b.unload+b.detaintion+b.epoint+b.chanda+b.other-b.shortage AS other,
                    bank, cheque, chqdate, b_name, odate, ovno, narration,
                    group_concat(DISTINCT a.name) AS aname, c.name AS cname, c.freight_p, bd.vehno, bd.bno, bd.bnodate, 
                    SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight, bd.date, bd.invno
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE b.id_bill=bd.id_bill AND (b.date >= '$sdate' AND b.date <= '$edate') AND
                        (b.odate >= '$sdate1' AND b.odate <= '$edate1') AND 
                        bd.id_to_area=a.id_area AND bd.id_company=c.id_company $wcond
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";

        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function balancesummary() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $prev = $this->previousyear_prefix($this->prefix);
        $s1 = "SELECT 0 AS flag, s.odate, s.vehno, SUM(s.tfreight) AS tfreight, SUM(s.balance-s.tdsamt) AS balance,
            SUM(-s.shortage+s.load+s.unload+s.epoint+s.detaintion+s.chanda+s.treturn+s.other) AS other FROM {$this->prefix}bill s
            WHERE s.balance<>0 AND (s.odate >= '$sdate' AND s.odate <= '$edate') GROUP BY s.odate, s.vehno 
            UNION ALL
            SELECT 1 AS flag, s.odate, s.vehno, SUM(s.tfreight) AS tfreight, SUM(s.balance-s.tdsamt) AS balance,
            SUM(-s.shortage+s.load+s.unload+s.epoint+s.detaintion+s.chanda+s.treturn+s.other) AS other FROM {$prev}bill s
            WHERE s.balance<>0 AND (s.odate >= '$sdate' AND s.odate <= '$edate') GROUP BY s.odate, s.vehno ";
        $sql = "SELECT s.*, o.name, o.address, o.address1, o.panno
	            FROM ($s1) s LEFT JOIN {$this->prefix}vehicle v ON s.vehno=v.vehno LEFT JOIN {$this->prefix}vowner o ON v.id_vowner=o.id_vowner
                ORDER BY s.vehno, s.odate";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }

}
?>