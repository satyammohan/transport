<?php
$con=  mysql_connect('192.168.0.102', 'root', 'lit')or die('could not connect');
$db=  mysql_select_db('transport') or die("could not connect");
$parent=array();
$parent[]=1;
$parent[]=2;
$parent[]=3;
$parent[]=4;
$parent[]=5;
$parent[]=6;
$parent[]=7;
$parent[]=8;
$parent[]=9;
$parent[]=10;
$parent[]=11;
$parent[]=12;
for($i=0;$i<sizeof($parent);$i++){
    tree_view($parent[$i]);
}

function tree_view($index)
{
     $q1=mysql_query("SELECT name FROM `group` WHERE id=$index AND id_parent='0'");
     if($par=mysql_fetch_array($q1)){
       echo "<b>";  echo $par['name'];echo "</b>";
     }
    $q=mysql_query("SELECT * FROM `group` WHERE id_parent=$index");
    if(!mysql_num_rows($q))
    echo '<ul>';
    while($arr=mysql_fetch_assoc($q))
    {
        echo '<li>';
        echo $arr['name'];//you can add another output there
        tree_view($arr['id']);
        echo '</li>';
    }
    echo '</ul>';
    
}  

?>