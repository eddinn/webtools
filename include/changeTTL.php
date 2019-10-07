<?php
$baseDir = "/var/named/chroot/var/named/";
$date = date("dmY");
$digExec = "/usr/bin/dig";
$domainOpt1 = $_POST["domain1"];
$domainOpt2 = $_POST["domain2"];
$hostOpt = $_POST["host"];
$joinArray = array($hostOpt, $domainOpt2);
$joinedArray = implode(".", $joinArray);
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReload = escapeshellarg("reload");
$ttlOpt1 = $_POST["ttl1"];
$ttlOpt2 = $_POST["ttl2"];
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$header = "From: ". $name . " <" . $email . ">\r\n";
if (isset($_REQUEST ['selectDomainHost'])) {
	switch ($_REQUEST['selectDomainHost']) {
        	case 'domainTTL':
			chdir ($baseDir);
			if (!$oldFileHandler = fopen($domainOpt1 . '/' . $domainOpt1 . '-hosts', "r")) {
			        exit("Failed to open file $domainOpt1-hosts for reading!");
			} else {
			        $newFileContents = "";
			        while (!feof($oldFileHandler)) {
			                $newFileContents .= preg_replace("/TTL\s+[0-9]+\s+/","TTL\t".$ttlOpt1."\n", fgets($oldFileHandler, 4096));
			        }
			        fclose($oldFileHandler);
			        if (!$hostFile = fopen($domainOpt1 . '/' . $domainOpt1 . '-hosts', 'w+')) {
			                exit("Failed to open file $domainOpt1-hosts for writing!");
			        } else {
			                fwrite($hostFile, $newFileContents);
			                fclose($hostFile);
                                        if (!$logFile = fopen($logDir . $logFile, 'a+')) {
                                                exit("Failed to open file $logFile for writing!");
                                        } else {
                                                fwrite($logFile, $date . " TTL changed to " . $ttlOpt1 . " for " . $domainOpt1 . "\n");
                                                fclose($logFile);
                                        }
					$mailBody = $date . " TTL changed to " . $ttlOpt1 . " for " . $domainOpt1;
					$subject = "TTL changed for ".$domainOpt1;
					mail($recipient, $subject, $mailBody, $header);
			                system("$rndcExec $rndcReload $domainOpt1");
			                echo "<p><strong>Changed TTL for $domainOpt1</strong></p><pre>";
			                system("$digExec @localhost $domainOpt1");
			                echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
			        }
			}
                	break;
                case 'hostTTL':
			chdir ($baseDir);
                        if (!$oldFileHandler = fopen($domainOpt2 . '/' . $domainOpt2 . '-hosts', "r")) {
                                exit("Failed to open file $domainOpt2-hosts for reading!");
                        } else {
                                $newFileContents = "";
                                while (!feof($oldFileHandler)) {
	                                $newFileContents .= preg_replace("/^".$hostOpt."(\s+IN|\s+[0-9]+\s+IN)/",$hostOpt."\t".$ttlOpt2."\tIN", fgets($oldFileHandler, 4096));
				}
                                fclose($oldFileHandler);
                                if (!$hostFile = fopen($domainOpt2 . '/' . $domainOpt2 . '-hosts', 'w+')) {
                                        exit("Failed to open file $domainOpt2-hosts for writing!");
                                } else {
                                        fwrite($hostFile, $newFileContents);
                                        fclose($hostFile);
					if (!$logFile = fopen($logDir . $logFile, 'a+')) {
                				exit("Failed to open file $logFile for writing!");
				        } else {
				                fwrite($logFile, $date . " TTL changed to " . $ttlOpt2 . " for " . $joinedArray . "\n");
				                fclose($logFile);
				        }
					$mailBody = $date . " TTL changed to " . $ttlOpt2 . " for " . $joinedArray;
					$subject = "TTL changed for ".$joinedArray;
					mail($recipient, $subject, $mailBody, $header);
                                        system("$rndcExec $rndcReload $domainOpt2");
                                        echo "<p><strong>Changed TTL for $joinedArray</strong></p><pre>";
                                        system("$digExec @localhost $joinedArray");
                                        echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
                                }
                        }
                        break;
        }
}
?>
