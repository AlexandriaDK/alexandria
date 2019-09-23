<?php
require("./connect.php");
require("base.inc");

function fixttftext($string) {
	return $string; // no need for conversion anymore
	$newstring = "";
	for($i=0;$i<strlen($string);$i++) {
		$char = substr($string,$i,1);
		$ord = ord($char);
		$newstring .= ($ord < 128 ? $char : "&#".$ord.";");
	}
	return $newstring;
}

// data er skiftevis person,scenarie,person,scenarie,..,person
$data = $_SERVER['QUERY_STRING'];
if (!$data) $data = "1,1455,53,174,8,392,122";
$dataset = explode(',',$data);
$chainlen = ceil(count($dataset)/2);

$fontpath = './gfx/arial.ttf';

// Define everything
$space = 70;
$leftmargin = 80;
$w = 250;
$h = ($space*$chainlen);
$radius = 30;
$radiuswidth = $radius*2.5;
$fontsize = 9;

// Define database-part

// Create objects
$im = imagecreate($w,$h);

$white = imagecolorallocate($im,255,255,255);
$black = imagecolorallocate($im,0,0,0);
$red = imagecolorallocate($im,204,0,51);
$yellow = imagecolorallocate($im,204,102,0);

// Draw circles

// circles
for ($i=1;$i<=$chainlen;$i=$i+0.5) {
	if ($i == intval($i)) $x = $leftmargin;
	else $x = ($w-$leftmargin);
	imagefilledellipse($im,$x,(($i - 0.5)*$space),$radiuswidth*2,$radius*2,$white);
	imageellipse($im,$x,(($i - 0.5)*$space),$radiuswidth*2,$radius*2,$black);
}

// names
$i = 0;
foreach($dataset AS $id) {
	$i++;
	$type = ($type=='aut'?'sce':'aut');
	if ($type == 'aut') {
		$label = getentry('aut',$id);
		$color = $red;
	} else {
		$label = getentry('sce',$id);
		$color = $yellow;
	}
	if (mb_strlen($label) > 20) $label = mb_substr($label,0,18)."...";

	list(,,$txtw,,,$txth) = imagettfbbox($fontsize,0, $fontpath, fixttftext($label));
	$txty = abs($txty);

	$x = ($type=='aut'?$leftmargin:($w-$leftmargin)) - ($txtw/2);
	$y = ($i*$space*0.5) - ($txth/2);


	imagettftext($im,$fontsize,0,$x,$y,$color, $fontpath, fixttftext($label));

}

// Border
imagerectangle($im,0,0,$w-1,$h-1,$black);

header("Content-type: image/png");
ImagePNG($im);
exit;

?>
