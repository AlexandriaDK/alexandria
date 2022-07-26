<?php
require "adm.inc.php";
if ($con = intval($_REQUEST['con'])) {
	SetCookie("conlock",$con);
} else {
	SetCookie("conlock");
}


header("Location: convention.php?con=$con");
