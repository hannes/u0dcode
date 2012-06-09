<?php
require_once('helper.inc.php'); 

$datafile = $_REQUEST['data_filename'];

if (!(is_hash($datafile) && is_file($dir.$datafile))) 
    die("fu!");

$res = mysql_query("SELECT `date`,`filename`,`downloads` 
                    FROM `files` 
                    WHERE `id`='$datafile';");
                    
if (mysql_num_rows($res)) {
    list($db_date,$db_filename,$db_downloads) = mysql_fetch_row($res);

    if (is_file_locked($db_date,$db_downloads)) 
    die("This file has been locked!");

    mysql_query("UPDATE `files` SET `downloads`=`downloads`+1 WHERE `id`='$datafile';");
} 
else die("fu!");
// do business

header("Content-Type: ".mime_content_type($dir.$datafile));
header("Content-Disposition: inline;");
header("Content-Length: ".filesize($dir.$datafile));
readfile($dir.$datafile);
?>
