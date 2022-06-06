<?php

class accountsana extends common {

    function __construct() {
        $this->checklogin();
        $this->get_permission("sales", "REPORT");
        $this->table_prefix();
        parent:: __construct();
    }

    function _default() {
        echo "This function is not enabled...";
    }

    function summary() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head, h.name FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.name='cash' AND p.content=h.code";
        $cash = $this->m->sql_getall($sql, 2, "name", "id_head");
        $sql = "SELECT h.* FROM {$this->prefix}head h, {$this->prefix}group g WHERE h.id_group=g.id_group AND g.name='BANK ACCOUNTS' ORDER BY 1";
        $data = $this->m->sql_getall($sql, 2, "name", "id_head");
        $acs = $cash + $data;
        $ac = implode(",", array_keys($acs));
	$sql = "SELECT date, dhead AS id_head, ROUND(SUM(total),2) AS total FROM {$this->prefix}ledger l, {$this->prefix}head h
		WHERE l.chead=h.id_head AND h.debtor AND l.dhead IN ($ac) AND (`date`>='$sdate' AND `date`<='$edate') GROUP BY 1,2";
	$opening = $this->m->sql_getall($sql, 1, "", "date", "id_head");
        $this->sm->assign("acs", $acs);
        $this->sm->assign("opening", $opening);
    }

    function rep_sale() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';

        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $sql = "SELECT DISTINCT MONTHNAME(date) AS month FROM {$this->prefix}sale s WHERE date>='$sdate' AND date<='$edate' ORDER BY date ";
        $this->sm->assign("month", $this->m->sql_getall($sql, 1, "month"));

        $sql = "SELECT DISTINCT r.id_represent, r.name FROM {$this->prefix}represent r, {$this->prefix}sale s WHERE s.id_represent=r.id_represent AND s.date >= '$sdate' AND s.date <= '$edate' ORDER BY name";
        $this->sm->assign("data", $this->m->sql_getall($sql, 2, "name", "id_represent"));

        switch ($_REQUEST['option']) {
        case 1:
            $sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.total) AS `total`, s.id_represent, r.name, s.id_head, h.name AS hname, h.address1
                    FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}represent r ON s.id_represent=r.id_represent 
                         LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                    WHERE s.date >= '$sdate' AND s.date <= '$edate'
                    GROUP BY s.id_represent, s.id_head, MONTH(s.date), YEAR(s.date) ORDER BY name, hname, s.date ";
            $rs = $this->m->query($sql);
            while ($r = mysql_fetch_assoc($rs)) {
                $m = $r["month"];
                $rep = $r["id_represent"];
                $h = $r["id_head"];
                $res[$rep][$h]['name'] = $r['hname'];
                $res[$rep][$h][$m] = $r['total'];
            }
            break;
        case 2:
            $sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.total) AS `total`, s.id_represent, r.name
                    FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}represent r ON s.id_represent=r.id_represent
                    WHERE s.date >= '$sdate' AND s.date <= '$edate'
                    GROUP BY s.id_represent, MONTH(s.date), YEAR(s.date) ORDER BY name, s.date ";
            $res = $this->m->sql_getall($sql, 1, "", "id_represent", "month");
        }
        $this->sm->assign("sales", $res);
    }
    function rep_coll() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head, h.name FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.name='cash' AND p.content=h.code";
        $cash = $this->m->sql_getall($sql, 2, "name", "id_head");
        $sql = "SELECT h.* FROM {$this->prefix}head h, {$this->prefix}group g WHERE h.id_group=g.id_group AND g.name='BANK ACCOUNTS' ORDER BY 1";
        $data = $this->m->sql_getall($sql, 2, "name", "id_head");
        $acs = $cash + $data;
        $ac = implode(",", array_keys($acs));

        $sql = "SELECT s.id_head, s.id_represent FROM {$this->prefix}sale s, (
		SELECT id_head, MAX(id_sale) AS id_sale FROM {$this->prefix}sale GROUP BY 1) b
                WHERE s.id_sale=b.id_sale ORDER BY 1,2";


        $sql = "SELECT h.id_head, r.id_represent FROM {$this->prefix}head h, {$this->prefix}area a, {$this->prefix}represent r
	        WHERE h.id_area=a.id_area AND a.id_represent=r.id_represent ORDER BY 1,2";

        $pvsr = $this->m->sql_getall($sql, 2, "id_represent", "id_head");

        // party vs represent

        $sql = "SELECT DISTINCT MONTHNAME(date) AS month FROM {$this->prefix}ledger s WHERE date>='$sdate' AND date<='$edate' ORDER BY date ";
	$d = $this->m->sql_getall($sql, 1, "month");
        $this->sm->assign("month", $d);

        $sql = "SELECT r.id_represent, r.name FROM {$this->prefix}represent r ORDER BY name";
        $represent = $this->m->sql_getall($sql, 2, "name", "id_represent");
        $this->sm->assign("data", $represent);
        switch ($_REQUEST['option']) {
        case 1:
            $sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.total) AS `total`, h.id_head, h.name AS hname, h.address1
                    FROM {$this->prefix}ledger s, {$this->prefix}head h
                    WHERE s.chead=h.id_head AND h.debtor AND s.dhead IN ($ac) AND s.date >= '$sdate' AND s.date <= '$edate'
                    GROUP BY h.id_head, MONTH(s.date), YEAR(s.date) ORDER BY name, hname, s.date ";
            $rs = $this->m->query($sql);
            while ($r = mysql_fetch_assoc($rs)) {
                $m = $r["month"];
                $h = $r["id_head"];
                $rep = $pvsr[$r["id_head"]];
                $res[$rep][$h]['name'] = $r['hname'];
                $res[$rep][$h][$m] = $r['total'];
            }
            //$this->pr($res);
            break;
        case 2:
            $sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.total) AS `total`, h.id_head
                    FROM {$this->prefix}ledger s, {$this->prefix}head h
                    WHERE s.chead=h.id_head AND h.debtor AND s.dhead IN ($ac) AND s.date >= '$sdate' AND s.date <= '$edate'
                    GROUP BY h.id_head, MONTH(s.date), YEAR(s.date) ORDER BY name, s.date ";
            $rs = $this->m->query($sql);
            while ($r = mysql_fetch_assoc($rs)) {
                $m = $r["month"];
                $h = $r["id_head"];
                $rep = $pvsr[$r["id_head"]];
                $res[$rep][$m]['total'] = @$res[$rep][$m]['total'] + $r['total'];
            }
        }
        $this->sm->assign("sales", $res);      
    }
}
?>

