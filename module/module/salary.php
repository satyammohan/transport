<?php

class salary extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function _default() {
    }

    function getrow() {
	ob_clean();
        $id = @$_REQUEST['id'];
        if ($id) {
            $sql = "SELECT id_head, basic, da, hra, medical, convency, telephone, health FROM {$this->prefix}employee WHERE id_employee={$id}";
            $data = $this->m->sql_getall($sql);
            $id_head = $data[0]['id_head'];
            $sql = "SELECT SUM(-debit+credit) AS padvance FROM {$this->prefix}tb WHERE id_head={$id_head}";
            $ledg = $this->m->sql_getall($sql);
            $data[0]['padvance'] = $ledg[0]['padvance'];
            echo json_encode($data[0]);
        }
        exit;
    }

    function generate() {
        if (isset($_REQUEST['Date_Month']) AND $_REQUEST['Date_Month']) {
            $month = $_REQUEST['Date_Month'];
            $year = $_REQUEST['Date_Year'];
            $sql = "SELECT s.*, e.no, e.id_employee, e.name, e.designation, e.no*1 AS eno
                FROM {$this->prefix}employee e LEFT JOIN (SELECT * FROM {$this->prefix}salary WHERE month={$month} AND year={$year}) s
                ON e.id_employee=s.id_employee WHERE e.status!=1 ORDER BY eno";
            $data = $this->m->sql_getall($sql);
            $this->sm->assign("employee", $data);
        }
    }

    function edit() {
        $this->addfield('esiflag', $this->prefix . 'employee', 'ADD `esiflag` TINYINT(1)');
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "0";
        if ($id)
            $this->get_permission("salary", "INSERT");
        else
            $this->get_permission("salary", "UPDATE");
        $sql = "SELECT e.*, h.name AS party_name FROM {$this->prefix}employee e LEFT JOIN  {$this->prefix}head h ON e.id_head=h.id_head WHERE id_employee=$id ";
        $data = $this->m->fetch_assoc($sql);
        $this->sm->assign("data", $data);
        $this->sm->assign("page", "salary/add.tpl.html");
    }

    function insert() {
        $this->get_permission("salary", "INSERT");
        $data = $_REQUEST['employee'];
        $data['id_create'] = $_SESSION['id_user'];
        $data['id_modify'] = $_SESSION['id_user'];
        $data['create_date'] = date("Y-m-d h:i:s");
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $res = $this->m->query($this->create_insert($this->prefix . "employee", $data));
        $_SESSION['msg'] = "Record Successfully Inserted";
        $this->redirect("index.php?module=&func=listing");
    }

    function update() {
        $this->get_permission("salary", "UPDATE");
        $data['id_modify'] = $_SESSION['id_user'];
        $res = $this->m->query($this->create_update($this->prefix . "employee", $_REQUEST['employee'], "id_employee='{$_REQUEST['id']}'"));
        $_SESSION['msg'] = "Record Successfully Updated";
        $this->redirect("index.php?module=salary&func=listing");
    }

    function listing() {
        $this->get_permission("salary", "REPORT");
        if (isset($_REQUEST['status'])) {
            ($_REQUEST['status'] == 2) ? $wcond = "" : $wcond = " WHERE status = " . $_REQUEST['status'];
        } else {
            $_REQUEST['status'] = 2;
            $wcond = "  ";
        }
        $sql = "SELECT * FROM {$this->prefix}employee $wcond ORDER BY `name`";
        $this->sm->assign("employee", $this->m->sql_getall($sql));
    }

    function delete() {
        $this->get_permission("salary", "DELETE");
        $res1 = $this->m->query($this->create_select("{$this->prefix}salary", "id_employee='{$_REQUEST['id']}'"));
        if ($this->m->num_rows($res1) > 0) {
            $_SESSION['msg'] = "Employee Can't be Deleted<br>It's Found to be Associated With some Other Information! ";
        } else {
            $this->m->query($this->create_delete("{$this->prefix}employee", "id_employee='{$_REQUEST['id']}'"));
            $_SESSION['msg'] = "Record Successfully Deleted";
        }
        $this->redirect("index.php?module=salary&func=listing");
    }

    function deleterow() {
        $this->get_permission("salary", "DELETE");
        $id = $_REQUEST['id'];
        $month = $_REQUEST['month'];
        $year = $_REQUEST['year'];
        $sql = $this->create_delete("{$this->prefix}salary", "id_salary='{$id}' AND month='{$month}' AND year='{$year}'");
        $this->m->query($sql);
        exit;
    }

    function saverow() {
        // deduct_other, otamt, others, cgross, cspecial, special, cothers
        $data1 = $_REQUEST['data'];
        foreach($data1 as $k =>$v) {
            $data1[$k] = ($v=="") ? 0 : $v;
        }
        $data1['id_create'] = $_SESSION['id_user'];
        $data1['id_modify'] = $_SESSION['id_user'];
        $data1['create_date'] = date("Y-m-d h:i:s");
        $data1['ip'] = $_SERVER['REMOTE_ADDR'];

        $id = $_REQUEST['data']['id_salary'];
        if ($id) {
            $this->get_permission("salary", "UPDATE");
            $sql = $this->create_update("{$this->prefix}salary", $data1, "id_salary='$id'");
        } else {
            $this->get_permission("salary", "INSERT");
            unset($data1['id_salary']);
            $sql = $this->create_insert("{$this->prefix}salary", $data1);
        }
        $this->m->query($sql);
        exit;
    }

    function payslip() {
        $this->get_permission("salary", "REPORT");
        $_REQUEST['start_date'] = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : $this->format_ymd($_SESSION['start_date']);
        $_REQUEST['end_date'] = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : $this->format_ymd($_SESSION['end_date']);
        $sdate = $this->format_date($_REQUEST['start_date']);
        $edate = $this->format_date($_REQUEST['end_date']);
        $sql = "SELECT e.name, e.no, e.function, e.designation, e.location, e.bank, e.acno, s.*, `year`*12+`month` AS mymonth
                FROM `{$this->prefix}salary` s, `{$this->prefix}employee` e 
                WHERE e.id_employee=s.id_employee AND (`year`*12+`month` >= YEAR('$sdate')*12+month('$sdate')
                AND `year`*12+`month` <= YEAR('$edate')*12+month('$edate')) ORDER BY mymonth DESC, e.no, e.name";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("data", $res);
    }

    function getslip() {
        $this->get_permission("salary", "REPORT");
        $id = $_REQUEST['id'];
        $sql = "SELECT e.name, e.no, e.function, e.designation, e.location, e.bank, e.acno, e.pan, e.esidetails, e.pfdetails, 
                s.*, `year`*12+`month` AS mymonth
                FROM `{$this->prefix}salary` s, `{$this->prefix}employee` e 
                WHERE e.id_employee=s.id_employee AND s.id_salary=$id ";
        $res = $this->m->sql_getall($sql);
        $res[0]['words'] = $this->convert_number(round($res[0]['total']));
        $this->sm->assign("data", $res);
    }

    function deduction() {
        $this->get_permission("salary", "REPORT");
    }

    function esi() {
        $this->get_permission("salary", "REPORT");
    }

    function epf() {
        $this->get_permission("salary", "REPORT");
    }
    function statement() {
        $this->get_permission("salary", "REPORT");
        $sql = "SELECT id_employee, name FROM  `{$this->prefix}employee` ORDER BY name";
        $this->sm->assign("employee", $this->m->sql_getall($sql, 2, "name", "id_employee"));
        if (isset($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            $sql = "SELECT e.name, e.no, e.function, e.designation, e.location, e.bank, e.acno, s.*, `year`*12+`month` AS mymonth
                    FROM `{$this->prefix}salary` s, `{$this->prefix}employee` e 
                    WHERE e.id_employee=s.id_employee AND s.id_employee='$id'
                    ORDER BY mymonth DESC, e.no, e.name";
            $res = $this->m->sql_getall($sql);
            $this->sm->assign("data", $res);
        }
    }
    function master() {
        $this->get_permission("salary", "INSERT");
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->prefix}salarymaster` (
            `id_salarymaster` int(11) NOT NULL AUTO_INCREMENT, 
            `esi` decimal(15,2) NOT NULL, `epfo` decimal(15,2) NOT NULL, `epfe` decimal(15,2) NOT NULL,
            `ip` varchar(30) NOT NULL,
            `id_create` int(11) NOT NULL,`create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
            `id_modify` int(11) NOT NULL,`modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id_salarymaster`)) ENGINE=InnoDB ;";
        $this->m->query($sql);
        $sql = "SELECT * FROM {$this->prefix}salarymaster";
        $res = $this->m->sql_getall($sql);
        $this->sm->assign("master", $res[0]);
    }
    function saveupdate() {
        $this->get_permission("salary", "INSERT");
        $data = $_REQUEST['master'];
        if ($_REQUEST['id']) {
            $sql = $this->create_update($this->prefix . "salarymaster", $data, "id_salarymaster='{$_REQUEST['id']}'");
        } else {
            $sql = $this->create_insert($this->prefix . "salarymaster", $data);
        }
        $this->m->query($sql);
        $_SESSION['msg'] = "Salary Masters Saved Successfully.";
        $this->redirect("index.php");
    }

    function attendance() {
        $this->get_permission("salary", "INSERT");
        $date = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("Y-m-d");
        $sql = "SELECT e.id_employee, e.name, e.no, e.function, e.designation, e.location, a.date, a.type
                FROM `{$this->prefix}employee` e LEFT JOIN `{$this->prefix}salary_attendance` a ON a.id_employee=e.id_employee AND a.date='$date'
                WHERE e.status=0
                ORDER BY e.name";
        $attendance = $this->m->sql_getall($sql);
        $this->sm->assign("attendance", $attendance);
        $this->sm->assign("date", $date);
    }
    function save_salary_attendance() {
        $this->get_permission("salary", "INSERT");
        $date = $_REQUEST['date'];
        $id_employee = $_REQUEST['id_emp'];
        $status = $_REQUEST['status'];
        ob_clean();
        $sql = "DELETE FROM `{$this->prefix}salary_attendance` WHERE date='$date' AND id_employee='$id_employee'";
        $this->m->query($sql);
        if ($status!=0) {
            $id_create = $_SESSION['id_user'];
            $create_date = date("Y-m-d h:i:s");
            $ip = $_SERVER['REMOTE_ADDR'];
            $sql = "INSERT INTO `{$this->prefix}salary_attendance` (date, id_employee, type, ip, id_create, create_date) VALUES 
                    ('$date', '$id_employee', '$status', '$ip', '$id_create', '$create_date')";
            echo $sql;
            $this->m->query($sql);
        }
        exit;
    }
    function attendance_summary() {
        if (!isset($_REQUEST['Date_Month'])) {
            $_REQUEST['Date_Month'] = date("m");
            $_REQUEST['Date_Year'] = date("Y");
        }
        $month = $_REQUEST['Date_Month'];
        $year = $_REQUEST['Date_Year'];
        $sql = "SELECT date, type, id_employee FROM {$this->prefix}salary_attendance
            WHERE month(date)={$month} AND year(date)={$year} ORDER BY date";
        $data = $this->m->sql_getall($sql);

        $sunday = $att = array();
        $lastday = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        for ($i=1; $i<=$lastday; $i++) {
            $week = date("w", strtotime("$year-$month-$i"));
            if ($week==0) {
                $sunday[$i] = $i;
            }
        }
        $this->sm->assign("sunday", $sunday);
        foreach($data as $v) {
            $week = date("w", strtotime($v['date']));
            $day = date("d", strtotime($v['date']))*1;
            $e = $v['id_employee'];
            $att[$e][$day] = $v['type'];
        }
        $sql = "SELECT no, id_employee, name, esiflag, designation FROM {$this->prefix}employee WHERE status!=1 ORDER BY esiflag, name";
        $emp = $this->m->sql_getall($sql);
        $this->sm->assign("emp", $emp);
        $this->sm->assign("attendance", $att);
        $this->sm->assign("days", $lastday);
    }
}
?>
