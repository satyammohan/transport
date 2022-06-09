<?php
include ("import_dbf.php");
class import extends common {
    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }
    function _default() {
        echo "This function is not enabled...";
    }
    function remove($prefix) {
        $sql = "SHOW TABLES LIKE '{$prefix}_%'";
        $rs = $this->m->query($sql);
        $records = $this->m->num_rows($rs);
        for ($i = 1; $i <= $records; $i++) {
            $data = $this->m->fetch_array($rs);
            $this->m->query("DROP TABLE `{$data[0]}`");
        }
        print("Tables for <b>{$prefix}</b> removed Successfully.<br>");
    }
    function make_transport($prefix) {
        $sql = "ALTER TABLE `{$prefix}_transport`
            CHANGE `transport` `name` VARCHAR( 60 ),
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL";
        $this->m->query($sql);
        echo "Transport created Successfully<br>";
    }
    function make_vomaster($prefix) {
        $sql = "ALTER TABLE `{$prefix}_vowner`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL,
            DROP `consider`, ADD INDEX ( `code` )  ";
        $this->m->query($sql);
        echo "Vomaster converted Successfully<br>";
    }
    function make_vehicle($prefix) {
        $sql = "ALTER TABLE `{$prefix}_vehicle`
            ADD `id_vowner` INT( 11 ) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_vehicle` h, `{$prefix}_vowner` a SET h.id_vowner = a.id_vowner WHERE h.vocode = a.code";
        $this->m->query($sql);
        echo "Vehicle converted Successfully<br>";
    }
    function make_product($prefix) {
        $sql = "ALTER TABLE `{$prefix}_product`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        echo "Product converted Successfully<br>";
    }
    function make_lr($prefix) {
        $sql = "ALTER TABLE `{$prefix}_lr`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        echo "LR converted Successfully<br>";
    }
    function make_lrdet($prefix) {
        $sql = "ALTER TABLE `{$prefix}_lrdet`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        echo "LRdet converted Successfully<br>";
    }
    function make_bill($prefix) {
        $sql = "ALTER TABLE `{$prefix}_bill`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL, ADD INDEX ( `invno` ) ";
        $this->m->query($sql);
        echo "Bill converted Successfully<br>";
    }
    function make_billdet($prefix) {
        $sql = "ALTER TABLE `{$prefix}_billdet`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL, ADD INDEX ( `invno` ) ";
        $this->m->query($sql);
        echo "Bill Detail converted Successfully<br>";
    }
    function convert_all() {
        set_time_limit(6000);
        $sql = "SET SESSION sql_mode = ''";
        $this->m->query($sql);
        $prefixcsv = "data/" . trim($this->prefix, "_") . "csv";
        @mkdir($prefixcsv);

        $prefix = "data/" . trim($this->prefix, "_");
        if (file_exists($prefix)) {
            echo "Directory Exists<br>";
        } else {
            mkdir($prefix);
        }
        /* ADVANCE, AREA, BILL, BILLDET, BOOK, CASHSALE, COMPANY, GROUP, HEAD, ITEM, LR, LRDET, MODE, PARAM, PRODUCT, PURC, RATES, SALE, SALESMAN, TRANSPOR, VDETAIL, VEHICLE, VOMASTER, VOUCHER */
        
        $this->remove($prefix);
        $this->insert_data_from_dir($prefix);
        $this->make_salesman($prefix);
        $this->make_area($prefix);
        $this->make_group($prefix);
        $this->make_head($prefix);
        $this->make_company($prefix);
        $this->make_book($prefix);
        $this->make_product($prefix);
        $this->make_item($prefix);
        $this->make_advance($prefix);
        $this->make_mode($prefix);
        $this->make_rates($prefix);
        $this->make_transport($prefix);
        $this->make_vdetail($prefix);
        $this->make_vomaster($prefix);
        $this->make_vehicle($prefix);

        $this->make_lr($prefix);
        $this->make_lrdet($prefix);
        $this->make_bill($prefix);
        $this->make_billdet($prefix);
        // $this->make_purc($prefix);
        // $this->make_sale($prefix);
        // $this->make_voucher($prefix);
        $this->export($prefix);
        $dirHandle = opendir($prefix);
        while ($file = readdir($dirHandle)) {
            if (!is_dir($file)) {
                unlink("$prefix/" . "$file");
            }
        }
        closedir($dirHandle);
        $dirHandle = opendir($prefixcsv);
        while ($file = readdir($dirHandle)) {
            if (!is_dir($file)) {
                unlink("$prefix/" . "$file");
            }
        }
        closedir($dirHandle);
        rmdir($prefix);
        rmdir($prefixcsv);
	    ob_clean();
        echo "Tables for <b>{$prefix}</b> Exported to Inventory Database Successfully.";
        $this->redirect("index.php?module=util&func=check");
    }
    function export($dir) {
        $sql = "SHOW TABLES LIKE '{$dir}_%'";
        $rs = $this->m->query($sql);
        $records = $this->m->num_rows($rs);
        for ($i = 1; $i <= $records; $i++) {
            $data = $this->m->fetch_array($rs);
            $tblname = explode("_", $data[0]);
            $new = trim($this->prefix, "_") . "__" . end($tblname);
            $sql = "DROP TABLE IF EXISTS `{$new}`;";
            $this->m->query($sql);
            $sql = "RENAME TABLE `{$data[0]}` TO `{$new}`";
            $this->m->query($sql);
        }
        print("Tables for <b>{$dir}</b> Exported to Inventory Database Successfully.<br>");
    }
    function get_files($dir) {
        $files = array();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                $arr = explode('.', $entry);
                $ext = end($arr);
                if ($entry != "." && $entry != ".." && $ext != "FPT" && !is_dir($entry)) {
                    $files[] = $entry;
                }
            }
            closedir($handle);
        }
        return $files;
    }
    function insert_data_from_dir($dir) {
        $files = $this->get_files($dir);
        $files = array_diff($files, ["chal.dbf", "chaldet.dbf", "CHAL.DBF", "CHALDET.DBF"]);
        $this->m->query("START TRANSACTION");
        print "<table border=1><tr><th>Table Name</th><th>Records</th><th>Data Inserted</th><th>Deviation</th><th>Fields</th><th>Field List</th></tr>";
        foreach ($files as $k => $v) {
            echo "<tr>";
            $arr = explode('.', $v);
            $file = array_shift($arr);
            $fullpath = $dir . "/" . $file;
            $file = strtolower($file);
            switch (strtolower($file)) {
                case "purc":
                    $file = "purchase";
                    break;
                case "salesman":
                    $file = "represent";
                    break;
                case "tax_stru":
                    $file = "taxmaster";
                    break;
                case "vomaster":
                    $file = "vowner";
                    break;
                case "transpor":
                    $file = "transport";
                    break;
                default:
                    $file = strtolower($file);
            }
            if (!($file=="csv" or $file=="vat317")) { 
                    $Test = new fox_dbf($fullpath);
                    $this->get_create_sql($Test, $file, $dir);
                    $ins = $this->get_insert_sql($Test, $file, $dir);
                    $diff = $Test->records() - $ins == 0 ? "" : $Test->records() - $ins;
                    echo "<td>" . $v . "</td><td>" . $Test->records() . "</td><td>" . $ins . "</td><td>" . $diff . "</td><td>" . count($Test->fields()) . "</td>";
                    echo "</tr>";
            }
        }
        echo "</table>";
        $this->m->query("COMMIT");
        echo "Data Import. Successful for <b>$dir</b> Folder.<br>";
    }
    function get_insert_sql($Test, $file, $dir) {
        global $db;
        $inserts = 0;
        $sql = "INSERT INTO `{$dir}_{$file}` VALUES (";
        while (($Record = $Test->GetNextRecord(true)) and ! empty($Record)) {
            $newRecord = array();
            foreach ($Record as $k => $v) {
                $nk = strtolower($k);
                if (strpos($nk, 'date') !== false) {
                    if ($v) {
                        $newRecord[$nk] = date("Y/m/d", strtotime($v));
                    } else {
                        $newRecord[$nk] = NULL;
                    }
                } else {
                    $newRecord[$nk] = addslashes($v);
                }
            }
            $masters = array("group", "head", "area", "mode", "company", "item", "vowner");
            if (in_array( $file, $masters)) {
                if ($newRecord['code']=="") {
                    $newRecord = [];
                }
            }
            $masters = array("param", "book");
            if (in_array( $file, $masters)) {
                if ($newRecord['name']=="") {
                    $newRecord = [];
                }
            }
            $masters = array("bill", "billdet");
            if (in_array( $file, $masters)) {
                if ($newRecord['invno']=="") {
                    $newRecord = [];
                }
            }
            $val = $this->create_sql_from_array($newRecord);
            if ($val) {
                $isql = $sql . $val . ");";
                ++$inserts;
                $this->m->query($isql);
            }
        }
        return $inserts;
    }
    function create_sql_from_array($rec) {
        if (strlen(implode('', array_values($rec))) == 0) {
            return "";
        } else {
            return "'','" . implode("','", array_values($rec)) . "'";
        }
    }
    function get_create_sql($Test, $file, $dir) {
        $fields = $Test->fields();
        $sql = "DROP TABLE IF EXISTS `{$dir}_{$file}`;";
        $this->m->query($sql);
        $sql = "CREATE TABLE `{$dir}_{$file}` ( `id_{$file}` int(11) NOT NULL AUTO_INCREMENT, ";
        foreach ($fields as $k => $v) {
            switch ($v['Type']) {
                case "C":
                    $type = "VARCHAR ({$v['Size']})";
                    break;
                case "L":
                    $type = "VARCHAR (3)";
                    break;
                case "D":
                    $type = "DATE";
                    break;
                case "M":
                    $type = "TEXT";
                    break;
                case "N":
                    if ($v['Size'] == 1) {
                        $type = "TINYINT (1)";
                    } elseif ($v['Size'] >= 8) {
                        $type = "DECIMAL (16, 4) DEFAULT 0";
                    } else {
                        $size = $v['Size'] + 4;
                        $type = "DECIMAL ({$size}, 2) DEFAULT 0";
                    }
                    break;
                default:
                    print "***";
                    exit;
            }
            $sql .= '`' . strtolower($v['Name']) . "` " . $type . ", ";
        }
        $sql = trim($sql, ", ") . ", PRIMARY KEY (`id_{$file}`) ) ENGINE=MyISAM ";
        $this->m->query($sql);
        return;
    }
    function make_taxmaster($prefix) {
        $sql = "SHOW TABLES LIKE '{$prefix}_taxmaster'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            $sql = "SHOW TABLES LIKE '{$this->$prefix}_taxmaster'";
            $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
            if ($table == 0) {
                $sql = "CREATE TABLE `{$prefix}_taxmaster` (SELECT * FROM `{$this->prefix}taxmaster`)";
            } else {
                $sql = "CREATE TABLE `{$prefix}_taxmaster` (
                `id_taxmaster` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(30) DEFAULT NULL,
                `tax_per` decimal(6,2) DEFAULT NULL,
                `description` text NOT NULL,
                `status` tinyint(1) NOT NULL,
                `ip` varchar(30) NOT NULL,
                `id_create` int(11) NOT NULL,
                `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `id_modify` int(11) NOT NULL,
                `modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id_taxmaster`)) ENGINE=MyISAM";
            }
            $this->m->query($sql);
            echo "TaxMaster Created Successfully<br>";
        } else {
            $sql = "ALTER TABLE `{$prefix}_taxmaster` CHANGE `taxstr` `name` VARCHAR( 30 ) NULL DEFAULT NULL,
            CHANGE `taxrate` `tax_per` DECIMAL( 6, 2 ) NULL DEFAULT NULL";
            $this->m->query($sql);
            $sql = "ALTER TABLE `{$prefix}_taxmaster` ADD `description` TEXT NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
            $this->m->query($sql);
            echo "TaxMaster converted Successfully<br>";
        }
    }
    function make_voucher($prefix) {
        $sql = "CREATE TABLE `{$prefix}_voucher_temp` (
            `id_voucher` int(11) NOT NULL AUTO_INCREMENT,
            `date` date NOT NULL,
            `type` int(5) NOT NULL,
            `no` VARCHAR (11) NOT NULL,
            `total` decimal(16,2) NOT NULL,
            `memo` text NOT NULL,
            `id_head_debit` INT (11) NOT NULL,
            `id_head_credit` INT (11) NOT NULL,
            `id_no_debit` INT (11) NULL,
            `id_no_credit` INT (11) NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            `dhead` VARCHAR (4) NOT NULL,
            `chead` VARCHAR (4) NOT NULL,
            `ref1` VARCHAR (10) NOT NULL,
            `ref2` VARCHAR (10) NOT NULL,
            `dbill` VARCHAR (10) NOT NULL,
            `cbill` VARCHAR (10) NOT NULL,
            `reconcile` VARCHAR (1) NOT NULL,
            PRIMARY KEY (`id_voucher`) ) ENGINE=MyISAM";
        $this->m->query($sql);
        $sql = "INSERT INTO `{$prefix}_voucher_temp` (`date`, `type`, `no`, `total`, `memo`, `dhead`, `chead`, `ref1`, `ref2`, `dbill`, `cbill`, `reconcile`) 
                SELECT `date`, 4, `no`, `amt`, `narration`, `dhead`, `chead`, `ref1`, `ref2`, `dbill`, `cbill`, `reconcile` FROM `{$prefix}_voucher` WHERE code='O';";
        $this->m->query($sql);
        $sql = "DROP TABLE `{$prefix}_voucher`";
        $this->m->query($sql);
        $sql = "RENAME TABLE `{$prefix}_voucher_temp` TO `{$prefix}_voucher`";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_voucher` ADD INDEX ( `chead` )";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_voucher` ADD INDEX ( `dhead` )";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_voucher` v, `{$prefix}_head` h SET v.id_head_credit=h.id_head WHERE v.chead=h.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_voucher` v, `{$prefix}_head` h SET v.id_head_debit=h.id_head WHERE v.dhead=h.code";
        $this->m->query($sql);
        echo "Voucher converted Successfully<br>";
    }
    function make_purc($prefix) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}_purchase_temp` (
            `id_purchase` int(11) NOT NULL AUTO_INCREMENT,
            `taxbill` tinyint(1) NOT NULL,
            `no` varchar(20) NOT NULL,
            `date` date NOT NULL,
            `cash` tinyint(1) NOT NULL,
            `id_head` int(11) NOT NULL,
            `id_company` int(11) NOT NULL,
            `id_area` int(11) NOT NULL,
            `id_represent` int(11) NOT NULL,
            `bill_no` varchar(20) NOT NULL,
            `bill_date` date NOT NULL,
            `totalamt` decimal(16,2) NOT NULL,
            `discount` decimal(16,2) NOT NULL,
            `vat` decimal(16,2) NOT NULL,
            `packing` decimal(16,2) NOT NULL,
            `add` decimal(16,2) NOT NULL,
            `less` decimal(16,2) NOT NULL,
            `round` decimal(16,2) NOT NULL,
            `tcsper` decimal(10,3) NOT NULL,
            `tcsamt` decimal(16,2) NOT NULL,
            `total` decimal(16,2) NOT NULL,
            `pending` decimal(16,2) NOT NULL,
            `memo` text NOT NULL,
            `is_payment` tinyint(1) NOT NULL,
            `bank` varchar(35) NOT NULL,
            `cheque_no` varchar(25) NOT NULL,
            `cheque_date` date NOT NULL,
            `cheque_amount` decimal(16,2) NOT NULL,
            `is_form` tinyint(1) NOT NULL,
            `id_form` int(11) NOT NULL,
            `form_type` varchar(30) NOT NULL,
            `form_no` varchar(30) NOT NULL,
            `form_date` date NOT NULL,
            `form_amount` decimal(16,2) NOT NULL,
            `is_waybill` tinyint(1) NOT NULL,
            `waybill_no` varchar(30) NOT NULL,
            `waybill_date` date NOT NULL,
            `waybill_amount` decimal(16,2) NOT NULL,
            `lr_no` varchar(20) NULL,
            `lr_date` date NULL,
            `transport_mr_date` date NULL,
            `transport_no` varchar (30) NULL,
            `station` VARCHAR( 40 ) NULL,
            `gatename` VARCHAR( 40 ) NULL,
            `vehicle` VARCHAR( 40 ) NULL,
            `bales` VARCHAR( 20 ) NULL,
            `freight` decimal(16,2)  NULL,
            `cases` varchar(10) NULL,
            `weight` decimal(16,4) NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            `ic` decimal (16,2) NOT NULL,
            `cd` decimal (5,2) NOT NULL,
            `cdamt` decimal (16,2) NOT NULL,
            `party_name` varchar (30) NOT NULL,
            `party_address` varchar (30) NOT NULL,
            `party_address1` varchar (30) NOT NULL,
            `party_vattype` varchar (5) NOT NULL,
            `party_vatno` varchar (20) NOT NULL,            
            `type` varchar (1) NOT NULL,
            `company` varchar (2) NOT NULL,
            `cust` varchar (4) NOT NULL,
            `salesman` varchar (2) NOT NULL,
            `area` varchar (2) NOT NULL,
            PRIMARY KEY (`id_purchase`) ) ENGINE=MyISAM";
        $this->m->query($sql);
        $sql = "SHOW COLUMNS FROM `{$prefix}_purchase` LIKE 'waybill'";
        $waybill = $this->m->num_rows($this->m->query($sql)) == 0 ? "''" : "`waybill`";

        $sql = "SHOW COLUMNS FROM `{$prefix}_purchase` LIKE 'tcsamt'";
        $tcsper = $this->m->num_rows($this->m->query($sql)) == 0 ? "''" : "`tcsper`";
        $tcsamt = $this->m->num_rows($this->m->query($sql)) == 0 ? "''" : "`tcsamt`";

        $sql = "INSERT INTO `{$prefix}_purchase_temp` (`type`, `no`, `bill_no`, `company`, `bill_date`, `date`, `cust`, `cash`, `area`, `salesman`, `cd`, `vat`, `add`, `less`, `packing`, `total`, `pending`, `memo`, `waybill_no`, `tcsper`, `tcsamt`, taxbill ) 
                SELECT `type`, `no`, `bno`, `company`, `idate`, `rdate`, `cust`, `cash`, `area`, `salesman`, `cd`, `purctax`, `extra1`, `extra2`, `extra3`, `total`, `pending`, `narration`, {$waybill}, {$tcsper}, {$tcsamt}, 1 FROM `{$prefix}_purchase`;";
        $this->m->query($sql);
        $sql = "DROP TABLE `{$prefix}_purchase`";
        $this->m->query($sql);
        $sql = "RENAME TABLE `{$prefix}_purchase_temp` TO `{$prefix}_purchase`";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchase` s, `{$prefix}_area` a SET s.id_area=a.id_area WHERE s.area=a.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchase` s, `{$prefix}_represent` r SET s.id_represent=r.id_represent WHERE s.salesman=r.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchase` s, `{$prefix}_head` h SET s.id_head=h.id_head, s.party_name=h.name, s.party_address=h.address1, s.party_address1=h.address2, s.party_vattype=h.vattype, s.party_vatno=h.vatno WHERE s.cust=h.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchase` s, `{$prefix}_company` c SET s.id_company=c.id_company WHERE s.company=c.code";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_purchase` ADD INDEX ( `no` );";
        $this->m->query($sql);
        echo "Purchase converted Successfully<br>";
    }
    function make_sale($prefix) {
        $sql = "SHOW COLUMNS FROM `{$prefix}_sale` LIKE 'ic'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_sale` ADD `ic` DECIMAL ( 12,2 ) NOT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_sale` LIKE 'totalcess'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_sale` ADD `totalcess` DECIMAL ( 16,2 ) NOT NULL;";
            $this->m->query($sql);
        }
        $sql = "CREATE TABLE `{$prefix}_sale_temp` (
            `id_sale` int(11) NOT NULL AUTO_INCREMENT,
            `taxbill` tinyint(1) NOT NULL,
            `invno` varchar(20) NOT NULL,
            `date` date NOT NULL,
            `cash` tinyint(1) NOT NULL,
            `id_head` int(11) NOT NULL,
            `id_company` int(11) NOT NULL,
            `id_area` int(11) NOT NULL,
            `id_represent` int(11) NOT NULL,
            `challan_no` varchar(20) NOT NULL,
            `challan_date` date NOT NULL,
            `totalamt` decimal(16,2) NOT NULL,
            `discount` decimal(16,2) NOT NULL,
            `vat` decimal(16,2) NOT NULL,
            `totalcess` decimal(16,2) NOT NULL,
            `packing` decimal(16,2) NOT NULL,
            `add` decimal(16,2) NOT NULL,
            `less` decimal(16,2) NOT NULL,
            `round` decimal(16,2) NOT NULL,
            `tcsper` decimal(10,3) NOT NULL,
            `tcsamt` decimal(16,2) NOT NULL,
            `total` decimal(16,2) NOT NULL,
            `pending` decimal(16,2) NOT NULL,
            `memo` text NOT NULL,
            `is_payment` tinyint(1) NOT NULL,
            `bank` varchar(35) NOT NULL,
            `cheque_no` varchar(25) NOT NULL,
            `cheque_date` date NOT NULL,
            `cheque_amount` decimal(16,2) NOT NULL,
            `is_form` tinyint(1) NOT NULL,
            `id_form` int(11) NOT NULL,
            `form_type` varchar(30) NOT NULL,
            `form_no` varchar(30) NOT NULL,
            `form_date` date NOT NULL,
            `form_amount` decimal(16,2) NOT NULL,
            `is_waybill` tinyint(1) NOT NULL,
            `waybill_no` varchar(30) NOT NULL,
            `waybill_date` date NOT NULL,
            `waybill_amount` decimal(16,2) NOT NULL,
            `is_transport` tinyint(1) NOT NULL,
            `id_transport` int(11) NOT NULL,
            `lr_no` varchar(20) NOT NULL,
            `lr_date` date NOT NULL,
            `cases` varchar(10) NOT NULL,
            `weight` decimal(16,4) NOT NULL,
            `station` VARCHAR( 40 ) NOT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            `ic` decimal (16,2) NOT NULL,
            `cd` decimal (5,2) NOT NULL,
            `cdamt` decimal (16,2) NOT NULL,
            `party_name` varchar (30) NOT NULL,
            `party_address` varchar (30) NOT NULL,
            `party_address1` varchar (30) NOT NULL,
            `party_vattype` varchar (5) NOT NULL,
            `party_vatno` varchar (20) NOT NULL,            
            `company` varchar (2) NOT NULL,
            `cust` varchar (4) NOT NULL,
            `salesman` varchar (2) NOT NULL,
            `area` varchar (2) NOT NULL,
            PRIMARY KEY (`id_sale`) ) ENGINE=MyISAM";
        $this->m->query($sql);

        $sql = "SHOW COLUMNS FROM `{$prefix}_sale` LIKE 'tcsamt'";
        $tcsper = $this->m->num_rows($this->m->query($sql)) == 0 ? "''" : "`tcsper`";
        $tcsamt = $this->m->num_rows($this->m->query($sql)) == 0 ? "''" : "`tcsamt`";
        $sql = "INSERT INTO `{$prefix}_sale_temp` (`invno`, `date`, `challan_no`, `challan_date`, `company`, `cash`, `cust`, `salesman`, `area`, `ic`, `cd`, `vat`, `add`, `less`, `round`, `total`, totalcess, `memo`, `tcsper`, `tcsamt`, `taxbill`) 
                SELECT `invno`, `idate`, `bno`, `bdate`, `company`, `cash`, `cust`, `salesman`, `area`, `ic`, `cd`, `salestax`, `extra1`, `extra2`, `extra3`, `total`, totalcess, `narration`, {$tcsper}, {$tcsamt}, 1 FROM `{$prefix}_sale`;";
        $this->m->query($sql);
        $sql = "DROP TABLE `{$prefix}_sale`";
        $this->m->query($sql);
        $sql = "RENAME TABLE `{$prefix}_sale_temp` TO `{$prefix}_sale`";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_sale` s, `{$prefix}_area` a SET s.id_area=a.id_area WHERE s.area=a.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_sale` s, `{$prefix}_represent` r SET s.id_represent=r.id_represent WHERE s.salesman=r.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_sale` s, `{$prefix}_head` h SET s.id_head=h.id_head, s.party_name=h.name, s.party_address=h.address1, s.party_address1=h.address2, s.party_vattype=h.vattype, s.party_vatno=h.vatno WHERE s.cust=h.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_sale` s, `{$prefix}_company` c SET s.id_company=c.id_company WHERE s.company=c.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_sale` s, `{$prefix}_cashsale` c SET s.party_name=c.name, s.party_address=c.address, s.party_vatno=c.ostno WHERE s.invno=c.invno AND c.type='S'";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_sale` ADD INDEX ( `invno` );";
        $this->m->query($sql);
        echo "Sale converted Successfully<br>";
    }
    function make_item($prefix) {
        $sql = "ALTER TABLE `{$prefix}_item`  ADD `status` TINYINT( 1 ) NOT NULL, ADD `ip` VARCHAR( 30 ) NOT NULL, ADD `id_create` INT( 11 ) NOT NULL, ADD `create_date` TIMESTAMP NOT NULL, ADD `id_modify` INT( 11 ) NOT NULL, ADD `modify_date` TIMESTAMP NOT NULL, ADD `id_company` INT( 11 ) NOT NULL, ADD `id_product` INT( 11 ) NOT NULL, ADD INDEX ( `code` ); ";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_item` i, `{$prefix}_product` p SET i.id_product = p.id_product WHERE i.product = p.code";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_item` i, `{$prefix}_company` p SET i.id_company = p.id_company WHERE i.company = p.code";
        $this->m->query($sql);
        echo "Item converted Successfully<br>";
    }
    function make_book($prefix) {
        $sql = "ALTER TABLE `{$prefix}_book` ADD `id_head` INT (11) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_book` b, `{$prefix}_head` h SET b.id_head = h.id_head WHERE b.head = h.code";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_book` DROP `head`";
        $this->m->query($sql);
        echo "Book converted Successfully<br>";
    }
    function make_company($prefix) {
        $sql = "ALTER TABLE `{$prefix}_company`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL,  ADD INDEX ( `code` ); ";
        $this->m->query($sql);
        echo "Company converted Successfully<br>";
    }
    function make_salesman($prefix) {
        $sql = "ALTER TABLE `{$prefix}_represent` CHANGE `address` `address` TEXT  NULL DEFAULT NULL,
            ADD `phone` TEXT NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL, ADD INDEX ( `code` ); ";
        $this->m->query($sql);
        echo "Salesman converted to Represent Table Successfully<br>";
    }
    function make_area($prefix) {
        //ALTER TABLE `mch23__area` ADD `ddd` INT NOT NULL AUTO_INCREMENT AFTER `zone`, ADD PRIMARY KEY (`ddd`);
        $sql = "ALTER TABLE `{$prefix}_area` ADD `description` TEXT NOT NULL,
            ADD `id_zone` INT( 11 ) NOT NULL,
            ADD `id_represent` INT( 10 ) NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);

        $sql = "ALTER TABLE `{$prefix}_area` ADD INDEX ( `code` );";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_area` a, `{$prefix}_represent` r SET a.id_represent = r.id_represent WHERE a.salesman=r.code";
        $this->m->query($sql);

        $sql = "SHOW COLUMNS FROM `{$prefix}_area` LIKE 'zone'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_area` ADD `zone` VARCHAR(6);";
            $this->m->query($sql);
        } else {
            $sql = "UPDATE `{$prefix}_area` a, `{$prefix}_zone` r SET a.id_zone = r.id_zone WHERE a.zone=r.code";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_area` DROP `consider`";
        $this->m->query($sql);
        echo "Area converted Successfully<br>";
    }
    function make_group($prefix) {
        $sql = "ALTER TABLE `{$prefix}_group` DROP `rbal`, DROP `consider`, DROP `datebal` ";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_group` ADD `id_parent` INT( 11 ) NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_group` g, `{$prefix}_group` g1 SET g.id_parent = g1.id_group WHERE g.gcode = g1.code";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_group` ADD INDEX ( `code` );";
        $this->m->query($sql);
        echo "Group converted Successfully<br>";
    }
    function make_head($prefix) {
        $sql = "ALTER TABLE `{$prefix}_head`
            CHANGE `name` `name` VARCHAR( 60 ),
            CHANGE `obal` `opening_balance` DECIMAL( 16, 2 ),
            CHANGE `rbal` `closing_balance` DECIMAL( 16, 2 ),
            CHANGE `address1` `address2` VARCHAR( 60 ),
            CHANGE `ostno` `ost_no` VARCHAR( 60 ),
            CHANGE `ostdate` `ost_date` DATE,
            CHANGE `cstno` `cst_no` VARCHAR( 60 ),
            CHANGE `cstdate` `cst_date` DATE,
            ADD `debtor` smallint( 1 ) AFTER `name`,
            ADD `creditor` smallint( 1 ) AFTER `name`,
            ADD `id_group` INT( 11 ) NOT NULL AFTER `gcode`,
            ADD `id_area` INT( 11 ) NOT NULL AFTER `area`,
            ADD `email` TEXT NOT NULL AFTER `address`,
            ADD `contact_person` VARCHAR ( 50 ) NOT NULL AFTER `address`,
            ADD `pincode` VARCHAR ( 20 ) NOT NULL,
            ADD `mobile` TEXT NOT NULL,
            ADD `climit` DECIMAL ( 12,2 ),
            ADD `address3` VARCHAR( 60 ),
            ADD `cday` DECIMAL( 16, 2 ),
            ADD `gstin` VARCHAR(30),
            ADD `credit_limit` DECIMAL( 16, 2 ) NULL DEFAULT 0,
            ADD `credit_days` DECIMAL( 16, 2 ) NULL DEFAULT 0,
            ADD `vattype` VARCHAR ( 4 ),
            ADD `vatno` VARCHAR ( 11 ),
            ADD `dealer` INT(2) COMMENT '0-Distributor/Retailer, 1-Super-Distributor',
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL,
            ADD `message` varchar(200) NULL,
            ADD `dob` date NULL,
            ADD `doa` date  NULL,
            ADD `location` varchar(30) NULL,
            ADD `tanno` varchar(30) NULL,
            ADD `transport` varchar(60) NULL,
            ADD `banker` varchar(60) NULL,
            ADD `station` varchar(30) NULL,
            ADD `dlicence` VARCHAR(60) NULL,
            ADD `acno` VARCHAR(45) NULL,
            ADD `acifsc` VARCHAR(30) NULL,
            ADD `acname` VARCHAR(45) NULL,
            ADD `actype` VARCHAR(45) NULL,
            ADD `flicence` VARCHAR(40) NULL,
            ADD `tcs` int(1) NULL DEFAULT 0,
            ADD `tcsper` decimal(9,3) NULL DEFAULT 0,
            ADD `distance` varchar(10) NULL,
            ADD `statecode` varchar(2) NULL";



        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_head` CHANGE `address` `address1` VARCHAR( 60 )";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` h, `{$prefix}_group` g SET h.id_group = g.id_group WHERE h.gcode = g.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` h, `{$prefix}_area` a SET h.id_area = a.id_area WHERE h.area = a.code";
        $this->m->query($sql);
        $sql = "SELECT content FROM `{$prefix}_param` WHERE name = 'debtors'";
        $deb = $this->m->fetch_assoc($sql);
        $sql = "UPDATE `{$prefix}_head` SET debtor = 1 WHERE gcode = '{$deb['content']}'";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` SET local = 0";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` SET local = 1 WHERE gstin NOT LIKE '21%' AND gstin";
        $this->m->query($sql);
        $sql = "SELECT content FROM `{$prefix}_param` WHERE name = 'creditors'";
        $deb = $this->m->fetch_assoc($sql);
        $sql = "UPDATE `{$prefix}_head` SET creditor = 1 WHERE gcode = '{$deb['content']}'";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_head` ADD INDEX ( `code` )";
        $this->m->query($sql);
        echo "Head converted Successfully<br>";
    }
    function make_advance($prefix) {
        $sql = "ALTER TABLE `{$prefix}_advance`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL";
        $this->m->query($sql);
        echo "Advance converted Successfully<br>";
    }
    function make_mode($prefix) {
        $sql = "ALTER TABLE `{$prefix}_mode`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL,
            DROP `consider` ";
        $this->m->query($sql);        
        echo "Mode converted Successfully<br>";
    }
    function make_rates($prefix) {
        $sql = "ALTER TABLE `{$prefix}_rates`
            ADD `id_from_area` INT( 11 ) NOT NULL,
            ADD `id_to_area` INT( 11 ) NOT NULL,
            ADD `id_mode` INT( 11 ) NOT NULL,
            ADD `id_head` INT( 11 ) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` r, `{$prefix}_mode` m SET r.id_mode = m.id_mode WHERE r.mode = m.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` r, `{$prefix}_area` a SET r.id_from_area = a.id_area WHERE r.from = a.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` r, `{$prefix}_area` a SET r.id_to_area = a.id_area WHERE r.to = a.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` r, `{$prefix}_head` h SET r.id_head = h.id_head WHERE r.code = h.code";
        $this->m->query($sql);
        echo "Mood converted Successfully<br>";
    }
    function make_vdetail($prefix) {
        $sql = "ALTER TABLE `{$prefix}_vdetail`
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        echo "Vehicle detail converted Successfully<br>";
    }
}
?>