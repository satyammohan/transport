<?php

class sreport extends common {

    function __construct() {
        $this->checklogin();
        $this->get_permission("sales", "REPORT");
        $this->table_prefix();
        parent:: __construct();
    }

    function _default() {
        echo "This function is not enabled...";
    }

    function getproducts() {
        $wcond = isset($_REQUEST['cid']) ? "id_company={$_REQUEST['cid']} AND " : "";
        $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
        $sql = "SELECT id_product AS value, name AS text FROM {$this->prefix}product WHERE $wcond name LIKE '%{$search}%' ORDER BY name LIMIT 30";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function representInvoiceDetail() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $sql = "SELECT r.name AS rname, sd.invno, sd.date, h.name AS pname, h.address1, p.name, sd.* 
            FROM `{$this->prefix}product` p, `{$this->prefix}represent` r,  `{$this->prefix}saledetail` sd LEFT JOIN `{$this->prefix}head` h ON sd.id_head=h.id_head
            WHERE sd.id_product=p.id_product AND sd.id_represent=r.id_represent AND `date` >= '$sdate' AND `date` <= '$edate' 
            ORDER BY sd.date, sd.invno, p.name";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }

    function getgroup() {
        $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
        $group = $_REQUEST['group'];
        $tbl = ($group == 1) ? "area" : ($group == 2 ? "represent" : "head");
        $sql = "SELECT id_{$tbl} AS value, name AS text FROM {$this->prefix}{$tbl} WHERE name LIKE '%{$search}%' ORDER BY name LIMIT 30";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function sreg() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $id_represent = isset($_REQUEST['represent']) ? $_REQUEST['represent'] : '0';
        if ($id_company != 0 && $id_represent != 0) {
            $wcond = "AND id_company='$id_company' AND id_represent='$id_represent'";
        } elseif ($id_company != 0 && $id_represent == 0) {
            $wcond = "AND id_company='$id_company'";
        } elseif ($id_company == 0 && $id_represent != 0) {
            $wcond = "AND id_represent='$id_represent'";
        } else {
            $wcond = "";
        }
        switch ($_REQUEST['option']) {
            case 1:
                if (isset($_REQUEST['itemdetails'])) {
                    $sql_detail = "SELECT sd.*, p.name FROM `{$this->prefix}saledetail` sd, `{$this->prefix}product` p
                        WHERE sd.id_product=p.id_product AND `date` >= '$sdate' AND `date` <= '$edate' ORDER BY date, invno LIMIT 2000";
                    $detail = $this->m->sql_getall($sql_detail, 1, "", "id_sale", "id_saledetail");
                    $this->sm->assign("detail", $detail);
                }
                $sql = "SELECT s.*, CAST(SUBSTRING(invno, POSITION('/' IN invno)+1) AS signed) AS myno, SUBSTRING(invno, 1, POSITION('/' IN invno)-1) AS pref, h.gstin, h.local FROM `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head  WHERE `date` >= '$sdate' AND `date` <= '$edate' " . " $wcond ORDER BY `date`, pref, myno, invno";

                break;
            case 2:
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`, COUNT(IF(cash=2,1,NULL)) AS cashbills, COUNT(IF(cash=1,1,NULL)) AS creditbills , SUM(IF(cash=2,total,0.00)) AS cashtotal, SUM(IF(cash=1,total,0.00)) AS credittotal FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' {$wcond} GROUP BY `date` ORDER BY `date`";
                break;
            case 3:
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`, SUM(IF(cash=2,total,0.00)) AS cashtotal, SUM(IF(cash=1,total,0.00)) AS credittotal,COUNT(IF(cash=2,1,NULL)) AS cashbills, COUNT(IF(cash=1,1,NULL)) AS creditbills  FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' {$wcond} GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, 
                SUM(IF(!h.local, 0, 1)*vat) AS igst,
                SUM(IF(!h.local, 1, 0)*(vat/2)) AS cgst,
                SUM(IF(!h.local, 1, 0)*(vat/2)) AS sgst,
                SUM(vat) AS vat, SUM(`total`) AS `total`, SUM(`totalamt`-p.discount) AS `totalamt`,
                SUM(`totalcess`) AS `totalcess`, SUM(`tcsamt`) AS `tcs`, SUM(`add`+`less`+`round`+`packing`) AS `other`,
                count(*) AS billn FROM `{$this->prefix}sale` p LEFT JOIN `{$this->prefix}head` h ON h.id_head=p.id_head
                WHERE `date` >= '$sdate' AND `date` <= '$edate' {$wcond} GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent  ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/sreg.tpl.html");
    }

    function vat() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $wcond = " `date` >= '$sdate' AND `date` <= '$edate' ";
        $taxbill = isset($_REQUEST['taxbill']) ? $_REQUEST['taxbill'] : '';
        if ($taxbill != "") {
            $wcond .= " AND taxbill='$taxbill'";
        }

        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}saledetail` s, `{$this->prefix}taxmaster` t 
            WHERE s.`date` >= '$sdate' AND s.`date` <= '$edate' AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
            case 1:
                $sql_detail = "SELECT id_sale, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount 
                    FROM `{$this->prefix}saledetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY id_sale, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "id_sale", "id_taxmaster");
                $sql = "SELECT s.* FROM `{$this->prefix}sale` s WHERE $wcond ORDER BY s.`date`, s.`invno`";
                break;
            case 2:
                $sql_detail = "SELECT `date`, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount
                    FROM `{$this->prefix}saledetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "date", "id_taxmaster");
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}sale` 
                    WHERE $wcond GROUP BY `date` ORDER BY `date` ";
                break;
            case 3:
                $sql_detail = "SELECT MONTHNAME(`date`) AS month, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount
                    FROM `{$this->prefix}saledetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "month", "id_taxmaster");
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, 
                        SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}sale` 
                    WHERE $wcond GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("detail", $detail);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/vat.tpl.html");
    }
    function gst_return() {
        $type = $_REQUEST['type'] = isset($_REQUEST['type']) ? $_REQUEST['type'] : '1';
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $wcond = " (s.date >= '$sdate' AND s.date <= '$edate') ";
        $tcond = ($type==1) ? '' : ($type==2 ? " AND length(h.gstin)=15 " : " AND length(h.gstin)!=15 ");
	
        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}taxmaster` t, `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
            WHERE $wcond $tcond AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
		case 1:
		$sql = "SELECT s.id_sreturn, s.id_taxmaster, SUM(s.goods_amount) AS goods_amount, 
		        SUM(s.tax_amount) AS tax_amount,SUM(s.cessamt) AS cessamt 
			FROM `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
			WHERE $wcond $tcond GROUP BY s.id_sreturn, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "id_sreturn", "id_taxmaster"));
		$sql = "SELECT s.id_sreturn AS id_sale, s.slno AS invno, s.date, s.challan_no, s.challan_date, 
			s.party_name, s.party_address, s.totalamt, s.vat, s.totalcess, s.add, s.less,
			s.round, 0 AS packing, s.total, h.local, h.gstin 
			FROM `{$this->prefix}sreturn` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
			WHERE $wcond $tcond ORDER BY s.date, s.slno";
		break;
		case 2:
		$sql = "SELECT s.date, s.id_taxmaster, SUM(s.goods_amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount, 
			SUM(s.cessamt) AS cessamt 
			FROM `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
			WHERE $wcond $tcond GROUP BY s.date, s.id_taxmaster";
		$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "date", "id_taxmaster"));
		$sql = "SELECT s.date, SUM(s.totalamt) AS totalamt, SUM(s.vat) AS vat, SUM(s.total) AS `total`,
			SUM(s.add) AS `add`,  SUM(s.less) AS less, SUM(s.round) AS round, 0 AS packing
			FROM `{$this->prefix}sreturn` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
			WHERE $wcond $tcond  GROUP BY s.date ORDER BY s.date ";
		break;
		case 3:
		$sql = "SELECT MONTHNAME(s.date) AS month,s.id_taxmaster,SUM(s.goods_amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount,
			SUM(s.cessamt) AS cessamt 
			FROM `{$this->prefix}sreturndetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
			WHERE $wcond $tcond GROUP BY 1, s.id_taxmaster";
		$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "month", "id_taxmaster"));
		$sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.totalamt) AS totalamt, 
			SUM(s.vat) AS vat, SUM(s.totalcess) AS totalcess, SUM(s.total) AS `total`,
			SUM(s.add) AS `add`,  SUM(s.less) AS less, SUM(s.round) AS round, 0 AS packing
			FROM `{$this->prefix}sreturn` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
			WHERE $wcond $tcond  GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
		break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "gst/gst_return.tpl.html");
    }
    function gst() {
        $type = $_REQUEST['type'] = isset($_REQUEST['type']) ? $_REQUEST['type'] : '1';
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $wcond = " (s.date >= '$sdate' AND s.date <= '$edate') ";
        $tcond = ($type==1) ? '' : ($type==2 ? " AND length(h.gstin)=15 " : " AND length(h.gstin)!=15 ");

        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}taxmaster` t, `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
            WHERE $wcond $tcond AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
		case 1:
			$sql = "SELECT s.id_sale, s.invno, s.id_taxmaster, SUM(s.goods_amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount,
				SUM(s.cessamt) AS cessamt 
				FROM `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond GROUP BY s.id_sale, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "id_sale", "id_taxmaster"));
			
			$sql = "SELECT s.id_sale, s.invno, s.date, s.party_name, s.party_address, s.totalamt, s.vat, s.totalcess, s.add, s.less,
				s.round, s.packing, s.total, h.local, h.gstin, s.discount 
				FROM `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond ORDER BY s.date, s.invno";
			break;
		case 2:
			$sql = "SELECT s.date, s.id_taxmaster, SUM(s.goods_amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount, 
				SUM(s.cessamt) AS cessamt 
				FROM `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond GROUP BY s.date, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "date", "id_taxmaster"));

			$sql = "SELECT s.date, SUM(s.totalamt) AS totalamt, SUM(s.vat) AS vat, SUM(s.total) AS `total`,
				SUM(s.add) AS `add`,  SUM(s.less) AS less, SUM(s.round) AS round, SUM(s.packing) AS packing
				FROM `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
				WHERE $wcond $tcond  GROUP BY s.date ORDER BY s.date ";
			break;
		case 3:
			$sql = "SELECT MONTHNAME(s.date) AS month, s.id_taxmaster, SUM(s.goods_amount) AS goods_amount, SUM(s.tax_amount) AS tax_amount,
				SUM(s.cessamt) AS cessamt 
				FROM `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond GROUP BY 1, s.id_taxmaster";
			$this->sm->assign("detail", $this->m->sql_getall($sql, 1, "", "month", "id_taxmaster"));

			$sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.totalamt) AS totalamt, 
				SUM(s.vat) AS vat, SUM(s.totalcess) AS totalcess, SUM(s.total) AS `total`,
				SUM(s.add) AS `add`,  SUM(s.less) AS less, SUM(s.round) AS round, SUM(s.packing) AS packing
				FROM `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
				WHERE $wcond $tcond  GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
			break;
		}
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "gst/salegst.tpl.html");
    }


    function gsto() {
// goods_amount is changed to amount
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $wcond = " `date` >= '$sdate' AND `date` <= '$edate' ";

        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}saledetail` s, `{$this->prefix}taxmaster` t 
            WHERE s.`date` >= '$sdate' AND s.`date` <= '$edate' AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
            case 1:
                $sql_detail = "SELECT id_sale, invno, id_taxmaster, SUM(amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt 
                    FROM `{$this->prefix}saledetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY id_sale, id_taxmaster";
                $this->sm->assign("detail", $this->m->sql_getall($sql_detail, 1, "", "id_sale", "id_taxmaster"));
                $sql = "SELECT s.id_sale, s.invno, s.date, s.party_name, s.party_address, s.totalamt, s.vat, s.totalcess, s.add, s.less, s.round, s.packing, s.total, h.local, h.gstin 
                    FROM `{$this->prefix}sale` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                    WHERE $wcond ORDER BY s.`date`, s.`invno`";
                break;
            case 2:
                $sql_detail = "SELECT `date`, id_taxmaster, SUM(amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt 
                    FROM `{$this->prefix}saledetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "date", "id_taxmaster");
                $this->sm->assign("detail", $detail);
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}sale` 
                    WHERE $wcond GROUP BY `date` ORDER BY `date` ";
                break;
            case 3:
                $sql_detail = "SELECT MONTHNAME(`date`) AS month, id_taxmaster, SUM(amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt 
                    FROM `{$this->prefix}saledetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "month", "id_taxmaster");
                $this->sm->assign("detail", $detail);
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, 
                        SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}sale` 
                    WHERE $wcond GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "gst/salegst.tpl.html");
    }

    function vatdet() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $wcond = " `date` >= '$sdate' AND `date` <= '$edate' ";
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT taxbill, MIN(invno) AS min_invno, MAX(invno) AS max_invno, count(*) AS bills, SUM(totalamt) AS totalamt, 
                    SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(total) AS total
                FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY taxbill";
                break;
            case 2:
                $sql = "SELECT invno, date, party_name, party_vatno, packing+`add`-`less`+`round` AS other, vat, totalcess, total
		FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' AND taxbill ORDER BY date, id_sale";
                break;
            case 3:
                $sql = "SELECT invno, date, party_name, party_vatno, packing+`add`-`less`+`round` AS other, vat, totalces, total
		FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' AND !taxbill ORDER BY date, id_sale";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function error() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT s.id_sale, s.invno, s.date, s.vat, s.packing+s.add-s.less+s.round+s.tcsamt AS other, s.total, SUM(sd.amount) AS amount, 
                SUM(sd.cessamt) AS cessamt, SUM(sd.discount_amount1) AS d1, SUM(sd.discount_amount2) AS d2, 
                SUM(sd.discount_amount3) AS d3, SUM(sd.discount_amount4) AS d4, 
                SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.net_amount) AS net_amount
            FROM `{$this->prefix}sale` s, `{$this->prefix}saledetail` sd
            WHERE s.id_sale=sd.id_sale AND s.date >= '$sdate' AND s.date <= '$edate'
            GROUP BY s.id_sale ORDER BY s.date, s.invno";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function area() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $area1 = isset($_REQUEST['area']) ? $_REQUEST['area'] : '0';
        if (is_array($area1)) {
            $id_area = implode(",", $area1);
        } else {
            $id_area = 0;
        }
        if ($id_area != 0) {
            $wcond = "AND s.id_area IN ($id_area)";
        } else {
            $wcond = "";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT s.*, a.name AS areaname FROM {$this->prefix}sale s, {$this->prefix}area a  
                    WHERE  s.id_area=a.id_area AND s.date >= '$sdate' AND s.date <= '$edate' " . " {$wcond} ORDER BY areaname  ";
                break;
            case 2:
                $sql = "SELECT s.date AS `date`, a.name AS areaname, SUM(s.vat) AS vat, SUM(totalcess) AS totalcess, SUM(s.total) AS `total`, COUNT(s.invno) AS bills, s.id_area, s.id_sale AS id
                        FROM {$this->prefix}sale s, {$this->prefix}area a 
                        WHERE s.id_area=a.id_area AND s.date >= '$sdate' AND s.date <= '$edate' {$wcond} GROUP BY date  ORDER BY areaname,date DESC ";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, "", "id_area", "id_sale");
        if ($_REQUEST['ltrkg']) {
	   $sql = "SELECT s.id_sale, ((s.qty+s.free)*p.case) AS ltrkg FROM {$this->prefix}saledetail s, {$this->prefix}product p
		WHERE s.date >= '$sdate' AND s.date <= '$edate' AND s.id_product=p.id_product GROUP BY s.id_sale";
           $ltr = $this->m->sql_getall($sql, 2, "ltrkg", "id_sale");
           $this->sm->assign("ltrkg", $ltr);
        }
        $sql = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $area = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("area", $area);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/area.tpl.html");
    }

    function represent() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        if (isset($_REQUEST['represent'])) {
            $id_represent = implode(",", $_REQUEST['represent']);
            $wcond = "AND s.id_represent IN ($id_represent)";
        } else {
            $wcond = "";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT s.*, r.name AS rname FROM {$this->prefix}sale s, {$this->prefix}represent r  
                        WHERE s.id_represent=r.id_represent AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY rname";
                $res = $this->m->sql_getall($sql, 1, "", "id_represent", "id_sale");
                break;
            case 2:
                $sql = "SELECT s.date, r.name AS rname, a.name AS aname, SUM(s.vat) AS vat, SUM(totalcess) AS totalcess, SUM(s.total) AS `total`, COUNT(s.invno) AS bills, s.id_represent, s.id_sale
                        FROM {$this->prefix}sale s, {$this->prefix}represent r, {$this->prefix}area a
                        WHERE s.id_area=a.id_area AND s.id_represent=r.id_represent AND s.date>='$sdate' AND s.date<='$edate' {$wcond}
                            GROUP BY id_represent, date ORDER BY r.name, date ";
                    $res = $this->m->sql_getall($sql, 1, "", "id_represent", "id_sale");
                break;
            case 3:
                $sql = "SELECT r.name AS rname, SUM(s.vat) AS vat, SUM(totalcess) AS totalcess, SUM(s.total) AS `total`, COUNT(s.invno) AS bills, s.id_represent
                        FROM {$this->prefix}sale s, {$this->prefix}represent r 
                        WHERE s.id_represent=r.id_represent AND s.date>='$sdate' AND s.date<='$edate' {$wcond}
                        GROUP BY id_represent ORDER BY r.name ";
                $res = $this->m->sql_getall($sql, 1, "", "id_represent");
                break;
        }
        $opt6 = "SELECT id_represent AS id, name FROM {$this->prefix}represent ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
        $this->sm->assign("data", $res);

        if (isset($_REQUEST['items'])) {
            $sql = "SELECT s.id_product, p.name, p.case, p.weight, s.rate, SUM(s.qty) AS qty, SUM(s.free) AS free, SUM(s.amount) AS amount
                    FROM {$this->prefix}saledetail s, {$this->prefix}product p
                    WHERE s.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' " . " {$wcond} GROUP BY s.id_product ORDER BY p.name ";
            $items = $this->m->sql_getall($sql);
            $this->sm->assign("saleitem", $items);
        }
    }

    function company() {
        $_REQUEST['disc'] = $_REQUEST['spl'] = $_REQUEST['cash'] = $_REQUEST['vdisc'] = $_REQUEST['vat'] = $_REQUEST['address'] = 1;
        $opt4 = "SELECT id_company, name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($opt4, 2, "name", "id_company"));
    }

    function companyrep() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : $_SESSION['edate']);

        $itemids = isset($_REQUEST['itemids']) ? implode(',', $_REQUEST['itemids']) : "";
        $wcond = ($itemids != "") ? " AND s.id_product IN ($itemids) " : "";
        $group = $_REQUEST['group'];
        if ($group != 0) {
            $tbl = ($group == 1) ? "area" : ($group == 2 ? "represent" : "head");
            $grouptitle = ($group == 1) ? "area" : ($group == 2 ? "representative" : "party");
            $this->sm->assign("grouptitle", $grouptitle);
            $groupids = isset($_REQUEST['groupids']) ? implode(',', $_REQUEST['groupids']) : "";
            $wcond .= ($groupids != "") ? " AND s.id_{$tbl} IN ($groupids) " : "";
            $field = " z.name AS groupname ";
            $order = " ORDER BY z.name, p.name, s.date ";
            $join = "LEFT JOIN `{$this->prefix}{$tbl}` z ON s.id_{$tbl}=z.id_{$tbl}";
        } else {
            $field = " '' AS groupname ";
            $order = " ORDER BY p.name, s.date ";
            $join = "";
        }
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : "";
        $wcond .= ($id_company != " ") ? " AND p.id_company='$id_company' " : "";

        $taxbill = isset($_REQUEST['taxbill']) ? $_REQUEST['taxbill'] : "";
        $wcond .= ($taxbill != "") ? " AND s.taxbill='$taxbill'" : "";

        $tran_type = isset($_REQUEST['tran_type']) ? $_REQUEST['tran_type'] : "";
        $wcond .= ($tran_type != "") ? " AND s.cash='$tran_type'" : "";

        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT s.*, h.name AS pname, h.address1, p.name, p.short, {$field}
                FROM `{$this->prefix}product` p, `{$this->prefix}saledetail` s
                LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head
                {$join}
                WHERE p.id_product=s.id_product AND `date`>='$sdate' AND `date`<='$edate' {$wcond} {$order}";
                break;
            case 2:
                if ($group != 0)
                    $group = " GROUP BY s.id_{$tbl}, s.id_product ";
                else
                    $group = " GROUP BY s.id_product ";
                $sql = "SELECT s.id_product, SUM(s.qty) AS qty, SUM(s.free) AS free, 
                    SUM(s.discount_amount1) AS discount_amount1, SUM(s.discount_amount2) AS discount_amount2, 
                    SUM(s.discount_amount3) AS discount_amount3, SUM(s.discount_amount4) AS discount_amount4, 
                    SUM(s.tax_amount) AS tax_amount, SUM(s.amount) AS amount, p.name, p.short, {$field}
                FROM `{$this->prefix}product` p, `{$this->prefix}saledetail` s
                {$join}
                WHERE p.id_product=s.id_product AND `date`>='$sdate' AND `date`<='$edate' {$wcond} {$group} {$order} ";
                break;
            case 3:
                if ($group != 0)
                    $group = " GROUP BY s.id_{$tbl}, s.id_company ";
                else
                    $group = " GROUP BY s.id_company ";
                $sql = "SELECT s.id_company, SUM(s.qty) AS qty, SUM(s.free) AS free, 
                    SUM(s.discount_amount1) AS discount_amount1, SUM(s.discount_amount2) AS discount_amount2, 
                    SUM(s.discount_amount3) AS discount_amount3, SUM(s.discount_amount4) AS discount_amount4, 
                    SUM(s.tax_amount) AS tax_amount, SUM(s.amount) AS amount, p.name, {$field}
                FROM `{$this->prefix}company` p, `{$this->prefix}saledetail` s
                {$join}
                WHERE p.id_company=s.id_company AND `date`>='$sdate' AND `date`<='$edate' {$wcond} {$group} {$order} ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/company{$_REQUEST['option']}.tpl.html");
    }

    function companyreport() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : $_SESSION['edate']);
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '';
        $products = isset($_REQUEST['products']) ? implode(",", $_REQUEST['products']) : '';
        $wcond = $id_company != " " ? " AND p.id_company='$id_company' " : "";
        /* $taxbill = isset($_REQUEST['taxbill']) ? $_REQUEST['taxbill'] : '';
          if ($taxbill != "") {
          $wcond .= " AND s.taxbill='$taxbill'";
          }
          $tran_type = isset($_REQUEST['tran_type']) ? $_REQUEST['tran_type'] : '';
          if ($tran_type != "") {
          $wcond .= " AND s.cash='$tran_type'";
          } */

        if ($products) {
            $sql = "SELECT s.*, p.name, p.short, h.name AS pname, h.address1 
                    FROM `{$this->prefix}product` p, `{$this->prefix}saledetail` s
                    LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head
                    WHERE p.id_product=s.id_product AND `date`>='$sdate' AND `date`<='$edate'
                    {$wcond} AND p.id_product IN ($products) ORDER BY p.name, s.`date`  ";
        } else {
            $sql = "SELECT s.*, p.name, p.short, h.name AS pname, h.address1 
                    FROM `{$this->prefix}product` p, `{$this->prefix}saledetail` s
                    LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head
                    WHERE p.id_product=s.id_product AND `date`>='$sdate' AND `date`<='$edate'
                    {$wcond}  ORDER BY p.name, s.`date`  ";
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/company1.tpl.html");
    }

    function ccompair() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : "{$_SESSION['sdate']}");
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : "{$_SESSION['edate']}");
        $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(`total`) AS `total`, party_name,id_head  FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`), id_head ORDER BY party_name, `date`  ";
        $res = $this->m->sql_getall($sql, 1, "", "party_name", "month");
        $month = array("April" => "April", "May" => "May", "June" => "June", "July" => "July",
            "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December", "January" => "January", "February" => "February", "March" => "March");
        $this->sm->assign("month", $month);
        $this->sm->assign("data", $res);
        $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(`total`) AS `total` FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date`  ";
        $res = $this->m->sql_getall($sql, 1, "", "month");
        $this->sm->assign("summary", $res);
        $this->sm->assign("page", "sreport/ccompair.tpl.html");
    }

    function itemrep() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $paymod = isset($_REQUEST['cash']) ? $_REQUEST['cash'] : '0';
        $wcond = '';
        if ($id_company != 0) {
            $wcond .= " AND s.id_company='$id_company' ";
            $id_item = isset($_REQUEST['id_item']) ? $_REQUEST['id_item'] : '0';
        }
        if (isset($_REQUEST['itemids'])) {
            $itemids = implode(',', $_REQUEST['itemids']);
            $wcond .= " AND sd.id_product IN ($itemids) ";
        }
        if ($paymod != 0) {
            $wcond .= " AND s.cash='$paymod' ";
        }
        if (@$_REQUEST['sreturn']) {
            switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT -sd.id_sreturndetail AS id_saledetail, sd.date, sd.rate, -sd.qty AS qty, -sd.free AS free, -sd.amount AS amount, s.party_name AS party, p.name AS iname,  p.short, challan_no AS myinvno
                        FROM {$this->prefix}sreturn s, {$this->prefix}sreturndetail sd, {$this->prefix}product p
                        WHERE s.id_sreturn=sd.id_sreturn AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond}
                        UNION ALL
                        SELECT sd.id_saledetail, sd.date, sd.rate, sd.qty, sd.free, sd.amount, s.party_name AS party, p.name AS iname,  p.short, CAST(substring(s.invno, 3) as decimal(11)) AS myinvno
                        FROM {$this->prefix}sale s, {$this->prefix}saledetail sd, {$this->prefix}product p
                        WHERE s.id_sale=sd.id_sale AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY iname, date, myinvno";
                break;
            case 3:
                $sql = "SELECT sd.id_sreturndetail AS id_saledetail, sd.date, p.id_company, SUM(-sd.qty) AS qty, SUM(-sd.free) AS free, SUM(-sd.amount) AS amount, p.name AS iname, p.short AS short
                        FROM {$this->prefix}sreturndetail sd, {$this->prefix}product p
                        WHERE sd.id_product=p.id_product AND sd.date>='$sdate' AND sd.date<='$edate' {$wcond} GROUP BY sd.id_product
                        UNION ALL
                        SELECT sd.id_saledetail, sd.date, p.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.name AS iname, p.short AS short
                        FROM {$this->prefix}saledetail sd, {$this->prefix}product p
                        WHERE sd.id_product=p.id_product AND sd.date>='$sdate' AND sd.date<='$edate' {$wcond} GROUP BY sd.id_product ORDER BY iname";
                break;
            }
        } else {
            switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT sd.*, s.party_name AS party, p.name AS iname,  p.short, CAST(substring(s.invno, 3) as decimal(11)) AS myinvno
                        FROM {$this->prefix}sale s, {$this->prefix}saledetail sd, {$this->prefix}product p
                        WHERE s.id_sale=sd.id_sale AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY iname, sd.date,myinvno";
                break;
            case 3:
                $sql = "SELECT sd.id_saledetail, sd.date, p.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.name AS iname, p.short AS short
                        FROM {$this->prefix}saledetail sd, {$this->prefix}product p
                        WHERE sd.id_product=p.id_product AND sd.date>='$sdate' AND sd.date<='$edate' {$wcond} GROUP BY sd.id_product ORDER BY iname";
                break;
            }
        }
        $res = $this->m->sql_getall($sql, 1, "", "iname", "id_saledetail");
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/item{$_REQUEST['option']}.tpl.html");
    }

    function item() {
        $opt4 = "SELECT id_company AS id, name FROM {$this->prefix}company ORDER BY name";
        $comp = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $comp);
    }

    function partyitem() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $wcond = '';
        if ($id_company != 0) {
            $wcond .= " AND s.id_company='$id_company' ";
        }
        $sql = "SELECT s.party_name AS party, p.name AS iname,  p.short, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.case
                FROM {$this->prefix}sale s, {$this->prefix}saledetail sd, {$this->prefix}product p
                WHERE s.id_sale=sd.id_sale AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond}
                GROUP BY s.party_name, s.party_address, sd.id_product 
                ORDER BY party_name, iname";
        $res = $this->m->sql_getall($sql);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("data", $res);
    }

    function party() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $head = isset($_REQUEST['party2']) ? $_REQUEST['party2'] : '0';
        if (is_array($head)) {
            $wcond = "AND s.id_head IN (" . implode(",", $head) . ")";
        } else {
            $wcond = "";
        }
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond .= "AND s.id_company='$id_company'";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT s.id_sale, s.id_head, s.invno, s.date, s.vat, s.totalcess, s.total, s.party_name AS party, s.party_address, s.party_address1 FROM {$this->prefix}sale s
                        WHERE s.date >= '$sdate' AND s.date <= '$edate' " . " {$wcond} ORDER BY party, s.date  ";
                $res = $this->m->sql_getall($sql, 1, "", "id_head", "id_sale");
                break;
            case 2:
                $sql = "SELECT DISTINCT MONTHNAME(date) AS month FROM {$this->prefix}sale s WHERE date>='$sdate' AND date<='$edate' {$wcond} ORDER BY date ";
                $res = $this->m->sql_getall($sql, 1, "month");
                $this->sm->assign("month", $res);
                $sql = "SELECT DISTINCT id_head, party_name, party_address, party_address1 FROM {$this->prefix}sale s WHERE date>='$sdate' AND date<='$edate' {$wcond} ORDER BY date ";
                $res = $this->m->sql_getall($sql, 1, "", "id_head");
                $this->sm->assign("salesparty", $res);
                $sql = "SELECT MONTHNAME(s.date) AS month, YEAR(s.date) AS year, SUM(s.total) AS `total`, s.id_head
                        FROM {$this->prefix}sale s  WHERE s.date >= '$sdate' AND s.date <= '$edate' {$wcond}
                        GROUP BY s.id_head, MONTH(s.date), YEAR(s.date) ORDER BY party_name, s.date ";
                $res = $this->m->sql_getall($sql, 1, "", "id_head", "month");
                break;
            case 3:
                $sql = "SELECT s.party_name AS party, s.party_address, s.party_address1, SUM(s.vat) AS vat, SUM(totalcess) AS totalcess, SUM(s.total) AS `total`, SUM(IF(s.cash=2,total,0.00)) AS cashtotal, SUM(IF(s.cash=1,total,0.00)) AS credittotal,COUNT(IF(s.cash=2,1,NULL)) AS cashbills, COUNT(IF(s.cash=1,1,NULL)) AS creditbills, s.id_sale, s.id_head
                        FROM {$this->prefix}sale s WHERE s.date >= '$sdate' AND s.date <= '$edate' {$wcond}
                        GROUP BY s.id_head ORDER BY party ";
                $res = $this->m->sql_getall($sql, 1, "", "id_head", "id_sale");
                break;
        }
        $this->sm->assign("data", $res);
        $opt2 = "SELECT id_head AS id,name FROM {$this->prefix}head WHERE debtor ORDER BY name";
        $party = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("party", $party);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);

        if (isset($_REQUEST['items'])) {
            $sql = "SELECT sd.id_product, p.name, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount
                    FROM {$this->prefix}saledetail sd, {$this->prefix}product p, {$this->prefix}sale s
                    WHERE sd.id_product=p.id_product AND s.id_sale=sd.id_sale AND s.date>='$sdate' AND s.date<='$edate' " . " {$wcond} GROUP BY sd.id_product ORDER BY p.name ";
            $items = $this->m->sql_getall($sql);
            $this->sm->assign("saleitem", $items);
        }
    }

    function zone() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        if (isset($_REQUEST['zone'])) {
            $id_zone = implode(",", $_REQUEST['zone']);
            $sql = "SELECT id_area AS id FROM {$this->prefix}area WHERE id_zone IN ($id_zone)";
        } else {
            $sql = "SELECT id_area AS id FROM {$this->prefix}area";
        }
        $id_area = implode(",", $this->m->sql_getall($sql, 2, "id", "id"));
        $wcond = " AND s.id_area IN ($id_area) ";
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT s.*, a.name AS aname, a.id_zone AS id_zone, z.name AS zname 
                        FROM {$this->prefix}sale s, {$this->prefix}area a, {$this->prefix}zone z 
                        WHERE a.id_zone=z.id_zone AND s.id_area=a.id_area AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY zname ";
                break;
            case 2:
                $sql = "SELECT s.date AS `date`, a.name AS aname, a.id_zone, z.name AS zname, SUM(s.vat) AS vat, SUM(totalcess) AS totalcess, SUM(s.total) AS `total`, COUNT(s.invno) AS bills, s.id_area AS id_area, s.id_sale
                        FROM {$this->prefix}sale s,{$this->prefix}area a, {$this->prefix}zone z
                        WHERE a.id_zone=z.id_zone AND s.id_area=a.id_area AND s.date>='$sdate' AND s.date<='$edate' {$wcond} GROUP BY date ORDER BY zname, date DESC ";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, " ", "id_zone", "id_sale");
        $this->sm->assign("data", $res);
        $sql = "SELECT id_zone AS id, name FROM {$this->prefix}zone ORDER BY name";
        $this->sm->assign("zone", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "sreport/zone.tpl.html");
    }

    function discount() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '';
        $pcond = $id_company ? " AND i.id_company = {$id_company} " : "";
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        if (!($_REQUEST['free'] || $_REQUEST['disc'] || $_REQUEST['spl'] || $_REQUEST['cd'] || $_REQUEST['vatdisc'])) {
            $_REQUEST['free'] = $_REQUEST['disc'] = $_REQUEST['spl'] = $_REQUEST['cd'] = $_REQUEST['vatdisc'] = 'on';
        }
        if ($_REQUEST['free'])
            $wcond[] = ' free != 0 ';
        if ($_REQUEST['disc'])
            $wcond[] = ' discount_amount1 != 0 ';
        if ($_REQUEST['spl'])
            $wcond[] = ' discount_amount2 != 0 ';
        if ($_REQUEST['cd'])
            $wcond[] = ' discount_amount3 != 0 ';
        if ($_REQUEST['vatdisc'])
            $wcond[] = ' discount_amount4 != 0 ';
        $wc = implode(" OR ", $wcond);
        $sql = "SELECT s.party_name, s.party_address, s.party_address1, sd.*, i.name "
                . "FROM {$this->prefix}sale s, 
              (SELECT * FROM {$this->prefix}saledetail WHERE date>='$sdate' AND date<='$edate' AND ({$wc})) sd, 
              {$this->prefix}product i "
                . "WHERE s.date>='$sdate' AND s.date<='$edate' AND s.id_sale=sd.id_sale AND sd.id_product=i.id_product {$pcond}";
        $this->sm->assign("data", $this->m->query($sql));
    }

    function companydetail() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : "{$_SESSION['sdate']}");
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : "{$_SESSION['edate']}");
        $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(`total`) AS `total`, party_name,id_head  FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`), id_head ORDER BY party_name, `date`  ";
        $res = $this->m->sql_getall($sql, 1, "", "party_name", "month");
        $month = array("April" => "April", "May" => "May", "June" => "June", "July" => "July",
            "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December", "January" => "January", "February" => "February", "March" => "March");
        $this->sm->assign("month", $month);
        $this->sm->assign("data", $res);
        $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(`total`) AS `total` FROM `{$this->prefix}sale` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date`  ";
        $res = $this->m->sql_getall($sql, 1, "", "month");
        $this->sm->assign("summary", $res);
        $this->sm->assign("page", "sreport/company.tpl.html");
    }
    function salesana() {
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
    }

    function salesanarep() {
        $sdate = $this->format_date($_REQUEST['start_date']);
        $edate = $this->format_date($_REQUEST['end_date']);
        switch ($_REQUEST['group']) {
            case 1: //area
                $gcode = " GROUP BY s.id_area, s.id_product ";
                $from = " `{$this->prefix}company` c, `{$this->prefix}product` p, `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}area` h ON s.id_area=h.id_area ";
                break;
            case 2: //rep
                $gcode = " GROUP BY s.id_represent, s.id_product ";
                $from = " `{$this->prefix}company` c,  `{$this->prefix}product` p, `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}represent` h ON s.id_represent=h.id_represent ";
                break;
            case 3: //party
                $gcode = " GROUP BY s.id_head, s.id_product ";
                $from = "  `{$this->prefix}company` c, `{$this->prefix}product` p, `{$this->prefix}saledetail` s LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head ";
                break;
        }
        $comp = isset($_REQUEST['company']) ? $_REQUEST['company'] : "";
        $wcond = ($comp) ? " AND c.id_company='$comp'" : "";
        $sql = "SELECT h.name, p.name AS pname, p.case, MONTHNAME(date) AS m1, MONTH(date) AS m, YEAR(date) AS y, s.id_product, 
		    SUM((s.qty+s.free)*p.case) AS `case`, SUM(s.qty+s.free) AS qty, SUM(s.free) AS free,  SUM(s.net_amount) AS total 
            FROM $from
	        WHERE `date`>=  '$sdate' AND `date`<='$edate' AND s.id_product=p.id_product AND p.id_company=c.id_company $wcond $gcode,m,y ORDER BY h.name, p.name,y,m ";
        $res = $this->m->sql_getall($sql);
        if ($res) {
            foreach($res as $v)  {
                $r[$v['name']][$v['pname']][$v['m1']] = $v;
                $str = $v['m']+$v['y']*12;
                $m[$str] = $v['m1'];
            }
            unset($res);
            ksort($m);
            $this->sm->assign("data", $r);
            $this->sm->assign("month", $m);
        }
    }
    function ltrkg() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $head = isset($_REQUEST['party2']) ? $_REQUEST['party2'] : '0';
        if (is_array($head)) {
            $wcond = "AND sd.id_head IN (" . implode(",", $head) . ")";
        } else {
            $wcond = "";
        }
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        if ($id_company != 0) {
            $wcond .= "AND p.id_company='$id_company'";
        }
        $id_area = isset($_REQUEST['area']) ? $_REQUEST['area'] : '0';
        if ($id_area != 0) {
            $wcond .= "AND h.id_area='$id_area'";
        }
        $sql = "SELECT SUM(sd.amount) AS amount, SUM(sd.qty) AS qty, SUM(sd.free) AS free,
                        sd.id_head, sd.id_product, p.name, h.name AS party_name, p.case
            FROM `{$this->prefix}product`p, `{$this->prefix}saledetail` sd LEFT JOIN `{$this->prefix}head` h ON sd.id_head=h.id_head
            WHERE (sd.date >= '$sdate' AND sd.date <= '$edate') AND sd.id_product=p.id_product {$wcond}
            GROUP BY sd.id_head, sd.id_product ORDER BY h.name, p.name";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $party = $this->m->sql_getall("SELECT id_head AS id,name FROM {$this->prefix}head WHERE debtor ORDER BY name", 2, "name", "id");
        $this->sm->assign("party", $party);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt4 = "SELECT id_area AS id,name FROM {$this->prefix}area WHERE status=0 ORDER BY name";
        $area = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("area", $area);
    }
    function tcs() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $sql = "SELECT s.id_sale, s.invno, h.name, s.date, s.total, s.tcsamt, h.address1, h.gstin, h.tanno
            FROM `{$this->prefix}sale` s, `{$this->prefix}head` h
            WHERE s.id_head=h.id_head AND (s.date >= '$sdate' AND s.date <= '$edate') AND s.tcsamt>0
            ORDER BY s.date, s.invno";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }
}
?>

