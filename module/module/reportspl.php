<?php

class reportspl extends common {

  function __construct() {
    $this->checklogin();
    $this->table_prefix();
    parent:: __construct();
  }

  function _default() {
    echo "This function is not enabled...";
  }

  function remi() {
    $_REQUEST['refno'] = isset($_REQUEST['refno']) ? $_REQUEST['refno'] : '1';
    $sql = "SELECT v.*, h.name, h.address1 FROM `{$this->prefix}head` h, `{$this->prefix}voucher` v WHERE h.id_head = id_head_credit AND `ref1`= {$_REQUEST['refno']}";
    $this->sm->assign("data", $this->m->sql_getall($sql));
  }

}

?>