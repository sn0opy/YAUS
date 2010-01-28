<?php

ob_start();
include('inc/config.php'); # SQL SETTINGS!
include('inc/mysqlclass.php');
include('inc/functions.inc.php');
$db = new db();

include 'tpl/header.tpl.php';

# Request for adding a new Link
if(isset($_GET['add'])) {
	if(isset($_POST)) {
		$url = mysql_real_escape_string($_POST['url']);

		// Get server url
		$server = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
		$serverurl = (dirname($_SERVER['REQUEST_URI']) == "/") ? $server : $server."/";
				
		$db->query('SELECT linkID, linkURL FROM `su_urls` WHERE linkURL = "'.$url.'"');		
		if($db->numRows() > 0) {
			$db->fetch();
			$linkID = $db->row('linkID');
		} else {
			if(ereg("((https?|ftp):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)" , $url) && $linkID != "http://" && eregi($serverurl, $url) == FALSE) {
				$db->query('INSERT INTO `su_urls` SET linkURL = "'.$url. '"');
				$linkID = base36($db->insertID(), true); # converting linkID to a base36 decoded hash
				$error = false;
			} else {
				error('URL nicht valide');
				$error = true;
			}
		}			
		if(!$error) : include 'tpl/output.tpl.php'; endif;			
	}
	

# real GET request. This will be hidden by .htaccess 
# http://example.org/abc12 => http://example.org/?link=abc12
} elseif(isset($_GET['link'])) {
	$linkID = base36($_GET['link'], false); # decoding link from base36 to decimal
	
	$db->query('SELECT linkURL FROM `su_urls` WHERE linkID = '.$db->escape($linkID));

	if($db->numRows() > 0) {
		$db->fetch();
		$linkURL = $db->row('linkURL');
		
		if(substr($_SERVER['QUERY_STRING'], -1) == '+') {
			include 'tpl/prev.tpl.php';
		} else {
			header('Location: '.$linkURL); # redirect to url
		}
	} else {
		error('linkID nicht vorhanden');
	}
} else {
	include 'tpl/formular.tpl.php';
}

// count entries / urls
$db->query('SELECT COUNT(*) as cnt FROM `su_urls`');
$db->fetch();
$urlcount = $db->row('cnt');

include 'tpl/footer.tpl.php';

?>
