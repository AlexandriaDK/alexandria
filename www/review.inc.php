<?php
$this_type = 'review';

$r = getrow( "
	SELECT data_id, category, title, description, spoilertext, relation, user_id, reviewer, syndicatedurl, language
	FROM reviews
	WHERE id = $review
	AND visible = 1
" );

if ( ! $r ) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}

// hardcoded to use game, not other categories
$target_title = getone( "SELECT title FROM sce WHERE id = " . $r['data_id'] );
$target_link = getdatalink( $r['category'], $r['data_id'] );
$target_html = getdatahtml( $r['category'], $r['data_id'], $target_title );

if ( ! $r['reviewer'] ) {
	$r['reviewer'] = $t->getTemplateVars('_unknown');
}

$t->assign('type',$this_type);
$t->assign('target_title',$target_title);
$t->assign('target_link',$target_link);
$t->assign('target_html',$target_html);
$t->assign('review',$r);

$t->display('data.tpl');
?>
