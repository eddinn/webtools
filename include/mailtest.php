<?php
//email form
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$mailBody = $date . " Added CNAME " . $hostOpt . " for " . $cnameOpt . " to " .$domainOpt;
$subject = "New CNAME host ".$hostOpt;
$header = "From: ". $name . " <" . $email . ">\r\n";
mail($recipient, $subject, $mailBody, $header);

//logger
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
if (!$logFile = fopen($logDir . $logFile, 'a+')) {
                exit("Failed to open file $logFile for writing!");
        } else {
                fwrite($logFile, $date . " Added CNAME " . $hostOpt . " for " . $cnameOpt . " to " .$domainOpt . "\n");
                fclose($logFile);
        }
?>
