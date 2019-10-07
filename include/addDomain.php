<?php
$baseDir = "/var/named/chroot/var/named/";
$date = date("dmY");
$digExec = escapeshellcmd("/usr/bin/dig");
$namedConfFile = "/etc/named.conf";
$namedDir = "/var/named/";
$domainOpt = $_POST["domain"];
$ipAdd = $_POST["ip"];
$logDir = "/var/log/";
$logFile = "webdnslog.txt";
$mxAdd = $_POST["mxForm"];
$rndcExec = escapeshellcmd("/usr/sbin/rndc");
$rndcReconf = escapeshellarg("reconfig");
$rndcReload = escapeshellarg("reload");
$validIP = '/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/';
$name = "hostmaster@eddinn.net";
$email = "hostmaster@eddinn.net";
$recipient = "hostmaster@eddinn.net";
$mailBody = $date . " Created a new domain " . $domainOpt;
$subject = "New domain ".$domainOpt;
$header = "From: ". $name . " <" . $email . ">\r\n";
if (preg_match($validIP, $mxAdd)) {
	$mxAdd = $_POST["mxForm"];
} elseif ($mxAdd === '') {
	exit("ERROR: MX can't be blank!");
} else {
	exit("ERROR: ($mxAdd) is not a valid IP Address!");
}
if (preg_match($validIP, $ipAdd)) {
	$ipAdd = $_POST["ip"];
} else {
	exit("ERROR: ($ip) is not a valid IP Address!");
}
$search_mx_array = array('82.221.35.186' => 0, $mxAdd => 1, '' => 2);
if (!array_key_exists($mxAdd,$search_mx_array)) {
	exit("ERROR: ($mxAdd) is not a valid IP Address!");
}
chdir ($baseDir);
if (!file_exists($domainOpt)) {
	if (!mkdir($domainOpt, 0775)) {
		exit("Failed to create directory ($domainOpt)!");
	}
	if (!$hostFile = fopen($domainOpt . '/' . $domainOpt . '-hosts', 'a+')) {
		exit("Failed to open file ($domainOpt)-hosts for writing!");
	} else {
		fwrite($hostFile, "\$TTL\t86400\n");
		fwrite($hostFile, "@	IN	SOA	" . $domainOpt . ".	hostmaster.eddinn.net. (\n");
		fwrite($hostFile, "			" . $date . "00 ; serial, creation date + todays serial\n");
		fwrite($hostFile, "			28800		; Refresh\n");
		fwrite($hostFile, "			14400		; Retry\n");
		fwrite($hostFile, "			3600000		; Expire\n");
		fwrite($hostFile, "			86400		; Minimum\n");
		fwrite($hostFile, ");\n");
		fwrite($hostFile, "		IN	NS		" . $domainOpt . ".	; Inet Address of name server\n");
		fwrite($hostFile, "		IN	MX	10	mail."  . $domainOpt . ".	; Primary Mail Exchanger\n");
		fwrite($hostFile, ";\n");
		fwrite($hostFile, $domainOpt . ".	IN	A	" . $ipAdd . "\n");
		fwrite($hostFile, "www		IN	CNAME	" . $domainOpt . ".\n");
		fwrite($hostFile, "mail		IN	A	" . $mxAdd . "\n");
		fclose($hostFile);
	}
	if (!symlink($baseDir . $domainOpt, $namedDir . $domainOpt)) {
		exit("Failed to create symlink for ($domainOpt) in /var/named/!");
	}
	if (!$namedConf = fopen($namedConfFile, 'a+')) {
		exit("Failed to open file ($namedConfFile) for writing!");
	} else {
		fwrite($namedConf, "\n//###### " . $domainOpt . " created " . $date . "\n");
		fwrite($namedConf, "zone \"" . $domainOpt . "\" IN {\n");
		fwrite($namedConf, "	type master;\n");
		fwrite($namedConf, "	file \"" . $namedDir . $domainOpt . "/" . $domainOpt . "-hosts\";\n");
		fwrite($namedConf, "	allow-update { none; };\n");
		fwrite($namedConf, "};\n");
		fclose($namedConf);
		if (!$logFile = fopen($logDir . $logFile, 'a+')) {
	                exit("Failed to open file $logFile for writing!");
        	} else {
                	fwrite($logFile, $date . " Created a new domain " . $domainOpt . "\n");
                	fclose($logFile);
        	}
		mail($recipient, $subject, $mailBody, $header);
		echo "<p><strong>DNS records for $domainOpt </strong></p><pre>";
		system("$rndcExec $rndcReconf && $rndcExec $rndcReload $domainOpt");
		system("$digExec @localhost $domainOpt");
		echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
	}
} else {
        if (!$namedConf = fopen($namedConfFile, 'a+')) {
		exit("Failed to open file ($namedConfFile) for writing!");
        } else {
                fwrite($namedConf, "\n//###### " . $domainOpt . " created " . $date . "\n");
                fwrite($namedConf, "zone \"" . $domainOpt . "\" IN {\n");
                fwrite($namedConf, "    type master;\n");
                fwrite($namedConf, "    file \"" . $namedDir . $domainOpt . "/" . $domainOpt . "-hosts\";\n");
                fwrite($namedConf, "    allow-update { none; };\n");
                fwrite($namedConf, "};\n");
                fclose($namedConf);
                if (!$logFile = fopen($logDir . $logFile, 'a+')) {
                        exit("Failed to open file $logFile for writing!");
                } else {
                        fwrite($logFile, $date . " Created a new domain " . $domainOpt . "\n");
                        fclose($logFile);
                }
                mail($recipient, $subject, $mailBody, $header);
                echo "<p><strong>DNS records for $domainOpt </strong></p><pre>";
                system("$rndcExec $rndcReconf && $rndcExec $rndcReload $domainOpt");
                system("$digExec @localhost $domainOpt");
                echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
        }
}
?>
