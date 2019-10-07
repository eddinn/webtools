<?php
$whoisExec = escapeshellcmd("/usr/bin/whois");
$whoisOpt = escapeshellarg($_POST["whois"]);
echo "<p><strong>Whois record for $whoisOpt</strong></p><pre>";
system("$whoisExec $whoisOpt");
echo '</pre><br /><br /><form action="?action=dns" method="post"><input type="submit" name="Reset" value="Reset" /></form>';
?>
