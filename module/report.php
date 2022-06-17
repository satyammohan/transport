<?php
class report extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function vehicledetail() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-04-01");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $_REQUEST['vehno'] = $vehno = isset($_REQUEST['vehno']) ? $_REQUEST['vehno'] : "";
<<<<<<< HEAD


        $sql = "SELECT b.*, a.name AS aname, c.name AS cname FROM {$this->prefix}billdet b, {$this->prefix}company c, {$this->prefix}area a
            WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.vehno='$vehno' AND b.id_from_area=a.id_area AND b.id_company=c.id_company ORDER BY date";
=======
        $sql = "SELECT b.*, a.name AS aname, c.name AS cname FROM {$this->prefix}billdet b, {$this->prefix}company c, {$this->prefix}area a WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.vehno='$vehno' AND b.id_from_area=a.id_area AND b.id_company=c.id_company ORDER BY date";
>>>>>>> eded28d1d1b51ce7eb455dc152e8a0bb5c86a597
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function despatchregister() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-01");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $_REQUEST['vehno'] = $vehno = isset($_REQUEST['vehno']) ? $_REQUEST['vehno'] : "";
        $sql = "SELECT b.*, a.name AS aname, c.name AS cname FROM {$this->prefix}billdet b, {$this->prefix}company c, {$this->prefix}area a WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.vehno='$vehno' AND b.id_to_area=a.id_area AND b.id_company=c.id_company ORDER BY date";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function shortagedetail() {
    }
    function tripsummary() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate'];
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, group_concat(DISTINCT a.name) AS aname, c.name AS cname, 
                c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                SUM(qty) AS qty, SUM(weight) AS weight
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company 
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function tripsummarynew() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate'];
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, group_concat(DISTINCT a.name) AS aname, c.name AS cname, 
                c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                SUM(qty) AS qty, SUM(weight) AS weight
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company 
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
    function balancepayment() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate'];
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name");
        $this->sm->assign("company", $this->m->getall($res1, 2, "name", "id_company"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        //SELE s.vehno, s.tfreight, s.advance, s.cadvance, s.a_cheque, s.a_bank, s.vno, s.balance, s.tdsamt, s.unload+s.detaintion+s.epoint+s.chanda+s.other-s.shortage AS other, s.bank, s.cheque, s.chqdate, s.b_name, s.odate, s.ovno, s.narration, sd.* FROM bill s, billdet sd, company p, area a WHERE s.invno=sd.invno AND BETW(s.idate,msdate,medate) AND BETW(s.odate,tmsdate,tmedate) AND sd.company=p.code AND p.consider AND &wcond AND sd.area=a.code AND a.consider ORDER BY s.idate, sd.invno, s.vehno INTO TABLE desreg
        $sql = "SELECT b.date, b.tfreight, b.advance, b.cadvance, b.fuel, b.vno, b.balance, b.odate, b.ovno, b.other, b.narration, 
                b.unload+b.detaintion+b.epoint+b.chanda+b.other AS other, group_concat(DISTINCT a.name) AS aname, c.name AS cname, c.freight_p, bd.vehno, bd.bno, bd.bnodate, SUM(bd.weight) AS weight, SUM(bd.qty) AS qty, SUM(bd.freight) AS freight,
                SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18', 1, 0)*qty) AS qty,
                SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18', 1, 0)*weight) AS twt,
                SUM(if(bd.company='ST' OR bd.company='03', 1, 0)*weight) AS tsaltwt,
                SUM(if(bd.company='PP' OR bd.company='07' OR bd.company='18' OR bd.company='ST' OR bd.company='03', 0, 1)*weight) AS totherwt
                FROM {$this->prefix}bill b, {$this->prefix}billdet bd, {$this->prefix}company c, {$this->prefix}area a
                WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.id_bill=bd.id_bill AND bd.id_to_area=a.id_area AND bd.id_company=c.id_company 
                GROUP BY bd.date, bd.invno
                ORDER BY bd.date, bd.invno, bd.vehno";
        $data = $this->m->sql_getall($sql);
        pr($data);
        $this->sm->assign("data", $data);
    }
}
?>
