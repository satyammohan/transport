<?php

class head extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
	
	function getgstdetails() {
		$id = @$_REQUEST['gstin'];
		$url = "https://cleartax.in/f/compliance-report/$id/";
		$file = json_decode(file_get_contents($url, true), true);
//		$this->pr($file);
		echo "<table width='400px' >";
		echo "<tr><td>Name    :</td><td>".$file['taxpayerInfo']['tradeNam']."</td></tr>";
		echo "<tr><td>Person  :</td><td>".$file['taxpayerInfo']['lgnm']."</td></tr>";
		echo "<tr><td>GSTIN   :</td><td>".$file['taxpayerInfo']['gstin']."</td></tr>";
		echo "<tr><td>Address :</td><td>".$file['taxpayerInfo']['pradr']['addr']['bno']."</td></tr>";
		echo "<tr><td>&nbsp;</td><td>".$file['taxpayerInfo']['pradr']['addr']['bnm']."</td></tr>";
		echo "<tr><td>&nbsp;	</td><td>".$file['taxpayerInfo']['pradr']['addr']['st']."</td></tr>";
		echo "<tr><td>District:</td><td>".$file['taxpayerInfo']['pradr']['addr']['flno']."</td></tr>";
		echo "<tr><td>Pincode :</td><td>".$file['taxpayerInfo']['pradr']['addr']['pncd']."</td></tr>";
		echo "<tr><td>Location:</td><td>".$file['taxpayerInfo']['pradr']['addr']['loc']."</td></tr>";
		echo "<tr><td>District:</td><td>".$file['taxpayerInfo']['pradr']['addr']['dst']."</td></tr>";
		echo "</table><br><br>";
		exit;
	}

    function getgroup() {
        $filt = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        $sql = "SELECT id_group AS id, name FROM `{$this->prefix}group` WHERE name LIKE '{$filt}%' AND status=0 ORDER BY name";
        $data = $this->m->sql_getall($sql);
        echo json_encode($data);
        exit;
    }

    function sdays() {
	$day = date('d');
	$mon = date('m');
	$sql = "SELECT * FROM {$this->prefix}head WHERE (day(doa)=$day AND month(doa)=$mon) OR (day(dob)=$day AND month(dob)=$mon)";
	$res = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $res);
	$sql = "SELECT * FROM {$this->prefix}employee WHERE (day(doj)=$day AND month(doj)=$mon)";
	$res = $this->m->getall($this->m->query($sql));
        $this->sm->assign("emp", $res);
	
    } 

    function sopening() {
        $sql = "SELECT h.*, g.name AS gname
                FROM {$this->prefix}head h LEFT JOIN {$this->prefix}group g ON h.id_group=g.id_group ORDER BY trim(h.name)";
 
	$res = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $res);
    } 

    function insert() {
        $this->get_permission("head", "INSERT");
        $data = $_REQUEST['head'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['id_user'] = $_SESSION['id_user'];
        $data['name'] = addslashes($data['name']);
        $data['doa'] = $this->format_date($data['doa']);
        $data['dob'] = $this->format_date($data['dob']);
        $data['cst_date'] = $this->format_date($data['cst_date']);
        $res = $this->m->query($this->create_insert("{$this->prefix}head", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=head&func=edit");
    }

    function edit() {
        error_reporting(E_ALL);


        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id == "0")
            $this->get_permission("head", "INSERT");
        else
            $this->get_permission("head", "UPDATE");
        $data = $this->m->fetch_assoc($this->create_select("{$this->prefix}head", "id_head='{$id}'"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));
        $res2 = $this->m->query("SELECT * FROM {$this->prefix}transport WHERE status=0  ORDER BY name");
        $this->sm->assign("transport", $this->m->getall($res2, 2, "name", "id_transport"));
        $res3 = $this->m->query("SELECT * FROM `{$this->prefix}group` WHERE status=0  ORDER BY name");
        $this->sm->assign("group", $this->m->getall($res3, 2, "name", "id_group"));
        if ($id == "0") {
            $sql = "SELECT MAX(code) AS code FROM `{$this->prefix}head`";
            $cd = $this->m->fetch_array($this->m->query($sql));
            $data['code'] = $cd['code'] + 1;
        }
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "head/add.tpl.html");
    }

    function update() {
        $this->get_permission("head", "UPDATE");
        $data = $_REQUEST['head'];
        $data['name'] =  addslashes($data['name']);
        $data['doa'] = $this->format_date($data['doa']);
        $data['dob'] = $this->format_date($data['dob']);
        $data['cst_date'] = $this->format_date($data['cst_date']);
        $res = $this->m->query($this->create_update("{$this->prefix}head", $data, "id_head='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=head&func=listing");
    }

    function delete() {
        $this->get_permission("head", "DELETE");
        $res1 = $this->m->query($this->create_select("{$this->prefix}sale", "id_head='{$_REQUEST['id']}'"));
        if ($this->m->num_rows($res1) > 0) {
            $_SESSION['msg'] = "Head Can't be Deleted<br>It's Found to be Associated With some Other Information! ";
        } else {
            $res = $this->m->query($this->create_delete("{$this->prefix}head", "id_head='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=head&func=listing");
    }

    function listing() {
        $this->get_permission("head", "REPORT");
        $sql = "SELECT h.*, g.name AS gname, a.name AS area, t.name AS transport 
                FROM {$this->prefix}head h LEFT JOIN {$this->prefix}group g ON h.id_group=g.id_group  
                    LEFT JOIN {$this->prefix}area a ON h.id_area=a.id_area
                LEFT JOIN {$this->prefix}transport t ON  h.id_transport=t.id_transport";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $profile);
    }

    function opening() {
	$this->get_permission("head", "REPORT");
        $res3 = $this->m->query("SELECT * FROM `{$this->prefix}group` WHERE status=0  ORDER BY name");
        $this->sm->assign("group", $this->m->getall($res3, 2, "name", "id_group"));

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        $sql = "SELECT h.id_head, h.name, h.address1, h.address2, h.opening_balance, h.otype, h.debtor, g.name AS gname
                FROM {$this->prefix}head h LEFT JOIN {$this->prefix}group g ON h.id_group=g.id_group 
                WHERE !h.status AND h.id_group='$id' ORDER BY g.name, h.name";
        $head = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $head);
    }

    function oldopening() {
        $this->get_permission("head", "REPORT");
        $sql = "SELECT h.*, g.name AS gname, a.name AS area, t.name AS transport 
                FROM {$this->prefix}head h LEFT JOIN {$this->prefix}group g ON h.id_group=g.id_group  
                    LEFT JOIN {$this->prefix}area a ON h.id_area=a.id_area
                LEFT JOIN {$this->prefix}transport t ON  h.id_transport=t.id_transport WHERE opening_balance!=0 ORDER BY id_group ";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $profile);
        $this->sm->assign("page", "head/listing.tpl.html");
    }

    function listing_rep() {
        $this->get_permission("head", "REPORT");
        $sql = "SELECT h.name, h.address1, h.address2, h.address3, h.gstin, h.panno, h.adhar, h.contact_person, h.phone, h.email, h.dealer, a.name AS areaname, r.name AS represent 
                FROM {$this->prefix}head h, {$this->prefix}area a, {$this->prefix}represent r
                WHERE h.debtor AND h.id_area=a.id_area AND a.id_represent=r.id_represent
                ORDER BY h.name, h.address1";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("data", $data);
    }
    
    function setrepresent_notused() {
        $this->get_permission("head", "REPORT");
	$res1 = $this->m->query("SELECT * FROM {$this->prefix}represent WHERE status=0 ORDER BY name");
        $this->sm->assign("represent", $this->m->getall($res1, 2, "name", "id_represent"));

        $sql = "SELECT h.id_head, h.name, h.address1, h.address2, h.address3, h.contact_person, h.phone, r.name AS represent, h.partyuser
                FROM {$this->prefix}head h LEFT JOIN {$this->prefix}represent r ON h.id_represent=r.id_represent
                WHERE h.debtor AND partyuser IS NOT NULL AND partyuser!=''
                ORDER BY h.name, h.address1";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("data", $data);
    }

    function setrepresent() {
        $this->get_permission("head", "REPORT");
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}represent WHERE status=0 ORDER BY name");
        $this->sm->assign("represent", $this->m->getall($res1, 2, "name", "id_represent"));
        $res1 = $this->m->query("SELECT * FROM {$this->prefix}area WHERE status=0 ORDER BY name");
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id_area"));

        $sql = "SELECT h.id_head, h.id_area, h.name, h.address1, h.address2, h.address3, h.contact_person, h.phone, h.partyuser, h.partypass, a.name AS area, r.name AS represent
                FROM {$this->prefix}head h, {$this->prefix}area a LEFT JOIN {$this->prefix}represent r ON a.id_represent=r.id_represent
                WHERE h.debtor AND h.partyuser IS NOT NULL AND h.partyuser!='' AND h.id_area=a.id_area
                ORDER BY a.name, h.name, h.address1";
        $data = $this->m->getall($this->m->query($sql));
        $this->sm->assign("data", $data);
    }
    function saverepresent() {
        $id = isset($_REQUEST['id_head']) ? $_REQUEST['id_head'] : "0";
        $area = isset($_REQUEST['id_area']) ? $_REQUEST['id_area'] : "0";
        $sql = "UPDATE {$this->prefix}head SET id_area='$area' WHERE id_head='$id'";
        $this->m->query($sql);
        exit;
    }

    function creditlimit() {
        $this->get_permission("head", "REPORT");
        $id = isset($_REQUEST['represent']) ? $_REQUEST['represent'] : 0;
        $mismatch = isset($_REQUEST['mismatch']) ? $_REQUEST['mismatch'] : 0;
		if ($id!=0) {
			$sql = "SELECT h.*, SUM(t.debit-t.credit) AS balance, 0.00 AS billamt FROM {$this->prefix}head h, {$this->prefix}tb t, {$this->prefix}area a 
                		WHERE h.debtor AND h.id_head=t.id_head AND h.id_area=a.id_area AND a.id_represent=$id 
				GROUP BY h.id_head ORDER BY h.name";
		} else {
			$sql = "SELECT h.*, SUM(t.debit-t.credit) AS balance, 0.00 AS billamt FROM {$this->prefix}head h, {$this->prefix}tb t
		                WHERE h.debtor AND h.id_head=t.id_head GROUP BY h.id_head ORDER BY h.name";
		}
        $party = $this->m->sql_getall($sql, 1, "", "id_head");
		
        $sdate = $_SESSION['sdate'];
		$sql = "SELECT id_head, SUM(total) AS total FROM {$this->prefix}sale WHERE `date`<'$sdate' AND id_head GROUP BY 1 ORDER BY 1";
        $os = $this->m->sql_getall($sql, 2, "total", "id_head");
		foreach ($os as $k => $v) {
			if (isset($party[$k])) {
				$party[$k]['billamt'] = $v;
			}
		}
		if ($mismatch) {
			foreach ($party as $k => $v) {
				if ($v['billamt']!=$v['opening_balance']) {
					$newparty[$k] = $v;
				}
			}
        		$this->sm->assign("head", $newparty);
		} else {
        		$this->sm->assign("head", $party);
		}
        $salesman = $this->m->sql_getall("SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name", 2, "name", "id");
        $this->sm->assign("salesman", $salesman);
    }

    function fixobledger() {
    }

    function showdetail() {
	$id = $_REQUEST['id'];
        $sdate = $this->format_date($_SESSION['sdate']);
        $sdate = $_SESSION['sdate'];
        $sql = "SELECT s.id_sale, s.invno, s.date, s.id_represent, s.id_company, s.total, c.name AS cname, r.name AS rname FROM {$this->prefix}sale s, {$this->prefix}represent r, {$this->prefix}company c WHERE s.id_head = $id AND s.id_represent=r.id_represent AND s.id_company=c.id_company AND `date`<'$sdate' ORDER BY date";
        $osbills = $this->m->getall($this->m->query($sql));
	$this->sm->assign("os", $osbills);
	$sql = "SELECT * FROM {$this->prefix}head h WHERE id_head=$id";
        $head = $this->m->getall($this->m->query($sql));
	$this->sm->assign("head", $head);
    }

    function setcreditlimit() {
        $this->get_permission("head", "UPDATE");
        $data[$_REQUEST['field']] = $_REQUEST['fvalue'];
        $this->m->query($this->create_update("{$this->prefix}head", $data, "id_head='{$_REQUEST['id']}'"));
        echo 1;
        exit;
    }

    function printrep($id = "") {
        $this->get_permission("head", "REPORT");
        $id = ($id != "") ? $id : $_REQUEST['id'];
	$sql = "SELECT * FROM {$this->prefix}head WHERE id_head IN ($id) ";
        $res1 = $this->m->sql_getall($sql);
        $this->sm->assign("head", $res1);
    }

    function call() {
	$this->get_permission("head", "REPORT");
        $sql = "SELECT h.* FROM {$this->prefix}head h WHERE debtor ORDER BY h.name";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $profile);
    }

function obchange() {
	$this->get_permission("head", "REPORT");
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
	$sdate = $_SESSION['sdate'];
	$edate = $_SESSION['edate'];
	$sql = "SELECT s.id_sale, s.date, s.invno, s.id_area, s.id_company, s.id_represent, s.id_head, s.total, h.opening_balance, h.id_area, h.name
		FROM {$this->prefix}head h LEFT JOIN {$this->prefix}sale s ON h.id_head=s.id_head AND !(s.date>='$sdate' AND s.date<='$edate')  
        WHERE h.id_head = '$id' ORDER BY date";
	$this->sm->assign("data", $this->m->getall($this->m->query($sql)));
	$company = $this->m->sql_getall("SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name", 2, "name", "id");
	$this->sm->assign("company", $company);
	$salesman = $this->m->sql_getall("SELECT id_represent AS id,name FROM {$this->prefix}represent ORDER BY name", 2, "name", "id");
	$this->sm->assign("salesman", $salesman);
}

    function saveobchange() {
	$this->get_permission("head", "REPORT");
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
	$sdate = $_SESSION['sdate'];
	$edate = $_SESSION['edate'];
	//$sql = "DELETE FROM {$this->prefix}sale WHERE id_head='$id' AND !(date>='$sdate' AND date<='$edate')";
	$sql = "UPDATE {$this->prefix}sale SET id_head=-id_head WHERE id_head='$id' AND !(date>='$sdate' AND date<='$edate')";
	$this->m->query($sql);
	$data1 = $_REQUEST;
	for ($i = 0; $i < count($_REQUEST['billtotal']); $i++) {
		if ($_REQUEST['billtotal'][$i]) {
			$date = $this->format_date($_REQUEST['date'][$i]);
			$data = array("taxbill"=>1, "cash"=>1,
			"invno" => "{$data1['invno'][$i]}", 
			"date" => "{$date}",
			"id_head" => "{$data1['id_head']}",
			"id_area" => "{$data1['id_area']}",
			"total" => "{$data1['billtotal'][$i]}", 
			"id_company" => "{$data1['company'][$i]}", 
			"id_represent" => "{$data1['represent'][$i]}");
			$this->m->query($this->create_insert("{$this->prefix}sale", $data));
		}
	}
	$this->redirect("index.php?module=head&func=listing");
    }

    function reminder() {
        $sql = "SELECT * FROM {$this->prefix}head WHERE debtor ORDER BY name";
        $profile = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $profile);
    }
    function tcs() {
        $this->get_permission("head", "REPORT");
        $sql = "SELECT h.* FROM {$this->prefix}head h WHERE h.debtor ORDER BY h.name";
        $party = $this->m->sql_getall($sql, 1, "", "id_head");
        $this->sm->assign("head", $party);
    }
    function headuser() {
        $sql = "SELECT h.* FROM {$this->prefix}head h WHERE h.debtor ORDER BY h.name";
        $party = $this->m->sql_getall($sql, 1, "", "id_head");
        $this->sm->assign("head", $party);
    }
    function dopening() {
        $this->get_permission("head", "REPORT");
        $sql = "SELECT id_head, name, address1, address2, opening_balance, otype, status
                FROM {$this->prefix}head WHERE !status AND debtor ORDER BY name, address1";
        $head = $this->m->getall($this->m->query($sql));
        $this->sm->assign("head", $head);

        $sdate = $_SESSION['sdate'];
		$sql = "SELECT id_head, SUM(total) AS total, MAX(date) AS date FROM {$this->prefix}sale WHERE `date`<'$sdate' AND id_head GROUP BY 1";
        $os = $this->m->sql_getall($sql, 3, "", "id_head");
        $this->sm->assign("os", $os);
    }
}
?>
