<?php

class prodreport extends common {

    function __construct() {
        $this->checklogin();
        $this->get_permission("production", "REPORT");
        $this->table_prefix();
        parent:: __construct();
    }

    function _default() {
        echo "This function is not enabled...";
    }

    function register() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT p.slno, p.date, p.reference_no, p.reference_date, p.shift, i.name, pd.id_batch, pd.qty, pd.free, pd.type, pd.batch_name
                FROM `{$this->prefix}production` p, `{$this->prefix}productiondetail` pd, `{$this->prefix}product` i
                WHERE p.date >= '$sdate' AND p.date <= '$edate' AND p.id_production=pd.id_production AND pd.id_product=i.id_product
                ORDER BY p.date, p.slno
                        ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function wastage() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '2';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $_REQUEST['all'] = isset($_REQUEST['all']) ? $_REQUEST['all'] : 'Wastage';
        switch ($_REQUEST['all']) {
            case 'Issue+Wastage':
                $wcond = " AND (pd.type='Issue' OR pd.type='Wastage') ";
                break;
            default:
                $wcond = " AND pd.type='{$_REQUEST['all']}' ";
        }
        switch ($_REQUEST['option']) {
        case 2:
            $sql = "SELECT DISTINCT pd.id_product FROM `{$this->prefix}productiondetail` pd WHERE pd.date>='$sdate' AND pd.date<='$edate' $wcond ";
            $products = $this->m->sql_getall($sql, 2, "id_product", "id_product");
            $pids = implode(",", $products);
            if ($pids) {
                $osrate = $this->getosrate($sdate, $pids);
                foreach($osrate as $v) {
                    $os[$v['id_product']] = @$os[$v['id_product']] ? $os[$v['id_product']] : $v['rate'];
                }
                $this->sm->assign("osrate", $os);
                $sql = "SELECT i.id_product, i.name, SUM(pd.qty) AS qty, SUM(pd.free) AS free
                    FROM `{$this->prefix}production` p, `{$this->prefix}productiondetail` pd, `{$this->prefix}product` i
                    WHERE p.date >= '$sdate' AND p.date <= '$edate' AND p.id_production=pd.id_production AND pd.id_product=i.id_product $wcond
                    GROUP BY pd.id_product ORDER BY i.name";
                $res = $this->m->sql_getall($sql);
            }
            break;
        default:
            $sql = "SELECT p.slno, p.date, p.reference_no, p.reference_date, p.shift, i.name, pd.id_batch, pd.qty, pd.free, pd.type, pd.batch_name
                FROM `{$this->prefix}production` p, `{$this->prefix}productiondetail` pd, `{$this->prefix}product` i
                WHERE p.date >= '$sdate' AND p.date <= '$edate' AND p.id_production=pd.id_production AND pd.id_product=i.id_product $wcond
                ORDER BY p.date, p.slno";
            $res = $this->m->sql_getall($sql);
        }
        $this->sm->assign("data", $res);
    }
    function getosrate($edate, $ids) {
        $sql = "SELECT pd.id_product, max(pd.date) AS date, SUM(pd.qty+pd.free) AS qty, pd.rate FROM {$this->prefix}product p, {$this->prefix}purchasedetail pd
                WHERE p.id_product=pd.id_product AND pd.date <= '$edate' AND pd.id_product IN ($ids) GROUP BY pd.id_product, pd.rate ORDER BY 1,2 DESC";
        $res = $this->m->sql_getall($sql);
        foreach($res as $k => $v) {
            $id = $v['id_product'];
            $na[$id][] = $v;
        }
        $sql = "SELECT l.id_product, SUM(l.qty+l.free) AS balance, p.purchase_price  FROM {$this->prefix}product p, {$this->prefix}product_ledger l
                WHERE p.id_product=l.id_product AND (l.date <= '$edate' OR l.date IS NULL) AND p.id_product IN ($ids)
		        GROUP BY p.id_product HAVING balance<>0 ORDER BY p.name ";
        $res = $this->m->sql_getall($sql);
        $newdata=array();
        foreach($res as $k => $v) {
            $id = $v['id_product'];
            $qty = $v['balance'];
            if (@$na[$id] && $qty>0) {
                foreach($na[$id] as $k1 => $v1) {
                    $v['balance'] = min($v1['qty'], $qty);
                    $qty = max($qty - $v1['qty'], 0);
                    $v['date'] = $v1['date'];
                    $v['rate'] = $v1['rate'];
                    $v['total'] = $v1['rate']*$v['balance'];
                    $newdata[] = $v;
                    if ($qty<=0) break;
                }
            }
            if ($qty!=0) {
                $v['balance'] = $qty;
                $v['date'] = "Open";
                $v['rate'] = $v['purchase_price'];
                $v['total'] = $v['purchase_price']*$v['balance'];
                $newdata[] = $v;
            }
        }
        return $newdata;
    }
}
?>
