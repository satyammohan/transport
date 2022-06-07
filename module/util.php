<?php

class util extends common {

    function __construct() {
        $this->table_prefix();
        parent:: __construct();
    }

    function upgrade() {
        $sql = "SHOW COLUMNS FROM `user` LIKE 'uid'";
        $uid = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($uid == 1) {
            $sql = "ALTER TABLE `user` CHANGE uid id_user INT (11), ADD `random` VARCHAR (32),
                        ADD `is_admin` TINYINT (1), ADD `id_create` INT (11), ADD `create_date` TIMESTAMP, ADD `id_modify` INT (11),
                        ADD `modify_date` TIMESTAMP, ADD `ip` VARCHAR (30)";
            $this->m->query($sql);
        }
        $data = $this->m->fetch_assoc("SELECT COUNT(*) AS cnt FROM `configuration`");
        if (!$data['cnt']) {
            $sql = "DROP TABLE IF EXISTS `configuration`";
            $this->m->query($sql);
            $sql = "CREATE TABLE `configuration` (`id_config` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(30) NOT NULL,`description` varchar(100) NOT NULL,`value` text NOT NULL,PRIMARY KEY (`id_config`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
            $this->m->query($sql);
            $sql = "INSERT INTO `configuration` (`id_config`, `name`, `description`, `value`) VALUES (1, 'salestock', 'Show only Products having Stock in Sales Billing', '1'),(2, 'purchasereturnstock', 'Show only Products having Stock in Purchase Return', '1'),(3, 'showstockinsale', 'Show Stock available in Sales Billing', '1'), (4, 'salebillfooter', 'Show Footer content in Sales Billing Print', '<b>TERMS & CONDITIONS ::</b> 1. Goods once sold will not be taken back.<br>2. Subject to State Jurisdiction.<br>3. We hereby certify that the good/goods mentioned in this Invoice is/are to be of the nature & quality it purport/purports to be.<br>4. This registration certificate is valid on the date of issue of this Invoice.');";
            $this->m->query($sql);
            $fr = "E. & O. E.<br>For M/s. {$_SESSION['companyname']}<br><br>Authorisied Signatory";
            $sql = "INSERT INTO `configuration` (`id_config`, `name`, `description`, `value`) VALUES 
                        (5, 'salebillfooterright', 'Show Footer content in Rightside of Sales Billing Print',  '{$fr}');";
            $this->m->query($sql);
        }
        $data = $this->m->fetch_assoc("SELECT COUNT(*) AS cnt FROM `permission`");
        if (!$data['cnt']) {
            $sql = "DROP TABLE IF EXISTS `permission`";
            $this->m->query($sql);
            $sql = "CREATE TABLE IF NOT EXISTS `permission` (
                    `id_permission` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(50) NOT NULL,
                    `status` tinyint(1) NOT NULL, `ip` varchar(30) NOT NULL,
                    `id_create` int(11) NOT NULL,`create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `id_modify` int(11) NOT NULL,`modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY (`id_permission`)) ENGINE=InnoDB ;";
            $this->m->query($sql);
            $sql = " INSERT INTO `permission` (`id_permission`, `name`, `status`, `ip`, `id_create`, `create_date`, `id_modify`, `modify_date`) VALUES
                        (1, 'INSERT', 0, '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
                        (2, 'UPDATE', 0, '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
                        (3, 'DELETE', 0, '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
                        (4, 'REPORT', 0, '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');";
            $this->m->query($sql);
        }
        $data = $this->m->fetch_assoc("SELECT COUNT(*) AS cnt FROM `module_map`");
        if (!$data['cnt']) {
            $sql = "DROP TABLE IF EXISTS `module_map`";
            $this->m->query($sql);
            $sql = "CREATE TABLE IF NOT EXISTS `module_map` (
                        `id_module_map` int(11) NOT NULL AUTO_INCREMENT, `id_user` int(11) NOT NULL,
                        `id_module` int(11) NOT NULL, `id_permission` int(11) NOT NULL, `permission` tinyint(1) NOT NULL,
                        `status` tinyint(1) NOT NULL, `ip` varchar(30) NOT NULL,
                        `id_create` int(11) NOT NULL, `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        `id_modify` int(11) NOT NULL, `modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                        PRIMARY KEY (`id_module_map`)) ENGINE=MyISAM;";
            $this->m->query($sql);
        }
        $sql = "DROP TABLE IF EXISTS `module`";
        $this->m->query($sql);
        $sql = "CREATE TABLE IF NOT EXISTS `module` (
                        `id_module` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(50) NOT NULL,
                        `status` tinyint(1) NOT NULL, `ip` varchar(30) NOT NULL,
                        `id_create` int(11) NOT NULL, `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        `id_modify` int(11) NOT NULL, `modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                        PRIMARY KEY (`id_module`)) ENGINE=MyISAM;";
        $this->m->query($sql);
        $sql = "INSERT INTO `module` (`id_module`, `name`) VALUES
                        (1, 'accounts'), (2, 'adjust'), (3, 'area'), (4, 'batch'), (5, 'company'), (6, 'discount'), (7, 'form'),
                        (8, 'group'), (9, 'head'), (10, 'preturn'), (11, 'product'), (12, 'purchase'), (13, 'represent'), (14, 'sales'),
                        (15, 'series'), (16, 'sreturn'), (17, 'taxmaster'), (18, 'transport'), (19, 'voucher'), (20, 'zone'),
                        (21, 'debitnote'), (22, 'creditnote'), (23, 'production'), (24, 'salesorder'), (25, 'salary');";
        $this->m->query($sql);
        $_SESSION['msg'] = "Database Upgraded Successful.";
    }

