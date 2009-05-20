<?php

ob_start();
include('inc/config.php'); // MySQL Einstellungen werden hier drin vorgenommen
include('inc/mysqlclass.php');
$db = new db();

include 'tpl/header.tpl.php';

/*
GET Abfrage fürs hinzufügen eines neuen Links
*/
if(isset($_GET['add'])) {
	if(isset($_POST)) {
		$url = mysql_real_escape_string($_POST['url']);

		// Serverurl holen
		if(dirname($_SERVER['REQUEST_URI']) == "/")
			$serverurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
		else
			$serverurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/";
				
		$db->query('SELECT linkID, linkURL FROM `su_urls` WHERE linkURL = "'.$url.'"');
		
		if($db->numRows() > 0) {
			$db->fetch();
			$linkID = $db->row('linkID');
		} else {
			if(ereg("((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)" , $url) && $linkID != "http://" && eregi($serverurl, $url) == FALSE) {
				$db->query('INSERT INTO `su_urls` SET linkURL = "'.$url. '"');
				$linkID = $db->insertID();
				$error = FALSE;
			} else {
				error('URL nicht valide');
				$error = TRUE;
			}
		}	
		
		if(!$error) : include 'tpl/output.tpl.php'; endif;
			
	}
	
/*
Eigentliche GET Abfrage für den Aufruf eines Links.
Durch .htaccess verschleiert

http://example.org/1337 => http://example.org/?link=1337
*/
} elseif(isset($_GET['link'])) {
	$linkID = (int)$_GET['link'];
	
	$db->query('SELECT linkURL FROM `su_urls` WHERE linkID = '.$linkID);

	if($db->numRows() > 0) {
		$db->fetch();
		$linkURL = $db->row('linkURL');
		
		if(isset($_GET['p'])) {
			include 'tpl/prev.tpl.php';
		} else {
			header('Location: '.$linkURL);
		}
	} else {
		error('linkID nicht vorhanden');
	}

/*
Ansonsten: Eingabeformular aufrufen
*/	
} else {
	include 'tpl/formular.tpl.php';
}

// Url Count berechnen
$db->query('SELECT COUNT(*) as cnt FROM `su_urls`');
$db->fetch();
$urlcount = $db->row('cnt');

include 'tpl/footer.tpl.php';

function error($msg){
	echo '<div id="error">'.$msg.'</div>';
}

?>
