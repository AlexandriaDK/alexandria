<?php
function skinkify ($regs) {
	$content = $regs[1];
	$words = preg_split('/\s+/',$regs[2]);
	for($i=0;$i<count($words);$i++) {
		$word = $words[$i];
		if (rand(1,6) == 1 && preg_match('/^[A-Za-zÆØÅæøåé0-9]+$/',$word) && strlen($word) > 3) {
			if ($word == strtoupper($word)) $content .= "SKINKE"; 
			elseif ($word == strtolower($word)) $content .= "skinke";
			else $content .= "Skinke";
		} else {
			$content .= $word;
		}
		if ($i < count($words)-1) $content .= " ";
	}
	return $content;
}

function skinke ($content) {
	$content = preg_replace('/(<head[^>]*>)/i',"$1\n\t\t<base href=\"$url\" />",$content);
	if (preg_match('/<body[^>]*>(.*)/s',$content,$regs) ) {
		$body = preg_replace_callback('/(<[^>]*>)([^<]*)/','skinkify',$regs[1]);
	#		$body = preg_replace_callback('/(<[^>]*>)([^<]*)/','skinkify',$regs[2]);
	#	$body = "Hest";	
		$content = preg_replace('/(<body[^>]*>)(.*)/s','$1'.$body,$content);
	}
	print $content;
}
?>
