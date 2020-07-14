<?php
// from this point on we know that the setup is incomplete
define( 'IMPORT_ENDPOINT', 'https://alexandria.dk/en/export' );
$action = $_POST[ 'action' ] ?? '';
if ( $action && ( $_SESSION['token'] !== $_POST['token'] ) ) {
	$t->assign( 'stage', 'tokenerror' );
	$t->assign( 'installation', TRUE );
	$t->assign( 'dbname', DB_NAME ); 
	$t->display( 'installation.tpl' );
	exit;
}

function dbmultiinsert( $table, $allvalues, $fields = NULL ) {
	if ( $fields == NULL ) {
		$fields = [];
		foreach ( $allvalues[0] AS $key => $list ) {
			$fields[] = $key;
		}
	}
	$dataset = [];
	foreach( $allvalues AS $list ) {
		$set = [];
		foreach ( $list AS $part ) {
			$set[] = ( is_numeric($part) ? $part : "'" . dbesc($part) . "'" ) ;
		}
		$dataset[] = "(" . implode(", ", $set ) . ")";
	}
	if ( $dataset ) {
		$multisql = "INSERT INTO `$table` (" . implode( ", ", $fields ) . ") VALUES " . implode( ", ", $dataset );
		doquery( "TRUNCATE TABLE `$table` ");
		doquery( $multisql );
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
		$url = IMPORT_ENDPOINT . "?dataset=" . rawurlencode( $dataset );
		doquery( "DELETE FROM installation WHERE `key` = 'currentdataset'" );
		doquery( "INSERT INTO installation (`key`, `value`) VALUES ('currentdataset', '" . dbesc( $dataset ). "')" );
		$data = json_decode ( file_get_contents( $url ) );

		switch ( $dataset ) {
		case 'persons':
		case 'conventions':
		case 'conventionsets':
		case 'systems':
		case 'genres':
		case 'tags':
		case 'gameruns':
		case 'gamedescriptions':
		case 'titles':
		case 'presentations':
		case 'feeds':
		case 'trivia':
		case 'links':
		case 'aliases':
		case 'sitetexts':
		case 'awards':
		case 'award_categories':
		case 'award_nominee_entities':
		case 'award_nominees':
			$tablemap = [ 'persons' => 'aut', 'conventions' => 'convent', 'conventionsets' => 'conset', 'systems' => 'sys', 'genres' => 'gen', 'gameruns' => 'scerun', 'titles' => 'title', 'presentations' => 'pre', 'aliases' => 'alias', 'sitetexts' => 'weblanguages', 'tags' => 'tag', 'gametags' => 'tags', 'gamedescriptions' => 'game_description' ];
			if ( isset( $tablemap[ $dataset ] ) ) {
				$table = $tablemap[ $dataset ];
			} else {
				$table = $dataset;
			}
			dbmultiinsert( $table, $data->result );
			break;
		case 'gametags':
			dbmultiinsert( 'tags', $data->result, [ 'id', 'sce_id', 'tag' ] );
			break;
		case 'games':
			dbmultiinsert( 'sce', $data->result, [ 'id', 'title', 'boardgame', 'sys_id', 'sys_ext', 'aut_extra', 'gms_min', 'gms_max', 'players_min', 'players_max', 'participants_extra' ] );
			break;
		case 'genre_game_connections':
			dbmultiinsert( 'gsrel', $data->result, [ 'id', 'gen_id', 'sce_id' ] );
		case 'person_game_title_connections':
			dbmultiinsert( 'asrel', $data->result, [ 'id', 'aut_id', 'sce_id', 'tit_id', 'note' ] );
			break;
		case 'game_convention_title_connections':
			dbmultiinsert( 'csrel', $data->result, [ 'id', 'sce_id', 'convent_id', 'pre_id' ] );
			break;
		case 'person_convention_connections':
			dbmultiinsert( 'acrel', $data->result, [ 'id', 'aut_id', 'convent_id', 'aut_extra', 'role' ] );
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
