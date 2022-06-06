<?php

class permission extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']!=1) {
            $_SESSION['msg'] = "Your are not Authorised to set Permissions.";
            $this->redirect("index.php");
        }
        parent:: __construct();
    }

    function config() {
        $sql = "SELECT * FROM `configuration`";
        $config = $this->m->getall($this->m->query($sql));
        $this->sm->assign("config", $config);
    }
    
    function saveconfig() {
        $id = $_REQUEST['id'];
        $cid = $_REQUEST['cid'];
        $v = $_REQUEST['set'];
        $sql = "UPDATE `configuration` SET `value` = '$v' WHERE id_config='$id'";
        $this->m->query($sql);
        $sql = "SELECT name FROM `configuration` WHERE id_config='$id'";
        $data = $this->m->fetch_assoc($sql);
        $cname = $data['name'];
        $_SESSION['config'][$cname] = $v;
        echo "Successfully Updated.";
        exit;
    }

    function listing() {
        $sql = "SELECT id_permission, name FROM `permission`";
        $this->sm->assign("permission", $this->m->sql_getall($sql, 2, "name", "id_permission"));
        $sql = "SELECT id_module, name FROM `module`";
        $this->sm->assign("module", $this->m->sql_getall($sql, 2, "name", "id_module"));
        $sql = "SELECT a.*, mp.permission FROM (SELECT u.id_user, u.name AS username, m.id_module, m.name AS modulename, 
            p.id_permission, p.name AS permissionname FROM user u, module m, permission p WHERE u.is_admin!=1) AS a 
            LEFT JOIN module_map mp ON a.id_user=mp.id_user AND a.id_module=mp.id_module AND a.id_permission=mp.id_permission
            ORDER BY a.username, a.modulename";
        $data = array();
        $rs = $this->m->query($sql);
        while ($row = $this->m->movenexta($rs)) {
            $uname = $row['username'];
            $module = $row['modulename'];
            $permissionname = $row['id_permission'];
            $permission = $row['permission'];
            if (!isset($data[$uname][$module]))
                $data[$uname][$module] = $row;
            $data[$uname][$module][$permissionname] = $permission;
        }
//        $this->pr($data);
        $this->sm->assign("value", $data);
    }

    function save() {
        $u = $_REQUEST['id_user'];
        $m = $_REQUEST['id_module'];
        $p = $_REQUEST['id_permission'];
        $sql = "DELETE FROM module_map WHERE id_user=$u AND id_module=$m AND id_permission=$p";
        $this->m->query($sql);
        if (isset($_REQUEST['set']) && $_REQUEST['set']) {
            $v = $_REQUEST['set'];
            $sql = "INSERT INTO module_map (id_user, id_module, id_permission, permission) VALUES ($u, $m, $p, $v)";
            $this->m->query($sql);
        }
        echo "Successfully Updated.";
        exit;
    }

    function saveall() {
        $u = $_REQUEST['id_user'];
        $m = $_REQUEST['id_module'];
        $sql = "DELETE FROM module_map WHERE id_user=$u AND id_module=$m";
        $this->m->query($sql);
        if (isset($_REQUEST['set']) && $_REQUEST['set']) {
            $v = $_REQUEST['set'];
            $sql = "INSERT INTO module_map (id_user, id_module, id_permission, permission) SELECT $u, $m, id_permission, $v FROM permission";
            $this->m->query($sql);
        }
        echo "Successfully Updated.";
        exit;
    }

    function delete() { // currently not in use
        $u = $_REQUEST['user_id'];
        $m = $_REQUEST['config_id'];
        $sql = "delete from map where user_id='$u' and config_id='$m'";
        if ($this->m->query($sql)) {
            echo "success";
        } else {
            echo "fail";
        }
        exit;
    }

    function insert() { // currently not in use
        $this->m->query($this->create_delete("map", "user_id={$_REQUEST['user_id']}"));
        foreach ($_REQUEST['mod'] as $mod) {
            $sql = "INSERT INTO `map`(`user_id`, `config_id`, `access`) VALUES ({$_REQUEST['user_id']},{$mod},0)";
            $this->m->query($sql);
        }
        echo "success";
        exit;
    }

    function add() {  // currently not in use
        $sql1 = "SELECT id_user,user from user where user!='admin'";
        $a = $this->m->query($sql1);
        $this->sm->assign("user", $this->m->getall($a, 2, "user", "id_user"));
        $this->sm->assign("page", 'permission/home.tpl.html');
    }

    function assign() {  // currently not in use
        $sql = $this->create_select("map", "");
        $rs = $this->m->query($sql);
        $check = array();
        while ($row = $this->m->movenexta($rs)) {
            $check[$row['user_id']][$row['config_id']] = "checked";
        }
        $this->sm->assign("check", $check);
        $data = $this->m->sql_getall("select id_user,user from user ");
        $this->sm->assign("data", $data);
        $module = $this->m->sql_getall("select id,name from config where type='module'");
        $this->sm->assign("col", sizeof($module));
        $this->sm->assign("row", sizeof($data));
        $this->sm->assign("module", $module);
        $this->sm->assign("page", 'permission/add.tpl.html');
    }
    function access() {
        $sdate = $this->format_date(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date("d/m/Y"));
        $edate = $this->format_date(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date("d/m/Y"));
        $sql = "SELECT id_user,user FROM user ORDER BY user";
        $this->sm->assign("user", $this->m->sql_getall($sql, 2, "user", "id_user"));
        $id = $_REQUEST['id'];
        if ($id) {
            $sql = "SELECT * FROM accesslog WHERE date(date) >= '$sdate' AND date(date) <= '$edate' AND id_user='$id' ORDER BY date DESC";
            $data = $this->m->sql_getall($sql);
            $this->sm->assign("data", $data);
        }
    }
}
?>
