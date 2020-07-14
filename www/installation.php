<?php
// from this point on we know that the setup is incomplete
define( 'IMPORT_ENDPOINT', 'https://alexandria.dk/en/export.php' );

function dbmultiinsert( $table, $fields, $allvalues ) {
	$dataset = [];
	foreach( $allvalues AS $person ) {
		$set = [];
		foreach ( $person AS $part ) {
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

if ( ( $_POST['action'] ?? '' ) == 'importstructure' && ( $_SESSION['token'] === $_POST['token'] ) ) {
	$url = IMPORT_ENDPOINT . '?dataset=sqlstructure';
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
} elseif ( ( $_POST['action'] ?? '' ) == 'populate' && ( $_SESSION['token'] === $_POST['token'] ) ) {
	$url = IMPORT_ENDPOINT;
	$datasets = json_decode( file_get_contents( $url ) );
	foreach ( $datasets->result->datasets AS $dataset => $description ) {
		if ( $dataset == 'sqlstructure' ) { // don't import the structure again
			continue;
		}
		$url = IMPORT_ENDPOINT . "?dataset=" . rawurlencode( $dataset );
		doquery( "DELETE FROM installation WHERE `key` = 'currentdataset'" );
		doquery( "INSERT INTO installation (`key`, `value`) VALUES ('currentdataset', '" . dbesc( $dataset ). "')" );
		$data = json_decode ( file_get_contents( $url ) );

		switch ( $dataset ) {
		case 'persons':
			dbmultiinsert( 'aut', [ 'id', 'firstname', 'surname' ], $data->result );
			break;
		case 'games':
			dbmultiinsert( 'sce', [ 'id', 'title', 'boardgame', 'sys_id', 'sys_extra', 'aut_extra', 'gms_min', 'gms_max', 'players_min', 'players_max', 'participants_extra' ], $data->result );
			break;
		default:
			exit;
		}

	}
	exit;
} elseif ( getone( "SHOW tables LIKE 'installation'" ) !== NULL && getone( "SELECT 1 FROM installation WHERE `key` = 'status' AND `value` = 'empty'" ) )  {
	$t->assign( 'stage', 'populate' );
} else {
	$t->assign( 'stage', 'dbsetup' );

}

$t->assign( 'installation', TRUE );
$t->assign( 'dbname', DB_NAME ); 
$t->display( 'installation.tpl' );

exit;
?>
