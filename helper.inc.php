<?php

// CHANGE BELOW!
    $host = "localhost";
    $user = "asdf";
    $pass = "asdfpass";
    $db   = "asdfdb";
    
    $sys_url = "http://d.u0d.de/";
    $dir = "/some/absolute/path/to/data/";
    
    $limit_dl = 200;
    $limit_days = 100;
    
   // END CONFIG 
    
    require_once("specialchars.inc.php");

    function get_hash() {
	    return substr(md5(microtime().rand()),0,6);
    }
    
    function is_hash($name) {
	    return (preg_match('/^[a-f0-9]{6}$/', $name));
    }
    
    function digest_url($url) {
        preg_match("/(\/|^)([a-f0-9]{6})(\/|$)/",$url, $match);
        return $match[2];
    }
    
    function day_distance($date) {
        $timestamp = strtotime($date);
        $todaystamp = time();
        $dist = $todaystamp - $timestamp;
        return round($dist / (24*60*60) ,0);
    }
    
    function check_day_distance($date) {
        global $limit_days;
        return day_distance($date) > $limit_days;
    }
    
    function check_download_limit($downloads) {
        global $limit_dl;
        return $downloads > $limit_dl;
    }
    
    function is_file_locked ($date, $downloads) {
        return check_day_distance($date) || check_download_limit($downloads);
    }

    $conn = @mysql_connect($host,$user,$pass) or die("DB ERROR: ".mysql_error());
    @mysql_select_db($db) or die("DB ERROR: ".mysql_error());

	if (!function_exists("mime_content_type")) {
		function mime_content_type($file) {
			$cmd = "file -bi -- ".escapeshellarg($file);
			$type = trim(`$cmd`);
			return $type;
		}
	}
	
	function check_pw($pw) {
		$dbpw = md5($pw);
		
		$res = mysql_query("SELECT `id`
                    FROM `users` 
                    WHERE `pw_hash`='$dbpw';");
                    
		if (mysql_num_rows($res)) {
    		list($db_id) = mysql_fetch_row($res);
    		return $db_id;
		}
		return false;
	}
	
	function handle_upload() {
	global $sys_url, $dir, $limit_days, $limit_dl;
	cleanup();
	
	$user = check_pw($_REQUEST['pw']);
	
	if ($user === false) {
		print "<div class='error'>Wrong Password.</div>";
		return;
	}

	$upload_filename = $_FILES['probe']['name'];

	$filename = get_hash();
	while (is_file($dir.$filename))
		$filename = get_hash();

	$img_url = $sys_url .$filename . "/" . specialchars_replace($upload_filename);
	if (move_uploaded_file($_FILES['probe']['tmp_name'], $dir . $filename)) {
		print "<div class='message'>";
		print "<h2>Access to your file:</h2>";
		print "<a class='dl-link' href='$img_url'>$img_url</a><br><br>";
		print "<input class='dl-input' type='text' value='$img_url' size=45 onclick='select(this);'><small>[Plain URL]</small><br>"; 
		print "<input class='dl-input' type='text' value='<a href=\"$img_url\">$upload_filename</a>' size=45 onclick='select(this);'><small>[HTML Link]</small><br>"; 
		print "<input class='dl-input' type='text' value='[url=".$img_url."]".$upload_filename."[/url]' size=45 onclick='select(this);'><small>[BBCode]</small>"; 
		print "<br><br><small>Your file will be available for <b>$limit_days days</b>, and is limited to <b>$limit_dl downloads</b>.</small>";
		print "</div>" ;
        mysql_query("INSERT INTO `files` 
                     (`id`,`date`,`time`,`size`,`host`,`filename`,`downloads`,`user`) 
              VALUES ('$filename',CURDATE(),CURTIME(),'".filesize($dir . $filename)."','".gethostbyaddr($_SERVER['REMOTE_ADDR'])."','$upload_filename',0,$user);");
	}
	else
		print "<div class='error'>Could not save file.</div>";
	}
	
	function cleanup() {
	global $dir;
		$list = array();
		$del = array();
		$cnt = 0;
		$del = 0;
		
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
	}

?>
