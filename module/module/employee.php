<?php

class employee extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
	
	function listing() {
		$this->redirect("index.php?module=salary&func=listing");
	}
}
