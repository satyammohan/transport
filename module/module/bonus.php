<?php
class bonus extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function edit() {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("bonus", "INSERT");
        else
            $this->get_permission("bonus", "UPDATE");
		$sql = "SELECT b.*, p.name FROM {$this->prefix}bonus b, {$this->prefix}product p WHERE b.id_product=p.id_product AND id_bonus='{$id}'";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
    }

    function getbonus() {
        $this->get_permission("bonus", "UPDATE");
        $product = $_REQUEST['id_product'];
        $sql = "INSERT INTO {$this->prefix}bonus (id_product) (SELECT id_product FROM {$this->prefix}product p 
            WHERE p.id_product='$product' AND id_product NOT IN (SELECT DISTINCT id_product FROM {$this->prefix}bonus)) ";
        $this->m->query($sql);
	$sql = "SELECT b.*, p.name FROM {$this->prefix}bonus b, {$this->prefix}product p WHERE b.id_product=p.id_product AND p.id_product='$product'";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
	$this->sm->assign("page", "bonus/edit.tpl.html");
    }

    function update() {
        $this->get_permission("bonus", "UPDATE");
	$res = $this->m->query($this->create_update($this->prefix . "bonus", $_REQUEST['bonus'], "id_bonus='{$_REQUEST['id']}'"));
        if (@$_REQUEST['frombatch']!="0") {
            $_SESSION['msg'] = "Record Successfully Updated";
	    $id = $_REQUEST['id_company'];
            $this->redirect("index.php?module=bonus&func=listing&id_company=$id");
        } else {
            $this->redirect("index.php?module=bonus&func=getbonus&id_product={$_REQUEST['id_product']}&frombatch=0&ce=0");
        }
    }

    function delete() {
        $this->get_permission("bonus", "DELETE");
        $res = $this->m->query($this->create_delete($this->prefix . "bonus", "id_bonus='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Deleted";
        $this->redirect("index.php?module=bonus&func=listing");
    }

    function listing() {
        $this->get_permission("bonus", "REPORT");
		$sql = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE !status ORDER BY name";
        $company = $this->m->sql_getall($sql, 2, "name", "id");
        $this->sm->assign("company", $company);
		$id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : 0;
		if ($id) {
			$sql = "INSERT INTO {$this->prefix}bonus (id_product) (SELECT id_product FROM {$this->prefix}product p 
					WHERE p.id_company='$id' AND id_product NOT IN (SELECT DISTINCT id_product FROM {$this->prefix}bonus)) ";
			$this->m->query($sql);
			$sql = "SELECT b.*, p.name FROM {$this->prefix}bonus b, {$this->prefix}product p 
				WHERE b.id_product=p.id_product AND p.id_company='$id'
				ORDER BY p.name, b.startdate";
			$profile = $this->m->getall($this->m->query($sql));
			$this->sm->assign("bonus", $profile);
			$this->sm->assign("id_company", $id);
		}
    }
	
	function bonusstm() {
        $this->get_permission("bonus", "REPORT");
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("01/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));

        $id = isset($_REQUEST['id_company']) ? $_REQUEST['id_company'] : '0';
        $wcond = ($id != 0) ? "AND p.id_company='$id'" : "";
		
		$sql = "SELECT sd.id_product, sd.invno, sd.date, sd.qty, sd.free, c.name AS cname, h.name AS hname, p.name AS pname, b.qty AS bqty, b.free AS bfree 
				FROM {$this->prefix}product p, {$this->prefix}company c, {$this->prefix}head h, 
				{$this->prefix}saledetail sd LEFT JOIN (SELECT DISTINCT id_product, qty, free FROM {$this->prefix}bonus) b ON sd.id_product=b.id_product
			WHERE sd.id_product=p.id_product AND p.id_company=c.id_company AND (sd.date>='$sdate' AND sd.date<='$edate') 
				AND sd.id_head=h.id_head AND sd.free>0 {$wcond}
			ORDER BY cname, pname, sd.date, sd.invno";
		$this->sm->assign("bonus", $this->m->getall($this->m->query($sql)));
		$sql = "SELECT id_company AS id,name FROM {$this->prefix}company WHERE !status ORDER BY name";
        $this->sm->assign("company", $this->m->sql_getall($sql, 2, "name", "id"));
		$this->sm->assign("id_company", $id);
	}
}
?>
