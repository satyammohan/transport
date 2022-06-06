<?php
class s1 extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function oldsms() {
        include ("php-class/sms/TextMagicAPI.php");
        $api = new TextMagicAPI(array(
            "username" => "your_user_name",
            "password" => "your_API_password",
        ));
        $text = "Hello world!";
        $phones = array(9991234567);
        $is_unicode = true;
        $api->send($text, $phones, $is_unicode);
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT * FROM {$this->prefix}sale WHERE id_sale='$id'";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("sale", $res);
        $this->sm->assign("page", "sales/sms.tpl.html");
    }

    function sms() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT * FROM {$this->prefix}sale WHERE id_sale='$id'";
        $res = $this->m->sql_getall($sql);
	$this->smssales($res[0]);
    }

    function smssales($r) {
        $id = $r['id_head'];
        $sql = "SELECT h.name, h.address1, concat(trim(h.phone), ',', trim(h.mobile)) AS ph, r.phone
		FROM {$this->prefix}head h, {$this->prefix}tb t, {$this->prefix}area a, {$this->prefix}represent r
		WHERE h.id_head=$id AND h.id_area=a.id_area AND a.id_represent=r.id_represent";
        $data = $this->m->sql_getall($sql);
        $num = $data[0]['ph'];
        $num = str_replace("[", "", $num);
        $num = str_replace("/", ",", $num);
        $nums = explode(",", $num);
        $nums[0] = $nums[0] ? $nums[0] : $nums[1];
        $numbers =  $nums[0] ? trim($nums[0]).", ".$data[0]['phone'] : $data[0]['phone'];
        if ($numbers) {
		$bal = number_format($r['total'], 2);
		$message = "**::Odiray:: ";
		$message .= " Party:".$data['name']." ".$data['address1'];
		$message .= " Invoice:".$r['invno'];
		$message .= " Date:".$r['date'];
		$message .= " Amount:".$bal;
		$message .= " Vehicle:".$r['vehicle_number'];
		$message .= " Contact:".$r['vehicle_contact'];
		$response = $this->sendsms($numbers, $message);
        	$sql = "INSERT INTO smslog (date, number, message, response) VALUES (NOW(), '$numbers', '$message', '$response')";
		$this->m->query($sql);
        } else {
            return 'No numbers to sent SMS';
        }
    }

    function mail() {
        include ("php-class/mail.php");
        $mail = new Mail();
        $mail->setMailHTML("Test");
        $mail->setMailPlainText("plainText");
        $mail->setMailSubject("Subject");
        $mail->setMailRecipient("satyammohan@gmail.com");
        $mail->setMailSender('inventory@software.com');
        $mail->send();
        exit;
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT s.*, h.address1 AS add1, h.address2 AS add2, h.name AS pname 
                FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_sale IN ($id)";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
        }
        $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}saledetail s, {$this->prefix}product p 
                    WHERE s.id_product=p.id_product AND s.id_sale IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_sale", "id_saledetail");
        $this->sm->assign("sale", $res1);
        $this->sm->assign("saledetail", $res);
        $sql = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $salesman = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
        $html = $this->sm->fetch("sales/print.tpl.html");
        ob_clean();
        require_once("php-class/dompdf/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->set_paper(array(0, 0, 12 * 72, 6 * 72), 'portrait');

        $dompdf->render();
        $output = $dompdf->output();
        $message = "Line 1\r\nLine 2\r\nLine 3";
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70, "\r\n");
        mail('satyammohan@gmail.com', 'My Subject', $message);
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT * FROM {$this->prefix}sale WHERE id_sale='$id'";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("sale", $res);
        $this->sm->assign("page", "sales/mail.tpl.html");
    }

    function getcomp() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = isset($_REQUEST['id']) && $_REQUEST['id'] ? "AND p.id_company='{$_REQUEST['id']}'" : "";
        $salestock = (isset($_SESSION['config']['showitemshavingstockinsales']) && $_SESSION['config']['showitemshavingstockinsales'] == 1) ? 1 : 0;
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        $price = ($dealer == "1") ? ' p.distributor_price ' : ' p.seller_price ';
        if ($salestock == 1) {
            $sql = "SELECT p.name as `value`, p.id_product AS col0, $price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3, p.cess AS col4, s.balance AS col5, p.case AS col6 
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s 
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product {$id} AND !p.status AND s.balance>0
            ORDER BY p.name";
        } else {
            $sql = "SELECT p.name as `value`, p.id_product AS col0, $price AS col1, t.tax_per AS col2, p.id_taxmaster_sale AS col3, p.cess AS col4, s.balance AS col5, p.case AS col6
            FROM {$this->prefix}product p, {$this->prefix}taxmaster t, {$this->prefix}product_ledger_summary s 
            WHERE p.name LIKE '%{$filt}%' AND p.id_taxmaster_sale=t.id_taxmaster AND p.id_product=s.id_product {$id} AND !p.status
            ORDER BY p.name";
        }
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getbatch() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $id = "AND b.id_product='{$_REQUEST['id']}'";
        $dealer = isset($_REQUEST['dealer']) ? $_REQUEST['dealer'] : '0';
        $price = ($dealer == "1") ? ' b.distributor_price ' : ' b.seller_price ';
        $sql = "SELECT b.batch_no as `value`, b.id_batch AS col0, b.expiry_date AS col1, $price AS col2, b.mfg_date AS col3  FROM {$this->prefix}batch b WHERE b.status=0 AND b.batch_no LIKE '%{$filt}%' {$id} ORDER BY b.batch_no";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getrepresent() {
        $id = $_REQUEST['id'];
        $sql = "SELECT id_represent FROM {$this->prefix}area a WHERE a.id_area=$id";
        $data = $this->m->sql_getall($sql);
        ob_clean();
        echo $data[0]['id_represent'];
        exit;
    }

    function getparty() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT concat(h.name, ',', h.address1, ',', h.address2, ',', IFNULL(address3, '')) as `value`, h.id_head AS col0, h.address1 AS col1, h.address2 AS col2, h.id_area AS col3, h.vattype AS col4, h.vatno AS col5, h.id_transport AS col6, h.dealer AS col7 FROM {$this->prefix}head h WHERE h.name LIKE '{$filt}%' AND h.debtor AND status=0 ORDER BY h.name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function getsuffix() {
        $series = mysql_real_escape_string($_REQUEST['series']);
        if ($series) {
            $pos = strlen($series) + 1;
            $sql = "SELECT MAX(CAST(substring(invno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}sale WHERE invno LIKE '$series%'";
            $pstr = $series;
        } else {
            $taxbill = $_REQUEST['taxbill'];
            $sql = "SELECT MAX(CAST(SUBSTRING(invno, 2) AS UNSIGNED)) AS maxid FROM {$this->prefix}sale WHERE taxbill=$taxbill";
            $pstr = $taxbill ? "T" : "R";
        }
        if ($_SESSION['name'] == "ODI RAY INDUSTRIES LIMITED") {
            if ($series=="U2/") {
                $sql = "SELECT MAX(CAST(substring(invno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}sale WHERE invno LIKE '$series%'";
            } else {
                $sql = "SELECT MAX(CAST(substring(invno, {$pos}) as decimal(11))) AS maxid FROM {$this->prefix}sale";
            }
        }
	if ($_SESSION['name'] == "MAHALAXMI & CO.") {
		$pstr = "S";	
	}
        $data = $this->m->fetch_assoc($sql);
        echo $pstr . ($data['maxid'] + 1);
        exit;
    }

    function getdiscount() {
        $id_head = mysql_real_escape_string($_REQUEST['id_head']);
        $id_product = mysql_real_escape_string($_REQUEST['id_product']);
        $sql = "SELECT d.discount AS dis1, p.id_company AS company FROM {$this->prefix}discount d, {$this->prefix}product p 
            WHERE (d.id_company=p.id_company AND d.id_head='$id_head') AND p.id_product='$id_product'";
        $data = $this->m->fetch_assoc($sql);
        echo $data['dis1'];
        exit;
    }

    function getform() {
        $id_form = mysql_real_escape_string($_REQUEST['id_form']);
        $sql = "SELECT name FROM {$this->prefix}form WHERE id_form='$id_form'";
        $data = $this->m->fetch_assoc($sql);
        echo $data['name'];
        exit;
    }

    function check() {
        $invno = trim($_REQUEST['invno']);
        $sdate = $_SESSION['sdate'];
        $edate = $_SESSION['edate'];
        $sql = $this->create_select("{$this->prefix}sale", "invno='$invno' AND `date`>='$sdate' AND `date`<='$edate'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function oldcheck() {
        $invno = trim($_REQUEST['invno']);
        $sdate = $this->format_date($_SESSION['sdate']);
        $sql = $this->create_select("{$this->prefix}sale", "invno='$invno' AND `date` >= '$sdate'");
        $num = $this->m->num_rows($this->m->query($sql));
        echo $num;
        exit;
    }

    function option_val() {
        $opt = "SELECT id_transport AS id,name FROM {$this->prefix}transport ORDER BY name";
        $transport = $this->m->sql_getall($opt, 2, "name", "id");
        $this->sm->assign("transport", $transport);
        $opt2 = "SELECT id_area AS id,name FROM {$this->prefix}area ORDER BY name";
        $area = $this->m->sql_getall($opt2, 2, "name", "id");
        $this->sm->assign("area", $area);
        $opt4 = "SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name";
        $company = $this->m->sql_getall($opt4, 2, "name", "id");
        $this->sm->assign("company", $company);
        $opt6 = "SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name";
        $salesman = $this->m->sql_getall($opt6, 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == 0)
            $this->get_permission("sales", "INSERT");
        else
            $this->get_permission("sales", "UPDATE");
        if (@$_REQUEST['order_id']) {
            $oid = $_REQUEST['order_id'];
            $sql = "SELECT s.*, h.name AS party_name, h.address1 AS party_address, h.address2 AS party_address2, h.vattype AS party_vattype, h.vatno AS party_vatno, h.dealer FROM {$this->prefix}salesorder s, {$this->prefix}head h WHERE s.is_billed!=1 AND s.id_salesorder='$oid' AND s.id_head=h.id_head";
            $sdata = $this->m->fetch_assoc($sql);
            $sdata['id_sale'] = '';
            if ($sdata) {
                $this->sm->assign("sdata", $sdata);
                $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}salesorderdetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_salesorder='$oid'";
                $this->sm->assign("data", $this->m->sql_getall($sql));
            }
        } else {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}saledetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sale='$id' ORDER BY id_saledetail";
            $this->sm->assign("data", $this->m->sql_getall($sql));
//            $sql = "SELECT * FROM {$this->prefix}sale WHERE id_sale='$id'";
            $sql = "SELECT s.*, h.dealer FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head WHERE s.id_sale='$id' ";
            $this->sm->assign("sdata", $this->m->fetch_assoc($sql));
        }
        $sql = "SELECT id_taxmaster, name FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id_taxmaster"));
        $sql = "SELECT id_taxmaster, tax_per FROM {$this->prefix}taxmaster ORDER BY tax_per";
        $this->sm->assign("taxrates", json_encode($this->m->sql_getall($sql, 2, "tax_per", "id_taxmaster")));
        $sql = "SELECT id_series, name FROM {$this->prefix}series ORDER BY name";
        $this->sm->assign("series", $this->m->sql_getall($sql, 2, "name", "id_series"));
        $sql = "SELECT id_form AS id, name FROM {$this->prefix}form ORDER BY name";
        $this->sm->assign("frm", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->option_val();
        $nobatch = (isset($_SESSION['config']['NOBATCHINSALES']) && $_SESSION['config']['NOBATCHINSALES'] == 1) ? 1 : 0;
        if ($nobatch) {
            $this->sm->assign("page", "sales/add.nobatch.tpl.html");
        } else {
            $this->sm->assign("page", "sales/add.tpl.html");
        }
    }

    function prsale($id = "") {
        unset($_SESSION['url']);
        $this->get_permission("sales", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.pincode, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.name AS hname, h.address1,  h.address2, h.address3, h.email, SUM(IF(b.credit, -b.credit, b.debit)) AS balance
                FROM {$this->prefix}sale s
                LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                LEFT JOIN {$this->prefix}tb b ON b.id_head=h.id_head
                WHERE s.id_sale IN ($id) GROUP BY s.id_sale ";
        $res1 = $this->m->sql_getall($sql);
        foreach ($res1 as $key => $val) {
            $res1[$key]['w'] = $this->convert_number(round($val['total']));
            $res1[$key]['balance'] = (float) $res1[$key]['balance'];
        }
        $this->sm->assign("sale", $res1);
        $sql = "SELECT s.*, p.name AS item, p.hsncode, p.case FROM {$this->prefix}saledetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sale IN ($id)";
        $res = $this->m->sql_getall($sql, 1, "", "id_sale", "id_saledetail");
        $this->sm->assign("saledetail", $res);
        $this->sm->assign("discount", $this->ini['discount']);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        if ((strtotime($res1[0]['date']) >= strtotime($_SESSION['gstdate'])) AND $_SESSION['gstdate'] != "") {
            if (@$_SESSION['config']['SALEBILLFORMAT']) {
                $format = $_SESSION['config']['SALEBILLFORMAT'];
                $this->sm->assign("page", "salesbill/{$format}.tpl.html");
            } else {
                $this->sm->assign("page", "salesbill/print.tpl.html");
            }
        } else {
            $this->sm->assign("page", "salesbill/oldprint.tpl.html");
        }
    }

    function ewaybill() {
        $this->get_permission("sales", "REPORT");
        $id = (isset($id) && ($id != "")) ? $id : $_REQUEST['id'];
        $sql = "SELECT s.*, h.phone, h.mobile, h.local, h.gstin, h.panno, h.adhar, h.name AS hname, h.address1,  h.address2, h.address3, h.email
                FROM {$this->prefix}sale s
                LEFT JOIN {$this->prefix}head h ON s.id_head=h.id_head
                WHERE s.id_sale IN ($id) GROUP BY s.id_sale ";
        $this->sm->assign("sale", $this->m->sql_getall($sql));
        //qty, free, goods_amount, tax_per, cessamt
        $sql = "SELECT s.id_sale, p.name AS item, p.hsncode, p.case, SUM(s.qty) AS qty, SUM(s.free) AS free, 
                    s.tax_per, SUM(s.goods_amount) AS goods_amount, SUM(s.cessamt) AS cessamt 
                FROM {$this->prefix}saledetail s, {$this->prefix}product p 
                WHERE s.id_product=p.id_product AND s.id_sale IN ($id) GROUP BY p.hsncode";
        $res = $this->m->sql_getall($sql, 1, "", "id_sale", "hsncode");
//        $sql = "SELECT s.id_sale, p.name AS item, p.hsncode, p.case, s.* FROM {$this->prefix}saledetail s, {$this->prefix}product p WHERE s.id_product=p.id_product AND s.id_sale IN ($id)";
//        $res = $this->m->sql_getall($sql, 1, "", "id_sale", "id_saledetail");
        $this->sm->assign("saledetail", $res);
        $sql = "SELECT id_taxmaster AS id,name FROM {$this->prefix}taxmaster";
        $this->sm->assign("tax", $this->m->sql_getall($sql, 2, "name", "id"));
        $this->sm->assign("page", "sales/ewaybill.tpl.html");
    }

    function insert() {
        $this->get_permission("sales", "INSERT");
        $data1 = $_REQUEST['sales'];
        $data1['pending'] = $data1['total'];
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        if ($data1['challan_date'] != '') {
            $data1['challan_date'] = $this->format_date($data1['challan_date']);
        }
        if ($data1['lr_date'] != '') {
            $data1['lr_date'] = $this->format_date($data1['lr_date']);
        }
        if ($data1['cheque_date'] != '') {
            $data1['cheque_date'] = $this->format_date($data1['cheque_date']);
        }
        if ($data1['form_date'] != '') {
            $data1['form_date'] = $this->format_date($data1['form_date']);
        }
        if ($data1['waybill_date'] != '') {
            $data1['waybill_date'] = $this->format_date($data1['waybill_date']);
        }
        $sql2 = $this->m->query($this->create_insert("{$this->prefix}sale", $data1));
        $id = $this->m->getinsertID($sql2);
        if (@$_REQUEST['order_id']) {
            $sql = "UPDATE {$this->prefix}salesorder SET is_billed=1, id_sale={$id} WHERE id_salesorder=" . $_REQUEST['order_id'];
            $this->m->query($sql);
        }
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $data = array("invno" => "{$data1['invno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}", "id_area" =>  "{$data1['id_area']}", "id_represent" =>  "{$data1['id_represent']}",
                    "id_sale" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                if (isset($_REQUEST['case'][$i])) {
                    $data['case'] = $_REQUEST['case'][$i];
                }
                $this->m->query($this->create_insert("{$this->prefix}saledetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $_SESSION['msg'] = "Sales Successfully Inserted";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=sales&func=listing");
        }
    }

    function update() {
        $this->get_permission("sales", "UPDATE");
        $data1 = $_REQUEST['sales'];
        $id = $_REQUEST['id'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];
        $data1['date'] = $this->format_date($data1['date']);
        $data1['challan_date'] = $this->format_date($data1['challan_date']);
        $data1['lr_date'] = $this->format_date($data1['lr_date']);
        $data1['cheque_date'] = $this->format_date($data1['cheque_date']);
        $data1['form_date'] = $this->format_date($data1['form_date']);
        $data1['waybill_date'] = $this->format_date($data1['waybill_date']);
        $this->m->query($this->create_update("{$this->prefix}sale", $data1, "id_sale='$id'"));
        $this->m->query($this->create_delete("{$this->prefix}saledetail", "id_sale='$id'"));
        for ($i = 0; $i < count($_REQUEST['items']); $i++) {
            if ($_REQUEST['id_product'][$i]) {
                $net_amount = $_REQUEST['goods_amount'][$i] + $_REQUEST['tax_amount'][$i] + $_REQUEST['cessamt'][$i];
                $data = array("invno" => "{$data1['invno']}", "date" => "{$data1['date']}", "id_product" => "{$_REQUEST['id_product'][$i]}",
                    "rate" => "{$_REQUEST['rate'][$i]}", "qty" => "{$_REQUEST['quantity'][$i]}", "free" => "{$_REQUEST['free'][$i]}", "amount" => "{$_REQUEST['amount'][$i]}",
                    "discount_type1" => "{$_REQUEST['discount_type1'][$i]}", "discount1" => "{$_REQUEST['discount1'][$i]}", "discount_amount1" => "{$_REQUEST['discount_amount1'][$i]}",
                    "discount_type2" => "{$_REQUEST['discount_type2'][$i]}", "discount2" => "{$_REQUEST['discount2'][$i]}", "discount_amount2" => "{$_REQUEST['discount_amount2'][$i]}",
                    "discount_type3" => "{$_REQUEST['discount_type3'][$i]}", "discount3" => "{$_REQUEST['discount3'][$i]}", "discount_amount3" => "{$_REQUEST['discount_amount3'][$i]}",
                    "id_taxmaster" => "{$_REQUEST['id_taxmaster'][$i]}", "tax_per" => "{$_REQUEST['tax_per'][$i]}", "tax_amount" => "{$_REQUEST['tax_amount'][$i]}",
                    "cess" => "{$_REQUEST['cess'][$i]}", "cessamt" => "{$_REQUEST['cessamt'][$i]}",
                    "goods_amount" => "{$_REQUEST['goods_amount'][$i]}", "id_area" =>  "{$data1['id_area']}", "id_represent" =>  "{$data1['id_represent']}",
                    "id_sale" => "{$id}", "id_head" => "{$data1['id_head']}", "net_amount" => $net_amount,
                    "id_batch" => "{$_REQUEST['id_batch'][$i]}", "batch_no" => "{$_REQUEST['batch_no'][$i]}", "exp_date" => "{$_REQUEST['exp_date'][$i]}");
                if (isset($_REQUEST['case'][$i])) {
                    $data['case'] = $_REQUEST['case'][$i];
                }
                $this->m->query($this->create_insert("{$this->prefix}saledetail", $data));
                $qty = $_REQUEST['quantity'][$i] + $_REQUEST['free'][$i];
                $sql1 = "UPDATE {$this->prefix}product SET closing_stock = closing_stock - {$qty} WHERE id_product='{$_REQUEST['id_product'][$i]}'";
                $this->m->query($sql1);
            }
        }
        $_SESSION['msg'] = "Sales Successfully Updated";
        if (isset($_REQUEST['ce'])) {
            $this->prsale($id);
        } else {
            $this->redirect("index.php?module=sales&func=listing");
        }
    }

    function updprint() {
        $idarr = $_REQUEST['id'];
        for ($i = 0; $i < count($idarr); $i++) {
            $sql = "UPDATE {$this->prefix}sale SET `printed` = NOW() WHERE id={$idarr[$i]}";
            $this->m->query($sql);
        }
        exit;
    }

    function listing() {
        $this->get_permission("sales", "REPORT");
        $this->option_val();
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $_SESSION['sdate']);
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $id_company = isset($_REQUEST['company']) ? $_REQUEST['company'] : '0';
        $id_represent = isset($_REQUEST['represent']) ? $_REQUEST['represent'] : '0';
        if ($id_company != 0 && $id_represent != 0) {
            $wcond = "AND s.id_company='$id_company' AND s.id_represent='$id_represent'";
        } elseif ($id_company != 0 && $id_represent == 0) {
            $wcond = "AND s.id_company='$id_company'";
        } elseif ($id_company == 0 && $id_represent != 0) {
            $wcond = "AND s.id_represent='$id_represent'";
        } else {
            $wcond = "";
        }
        $taxbill = isset($_REQUEST['taxbill']) ? $_REQUEST['taxbill'] : '';
        if ($taxbill != "") {
            $wcond .= "AND s.taxbill='$taxbill'";
        }
        $limit = isset($_REQUEST['start_date']) ? "" : " LIMIT 30";
        $sql = "SELECT s.*, r.name AS rname, CAST(SUBSTRING(invno, POSITION('/' IN invno)+1) AS signed) AS myno, SUBSTRING(invno, 1, POSITION('/' IN invno)-1) AS pref  FROM {$this->prefix}sale s LEFT JOIN {$this->prefix}represent r ON s.id_represent=r.id_represent
            WHERE s.date >= '$sdate' AND s.date <= '$edate' {$wcond}
                ORDER BY `date` DESC, pref, myno DESC {$limit} ";
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
        $this->sm->assign("page", "sales/list.tpl.html");
    }

    function history() {
        $this->get_permission("sales", "REPORT");
        $sql = "SELECT id_head AS id,name FROM {$this->prefix}head ORDER BY name";
        $this->sm->assign("head", $this->m->sql_getall($sql, 2, "name", "id"));
        if (isset($_REQUEST['party'])) {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}saledetail s, {$this->prefix}product p
                WHERE s.id_product=p.id_product AND s.id_product='{$_REQUEST['id']}' AND s.id_head='{$_REQUEST['party']}' ORDER BY s.`date` DESC ";
            $this->sm->assign("page", "sales/party.tpl.html");
        } else {
            $sql = "SELECT s.*, p.name AS item FROM {$this->prefix}saledetail s, {$this->prefix}product p
                WHERE s.id_product=p.id_product AND s.id_product='{$_REQUEST['id']}' ORDER BY s.`date` DESC ";
            $this->sm->assign("page", "sales/history.tpl.html");
        }
        $this->sm->assign("sdata", $this->m->sql_getall($sql));
    }

    function delete() {
        $this->get_permission("sales", "DELETE");
        $this->m->query($this->create_delete("{$this->prefix}sale", "id_sale='{$_REQUEST['id']}'"));
        $this->m->query($this->create_delete("{$this->prefix}saledetail", "id_sale='{$_REQUEST['id']}'"));

        $sql = "UPDATE {$this->prefix}salesorder SET is_billed=0, id_sale=0 WHERE id_sale=" . $_REQUEST['id'];
        $this->m->query($sql);

        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=sales&func=listing");
    }

    function ocheckbalance() {
        $id = $_REQUEST['id_head'];
        $sql = "SELECT SUM(debit-credit) AS balance FROM {$this->prefix}tb WHERE id_head='$id'";
        $res = $this->m->fetch_assoc($sql);
        $net = $res['balance'] + $_REQUEST['billamt'];
        $sql = "SELECT credit_limit AS balance, credit_days AS days FROM {$this->prefix}head WHERE id_head='$id'";
        $res = $this->m->fetch_assoc($sql);
        if ($res['balance']) {
	    $return['allow'] = 0;
	    $return['days'] = 0;
            $return['balance'] = $res['balance'];
            $return['net'] = $net;
//echo $res['balance'] .'---'. $net;exit;
            if ($res['balance'] < $net) {
                //echo "Credit Balance : " . $res['balance'] . ". Current Balance is " . sprintf('%0.2f', $net);
            }
            if ($res['days'] > 0) {
                $mybal = $net;
                $sql = "SELECT invno, date, total, DATEDIFF(NOW(), date) AS days FROM {$this->prefix}sale WHERE id_head='$id' AND cash ORDER BY date DESC";
                $res1 = $this->m->sql_getall($sql);
                foreach ($res1 as $k=>$v){
                    $return['billno'] = $v['invno'];
                    $return['allow'] = (int) $res['days'];
                    $return['days'] = (int) $v['days'];
                    if ($mybal>0) {
                        $mybal = $mybal - $v['total'];
                    }
                    if ($mybal < 0) {
                        break;
                    }
                }
                echo json_encode($return);
	    } else {
                echo json_encode($return);exit;
            }
        }
        exit;
    }

    function checkbalance() {
        $id = $_REQUEST['id_head'];
        $sql = "SELECT SUM(debit-credit) AS balance FROM {$this->prefix}tb WHERE id_head='$id'";
        $res = $this->m->fetch_assoc($sql);
        $net = $res['balance'] + $_REQUEST['billamt'];
        $sql = "SELECT credit_limit AS balance, credit_days AS days FROM {$this->prefix}head WHERE id_head='$id'";
        $res = $this->m->fetch_assoc($sql);
        if ($res['balance']) {
            $return['allow'] = 0;
    	    $return['days'] = 0;
            $return['balance'] = $res['balance'];
            $return['net'] = $net;
            if ($res['balance'] > $net) {
                $return['billno'] = '';
                $return['allow'] = (int) $res['days'];
                $return['days'] = 0;
            } else {
                if ($res['days'] > 0) {
                    $mybal = $net;
                    $sql = "SELECT invno, date, total, DATEDIFF(NOW(), date) AS days FROM {$this->prefix}sale WHERE id_head='$id' AND cash ORDER BY date DESC";
                    $res1 = $this->m->sql_getall($sql);
                    foreach ($res1 as $k=>$v){
                        $return['billno'] = $v['invno'];
                        $return['allow'] = (int) $res['days'];
                        $return['days'] = (int) $v['days'];
                        if ($mybal>0) {
                            $mybal = $mybal - $v['total'];
                        }
                        if ($mybal < 0) {
                            break;
                        }
                    }
                }
            }
            echo json_encode($return);exit;
        }
        exit;
    }
}
?>
