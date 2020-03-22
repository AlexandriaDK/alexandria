<?php
require("./connect.php");
require("base.inc");

function mailsanitize($string) {
	return str_replace(array("\r","\n"),"",$string);
}

$cat = $_REQUEST['cat'];
$data_id = $_REQUEST['data_id'];
$data_label = $_REQUEST['data_label'];
$data_description = $_REQUEST['data_description'];
$user_name = mailsanitize($_REQUEST['user_name']);
$user_email = mailsanitize($_REQUEST['user_email']);
$user_id = intval($_SESSION['user_id']);
$user_source = $_REQUEST['user_source'];

if ($cat && $data_id) {
	$data_label = getentry($cat,$data_id);
}

$mailoutput = "
$data_description

Kilde: $user_source
";

$output = $mailoutput . "
IP: {$_SERVER['REMOTE_ADDR']}
";

// php mail() prevent spam
if (stristr($cat,"Content-Type") ||
    stristr($data_id,"Content-Type") ||
    stristr($data_label,"Content-Type") ||
    stristr($data_description,"Content-Type") ||
    stristr($user_name,"Content-Type") ||
    stristr($user_email,"Content-Type") ||
    stristr($user_source,"Content-Type")) {

	header("HTTP/1.1 403 Forbidden");    	
	die("Don't send input containing Content-Type");
	exit;
}

if (strtolower(trim($_REQUEST['human'])) != "a") {
	die("Wrong anti-spam code. Type <b>A</b> in the field at the bottom.");
}


$query = "
	INSERT INTO updates (id, data_id, category, title, description, submittime, user_name, user_email, user_id)
	VALUES (NULL, '$data_id', '$cat', '".dbesc($data_label)."', '".dbesc($output)."', NOW(), '".dbesc($user_name)."', '".dbesc($user_email)."', '$user_id' )";
$last_id = doquery($query);

award_achievement(20);

// send en mail med rettelserne
$email = (strstr($user_email,'@') ? $user_email : 'robot@alexandria.dk');
$from = "\"$user_name\" <$email>"; // :TODO: should probably be sanitized
$link = "https://alexandria.dk/adm/ticket.php?id=$last_id";
if ($data_id && $cat) {
	$label = getentry($cat,$data_id);
} else {
	$label = $data_label;
}

$to = "peter@alexandria.dk";
$subject = "[Alexandria] Rettelser (#$last_id)";
$headers = "From: $from\r\nContent-Type: text/plain; charset=\"utf-8\"";
$body = "Alexandria-rettelse:\r\n\r\n$label\r\n".wordwrap(stripslashes($mailoutput))."\r\n\r\n$link";
mail($to,$subject,$body,$headers);

$t->display('update_thanks.tpl');

?>
