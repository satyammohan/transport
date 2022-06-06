<?php

class stockstmt extends common {

    function __construct() {
        $this->get_permission("sales", "REPORT");
        $this->template = 'stockstmt';
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function statement() {
        $valuation = isset($_REQUEST['valuation']) ? $_REQUEST['valuation'] : 0;
	if ($valuation==0) {
		$this->oldstatement();
		return;
	}
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $company = $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name", 2, "name", "id");
        $this->sm->assign("company", $company);
        if ($_REQUEST['company']) {
            $id_company = trim(isset($_REQUEST['company']) ? implode(",", $_REQUEST['company']) : '0');
            $wcond = empty($id_company) ? "" : " p.id_company IN ($id_company) AND ";
	    $minstock = isset($_REQUEST['minstock']) ? ' , p.minimum_stock ' : '';
	    $price = ($valuation==1) ? 'seller_price' : 'purchase_price';
            $sql = "SELECT c.name AS cname, concat(p.id_product,'_',IF(b.id_batch IS NULL,0,b.id_batch) ) AS id, p.id_product AS id_product, 
                    IF(b.{$price}=0 OR b.{$price} IS NULL, p.{$price}, b.{$price}) AS price, p.name $minstock
                    FROM {$this->prefix}company c, {$this->prefix}product p, {$this->prefix}batch b
		WHERE $wcond c.id_company=p.id_company AND p.id_product=b.id_product AND c.status=0 AND p.status=0 ORDER BY cname, p.name ";
            $res = $this->m->sql_getall($sql, 1, "", "id");
            
	    $sql1 = "SELECT concat(id_product,'_',id_batch) AS id, IF(`date` < '$sdate' OR date IS NULL,'O', type) AS type, 
		     SUM(qty+free) AS qty, SUM(free) AS free
		     FROM {$this->prefix}product_ledger WHERE `date` <= '$edate' OR date is NULL AND id_product!=0 GROUP BY 1, 2";
            $res1 = $this->m->sql_getall($sql1, 1, "", "id", "type");
            foreach ($res as $k => $v) {
                $ki = $v['id_product'];
                $result[$ki]['id_product'] = $k;
                $result[$ki]['id'] = $ki;
                $result[$ki]['price'] = $v['price'];
                $result[$ki]['cname'] = $v['cname'];
                $result[$ki]['name'] = $v['name'];
                
		$o = @$res1[$k]['O']['qty'];
                $s = -@$res1[$k]['S']['qty'];
                $sf = -@$res1[$k]['S']['free'];
                $p = @$res1[$k]['P']['qty'];
                $sr = -@$res1[$k]['CN']['qty'] - @$res1[$k]['PR']['qty'];
                $pr = -@$res1[$k]['DN']['qty'] + @$res1[$k]['SA']['qty']  + @$res1[$k]['SR']['qty'];

		@$result[$ki]['obal'] += $o;
                @$result[$ki]['sales'] += $s;
                @$result[$ki]['salesfree'] += $sf;
                @$result[$ki]['purchase'] += $p;
                @$result[$ki]['sreturn'] += $sr;
                @$result[$ki]['preturn'] += $pr;
                @$result[$ki]['cbal'] = $result[$ki]['obal'] - $result[$ki]['sales'] + @$result[$ki]['purchase'] - @$result[$ki]['sreturn'] + @$result[$ki]['preturn'];

                @$result[$ki]['obalv'] += $o*$v['price'];
                @$result[$ki]['salesv'] += $s*$v['price'];
                @$result[$ki]['purchasev'] += $p*$v['price'];
                @$result[$ki]['sreturnv'] += $sr*$v['price'];
                @$result[$ki]['preturnv'] += $pr*$v['price'];
                @$result[$ki]['cbalv'] = $result[$ki]['obalv'] - $result[$ki]['salesv'] + @$result[$ki]['purchasev'] + @$result[$ki]['sreturnv'] - @$result[$ki]['preturnv'];
            }
            unset($res1);
            unset($res);
            $this->sm->assign("sdata", $result);
        }
    }
    function oldstatement() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
	if ($_REQUEST['company']) {
        //$id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        //$wcond = ($id_company != 0) ? " p.id_company='$id_company' AND " : " ";

        #$id_company = isset($_REQUEST['company']) ? implode($_REQUEST['company'], ",") : '0';
        #$wcond = ($id_company != 0) ? " p.id_company IN ($id_company) AND " : " ";

        $id_company = trim(isset($_REQUEST['company']) ? implode(",", $_REQUEST['company']) : '0');
        $wcond = empty($id_company) ? "" : " p.id_company IN ($id_company) AND ";

	$minstock = isset($_REQUEST['minstock']) ? ' , p.minimum_stock ' : '';

        $sql1 = "SELECT id_product, IF(`date` < '$sdate' OR date IS NULL,'O', type) AS type, 
		SUM(qty+free) AS qty, SUM(free) AS free, SUM(amount) AS amount FROM {$this->prefix}product_ledger WHERE `date` <= '$edate' OR date is NULL GROUP BY id_product, 2";
        $res1 = $this->m->sql_getall($sql1, 1, "", "id_product", "type");
        $sql = "SELECT c.name AS cname, id_product AS id, seller_price, purchase_price, `case`, `weight`, `meter`, `pack`, p.name, 0 AS obal {$minstock} FROM {$this->prefix}product p, {$this->prefix}company c WHERE $wcond c.id_company=p.id_company AND c.status=0 AND p.status=0 ORDER BY cname, p.name ";
        $res = $this->m->sql_getall($sql, 1, "", "id");
	$valuation = isset($_REQUEST['valuation']) ? $_REQUEST['valuation'] : 0;
        foreach ($res as $k => $v) {
            $res[$k]['obal'] = $res[$k]['obal'] + @$res1[$k]['O']['qty'];
            $res[$k]['sales'] = -@$res1[$k]['S']['qty'];
            $res[$k]['salesfree'] = -$res1[$k]['S']['free'];
            $res[$k]['purchase'] = @$res1[$k]['P']['qty'] ;
            $res[$k]['sreturn'] = -@$res1[$k]['CN']['qty'] - @$res1[$k]['PR']['qty'];
            $res[$k]['preturn'] = -@$res1[$k]['DN']['qty'] + @$res1[$k]['SA']['qty'] + @$res1[$k]['SR']['qty'];
            $cbal =  $res[$k]['obal'] + $res[$k]['purchase'] - $res[$k]['sreturn'] - $res[$k]['sales'] + $res[$k]['preturn'];
	    $res[$k]['cbal'] = $cbal;
            }
	}
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("sdata", $res);
    }

