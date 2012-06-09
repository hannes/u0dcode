<?php 
$list = array();
$del = array();
$cnt = 0;
$del = 0;
require_once('helper.inc.php'); 

mysql_query("delete from files where `date` < DATE_SUB(curdate(),INTERVAL 100 day)  OR `downloads` > 200;") or die("Unable to query DB");

$res = mysql_query("SELECT `id` FROM `files`;") or die("Unable to query DB");
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    $list[] = $row['id'];
}
$handle = opendir($dir) or die("Unable to open directory ".$dir);
while (false !== ($file = readdir($handle))) {
//	print $dir.$file."\n";
	if (is_hash($file) && !in_array($file,$list)) { 
		unlink($dir.$file) or die("Unable to delete files...");
			
		$del++;
	}
	$cnt++;
}
closedir($handle);
print "Deleted ".$del." of ".$cnt." files.\n";
?>
