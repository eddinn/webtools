<div id="nmapContainer">
<div id="notesNmap">
                <h4>nMap scan</h4>
                <p>Enter a TLD name, hostname or an valid IP address to scan.<br />For example;<br /><strong>&nbsp;&nbsp;example.com<br />&nbsp;&nbsp;myhostname.example.com<br />&nbsp;&nbsp;66.249.93.104</strong></p>
                <p>Select scan options from the selectbox on the left.<br /><strong>Note:</strong> You can combine both the Basic and Additional options sets.</p>
        </div>
<p id="nmapActions">
<?php
	$version = "2.8";
	echo"<strong>Basic nMap - version $version</strong><br /><br />";
?>
<form action="?action=nmap&result" method="post">
<input type="text" name="host" value="<?php print(((isset($_POST['host']) ? $_POST['host'] : ""))); ?>" size="30" />&nbsp; Address<br />
<select name="basic">
	<option value="">Basic options</option>
	<option value="-P0">-P0: No Ping</option>
	<option value="-sP">-sP: Ping</option>
	<option value="-sL">-sL: List</option>
</select>
<select name="adv">
	<option value="">Additional options</option>
	<option value="-sU">-sU: UDP</option>
	<option value="-sN">-sN: TCP Null</option>
	<option value="-sT">-sT: TCP Connect</option>
</select><br /><br />
<input type="submit" name="submit" value="Submit" />
<br /><br /><br /><br /><br /><br /><br />
</p>
</form>
<?php
if (isset($_REQUEST["submit"]) == "submit") {
	$nmapExec = escapeshellcmd($nmapexec = "/usr/bin/nmap");
	$hostOpt = escapeshellarg($_POST["host"]);
	$basicOpt = escapeshellarg($_POST["basic"]);
	$advOpt = escapeshellarg($_POST["adv"]);
	$searchBasArray = array("'-P0'" => 0, "'-sP'" => 1, "'-sL'" => 2, "" => 3);
	$searchAdvArray = array("'-sU'" => 0, "'-sN'" => 1, "'-sT'" => 2, "" => 3);
	if (!array_key_exists($basicOpt,$searchBasArray)) {
		exit("ERROR: You are not allowed to use ($basicOpt)!"); }
	if (!array_key_exists($advOpt,$searchAdvArray)) {
		exit("ERROR: You are not allowed to use ($advOpt)!"); }
	echo "<p><strong>nMap resaults for $hostOpt </strong></p><pre>";
	system("$nmapExec $basicOpt $advOpt $hostOpt 2>&1");
	echo '</pre><br /><form action="?action=nmap" method="post">';
	echo '<input type="submit" name="Reset" value="Reset" /></form>'; }
?>
</div>
