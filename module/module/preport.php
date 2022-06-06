<?php

class preport extends common {

    function __construct() {
        $this->checklogin();
        $this->get_permission("purchase", "REPORT");
        $this->table_prefix();
        parent:: __construct();
    }

    function _default() {
        echo "This function is not enabled...";
    }

    function preg() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
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
            case 4:
            case 1:
                $sql = "SELECT p.*, h.gstin, h.local FROM `{$this->prefix}purchase` p LEFT JOIN `{$this->prefix}head` h ON h.id_head=p.id_head
                        WHERE `date` >= '$sdate' AND `date` <= '$edate' " . " $wcond";
                break;
            case 2:
                $sql = "SELECT `date`, SUM(vat) AS vat, SUM(`total`) AS `total` , SUM(IF(cash=1,total,0.00)) AS cashtotal, SUM(IF(cash=2,total,0.00)) AS credittotal, COUNT(IF(cash=1,1,NULL)) AS cashbills, COUNT(IF(cash=2,1,NULL)) AS creditbills ,count(*) AS billn   FROM `{$this->prefix}purchase` WHERE `date` >= '$sdate' AND `date` <= '$edate' {$wcond} GROUP BY `date` ORDER BY `date`";
                break;
            case 3:
		$sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, 
                SUM(IF(!h.local, 0, 1)*vat) AS igst,
                SUM(IF(!h.local, 1, 0)*(vat/2)) AS cgst,
                SUM(IF(!h.local, 1, 0)*(vat/2)) AS sgst,
                SUM(vat) AS vat, SUM(`total`) AS `total`, SUM(`totalamt`-p.discount) AS `totalamt`,
                SUM(`totalcess`) AS `totalcess`, SUM(`tcsamt`) AS tcs, SUM(`add`-`less`+`round`+`packing`) AS `other`,
                count(*) AS billn FROM `{$this->prefix}purchase` p LEFT JOIN `{$this->prefix}head` h ON h.id_head=p.id_head
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
        $this->sm->assign("page", "preport/preg.tpl.html");
    }

    function gst() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " `date` >= '$sdate' AND `date` <= '$edate' ";

        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}purchasedetail` s, `{$this->prefix}taxmaster` t 
            WHERE s.`date` >= '$sdate' AND s.`date` <= '$edate' AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
            case 1:
                $sql_detail = "SELECT id_purchase, no, date, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt
                    FROM `{$this->prefix}purchasedetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY id_purchase, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "id_purchase", "id_taxmaster");
                $sql = "SELECT s.*, h.local, h.gstin FROM `{$this->prefix}purchase` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head 
                    WHERE $wcond ORDER BY s.`date`, s.`no`";
                break;
            case 2:
                $sql_detail = "SELECT `date`, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount, 
			SUM(cessamt) AS cessamt
                    FROM `{$this->prefix}purchasedetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "date", "id_taxmaster");
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing, SUM(tcsamt) AS tcsamt
                        FROM `{$this->prefix}purchase` 
                    WHERE $wcond GROUP BY `date` ORDER BY `date` ";
                break;
            case 3:
                $sql_detail = "SELECT MONTHNAME(`date`) AS month, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount, SUM(cessamt) AS cessamt
                    FROM `{$this->prefix}purchasedetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "month", "id_taxmaster");
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, 
                        SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing, SUM(tcsamt) AS tcsamt
                        FROM `{$this->prefix}purchase` 
                    WHERE $wcond GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("detail", $detail);
        $this->sm->assign("file", "purchase");
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "gst/purcgst.tpl.html");
    }

    function vat() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $wcond = " `date` >= '$sdate' AND `date` <= '$edate' ";
        $taxbill = isset($_REQUEST['taxbill']) ? $_REQUEST['taxbill'] : '';
        if ($taxbill != "") {
            $wcond .= " AND taxbill='$taxbill'";
        }
        $sql = "SELECT DISTINCT s.id_taxmaster AS id, t.name, t.tax_per, 0000000000000.00 AS gm, 0000000000000.00 AS vm  
            FROM `{$this->prefix}purchasedetail` s, `{$this->prefix}taxmaster` t 
            WHERE s.`date` >= '$sdate' AND s.`date` <= '$edate' AND s.id_taxmaster = t.id_taxmaster ORDER BY t.tax_per";
        $tax = $this->m->sql_getall($sql, 1, "", "id");
        $this->sm->assign("tax", $tax);
        switch ($_REQUEST['option']) {
            case 1:
                $sql_detail = "SELECT id_purchase, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount 
                    FROM `{$this->prefix}purchasedetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY id_purchase, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "id_purchase", "id_taxmaster");
                $sql = "SELECT s.* FROM `{$this->prefix}purchase` s WHERE $wcond ORDER BY s.`bill_date`, s.`no`";
                break;
            case 2:
                $sql_detail = "SELECT `date`, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount
                    FROM `{$this->prefix}purchasedetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "date", "id_taxmaster");
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}purchase` 
                    WHERE $wcond GROUP BY `date` ORDER BY `bill_date` ";
                break;
            case 3:
                $sql_detail = "SELECT MONTHNAME(`date`) AS month, id_taxmaster, SUM(goods_amount) AS goods_amount, SUM(tax_amount) AS tax_amount
                    FROM `{$this->prefix}purchasedetail`
                    WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY 1, id_taxmaster";
                $detail = $this->m->sql_getall($sql_detail, 1, "", "month", "id_taxmaster");
                $sql = "SELECT MONTHNAME(`bill_date`) AS month, YEAR(`bill_date`) AS year, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(`total`) AS `total`,
                        SUM(`add`) AS `add`,  SUM(`less`) AS less, SUM(`round`) AS round, SUM(`packing`) AS packing
                        FROM `{$this->prefix}purchase` 
                    WHERE $wcond GROUP BY MONTH(`bill_date`), YEAR(`bill_date`) ORDER BY `bill_date` ";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("detail", $detail);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "preport/vat.tpl.html");
    }

    function vatdet() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $_REQUEST['type'] = isset($_REQUEST['type']) ? $_REQUEST['type'] : '1';
        $wcond = " (`date` >= '$sdate' AND `date` <= '$edate') ";
        if ($_REQUEST['type'] != 1) {
            $wcond .= " AND `local` = " . $_REQUEST['type'];
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT bill_no, bill_date, party_name, party_vatno, packing+`add`-`less`+`round` AS other, vat, total
		FROM `{$this->prefix}purchase` WHERE $wcond ORDER BY date, id_purchase";
                break;
            case 2:
                $sql = "SELECT party_name, party_vatno, COUNT(*) AS bills, SUM(packing+`add`-`less`+`round`) AS other, SUM(vat) AS vat, SUM(total) AS total
		FROM `{$this->prefix}purchase` WHERE $wcond GROUP BY id_head ORDER BY party_name";
                break;
        }
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function area() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $area1 = isset($_REQUEST['area']) ? $_REQUEST['area'] : '0';
        if (is_array($area1)) {
            $id_area = implode(",", $area1);
        } else {
            $id_area = 0;
        }
        if ($id_area != 0) {
            $wcond = "AND p.id_area IN ($id_area)";
        } else {
            $wcond = "";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT p.*, a.name AS area FROM {$this->prefix}purchase p, {$this->prefix}area a  WHERE  p.id_area=p.id AND p.date >= '$sdate' AND p.date <= '$edate' " . " {$wcond} ORDER BY area  ";
                break;
            case 2:
                $sql = "SELECT p.date AS `date` ,a.name AS area, SUM(p.vat) AS vat, SUM(p.total) AS `total`, COUNT(p.slno) AS bills,p.id_area AS id_area,p.id AS id ";
                $sql.="FROM {$this->prefix}purchase p,{$this->prefix}area a WHERE p.id_area=a.id AND p.date >= '$sdate' AND p.date <= '$edate' {$wcond} GROUP BY date  ORDER BY area,date DESC ";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, "", "id_area", "id");
        $opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $area = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("area", $area);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "preport/area.tpl.html");
    }

    function company() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(`total`) AS `total`, party_name,id_head  FROM `{$this->prefix}purchase` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`), id_head ORDER BY `date`  ";
        $res = $this->m->sql_getall($sql, 1, "", "party_name", "month");
        $month = array("April" => "April", "May" => "May", "June" => "June", "July" => "July",
            "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December", "January" => "January", "February" => "February", "March" => "March");
        $this->sm->assign("month", $month);
        $this->sm->assign("data", $res);
        $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(`total`) AS `total` FROM `{$this->prefix}purchase` WHERE `date` >= '$sdate' AND `date` <= '$edate' GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date`  ";
        $res = $this->m->sql_getall($sql, 1, "", "month");
        $this->sm->assign("summary", $res);
        $this->sm->assign("page", "preport/company.tpl.html");
    }

    function olditem() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $paymod = isset($_REQUEST['cash']) ? $_REQUEST['cash'] : '0';
        if ($id_company != 0 && $paymod != 0) {
            $wcond = "AND s.id_company='$id_company' AND s.cash='$paymod'";
        } elseif ($id_company != 0 && $paymod == 0) {
            $wcond = "AND s.id_company='$id_company'";
        } elseif ($id_company == 0 && $paymod != 0) {
            $wcond = "AND s.cash='$paymod'";
        } else {
            $wcond = "";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT sd.*, s.bill_no, s.bill_date, s.party_name AS party, concat(p.name, ' (', p.short, ')') AS iname
                        FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                        WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY iname, sd.date";
                break;
            case 2:
                $sql = "SELECT sd.id_purchasedetail, s.date, s.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, concat(p.name, ' (', p.short, ')') AS iname
                        FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                            WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} GROUP BY sd.id_product, sd.date ORDER BY iname, sd.date";
                break;
            case 3:
                $sql = "SELECT sd.id_purchasedetail, s.date, s.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.name AS iname, p.short AS short
                        FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                            WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} GROUP BY sd.id_product ORDER BY iname";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, "", "iname", "id_purchasedetail");
        $this->sm->assign("data", $res);
        $opt4 = "SELECT id_company AS id, name FROM {$this->prefix}company ORDER BY name";
        $comp = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $comp);
    }

    function item() {
        $opt4 = "SELECT id_company AS id, name FROM {$this->prefix}company ORDER BY name";
        $comp = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $comp);
    }

    function itemrep() {
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $paymod = isset($_REQUEST['cash']) ? $_REQUEST['cash'] : '0';
        $wcond = '';
        if ($id_company != 0) {
            $wcond .= " AND p.id_company='$id_company' ";
            $id_item = isset($_REQUEST['id_item']) ? $_REQUEST['id_item'] : '0';
        }
        if (isset($_REQUEST['itemids'])) {
            $itemids = implode(',', $_REQUEST['itemids']);
            $wcond .= " AND sd.id_product IN ($itemids) ";
        }
        if ($paymod != 0) {
            $wcond .= " AND s.cash='$paymod' ";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT sd.*, s.bill_no AS invno, s.bill_date, s.party_name AS party, concat(p.name, ' (', p.short, ')') AS iname
                        FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                        WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} ORDER BY iname, sd.date";
                break;
            case 2:
                $sql = "SELECT sd.id_purchasedetail, s.date, s.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, concat(p.name, ' (', p.short, ')') AS iname
                        FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                            WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} GROUP BY sd.id_product, sd.date ORDER BY iname, sd.date";
                break;
            case 3:
                $sql = "SELECT sd.id_purchasedetail, s.date, s.id_company, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.name AS iname, p.short AS short
                        FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                            WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond} GROUP BY sd.id_product ORDER BY iname";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, "", "iname", "id_purchasedetail");
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "sreport/item{$_REQUEST['option']}.tpl.html");
    }

    function party() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $chk = isset($_REQUEST['party_select']) ? $_REQUEST['party_select'] : '1';
        $head = isset($_REQUEST['party2']) ? $_REQUEST['party2'] : '0';
        if (is_array($head)) {
            $id_head = implode(",", $head);
        } else {
            $id_head = 0;
        }
        if ($chk == 1 && $id_head == 0) {
            $wcond = "";
        } else {

            $wcond = "AND p.id_head IN ($id_head)";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT p.*, h.name AS party FROM {$this->prefix}purchase p, {$this->prefix}head h  WHERE  p.id_head=h.id_head AND p.date >= '$sdate' AND p.date <= '$edate' " . " {$wcond} ORDER BY party, p.date  ";
                break;
            case 2:
                $sql = "SELECT p.date AS `date` ,h.name AS party , SUM(p.vat) AS vat, SUM(p.total) AS `total`, COUNT(*) AS bills, COUNT(IF(p.cash=1,1,NULL)) AS cashbills, COUNT(IF(p.cash=2,1,NULL)) AS creditbills , SUM(IF(p.cash=1,total,0.00)) AS cashtotal, SUM(IF(p.cash=2,total,0.00)) AS credittotal, p.id_purchase AS id,p.id_head AS id_head ";
                $sql.="FROM {$this->prefix}purchase p,{$this->prefix}head h WHERE p.id_head=h.id_head AND p.date >= '$sdate' AND p.date <= '$edate' {$wcond} GROUP BY p.date  ORDER BY party,p.date DESC ";
                break;
            case 3:
                $sql = "SELECT MONTHNAME(p.date) AS month, YEAR(p.date) AS year,h.name AS party, SUM(p.vat) AS vat, SUM(p.total) AS `total`, SUM(IF(p.cash=1,total,0.00)) AS cashtotal, SUM(IF(p.cash=2,total,0.00)) AS credittotal,COUNT(IF(p.cash=1,1,NULL)) AS cashbills, COUNT(IF(p.cash=2,1,NULL)) AS creditbills,p.id_purchase AS id,p.id_head AS id_head ";
                $sql.="FROM {$this->prefix}purchase p,{$this->prefix}head h WHERE p.id_head=h.id_head AND  p.date >= '$sdate' AND p.date <= '$edate' {$wcond} GROUP BY party,MONTH(p.date), YEAR(p.date) ORDER BY party,p.date ";
                break;
        }
        $res = $this->m->sql_getall($sql, 1, "", "id_head", "id_purchase");
        $this->sm->assign("data", $res);
        $opt2 = "SELECT id_head AS id,name FROM {$this->prefix}head WHERE creditor ORDER BY name";
        $party = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("party", $party);
        $this->sm->assign("page", "preport/party.tpl.html");
    }

    function zone() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $_REQUEST['option'] = isset($_REQUEST['option']) ? $_REQUEST['option'] : '1';
        $zone = isset($_REQUEST['zone']) ? $_REQUEST['zone'] : '0';
        if (is_array($zone)) {
            $id_zone = implode(",", $zone);
            $sql1 = "SELECT id FROM {$this->prefix}area WHERE id_zone IN($id_zone)";
            $res1 = $this->m->sql_getall($sql1, 2, "id", "id");
            $id_area = implode(",", $res1);
        } else {
            $id_area = 0;
        }
        if ($id_area != 0) {
            $wcond = "AND p.id_area IN ($id_area)";
        } else {
            $wcond = "";
        }
        switch ($_REQUEST['option']) {
            case 1:
                $sql = "SELECT p.*, a.name AS area, a.id_zone AS id_zone,z.name AS zone FROM {$this->prefix}purchase p, {$this->prefix}area a,{$this->prefix}zone z  WHERE  id_zone=z.id AND p.id_area=a.id AND p.date >= '$sdate' AND p.date <= '$edate' " . " {$wcond} ORDER BY zone  ";
                break;
            case 2:
                $sql = "SELECT p.date AS `date` ,a.name AS area,a.id_zone AS id_zone,z.name AS zone, SUM(p.vat) AS vat, SUM(p.total) AS `total`, COUNT(p.slno) AS bills,p.id_area AS id_area,p.id AS id ";
                $sql.="FROM {$this->prefix}purchase p,{$this->prefix}area a ,{$this->prefix}zone z  WHERE  id_zone=z.id AND  p.id_area=a.id AND p.date >= '$sdate' AND p.date <= '$edate' {$wcond} GROUP BY date  ORDER BY zone,date DESC ";
                break;
        }
        if ($id_area == '') {
            $res = '';
        } else {
            $res = $this->m->sql_getall($sql, 1, " ", "id_zone", "id");
        }
        $opt2 = "SELECT id_zone AS id,name FROM {$this->prefix}zone ORDER BY name";
        $zone1 = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("zone", $zone1);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "preport/zone.tpl.html");
    }

    function partyitem() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $wcond = '';
        if ($id_company != 0) {
            $wcond .= " AND s.id_company='$id_company' ";
        }
        $sql = "SELECT s.party_name AS party, p.name AS iname,  p.short, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.amount) AS amount, p.case
                FROM {$this->prefix}purchase s, {$this->prefix}purchasedetail sd, {$this->prefix}product p
                WHERE s.id_purchase=sd.id_purchase AND sd.id_product=p.id_product AND s.date>='$sdate' AND s.date<='$edate' {$wcond}
                GROUP BY s.party_name, s.party_address, sd.id_product 
                ORDER BY party_name, iname";
        $res = $this->m->sql_getall($sql);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $this->sm->assign("data", $res);
        $this->sm->assign("page", "preport/partyitem.tpl.html");
    }

    function ltrkg() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();

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
        $sql = "SELECT SUM(sd.amount) AS amount, SUM(sd.qty) AS qty, SUM(sd.free) AS free,
                        sd.id_head, sd.id_product, p.name, h.name AS party_name, p.case
            FROM `{$this->prefix}product`p, `{$this->prefix}purchasedetail` sd LEFT JOIN `{$this->prefix}head` h ON sd.id_head=h.id_head
            WHERE (sd.date >= '$sdate' AND sd.date <= '$edate') AND sd.id_product=p.id_product {$wcond}
            GROUP BY sd.id_head, sd.id_product ORDER BY h.name, p.name";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
        $party = $this->m->sql_getall("SELECT id_head AS id,name FROM {$this->prefix}head WHERE creditor ORDER BY name", 2, "name", "id");
        $this->sm->assign("party", $party);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
    }

    function error() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $wcond = isset($_REQUEST['debug']) ? " HAVING tax_amount!=vat OR total!=net_amount+other " : "";
        $sql = "SELECT s.id_purchase, s.bill_no, s.party_name, s.date, s.vat, s.totalcess, s.tcsamt, s.packing+s.add-s.less+s.round AS other, s.total, 
		SUM(sd.amount) AS amount, SUM(sd.discount_amount1) AS d1, SUM(sd.discount_amount2) AS d2, 
                SUM(sd.discount_amount3) AS d3, SUM(sd.discount_amount4) AS d4, 
                SUM(sd.goods_amount) AS goods_amount, SUM(sd.tax_amount) AS tax_amount, SUM(sd.net_amount) AS net_amount
            FROM `{$this->prefix}purchase` s, `{$this->prefix}purchasedetail` sd
            WHERE s.id_purchase=sd.id_purchase AND s.date >= '$sdate' AND s.date <= '$edate'
            GROUP BY s.id_purchase $wcond ORDER BY s.date, s.no";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }
    function tcs() {
        $sdate = $this->getstartdate();
        $edate = $this->getenddate();
        $sql = "SELECT s.id_purchase, s.bill_date, s.bill_no, h.name, s.date, s.total, s.tcsamt, h.address1, h.gstin, h.tanno
            FROM `{$this->prefix}purchase` s, `{$this->prefix}head` h
            WHERE s.id_head=h.id_head AND (s.date >= '$sdate' AND s.date <= '$edate') AND s.tcsamt>0
            ORDER BY s.date, s.no";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }
}

?>