    function batchstatement() {
        $valuation = isset($_REQUEST['valuation']) ? $_REQUEST['valuation'] : 0;
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company) {
            //$sql1 = "SELECT concat(id_product, '-', id_batch) AS idp, IF(`date` < '$sdate' OR date IS NULL,'O', type) AS type, SUM(qty+free) AS qty, SUM(amount) AS amount FROM {$this->prefix}product_ledger WHERE `date` <= '$edate' OR date is NULL GROUP BY idp, 2";
            $sql1 = "SELECT concat(l.id_product, '-', l.id_batch) AS idp, IF(l.date < '$sdate' OR l.date IS NULL,'O', l.type) AS type, SUM(l.qty+l.free) AS qty, SUM(l.amount) AS amount FROM {$this->prefix}product_ledger l, {$this->prefix}product p WHERE (l.date<='$edate' OR date is NULL) AND p.id_product=l.id_product AND p.id_company='$id_company' GROUP BY idp, 2";
 
            $res1 = $this->m->sql_getall($sql1, 1, "", "idp", "type");
            $price = ($valuation==1) ? 'b.seller_price' : 'b.purchase_price';
            $sql = "SELECT concat(p.id_product,'-',b.id_batch) AS id, $price AS price, b.batch_no, b.expiry_date, p.id_product, p.case, p.weight, p.meter, p.pack, p.name, 0 AS obal FROM {$this->prefix}product p LEFT JOIN {$this->prefix}batch b ON p.id_product=b.id_product WHERE p.id_company='$id_company' ORDER BY p.name ";
            $res = $this->m->sql_getall($sql, 1, "", "id");
            foreach ($res as $k => $v) {
                @$res[$k]['obal'] += @$res1[$k]['O']['qty'];
                @$res[$k]['sales'] += -@$res1[$k]['S']['qty'] + -@$res1[$k]['SA']['qty'] + -@$res1[$k]['SR']['qty'];
                @$res[$k]['purchase'] += @$res1[$k]['P']['qty'] + @$res1[$k]['PR']['qty'];
                @$res[$k]['sreturn'] += @$res1[$k]['CN']['qty'];
                @$res[$k]['preturn'] += -@$res1[$k]['DN']['qty'];

                @$res[$k]['obalv'] += $res[$k]['obal']*$v['price'];
                @$res[$k]['salesv'] += $res[$k]['sales']*$v['price'];
                @$res[$k]['purchasev'] += $res[$k]['purchase']*$v['price'];
                @$res[$k]['sreturnv'] += $res[$k]['sreturn']*$v['price'];
                @$res[$k]['preturnv'] += $$res[$k]['preturn']*$v['price'];
                @$res[$k]['cbalv'] = $res[$k]['obalv'] - $res[$k]['salesv'] + @$res[$k]['purchasev'] + @$res[$k]['sreturnv'] - @$res[$k]['preturnv'];
            }
            $this->sm->assign("sdata", $res);
        }
        $this->sm->assign("company", $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name", 2, "name", "id"));
    }

