<?php
$baseDir = "/var/named/chroot/var/named/";
$date = date("dmY");
$domainOpt = $_POST["domain"];
$hostOpt = $_POST["host"];
$joinArray = array($hostOpt, $domainOpt);
$joinedArray = implode(".", $joinArray);
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReconf = escapeshellarg("reconfig");
$rndcReload = escapeshellarg("reload");
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$mailBody = $date . " Deleted hostname " . $joinedArray . " from " .$domainOpt;
$subject = "Deleted hostname ".$joinedArray;
$header = "From: ". $name . " <" . $email . ">\r\n";
chdir ($baseDir);
if (!$oldFileHandler = fopen($domainOpt . '/' . $domainOpt . '-hosts', "r")) {
	exit("Failed to read file ($domainOpt)-hosts!");
} else {
	$newFileContents = "";
	while (!feof($oldFileHandler)) {
		$newFileContents .= preg_replace("/^".$hostOpt."\s+/",";".$hostOpt."\t", fgets($oldFileHandler, 4096));
	}
	fclose($oldFileHandler);
	if (!$hostFile = fopen($domainOpt . '/' . $domainOpt . '-hosts', 'w+')) {
		exit("Failed to open file ($domainOpt)-hosts for writing!");
	} else {
		fwrite($hostFile, $newFileContents);
		fclose($hostFile);
		if (!$logFile = fopen($logDir . $logFile, 'a+')) {
	                exit("Failed to open file $logFile for writing!");
        	} else {
                	fwrite($logFile, $date . " Deleted hostname " . $joinedArray . " from " .$domainOpt . "\n");
                	fclose($logFile);
        	}
		mail($recipient, $subject, $mailBody, $header);
		system("$rndcExec $rndcReconf && $rndcExec $rndcReload $domainOpt");
		echo "<p><strong>Host record $joinedArray deleted</strong></p>";
		echo '<br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
	}
}
?>
