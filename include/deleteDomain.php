<?php
$axfr = null;
$date = date("dmY");
$digExec = escapeshellcmd("/usr/bin/dig");
$domainOpt = $_POST["domain"];
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$namedConfFile = "/etc/named.conf";
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReconf = escapeshellarg("reconfig");
$rndcReload = escapeshellarg("reload");
exec("$digExec @localhost $domainOpt axfr", $axfr);
$axfrOut = implode("\n", $axfr);
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$mailBody = $date . " Deleted domain " . $domainOpt . "\n" . $axfrOut;
$subject = "Deleted domain ".$domainOpt;
$header = "From: ". $name . " <" . $email . ">\r\n";
if (!$namedconf = fopen($namedConfFile, 'r')) {
	exit("Failed to open file ($namedConfFile) for writing!");
} else {
	$reachedZone = 0;
	$inFile = "";
	while(!feof($namedconf)) {
		$line = fgets($namedconf, 4096);
		if (trim($line) == "zone \"".$domainOpt."\" IN {" || $reachedZone == 1) {
			if ($reachedZone == 0) {
				$inFile .= "//###### " . $domainOpt . " deleted " . $date . "\n";
				$reachedZone = 1;
			}
			$inFile .= "//".$line;
			if (trim($line) == "};") {
				$reachedZone = 0;
			}
		} else {
			$inFile .= $line;
		}
	}
	fclose($namedconf);
	$outFile = fopen($namedConfFile, "w");
	fwrite($outFile, $inFile);
	fclose($outFile);
	if (!$logFile = fopen($logDir . $logFile, 'a+')) {
                exit("Failed to open file $logFile for writing!");
        } else {
                fwrite($logFile, $date . " Deleted domain " . $domainOpt . "\n");
                fclose($logFile);
        }
	mail($recipient, $subject, $mailBody, $header);
	echo "<p><strong>Deleted records for $domainOpt </strong></p><pre>";
	system("$rndcExec $rndcReconf && $rndcExec $rndcReload $domainOpt");
echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
}
?>
