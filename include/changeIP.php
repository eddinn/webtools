<?php
$baseDir = "/var/named/chroot/var/named/";
$date = date("dmY");
$digExec = "/usr/bin/dig";
$domainOpt = $_POST["domain"];
$hostOpt = $_POST["host"];
$ipAdd = $_POST["ip"];
$joinArray = array($hostOpt, $domainOpt);
$joinedArray = implode(".", $joinArray);
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReload = escapeshellarg("reload");
$ttlOpt = $_POST["ttl"];
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$header = "From: " . $name . " <" . $email . ">\n";
$mailBody = $date . " IP address for " . $hostOpt . " changed to " . $ipAdd . " with TTL " . $ttlOpt;
$subject = "IP address changed for " . $hostOpt;
chdir ($baseDir);
	if (!$oldFileHandler = fopen($domainOpt . '/' . $domainOpt . '-hosts', "r")) {
	        exit("Failed to open file $domainOpt-hosts for reading!");
	} else {
	        $newFileContents = "";
	        while (!feof($oldFileHandler)) {
			$newFileContents .= preg_replace("/^" . $hostOpt . "(\s+IN|\s+[0-9]+\s+IN)\s+A\s+[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/",$hostOpt . "\t" . $ttlOpt . "\tIN\tA\t" . $ipAdd . "\t;IP breytt " . $date, fgets($oldFileHandler, 4096));
	        }
	        fclose($oldFileHandler);
	        if (!$hostFile = fopen($domainOpt . '/' . $domainOpt . '-hosts', 'w+')) {
	                exit("Failed to open file $domainOpt-hosts for writing!");
	        } else {
	                fwrite($hostFile, $newFileContents);
	                fclose($hostFile);
                        if (!$logFile = fopen($logDir . $logFile, 'a+')) {
	                        exit("Failed to open file $logFile for writing!");
        		} else {
                        	fwrite($logFile, $date . " IP address for " . $hostOpt . " changed to " . $ipAdd . " with TTL " . $ttlOpt . "\n");
                                fclose($logFile);
                        }
			mail($recipient, $subject, $mailBody, $header);
	                system("$rndcExec $rndcReload $domainOpt");
			echo "<p><strong>Changed IP address for $joinedArray</strong></p><pre>";
			system("$digExec @localhost $joinedArray");
	                echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
	        }
	}
?>
