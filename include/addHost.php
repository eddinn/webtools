<?php
$baseDir = "/var/named/chroot/var/named/";
$date = date("dmY");
$digExec = escapeshellcmd("/usr/bin/dig");
$domainOpt = $_POST["domain"];
$hostOpt = $_POST["host"];
$ipAdd = $_POST["ip"];
$joinArray = array($hostOpt, $domainOpt);
$joinedArray = implode(".", $joinArray);
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReload = escapeshellarg("reload");
$validIP = '/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/';
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$mailBody = $date . " Added hostname " . $hostOpt . " with IP " . $ipAdd . " to " .$domainOpt;
$subject = "New hostname ".$hostOpt;
$header = "From: ". $name . " <" . $email . ">\r\n";
if (preg_match($validIP, $ipAdd)) {
	$ipAdd = $_POST["ip"];
} elseif ($ipAdd === '') {
	exit("ERROR: IP can't be blank!");
} else {
	exit("ERROR: ($ipAdd) is not a valid IP Address!");
}
if (preg_match($validIP, $ipAdd)) {
	$ipAdd = $_POST["ip"];
} else {
	exit("ERROR: ($ipAdd) is not a valid IP Address!");
}
$searchIpArray = array($ipAdd => 0, '' => 1);
if (!array_key_exists($ipAdd,$searchIpArray)) {
	exit("ERROR: ($ipAdd) is not a valid IP Address!");
}
chdir ($baseDir);
if (!$hostFile = fopen($domainOpt . '/' . $domainOpt . '-hosts', 'a+')) {
	exit("Failed to open file ($domainOpt)-hosts for writing!");
} else {
	fwrite($hostFile, $hostOpt . "		IN	A	" . $ipAdd . "\n");
	fclose($hostFile);
	if (!$logFile = fopen($logDir . $logFile, 'a+')) {
                exit("Failed to open file $logFile for writing!");
        } else {
                fwrite($logFile, $date . " Added hostname " . $hostOpt . " with IP " . $ipAdd . " to " .$domainOpt . "\n");
                fclose($logFile);
        }
	mail($recipient, $subject, $mailBody, $header);
	echo "<p><strong>DNS records for $joinedArray </strong></p><pre>";
	system("$rndcExec $rndcReload $domainOpt");
	system("$digExec @localhost $joinedArray");
	echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
}
?>
