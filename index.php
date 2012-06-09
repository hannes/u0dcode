<?php
require_once('helper.inc.php');

$have_file = isset($_FILES['probe']) and 
				  !$_FILES['probe']['error'] and 
				   $_FILES['probe']['size'] > 10;
?>
<html>
<head>
  <title>upload your crap - u0d.de</title>
  <link rel="stylesheet" href="style.css" type="text/css"> 
  <link rel="shortcut icon" href="http://d.u0d.de/icon.png" type="image/png">
  <link rel="icon" href="http://u0d.de/icon.png" type="image/png">	
</head>
<body>
<div id="logo"><a href="/"><img src="u0d_logo.png" border=0></a></div>

<?php 
if (!$have_file) {
?>
<div class="message">
<h2>Upload File:</h2>
<form
  action="<?php print $_SERVER['PHP_SELF']; ?>"
  method="post"
  enctype="multipart/form-data">
  <!-- onchange="this.form.btn.disabled=true;this.form.btn.value='please stand by...';this.form.submit();" -->
<input type="file" name="probe" />&nbsp;&nbsp;
PW:
<input type="password" value="" name="pw" size="10" />&nbsp;&nbsp;

<input type="submit" value="upload!" name="btn" class="button" />

</form>
</div>
<small>Max. Size: <?php print ini_get('post_max_size'); ?> / <a href="status.php">check file status</a> / 
ask <a href="http://hannes.muehleisen.org">Hannes M&uuml;hleisen</a> for a password</small>
<br><br>
<?php
}

if ($have_file) {
	handle_upload();
	?>
	<a class='back-link' href='/'>&larr; back</a>
	<?php
}
?>
</body></html>
