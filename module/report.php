<?php
class report extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function vehicledetail() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-04-01");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");



        $_REQUEST['vehno'] = $vehno = isset($_REQUEST['vehno']) ? $_REQUEST['vehno'] : "";


        $sql = "SELECT b.*, a.name AS aname, c.name AS cname FROM {$this->prefix}billdet b, {$this->prefix}company c, {$this->prefix}area a
            WHERE (b.date >= '$sdate' AND b.date <= '$edate') AND b.vehno='$vehno' AND b.id_from_area=a.id_area AND b.id_company=c.id_company ORDER BY date";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
}
?>
