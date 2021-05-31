<?php
// from this point on we know that the setup is incomplete
define( 'IMPORT_ENDPOINT', 'https://alexandria.dk/en/export' );
define( 'INSTALLATION_DEBUG', FALSE);
$action = $_POST[ 'action' ] ?? '';
if ( $action && ( $_SESSION['token'] !== $_POST['token'] ) ) {
	$t->assign( 'stage', 'tokenerror' );
	$t->assign( 'installation', TRUE );
	$t->assign( 'dbname', DB_NAME ); 
	$t->display( 'installation.tpl' );
	exit;
}

function dbmultiinsert( $table, $allvalues, $fields = NULL ) {
	$replacements = [
		'game_id' => 'sce_id',
		'person_id' => 'aut_id',
		'person_extra' => 'aut_extra',
		'title_id' => 'tit_id',
		'convention_id' => 'convent_id',
		'system_id' => 'sys_id',
		'system_extra' => 'sys_ext',
		'genre_id' => 'gen_id',
		'presentation_id' => 'pre_id',
	];
	if ( $fields == NULL ) {
		$fields = [];
		foreach ( $allvalues[0] AS $key => $list ) {
			if ( $table != 'game_description' ) { // Cheating, converting game_id to sce_id and person_id to aut_id
				if (isset($replacements[$key])) {
					$key = $replacements[$key];
				}
			}
			$fields[] = $key;
		}
	}
	$dataset = $datasets = [];
	foreach( $allvalues AS $list ) {
		$set = [];
		foreach ( $list AS $part ) {
			$set[] = ( is_null( $part ) ? 'NULL' : ( is_numeric($part) ? $part : "'" . dbesc($part) . "'" ) ) ;
		}
		$dataset[] = "(" . implode(", ", $set ) . ")";
		if ( count( $dataset ) >= 1000 ) {
			$datasets[] = $dataset;
			$dataset = [];
		}
	}
	if ( $dataset ) {
		$datasets[] = $dataset;
	}
	
	if ( $datasets ) {
		if (INSTALLATION_DEBUG === TRUE) {
			print "<pre>";
			print "TABLE $table \n";
		}
		doquery( "TRUNCATE TABLE `$table` ");
		foreach ( $datasets AS $dataset ) {
			$multisql = "INSERT INTO `$table` (" . implode( ", ", $fields ) . ") VALUES " . implode( ", ", $dataset );
			doquery( $multisql );
			if (INSTALLATION_DEBUG === TRUE) {
				print htmlspecialchars($multisql) . "\n";
				$error = dberror();
				if ($error) {
					print "\nMySQL error: " . $error . "\n";
				}
			}
		}
		return true;
	} else {
		return false;
	}
}

if ( ! defined( "INSTALLNOW" ) || INSTALLNOW !== TRUE ) { //should not be called directly
	header("HTTP/1.1 403 Forbidden");
	header("X-Error: Setup");

	die("Do not access this file directly. Just visit the front page.");
	exit;
}
header("HTTP/1.1 503 Service Unavailable");
header("X-Error: Setup");
if ( ! isset( $_SESSION['token'] ) ) {
	$_SESSION['token'] = md5( uniqid() );
}
$t->assign('token',$_SESSION['token'] ?? '');

