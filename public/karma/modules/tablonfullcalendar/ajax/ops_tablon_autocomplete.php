<?php
if (!defined("CHK_KARMA")) die("{}");
$q = strtolower($_GET["q"]);
if (!$q) return;
$sql = "select ".mysql_real_escape_string($_GET['autocompletefield'])." as f from ".mysql_real_escape_string($_GET['autocompletetab'])." where  ".mysql_real_escape_string($_GET['autocompletefield'])." like '".mysql_real_escape_string($_GET['q'])."%' ";


//echo 1$_GET['autocompletetabconds'] ;


$conds = isset($_GET['autocompletetabconds'])? mysql_real_escape_string($_GET['autocompletetabconds']):false;

if ($conds!="null" && trim($conds)!="") $sql.= " and ".stripslashes($conds);  

$c = new con($sql);
while ($r=$c->getResult()){
echo $r['f']."|\n";	
}	
	exit();
?>
