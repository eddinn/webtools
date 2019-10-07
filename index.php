<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php	$version = "2.0";
	$date = date("Y");
	$author = "Edvin Dunaway";
	$email = "edvin[at]eddinn.net";
	$webpage = "eddinn.net";
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>Basic Webtools</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="styleie.css" media="screen" />
<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	</head>
<body>
<div class="wrap" id="wrap">
<div id="header">
</div>
	<div id="sidebarLeft">
		<br />
		<ul>
		<li><a href="?action=dns">DNS</a></li>
                <li><a href="?action=mrtg">MRTG</a></li>
                <li><a href="?action=networkmap">Network</a></li>
		<li><a href="?action=nmap">nMap</a></li>
		<li><a href="?action=web">Vefur</a><br /><br /></li>
		<li><a href="?action=home">Heim</a></li>
		</ul>
		<div id="credit">
			<p>
			&copy; <?php echo"$date"; ?><br />
			<?php echo"$author"; ?><br />
			<a href="mailto:edvin[at]eddinn.net"><?php echo"$email"; ?></a><br />
			<a href="http://www.eddinn.net/"><?php echo"$webpage"; ?></a><br />
			</p>
		</div>
	</div> 
	<div id="mainBody">
		<?php if (isset($_REQUEST['action'])) {
			switch ($_REQUEST['action']) {
				case 'dns':
					include("include/dns.php");
					break;
				case 'home':
					include("include/body.php");
					break;
                                case 'mrtg':
                                        include("");
                                        break;
                                case 'networkmap':
                                        include("include/networkmap.php");
                                        break;
				case 'nmap':
					include("include/nmap.php");
					break;
				case 'web':
					include("include/web.php");
					break;
				default:
					include("include/body.php");
					break;
				}
			} else {
				include("include/body.php"); 
			}
		?>
	</div>
</div>
</body>
</html>