    function sreg() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $wcond = ($id_company != 0) ? "AND id_company='$id_company'" : " ";
        $sql = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
        $sql = "SELECT id_product AS id, name FROM {$this->prefix}product WHERE status=0 $wcond ORDER BY name";
        $product = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("item", $product);
        if (isset($_REQUEST['id_product'])) {
            $sql = "SELECT l.*, h.name, `case`, `weight`, `meter`, `pack`, p.name AS itemname  
                  FROM {$this->prefix}product p, {$this->prefix}product_ledger l LEFT JOIN {$this->prefix}head h ON l.id_head=h.id_head
                  WHERE l.id_product={$_REQUEST['id_product']} AND p.id_product=l.id_product ORDER BY l.date, l.type";
            $res = $this->m->sql_getall($sql);
            $qty=$flag=0;
            foreach ($res as $k => $v) {
                if ($v['date']<$sdate) {
                    $qty = $qty + ($v['qty']+$v['free']);
                    $flag =  1;
                } else {
                    if ($flag==1) {
                        $data[] = array("type"=>0, "qty"=>$qty);
                        $flag = 0;
                    }
                    $data[] = $v;
                }
            }
            if ($flag==1) {
                $data[] = array("type"=>0, "qty"=>$qty);
            }
            $this->sm->assign("sdata", $data);
        } else {
            $this->sm->assign("sdata", "");
        }
    }
    function batchsreg() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $wcond = ($id_company != 0) ? "AND id_company='$id_company'" : " ";
        $sql = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
        $sql = "SELECT id_product AS id, name FROM {$this->prefix}product WHERE status=0 $wcond ORDER BY name";
        $product = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("item", $product);
        if (@$_REQUEST['id_product']) {
            $sql = "SELECT id_batch, batch_no, expiry_date FROM {$this->prefix}batch WHERE id_product={$_REQUEST['id_product']}";
            $this->sm->assign("batch", $this->m->sql_getall($sql, 1, "", "id_batch"));
            $sql = "SELECT id_batch, batch_no FROM {$this->prefix}batch WHERE id_product={$_REQUEST['id_product']}";
            $this->sm->assign("batchs", $this->m->sql_getall($sql, 2, "batch_no", "id_batch"));

            $sql = "SELECT id_head, name FROM {$this->prefix}head";
            $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id_head"));
	    
	    $bcond = @$_REQUEST['id_batch'] ? " AND l.id_batch=".$_REQUEST['id_batch'] : "";
            $sql = "SELECT l.*, h.name, `case`, `weight`, `meter`, `pack`, p.name AS itemname  
                  FROM {$this->prefix}product p, {$this->prefix}product_ledger l LEFT JOIN {$this->prefix}head h ON l.id_head=h.id_head
                  WHERE l.id_product={$_REQUEST['id_product']} AND p.id_product=l.id_product $bcond ORDER BY l.id_batch, l.date, l.type";
            $res = $this->m->sql_getall($sql);
            $qty=$flag=0;
	    $pbatch = "";
            foreach ($res as $k => $v) {
                if ($v['date']<$sdate) {
                    $qty = $qty + ($v['qty']+$v['free']);
                    $flag = 1;
                } else {
		  if ($v['date']<=$edate) {
                    if ($flag==1) {
                        $data[] = array("type"=>0, "qty"=>$qty, "id_batch"=>$v["id_batch"]);
                        $flag = 0;
                    }
                    $data[] = $v;
		    if ($pbatch==$v["id_batch"]) {
			$qty = 0;
		    } else {
			$pbatch =$v["id_batch"];
		    }
		  }
                }
            }
            if ($flag==1) {
                $data[] = array("type"=>0, "qty"=>$qty, "id_batch"=>$v["id_batch"]  );
            }
            $this->sm->assign("sdata", $data);
        } else {
            $this->sm->assign("sdata", "");
        }
    }

    function age() {
        
    }

    function value() {
	$edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond = "p.id_company='$id_company' AND ";
        } else {
            $wcond = " ";
        }
        $sql = "SELECT pd.id_product, max(pd.date) AS date, SUM(pd.qty+pd.free) AS qty, pd.rate FROM {$this->prefix}product p, {$this->prefix}purchasedetail pd
                WHERE {$wcond} p.id_product=pd.id_product AND pd.date <= '$edate' GROUP BY pd.id_product, pd.rate ORDER BY 1,2 DESC";
        $res = $this->m->sql_getall($sql);
        foreach($res as $k => $v) {
            $id = $v['id_product'];
            $na[$id][] = $v;
        }
        $sql = "SELECT p.name, p.case, p.unit, p.pack, l.id_product, SUM(l.qty+l.free) AS balance, p.purchase_price 
                FROM {$this->prefix}product p, {$this->prefix}product_ledger l
                WHERE {$wcond} p.id_product=l.id_product AND (l.date <= '$edate' OR l.date IS NULL)
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
        $this->sm->assign("sdata", $newdata);
        $this->sm->assign("company", $this->m->sql_getall("SELECT * FROM {$this->prefix}company WHERE status=0 ORDER BY name", 2, "name", "id_company"));
    }

}

?>