    function changefield($fld, $tbl, $qstring) {
        $sql = "SHOW COLUMNS FROM {$tbl} LIKE '{$fld}'";
        $uid = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($uid == 1) {
            $sql = "ALTER TABLE `{$tbl}` {$qstring}";
            $this->m->query($sql);
        }
    }
    function check() {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->prefix}taxmaster` (
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
                PRIMARY KEY (`id_taxmaster`)
              ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        $this->m->query($sql);

        $this->addfield('pincode', $this->prefix . 'head', 'ADD COLUMN `pincode` VARCHAR(30)');
        $this->addfield('gstin', $this->prefix . 'head', 'ADD COLUMN `gstin` VARCHAR(30)');
        $this->addfield('adhar', $this->prefix . 'head', 'ADD COLUMN adhar VARCHAR(30)');
        $this->addfield('state', $this->prefix . 'head', 'ADD COLUMN `state` VARCHAR(30)');
        $this->addfield('panno', $this->prefix . 'head', 'ADD COLUMN `panno` VARCHAR(30)');
        $this->addfield('id_user', $this->prefix . 'head', 'ADD COLUMN `id_user` INT (11)');
        $this->addfield('ip', $this->prefix . 'head', 'ADD COLUMN `ip` VARCHAR (30)');
        $this->addfield('flicence', $this->prefix . 'head', 'ADD COLUMN `flicence` VARCHAR(40) NOT NULL AFTER dlicence');
        $this->addfield('acno', $this->prefix . 'head', 'ADD COLUMN `acno` VARCHAR(45)');
        $this->addfield('acifsc', $this->prefix . 'head', 'ADD COLUMN `acifsc` VARCHAR(30)');
        $this->addfield('acname', $this->prefix . 'head', 'ADD COLUMN `acname` VARCHAR(45)');
        $this->addfield('actype', $this->prefix . 'head', 'ADD COLUMN `actype` VARCHAR(30)');


        $this->addfield('id_represent', $this->prefix . 'area', 'ADD COLUMN `id_represent` INT(10)');
        $this->changefield('ref1', $this->prefix . 'voucher', 'CHANGE `ref1` `ref1` VARCHAR(40)');
        $this->changefield('ref2', $this->prefix . 'voucher', 'CHANGE `ref2` `ref2` VARCHAR(40)');

        
        $sql = "SHOW COLUMNS FROM `info` LIKE 'gstin'";
        $uid = $this->m->num_rows($this->m->query($sql)) == 0 ? 0 : 1;
        if ($uid == 0) {
            $sql = "ALTER TABLE `info` ADD COLUMN adhar VARCHAR(30), ADD COLUMN `gstin` VARCHAR(30) NOT NULL AFTER `membercode`, ADD COLUMN `gstdate` DATE NULL AFTER `gstin`,ADD COLUMN `flicence` VARCHAR(40) NOT NULL AFTER gstin, ADD COLUMN `dlicence` VARCHAR(40) NOT NULL AFTER flicence";
            $this->m->query($sql);
        }
        $this->upgrade();
        $_SESSION['msg'] = "Software Upgraded Successful.";
        $this->sm->assign("page", "util/check.tpl.html");
    }
    function check_autoincrement() {
        $msg = "<b>Autoincrement Checking</b><br><br>";
        $prefix = $this->prefix;
        $exclude = array($prefix . "ledger", $prefix . "product_ledger", $prefix . "product_ledger_summary", $prefix . "tb");
        $sql = "SHOW TABLES LIKE '{$prefix}_%'";
        $rs = mysql_query($sql);
        $records = $this->m->num_rows($rs);
        for ($i = 1; $i <= $records; $i++) {
            $data = $this->m->fetch_array($rs);
            $tablename = $data[0];
            if (!in_array($tablename, $exclude)) {
                $sql1 = "describe `{$tablename}`";
                $dt1 = $this->m->fetch_assoc($sql1);
                if ($dt1['Key'] != 'PRI') {
                    $msg .= $tablename . " do not Primary Key<br>";
                    $fld = $dt1['Field'];
                    $sql1 = "ALTER TABLE {$data[0]} CHANGE {$fld} {$fld} INT(11) AUTO_INCREMENT PRIMARY KEY;";
                    mysql_query($sql1);
                }
            } else {
                $msg .= $tablename . " is a view.<br>";
            }
        }
        $sql1 = "ALTER TABLE user CHANGE id_user id_user INT(11) primary key AUTO_INCREMENT  not null;";
        mysql_query($sql1);
        $sql1 = "ALTER TABLE info CHANGE id_info id_info INT(11) primary key AUTO_INCREMENT  not null;";
        mysql_query($sql1);
        $sql1 = "ALTER TABLE template CHANGE id_template id_template INT(11) primary key AUTO_INCREMENT  not null;";
        mysql_query($sql1);
        $this->sm->assign("msg", $msg);
        $this->sm->assign("page", "util/check.tpl.html");
    }
    function detail() {
        $data = $this->m->all_Tables($this->prefix);
        $this->sm->assign("prefix", $this->prefix);
        $this->sm->assign("detail", $data);
    }
    function import() {
        //    $this->sm->assign("page", "util/import.tpl.html");
    }
    function ulocal() {
        $sql = "SELECT id_head AS id,name FROM {$this->prefix}head WHERE debtor ORDER BY name";
        $this->sm->assign("head", json_encode($this->m->sql_getall($sql, 2, "name", "id")));
        $sql = "SELECT id_company AS id,name FROM {$this->prefix}company ORDER BY name";
        $this->sm->assign("company", json_encode($this->m->sql_getall($sql, 2, "name", "id")));
        $sql = "SELECT id_product AS id,name,mrp,seller_price,id_taxmaster_sale,pack,unit,`case`,closing_stock FROM {$this->prefix}product ORDER BY name";
        $data = json_encode($this->m->sql_getall($sql, 1, "name", "id"));
        $this->sm->assign("product", $data);
    }
    function upload() {
        ini_set('upload_max_filesize', '250M');
        ini_set('post_max_size', '250M');
        ini_set('max_input_time', 300);
        ini_set('max_execution_time', 300);
        $dest = trim($this->prefix, "_");
        move_uploaded_file($_FILES['upload']['tmp_name'], "data/{$dest}.zip");
        $zip = new ZipArchive;
        $res = $zip->open("data/{$dest}.zip");
        if ($res === TRUE) {
            $zip->extractTo("data/{$dest}/");
            $zip->close();
            $msg = 'Zip file Uploaded and extracted Successfully!';
        } else {
            $msg = 'Problem while extractin zip File!';
        }
        unlink("data/{$dest}.zip");
        $type = isset($_REQUEST['type']) ? "&type=" . $_REQUEST['type'] : "";
        $link = "index.php?module=import&func=convert_all" . $type;
        $this->redirect($link);
    }

    function create_backup() {
        set_time_limit(6000);
        ini_set("memory_limit", "256M");
        date_default_timezone_set('Asia/Kolkata');
        $tables = '*';
        if ($tables == '*') {
            $tables = array();
            $result = mysql_query('SHOW FULL TABLES WHERE Table_Type = "BASE TABLE"');
            while ($row = mysql_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            if (is_array($tables)) {
                $tables = explode(',', $tables);
            }
        }
        $return = "";
        foreach ($tables as $table) {
            $result = mysql_query("SELECT * FROM `$table`");
            $num_fields = mysql_num_fields($result);

            $return .= "DROP TABLE IF EXISTS `$table`;<|||||||>";

            $row2 = mysql_fetch_row(mysql_query("SHOW CREATE TABLE `$table`"));
            $return .= "\n\n" . $row2[1] . ";<|||||||>\n\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysql_fetch_row($result)) {
                    $return.= "INSERT INTO `$table` VALUES(";
                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n", "\\n", $row[$j]);
                        if (isset($row[$j])) {
                            $return .= '"' . $row[$j] . '"';
                        } else {
                            $return .= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return.= ',';
                        }
                    }
                    $return.= ");<|||||||>\n";
                }
            }
            $return.="\n\n\n";
        }
        $fname = str_replace(" ", "_", trim(trim($_SESSION['companyname']), "."));
        $fname = str_replace("&", "_", $fname);
        $sdate = substr($_SESSION['sdate'], 0, 4);
        $edate = substr($_SESSION['edate'], 0, 4);
        $this->file = $file = 'backup/' . $fname . "_" . $sdate . "_" . $edate . "_" . date("d-m-Y_h_i_s") . '.sql';
        $handle = fopen($file, 'w') or die("Can't open file");
        fwrite($handle, $return);
        fclose($handle);
    }

    function backup() {
        $this->create_backup();
        $_SESSION['msg'] = "Backup is Successful. File Created : " . $this->file;
        $this->redirect("index.php?module=util&func=listing");
    }

    function listing() {
        //$this->get_permission("util","UPDATE");
        $files = array();
        if ($dir = opendir("backup")) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.sql', 1)) {
                    $date = substr($file, strlen($file) - 23, 19);
                    $filename = str_replace('.sql', '', $file);
                    $files[] = array("date" => str_replace("_", ":", $date), "filename" => $filename);
                }
            }
        }
        $this->sm->assign("file", $files);
        $this->sm->assign("page", "util/listing.tpl.html");
    }

    function delete() {
        //          $this->get_permission("util","DELETE");
        $file = $_REQUEST['id'];
        if (!is_dir("backup/" . $file . '.sql')) {
            unlink("backup/" . $file . '.sql');
            $_SESSION['msg'] = "Backup file Successfully Delete.";
        } else {
            $_SESSION['msg'] = "Error occured while Deleting Backup file!!!";
        }
        $this->redirect("index.php?module=util&func=listing");
    }

    function downsql() {
        //         $this->get_permission("util","REPORT");
        $file = "backup/" . $_REQUEST['id'];
        if (!is_dir($file)) {
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $_REQUEST['id'] . '"');
            readfile($file);
            exit;
        }
    }

    function downzip() {
        return false;
    }

    function restore() {
        set_time_limit(6000);
        //        $this->get_permission("util","UPDATE");
        $this->create_backup();
        $file = "backup/" . $_REQUEST['id'] . ".sql";
        if (!is_dir($file)) {
            $f = fopen($file, "r+");
            $sqlFile = fread($f, filesize($file));
            $sqlArray = explode(';<|||||||>', $sqlFile);
            foreach ($sqlArray as $stmt) {
                if (strlen($stmt) > 3) {
                    mysql_query($stmt);
                }
            }
            $_SESSION['msg'] = "Backup file Restored Successfully.";
        }
        $this->redirect("index.php?module=util&func=listing");
    }

    function transentry() {
        echo "Please create Account Head as followings<br>";
        $sql = "SELECT p.name, h.id_head FROM `{$this->prefix}param` p, `{$this->prefix}head` h
                WHERE (p.name = 'saleac' OR p.name='purcac' OR p.name='cash' OR p.name='SALESRETURN' OR p.name='PURCHASERETURN') AND p.content=h.code";
        $data = $this->m->sql_getall($sql, 2, "id_head", "name");
        $sale = $data['saleac'] ? $data['saleac'] : $data['SALEAC'];
        $purc = $data['purcac'] ? $data['purcac'] : $data['PURCAC'];
        $saler = isset($data['SALESRETURN']) ? $data['SALESRETURN'] : $sale;
        $purcr = isset($data['PURCHASERETURN']) ? $data['PURCHASERETURN'] : $purc;
        $sql = 'DELETE FROM ' . $this->prefix . "voucher WHERE type=10";
        $this->m->query($sql);

        $sql = "SELECT sd.date, h.local, sd.tax_per, SUM(sd.tax_amount) as tax_amount 
                FROM {$this->prefix}saledetail sd LEFT JOIN {$this->prefix}head h ON sd.id_head=h.id_head GROUP BY 1, 2,3";
        $this->updategstentries($sql, $sale, 1, "SALES GST");

        $sql = "SELECT sd.date, h.local, sd.tax_per, SUM(sd.tax_amount) as tax_amount 
                FROM {$this->prefix}purchasedetail sd LEFT JOIN {$this->prefix}head h ON sd.id_head=h.id_head GROUP BY 1,2,3";
        $this->updategstentries($sql, $purc, 2, "PURCHASE GST");

        $sql = "SELECT sd.date, h.local, sd.tax_per, SUM(sd.tax_amount) as tax_amount 
                FROM {$this->prefix}sreturndetail sd LEFT JOIN {$this->prefix}head h ON sd.id_head=h.id_head GROUP BY 1,2,3";
        $this->updategstentries($sql, $saler, 2, "SALES RETURN GST");
        // $_SESSION['msg'] = "GST Journal Entry passed from Sales, Purchases and Sales Return.";
        // $this->redirect("index.php");
        echo "<h2>Please click <a href='index.php'>here</a> to go to Menu options.</h2>"; exit;
    }

    function updategstentries($sql, $acno, $type, $memo) {
        $data = $this->m->sql_getall($sql);
        foreach ($data as $k => $v) {
            if ($v['tax_per'] == 0) {
                continue;
            }
            if ($v['local'] == 0) {
                $tp = $v['tax_per'] / 2;
                $tp = (intval($tp) == $tp) ? intval($tp) : $tp;
                $str = "CGST " . $tp . "%";
                if (!isset($main[$str])) {
                    $sql = "SELECT id_head FROM {$this->prefix}head WHERE name='{$str}' LIMIT 1";
                    $c = $this->m->sql_getall($sql);
                    $main[$str] = isset($c[0]['id_head']) ? $c[0]['id_head'] : "";
                }
                if ($main[$str] != '') {
                    $dhead = ($type == 1) ? $acno : $main[$str];
                    $chead = ($type == 1) ? $main[$str] : $acno;
                    $sql = 'INSERT INTO ' . $this->prefix . "voucher (type, date, total, id_head_debit, id_head_credit, memo) VALUES 
                           (10, '$v[date]', $v[tax_amount]/2, $dhead, $chead, '$memo')";
                    $this->m->query($sql);
                } else {
                    echo $str . "- under DUTIES AND TAXES<br>";
                }
                $str = "SGST " . $tp . "%";
                if (!isset($main[$str])) {
                    $sql = "SELECT id_head FROM {$this->prefix}head WHERE name='{$str}' LIMIT 1";
                    $c = $this->m->sql_getall($sql);
                    $main[$str] = isset($c[0]['id_head']) ? $c[0]['id_head'] : "";
                }
                if ($main[$str] != '') {
                    $dhead = ($type == 1) ? $acno : $main[$str];
                    $chead = ($type == 1) ? $main[$str] : $acno;
                    $sql = 'INSERT INTO ' . $this->prefix . "voucher (type, date, total, id_head_debit, id_head_credit, memo) VALUES 
                           (10, '$v[date]', $v[tax_amount]/2, $dhead, $chead, '$memo')";
                    $this->m->query($sql);
                } else {
                    echo $str . "- under DUTIES AND TAXES<br>";
                }
            } else {
                $tp = $v['tax_per'];
                $tp = (intval($tp) == $tp) ? intval($tp) : $tp;
                $str = "IGST " . $tp . "%";
                if (!isset($main[$str])) {
                    $sql = "SELECT id_head FROM {$this->prefix}head WHERE name='{$str}' LIMIT 1";
                    $c = $this->m->sql_getall($sql);
                    $main[$str] = isset($c[0]['id_head']) ? $c[0]['id_head'] : "";
                }
                if ($main[$str] != '') {
                    $dhead = ($type == 1) ? $acno : $main[$str];
                    $chead = ($type == 1) ? $main[$str] : $acno;
                    $sql = 'INSERT INTO ' . $this->prefix . "voucher (type, date, total, id_head_debit, id_head_credit, memo) VALUES 
                           (10, '$v[date]', $v[tax_amount], $dhead, $chead, '$memo')";
                    $this->m->query($sql);
                } else {
                    echo $str . "- under DUTIES AND TAXES<br>";
                }
            }
        }
    }
}
?>