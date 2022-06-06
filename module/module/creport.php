<?php

class creport extends common {

    function __construct() {
        $this->checklogin();
        $this->get_permission("creditnote", "REPORT");
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
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $id_saletype = isset($_REQUEST['saletype']) ? $_REQUEST['saletype'] : '0';
        if ($id_saletype != 0) {
            $wcond = " AND s.saletype='$id_saletype' AND (s.caltype='C') ";
        } else {
            $wcond = "";
        }
        if ($id_company != 0) {
            $wcond .= " AND s.id_company='$id_company' ";
        }
        $id_head = isset($_REQUEST['id_head']) ? $_REQUEST['id_head'] : '0';
        if ($id_head != 0) {
            $wcond .= " AND s.id_head='$id_head' ";
        }
        switch ($_REQUEST['option']) {
            case 1:
                if (isset($_REQUEST['itemdetails'])) {
                    $sql_detail = "SELECT sd.*, p.name FROM `{$this->prefix}creditnotedetail` sd, `{$this->prefix}product` p
                        WHERE sd.id_product=p.id_product AND `date` >= '$sdate' AND `date` <= '$edate' ORDER BY date, no LIMIT 2000";
                    $detail = $this->m->sql_getall($sql_detail, 1, "", "id_creditnote", "id_creditnotedetail");
                    $this->sm->assign("detail", $detail);
                }
                $sql = "SELECT s.*, s.no AS myno, s.no AS pref, h.name, h.address1 as address, h.address2 AS address1, h.gstin, h.local, 
			sd.tax_per AS per, SUM(goods_amount) AS gv, SUM(tax_amount) AS ta
		FROM `{$this->prefix}creditnote` s LEFT JOIN `{$this->prefix}head` h ON s.id_head=h.id_head, `{$this->prefix}creditnotedetail` sd  
		WHERE s.date >= '$sdate' AND s.date <= '$edate' " . " $wcond AND s.id_creditnote=sd.id_creditnote 
		GROUP BY s.id_creditnote, sd.tax_per ORDER BY `date`, pref, myno, no";
                break;
            case 2:
                $sql = "SELECT `date`, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total` FROM `{$this->prefix}creditnote` s WHERE `date` >= '$sdate' AND `date` <= '$edate' {$wcond} GROUP BY `date` ORDER BY `date`";
                break;
            case 3:
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, SUM(totalamt) AS totalamt, SUM(vat) AS vat, SUM(totalcess) AS totalcess, SUM(`total`) AS `total`  FROM `{$this->prefix}creditnote` WHERE `date` >= '$sdate' AND `date` <= '$edate' {$wcond} GROUP BY MONTH(`date`), YEAR(`date`) ORDER BY `date` ";
                $sql = "SELECT MONTHNAME(`date`) AS month, YEAR(`date`) AS year, 
                SUM(IF(!h.local, 0, 1)*vat) AS igst,
                SUM(IF(!h.local, 1, 0)*(vat/2)) AS cgst,
                SUM(IF(!h.local, 1, 0)*(vat/2)) AS sgst,
                SUM(vat) AS vat, SUM(`total`) AS `total`, SUM(`totalamt`-s.discount) AS `totalamt`,
                SUM(`totalcess`) AS `totalcess`, SUM(`add`+`less`+`round`+`packing`) AS `other`,
                count(*) AS billn FROM `{$this->prefix}creditnote` s LEFT JOIN `{$this->prefix}head` h ON h.id_head=s.id_head
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
	$partysql = "SELECT id_head, name FROM {$this->prefix}head WHERE status=0 AND debtor ORDER BY name";
        $party = $this->m->sql_getall($partysql, 2, "name", "id_head");
        $this->sm->assign("party", $party);

        $this->sm->assign("data", $res);
        $this->sm->assign("page", "creport/register.tpl.html");
    }
    function summary() {
        $sd = $_SESSION['sdate'];
        $ed = $_SESSION['edate'];
        $sql = "CREATE TABLE IF NOT EXISTS allpurchase (SELECT p.party_name, p.party_address, p.date, p.bill_no, pd.qty, pd.free, pd.rate, pd.id_product, pd.id_batch, pd.batch_no FROM {$this->prefix}purchase p, {$this->prefix}purchasedetail pd WHERE p.id_purchase=pd.id_purchase AND p.id_purchase=0)";
        $this->m->query($sql);
        $sql = "DELETE FROM allpurchase WHERE date>='$sd' AND date<='$ed'";
        $this->m->query($sql);
        $sql = "INSERT INTO allpurchase (SELECT p.party_name, p.party_address, p.date, p.bill_no, pd.qty, pd.free, pd.rate, pd.id_product, pd.id_batch, pd.batch_no FROM {$this->prefix}purchase p, {$this->prefix}purchasedetail pd WHERE p.id_purchase=pd.id_purchase)";
        $this->m->query($sql);

        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
	//$id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $id_saletype = isset($_REQUEST['saletype']) ? $_REQUEST['saletype'] : '0';
        if ($id_saletype != 0) {
            $wcond = " AND s.saletype='$id_saletype' ";
        } else {
            $wcond = "";
        }
	$id_company = isset($_REQUEST['company']) ? implode($_REQUEST['company'], ",") : '0';
        if ($id_company != 0) {
            $wcond .= " AND p.id_company IN ($id_company) ";
        }
        $sql = "SELECT sd.id_product, c.name AS cname, sd.batch_no, b.mrp, b.mrp_without_tax, b.expiry_date, SUM(sd.qty) AS qty, SUM(sd.free) AS free, SUM(sd.goods_amount) AS goods_amount, sd.rate, p.name FROM `{$this->prefix}creditnote` s, {$this->prefix}company c, {$this->prefix}creditnotedetail sd LEFT JOIN `{$this->prefix}batch` b ON sd.id_batch=b.id_batch, `{$this->prefix}product` p WHERE (s.date >= '$sdate' AND s.date <= '$edate') AND s.id_creditnote=sd.id_creditnote AND p.id_company=c.id_company AND sd.id_product=p.id_product $wcond GROUP BY 1,2 ORDER BY c.name, p.name, p.batch";

        $sql = "SELECT a.*, b.party_name, b.party_address, b.bill_no, b.date as bill_date FROM ($sql) a LEFT JOIN (SELECT id_product, batch_no, party_name, party_address, bill_no, min(date) AS date FROM `allpurchase` GROUP BY 1,2) b ON a.id_product=b.id_product AND a.batch_no=b.batch_no ORDER BY cname, name, batch_no";

	$detail = $this->m->sql_getall($sql);
	$this->sm->assign("data", $detail);

        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE status=0 ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
    }
}
?>

