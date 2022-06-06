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
    function make_vomaster($prefix) {
        $sql = "ALTER TABLE `{$prefix}_vomaster` DROP `consider`";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_vomaster` ADD `id_vomaster` INT (11) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        echo "Vehicle converted Successfully<br>";
    }
    function make_vehicle($prefix) {
        $sql = "ALTER TABLE `{$prefix}_vehicle` ADD `id_vehicle` INT (11) NOT NULL,
            ADD `id_vomaster` INT( 11 ) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_vehicle` h, `{$prefix}_vomaster` a SET h.id_vomaster = a.id_vomaster WHERE h.vocode = a.code";
        $this->m->query($sql);
        echo "Vehicle converted Successfully<br>";
    }
    function convert_all() {
        set_time_limit(6000);
        $prefixcsv = "data/" . trim($this->prefix, "_") . "csv";
        @mkdir($prefixcsv);

        $prefix = "data/" . trim($this->prefix, "_");
        if (file_exists($prefix)) {
            echo "Directory Exists<br>";
        } else {
            mkdir($prefix);
        }
        /* ADVANCE, AREA, BILL, BILLDET, BOOK, CASHSALE, COMPANY, GROUP, HEAD, ITEM, LR, LRDET, MODE, PARAM, PRODUCT, PURC, RATES, SALE, SALESMAN, TRANSPOR, VDETAIL, VEHICLE, VOMASTER, VOUCHER */
        
        // $this->remove($prefix);
        // $this->insert_data_from_dir($prefix);
        // $this->make_salesman($prefix);
        // $this->make_area($prefix);
        // $this->make_group($prefix);
        // $this->make_head($prefix);
        // $this->make_company($prefix);
        // $this->make_book($prefix);
        $this->make_product($prefix);
        // $this->make_item($prefix);
        // $this->make_advance($prefix);
        // $this->make_mode($prefix);

        $this->make_rates($prefix);
        $this->make_transport($prefix);
        $this->make_vdetail($prefix);
        $this->make_vomaster($prefix);
        $this->make_vehicle($prefix);
echo "done";exit;

        $this->make_purc($prefix);
        $this->make_sale($prefix);
        $this->make_cashsale($prefix);
        $this->make_lr($prefix);
        $this->make_lrdet($prefix);
        $this->make_bill($prefix);
        $this->make_billdet($prefix);
        $this->make_voucher($prefix);
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
        //      $sql = "LOCK TABLES `{$dir}_{$file}` WRITE";
        //      $this->m->query($sql);
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
                        $type = "DECIMAL (16, 4)";
                    } else {
                        $size = $v['Size'] + 4;
                        $type = "DECIMAL ({$size}, 2)";
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

    function make_saledet($prefix) {
        $sql = "SELECT * FROM `{$prefix}_param` WHERE `name`='incltax' AND `type`='L' AND `content`='TRUE'";
        $taxincl = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;

        $sql = "DROP TABLE IF EXISTS `{$prefix}_saledetail_temp`";
        $this->m->query($sql);
        $sql = "SHOW COLUMNS FROM `{$prefix}_saledetail` LIKE 'cessper'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_saledetail` ADD `cessper` decimal(10,2) NOT NULL, ADD `cessamt` decimal(15,2) NOT NULL;";
            $this->m->query($sql);
        }
        $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}_saledetail_temp` (
            `id_saledetail` int(11) NOT NULL AUTO_INCREMENT,
            `invno` varchar(20) NOT NULL,
            `date` date NOT NULL,
            `id_product` int(11) NOT NULL,
            `rate` decimal(15,4) NOT NULL,
            `case` decimal(12,4) NOT NULL,
            `qty` decimal(12,4) NOT NULL,
            `free` decimal(12,4) NOT NULL,
            `discount_type1` tinyint(2) NOT NULL,
            `discount1` decimal(10,4) NOT NULL,
            `discount_amount1` decimal(10,2) NOT NULL,
            `discount_type2` tinyint(2) NOT NULL,
            `discount2` decimal(10,4) NOT NULL,
            `discount_amount2` decimal(10,2) NOT NULL,
            `discount_type3` tinyint(2) NOT NULL,
            `discount3` decimal(10,4) NOT NULL,
            `discount_amount3` decimal(10,2) NOT NULL,
            `discount_type4` tinyint(2) NOT NULL,
            `discount4` decimal(10,4) NOT NULL,
            `discount_amount4` decimal(10,2) NOT NULL,
            `amount` decimal(15,2) NOT NULL,
            `goods_amount` decimal(15,2) NOT NULL,
            `id_taxmaster` int(11) NOT NULL,
            `tax_per` decimal(8,2) NOT NULL,
            `tax_amount` decimal(15,2) NOT NULL,
            `net_amount` decimal(15,2) NOT NULL,
            `cessper` decimal(10,2) NOT NULL,
            `cessamt` decimal(15,2) NOT NULL,
            `id_sale` int(11) NOT NULL,
            `id_company` int(11) NOT NULL,
            `id_head` int(11) NOT NULL,
            `id_represent` int(11) NOT NULL,
            `id_area` int(11) NOT NULL,
            `id_batch` int(11) NOT NULL,
            `batch_no` varchar(15) NOT NULL,
            `code` varchar (5) NOT NULL,
            `exp_date` varchar(15) NOT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_saledetail`) ) ENGINE=MyISAM";
        $this->m->query($sql);

        $sql = "SHOW COLUMNS FROM `{$prefix}_saledetail` LIKE 'discamt'";
        $discamt = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : "`discamt`";

        $sql = "SHOW COLUMNS FROM `{$prefix}_saledetail` LIKE 'splamt'";
        $splamt = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : "`splamt`";

        $sql = "SHOW COLUMNS FROM `{$prefix}_saledetail` LIKE 'batch'";
        $batch = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : "`batch`";

        $sql = "SHOW COLUMNS FROM `{$prefix}_saledetail` LIKE 'case'";
        $case = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : "`case`";
        //if ($case) {
        //$splamt = "`spl`*`case`";
        //}
        $sql = "SHOW COLUMNS FROM `{$prefix}_saledetail` LIKE 'totaltax'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "INSERT INTO `{$prefix}_saledetail_temp` (`invno`, `date`, `code`, `id_taxmaster`, `rate`, `discount_type1`, `discount1`, `discount_amount1`, `discount2`, `discount_amount2`, `case`, `qty`, `free`, `batch_no`, `amount`, cessper, cessamt) 
                SELECT `invno`, `idate`, `code`, `tax`, `rate`, IF(scheme='P', '0', IF(scheme='Q','1', IF(scheme='a','2', '3'))), `disc`, {$discamt}, `spl`, if(`spl`=0, 0, {$splamt}), {$case}, IF(`type`='S', `qty`, -`qty`) AS qty,  IF(`type`='S', `free`, -`free`)  AS free, {$batch},  IF(`type`='S', `amt`, -`amt`) AS amt, cessper, cessamt  FROM `{$prefix}_saledetail`;";
        } else {
            $sql = "INSERT INTO `{$prefix}_saledetail_temp` (`invno`, `date`, `code`, `id_taxmaster`, `rate`, `discount_type1`, `discount1`, `discount_amount1`,`discount2`, `discount_amount2`, `case`, `qty`, `free`, `batch_no`, `amount`, tax_amount, cessper, cessamt) 
                SELECT `invno`, `idate`, `code`, `tax`, `rate`, IF(scheme='P', '0', IF(scheme='Q','1', IF(scheme='a','2', '3'))), `disc`, {$discamt}, `spl`, if(`spl`=0, 0, {$splamt}), {$case}, IF(`type`='S', `qty`, -`qty`) AS qty,  IF(`type`='S', `free`, -`free`)  AS free, {$batch},  IF(`type`='S', `amt`, -`amt`) AS amt, IF(`type`='S', totaltax, -totaltax) AS totaltax, cessper, cessamt FROM `{$prefix}_saledetail`;";
        }
        $this->m->query($sql);

        $sql = "DROP TABLE `{$prefix}_saledetail`";
        $this->m->query($sql);
        $sql = "RENAME TABLE `{$prefix}_saledetail_temp` TO `{$prefix}_saledetail`";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d, `{$prefix}_product` p SET d.case=p.case, d.id_product=p.id_product WHERE d.code=p.code";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_saledetail` d, `{$prefix}_taxmaster` t SET d.tax_per=t.tax_per WHERE d.id_taxmaster=t.id_taxmaster";
        $this->m->query($sql);
        if ($taxincl == 1) {
            $sql = "UPDATE `{$prefix}_saledetail` SET `rate`=(rate/(100+tax_per)*100), `amount`=(amount/(100+tax_per)*100) WHERE tax_per!=0";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_saledetail` SET goods_amount=amount";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_saledetail` d SET tax_amount=ROUND(goods_amount*tax_per/100,2) WHERE tax_per!=0";
            $this->m->query($sql);
        }
        //$sql = "UPDATE `{$prefix}_saledetail` SET discount2=discount_amount2/`qty`, discount_type2=2 WHERE discount2!=0";
        //$this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_saledetail` ADD INDEX ( `invno` );";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d, `{$prefix}_sale` s SET d.id_area=s.id_area, d.id_represent=s.id_represent, d.discount3=s.cd,
                d.id_head=s.id_head, d.id_company=s.id_company, d.id_sale=s.id_sale WHERE d.invno=s.invno AND d.date=s.date";
        $this->m->query($sql);
        if (isset($_REQUEST['type']) && $_REQUEST['type'] == "srsk") {
            $sql = "UPDATE `{$prefix}_saledetail` d SET discount_amount1=IF(discount_type1=0, ROUND(amount*discount1/100,2), IF(discount_type1=1, qty*discount1/`case`, discount1)) WHERE discount1!=0 AND discount_amount1=0";
        } else {
            $sql = "UPDATE `{$prefix}_saledetail` d SET discount_amount1=IF(discount_type1=0, ROUND(amount*discount1/100,2), IF(discount_type1=1, qty*discount1, discount1)) WHERE discount1!=0 AND discount_amount1=0";
        }
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d SET discount_amount2=IF(discount_type2=0, ROUND((amount-discount_amount1)*discount2/100,2), IF(discount_type2=1, qty*discount2, discount2)) WHERE discount2!=0 AND discount_amount2=0";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d SET discount_amount3=ROUND((amount-discount_amount1-discount_amount2)*discount3/100,2) WHERE discount3!=0 AND discount_amount3=0";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d SET goods_amount=amount-discount_amount1-discount_amount2-discount_amount3";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d SET tax_amount=ROUND(goods_amount*tax_per/100,2) WHERE tax_amount=0";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_saledetail` d SET net_amount=goods_amount+tax_amount, `case`=`qty`/`case`";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_saledetail` ADD INDEX ( `id_sale` );";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_sale` s,  (SELECT id_sale, SUM(amount) AS ga, SUM(discount_amount1+discount_amount2+discount_amount3) AS da 
            FROM `{$prefix}_saledetail` GROUP BY id_sale) s1 SET s.totalamt=s1.ga, s.discount=s1.da WHERE s.id_sale=s1.id_sale";
        $this->m->query($sql);
        if ($taxincl == 1) {
            $sql = "UPDATE `{$prefix}_sale` s,  (SELECT id_sale, SUM(tax_amount) AS xa
                FROM `{$prefix}_saledetail` GROUP BY id_sale) s1 SET s.vat=s1.xa WHERE s.id_sale=s1.id_sale";
            $this->m->query($sql);
        }
        echo "Sale Detail converted Successfully<br>";
    }
    function make_batch($prefix) {
        $sql = "SHOW TABLES LIKE '{$prefix}_batch'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            $sql = "CREATE TABLE `{$prefix}_batch` (
            `id_batch` int(11) NOT NULL AUTO_INCREMENT,
            `id_product` int(11) NOT NULL,
            `batch_no` varchar(15) NOT NULL,
            `mfg_date` varchar(15) NOT NULL,
            `expiry_date` varchar(15) NOT NULL,
            `expiry_date_show` varchar(10) NOT NULL,
            `distributor_price` decimal(16,2) NOT NULL,
            `seller_price` decimal(16,2) NOT NULL,
            `purchase_price` decimal(16,2) NOT NULL,
            `mrp` decimal(16,2) NOT NULL,
            `mrp_without_tax` decimal(16,2) NOT NULL,
            `opening_stock` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            `opening_stock_free` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
          PRIMARY KEY (`id_batch`)) ENGINE=MyISAM";
            $this->m->query($sql);
        } else {
            $sql = "ALTER TABLE `{$prefix}_batch`
            ADD `id_product` INT(11) NOT NULL,
            CHANGE `batch` `batch_no` varchar(15) NOT NULL,
            ADD `mfg_date` varchar(15) NOT NULL,
            CHANGE `exp_date` `expiry_date` varchar(15),
            ADD `expiry_date_show` varchar(10) NOT NULL,
            CHANGE `rrate` `distributor_price` decimal(16,2) NOT NULL,
            CHANGE `rate` `seller_price` decimal(16,2) NOT NULL,
            CHANGE `srate` `purchase_price` decimal(16,2) NOT NULL,
            CHANGE `mrp` `mrp` decimal(16,2) NOT NULL,
            CHANGE `mrp1` `mrp_without_tax` decimal(16,2) NOT NULL,
            CHANGE `obal` `opening_stock` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            CHANGE `obalfree` `opening_stock_free` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
            $this->m->query($sql);

            $sql = "ALTER TABLE `{$prefix}_batch` ADD INDEX ( `code` );";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_batch` b, `{$prefix}_product` p SET b.id_product=p.id_product WHERE b.code=p.code";
            $this->m->query($sql);
        }
        echo "Batch file Created Successfully<br>";
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
    function make_adjust($prefix) {

        $sql = "CREATE TABLE `{$prefix}_adjust` AS SELECT * FROM `{$prefix}_purchase` WHERE `type`!='P';";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_adjust` ENGINE = MyISAM";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_adjust` ADD PRIMARY KEY ( `id_purchase` ) ;";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_adjust` CHANGE `id_purchase` `id_adjust` INT( 11 ) NOT NULL AUTO_INCREMENT; ";
        $this->m->query($sql);
        $sql = "DELETE  FROM `{$prefix}_purchase` WHERE `type`!='P';";
        $this->m->query($sql);
        echo "Adjust converted Successfully<br>";
    }
    function make_adjustdet($prefix) {
        $sql = "CREATE TABLE `{$prefix}_adjustdetail` AS SELECT * FROM `{$prefix}_purchasedetail` WHERE `type`!='P';";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_adjustdetail` ENGINE = MyISAM";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_adjustdetail` ADD PRIMARY KEY ( `id_purchasedetail` ) ;";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_adjustdetail` CHANGE `id_purchasedetail` `id_adjustdetail` INT( 11 ) NOT NULL AUTO_INCREMENT,
        CHANGE `id_purchase` `id_adjust` INT( 11 ) NOT NULL;";
        $this->m->query($sql);
        $sql = "DELETE FROM `{$prefix}_purchasedetail` WHERE `type`!='P';";
        $this->m->query($sql);
        echo "Adjust Detail converted Successfully<br>";
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
            `is_transport` tinyint(1) NOT NULL,
            `id_transport` int(11) NOT NULL,
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
    function make_purcdet($prefix) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}_purchasedetail_temp` (
            `id_purchasedetail` int(11) NOT NULL AUTO_INCREMENT,
            `no` varchar(20) NOT NULL,
            `type` varchar(1) NOT NULL,
            `date` date NOT NULL,
            `id_product` int(11) NOT NULL,
            `rate` decimal(15,4) NOT NULL,
            `case` decimal(12,4) NOT NULL,
            `qty` decimal(12,4) NOT NULL,
            `free` decimal(12,4) NOT NULL,
            `discount_type1` tinyint(2) NOT NULL,
            `discount1` decimal(10,4) NOT NULL,
            `discount_amount1` decimal(10,2) NOT NULL,
            `discount_type2` tinyint(2) NOT NULL,
            `discount2` decimal(10,4) NOT NULL,
            `discount_amount2` decimal(10,2) NOT NULL,
            `discount_type3` tinyint(2) NOT NULL,
            `discount3` decimal(10,4) NOT NULL,
            `discount_amount3` decimal(10,2) NOT NULL,
            `discount_type4` tinyint(2) NOT NULL,
            `discount4` decimal(10,4) NOT NULL,
            `discount_amount4` decimal(10,2) NOT NULL,
            `amount` decimal(15,2) NOT NULL,
            `goods_amount` decimal(15,2) NOT NULL,
            `cessamt` decimal(15,2) NOT NULL,            
            `id_taxmaster` int(11) NOT NULL,
            `tax_per` decimal(8,2) NOT NULL,
            `tax_amount` decimal(15,2) NOT NULL,
            `net_amount` decimal(15,2) NOT NULL,
            `id_purchase` int(11) NOT NULL,
            `id_company` int(11) NOT NULL,
            `id_head` int(11) NOT NULL,
            `id_represent` int(11) NOT NULL,
            `id_area` int(11) NOT NULL,
            `id_batch` int(11) NOT NULL,
            `batch_no` varchar(15) NOT NULL,
            `code` varchar (5) NOT NULL,
            `exp_date` varchar(15) NOT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_purchasedetail`) ) ENGINE=MyISAM";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` SET scheme='0' WHERE scheme='P'";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` SET scheme='1' WHERE scheme='Q'";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` SET scheme='2' WHERE scheme='A'";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` SET scheme='3' WHERE scheme='C'";
        $this->m->query($sql);
        $sql = "SHOW COLUMNS FROM `{$prefix}_purchasedetail` LIKE 'batch'";
        $batch = $this->m->num_rows($this->m->query($sql)) == 0 ? "''" : "`batch`";
        $sql = "INSERT INTO `{$prefix}_purchasedetail_temp` (`type`, `no`, `date`, `code`, `id_taxmaster`, `rate`, `discount_type1`, `discount1`, `discount2`, `qty`, `free`, `batch_no`, `amount`) 
                SELECT `type`, `no`, `rdate`, `code`, `tax`, `rate`, `scheme`, `disc`, `spl`, `qty`, `free`, {$batch}, `amt` FROM `{$prefix}_purchasedetail`;";
        $this->m->query($sql);
        $sql = "DROP TABLE `{$prefix}_purchasedetail`";
        $this->m->query($sql);
        $sql = "RENAME TABLE `{$prefix}_purchasedetail_temp` TO `{$prefix}_purchasedetail`";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d, `{$prefix}_product` p SET d.case=p.case, d.id_product=p.id_product WHERE d.code=p.code";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_purchasedetail` d, `{$prefix}_purchase` s SET d.id_area=s.id_area, d.id_represent=s.id_represent, d.discount3=s.cd,
                d.id_head=s.id_head, d.id_company=s.id_company, d.id_purchase=s.id_purchase WHERE d.no=s.no AND d.type=s.type";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_purchasedetail` ADD INDEX ( `code` );";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d, `{$prefix}_product` p SET d.id_product=p.id_product WHERE d.code=p.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d, `{$prefix}_taxmaster` t SET d.tax_per=t.tax_per WHERE d.id_taxmaster=t.id_taxmaster";
        $this->m->query($sql);
        if (isset($_REQUEST['type']) && $_REQUEST['type'] == "srsk") {
            $sql = "UPDATE `{$prefix}_purchasedetail` d SET discount_amount1=IF(discount_type1=0, ROUND(amount*discount1/100,2), IF(discount_type1=1, qty*discount1/`case`, discount1)) WHERE discount1!=0 AND discount_amount1=0";
        } else {
            $sql = "UPDATE `{$prefix}_purchasedetail` d SET discount_amount1=IF(discount_type1=0, ROUND(amount*discount1/100,2), IF(discount_type1=1, qty*discount1, discount1)) WHERE discount1!=0 AND discount_amount1=0";
        }
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d SET discount_amount2=IF(discount_type2=0, ROUND((amount-discount_amount1)*discount2/100,2), IF(discount_type2=1, qty*discount2, discount2)) WHERE discount2!=0 AND discount_amount2=0";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d SET discount_amount3=ROUND((amount-discount_amount1-discount_amount2)*discount3/100,2) WHERE discount3!=0 AND discount_amount3=0";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_purchasedetail` d SET goods_amount=amount-discount_amount1-discount_amount2-discount_amount3";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d SET tax_amount=ROUND(goods_amount*tax_per/100,2)";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchasedetail` d SET net_amount=goods_amount+tax_amount";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_purchasedetail` ADD INDEX ( `id_purchase` );";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_purchase` s,  (SELECT id_purchase, SUM(goods_amount) AS ga, SUM(discount_amount1+discount_amount2+discount_amount3) AS da 
            FROM `{$prefix}_purchasedetail` GROUP BY id_purchase) s1 SET s.totalamt=s1.ga, s.discount=s1.da WHERE s.id_purchase=s1.id_purchase";
        $this->m->query($sql);
        echo "Purchase Detail converted Successfully<br>";
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
    function make_discount($prefix) {
        $sql = "DROP TABLE IF exists `{$prefix}_discount`";
        $this->m->query($sql);
        $sql = "CREATE TABLE `{$prefix}_discount` (
            `id_discount` int(11) NOT NULL AUTO_INCREMENT,
            `id_head` int(11) NOT NULL,
            `id_company` int(11) NOT NULL,
            `discount` DECIMAL( 16, 2 ),
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_discount`)) ENGINE=MyISAM";
        $this->m->query($sql);
        echo "Discount created Successfully<br>";
    }
    function make_zone($prefix) {
        $sql = "SHOW TABLES LIKE '{$prefix}_zone'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            $sql = "CREATE TABLE `{$prefix}_zone` (
                `id_zone` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(60) NOT NULL, `code` varchar(6), PRIMARY KEY (`id_zone`)) ENGINE=MyISAM";
            $this->m->query($sql);
        } else {
            $sql = "ALTER TABLE `{$prefix}_zone` DROP `consider`";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_zone` ADD `description` TEXT NOT NULL,
                ADD `status` TINYINT( 1 ) NOT NULL,
                ADD `ip` VARCHAR( 30 ) NOT NULL,
                ADD `id_create` INT( 11 ) NOT NULL,
                ADD `create_date` TIMESTAMP NOT NULL,
                ADD `id_modify` INT( 11 ) NOT NULL,
                ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_zone` ADD INDEX ( `code` );";
        $this->m->query($sql);
        echo "Zone created Successfully<br>";
    }
    function make_bonus($prefix) {
        $sql = "SHOW TABLES LIKE '{$prefix}_bonus'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            $sql = "CREATE TABLE `{$prefix}_bonus` (`id_zone` int(11) NOT NULL, `code` varchar(6),
				`startdate` date NOT NULL, `enddate` date NOT NULL, qty DECIMAL(12,4), free DECIMAL(12,4)				
				) ENGINE=MyISAM";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_bonus` ADD `description` TEXT NOT NULL,
                ADD `status` TINYINT( 1 ) NOT NULL,
                ADD `ip` VARCHAR( 30 ) NOT NULL,
				ADD `id_product` INT( 11 ) NOT NULL,
                ADD `id_create` INT( 11 ) NOT NULL,
                ADD `create_date` TIMESTAMP NOT NULL,
                ADD `id_modify` INT( 11 ) NOT NULL,
                ADD `modify_date` TIMESTAMP NOT NULL,
				ADD INDEX ( `code` )";
        $this->m->query($sql);
		$sql = "UPDATE `{$prefix}_bonus` b, `{$prefix}_product` p SET b.id_product=p.id_product WHERE b.code=p.code;";
		$this->m->query($sql);
        echo "Bonus created Successfully<br>";
    }
    function make_form($prefix) {
        $sql = "CREATE TABLE `{$prefix}_form` (
            `id_form` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(30) NOT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_form`)) ENGINE=MyISAM";
        $this->m->query($sql);
        $sql = "INSERT INTO `{$prefix}_form` (`name`, `status`) VALUES ('Form C', 0), ('d form', 0);";
        $this->m->query($sql);
        echo "Form created Successfully<br>";
    }
    function make_transport($prefix) {

        $sql = "CREATE TABLE `{$prefix}_transport` (
            `id_transport` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(60) NOT NULL,
            `address` text NOT NULL,
            `panno` varchar (50) NOT NULL,
            `contact_person` text NOT NULL,
            `contact_no` text NOT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_transport`))";
        $this->m->query($sql);
        echo "Transport created Successfully<br>";
    }
    function make_series($prefix) {
        $sql = "SHOW TABLES LIKE '{$this->prefix}series'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            $sql = "CREATE TABLE `{$prefix}_series` (
                `id_series` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(35) NOT NULL,
                `status` TINYINT( 1 ) NOT NULL,
                `ip` VARCHAR( 30 ) NOT NULL,
                `id_create` INT( 11 ) NOT NULL,
                `create_date` TIMESTAMP NOT NULL,
                `id_modify` INT( 11 ) NOT NULL,
                `modify_date` TIMESTAMP NOT NULL,
                PRIMARY KEY (`id_series`))";
            $this->m->query($sql);
        }
        echo "Series created Successfully<br>";
    }
    function make_item($prefix) {
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'type'";
        $type = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `type`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'taxpoint'";
        $taxpoint = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `taxpoint`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'drug'";
        $drug = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `drug`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'ic'";
        $ic = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `ic`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'freight'";
        $freight = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `freight`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'ptaxpoint'";
        $ptaxpoint = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `ptaxpoint`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'obal2'";
        $obal2 = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `obal2`,";
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'rbal2'";
        $rbal2 = $this->m->num_rows($this->m->query($sql)) == 0 ? "" : "DROP `rbal2`,";
        $sql = "ALTER TABLE `{$prefix}_product` {$type} {$taxpoint} {$drug} {$ic} {$freight} {$ptaxpoint}
            {$obal2} {$rbal2}
            DROP `consider`";
        $this->m->query($sql);
        if (strpos($prefix, 'BE') !== false) {
            $sql = "ALTER TABLE `{$prefix}_product` DROP `lock`, DROP `rbaln`;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'minstock'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `minstock` DECIMAL( 16, 4 ) NULL DEFAULT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'obalrate'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `obalrate` DECIMAL( 16, 4 ) NULL DEFAULT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'batch'";
        if ($this->m->num_rows($this->m->query($sql)) == 1) {
            $sql = "ALTER TABLE `{$prefix}_product` CHANGE `batch` `batch_no` VARCHAR (20) NULL DEFAULT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'pscheme'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `pscheme` VARCHAR (1) NULL DEFAULT NULL, ADD `psrate` DECIMAL (8,2) NULL DEFAULT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'scheme'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `scheme` VARCHAR( 1 ) NOT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'weight'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `weight` DECIMAL( 10, 3 ) NOT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'case'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `case` DECIMAL( 10, 3 ) NOT NULL;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_product` LIKE 'METER'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_product` ADD `meter` DECIMAL( 10, 3 ) NOT NULL";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_product` CHANGE `sp1` `seller_price` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            CHANGE `taxtype` `id_taxmaster_sale` INT( 11 ) NULL DEFAULT NULL,
            CHANGE `pp1` `purchase_price` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            CHANGE `purctax` `id_taxmaster_purchase` INT( 11 ) NULL DEFAULT NULL,
            CHANGE `obal1` `opening_stock` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            CHANGE `rbal1` `closing_stock` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            CHANGE `minstock` `minimum_stock` DECIMAL( 16, 4 ) NULL DEFAULT NULL, 
            CHANGE `obalrate` `opening_price` DECIMAL( 16, 4 ) NULL DEFAULT NULL,
            ADD `id_company` INT( 11 ) NOT NULL AFTER `id_product`,
            ADD `distributor_price` DECIMAL( 16, 4 ) NOT NULL AFTER `unit`,
            ADD `case_unit` VARCHAR( 10 ) NOT NULL AFTER `case`,
            CHANGE `mrp` `mrp` DECIMAL( 16, 2 ) NULL DEFAULT NULL,
            CHANGE `scheme` `sale_scheme` VARCHAR( 1 ) NULL DEFAULT NULL,
            CHANGE `srate` `sale_scheme_rate` DOUBLE( 5, 2 ) NULL DEFAULT NULL,
            CHANGE `pscheme` `purchase_scheme` VARCHAR( 1 ) NULL DEFAULT NULL,
            CHANGE `psrate` `purchase_scheme_srate` DECIMAL( 5, 2 ) NULL DEFAULT NULL,
            ADD `batch` TINYINT( 1 ) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_product` p, `{$prefix}_company` c SET p.id_company = c.id_company WHERE LEFT(p.code, length(c.code)) = c.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_product` SET `meter` = 1, `weight` = 1";
        $this->m->query($sql);
           // CHANGE `pack` `pack` DECIMAL(16,4) NULL DEFAULT NULL COMMENT 'Pack', 
        $sql = "ALTER TABLE `{$prefix}_product` CHANGE `code` `code` VARCHAR(5)  NULL DEFAULT NULL COMMENT 'Product Code', 
            CHANGE `name` `name` VARCHAR(30)  NULL DEFAULT NULL COMMENT 'Product Name', 
            CHANGE `short` `short` VARCHAR(10)  NULL DEFAULT NULL COMMENT 'Short Name', 
            CHANGE `unit` `unit` VARCHAR(3)  NULL DEFAULT NULL COMMENT 'Unit', 
            CHANGE `distributor_price` `distributor_price` DECIMAL(16,4) NOT NULL COMMENT 'Distributor Price', 
            CHANGE `seller_price` `seller_price` DECIMAL(16,4) NULL DEFAULT NULL COMMENT 'Selling Price', 
            CHANGE `mrp` `mrp` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'MRP', 
            CHANGE `purchase_price` `purchase_price` DECIMAL(16,4) NULL DEFAULT NULL COMMENT 'Purchse Price', 
            CHANGE `opening_stock` `opening_stock` DECIMAL(16,4) NULL DEFAULT NULL COMMENT 'Opending Stock', 
            CHANGE `case` `case` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Case', 
            CHANGE `case_unit` `case_unit` VARCHAR(10)  NOT NULL COMMENT 'Unit for Case',
            CHANGE `opening_price` `opening_price` DECIMAL(16,4) NULL DEFAULT NULL COMMENT 'Rate of Opening Stock',
            CHANGE `id_company` `id_company` INT(11) NULL DEFAULT NULL COMMENT 'Company ID',
            CHANGE `id_taxmaster_sale` `id_taxmaster_sale` INT(11) NULL DEFAULT NULL COMMENT 'Sales VAT',
            CHANGE `id_taxmaster_purchase` `id_taxmaster_purchase` INT(11) NULL DEFAULT NULL COMMENT 'Purchase VAT',
            CHANGE `minimum_stock` `minimum_stock` DECIMAL(16,4) NULL DEFAULT NULL COMMENT 'Minimum Stock'";
        $this->m->query($sql);

        if (isset($_REQUEST['type']) && $_REQUEST['type'] == "srsk") {
            $sql = "UPDATE `{$prefix}_product` SET closing_stock = floor(floor(`opening_stock`)*`case` + (`opening_stock`-floor(`opening_stock`))*1000)";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_product` SET opening_stock=closing_stock";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_product` ADD INDEX ( `code` );";
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
    function make_vat317($prefix) {
        $sql = "DROP TABLE `{$prefix}_vat317` IF EXISTS";
        $this->m->query($sql);
        echo "VAT317 removed Successfully<br>";
    }
    function make_company($prefix) {
        $sql = "SHOW COLUMNS FROM `{$prefix}_company` LIKE 'stockist'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_company` DROP `consider`";
        } else {
            $sql = "ALTER TABLE `{$prefix}_company` DROP `stockist`, DROP `consider`";
        }
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_company` ADD `description` TEXT NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_company` ADD INDEX ( `code` );";
        $this->m->query($sql);
        echo "Company converted Successfully<br>";
    }
    function make_category($prefix) {
        $sql = "SHOW TABLES LIKE '{$prefix}_category'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            $sql = "CREATE TABLE `{$prefix}_category` (
                `id_category` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(30) DEFAULT NULL,
                PRIMARY KEY (`id_category`)) ENGINE=MyISAM";
            $this->m->query($sql);
            echo "Category Created Successfully<br>";
        } else {
            $sql = "ALTER TABLE `{$prefix}_category` DROP `consider`";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_category` ADD `description` TEXT NOT NULL,
                ADD `status` TINYINT( 1 ) NOT NULL,
                ADD `ip` VARCHAR( 30 ) NOT NULL,
                ADD `id_create` INT( 11 ) NOT NULL,
                ADD `create_date` TIMESTAMP NOT NULL,
                ADD `id_modify` INT( 11 ) NOT NULL,
                ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        echo "Category converted Successfully<br>";
    }
    function make_billdetio($prefix) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}_creditnotedetail` (
            `id_creditnotedetail` int(11) NOT NULL AUTO_INCREMENT,
            `no` varchar(20) NOT NULL,
            `date` date NOT NULL,
            `id_product` int(11) NOT NULL,
            `rate` decimal(15,4) NOT NULL,
            `qty` decimal(12,4) NOT NULL,
            `free` decimal(12,4) NOT NULL,
            `amount` decimal(15,2) NOT NULL,
            `goods_amount` decimal(15,2) NOT NULL,
            `id_taxmaster` int(11) NOT NULL,
            `tax_per` decimal(8,2) NOT NULL,
            `tax_amount` decimal(15,2) NOT NULL,
            `net_amount` decimal(15,2) NOT NULL,
            `id_creditnote` int(11) NOT NULL,
            `id_company` int(11) NOT NULL,
            `id_head` int(11) NOT NULL,
            `id_represent` int(11) NOT NULL,
            `id_area` int(11) NOT NULL,
            `id_batch` int(11) NOT NULL,
            `batch_no` varchar(15) NOT NULL,
            `code` varchar (5) NOT NULL,
            `exp_date` varchar(15) NOT NULL,
            `status` TINYINT( 1 ) NOT NULL,
            `ip` VARCHAR( 30 ) NOT NULL,
            `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL,
            `id_modify` INT( 11 ) NOT NULL,
            `modify_date` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_creditnotedetail`) ) ENGINE=MyISAM";
        $sql = "SHOW TABLES LIKE '{$this->$prefix}_billdet'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table == 0) {
            return 0;
        }

        $this->m->query($sql);
        $sql = "INSERT INTO `{$prefix}_creditnotedetail` (`no`, `date`, `code`, `id_taxmaster`, `rate`, `qty`, `free`, `amount`) 
                SELECT `no`, `date`, `code`, `tax`, `rate`, `qty`,  `free`, `amt` FROM `{$prefix}_billdet`;";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_creditnotedetail` d, `{$prefix}_product` p SET d.id_product=p.id_product WHERE d.code=p.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_creditnotedetail` d, `{$prefix}_taxmaster` t SET d.tax_per=t.tax_per WHERE d.id_taxmaster=t.id_taxmaster";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_creditnotedetail` ADD INDEX ( `no` );";
        $this->m->query($sql);

        $sql = "UPDATE `{$prefix}_creditnotedetail` d, `{$prefix}_creditnote` s SET d.id_area=s.id_area, d.id_represent=s.id_represent, 
                d.id_head=s.id_head, d.id_company=s.id_company, d.id_creditnote=s.id_creditnote WHERE d.no=s.no AND d.date=s.date";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_creditnotedetail` d SET tax_amount=ROUND(goods_amount*tax_per/100,2)";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_creditnotedetail` d SET net_amount=goods_amount+tax_amount";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_creditnotedetail` ADD INDEX ( `id_creditnote` );";
        $this->m->query($sql);
        echo "Credit note Detail converted Successfully<br>";
    }
    function make_bill($prefix) {
        $fields = " int(11) NOT NULL AUTO_INCREMENT,  `no` varchar(20) NOT NULL, `date` date NOT NULL,
            `caltype` varchar(10), `mode` varchar(1), `reference` varchar(20) NOT NULL, `refdate` date NOT NULL,
            `billno` varchar(20) NOT NULL, `billdate` date NOT NULL, `description` varchar(30) NOT NULL, `id_head` int(11) NOT NULL,
            `id_company` int(11) NOT NULL, `id_area` int(11) NOT NULL, `id_represent` int(11) NOT NULL, `totalamt` decimal(16,2) NOT NULL,
            `discount` decimal(16,2) NOT NULL, `cd` decimal(8,2) NOT NULL, `vat` decimal(16,2) NOT NULL, `packing` decimal(16,2) NOT NULL,
            `add` decimal(16,2) NOT NULL, `less` decimal(16,2) NOT NULL, `round` decimal(16,2) NOT NULL, `totalcess` decimal(16,2) NOT NULL,
            `total` decimal(16,2) NOT NULL, `memo` text NOT NULL, `ip` VARCHAR( 30 ) NOT NULL, `id_create` INT( 11 ) NOT NULL,
            `create_date` TIMESTAMP NOT NULL, `id_modify` INT( 11 ) NOT NULL, `modify_date` TIMESTAMP NOT NULL, `company` varchar (2) NOT NULL,
            `cust` varchar (4) NOT NULL, `salesman` varchar (2) NOT NULL, `area` varchar (2) NOT NULL, `type` varchar (1) NOT NULL,
            `acno` varchar (4) NOT NULL, id_account int(11) NOT NULL, PRIMARY KEY";
        $sql = "CREATE TABLE `{$prefix}_creditnote` (`id_creditnote` $fields  (`id_creditnote`) ) ENGINE=MyISAM";
        $this->m->query($sql);
        $sql = "CREATE TABLE `{$prefix}_debitnote` (`id_debitnote` $fields (`id_debitnote`) ) ENGINE=MyISAM";
        $this->m->query($sql);
        $table = $this->m->num_rows($this->m->query("SHOW TABLES LIKE '{$prefix}_bill'")) == 0 ? 0 : 1;
        if ($table != 0 ) {
            $fld = $this->m->sql_getall("SHOW COLUMNS FROM `{$prefix}_bill`", 2, "Type", "Field");
            $caltype = isset($fld['caltype']) ? '`caltype`' : "'SALES'";
            $mode = isset($fld['mode']) ? '`mode`' : "'S'";
            $refno = isset($fld['refno']) ? '`refno`' : "''";
            $refdate = isset($fld['refdate']) ? '`refdate`' : "''";
            $acno = isset($fld['acno']) ? '`acno`' : "''";
            $add = isset($fld['extra1']) ? '`extra1`' : '0.00';
            $less = isset($fld['extra2']) ? '`extra2`' : '0.00';
            $round = isset($fld['extra3']) ? '`extra3`' : '0.00';
            $date = isset($fld['date']) ? '`date`' : '`rdate`';
            $bdate = isset($fld['bdate']) ? '`bdate`' : '`idate`';
            $salestax = isset($fld['salestax']) ? '`salestax`' : '`purctax`';
            $sql = "INSERT INTO `{$prefix}_creditnote` (`no`, `date`, `type`, `caltype`, `mode`, `cust`, `company`, `area`, `salesman`, `reference`, `refdate`, `billno`, `billdate`, 
                `cd`, `vat`, `total`, `memo`, `acno`, `add`, `less`, `round`) 
                SELECT `no`, $date, $caltype, `type`, $mode, cust, company, area, salesman, 
                $refno, $refdate, bno, $bdate, cd, $salestax, total, narration, $acno, $add, $less, $round FROM `{$prefix}_bill`;";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnote` s, `{$prefix}_area` a SET s.id_area=a.id_area WHERE s.area=a.code";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnote` s, `{$prefix}_represent` r SET s.id_represent=r.id_represent WHERE s.salesman=r.code";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnote` s, `{$prefix}_head` h SET s.id_head=h.id_head WHERE s.cust=h.code";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnote` s, `{$prefix}_head` h SET s.id_account=h.id_head WHERE s.acno=h.code";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnote` s, `{$prefix}_company` c SET s.id_company=c.id_company WHERE s.company=c.code";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_creditnote` ADD INDEX ( `no` );";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_debitnote` ADD INDEX ( `no` );";
        $this->m->query($sql);
        echo "Credit note converted Successfully<br>";
    }
    function make_billdet($prefix) {
        $fields = " int(11) NOT NULL AUTO_INCREMENT, `no` varchar(20) NOT NULL, `date` date NOT NULL, `mode` varchar(1), `id_product` int(11) NOT NULL,
            `rate` decimal(15,4) NOT NULL, `qty` decimal(12,4) NOT NULL, `free` decimal(12,4) NOT NULL, `amount` decimal(15,2) NOT NULL,
            `goods_amount` decimal(15,2) NOT NULL, `id_taxmaster` int(11) NOT NULL, `discount_type1` tinyint(2) NOT NULL,
            `discount1` decimal(10,4) NOT NULL, `discount_amount1` decimal(10,2) NOT NULL, `tax_per` decimal(8,2) NOT NULL,
            `tax_amount` decimal(15,2) NOT NULL, `net_amount` decimal(15,2) NOT NULL, `cessamt` decimal(15,2) NOT NULL,
            `type` varchar (1) NOT NULL, `id_company` int(11) NOT NULL, `id_head` int(11) NOT NULL, `id_represent` int(11) NOT NULL,
            `id_area` int(11) NOT NULL, `id_batch` int(11) NOT NULL, `batch_no` varchar(15) NOT NULL, `code` varchar (5) NOT NULL,
            `exp_date` varchar(15) NOT NULL, `status` TINYINT( 1 ) NOT NULL, `ip` VARCHAR( 30 ) NOT NULL, `id_create` INT( 11 ) NOT NULL, 
            `create_date` TIMESTAMP NOT NULL, `id_modify` INT( 11 ) NOT NULL, `modify_date` TIMESTAMP NOT NULL, ";

        $sql = "CREATE TABLE `{$prefix}_debitnotedetail` (`id_debitnotedetail` $fields `id_debitnote` int(11) NOT NULL, PRIMARY KEY (`id_debitnotedetail`) ) ENGINE=MyISAM";
        $this->m->query($sql);
        $sql = "CREATE TABLE `{$prefix}_creditnotedetail` (`id_creditnotedetail` $fields `id_creditnote` int(11) NOT NULL, PRIMARY KEY (`id_creditnotedetail`) ) ENGINE=MyISAM";
        $this->m->query($sql);

        $sql = "SHOW TABLES LIKE '{$prefix}_billdet'";
        $table = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($table != 0) {
            $fld = $this->m->sql_getall("SHOW COLUMNS FROM `{$prefix}_billdet`", 2, "Type", "Field");
            $mode = isset($fld['mode']) ? '`mode`' : "'S'";
            $batch = isset($fld['batch']) ? '`batch`' : "''";
            $disc = isset($fld['disc']) ? '`disc`' : "0.00";
	        $sql = "INSERT INTO `{$prefix}_creditnotedetail` (`no`, `date`, `code`, `type`, `id_taxmaster`, `rate`, `qty`, `free`, `amount`, `mode`, batch_no, discount1, discount_type1) 
                SELECT `no`, `date`, `code`, `type`, `tax`, `rate`, `qty`,  `free`, `amt`, $mode, $batch, $disc, 'P' FROM `{$prefix}_billdet`;";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnotedetail` d, `{$prefix}_product` p SET d.id_product=p.id_product WHERE d.code=p.code";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnotedetail` d, `{$prefix}_taxmaster` t SET d.tax_per=t.tax_per WHERE d.id_taxmaster=t.id_taxmaster";
            $this->m->query($sql);
            $sql = "ALTER TABLE `{$prefix}_creditnotedetail` ADD INDEX ( `no` );";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnotedetail` d, `{$prefix}_creditnote` s SET d.id_area=s.id_area, d.id_represent=s.id_represent, 
                    d.id_head=s.id_head, d.id_company=s.id_company, d.id_creditnote=s.id_creditnote WHERE d.no=s.no AND d.date=s.date";
            $this->m->query($sql);
    
            $sql = "UPDATE `{$prefix}_creditnotedetail` d SET discount_amount1=ROUND(amount*discount1/100,2) WHERE discount1!=0";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnotedetail` d SET goods_amount=amount-discount_amount1";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnotedetail` d SET tax_amount=ROUND(goods_amount*tax_per/100,2)";
            $this->m->query($sql);
            $sql = "UPDATE `{$prefix}_creditnotedetail` d SET net_amount=goods_amount+tax_amount";
            $this->m->query($sql);
        }
        $sql = "ALTER TABLE `{$prefix}_creditnotedetail` ADD INDEX ( `id_creditnote` );";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_debitnotedetail` ADD INDEX ( `id_debitnote` );";
        $this->m->query($sql);
        $sql = "INSERT INTO `{$prefix}_debitnotedetail` SELECT * FROM `{$prefix}_creditnotedetail` WHERE type='D'";
        $this->m->query($sql);
        $sql = "INSERT INTO `{$prefix}_debitnote` SELECT * FROM `{$prefix}_creditnote` WHERE type='D'";
        $this->m->query($sql);
        $sql = "DELETE FROM `{$prefix}_creditnote` WHERE type='D'";
        $this->m->query($sql);
        $sql = "DELETE FROM `{$prefix}_creditnotedetail` WHERE type='D'";
        $this->m->query($sql);
        echo "Credit and Debit note Detail converted Successfully<br>";
    }


    function make_salesman($prefix) {
        $sql = "ALTER TABLE `{$prefix}_represent` CHANGE `address` `address` TEXT  NULL DEFAULT NULL";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_represent` DROP `consider`";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_represent` ADD `phone` TEXT NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_represent` ADD INDEX ( `code` );";
        $this->m->query($sql);
        echo "Salesman converted to Represent Table Successfully<br>";
    }
    function make_area($prefix) {
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
        $sql = "SHOW COLUMNS FROM `{$prefix}_head` LIKE 'mobile'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_head` ADD `mobile` TEXT NOT NULL AFTER `address`;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_head` LIKE 'climit'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_head` ADD `climit` DECIMAL ( 12,2 ) ;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_head` LIKE 'cday'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_head` ADD cday DECIMAL( 16, 2 ) ;";
            $this->m->query($sql);
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_head` LIKE 'pincode'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_head` ADD `pincode` VARCHAR ( 20 ) NOT NULL AFTER `address`;";
        }
        $sql = "SHOW COLUMNS FROM `{$prefix}_head` LIKE 'address2'";
        if ($this->m->num_rows($this->m->query($sql)) == 0) {
            $sql = "ALTER TABLE `{$prefix}_head` ADD `address3` VARCHAR( 60 );";
        } else {
            $sql = "ALTER TABLE `{$prefix}_head` CHANGE `address2` `address3` VARCHAR( 60 );";
        }
        $this->m->query($sql);
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
            ADD `id_transport` INT( 11 ) NOT NULL AFTER `transport`,
            ADD `email` TEXT NOT NULL AFTER `address`,
            ADD `contact_person` VARCHAR ( 50 ) NOT NULL AFTER `address`,
            CHANGE `climit` `credit_limit` DECIMAL( 16, 2 ),
            CHANGE `cday` `credit_days` DECIMAL( 16, 2 ),
            CHANGE `notype` `vattype` VARCHAR ( 4 ),
            CHANGE `no` `vatno` VARCHAR ( 11 ),
            ADD `dealer` INT(2) COMMENT '0-Distributor/Retailer, 1-Super-Distributor',
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_head` CHANGE `address` `address1` VARCHAR( 60 )";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` h, `{$prefix}_group` g SET h.id_group = g.id_group WHERE h.gcode = g.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` h, `{$prefix}_area` a SET h.id_area = a.id_area WHERE h.area = a.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_head` h, `{$prefix}_transport` t SET h.id_transport = t.id_transport WHERE h.transport = t.name";
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
        $sql = "ALTER TABLE `{$prefix}_advance` ADD `id_advance` INT (11) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_book` DROP `head`";
        $this->m->query($sql);
        echo "Advance converted Successfully<br>";
    }
    function make_mode($prefix) {
        $sql = "ALTER TABLE `{$prefix}_mode` DROP `consider`";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_mode` ADD `id_mode` INT (11) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        
        echo "Mood converted Successfully<br>";
    }
    function make_rates($prefix) {
        $sql = "ALTER TABLE `{$prefix}_rates` DROP `consider`";
        $this->m->query($sql);
        $sql = "ALTER TABLE `{$prefix}_rates` ADD `id_rates` INT (11) NOT NULL,
            ADD `from_area` INT( 11 ) NOT NULL,
            ADD `to_area` INT( 11 ) NOT NULL,
            ADD `id_mode` INT( 11 ) NOT NULL,
            ADD `status` TINYINT( 1 ) NOT NULL,
            ADD `ip` VARCHAR( 30 ) NOT NULL,
            ADD `id_create` INT( 11 ) NOT NULL,
            ADD `create_date` TIMESTAMP NOT NULL,
            ADD `id_modify` INT( 11 ) NOT NULL,
            ADD `modify_date` TIMESTAMP NOT NULL ";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` h, `{$prefix}_mood` a SET h.id_mood = a.id_mood WHERE h.mood = a.mood";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` h, `{$prefix}_area` a SET h.from_area = a.id_area WHERE h.area = a.code";
        $this->m->query($sql);
        $sql = "UPDATE `{$prefix}_rates` h, `{$prefix}_area` a SET h.to_area = a.id_area WHERE h.area = a.code";
        $this->m->query($sql);
        echo "Mood converted Successfully<br>";
    }
    function make_vdetail($prefix) {
        $sql = "ALTER TABLE `{$prefix}_vdetail` ADD `id_vdetail` INT (11) NOT NULL,
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