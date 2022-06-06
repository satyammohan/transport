<?php

class accounts extends common {

    function __construct() {
        $this->checklogin();
//        $this->get_permission("accounts", "REPORT");
        $this->table_prefix();
        parent:: __construct();
    }

    function _default() {
        $this->sm->assign("page", "common/comming.tpl.html");
    }
function fundflowd() {
        $gid = $_REQUEST['id']; $pid = $_REQUEST['pid']; $s = $_REQUEST['sdate']; $e = $_REQUEST['edate']; $d = $_REQUEST['debit'];
        if ($d) {
            $sql = "SELECT h.name, SUM(total) AS total FROM {$this->prefix}ledger l, {$this->prefix}head h
            WHERE l.chead=h.id_head AND l.dhead IN ({$pid}) AND h.id_group='$gid' AND (date >= '$s' AND date <= '$e') GROUP BY chead ORDER BY h.name";
        } else {
            $sql = "SELECT h.name, SUM(total) AS total FROM {$this->prefix}ledger l, {$this->prefix}head h
            WHERE l.dhead=h.id_head AND l.chead IN ({$pid}) AND h.id_group='$gid' AND (date >= '$s' AND date <= '$e') GROUP BY dhead ORDER BY h.name";
        }
        $fundflowd = $this->m->sql_getall($sql);
        $this->sm->assign("fundflowd", @$fundflowd);
    }
    function fundflow() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['all'] = isset($_REQUEST['all']) ? $_REQUEST['all'] : '2';
        $_REQUEST['start_date'] = $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $_REQUEST['end_date'] = $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head AS id, h.name FROM {$this->prefix}head h, {$this->prefix}group g
            WHERE h.id_group=g.id_group AND (h.name LIKE 'cash%' OR g.name LIKE 'BANK ACCOUNTS%') AND !h.status ORDER BY h.name";
        $party = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("party", $party);
        if ($_REQUEST['all']==1) {
            $party = $_REQUEST['party2'] = implode(array_keys($party), ',');
        } else {
            $party = @$_REQUEST['party2'];
        }
        if (@$party) {
            $sql = "SELECT SUM(IF(dhead IN ($party), 1, -1)*total) AS total FROM {$this->prefix}ledger WHERE (chead IN ($party) or dhead IN ($party)) AND `date`<'$sdate'";
            $opening = $this->m->sql_getall($sql);
            if ($opening[0]['total'] > 0) {
                $op = array("dhead" => 0, "dname" => "Opening Balance", "dtotal" => $opening[0]['total']);
            } else {
                $op = array("chead" => 0, "cname" => "Opening Balance", "ctotal" => -$opening[0]['total']);
            }
            switch ($_REQUEST['option']) {
            case 1:
                $sql1 = "SELECT g.id_group AS chead, g.name AS cname, SUM(total) AS ctotal FROM {$this->prefix}ledger l, {$this->prefix}head h, {$this->prefix}group g
                        WHERE l.dhead=h.id_head AND l.chead IN ($party) AND (date >= '$sdate' AND date <= '$edate') AND type!='H' AND h.id_group=g.id_group GROUP BY 1 ORDER BY h.name";
                $sql2 = "SELECT g.id_group AS dhead, g.name AS dname, SUM(total) AS dtotal FROM {$this->prefix}ledger l, {$this->prefix}head h, {$this->prefix}group g
                        WHERE l.chead=h.id_head AND l.dhead IN ($party) AND (date >= '$sdate' AND date <= '$edate') AND type!='H' AND h.id_group=g.id_group GROUP BY 1 ORDER BY h.name";
                break;
            case 2:
                $sql1 = "SELECT dhead AS chead, h.name AS cname, SUM(total) AS ctotal FROM {$this->prefix}ledger l, {$this->prefix}head h
                    WHERE l.dhead=h.id_head AND l.chead IN ($party) AND (date >= '$sdate' AND date <= '$edate') AND type!='H' GROUP BY dhead ORDER BY h.name";
                $sql2 = "SELECT chead AS dhead, h.name AS dname, SUM(total) AS dtotal FROM {$this->prefix}ledger l, {$this->prefix}head h
                    WHERE l.chead=h.id_head AND l.dhead IN ($party) AND (date >= '$sdate' AND date <= '$edate') AND type!='H' GROUP BY chead ORDER BY h.name";
                break;
            }
            $data = $this->m->sql_getall($sql1);
            array_unshift($data, $op);
            $credit = $this->m->sql_getall($sql2);
            foreach ($credit as $k => $v) {
                $data[$k + 1]['dhead'] = $v['dhead'];
                $data[$k + 1]['dname'] = $v['dname'];
                $data[$k + 1]['dtotal'] = $v['dtotal'];
            }
            unset($credit);
        }
        $this->sm->assign("data", @$data);
    }
    function bankrecon() {
        $this->addfield('reconcile', $this->prefix . 'voucher', 'ADD `reconcile` int(1)');
        $this->addfield('reconciledate', $this->prefix . 'voucher', 'ADD `reconciledate` DATETIME');
        $sql = "SELECT id_head, name, id_group, opening_balance, otype FROM {$this->prefix}head WHERE id_group IN
            (SELECT id_group FROM {$this->prefix}group WHERE name LIKE '%bank%') ORDER BY name";
        $heads = $this->m->sql_getall($sql);
        foreach ($heads as $k => $v) {
                $ac = $v['id_head'];
                $s = "SELECT SUM(IF(id_head_debit='$ac', 1, -1)*total) AS total,
                    SUM(If(reconcile=1, IF(id_head_debit='$ac', -1, 1), 0)*total) AS rtotal, MAX(reconciledate) AS date
                FROM {$this->prefix}voucher WHERE id_head_debit='$ac' OR id_head_credit='$ac'";
                $r = $this->m->sql_getall($s);
                $amt = ($v['otype']=="C"?1:1)*$v['opening_balance'];
                $heads[$k]['total'] = $r[0]['total'] - $amt;
                $heads[$k]['rtotal'] = $r[0]['rtotal'] - $amt;
                $heads[$k]['date'] = $r[0]['date'];
        }
        $this->sm->assign("heads", $heads);
    }
    function bankrecondetail() {
        $ac = $_REQUEST['id'];
        $all = $_REQUEST['all'];
        $wcond = ($all==1) ? "" : " AND reconcile!=1 ";
        $s = "SELECT date, id_head_debit, id_head_credit, SUM(total) AS total, ref1, memo, reconcile, group_concat(id_voucher) AS id_voucher
            FROM {$this->prefix}voucher WHERE (id_head_debit='$ac' OR id_head_credit='$ac') $wcond
            GROUP BY date, id_head_debit, id_head_credit, ref1, memo, no ORDER BY reconciledate DESC, date, id_head_debit, id_head_credit";
        $r = $this->m->sql_getall($s);
        $this->sm->assign("vouchers", $r);
        $sql = "SELECT id_head AS id, concat(name,' ',address1) AS name FROM {$this->prefix}head ORDER BY name";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));
    }
    function bankreconsave() {
        $id = $_REQUEST['id'];
        if ($id) {
            $r = $_REQUEST['reconcile'];
            $ids = explode(",",$id);
            $id = "('".implode("','", $ids)."')";
            $sql = "UPDATE {$this->prefix}voucher SET reconcile='$r', reconciledate=NOW() WHERE id_voucher IN $id";
            $this->m->query($sql);
        }
        echo "success";exit;
    }
    function profit() {
# liabilities
#DEFINE CAPITAL_ACCOUNT "0001"
#DEFINE RESERVES_AND_SURPLUS "0002"
#DEFINE LOANS "0003"
#DEFINE CURRENT_LIABILITIES "0004"
#DEFINE SUSPENSE_ACCOUNT "0005"
# assets
#DEFINE FIXED_ASSETS "0006"
#DEFINE INVESTMENTS "0007"
#DEFINE CURRENT_ASSETS "0008"
#DEFINE LOANS_AND_ADVANCES "0009"
#DEFINE MISCELLANEOUS_EXPENDITURE "0010"
# revenue
#DEFINE REVENUE "0011"
# REVENUE SUBGROUPS
#DEFINE SALES_ACCOUNTS "0028"
#DEFINE PURCHASE_ACCOUNTS "0029"
#DEFINE EXPENSES_DIRECT "0030"
#DEFINE EXPENSES_INDIRECT "0031"
#DEFINE OTHER_INCOME "0032"

        $startdate = $_SESSION['start_date'];
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT g.name AS gname,h.id_head, h.name, h.address1, SUM(l.opening) AS opening, SUM(l.debit) AS debit, SUM(l.credit) AS credit, SUM(l.cbal) AS closing
          FROM `{$this->prefix}group` g, `{$this->prefix}head` h, (
          SELECT dhead AS id_head, SUM(IF(date<'$startdate', -1, 0)*total) AS opening, SUM(IF(date>='$startdate' AND date<='$sdate', 1, 0)*total) AS debit, 
            0.00 AS credit, SUM(IF(date<='$sdate', -1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$sdate' GROUP BY 1
          UNION ALL 
          SELECT chead AS id_head, SUM(IF(date<'$startdate', 1, 0)*total) AS opening, 0.00 AS debit, SUM(IF(date>='$startdate' AND date<='$sdate', 1, 0)*total) AS credit, 
            SUM(IF(date<='$sdate', 1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$sdate' GROUP BY 1) l
          WHERE l.id_head=h.id_head AND h.id_group=g.id_group GROUP BY h.id_head ORDER BY h.name";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function cashbook() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT p.name, h.id_head FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.name='cash' AND p.content=h.code";
        $data = $this->m->sql_getall($sql, 2, "id_head", "name");
        $_REQUEST['id'] = $cash = $data['cash'];
        $this->fetchdata($cash, $sdate, $edate);
        $this->sm->assign("page", "accounts/cashbook.tpl.html");
    }

    function gethead() {
        $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
        $sql = "SELECT id_head AS value, concat(name,' ',address1, ' ', IF(debtor, '(DB)', ''), ' ', IF(creditor, '(CR)', '')) AS text FROM {$this->prefix}head WHERE name LIKE '%{$search}%' ORDER BY name LIMIT 100";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function ledger() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $this->fetchdata($_REQUEST['id'], $sdate, $edate);
        $this->sm->assign("page", "accounts/ledger.tpl.html");
    }

    function fetchdata($id, $sdate, $edate) {
        $sql = "SELECT id_head AS id, concat(name,' ',address1) AS name FROM {$this->prefix}head ORDER BY name";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));
        $sql = "SELECT * FROM {$this->prefix}head WHERE id_head='$id'";
	$head = $this->m->sql_getall($sql);
        $this->sm->assign("head1", $head);
        if ($id) {
            $id = $_REQUEST['id'];
            switch ($_REQUEST['option']) {
                case 1:
		$sql_detail = "SELECT sd.id_product, sd.qty, sd.free, sd.rate, sd.tax_per, sd.cess, sd.net_amount, invno AS myno, p.name FROM `{$this->prefix}saledetail` sd, `{$this->prefix}product` p
                    WHERE sd.id_head='$id' AND sd.id_product=p.id_product AND `date` >= '$sdate' AND `date` <= '$edate'
                    UNION ALL
                       SELECT sd.id_product, sd.qty, sd.free, sd.rate, sd.tax_per, sd.cess, sd.net_amount, s.bill_no AS myno, p.name 
			FROM `{$this->prefix}purchasedetail` sd, `{$this->prefix}purchase` s, `{$this->prefix}product` p
                    WHERE sd.id_purchase=s.id_purchase AND sd.id_head='$id' AND sd.id_product=p.id_product AND s.`date` >= '$sdate' AND s.`date` <= '$edate'";

	            $sql_detail = "SELECT concat(sd.id_saledetail, '_', sd.id_product) as id_product, sd.qty, sd.free, sd.rate, sd.tax_per, sd.cess, sd.net_amount, invno AS myno, p.name FROM `{$this->prefix}saledetail` sd, `{$this->prefix}product` p
                        WHERE sd.id_head='$id' AND sd.id_product=p.id_product AND `date` >= '$sdate' AND `date` <= '$edate'
                    UNION ALL
                    SELECT concat(sd.id_purchasedetail, '_', sd.id_product) as id_product, sd.qty, sd.free, sd.rate, sd.tax_per, sd.cess, sd.net_amount, s.bill_no AS myno, p.name 
			            FROM `{$this->prefix}purchasedetail` sd, `{$this->prefix}purchase` s, `{$this->prefix}product` p
                        WHERE sd.id_purchase=s.id_purchase AND sd.id_head='$id' AND sd.id_product=p.id_product AND s.`date` >= '$sdate' AND s.`date` <= '$edate'";


                    $detail = $this->m->sql_getall($sql_detail, 1, "", "myno", "id_product");
                    $this->sm->assign("detail", $detail);
                    $sql = "SELECT 'H' AS type, '' AS id, '' AS refno, '' AS date, '' AS chead, '$id' AS dhead, SUM(IF(dhead='$id', 1, -1)*total) AS total, '' AS memo 
                            FROM `{$this->prefix}ledger` WHERE (dhead='$id' or chead='$id') AND `date`<'$sdate' GROUP BY 1 UNION ALL
                            SELECT * FROM `{$this->prefix}ledger` WHERE (dhead='$id' or chead='$id') AND `date`>='$sdate' AND `date`<='$edate' ORDER BY date";
                    //$sql = "SELECT 'H' AS type, '' AS id, '' AS refno, '' AS date, '' AS chead, '$id' AS dhead, SUM(IF(dhead='$id', 1, -1)*total) AS total, '' AS memo 
                    //        FROM `{$this->prefix}ledger` WHERE (dhead='$id' or chead='$id') AND `date`<'$sdate' GROUP BY 1 UNION ALL
                    //        SELECT type, id, refno, date, chead, dhead, SUM(total) AS total, memo  FROM `{$this->prefix}ledger` 
                    //        WHERE (dhead='$id' or chead='$id') AND `date`>='$sdate' AND `date`<='$edate' GROUP BY date, refno, memo, chead, dhead ORDER BY date";
		    if (($this->prefix=="DBF21__" || $this->prefix=="DBF22__") && $head['debtor']==1) {
		       $sql = "SELECT 'H' AS type, '' AS id, '' AS refno, '' AS date, '' AS chead, '$id' AS dhead, SUM(IF(dhead='$id', 1, -1)*total) AS total, '' AS memo 
		            FROM `{$this->prefix}ledger` WHERE (dhead='$id' or chead='$id') AND `date`<'$sdate' GROUP BY 1 UNION ALL
		            SELECT type, id, refno, date, chead, dhead, SUM(total) AS total, memo FROM `{$this->prefix}ledger` 
		            WHERE (dhead='$id' or chead='$id') AND `date`>='$sdate' AND `date`<='$edate' GROUP BY date, refno, chead, dhead ORDER BY date";
		    }
                    break;
                case 2:
                    $sql = "SELECT date, SUM(IF(dhead=$id,1,0)*total) AS debit, SUM(IF(dhead=$id,0,1)*total) AS credit, SUM(IF(dhead=$id,-1,1)*total) AS total FROM `{$this->prefix}ledger` WHERE dhead=$id or chead=$id GROUP BY 1 ORDER BY 1";
                    break;
                case 3:
                    $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(IF(dhead=$id,1,0)*total) AS debit, SUM(IF(dhead=$id,0,1)*total) AS credit, SUM(IF(dhead=$id,-1,1)*total) AS total FROM `{$this->prefix}ledger` WHERE dhead=$id or chead=$id GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY date";
                    break;
            }
            $res = $this->m->sql_getall($sql);
        }
        $this->sm->assign("data", $res);
		return $res;
    }

    function gledger() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '0';
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
        if ($id) {
            $hcond = $option ? '' : ' HAVING closing<>0 ';
            $sql = "SELECT h.id_head, h.name, h.address1, SUM(l.opening) AS opening, SUM(l.debit) AS debit, SUM(l.credit) AS credit, SUM(l.cbal) AS closing
              FROM `{$this->prefix}head` h, (
              SELECT dhead AS id_head, SUM(IF(date<'$sdate', -1, 0)*total) AS opening, SUM(IF(date>='$sdate' AND date<='$edate', 1, 0)*total) AS debit, 
                0.00 AS credit, SUM(IF(date<='$edate', -1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$edate' GROUP BY 1
              UNION ALL 
              SELECT chead AS id_head, SUM(IF(date<'$sdate', 1, 0)*total) AS opening, 0.00 AS debit, SUM(IF(date>='$sdate' AND date<='$edate', 1, 0)*total) AS credit, 
                SUM(IF(date<='$edate', 1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$edate' GROUP BY 1) l
              WHERE l.id_head=h.id_head AND h.id_group = {$id}
              GROUP BY h.id_head {$hcond} ORDER BY h.name";
        } else {
            $sql = "SELECT g.id_group, g.name, '' AS address, SUM(t.opening) AS opening, SUM(t.debit) AS debit, SUM(t.credit) AS credit, SUM(t.closing) AS closing
              FROM `{$this->prefix}group` g,
                (SELECT h.id_head, id_group, SUM(l.opening) AS opening, SUM(l.debit) AS debit, SUM(l.credit) AS credit, SUM(l.cbal) AS closing
              FROM `{$this->prefix}head` h, (
              SELECT dhead AS id_head, SUM(IF(date<'$sdate', -1, 0)*total) AS opening, SUM(IF(date>='$sdate' AND date<='$edate', 1, 0)*total) AS debit, 
                0.00 AS credit, SUM(IF(date<='$edate', -1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$edate' GROUP BY 1
              UNION ALL 
              SELECT chead AS id_head, SUM(IF(date<'$sdate', 1, 0)*total) AS opening, 0.00 AS debit, SUM(IF(date>='$sdate' AND date<='$edate', 1, 0)*total) AS credit, 
                SUM(IF(date<='$edate', 1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$edate' GROUP BY 1) l
              WHERE l.id_head=h.id_head
              GROUP BY h.id_head) t WHERE g.id_group=t.id_group GROUP BY g.id_group ORDER BY g.name";
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("group", $this->m->sql_getall("SELECT id_group AS id, name FROM {$this->prefix}group ORDER BY name", 2, "name", "id"));
        $this->sm->assign("data", $res);
    }

    function trial() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '2';
        $startdate = $_SESSION['start_date'];
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y"));
        $wcond = " date<='$sdate' ";
        if (isset($_REQUEST['opening'])) {
            $wcond .= " AND type = 'H' ";
        }
        $transact = isset($_REQUEST['transact']) ? 1 : 0;
        $ocond = ($_REQUEST['option'] == 1) ? " h.name, h.address1 " : " g.name, h.name, h.address1 ";
        if ($transact != 1) {
            $sql = "SELECT g.name AS gname, h.*, t.id_head, SUM(t.debit) AS debit, SUM(t.credit) AS credit 
           FROM (SELECT dhead AS id_head, ROUND(SUM(total),2) AS debit, 0 AS credit  FROM `{$this->prefix}ledger` WHERE $wcond GROUP BY 1
           UNION ALL 
           SELECT chead AS id_head, 0 AS debit, ROUND(SUM(total),2) AS credit FROM `{$this->prefix}ledger` WHERE $wcond GROUP BY 1
           ) t, `{$this->prefix}head` h, `{$this->prefix}group` g
            WHERE h.id_head=t.id_head AND h.id_group=g.id_group GROUP BY h.id_head ORDER BY $ocond";
        } else {
            $sql = "SELECT g.name AS gname,h.id_head, h.name, h.address1, SUM(l.opening) AS opening, SUM(l.debit) AS debit, SUM(l.credit) AS credit, SUM(l.cbal) AS closing
              FROM `{$this->prefix}group` g, `{$this->prefix}head` h, (
              SELECT dhead AS id_head, SUM(IF(date<'$startdate', -1, 0)*total) AS opening, SUM(IF(date>='$startdate' AND date<='$sdate', 1, 0)*total) AS debit, 
                0.00 AS credit, SUM(IF(date<='$sdate', -1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$sdate' GROUP BY 1
              UNION ALL 
              SELECT chead AS id_head, SUM(IF(date<'$startdate', 1, 0)*total) AS opening, 0.00 AS debit, SUM(IF(date>='$startdate' AND date<='$sdate', 1, 0)*total) AS credit, 
                SUM(IF(date<='$sdate', 1, 0)*total) AS cbal FROM `{$this->prefix}ledger` WHERE date<='$sdate' GROUP BY 1) l
              WHERE l.id_head=h.id_head AND h.id_group=g.id_group GROUP BY h.id_head ORDER BY $ocond";
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function repparty() {
        $startdate = $_SESSION['start_date'];
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y"));
        $wcond = " date<='$sdate' ";
        $ocond = ($_REQUEST['option'] == 1) ? " h.name, h.address1 " : " g.name ";
        $sql = "SELECT r.name AS rname, h.*, t.id_head, SUM(t.debit) AS debit, SUM(t.credit) AS credit, SUM(t.debit-t.credit) AS balance
           FROM (SELECT dhead AS id_head, ROUND(SUM(total),2) AS debit, 0 AS credit  FROM `{$this->prefix}ledger` WHERE $wcond GROUP BY 1
           UNION ALL 
           SELECT chead AS id_head, 0 AS debit, ROUND(SUM(total),2) AS credit FROM `{$this->prefix}ledger` WHERE $wcond GROUP BY 1
           ) t, `{$this->prefix}head` h, `{$this->prefix}area` a, `{$this->prefix}represent` r
            WHERE h.id_head=t.id_head AND h.debtor AND h.id_area=a.id_area AND a.id_represent=r.id_represent GROUP BY h.id_head  HAVING balance<>0 ORDER BY r.name, h.name, h.address1";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function ooutstanding() {
        $sql = "SELECT id_sale, SUM(amt) AS amt FROM {$this->prefix}voucher_details GROUP BY 1";
        $billpayments = $this->m->sql_getall($sql, 2, "amt", "id_sale");
        foreach ($billpayments as $k => $v) {
            $sql = "UPDATE {$this->prefix}sale SET pending=total-$v WHERE id_sale={$k}";
            $this->m->query($sql);
        }

		$opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
		$this->sm->assign("area", $this->m->sql_getall($opt2, 2, "name", "id"));
		$opt4 = "SELECT id_head AS id,name FROM {$this->prefix}head ORDER BY name";
		$this->sm->assign("party", $this->m->sql_getall($opt4, 2, "name", "id"));
		$opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
		$this->sm->assign("represent", $this->m->sql_getall($opt6, 2, "name", "id"));

        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['order'] = isset($_REQUEST['order']) ? $_REQUEST['order'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head, p.total FROM `{$this->prefix}head` h, (SELECT id_head, SUM(t.total1) AS total FROM (
              SELECT dhead AS id_head, SUM(-total) AS total1 FROM `{$this->prefix}ledger` WHERE date<='$edate' AND !(type='S' OR type='H') GROUP BY 1
              UNION ALL
              SELECT chead AS id_head, SUM(total) AS total1 FROM `{$this->prefix}ledger` WHERE date<='$edate' AND !(type='S' OR type='H') GROUP BY 1) t
              GROUP BY 1) p WHERE h.id_head=p.id_head AND h.debtor AND p.total<>0
              GROUP BY h.id_head  ORDER BY h.id_head ";
        $res = $this->m->sql_getall($sql, 2, "total", "id_head");
        switch ($_REQUEST['order']) {
            case 1: // namewise
                $ocond = " h.name, s.date, s.invno ";
                break;
            case 2: // areawise
                $ocond = " a.name, h.name, s.date, s.invno ";
                break;
            case 3: // Representative
                $ocond = " r.name, h.name, s.date, s.invno ";
                break;
            case 4: // Companywise
                $ocond = " c.name, a.name, h.name, s.date, s.invno ";
        }
        $sql = "SELECT s.id_represent, a.name AS aname, s.id_company, r.name AS rname, c.name AS cname, h.id_area, s.invno, s.date, s.id_head, s.total, s.total AS balance, h.name, h.address1
            FROM `{$this->prefix}sale` s 
                LEFT JOIN `{$this->prefix}represent` r ON s.id_represent=r.id_represent 
                LEFT JOIN `{$this->prefix}company` c ON s.id_company=c.id_company, 
		  `{$this->prefix}head` h, `{$this->prefix}area` a
		WHERE s.id_head=h.id_head AND h.id_aread=a.id_area AND s.cash=1 ORDER BY $ocond ";
        $rs = $this->m->query($sql);
        $dt = date('m/d/Y', time());
        while ($row = mysql_fetch_assoc($rs)) {
            $hid = $row['id_head'];
            $res[$hid] = isset($res[$hid]) ? $res[$hid] : 0;
            $row['balance'] = max(0, $row['balance'] - $res[$hid]);
            $res[$hid] = max(0, $res[$hid] - $row['total']);
            $diff = strtotime($dt) - strtotime($row['date']);
            $days = (int) ($diff / 60 / 60 / 24) + 1;
            $row['days'] = $days;
            if ($row['balance'] != 0)
                $result[] = $row;
        }
        if ($_REQUEST['option']==2) {
            foreach ($result as $k => $v) {
                $hid = $v['id_head'];
                $os[$hid]['aname'] =  isset($os[$hid]['aname']) ? $os[$hid]['aname'] : $v['aname'];
                $os[$hid]['cname'] =  isset($os[$hid]['cname']) ? $os[$hid]['cname'] : $v['cname'];
                $os[$hid]['name'] =  isset($os[$hid]['name']) ? $os[$hid]['name'] : $v['name'];
                $os[$hid]['address1'] =  isset($os[$hid]['address1']) ? $os[$hid]['address1'] : $v['address1'];
                $os[$hid]['totalos'] =  (isset($os[$hid]['totalos']) ? $os[$hid]['totalos'] : 0) + $v['balance'];
            }
            $this->sm->assign("data", $os);
        } else {
$sql = "DROP TABLE if exists os;CREATE TABLE os (id_represent INT, id_company INT, id_area INT, invno VARCHAR(20), `data` date, id_head INT, total decimal(16,2));";
//echo $sql;
            foreach ($result as $k => $v) {
                $hid = $v['id_head'];
                $os[$hid] = isset($os[$hid]) ? $os[$hid] : 0;
                $os[$hid] +=  $v['balance'];
$sql = "INSERT INTO os VALUES ({$v['id_represent']}, {$v['id_company']}, {$v['id_area']}, '{$v['invno']}', '{$v['date']}', {$v['id_head']}, {$v['total']});"; 
//echo $sql."<br>";

            }
            $this->sm->assign("os", $os);
            $this->sm->assign("data", $result);
        }
    }

    function outstanding() {
	$opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
	$this->sm->assign("area", $this->m->sql_getall($opt2, 2, "name", "id"));
	$opt4 = "SELECT id_head AS id,name FROM {$this->prefix}head ORDER BY name";
	$this->sm->assign("party", $this->m->sql_getall($opt4, 2, "name", "id"));
	$opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
	$this->sm->assign("represent", $this->m->sql_getall($opt6, 2, "name", "id"));
        $company = $this->m->sql_getall("SELECT id_company,name FROM {$this->prefix}company WHERE status=0 ORDER BY name", 2, "name", "id_company");
        $this->sm->assign("company", $company);

        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['order'] = isset($_REQUEST['order']) ? $_REQUEST['order'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head, p.total FROM `{$this->prefix}head` h, (SELECT id_head, SUM(t.total1) AS total FROM (
              SELECT dhead AS id_head, SUM(-total) AS total1 FROM `{$this->prefix}ledger` WHERE date<='$edate' AND !(type='S' OR type='H') GROUP BY 1
              UNION ALL
              SELECT chead AS id_head, SUM(total) AS total1 FROM `{$this->prefix}ledger` WHERE date<='$edate' AND !(type='S' OR type='H') GROUP BY 1) t
              GROUP BY 1) p WHERE h.id_head=p.id_head AND h.debtor AND p.total<>0
              GROUP BY h.id_head  ORDER BY h.id_head ";
        $res = $this->m->sql_getall($sql, 2, "total", "id_head");
	
	$sql = "SELECT id_head_credit, SUM(amt) AS amt FROM {$this->prefix}voucher_details GROUP BY id_head_credit ORDER BY id_head_credit";
	$rs = $this->m->query($sql);
    	while ($row = mysql_fetch_assoc($rs)) {
            $res[$row['id_head_credit']] = $res[$row['id_head_credit']] - $row['amt'];
        }
        if ($this->prefix=="DBF21__" || $this->prefix=="DBF22__") {
            unset($res);
        }
        $ocond = "  ";
        $wcond = " ";
        if (@$_REQUEST['area']) {
            $wcond .= " a.id_area = {$_REQUEST['area']} AND ";
        }
        if (@$_REQUEST['represent']) {
            $wcond .= " s.id_represent = {$_REQUEST['represent']} AND ";
        }
        if (@$_REQUEST['party']) {
            $wcond .= " s.id_head = {$_REQUEST['party']} AND ";
        }
        if (@$_REQUEST['company']) {
            $wcond .= " s.id_company = {$_REQUEST['company']} AND ";
        }
        /*$sql = "SELECT s.id_represent, a.name AS aname, s.id_company, r.name AS rname, c.name AS cname, a.id_area, s.invno, s.date, s.id_head,
		 s.pending AS total, s.pending AS balance, h.name, h.address1
	        FROM `{$this->prefix}sale` s 
                LEFT JOIN `{$this->prefix}represent` r ON s.id_represent=r.id_represent
                LEFT JOIN `{$this->prefix}area` a ON s.id_area=a.id_area
                LEFT JOIN `{$this->prefix}company` c ON s.id_company=c.id_company, `{$this->prefix}head` h 
                WHERE $wcond s.id_head=h.id_head AND s.cash=1 ORDER BY h.name, s.date, s.invno";*/
	$sql = "SELECT s.id_represent, a.name AS aname, s.id_company, r.name AS rname, c.name AS cname, a.id_area, s.invno, s.date, s.id_head,
	   	 s.pending AS total, s.pending AS balance, h.name, h.address1
	 	FROM `{$this->prefix}sale` s 
	    	LEFT JOIN `{$this->prefix}represent` r ON s.id_represent=r.id_represent
	    	LEFT JOIN `{$this->prefix}company` c ON s.id_company=c.id_company, `{$this->prefix}head` h, `{$this->prefix}area` a 
	    	WHERE $wcond s.id_head=h.id_head AND h.id_area=a.id_area AND s.cash=1 ORDER BY h.name, s.date, s.invno";

        $rs = $this->m->query($sql);
        $dt = date('m/d/Y', time());
        $min_date = $max_date = 0;;
        if (isset($_REQUEST['days']) && $_REQUEST['days'] > 0) {
            if ($_REQUEST['daystype']=="2") {
                $min_date = $_REQUEST['days']; 
                $max_date = 999999999;
            } else {
                $min_date = -999999999;
                $max_date = $_REQUEST['days'];
            }

        }
        $os = $result = array();
	while ($row = mysql_fetch_assoc($rs)) {
            $hid = $row['id_head'];
            $res[$hid] = isset($res[$hid]) ? $res[$hid] : 0;
            $row['balance'] = max(0, $row['balance'] - $res[$hid]);
            $res[$hid] = max(0, $res[$hid] - $row['total']);
            $diff = strtotime($dt) - strtotime($row['date']);
            $days = (int) ($diff / 60 / 60 / 24) + 1;
            $row['days'] = $days;
	    if ($row['balance'] != 0) {
               if (($days>=$min_date && $days<=$max_date) || $min_date==0)
                    $result[] = $row;
            }
        }
        if ($_REQUEST['option']==2) {
            foreach ($result as $k => $v) {
                $hid = $v['id_head'];
                $os[$hid]['aname'] =  isset($os[$hid]['aname']) ? $os[$hid]['aname'] : $v['aname'];
                $os[$hid]['cname'] =  isset($os[$hid]['cname']) ? $os[$hid]['cname'] : $v['cname'];
                $os[$hid]['name'] =  isset($os[$hid]['name']) ? $os[$hid]['name'] : $v['name'];
                $os[$hid]['address1'] =  isset($os[$hid]['address1']) ? $os[$hid]['address1'] : $v['address1'];
                $os[$hid]['totalos'] =  (isset($os[$hid]['totalos']) ? $os[$hid]['totalos'] : 0) + $v['balance'];
            }
            $this->sm->assign("data", $os);
        } else {
            $sql = "DROP TABLE if exists os;CREATE TABLE os (id_represent INT, id_company INT, id_area INT, invno VARCHAR(20), `data` date, id_head INT, total decimal(16,2), cash INT, pending decimal(16,2));";
            //echo $sql;
            foreach ($result as $k => $v) {
                $hid = $v['id_head'];
                $os[$hid] = isset($os[$hid]) ? $os[$hid] : 0;
                $os[$hid] +=  $v['balance'];
                $sql = "INSERT INTO os VALUES ({$v['id_represent']}, {$v['id_company']}, {$v['id_area']}, '{$v['invno']}', '{$v['date']}', {$v['id_head']}, {$v['total']}, 1, {$v['total']});"; 
                //echo $sql."<br>";
            }
            $this->sm->assign("os", $os);
            $this->sm->assign("data", $result);
        }
        $this->sm->assign("url", "index.php?module=accounts&func=outstanding");
    }


    function collection() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head, h.name FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.name='cash' AND p.content=h.code";
        $cash = $this->m->sql_getall($sql, 2, "name", "id_head");
        $sql = "SELECT h.* FROM {$this->prefix}head h, {$this->prefix}group g WHERE h.id_group=g.id_group AND g.name='BANK ACCOUNTS' ORDER BY 1";
        $data = $this->m->sql_getall($sql, 2, "name", "id_head");
        $acs = $cash + $data;
        $ac = implode(",", array_keys($acs));
        $wcond = " (d.id_group<27 AND d.id_group!=15) ";
        $wcond = " v.dhead IN ($ac) ";
        switch ($_REQUEST['option']) {
        case 1:
            $sql = "SELECT v.date, v.refno, v.chead, concat(h.name,' ',h.address1) AS cname, v.dhead, concat(d.name,' ',d.address1) AS dname, v.total, v.memo 
            FROM `{$this->prefix}ledger` v, `{$this->prefix}head` h, `{$this->prefix}head` d 
            WHERE v.`type`='V' AND v.chead=h.id_head AND v.dhead=d.id_head AND (v.date >= '$sdate' AND v.date <= '$edate') AND h.debtor AND $wcond
            ORDER BY date";
            break;
        case 2:
            $sql = "SELECT v.chead, concat(h.name,' ',h.address1) AS cname, v.dhead, concat(d.name,' ',d.address1) AS dname, SUM(v.total) AS total 
            FROM `{$this->prefix}ledger` v, `{$this->prefix}head` h, `{$this->prefix}head` d 
            WHERE v.`type`='V' AND v.chead=h.id_head AND v.dhead=d.id_head AND (v.date >= '$sdate' AND v.date <= '$edate') AND h.debtor AND $wcond
            GROUP BY 3, 1 ORDER BY 2, 4";
            break;
        case 3:
            $sql = "SELECT r.name, v.date, v.refno, v.chead, concat(h.name,' ',h.address1) AS cname, v.dhead, concat(d.name,' ',d.address1) AS dname, v.total, v.memo  
            FROM `{$this->prefix}ledger` v, `{$this->prefix}head` h, `{$this->prefix}head` d, `{$this->prefix}area` a,`{$this->prefix}represent` r
            WHERE v.`type`='V' AND v.chead=h.id_head AND v.dhead=d.id_head AND (v.date >= '$sdate' AND v.date <= '$edate') AND h.debtor AND $wcond
                AND a.id_represent=r.id_represent AND h.id_area=a.id_area
            ORDER BY r.name, cname, date";
            break;
        case 4:
            $sql = "SELECT r.name, SUM(v.total) AS total  
            FROM `{$this->prefix}ledger` v, `{$this->prefix}head` h, `{$this->prefix}head` d, `{$this->prefix}area` a, `{$this->prefix}represent` r
            WHERE v.`type`='V' AND v.chead=h.id_head AND v.dhead=d.id_head AND (v.date >= '$sdate' AND v.date <= '$edate') AND h.debtor AND $wcond
                AND a.id_represent=r.id_represent AND h.id_area=a.id_area
            GROUP BY r.name ORDER BY r.name";
            break;
        }
	$id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $sql = "SELECT v.date, v.billno AS refno, v.id_head_credit AS chead, concat(h.name,' ',h.address1) AS cname, 
                v.id_head_debit AS dhead, concat(d.name,' ',d.address1) AS dname, v.amt AS total, v.memo
            FROM `{$this->prefix}voucher_details` v, `{$this->prefix}head` h, `{$this->prefix}head` d, `{$this->prefix}sale` s 
            WHERE v.id_sale=s.id_sale AND s.id_company='$id_company' AND v.id_head_credit=h.id_head AND v.id_head_debit=d.id_head 
                AND (v.date >= '$sdate' AND v.date <= '$edate')
            ORDER BY date";
            $_REQUEST['option'] = 1;
        }
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);

        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function expenses() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT v.date, v.refno, v.chead, concat(h.name,' ',h.address1) AS cname, v.dhead, concat(d.name,' ',d.address1) AS dname, v.total, v.memo  FROM 
                `{$this->prefix}ledger` v, `{$this->prefix}head` h, `{$this->prefix}head` d 
                WHERE v.`type`='V' AND v.chead=h.id_head AND v.dhead=d.id_head AND 
                      (v.date >= '$sdate' AND v.date <= '$edate') AND !(h.debtor AND  d.id_group<27)
                ORDER BY date";
                break;
            case 2:
                $sql = "SELECT v.chead, concat(h.name,' ',h.address1) AS cname, 
                    v.dhead, concat(d.name,' ',d.address1) AS dname, SUM(v.total) AS total FROM 
                `{$this->prefix}ledger` v, `{$this->prefix}head` h, `{$this->prefix}head` d 
                WHERE v.`type`='V' AND v.chead=h.id_head AND v.dhead=d.id_head AND 
                      (v.date >= '$sdate' AND v.date <= '$edate') AND !(h.debtor AND d.id_group<27)
                GROUP BY 3, 1 ORDER BY 2, 4";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function receipt() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['start_date'] = $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $_REQUEST['end_date'] = $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $party = $this->m->sql_getall("SELECT id_head AS id,name FROM {$this->prefix}head ORDER BY name", 2, "name", "id");
        $this->sm->assign("party", $party);
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT SUM(total) AS total FROM {$this->prefix}ledger WHERE (chead=0 or dhead=0) AND type='H'";
                $sql1 = "SELECT dhead AS chead, h.name AS cname, SUM(total) AS ctotal FROM {$this->prefix}ledger l, {$this->prefix}head h
                        WHERE l.dhead=h.id_head AND (date >= '$sdate' AND date <= '$edate') AND type!='H' GROUP BY dhead ORDER BY h.name";
                $sql2 = "SELECT chead AS dhead, h.name AS dname, SUM(total) AS dtotal FROM {$this->prefix}ledger l, {$this->prefix}head h
                        WHERE l.chead=h.id_head AND (date >= '$sdate' AND date <= '$edate') AND type!='H' GROUP BY chead ORDER BY h.name";
                break;
            case 2:
                $party = $_REQUEST['party2'][0];
                $sql = "SELECT SUM(IF(dhead=$party, 1, -1)*total) AS total FROM {$this->prefix}ledger WHERE (chead=$party or dhead=$party) AND `date`<'$sdate'";
                $sql1 = "SELECT dhead AS chead, h.name AS cname, SUM(total) AS ctotal FROM {$this->prefix}ledger l, {$this->prefix}head h
                        WHERE l.dhead=h.id_head AND l.chead=$party AND (date >= '$sdate' AND date <= '$edate') AND type!='H' GROUP BY dhead ORDER BY h.name";
                $sql2 = "SELECT chead AS dhead, h.name AS dname, SUM(total) AS dtotal FROM {$this->prefix}ledger l, {$this->prefix}head h
                        WHERE l.chead=h.id_head AND l.dhead=$party AND (date >= '$sdate' AND date <= '$edate') AND type!='H' GROUP BY chead ORDER BY h.name";
                break;
        }
        $opening = $this->m->sql_getall($sql);
        $data = $this->m->sql_getall($sql1);
        if ($opening[0]['total'] > 0) {
            $op = array("dhead" => 0, "dname" => "Opening Balance", "dtotal" => $opening[0]['total']);
        } else {
            $op = array("chead" => 0, "cname" => "Opening Balance", "ctotal" => -$opening[0]['total']);
        }
        array_unshift($data, $op);
        $credit = $this->m->sql_getall($sql2);
        foreach ($credit as $k => $v) {
            $data[$k + 1]['dhead'] = $v['dhead'];
            $data[$k + 1]['dname'] = $v['dname'];
            $data[$k + 1]['dtotal'] = $v['dtotal'];
        }
        unset($credit);
        $this->sm->assign("data", $data);
    }

    function receipt_spl() {
        $_REQUEST['start_date'] = $edate = $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y"));
//        $acs = array(3=>"Cash", 56=>"SBI", 92=>"PNB", 93=>"PNB CC", 240=>"HDFC");
//        $ac = '3, 56, 92, 93, 240';
        $sql = "SELECT h.id_head, h.name FROM `{$this->prefix}param` p, `{$this->prefix}head` h WHERE p.name='cash' AND p.content=h.code";
        $cash = $this->m->sql_getall($sql, 2, "name", "id_head");
        $sql = "SELECT h.id_head, h.name FROM `{$this->prefix}head` h, `{$this->prefix}group` g WHERE h.id_group=g.id_group AND g.name='BANK ACCOUNTS' ORDER BY h.id_head";
        $data = $this->m->sql_getall($sql, 2, "name", "id_head");
        $acs = $cash + $data;
        $ac = implode(",", array_keys($acs));
        $sql = "SELECT o.id_head, SUM(o.total) AS total FROM (SELECT dhead AS id_head, ROUND(SUM(total),2) AS total FROM {$this->prefix}ledger WHERE dhead IN ($ac) AND `date`<'$sdate' GROUP BY dhead
                UNION ALL
                SELECT chead AS id_head, ROUND(SUM(-total),2) AS total FROM {$this->prefix}ledger WHERE chead IN ($ac) AND `date`<'$sdate' GROUP BY chead) o GROUP BY o.id_head";
        $opening = $this->m->sql_getall($sql);
        $data = array();
        foreach ($acs as $k => $v) {
            $one = $this->oneac($k, $sdate, $edate);
            $data = array_merge($data, $one);
        }
        $this->sm->assign("acs", $acs);
        $this->sm->assign("opening", $opening);
        $this->sm->assign("data", $data);
    }

    function oneac($ac, $sdate, $edate) {
        $sql = "SELECT $ac AS id_head, h.name, concat(h.address2, ' ', IFNULL(address3, '')) as address, l.* FROM {$this->prefix}ledger l, {$this->prefix}head  h 
            WHERE `date`>='$sdate' AND `date`<='$edate' AND (dhead='$ac' OR chead='$ac') AND h.id_head=IF(dhead='$ac', chead, dhead) ORDER BY date";
        $data = $this->m->sql_getall($sql);
        return $data;
    }

    function confirmation() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $printdate = $this->format_date(isset($_REQUEST['printdate']) ? $_REQUEST['printdate'] : date("d/m/Y"));
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
		if ($id) {
			$_REQUEST['option'] = 1;
			$result = $this->fetchdata($id, $sdate, $edate);
			$sql = "SELECT id_head, concat(name,' ',address1) AS name FROM {$this->prefix}head ORDER BY name";
			$head = $this->m->sql_getall($sql, 2, "name", "id_head");
			$this->sm->assign("head", $head);
			$db = $cr = 0;
			foreach ($result as $k => $v) {
				$d = date_format(date_create($v['date']),"d-m-Y");
				$r = $v['refno']." ".$v['memo'];
				$t = abs($v['total']);
				if ($v['date'])
					$p = ($v['id']==$v['dhead']) ? $head[$v['chead']] : $head[$v['dhead']];
				else 
					$p = "Opening Balance";
				if ($id != $v['chead'] AND $v['total']>0) {
					$all[$db]['dd'] = $d;
					$all[$db]['dr'] = $r;
					$all[$db]['dp'] = $p;
					$all[$db++]['dt'] = $t;
				} else {
					$all[$cr]['cd'] = $d;
					$all[$cr]['cr'] = $r;
					$all[$cr]['cp'] = $p;
					$all[$cr++]['ct'] = $t;
				}
			}
			$this->sm->assign("data", $all);
		}
	}
    function billdetail() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $printdate = $this->format_date(isset($_REQUEST['printdate']) ? $_REQUEST['printdate'] : date("d/m/Y"));
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
        $sql = "SELECT * FROM {$this->prefix}head WHERE id_head='$id'";
        $this->sm->assign("head1", $this->m->sql_getall($sql));
	if (is_numeric($id)) {
	    $_REQUEST['option'] = 1;
	    $sql = "SELECT s.*, c.name, r.name AS rname FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}company c ON s.id_company=c.id_company  LEFT JOIN {$this->prefix}represent r ON s.id_represent=r.id_represent
            WHERE id_head='$id' AND cash=1 ORDER BY date";
            $this->sm->assign("data", $this->m->sql_getall($sql));

            $sql = "SELECT id_voucher_details, id_voucher, id_sale, date, billno, amt FROM {$this->prefix}voucher_details WHERE id_head_debit='$id' OR id_head_credit='$id' ORDER BY id_voucher";
            $bdet = $this->m->sql_getall($sql, 1, "", "id_sale", "id_voucher_details");
	    $this->sm->assign("billdetail", $bdet);
	}
    }

    function orfan_voucher_details() {
        ini_set('display_errors', 'On');
        $sql = "select v.id_voucher_details AS id FROM {$this->prefix}voucher_details v LEFT JOIN {$this->prefix}sale s
            ON v.id_sale=s.id_sale where v.id_head_credit!=s.id_head order by id_head_credit";
        $data = $this->m->sql_getall($sql);
        foreach ($data as $k => $v) {
            $sql1 = "DELETE FROM {$this->prefix}voucher_details WHERE id_voucher_details='{$v['id']}'";
            $this->m->query($sql1);
            echo $sql1."<br>";
        }
        exit;
    }

    function osVSledger() {
        $sql = "SELECT t.id_head, SUM(-t.balance) AS balance 
        FROM (SELECT dhead AS id_head, ROUND(SUM(-total),2) AS balance FROM `{$this->prefix}ledger` GROUP BY 1
        UNION ALL 
        SELECT chead AS id_head, ROUND(SUM(total),2) AS balance FROM `{$this->prefix}ledger` GROUP BY 1
        ) t GROUP BY id_head";
        $ledger = $this->m->sql_getall($sql, 2, "balance", "id_head");
//$this->pr($ledger);
        $sql = "SELECT t.id_head, SUM(-t.balance) AS balance 
        FROM (SELECT id_head, SUM(-total) AS balance FROM `{$this->prefix}sale` WHERE cash=1 GROUP BY 1
        UNION ALL 
        SELECT dhead AS id_head, ROUND(SUM(-total),2) AS balance FROM `{$this->prefix}ledger` WHERE !(type='H' OR type='S') GROUP BY 1
        UNION ALL 
        SELECT chead AS id_head, ROUND(SUM(total),2) AS balance FROM `{$this->prefix}ledger` WHERE !(type='H' OR type='S') GROUP BY 1
        ) t GROUP BY id_head";
        $os = $this->m->sql_getall($sql, 2, "balance", "id_head");
//$this->pr($os);
        $sql = "SELECT id_head, SUM(pending) AS balance FROM `{$this->prefix}sale` WHERE cash=1 GROUP BY id_head";
        $billos = $this->m->sql_getall($sql, 2, "balance", "id_head");
        $this->sm->assign("billos", $billos);

        $sql = "SELECT id_head, name, address1, address2, opening_balance, otype FROM `{$this->prefix}head` WHERE debtor=1 ORDER BY name";
        $party = $this->m->sql_getall($sql);

        $sdate = $_SESSION['start_date'];
        $sql = "SELECT id_head, SUM(total) AS osbill FROM `{$this->prefix}sale` WHERE cash=1 AND date<'$sdate' GROUP BY id_head";
        $partybill = $this->m->sql_getall($sql, 2, "osbill", "id_head");

        $this->sm->assign("partybill", $partybill);
        $this->sm->assign("party", $party);
        $this->sm->assign("ledger", $ledger);
        $this->sm->assign("os", $os);
    }

    function outstandingc()  {
        $opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $this->sm->assign("area", $this->m->sql_getall($opt2, 2, "name", "id"));
        $opt4 = "SELECT id_head AS id,name FROM {$this->prefix}head WHERE creditor ORDER BY name";
        $this->sm->assign("party", $this->m->sql_getall($opt4, 2, "name", "id"));
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $this->sm->assign("represent", $this->m->sql_getall($opt6, 2, "name", "id"));

        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['order'] = isset($_REQUEST['order']) ? $_REQUEST['order'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT h.id_head, SUM(p.total) AS total  FROM `{$this->prefix}head` h, (SELECT id_head, SUM(t.total1) AS total FROM (
                SELECT dhead AS id_head, SUM(total) AS total1 FROM `{$this->prefix}ledger` WHERE date<='$edate' AND !(type='P') GROUP BY 1
                UNION ALL
                SELECT chead AS id_head, SUM(-total) AS total1 FROM `{$this->prefix}ledger` WHERE date<='$edate' AND !(type='P') GROUP BY 1) t
                GROUP BY 1) p WHERE h.id_head=p.id_head AND h.creditor
                GROUP BY h.id_head  ORDER BY h.id_head ";
        $res = $this->m->sql_getall($sql, 2, "total", "id_head");
        $wcond = " ";
        if (@$_REQUEST['area']) {
            $wcond .= " s.id_area = {$_REQUEST['area']} AND ";
        }
        if (@$_REQUEST['represent']) {
            $wcond .= " s.id_represent = {$_REQUEST['represent']} AND ";
        }
	$wcondp='';
        if (@$_REQUEST['party']) {
            $wcond .= " s.id_head = {$_REQUEST['party']} AND ";
            $wcondp = " AND h.id_head = {$_REQUEST['party']} ";
        }

        $dt = date('m/d/Y', time());
        $min_date = $max_date = 0;;
        if (isset($_REQUEST['days']) && $_REQUEST['days'] > 0) {
            if ($_REQUEST['daystype'] == "2") {
                $min_date = $_REQUEST['days'];
                $max_date = 999999999;
            } else {
                $min_date = -999999999;
                $max_date = $_REQUEST['days'];
            }
        }
        $os = $result = array();
        $sql = "SELECT s.id_represent, a.name AS aname, s.id_company, r.name AS rname, c.name AS cname, h.id_area, s.bill_no AS invno, s.date, s.id_head,
             s.total AS total, s.total AS balance, h.name, h.address1
                FROM `{$this->prefix}purchase` s 
                    LEFT JOIN `{$this->prefix}represent` r ON s.id_represent=r.id_represent
                    LEFT JOIN `{$this->prefix}area` a ON s.id_area=a.id_area
                    LEFT JOIN `{$this->prefix}company` c ON s.id_company=c.id_company, `{$this->prefix}head` h 
                    WHERE $wcond s.id_head=h.id_head AND s.cash=1 ORDER BY h.name, s.date, s.no";
	$sdate = $_SESSION['sdate'];
        $sql1 = "SELECT h.name, '' AS date, r.id_represent, a.name AS aname, 0 AS id_company, r.name AS rname, '' AS cname, h.id_area, 'OPENING' AS invno, h.id_head,
                        h.opening_balance AS total, h.opening_balance AS balance, h.address1
                    FROM `{$this->prefix}head` h, `{$this->prefix}area` a LEFT JOIN `{$this->prefix}represent` r ON a.id_represent=r.id_represent
                    WHERE h.creditor AND h.id_area=a.id_area $wcondp 
                UNION ALL
                SELECT h.name, s.date, s.id_represent, a.name AS aname, s.id_company, r.name AS rname, c.name AS cname, h.id_area, s.bill_no AS invno, s.id_head,
                    s.total AS total, s.total AS balance, h.address1
                    FROM `{$this->prefix}purchase` s 
                    LEFT JOIN `{$this->prefix}represent` r ON s.id_represent=r.id_represent
                    LEFT JOIN `{$this->prefix}area` a ON s.id_area=a.id_area
                    LEFT JOIN `{$this->prefix}company` c ON s.id_company=c.id_company, `{$this->prefix}head` h 
                    WHERE $wcond s.date>='{$sdate}' AND s.id_head=h.id_head AND s.cash=1 ORDER BY 1,2";
        $rs = $this->m->sql_getall($sql);
        foreach ($rs as $row) {
            $hid = $row['id_head'];
            $res[$hid] = isset($res[$hid]) ? $res[$hid] : 0;
            //print("Party Id:".$hid." Payment:".$res[$hid]."  Bill Amount:".$row['total']."<br>");
            $row['balance'] = max(0, $row['balance'] - $res[$hid]);
            $res[$hid] = max(0, $res[$hid] - $row['total']);
            $diff = strtotime($dt) - strtotime($row['date']);
            $days = (int) ($diff / 60 / 60 / 24) + 1;
            $row['days'] = $days;
            if ($row['balance'] != 0) {
                if (($days >= $min_date && $days <= $max_date) || $min_date == 0)
                    $result[] = $row;
            }
        }
        if ($_REQUEST['option'] == 2) {
            foreach ($result as $k => $v) {
                $hid = $v['id_head'];
                $os[$hid]['aname'] =  isset($os[$hid]['aname']) ? $os[$hid]['aname'] : $v['aname'];
                $os[$hid]['cname'] =  isset($os[$hid]['cname']) ? $os[$hid]['cname'] : $v['cname'];
                $os[$hid]['name'] =  isset($os[$hid]['name']) ? $os[$hid]['name'] : $v['name'];
                $os[$hid]['address1'] =  isset($os[$hid]['address1']) ? $os[$hid]['address1'] : $v['address1'];
                $os[$hid]['totalos'] =  (isset($os[$hid]['totalos']) ? $os[$hid]['totalos'] : 0) + $v['balance'];
            }
            $this->sm->assign("data", $os);
        } else {
            foreach ($result as $k => $v) {
                $hid = $v['id_head'];
                $os[$hid] = isset($os[$hid]) ? $os[$hid] : 0;
                $os[$hid] +=  $v['balance'];
            }
            $this->sm->assign("os", $os);
            $this->sm->assign("data", $result);
        }
        $this->sm->assign("url", "index.php?module=accounts&func=outstandingc");
        $this->sm->assign("page", "accounts/outstanding.tpl.html");
    }
}
?>
