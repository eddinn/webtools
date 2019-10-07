<?php
$digExec = escapeshellcmd("/usr/bin/dig");
$lookupOpt = escapeshellarg($_POST["lookup"]);
$digTypeOpt = escapeshellarg($_POST["digType"]);
$searchTypeArray = array("'any'" => 0, "'axfr'" => 1, "'hinfo'" => 2, "'mx'" => 3, "'ns'" => 4, "'ptr'" => 5, "'soa'" => 6, "'txt'" => 7, "'-x'" => 8, "" => 9);
if (!array_key_exists($digTypeOpt,$searchTypeArray)) {
	exit("ERROR: You are not allowed to use ($digTypeOpt)!"); }
echo "<p><strong>DNS records for $lookupOpt </strong></p><pre>";
system("$digExec @localhost $digTypeOpt $lookupOpt");
echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
?>
