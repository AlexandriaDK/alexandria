<?php
require "adm.inc";
if ($con = intval($_REQUEST['con'])) {
	SetCookie("conlock",$con);
} else {
	SetCookie("conlock");
}


header("Location: convent.php?con=$con");

?>
