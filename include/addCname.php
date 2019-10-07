<?php
$baseDir = "/var/named/chroot/var/named/";
$date = date("dmY");
$digExec = escapeshellcmd("/usr/bin/dig");
$cnameOpt = $_POST["cnamehost"];
$domainOpt = $_POST["domain"];
$hostOpt = $_POST["host"];
$joinArray = array($hostOpt, $domainOpt);
$joinedArray = implode(".", $joinArray);
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReload = escapeshellarg("reload");
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$mailBody = $date . " Added CNAME " . $hostOpt . " for " . $cnameOpt . " to " .$domainOpt;
$subject = "New CNAME host ".$hostOpt;
$header = "From: ". $name . " <" . $email . ">\r\n";
chdir ($baseDir);
if (!$hostFile = fopen($domainOpt . '/' . $domainOpt . '-hosts', 'a+')) {
	exit("Failed to open file ($domainOpt)-hosts for writing!");
} else {
	fwrite($hostFile, $hostOpt . "		IN	CNAME	" . $cnameOpt . ".\n");
	fclose($hostFile);
	if (!$logFile = fopen($logDir . $logFile, 'a+')) {
        	exit("Failed to open file $logFile for writing!");
	} else {
        	fwrite($logFile, $date . " Added CNAME " . $hostOpt . " for " . $cnameOpt . " to " .$domainOpt . "\n");
        	fclose($logFile);
	}
	echo "<p style=\"font-family:arial;font-size:10pt;\"><strong>DNS records for $joinedArray </strong></p><pre>";
	system("$rndcExec $rndcReload $domainOpt && $digExec @localhost $joinedArray");
	echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
	mail($recipient, $subject, $mailBody, $header);
}
?>
