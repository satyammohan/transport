<?php
class report extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function vehicledetail() {
        $_REQUEST['start_date'] = $sdate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-01");
        $_REQUEST['end_date'] = $edate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("Y-m-d");


        $sql = "SELECT * FROM {$this->prefix}bill WHERE (date >= '$sdate' AND date <= '$edate') ORDER BY date";
        $data = $this->m->sql_getall($sql);
        $this->sm->assign("data", $data);
    }
}
?>