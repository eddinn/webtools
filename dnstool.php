<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php	$version = "1.10";
	$copydate = date("Y");
	$author = "Edvin Dunaway - edvin[at]eddinn.net"; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>New Domain - DNS Editor</title>
	<script type="text/javascript">
		/*<![CDATA[*/
			function getVALUE() { document.getElementById("mainform").mxform.value = document.getElementById("mainform").mx.value; }
		/*]]>*/
	</script>
	</head>
<body>
	<div style="border-left:1px solid #999999;width:500px;height:166px;margin:0px 0px 0px 4px;padding:0px 0px 0px 4px;">
		<form id="mainform" action="dnstool.php" method="post">
			<p style="font-family:arial;font-size:10pt;">
			<?php echo"<strong>New Domain - DNS Editor - version $version</strong><br /><br />"; ?>
			<input type="text" name="host" value="<?php print(((isset($_POST['host']) ? $_POST['host'] : "Domain.."))); ?>" size="42" style="margin:4px 0px 0px 0px;padding:1px 1px 1px 2px;border:1px solid #888888;" onfocus="this.value=''" onblur="this.style.background='#ffffff'" /><br />
			<input type="text" name="ip" value="<?php print(((isset($_POST['ip']) ? $_POST['ip'] : "IP Address.."))); ?>" size="42" style="margin:4px 0px 0px 0px;padding:1px 1px 1px 2px;border:1px solid #888888;" onfocus="this.value=''" onblur="this.style.background='#ffffff'" /><br />
			<select name="mx" style="border:1px solid #999999;margin:4px 0px 0px 0px;" onchange="javascript:getVALUE()">
                        <option value="">MX</option>
                        <option value="82.221.35.186">eddinn.net</option>
                        <option value="">Custom MX</option>
			</select>
			<input type="text" name="mxform" value="<?php print(((isset($_POST['mxform']) ? $_POST['mxform'] : "Custom MX.."))); ?>" onfocus="this.value=''" size="20" style="margin:4px 0px 0px 0px;padding:1px 1px 1px 2px;border:1px solid #888888;" /><br />
                        <br />
			<input type="submit" name="submit" value="Submit" style="font-family:arial;font-size:10pt;border:1px solid #888888;margin:4px 0px auto 0px;" />
			</p>
		</form>
	</div>
	<div style="border-left:1px solid #999999;border-top:1px solid #999999;width:500px;float:left;min-height:160px;margin:0px 0px 0px 4px;padding:0px 0px 0px 4px;">
		<?php if (isset($_REQUEST["submit"]) == "submit") {
			$basedir = "/var/named/chroot/var/named/";
                        $date = date("dmY");
			$digexec = "/usr/bin/dig";
                        $hostopt = $_POST["host"];
			$ip = $_POST["ip"];
			$mxadd = $_POST["mxform"];
			$namedconffile = "/etc/named.conf";
			$nameddir = "/var/named/";
			$rndcexec = escapeshellcmd("/usr/sbin/rndc");
			$rndcreconf = escapeshellarg("reconfig");
			$rndcreload = escapeshellarg("reload");
			$validip = '/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/';
			if (preg_match($validip, $mxadd)) {
                                $mxadd = $_POST["mxform"]; 
			} elseif ($mxadd === '') {
                                exit("ERROR: MX can't be blank!");
			} else {
				exit("ERROR: ($mxadd) is not a valid IP Address1!"); }
			if (preg_match($validip, $ip)) {
                                $ip = $_POST["ip"]; 
			} else {
				exit("ERROR: ($ip) is not a valid IP Address!"); }
			$search_mx_array = array('82.221.35.186' => 0, $mxadd => 1, '' => 2);
			if (!array_key_exists($mxadd,$search_mx_array)) {
				exit("ERROR: ($mxadd) is not a valid IP Address2!"); }
			chdir ($basedir);
			if (!mkdir($hostopt, 0775)) {
				exit("Failed to create directory ($hostopt)!"); }
			if (!$hostfile = fopen($hostopt . '/' . $hostopt . '-hosts', 'a+')) {
				exit("Failed to open file ($hostopt)-hosts for writing!");
			} else {
			        fwrite($hostfile, "\$TTL\t86400\n");
                                fwrite($hostfile, "@	IN	SOA	" . $hostopt . ".	hostmaster.eddinn.net. (\n");
                                fwrite($hostfile, "			" . $date . "00	; serial, creation date + todays serial\n");
                                fwrite($hostfile, "			28800		; Refresh\n");
                                fwrite($hostfile, "			14400		; Retry\n");
                                fwrite($hostfile, "			3600000		; Expire\n");
                                fwrite($hostfile, "			86400		; Minimum\n");
                                fwrite($hostfile, ");\n");
                                fwrite($hostfile, "		IN	NS		" . $hostopt . ".	; Inet Address of name server\n");
                                fwrite($hostfile, "		IN	MX	10	mail."  . $hostopt . ".	; Primary Mail Exchanger\n");
                                fwrite($hostfile, ";\n");
                                fwrite($hostfile, $hostopt . ".	IN	A	" . $ip . "\n");
                                fwrite($hostfile, "www		IN	CNAME	" . $hostopt . ".\n");
                                fwrite($hostfile, "mail		IN	A	" . $mxadd . "\n");
				fclose($hostfile); }
			if (!symlink($basedir . $hostopt, $nameddir . $hostopt)) {
				exit("Failed to create symlink for ($hostopt) in /var/named/!"); }
			if (!$namedconf = fopen($namedconffile, 'a+')) {
                                exit("Failed to open file ($namedconffile) for writing!");
                        } else {
				fwrite($namedconf, "\n//###### " . $hostopt . " created " . $date . "\n");
                                fwrite($namedconf, "zone \"" . $hostopt . "\" IN {\n");
                                fwrite($namedconf, "	type master;\n");
				fwrite($namedconf, "	file \"" . $nameddir . $hostopt . "/" . $hostopt . "-hosts\";\n");
				fwrite($namedconf, "	allow-update { none; };\n");
				fwrite($namedconf, "};\n");
                                fclose($namedconf); }
			echo "<p style=\"font-family:arial;font-size:10pt;\"><strong>DNS records for $hostopt </strong></p><pre>";
			system("$rndcexec $rndcreconf && $rndcexec $rndcreload $hostopt");
			system("$digexec $hostopt");
                        echo '<br />';
			echo '</pre><br /><form action="dnstool.php" method="post">';
                        echo '<input type="submit" name="Reset" value="Reset" style="font-family:arial;font-size:10pt;border:1px solid #888888;margin:4px 0px 4px 0px;" /></form>'; } ?>
	</div>
	<div style="width:500px;height:auto;margin:500px 0px 0px 4px;padding:0px 0px 0px 4px;">
		<p style="font-family:arial;font-size:10pt">&copy; <?php echo"$copydate - $author"; ?><br /><br />
		<a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10-blue" alt="Valid XHTML 1.0 Strict" height="31" width="88" style="border:0px;" /></a>
<a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Valid CSS!" />
</a></p>
	</div>
</body>
</html>