if ( $action == 'importstructure' ) {
	$url = IMPORT_ENDPOINT . '?setup=sqlstructure';
	$sqltables = json_decode( file_get_contents( $url ) );
	if ( ! $sqltables ) {
		$t->assign( 'stage', 'dbsetupnodata' );
	} else {
		foreach ( $sqltables->result AS $table => $sqlstatement ) {
			doquery( "DROP TABLE IF EXISTS `$table`" );
			doquery( $sqlstatement );
		}
		if ( getone( "SHOW tables LIKE 'installation'" ) !== NULL ) {
			doquery( "INSERT INTO `installation` (`key`, `value`) VALUES ('status', 'empty')" );
		}
		header( "Location: ./" );
		exit;
	}
} elseif ( $action == 'populate' ) {
	$url = IMPORT_ENDPOINT;
	$datasets = json_decode( file_get_contents( $url ) );
	foreach ( $datasets->result->datasets AS $dataset => $description ) {
		if ( $dataset == 'all' ) { // Don't fetch all in one result; request individually and skip special case for "all" 
			continue;
		}
		$url = IMPORT_ENDPOINT . "?dataset=" . rawurlencode( $dataset );
		doquery( "DELETE FROM installation WHERE `key` = 'currentdataset'" );
		doquery( "INSERT INTO installation (`key`, `value`) VALUES ('currentdataset', '" . dbesc( $dataset ). "')" );
		$data = json_decode ( file_get_contents( $url ) );

		switch ( $dataset ) {
		case 'persons':
		case 'games':
		case 'conventions':
		case 'conventionsets':
		case 'systems':
		case 'genres':
		case 'tags':
		case 'gametags':
		case 'gameruns':
		case 'gamedescriptions':
		case 'titles':
		case 'presentations':
		case 'feeds':
		case 'trivia':
		case 'links':
		case 'aliases':
		case 'sitetexts':
		case 'files':
		case 'awards':
		case 'award_categories':
		case 'award_nominee_entities':
		case 'award_nominees':
		case 'magazines':
		case 'issues':
		case 'articles':
		case 'contributors':
		case 'person_game_title_relations':
		case 'game_convention_presentation_relations':
		case 'person_convention_relations':
		case 'genre_game_relations':
			$tablemap = [ 'persons' => 'aut', 'conventions' => 'convent', 'conventionsets' => 'conset', 'systems' => 'sys', 'genres' => 'gen', 'gameruns' => 'scerun', 'titles' => 'title', 'presentations' => 'pre', 'aliases' => 'alias', 'sitetexts' => 'weblanguages', 'tags' => 'tag', 'gametags' => 'tags', 'gamedescriptions' => 'game_description', 'magazines' => 'magazine', 'issues' => 'issue', 'articles' => 'article', 'contributors' => 'contributor', 'person_game_title_relations' => 'asrel', 'game_convention_presentation_relations' => 'csrel', 'person_convention_relations' => 'acrel', 'genre_game_relations' => 'gsrel', 'games' => 'sce', 'gametags' => 'tags' ];
			if ( isset( $tablemap[ $dataset ] ) ) {
				$table = $tablemap[ $dataset ];
			} else {
				$table = $dataset;
			}
			dbmultiinsert( $table, $data->result );
			break;
		default:
			print "Unknown table from Alexandria server: $dataset";
			exit;
		}
	}
	doquery( "DELETE FROM installation WHERE `key` = 'status'" );
	doquery( "INSERT INTO installation (`key`, `value`) VALUES ('status', 'ready')" );
	header( "Location: ./" );
	exit;
} elseif ( $action == 'activate' ) {
	doquery( "DELETE FROM installation WHERE `key` = 'status'" );
	doquery( "INSERT INTO installation (`key`, `value`) VALUES ('status', 'live')" );
	header( "Location: ./" );
	exit;
} elseif ( getone( "SHOW tables LIKE 'installation'" ) !== NULL ) {
	if ( getone( "SELECT 1 FROM installation WHERE `key` = 'status' AND `value` = 'empty'" ) )  {
		$t->assign( 'stage', 'populate' );
	} elseif ( getone( "SELECT 1 FROM installation WHERE `key` = 'status' AND `value` = 'ready'" ) )  {
		$t->assign( 'stage', 'ready' );
	}
} else {
	$t->assign( 'stage', 'dbsetup' );
}

$t->assign( 'installation', TRUE );
$t->assign( 'dbname', DB_NAME ); 
$t->display( 'installation.tpl' );

exit;
?>
