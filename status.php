<?php require_once('helper.inc.php');  ?>
<html>
<head>
  <title>check file status - u0d.de</title>
  <link rel="stylesheet" href="style.css" type="text/css"> 
</head>
<body>
<div id="logo"><a href="/"><img src="u0d_logo.png" border=0></a></div>

<?php 
$url = $_POST['url'];
$have_id = !empty($url);

if (!$have_id) {
?>
<div class="message">
<h2>Enter URL</h2>
<form
  action="<?php print $_SERVER['PHP_SELF']; ?>"
  method="post"
  enctype="multipart/form-data">
<input type="text" name="url" size=20 value="XXXXXX" onclick="this.value='';" />&nbsp;&nbsp;
<input type="submit" value="check" />
</form>
</div>

<?php
}

else {
    //$res = mysql_query("SELECT ");
    $id = digest_url($url);
    if (empty($id)) die ("<div class='error'>not found!</div>");
    $res = mysql_query("SELECT `date`,`filename`,`downloads` 
                        FROM `files` 
                        WHERE `id`='$id';");
                        
    if (!mysql_num_rows($res)) die ("<div class='error'>$id not found! sorry.</div>");  
    list($db_date,$db_filename,$db_downloads) = mysql_fetch_row($res);
    $dl_link = 	$sys_url .$id . "/" . specialchars_replace($db_filename); 
    
    if (is_file_locked($db_date,$db_downloads)) {
        print "<div class='error'>Your file has been locked! Please contact us if you should need it.</div>";
    }
    
    $days_left = $limit_days - day_distance($db_date) + 1;
    if ($days_left < 0) $days_left = 0;
    
    $dl_left = $limit_dl - $db_downloads;
    if ($dl_left < 0) $dl_left = 0
?>
<div class="message">   
<table>
    <tr>
        <td>Filename:</td>
        <td class="info"><?php print $db_filename; ?> [<a href="<?php print $dl_link; ?>">download</a>]</td>
    </tr>
    <tr>
        <td>Downloads:</td>
        <td class="info"><?php print $dl_left." of ".$limit_dl; ?> left</td>
    </tr>
    <tr>
        <td>Days:</td>
        <td class="info"><?php print $days_left . " of ".$limit_days; ?> left</td>
    </tr>
</table>
</div>
<?php
}

    
?>
<small><a href="index.php">upload file</a> / service by <a href="http://www.living-site.net">living-site.net</a></small> 
</body>
</html>
