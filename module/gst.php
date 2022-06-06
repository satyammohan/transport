<?php

class gst extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
        $sql = "SHOW COLUMNS FROM `{$this->prefix}creditnote` LIKE 'totalcess'";;
        $found = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if (!$found) {
            $sql = "ALTER TABLE `{$this->prefix}creditnote` ADD totalcess DECIMAL( 16, 2 ) ";
            $this->m->query($sql);
            $sql = "ALTER TABLE `{$this->prefix}debitnote` ADD totalcess DECIMAL( 16, 2 ) ";
            //$this->m->query($sql);
            $sql = "ALTER TABLE `{$this->prefix}creditnotedetail` ADD cessamt DECIMAL( 16, 2 ) ";
            $this->m->query($sql);
            $sql = "ALTER TABLE `{$this->prefix}debitnotedetail` ADD cessamt DECIMAL( 16, 2 ) ";
            //$this->m->query($sql);
        }
    }

    function _default() {
        echo "This function is not enabled...";
    }
    function gstinsales() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $wcond = " (s.date >= '$sdate' AND s.date <= '$edate') ";
        $sql = "SELECT GROUP_CONCAT(DISTINCT h.id_head) AS heads, h.name, h.address1, if(h.gstin, h.gstin, h.id_head) as gst, h.panno, h.tanno, SUM(s.total) AS saletotal
                FROM `{$this->prefix}head` h, `{$this->prefix}sale` s
                WHERE $wcond AND h.id_head=s.id_head
                GROUP BY gst ORDER BY saletotal DESC";
        $this->sm->assign("data", $this->m->sql_getall($sql));
    }
    function b2b() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $wcond = " s.date >= '$sdate' AND s.date <= '$edate' ";
        $wcond1 = "";
        switch ($_REQUEST['option']) {
            case 2:
                $wcond .= "AND !(h.gstin='' OR h.gstin IS NULL)";
                break;
            case 3:
                $wcond .= "AND (h.gstin='' OR h.gstin IS NULL)";
                break;
        }
        switch ($_REQUEST['taxtype']) {
            case 2:
                $wcond1 = " AND sd.tax_per=0 ";
                break;
            case 3:
                $wcond1 = " AND sd.tax_per!=0 ";
                break;
        }
        /*$sql = "SELECT s.id_sale, s.invno, s.party_name, s.challan_no, s.date, s.total, h.gstin, h.state, sd.tax_per, SUM(sd.goods_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}saledetail` sd, `{$this->prefix}taxmaster` m, `{$this->prefix}sale` s 
                    LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE ($wcond) AND sd.id_taxmaster=m.id_taxmaster AND s.id_sale=sd.id_sale
                GROUP BY s.id_sale, sd.tax_per ORDER BY s.date, s.invno, sd.tax_per ";*/

        $sql = "SELECT s.id_sale, s.invno, s.party_name, s.challan_no, s.date, s.total, h.gstin, h.state, sd.tax_per, SUM(sd.goods_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                    LEFT JOIN `{$this->prefix}saledetail` sd ON s.id_sale=sd.id_sale {$wcond1}
                    LEFT JOIN `{$this->prefix}taxmaster` m ON sd.id_taxmaster=m.id_taxmaster
                    WHERE ($wcond)
                GROUP BY s.id_sale, sd.tax_per ORDER BY s.date, s.invno, s.id_sale, sd.tax_per ";
        $this->sm->assign("data", $this->m->sql_getall($sql));
    }
    function b2ball() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $wcond = " s.date >= '$sdate' AND s.date <= '$edate' ";
        $sql = "SELECT s.id_sale, s.invno, s.date, s.total, h.gstin, h.state, sd.tax_per, SUM(sd.goods_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}saledetail` sd, `{$this->prefix}taxmaster` m, `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE ($wcond) AND sd.id_taxmaster=m.id_taxmaster AND s.id_sale=sd.id_sale
                GROUP BY s.id_sale, sd.tax_per ORDER BY s.date, s.invno, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "gst/b2b.tpl.html");
    }

    function b2cs() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $wcond = " s.date >= '$sdate' AND s.date <= '$edate' ";
        $sql = "SELECT sd.tax_per, SUM(sd.goods_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}saledetail` sd, `{$this->prefix}taxmaster` m, `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE ($wcond) AND sd.id_taxmaster=m.id_taxmaster AND s.id_sale=sd.id_sale AND (h.gstin='' OR h.gstin IS NULL) AND s.total<250000
                GROUP BY sd.tax_per ORDER BY sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function b2cl() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $wcond = " s.date >= '$sdate' AND s.date <= '$edate' ";
        $sql = "SELECT s.id_sale, s.invno, s.date, s.total, h.gstin, h.state, sd.tax_per, SUM(sd.goods_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}saledetail` sd, `{$this->prefix}taxmaster` m, `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE ($wcond) AND sd.id_taxmaster=m.id_taxmaster AND s.id_sale=sd.id_sale AND (h.gstin='' OR h.gstin IS NULL) AND s.total>=250000
                GROUP BY s.id_sale, sd.tax_per ORDER BY s.date, s.invno, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function gstr3b() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $sql = "SELECT SUM(sd.tcsamt) AS tcsamt, SUM(`add`-`less`+`round`+packing) AS other FROM `{$this->prefix}sale` sd
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("saletcs", $res);
        $sql = "SELECT SUM(sd.tcsamt) AS tcsamt, SUM(`add`-`less`+`round`+packing) AS other FROM `{$this->prefix}purchase` sd
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("purctcs", $res);
        $sql = "SELECT h.local, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}saledetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate' AND id_sale!=0
                GROUP BY h.local, sd.tax_per ORDER BY h.local, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $sql = "SELECT h.local, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}purchasedetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate' AND id_purchase!=0
                GROUP BY h.local, sd.tax_per ORDER BY h.local, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("datap", $res);
    }
    function gstrdetail() {
	    $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $tax = $_REQUEST['tax'];
        $type = $_REQUEST['type'];
        $local = $_REQUEST['local'];
        $sql = "SELECT sd.*, h.name, h.address1, IF(h.gstin, 1, 0) AS havegst
                FROM `{$this->prefix}{$type}detail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE (sd.date >= '$sdate' AND sd.date <= '$edate') AND sd.tax_per=$tax AND h.local!=$local
                ORDER BY h.local, sd.tax_per ";
        echo $sql;
        $res = $this->m->sql_getall($sql);
        $this->pr($res);
        $this->sm->assign("data", $res);
    }
    function gstrsummary() {
	    $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $sql = "SELECT h.local, IF(h.gstin, 1, 0) AS havegst, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}saledetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'
                GROUP BY h.local, havegst, sd.tax_per ORDER BY h.local, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
	    $sql = "SELECT h.local, IF(h.gstin, 1, 0) AS havegst, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}sreturndetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'
                GROUP BY h.local, havegst, sd.tax_per ORDER BY h.local, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("sreturn", $res);
        $sql = "SELECT h.local, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}purchasedetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'
                GROUP BY h.local, sd.tax_per ORDER BY h.local, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("datap", $res);

        $sql = "SHOW COLUMNS FROM `{$this->prefix}creditnotedetail` LIKE 'mode'";
        $wcond = $this->m->num_rows($this->m->query($sql)) == 0 ? " " : " AND `mode`='S' ";

        $sql = "SELECT h.local, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}creditnotedetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'  $wcond
                GROUP BY h.local, sd.tax_per ORDER BY h.local, sd.tax_per ";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("creditnote", $res);
        $sql = "SELECT h.local, sd.tax_per, SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.cessamt) AS cessamt
                FROM `{$this->prefix}debitnotedetail` sd LEFT JOIN `{$this->prefix}head` h ON h.id_head=sd.id_head 
                WHERE sd.date >= '$sdate' AND sd.date <= '$edate'  $wcond
                GROUP BY h.local, sd.tax_per ORDER BY h.local, sd.tax_per ";
//        $res = $this->m->sql_getall($sql);
//        $this->sm->assign("debitnote", $res);
    }    
    function hsn() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') ";
        $sql = $this->hsnquery('sale', $wcond);
        if ($_REQUEST['saleablecrnote']) {
	    $_REQUEST['type']="S";
	    $sql1 = $this->hsncredit(-1);
            $sql1 = str_replace("ORDER BY 1, 2", "", $sql1);

            $sr = $this->hsnquery('sreturn',  " (`date` >= '$sdate' AND `date` <= '$edate') ", -1);
            $sr = str_replace("ORDER BY 1, 2", "", $sr);

            $sql = " $sql1 UNION ALL $sr UNION ALL $sql ";
        }
        if ($_REQUEST['option']==4 || $_REQUEST['option']==2) {
            $res = $this->executesql($sql);
        } else {
            $res = $this->executesqlnogroup($sql);
        }
        $this->sm->assign("data", $res);
    }
    function executesql($sql) {
        $rs = $this->m->query($sql);
        $res = array();
	while ($r = mysql_fetch_assoc($rs)) {
            $sgst = $cgst = $igst = 0;
            if ($r['local']) {
                $igst = $r['tax_amount'];
            } else {
                $cgst = $sgst = $r['tax_amount']/2;
            }
            @$res[$r['hsncode']]['hsncode'] = $r['hsncode'];
            @$res[$r['hsncode']]['name'] = $r['name'];
            @$res[$r['hsncode']]['unit'] = $r['unit'];
            @$res[$r['hsncode']]['qty'] += $r['qty'];
            @$res[$r['hsncode']]['amount'] += $r['amount'];
            @$res[$r['hsncode']]['goods_amount'] += $r['goods_amount'];
            @$res[$r['hsncode']]['cessamt'] += $r['cessamt'];
            @$res[$r['hsncode']]['igst'] += $igst;
            @$res[$r['hsncode']]['sgst'] += $sgst;
            @$res[$r['hsncode']]['cgst'] += $cgst;
        }
        return $res;
    }
    function executesqlnogroup($sql) {
        $res = array();
        $rs = $this->m->query($sql);
	    while ($r = mysql_fetch_assoc($rs)) {
            $sgst = $cgst = $igst = 0;
            if ($r['local']) {
                $r['igst'] = $r['tax_amount'];
            } else {
                $r['cgst'] = $r['sgst'] = $r['tax_amount']/2;
            }
            @$res[] = $r;
        }
        return $res;
    }
    function hsncredit($type=1) {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $cond = @$_REQUEST['type']=="N" ? " AND s1.mode='N' " : " AND s1.saletype=1 AND (s1.caltype='C') ";
        $wcond = " s1.id_creditnote=s.id_creditnote AND (s1.date >= '$sdate' AND s1.date <= '$edate') $cond ";
        $file = 'creditnote';
	$cond = $wcond;
        $field = "h.local, p.name, p.unit, SUM($type*qty) AS qty, SUM($type*net_amount) AS amount, SUM($type*goods_amount) AS goods_amount, SUM($type*tax_amount) AS tax_amount, SUM($type*cessamt) AS cessamt";
        switch ($_REQUEST['option']) {
        case 1:
            $sql = "SELECT p.hsncode, $field
                FROM {$this->prefix}{$file} s1, `{$this->prefix}product` p, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product GROUP BY 1, 2, 3 ORDER BY 1, 2 ";
            break;
        case 2:
            $sql = "SELECT p.hsncode, $field
                FROM {$this->prefix}{$file} s1, `{$this->prefix}product` p, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product GROUP BY 1, 2 ORDER BY 1, 2 ";
            break;
        case 3:
            $sql = "SELECT p.hsncode, $field, t.name AS taxname
                FROM {$this->prefix}{$file} s1, `{$this->prefix}product` p, `{$this->prefix}taxmaster` t, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product AND s.id_taxmaster=t.id_taxmaster
                                GROUP BY p.hsncode, h.local, s.id_taxmaster ORDER BY 1, 2 ";
            break;
        case 4:
            $sql = "SELECT LEFT(p.hsncode,4) AS hsncode, $field
                FROM {$this->prefix}{$file} s1, `{$this->prefix}product` p, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product GROUP BY 1, 2 ORDER BY 1, 2 ";
        }
        $res = $this->executesqlnogroup($sql);
        $this->sm->assign("data", $res);
        return $sql;
    }
    function hsnsreturn() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') ";
        $sql = $this->hsnquery('sreturn', $wcond);
        $res = $this->executesqlnogroup($sql);
        $this->sm->assign("data", $res);
	return $sql;
    }
    function hsndebit() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') ";
        $sql = $this->hsnquery('debitnote', $wcond);
        $res = $this->executesqlnogroup($sql);
        $this->sm->assign("data", $res);
    }
    function hsnpurc() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') ";
        $sql = $this->hsnquery('purchase', $wcond);
        $res = $this->executesqlnogroup($sql);
        $this->sm->assign("data", $res);
    }
    function hsnquery($file, $wcond, $type=1) {
        $field = "h.local, p.name, p.unit, SUM($type*qty) AS qty, SUM($type*net_amount) AS amount, SUM($type*goods_amount) AS goods_amount, SUM($type*tax_amount) AS tax_amount, SUM($type*cessamt) AS cessamt";
        switch ($_REQUEST['option']) {
        case 1:
            $sql = "SELECT p.hsncode, $field
                FROM `{$this->prefix}product` p, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product GROUP BY 1, 2, 3 ORDER BY 1, 2 ";
	    break;
        case 2:
            $sql = "SELECT p.hsncode, $field
                FROM `{$this->prefix}product` p, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product GROUP BY 1, 2 ORDER BY 1, 2 ";
	    break;
        case 3:
            $sql = "SELECT p.hsncode, $field, t.name AS taxname
                FROM `{$this->prefix}product` p, `{$this->prefix}taxmaster` t, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product AND s.id_taxmaster=t.id_taxmaster
				GROUP BY p.hsncode, h.local, s.id_taxmaster ORDER BY 1, 2 ";
	    break;
        case 4:
            $sql = "SELECT LEFT(p.hsncode,4) AS hsncode, $field
                FROM `{$this->prefix}product` p, `{$this->prefix}{$file}detail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE $wcond AND s.id_product=p.id_product GROUP BY 1, 2 ORDER BY 1, 2 ";
        }
        return $sql;
    }
    function dbnotereg() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') ";
        $sql = $this->hsnregister('debitnote', $wcond);
        $this->sm->assign("data", $this->m->sql_getall($sql));
    }
    function crnotereg() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $cond = @$_REQUEST['type']=="N" ? " AND `saletype`='2' " : " AND `saletype`='1' ";
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') $cond ";
	$comp = @$_REQUEST['company'];
	if ($comp!=0) {
		$wcond .= " AND id_company='$comp' ";
	}

        $sql = $this->hsnregister('creditnote', $wcond);
        $this->sm->assign("data", $this->m->sql_getall($sql));
    }    
    function hsnregister($file, $wcond) {
        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}{$file}detail` s, `{$this->prefix}taxmaster` t 
            WHERE $wcond AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
            case 1:
                $sql_detail = "SELECT id_{$file}, no, date, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt 
                    FROM `{$this->prefix}{$file}detail` WHERE $wcond GROUP BY id_{$file}, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "id_{$file}", "id_taxmaster");
                $sql = "SELECT s.*, h.name AS hname, h.address1 AS hadd1, h.address2 AS hadd2, h.local, h.gstin FROM `{$this->prefix}{$file}` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                    WHERE $wcond ORDER BY s.`date`, s.`no`";
                break;
            case 2:
                $sql_detail = "SELECT `date`, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt 
                    FROM `{$this->prefix}{$file}detail` WHERE $wcond GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "date", "id_taxmaster");
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}{$file}` WHERE $wcond GROUP BY `date` ORDER BY `date` ";
                break;
            case 3:
                $sql_detail = "SELECT MONTHNAME(`date`) AS month, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt 
                    FROM `{$this->prefix}{$file}detail`
                    WHERE $wcond GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "month", "id_taxmaster");
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, 
                        SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}{$file}` 
                    WHERE $wcond GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                break;
        }
        $this->sm->assign("file", $file);
        $this->sm->assign("detail", $detail);
        return $sql;
    }
    function gstrJSON() {
        $sdate = $_SESSION['sdate'];
        $syear = date("Y", strtotime($sdate));
        $eyear = date("Y", strtotime($_SESSION['edate']));
        $months = ["04"=>"April", "05"=>"May", "06"=>"June", "07"=>"July", "08"=>"August", "09"=>"September", "10"=>"October", "11"=>"November", "12"=>"December", "01"=>"January", "02"=>"February", "03"=>"March"];
        foreach ($months as $k =>$v) {
            $yr = $k < 4 ? $eyear : $syear;
            $k1 = $k.$yr;
            $v1 = $v." ".$yr;
            $period[$k1] = $v1;
        }
        $this->sm->assign("period", $period);
        if (@$_REQUEST['formonth']) {
            $fp = $_REQUEST['formonth'];
            $this->getgstrJSON($fp);
            $this->sm->assign("current", $fp);
        }
    }
    function getgstrJSON($fp) {
        // b2cs, b2b, nil, doc_issue, hsn
        $month = substr($fp,0,2);
        $year = substr($fp,2,4);
        $format = array("gstin"=>$_SESSION["gstin"], "fp"=>$fp, "version"=>"GST2.3.1", "hash"=>"hash");
        $b2cs = $this->getb2cs($month, $year); // done
        $format['b2cs'] = $b2cs; // done

        $b2b = $this->getb2b($month, $year);
        $format['b2b'] = $b2b;

        $nil = $this->getnil($month, $year); // done
        $format['nil'] = $nil; // done

        $doc_issue = $this->getdoc($month, $year); // done
        $format['doc_issue'] = $doc_issue; // done

        $hsn = $this->gethsn($month, $year); // done
        $format['hsn'] = $hsn; // done
        $this->sm->assign("data", $format);
    }
    function getdoc($month, $year) {
        $doc["doc_num"]=1;
        $doc["doc_typ"]="Invoices for outward supply";
        $sql = "SELECT DISTINCT invno FROM `{$this->prefix}sale` WHERE MONTH(date)=$month AND YEAR(date)=$year AND invno!='' ORDER BY invno";
        $res = $this->m->sql_getall($sql);
        $series = array();
        $start = $next = "";
        foreach ($res as $k => $v) {
            $myinv = $v['invno'];
            $num = "";
            do {
                $last = substr($myinv, strlen($myinv)-1, 1);
                if (!preg_match('/[0-9]+/',$last))
                    break;
                $num = $last.$num;
                $myinv = substr($myinv, 0, strlen($myinv)-1);
            } while ($myinv);
            if ($myinv=="") {
                $myinv = substr($v['invno'],0,1);
                $num = substr($v['invno'],1,20);
            }
            $num = (int) $num;
            $res1[$myinv][$num] = $num;
        }
        foreach ($res1 as $k => $v) {
            $mn = $final[$k]['min'] = min($v);
            $mx = $final[$k]['max'] = max($v);
            $missing = "";
            for ($i=$mn; $i<$mx; ++$i) {
                if (!isset($v[$i])) {
                    $missing .= $k.$i." ";
                }
            }
            $final[$k]['count'] = count($v);
            $final[$k]['missing'] = $missing;
        }
        $i=0;
        foreach ($final as $k => $v) {
            $s = $k.$v['min'];
            $e = $k.$v['max'];
	        $c = $v['max']-$v['min']+1;
            $doc["docs"][]=array("num"=>++$i,"from"=>$s,"to"=>$e,"totnum"=>$c,"cancel"=>$c-$v['count'],"net_issue"=>$v['count'],"missing"=>$v['missing']);
        }
        return array("doc_det"=>array($doc));
    }
    function getb2cs($month, $year) {
        $sql = "SELECT s.tax_per, SUM(s.goods_amount) AS c1, SUM(s.tax_amount) AS c2, SUM(s.cessamt) AS c3
            FROM `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head
            WHERE MONTH(s.date)=$month AND YEAR(s.date)=$year AND !h.gstin GROUP BY s.tax_per";
        $full_d = $this->m->sql_getall($sql, 1, "", "tax_per");
        $d = @$full_d['5.00'];
        $data[] = array("sply_ty"=>"INTRA", "rt"=>5, "typ"=>"OE", "pos"=>"21", "txval"=>@$d['c1']*1, "camt"=>ROUND(@$d['c2']/2,2), "samt"=>ROUND(@$d['c2']/2,2), "csamt"=>@$d['c3']*1);
        $d = @$full_d['12.00'];
        $data[] = array("sply_ty"=>"INTRA", "rt"=>12, "typ"=>"OE", "pos"=>"21", "txval"=>@$d['c1']*1, "camt"=>ROUND(@$d['c2']/2,2), "samt"=>ROUND(@$d['c2']/2,2), "csamt"=>@$d['c3']*1);
        $d = @$full_d['18.00'];
        $data[] = array("sply_ty"=>"INTRA", "rt"=>18, "typ"=>"OE", "pos"=>"21", "txval"=>@$d['c1']*1, "camt"=>ROUND(@$d['c2']/2,2), "samt"=>ROUND(@$d['c2']/2,2), "csamt"=>@$d['c3']*1);
        $d = @$full_d['28.00'];
        $data[] = array("sply_ty"=>"INTRA", "rt"=>28, "typ"=>"OE", "pos"=>"21", "txval"=>@$d['c1']*1, "camt"=>ROUND(@$d['c2']/2,2), "samt"=>ROUND(@$d['c2']/2,2), "csamt"=>@$d['c3']*1);
        // $data[] = array("sply_ty"=>"INTRA", "rt"=>5, "typ"=>"OE", "pos"=>"21", "txval"=>8283806.04, "camt"=>207095.15, "samt"=>207095.15, "csamt"=>0);
        // $data[] = array("sply_ty"=>"INTRA", "rt"=>12, "typ"=>"OE", "pos"=>"21", "txval"=>0, "camt"=>0, "samt"=>0, "csamt"=>0);
        // $data[] = array("sply_ty"=>"INTRA", "rt"=>18, "typ"=>"OE", "pos"=>"21", "txval"=>0, "camt"=>0, "samt"=>0, "csamt"=>0);
        // $data[] = array("sply_ty"=>"INTRA", "rt"=>28, "typ"=>"OE", "pos"=>"21", "txval"=>0, "camt"=>0, "samt"=>0, "csamt"=>0);
        return $data;
    }    
    function getnil($month, $year) {
        $sql = "SELECT IF(h.gstin,1,0) AS local, SUM(net_amount) AS amount
                FROM `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head AND s.tax_per=0
                WHERE MONTH(date)=$month AND YEAR(date)=$year AND s.tax_per=0 GROUP BY 1 ORDER BY 1 ";
        $full = $this->m->sql_getall($sql,2,"amount","local");
        $nil["inv"][] = array("sply_ty"=>"INTRB2B","expt_amt"=>0,"nil_amt"=>0,"ngsup_amt"=>0);
        $nil["inv"][] = array("sply_ty"=>"INTRAB2B","expt_amt"=>0,"nil_amt"=>$full[0],"ngsup_amt"=>0);
        $nil["inv"][] = array("sply_ty"=>"INTRB2C","expt_amt"=>0,"nil_amt"=>0,"ngsup_amt"=>0);
        $nil["inv"][] = array("sply_ty"=>"INTRAB2C","expt_amt"=>0,"nil_amt"=>$full[1],"ngsup_amt"=>0);

        // $nil["inv"][] = array("sply_ty"=>"INTRB2B","expt_amt"=>0,"nil_amt"=>0,"ngsup_amt"=>0);
        // $nil["inv"][] = array("sply_ty"=>"INTRAB2B","expt_amt"=>0,"nil_amt"=>10269505,"ngsup_amt"=>0);
        // $nil["inv"][] = array("sply_ty"=>"INTRB2C","expt_amt"=>0,"nil_amt"=>0,"ngsup_amt"=>0);
        // $nil["inv"][] = array("sply_ty"=>"INTRAB2C","expt_amt"=>0,"nil_amt"=>11071702,"ngsup_amt"=>0);
        // $this->pr($nil);
        return $nil;
    }
    function gethsn($month, $year) {
        $sql = "SELECT p.hsncode, p.name, p.unit, h.local, SUM(qty) AS qty, SUM(net_amount) AS amount, SUM(goods_amount) AS goods_amount, 
                SUM(if(h.local, 0, 1)*tax_amount) AS ltax_amount, SUM(if(h.local, 1, 0)*tax_amount) AS otax_amount, SUM(cessamt) AS cessamt
                FROM `{$this->prefix}product` p, `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                WHERE MONTH(date)=$month AND YEAR(date)=$year AND s.id_product=p.id_product GROUP BY p.hsncode ORDER BY p.hsncode, p.name ";
        $full = $this->m->sql_getall($sql);
        $i = 0;
        foreach ($full as $k => $v) {
            $l = ROUND($v['ltax_amount']/2,2);
            $data1[] = array("num"=>++$i, "hsn_sc"=>$v['hsncode']*1, "desc"=>$v['name'], "uqc"=>$v['unit'], "qty"=>$v['qty'], 
                "val"=>$v['amount'], "txval"=>$v['goods_amount'], "iamt"=>$v['otax_amount'], "samt"=>$l, "camt"=>$l, "csamt"=>$v['cessamt']);
        }
        //$data[] = array("num"=>1, "hsn_sc"=>"713", "desc"=>"PULSES", "uqc"=>"BAG", "qty"=>6677, "val"=>21341207, "txval"=>21341207, "iamt"=>0, "samt"=>0, "camt"=>0, "csamt"=>0);
        //$data[] = array("num"=>2, "hsn_sc"=>"1207", "desc"=>"MUSTARD SEEDS", "uqc"=>"BAG", "qty"=>485, "val"=>1109618.12, "txval"=>1056779.16, "iamt"=>0, "samt"=>26419.48, "camt"=>26419.48, "csamt"=>0);
        //$data[] = array("num"=>3, "hsn_sc"=>"1701", "desc"=>"SUGAR", "uqc"=>"BAG", "qty"=>12780, "val"=>22493883.13, "txval"=>21422745.84, "iamt"=>16300, "samt"=>527418.65, "camt"=>527418.65, "csamt"=>0);
        //$data[] = array("num"=>4, "hsn_sc"=>"1511", "desc"=>"REFINE OIL", "uqc"=>"OTH", "qty"=>10, "val"=>10920, "txval"=>10400, "iamt"=>0, "samt"=>260, "camt"=>260, "csamt"=>0);
        //$this->pr($data);
        return array("data"=>$data1);
    }
    function getb2b($month, $year) {
        $sql = "SELECT s.invno, s.date, h.gstin, s.total, p.hsncode, sd.tax_per AS rt, SUM(sd.goods_amount) AS c1, SUM(sd.tax_amount) AS c2, SUM(sd.cessamt) AS c3
            FROM `{$this->prefix}sale` s, `{$this->prefix}saledetail` sd, `{$this->prefix}head` h, `{$this->prefix}product` p 
            WHERE MONTH(s.date)=$month AND YEAR(s.date)=$year AND s.id_sale=sd.id_sale AND s.id_head=h.id_head AND sd.id_product=p.id_product AND h.gstin!='' 
            GROUP BY s.id_sale, sd.tax_per ORDER BY h.gstin, s.date, s.invno, sd.tax_per";
        $full = $this->m->sql_getall($sql);

        $data = "";
        foreach ($full as $k => $v) {
            $items = array("num"=> 501, "itm_det"=> array("txval"=>$v['c1'], "rt"=> $v['rt'], "camt"=> ROUND($v['c2']/2,2), "samt"=> ROUND($v['c2']/2,2), "csamt"=> $v['c3'] ));
            $n['ctin'] = $v['gstin'];
            $inv = array("inum"=> $v['invno'], "idt"=> $v['date'], "val"=> $v['total'], "pos"=> substr($v['gstin'],2), "rchrg"=> "N", "inv_typ"=> "R", "itms"=> array());
        }
        //$this->pr($data);
        $data["ctin"] = "21BNCPM4694N1ZM";
        $data["inv"][] =  array("inum"=>"G1576","idt"=>"01-02-2020","val"=>49140,"pos"=>"21","rchrg"=>"N","inv_typ"=>"R","itms"=>array(array("num"=>501,"itm_det"=>array("txval"=>46800,"rt"=>5,"camt"=>1170,"samt"=>1170,"csamt"=>0))));
        return array($data);
    }
    function saleseinvoicew() {
	$_REQUEST['start_date'] = $_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("d/m/Y");
        $sdate = $this->getstartdate();
        $wcond = " s.id_sale=sd.id_sale AND s.date='$sdate' AND h.gstin!='' ";
	$sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
        $comp = @$_REQUEST['company'];
        if ($comp!=0) {
            $wcond .= " AND s.id_company='$comp' ";
        }
        $sql = "SELECT h.*, s.*, sd.*, p.name as iname, p.hsncode, b.expiry_date
            FROM {$this->prefix}sale s, `{$this->prefix}head` h , `{$this->prefix}product` p, `{$this->prefix}saledetail` sd LEFT JOIN `{$this->prefix}batch` b
                ON sd.id_batch=b.id_batch
            WHERE $wcond AND sd.id_product=p.id_product AND h.id_head=s.id_head ORDER BY s.date, s.invno ";
        $inv = $this->m->sql_getall($sql);
        $prev = "";
        $l = 1;
	$res = "";
        if ($_REQUEST['submit']=="json") {
            foreach ($inv as $k => $v) {
                $r = "";
                if ($prev!=$v['invno']) {
                    $l=1;
                }
                $r['Version']="1.1";
                $r['TranDtls']['TaxSch']="GST";
                $r['TranDtls']['SupTyp']="B2B";
                $r['TranDtls']['IgstOnIntra']="N";
                $r['TranDtls']['RegRev']=null;
                $r['TranDtls']['EcmGstin']=null;

                $r['DocDtls']["Typ"]="INV";
                $r['DocDtls']["No"]=$v['invno'];
                $exd = date_create($v['date']);
                $d = date_format($exd, "d/m/Y");
                $r['DocDtls']["Dt"]=$d;

                $r['SellerDtls']["Gstin"]=$_SESSION['gstin'];
                $r['SellerDtls']["LglNm"]=trim($_SESSION['companyname']);
                $r['SellerDtls']["TrdNm"]=trim($_SESSION['companyname']);
                $r['SellerDtls']["Addr1"]=$_SESSION['address'];
                $r['SellerDtls']["Addr2"]=$_SESSION['address'];
                $r['SellerDtls']["Loc"]=$_SESSION['city'];
                $r['SellerDtls']["Pin"]=(int) $_SESSION['pincode'];
                $r['SellerDtls']["Stcd"]=$_SESSION['statecode'];
                $r['SellerDtls']["Ph"]=$_SESSION['phone'] ? $_SESSION['phone'] : null;
                $r['SellerDtls']["Em"]=$_SESSION['email'];

                $r['BuyerDtls']["Gstin"]=$v['gstin'];
                $r['BuyerDtls']["LglNm"]=$v['party_name'];
                $r['BuyerDtls']["TrdNm"]=$v['party_name'];
                $r['BuyerDtls']["Pos"]=$v['statecode'];
                $r['BuyerDtls']["Addr1"]=$v['address1'];
                $r['BuyerDtls']["Addr2"]=$v['address2']?$v['address2']:$v['address1'];
                $r['BuyerDtls']["Loc"]=$v['location'];
                $r['BuyerDtls']["Pin"]=(int) $v['pincode'];
                $r['BuyerDtls']["Stcd"]=$v['statecode'];
                $r['BuyerDtls']["Ph"]=null;
                $r['BuyerDtls']["Em"]=null;
                $r['DispDtls']=null;
                $r['ShipDtls']=null;

		$r['ValDtls']['AssVal']=round($v['totalamt']-$v['discount'],2);
                if ($v['local']==0) {
                    $r['ValDtls']['IgstVal']=0;
                    $r['ValDtls']['CgstVal']=round($v['vat']/2,2);
                    $r['ValDtls']['SgstVal']=round($v['vat']/2,2);
                } else {
                    $r['ValDtls']['IgstVal']=round($v['vat'],2);
                    $r['ValDtls']['CgstVal']=0;
                    $r['ValDtls']['SgstVal']=0;
                }
                $r['ValDtls']['CesVal']=$v['totalcess'] ? round($v['totalcess'],2) : 0 ;
                $r['ValDtls']['StCesVal']=0;
                $r['ValDtls']['Discount']=0;
                $r['ValDtls']['OthChrg']=round($v['tcsamt'],2);
                $r['ValDtls']['RndOffAmt']=$v['add']-$v['less']+$v['round'];
                $r['ValDtls']['TotInvVal']=round($v['total'],2);
                $r['EwbDtls']=null;
                $i = $l;

		$r['ItemList'][$i-1]["SlNo"] = "$l";
		$r['ItemList'][$i-1]["PrdDesc"] = $v['iname'];
                $r['ItemList'][$i-1]["IsServc"] = "N";
                $r['ItemList'][$i-1]["HsnCd"] = $v['hsncode'];
                $r['ItemList'][$i-1]["Qty"] = round($v['qty'],0);
                $r['ItemList'][$i-1]["FreeQty"] = round($v['free'],0);
                $r['ItemList'][$i-1]["Unit"] = "PCS";
                $r['ItemList'][$i-1]["UnitPrice"] = round($v['amount']/$v['qty'],3);
                $r['ItemList'][$i-1]["TotAmt"] = round($v['amount'],2);
                $r['ItemList'][$i-1]["Discount"] = round($v['discount_amount1']+$v['discount_amount2']+$v['discount_amount3']+$v['discount_amount4'],2);
                $r['ItemList'][$i-1]["PreTaxVal"] = 0;
                $r['ItemList'][$i-1]["AssAmt"] = round($v['goods_amount'],2);
                $r['ItemList'][$i-1]["GstRt"] = round($v['tax_per'],0);
                if ($v['local']==0) {
                    $r['ItemList'][$i-1]["IgstAmt"] = 0;
                    $r['ItemList'][$i-1]["CgstAmt"] = round($v['tax_amount']/2,2);
                    $r['ItemList'][$i-1]["SgstAmt"] = round($v['tax_amount']/2,2);
                } else {
                    $r['ItemList'][$i-1]["IgstAmt"] = round($v['tax_amount'],2);
                    $r['ItemList'][$i-1]["CgstAmt"] = 0;
                    $r['ItemList'][$i-1]["SgstAmt"] = 0;
                }
                $r['ItemList'][$i-1]["CesRt"] = $v['cess']?round($v['cess']):0;
                $r['ItemList'][$i-1]["CesAmt"] = $v['cessamt']?round($v['cessamt']):0;
                $r['ItemList'][$i-1]["CesNonAdvlAmt"] = 0;
                $r['ItemList'][$i-1]["StateCesRt"] = 0;
                $r['ItemList'][$i-1]["StateCesAmt"] = 0;
                $r['ItemList'][$i-1]["StateCesNonAdvlAmt"] = 0;
                $r['ItemList'][$i-1]["OthChrg"] = 0;
                $r['ItemList'][$i-1]["TotItemVal"] = round($v['net_amount'],2);
                if ($i==1) {
                    $res[] = $r;
                } else {
                    $res[count($res)-1]['ItemList'][$i-1] = $r['ItemList'][$i-1];
                }
                $l += 1;
                $prev=$v['invno'];
            }
            $json = json_encode($res);
            $this->sm->assign("json", $json);
        } else {
            foreach ($inv as $k => $v) {
                if ($prev!=$v['invno']) {
                    $l=1;
                }
                $r["SUPPLY_TYPE_CODE"]="B2B";
                $r["REVERSE_CHARGE"]="";
                $r["EComm_GSTIN"]="";
                $r["IGST_ON_INTRA"]="NO";
                $r["DOCUMENT_TYPE"]="Tax Invoice";
                $r["DOCUMENT_NUMBER"]=$v['invno'];
                $r["DOCUMENT_DATE"]=$v['date'];
                $r["BUYER_GSTIN"]=$v['gstin'];
                $r["BUYER_LEGAL_NAME"]=$v['party_name'];
                $r["BUYER_TRADE_NAME"]=$v['party_name'];
                $r["BUYER_POS"]=$v['statecode'];
                $r["BUYER_ADD1"]=$v['address1'];
                $r["BUYER_ADD2"]=$v['address2']?$v['address2']:$v['address1'];
                $r["BUYER_LOCATION"]=$v['location'];
                $r["BUYER_PIN_CODE"]=(int) $v['pincode'];
                $r["BUYER_STATE"]=$v['state'];
                $r["BUYER_PHONE_NUMBER"]="";
                $r["BUYER_EMAIL_ID"]="";
                $r["DISPATCH_NAME"]="";
                $r["DISPATCH_ADD1"]="";
                $r["DISPATCH_ADD2"]="";
                $r["DISPATCH_LOCATION"]="";
                $r["DISPATCH_PIN_CODE"]="";
                $r["DISPATCH_STATE"]="";
                $r["SHIPPING_GSTIN"]="";
                $r["SHIPPING_LEGAL_NAME"]="";
                $r["SHIPPING_TRADE_NAME"]="";
                $r["SHIPPING_ADD1"]="";
                $r["SHIPPING_ADD2"]="";
                $r["SHIPPING_LOCATION"]="";
                $r["SHIPPING_PIN_CODE"]="";
                $r["SHIPPING_STATE"]="";

                $r["SL_NO"]=$l;
                $r["PRODUCT_DESCRIPTION"]=$v['iname'];
                $r["IS_SERVICE"]="NO";
                $r["HSN_CODE"]=$v['hsncode'];
                $r["BARCODE"]="";
                $r["QUANTITY"]=round($v['qty'],0);
                $r["FREE_QUANTITY"]= round($v['free'],0);
                $r["UNIT"]="PIECES";
                $r["UNIT_PRICE"]=round($v['amount']/$v['qty'],3);
                $r["GROSS_AMOUNT"]=round($v['amount'],2);
                $r["DISCOUNT"]=round($v['discount_amount1']+$v['discount_amount2']+$v['discount_amount3']+$v['discount_amount4'],2);
                $r["PER_TAX_VALUE"]="";
                $r["TAXABLE_VALUE"]=round($v['goods_amount'],2);
                $r["GST_perc"]=round($v['tax_per'],0);
                if ($v['local']==0) {
                    $r["IGST_AMOUNT"]=0;
                    $r["SGST_AMOUNT"]=round($v['tax_amount']/2,2);
                    $r["CGST_AMOUNT"]=round($v['tax_amount']/2,2);
                } else {
                    $r["IGST_AMOUNT"]=round($v['tax_amount'],2);
                    $r["SGST_AMOUNT"]=0;
                    $r["CGST_AMOUNT"]=0;
                }
                $r["CESS_perc"]=$v['cess'];
                $r["CESS_AMOUNT"]=$v['cessamt'];
                $r["CESS_NONADVAL_AMT"]="";
                $r["STATE_RATE_CESS_perc"]="";
                $r["STATE_CESS_ADVAL_AMNT"]="";
                $r["STATE_CESS_NON_ADVAL_AMNT"]="";
                $r["OTHER_CHARGES"]="";
                $r["ITEM_TOTAL"]=$v['net_amount'];
                $r["BATCH_NAME"]=$v['batch_no']!=0 ? $v['batch_no'] : "";
                $r["BATCH_EXPIRY_DATE"]=$v['expiry_date'];
                $r["WARRENTY_DATE"]="";
                $r["TOTAL_TAXABLE_VALUE"]=$v['totalamt'];
                if ($v['local']==0) {
                    $r["SGST_AMOUNT1"]=ROUND($v['vat']/2,2);
                    $r["CGST_AMOUNT1"]=ROUND($v['vat']/2,2);
                    $r["IGST_AMOUNT1"]=0;
                } else {
                    $r["SGST_AMOUNT1"]=0;
                    $r["CGST_AMOUNT1"]=0;
                    $r["IGST_AMOUNT1"]=$v['vat']/2;
                }
                $r["CESS_AMOUNT1"]=$v['totalcess'];
                $r["STATE_CESS_AMOUNT"]="";
                $r["DISCOUNT_AMOUNT"]="0";
                $r["OTHER_CHARGES1"]=$v['tcsamt'];;
                $r["ROFF_AMOUNT"]=$v['add']-$v['less']+$v['round'];
                $r["TOTAL_INVOICE_VALUE"]=$v['total'];
                $r["Shipping_BillNo"]="";
                $r["Shipping_BillDt"]="";
                $r["port_"]="";
                $r["Refund_claim"]="";
                $r["foreign_Currency"]="";
                $r["Country_Code"]="";
                $r["Export_Duty_Amount"]="";
                $r["Trans_ID"]="";
                $r["Trans_Mode"]="";
                $r["Distance"]="";
                $r["Trans_Doc_No"]="";
                $r["Trans_Doc_Date"]="";
                $r["Vehicle_No"]="";
                $r["Vehicle_Type"]="";
                $res[] = $r;
                $l += 1;
                $prev=$v['invno'];
            }
            $this->sm->assign("data", $res);
        }
    }
    function saleseinvoice() {
	$_REQUEST['start_date'] = $_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("d/m/Y");
        $sdate = $this->getstartdate();
        $wcond = " s.date='$sdate' AND h.gstin!='' ";
	    $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
        $comp = @$_REQUEST['company'];
        if ($comp!=0) {
            $wcond .= " AND s.id_company='$comp' ";
        }
        $sql = "SELECT h.*, s.*  FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}company c ON s.id_company=c.id_company, `{$this->prefix}head` h
            WHERE $wcond AND h.id_head=s.id_head ORDER BY c.name, s.invno ";
        $inv = $this->m->sql_getall($sql);
        $this->sm->assign("data", $inv);
    }
    function saleseinvoice1() {
        $sdate = $this->getstartdate();
        $wcond = " s.id_sale=sd.id_sale AND s.date='$sdate' AND h.gstin!='' ";
        $wcond .= @$_REQUEST['id'] ?  " AND s.id_sale IN ({$_REQUEST['id']}) " : " ";
	    $sql = "SELECT id_company AS id, name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
        $comp = @$_REQUEST['company'];
        if ($comp!=0) {
            $wcond .= " AND s.id_company='$comp' ";
        }
        $sql = "SELECT h.*, s.*, sd.*, p.name as iname, p.hsncode, b.expiry_date
            FROM {$this->prefix}sale s, `{$this->prefix}head` h , `{$this->prefix}product` p, `{$this->prefix}saledetail` sd LEFT JOIN `{$this->prefix}batch` b
                ON sd.id_batch=b.id_batch
            WHERE $wcond AND sd.id_product=p.id_product AND h.id_head=s.id_head ORDER BY s.date, s.invno ";
        $inv = $this->m->sql_getall($sql);
        $prev = "";
        $l = 1;
	    $res = "";
        if (@$_REQUEST['submit']=="json") {
            foreach ($inv as $k => $v) {
                $r = "";
                if ($prev!=$v['invno']) {
                    $l=1;
                }
                $r['Version']="1.1";
                $r['TranDtls']['TaxSch']="GST";
                $r['TranDtls']['SupTyp']="B2B";
                $r['TranDtls']['IgstOnIntra']="N";
                $r['TranDtls']['RegRev']=null;
                $r['TranDtls']['EcmGstin']=null;

                $r['DocDtls']["Typ"]="INV";
                $r['DocDtls']["No"]=$v['invno'];
                $exd = date_create($v['date']);
                $d = date_format($exd, "d/m/Y");
                $r['DocDtls']["Dt"]=$d;

                $r['SellerDtls']["Gstin"]=$_SESSION['gstin'];
                $r['SellerDtls']["LglNm"]=trim($_SESSION['companyname']);
                $r['SellerDtls']["TrdNm"]=trim($_SESSION['companyname']);
                $r['SellerDtls']["Addr1"]=$_SESSION['address'];
                $r['SellerDtls']["Addr2"]=$_SESSION['address'];
                $r['SellerDtls']["Loc"]=$_SESSION['city'];
                $r['SellerDtls']["Pin"]=(int) $_SESSION['pincode'];
                $r['SellerDtls']["Stcd"]=$_SESSION['statecode'];
                $r['SellerDtls']["Ph"]=$_SESSION['phone'] ? $_SESSION['phone'] : null;
                $r['SellerDtls']["Em"]=$_SESSION['email'];

                $r['BuyerDtls']["Gstin"]=$v['gstin'];
                $r['BuyerDtls']["LglNm"]=$v['party_name'];
                $r['BuyerDtls']["TrdNm"]=$v['party_name'];
                $r['BuyerDtls']["Pos"]=$v['statecode'];
                $r['BuyerDtls']["Addr1"]=$v['address1'];
                $r['BuyerDtls']["Addr2"]=$v['address2']?$v['address2']:$v['address1'];
                $r['BuyerDtls']["Loc"]=$v['location'];
                $r['BuyerDtls']["Pin"]=(int) $v['pincode'];
                $r['BuyerDtls']["Stcd"]=$v['statecode'];
                $r['BuyerDtls']["Ph"]=null;
                $r['BuyerDtls']["Em"]=null;
                $r['DispDtls']=null;
                $r['ShipDtls']=null;

		        $r['ValDtls']['AssVal']=round($v['totalamt']-$v['discount'],2);
                if ($v['local']==0) {
                    $r['ValDtls']['IgstVal']=0;
                    $r['ValDtls']['CgstVal']=round($v['vat']/2,2);
                    $r['ValDtls']['SgstVal']=round($v['vat']/2,2);
                } else {
                    $r['ValDtls']['IgstVal']=round($v['vat'],2);
                    $r['ValDtls']['CgstVal']=0;
                    $r['ValDtls']['SgstVal']=0;
                }
                $r['ValDtls']['CesVal']=$v['totalcess'] ? round($v['totalcess'],2) : 0 ;
                $r['ValDtls']['StCesVal']=0;
                $r['ValDtls']['Discount']=0;
                $r['ValDtls']['OthChrg']=round($v['tcsamt'],2);
                $r['ValDtls']['RndOffAmt']=$v['add']-$v['less']+$v['round'];
                $r['ValDtls']['TotInvVal']=round($v['total'],2);
                $r['EwbDtls']=null;
                $i = $l;

                $r['ItemList'][$i-1]["SlNo"] = "$l";
                $r['ItemList'][$i-1]["PrdDesc"] = $v['iname'];
                $r['ItemList'][$i-1]["IsServc"] = "N";
                $r['ItemList'][$i-1]["HsnCd"] = $v['hsncode'];
                $r['ItemList'][$i-1]["Qty"] = round($v['qty'],0);
                $r['ItemList'][$i-1]["FreeQty"] = round($v['free'],0);
                $r['ItemList'][$i-1]["Unit"] = "PCS";
                $r['ItemList'][$i-1]["UnitPrice"] = round($v['amount']/$v['qty'],3);
                $r['ItemList'][$i-1]["TotAmt"] = round($v['amount'],2);
                $r['ItemList'][$i-1]["Discount"] = round($v['discount_amount1']+$v['discount_amount2']+$v['discount_amount3']+$v['discount_amount4'],2);
                $r['ItemList'][$i-1]["PreTaxVal"] = 0;
                $r['ItemList'][$i-1]["AssAmt"] = round($v['goods_amount'],2);
                $r['ItemList'][$i-1]["GstRt"] = round($v['tax_per'],0);
                if ($v['local']==0) {
                    $r['ItemList'][$i-1]["IgstAmt"] = 0;
                    $r['ItemList'][$i-1]["CgstAmt"] = round($v['tax_amount']/2,2);
                    $r['ItemList'][$i-1]["SgstAmt"] = round($v['tax_amount']/2,2);
                } else {
                    $r['ItemList'][$i-1]["IgstAmt"] = round($v['tax_amount'],2);
                    $r['ItemList'][$i-1]["CgstAmt"] = 0;
                    $r['ItemList'][$i-1]["SgstAmt"] = 0;
                }
                $r['ItemList'][$i-1]["CesRt"] = $v['cess']?round($v['cess']):0;
                $r['ItemList'][$i-1]["CesAmt"] = $v['cessamt']?round($v['cessamt']):0;
                $r['ItemList'][$i-1]["CesNonAdvlAmt"] = 0;
                $r['ItemList'][$i-1]["StateCesRt"] = 0;
                $r['ItemList'][$i-1]["StateCesAmt"] = 0;
                $r['ItemList'][$i-1]["StateCesNonAdvlAmt"] = 0;
                $r['ItemList'][$i-1]["OthChrg"] = 0;
                $r['ItemList'][$i-1]["TotItemVal"] = round($v['net_amount'],2);
                if ($i==1) {
                    $res[] = $r;
                } else {
                    $res[count($res)-1]['ItemList'][$i-1] = $r['ItemList'][$i-1];
                }
                $l += 1;
                $prev=$v['invno'];
            }
            $json = json_encode($res);
            //$this->pr($json);
            $this->sm->assign("json", $json);
        } else {
            foreach ($inv as $k => $v) {
                if ($prev!=$v['invno']) {
                    $l=1;
                }
                $r = "";
                $r["SUPPLY_TYPE_CODE"]="B2B";
                $r["REVERSE_CHARGE"]="";
                $r["EComm_GSTIN"]="";
                $r["IGST_ON_INTRA"]="NO";
                $r["DOCUMENT_TYPE"]="Tax Invoice";
                $r["DOCUMENT_NUMBER"]=$v['invno'];
                $r["DOCUMENT_DATE"]=$v['date'];
                $r["BUYER_GSTIN"]=$v['gstin'];
                $r["BUYER_LEGAL_NAME"]=$v['party_name'];
                $r["BUYER_TRADE_NAME"]=$v['party_name'];
                $r["BUYER_POS"]=$v['statecode'];
                $r["BUYER_ADD1"]=$v['address1'];
                $r["BUYER_ADD2"]=$v['address2']?$v['address2']:$v['address1'];
                $r["BUYER_LOCATION"]=$v['location'];
                $r["BUYER_PIN_CODE"]=(int) $v['pincode'];
                $r["BUYER_STATE"]=$v['state'];
                $r["BUYER_PHONE_NUMBER"]="";
                $r["BUYER_EMAIL_ID"]="";
                $r["DISPATCH_NAME"]="";
                $r["DISPATCH_ADD1"]="";
                $r["DISPATCH_ADD2"]="";
                $r["DISPATCH_LOCATION"]="";
                $r["DISPATCH_PIN_CODE"]="";
                $r["DISPATCH_STATE"]="";
                $r["SHIPPING_GSTIN"]="";
                $r["SHIPPING_LEGAL_NAME"]="";
                $r["SHIPPING_TRADE_NAME"]="";
                $r["SHIPPING_ADD1"]="";
                $r["SHIPPING_ADD2"]="";
                $r["SHIPPING_LOCATION"]="";
                $r["SHIPPING_PIN_CODE"]="";
                $r["SHIPPING_STATE"]="";

                $r["SL_NO"]=$l;
                $r["PRODUCT_DESCRIPTION"]=$v['iname'];
                $r["IS_SERVICE"]="NO";
                $r["HSN_CODE"]=$v['hsncode'];
                $r["BARCODE"]="";
                $r["QUANTITY"]=round($v['qty'],0);
                $r["FREE_QUANTITY"]= round($v['free'],0);
                $r["UNIT"]="PIECES";
                $r["UNIT_PRICE"]=round($v['amount']/$v['qty'],3);
                $r["GROSS_AMOUNT"]=round($v['amount'],2);
                $r["DISCOUNT"]=round($v['discount_amount1']+$v['discount_amount2']+$v['discount_amount3']+$v['discount_amount4'],2);
                $r["PER_TAX_VALUE"]="";
                $r["TAXABLE_VALUE"]=round($v['goods_amount'],2);
                $r["GST_perc"]=round($v['tax_per'],0);
                if ($v['local']==0) {
                    $r["IGST_AMOUNT"]=0;
                    $r["SGST_AMOUNT"]=round($v['tax_amount']/2,2);
                    $r["CGST_AMOUNT"]=round($v['tax_amount']/2,2);
                } else {
                    $r["IGST_AMOUNT"]=round($v['tax_amount'],2);
                    $r["SGST_AMOUNT"]=0;
                    $r["CGST_AMOUNT"]=0;
                }
                $r["CESS_perc"]=$v['cess'];
                $r["CESS_AMOUNT"]=$v['cessamt'];
                $r["CESS_NONADVAL_AMT"]="";
                $r["STATE_RATE_CESS_perc"]="";
                $r["STATE_CESS_ADVAL_AMNT"]="";
                $r["STATE_CESS_NON_ADVAL_AMNT"]="";
                $r["OTHER_CHARGES"]="";
                $r["ITEM_TOTAL"]=$v['net_amount'];
                $r["BATCH_NAME"]=$v['batch_no']!=0 ? $v['batch_no'] : "";
                $r["BATCH_EXPIRY_DATE"]=$v['expiry_date'];
                $r["WARRENTY_DATE"]="";
                $r["TOTAL_TAXABLE_VALUE"]=$v['totalamt'];
                if ($v['local']==0) {
                    $r["SGST_AMOUNT1"]=ROUND($v['vat']/2,2);
                    $r["CGST_AMOUNT1"]=ROUND($v['vat']/2,2);
                } else {
                    $r["IGST_AMOUNT1"]=$v['vat']/2;
                }
                $r["CESS_AMOUNT1"]=$v['totalcess'];
                $r["STATE_CESS_AMOUNT"]="";
                $r["DISCOUNT_AMOUNT"]="0";
                $r["OTHER_CHARGES1"]=$v['tcsamt'];;
                $r["ROFF_AMOUNT"]=$v['add']-$v['less']+$v['round'];
                $r["TOTAL_INVOICE_VALUE"]=$v['total'];
                $r["Shipping_BillNo"]="";
                $r["Shipping_BillDt"]="";
                $r["port_"]="";
                $r["Refund_claim"]="";
                $r["foreign_Currency"]="";
                $r["Country_Code"]="";
                $r["Export_Duty_Amount"]="";
                $r["Trans_ID"]="";
                $r["Trans_Mode"]="";
                $r["Distance"]="";
                $r["Trans_Doc_No"]="";
                $r["Trans_Doc_Date"]="";
                $r["Vehicle_No"]="";
                $r["Vehicle_Type"]="";
                $res[] = $r;
                $l += 1;
                $prev=$v['invno'];
            }
            //$this->pr($data);
            $this->sm->assign("data", $res);
        }
    }

}
?>
