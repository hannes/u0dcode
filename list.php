<?php
function ew($str,$sub) {
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

require_once('helper.inc.php');
if ($_REQUEST['key'] != "eule") die("FU!");
$result = mysql_query("SELECT `id`,`filename` FROM `files` ORDER BY `date` DESC, `time` DESC");
while ($row = mysql_fetch_assoc($result)) {
	$f =  strtolower($row['filename']);
    if (ew($f,".jpg") || ew($f,".png") || ew($f,".gif")) {
	$url = $sys_url.$row['id']."/".specialchars_replace($f);
	echo "<a href='".$url."'><img src='".$url."' height='200' border='0' /></a>";
}
}
?>
