<?php
function page_header($title, $error = "", $breadcrumb = array(), $title2 = "") {
	global $LANG, $VERSION, $adminer, $connection;
	header("Content-Type: text/html; charset=utf-8");
	header("X-Frame-Options: deny"); // ClickJacking protection in IE8, Safari 4, Chrome 2, NoScript plugin
	$title_all = $title . (strlen($title2) ? ": " . h($title2) : "");
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="<?php echo $LANG; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<title><?php echo $title_all . (strlen($_GET["server"]) && $_GET["server"] != "localhost" ? h("- $_GET[server]") : "") . " - " . $adminer->name(); ?></title>
<link rel="shortcut icon" type="image/x-icon" href="../adminer/static/favicon.ico">
<link rel="stylesheet" type="text/css" href="../adminer/static/default.css<?php // Ondrej Valka, http://valka.info ?>">
<?php if (file_exists("adminer.css")) { ?>
<link rel="stylesheet" type="text/css" href="adminer.css">
<?php } ?>

<body onload="bodyLoad(<?php echo substr($connection->server_info, 0, 3); ?>);<?php echo (isset($_COOKIE["adminer_version"]) ? "" : " verifyVersion();"); ?>">
<script type="text/javascript" src="../adminer/static/functions.js"></script>
<script type="text/javascript" src="static/editing.js"></script>

<div id="content">
<?php
	if (isset($breadcrumb)) {
		$link = substr(preg_replace('~db=[^&]*&~', '', ME), 0, -1);
		echo '<p id="breadcrumb"><a href="' . (strlen($link) ? h($link) : ".") . '">' . (isset($_GET["server"]) ? h($_GET["server"]) : lang('Server')) . '</a> &raquo; ';
		if (is_array($breadcrumb)) {
			if (strlen(DB)) {
				echo '<a href="' . h(substr(ME, 0, -1)) . '">' . h(DB) . '</a> &raquo; ';
			}
			foreach ($breadcrumb as $key => $val) {
				$desc = (is_array($val) ? $val[1] : $val);
				if (strlen($desc)) {
					echo '<a href="' . h(ME . "$key=") . urlencode(is_array($val) ? $val[0] : $val) . '">' . h($desc) . '</a> &raquo; ';
				}
			}
		}
		echo "$title\n";
	}
	echo "<h2>$title_all</h2>\n";
	restart_session();
	if ($_SESSION["messages"]) {
		echo "<div class='message'>" . implode("</div>\n<div class='message'>", $_SESSION["messages"]) . "</div>\n";
		$_SESSION["messages"] = array();
	}
	if (!$_POST && !isset($_SESSION["passwords"])) { // used in auth
		$_SESSION["passwords"] = array();
	}
	$databases = &$_SESSION["databases"][$_GET["server"]];
	if (strlen(DB) && $databases && !in_array(DB, $databases, true)) {
		$databases = null;
	}
	if ($error) {
		echo "<div class='error'>$error</div>\n";
	}
}

function page_footer($missing = false) {
	global $adminer;
	?>
</div>

<?php switch_lang(); ?>
<div id="menu">
<?php $adminer->navigation($missing); ?>
</div>
<?php
}
