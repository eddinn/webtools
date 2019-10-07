<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php	$version = "2.0";
	$date = date("Y");
	$author = "Edvin Dunaway";
	$email = "edvin[at]eddinn.net";
	$webpage = "eddinn.net"; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>IceDNS</title>
	</head>
<body>
	<div style="border-left:1px solid #999999;width:500px;height:117px;margin:0px 0px 0px 4px;padding:0px 0px 0px 4px;">
		<form action="icedns.php" method="post">
			<p style="font-family:arial;font-size:10pt;">
			<?php echo"<strong>IceDNS - version $version</strong><br /><br />"; ?>
			<input type="text" name="ip" value="<?php print(((isset($_POST['ip']) ? $_POST['ip'] : "Enter address.."))); ?>" size="42" style="margin:4px 0px 0px 0px;padding:1px 1px 1px 2px;border:1px solid #888888;" onfocus="this.value=''" onblur="this.style.background='#ffffff'" /><br />
                        <br />
			<input type="submit" name="submit" value="Submit" style="font-family:arial;font-size:10pt;border:1px solid #888888;margin:4px 0px auto 0px;" />
			</p>
		</form>
	</div>
	<div style="border-left:1px solid #999999;border-top:1px solid #999999;width:500px;float:left;min-height:160px;margin:0px 0px 0px 4px;padding:0px 0px 0px 4px;">
		<p style="font-family:arial;font-size:10pt;">
		<?php if( $_POST['ip'] != '' ) {
    			$arr_ip = array_reverse( explode( '.', $_POST['ip'] ) );
    				if( gethostbyname( implode( '.', $arr_ip ) . '.iceland.rix.is' ) == '127.1.0.1' ) {
        				echo $_POST['ip'] . ' er .is IP-tala!';
    				} else {
        				echo $_POST['ip'] . ' er ekki .is IP-tala!'; }
                        echo "<p style=\"font-family:arial;font-size:10pt;\"><strong>IceDNS resaults for $arr_ip </strong></p><pre>";
			echo '</pre><br /><form action="icedns.php" method="post">';
                        echo '<input type="submit" name="Reset" value="Reset" style="font-family:arial;font-size:10pt;border:1px solid #888888;margin:4px 0px 4px 0px;" /></form>'; } ?>
		</p>
	</div>
	<div style="width:500px;height:auto;margin:500px 0px 0px 4px;padding:0px 0px 0px 4px;">
		<p style="font-family:arial;font-size:10pt">&copy; <?php echo"$date"; ?> :: <?php echo"$author"; ?> :: <a href="mailto:edvin[at]eddinn.net"><?php echo"$email"; ?></a> :: <a href="http://www.eddinn.net"><?php echo"$webpage"; ?></a><br /><br />
		<a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10-blue" alt="Valid XHTML 1.0 Strict" height="31" width="88" style="border:0px;" /></a>
		<a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Valid CSS!" />
</a></p>
	</div>
</body>
</html>
