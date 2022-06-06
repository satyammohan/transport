<?php

class report extends common {

    function __construct() {
        $this->checklogin();
        $this->table_prefix();
        parent:: __construct();
    }

    function common() {
        $res = $this->m->query($this->create_select("`{$this->prefix}group`", ""));
        $this->sm->assign("group", $this->m->getall($res, 2, "name", "id"));
        $res1 = $this->m->query($this->create_select($this->prefix . "area", ""));
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id"));
        $res2 = $this->m->query($this->create_select($this->prefix . "transport", ""));
        $this->sm->assign("transport", $this->m->getall($res2, 2, "name", "id"));
    }

    function Area() {
        $sdata = $_REQUEST['head'];
        $data = ($sdata["id_area"]);
        $length = sizeof($data);
        for ($i = 0; $i < $length; $i++) {
            $res = $this->m->query($this->create_select($this->prefix . "head", "id_area='$data[$i]'"));
            $rows = $this->m->num_rows($res);
            for ($l = 0; $l < $rows; $l++) {
                $result[] = $this->m->fetch_array($res);
            }
        }
        $this->sm->assign("sdata", $result);
        $this->common();
        $this->sm->assign("page", "report/search_list.tpl.html");
    }

    function Zone() {
        $sdata = $_REQUEST['head'];
        $data = $sdata["id_area"];
        for ($i = 0; $i < sizeof($data); $i++) {
            $sql = "SELECT h.*
            FROM {$this->prefix}head h, {$this->prefix}area a, {$this->prefix}zone z
            WHERE a.id_zone='{$data[$i]}' AND a.id_zone=z.id AND a.id=h.id_area ";
            $res = $this->m->query($sql);
            for ($l = 0; $l < $this->m->num_rows($res); $l++) {
                $result[] = $this->m->fetch_array($res);
            }
        }
        $this->sm->assign("sdata", $result);
        $this->common();
        $this->sm->assign("page", "report/search_list.tpl.html");
    }

    function Group() {
        $result = array();
        $sdata = $_REQUEST['head'];
        $data = ($sdata["id_area"]);
        $length = sizeof($data);
        for ($i = 0; $i < $length; $i++) {
            $res = $this->m->query($this->create_select("{$this->prefix}head", "id_group='$data[$i]'"));
            $rows = $this->m->num_rows($res);
            for ($l = 0; $l < $rows; $l++) {
                $result[] = $this->m->fetch_array($res);
            }
        }
        if ($result !== '') {
            $this->sm->assign("sdata", $result);
            $this->common();
        }
        $this->sm->assign("page", "report/search_list.tpl.html");
    }

    function search_area() {
        $this->sm->assign("type", 'Area');
        $res1 = $this->m->query($this->create_select("{$this->prefix}area", ""));
        $this->sm->assign("area", $this->m->getall($res1, 2, "name", "id"));
        $this->sm->assign("page", "report/report.tpl.html");
    }

    function search_group() {
        $this->sm->assign("type", 'Group');
        $res1 = $this->m->query($this->create_select("`{$this->prefix}group`", ""));
        $this->sm->assign("group", $this->m->getall($res1, 2, "name", "id"));
        $this->sm->assign("page", "report/report.tpl.html");
    }

    function search_zone() {
        $res1 = $this->m->query($this->create_select("{$this->prefix}zone", ""));
        $this->sm->assign("type", 'Zone');
        $this->sm->assign("zone", $this->m->getall($res1, 2, "name", "id"));
        $this->sm->assign("page", "report/report.tpl.html");
    }

    function report() {
        if (isset($_REQUEST['ack'])) {
            $do = $_REQUEST['name'];
            $this->$do();
        } else {
            $this->$_REQUEST['page']();
        }
    }

}

?>
