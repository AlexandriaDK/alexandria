<?php
// Check if everything is set up correctly before calling functions and doing random stuff
$ignoreerrors = FALSE;

$setuperror = FALSE;
$errors = [];
$required_include_files = [ 'db.auth.php' ];
$required_php_extensions = [ 'mysqli', 'mbstring', 'intl' ];
$required_apache_modules = [ 'mod_rewrite' ];
$required_smarty = 'smarty-3.1.34/libs/';

// Rquired files
foreach( $required_include_files AS $file ) {
	$filepath = __DIR__ . '/../includes/' . $file;
	if ( ! file_exists( $filepath) ) {
		$setuperror = TRUE;
		$errors[] = "
Configuration file not found. Make sure the following file exists:
	includes/" . $file . "
A template file is provied. You can copy content from this file and fill out the credentials:
	includes/default." . $file . "
";
	}

}

// PHP Extensions
if ( $missing_php_extensions = array_diff( $required_php_extensions, get_loaded_extensions() ) ) {
	$setuperror = TRUE;
	$errors[] = "
The following required PHP extensions are not installed or enabled: " . implode( " ", $missing_php_extensions ) . "

Please check your PHP installation and configuration to make sure this extension is installed and enabled.

For Ubuntu/Debian, as root try out the following console command:
	apt install " . implode( " ", array_map( function ( $string ) { return "php-" . $string; }, $missing_php_extensions ) ) . "

Remember to restart the webserver after installing the extension.
";
}

// Check Apache setup - if running under Apache
if ( function_exists( 'apache_get_modules' ) ) {
	if ( $missing_apache_modules = array_diff( $required_apache_modules, apache_get_modules() ) ) {
		$setuperror = TRUE;
		$errors[] = "
The following required Apache modules are not installed or enabled: " . implode( " ",  $missing_apache_modules ) . "

Please check your Apache installation and configuration to make sure these modules are installed and enabled.

As root (or Administrator) try out the following console command:
	a2enmod " . implode( " ", $missing_apache_modules ) . "

Remember to restart the webserver after enabling the module.
";
	}
}

// Smarty installed? Should be installed with Alexandria, but currently it is not bundled with Alexandria. Currently hardcoded path with version. Eugh :-(
if ( ! file_exists( __DIR__ . '/../' . $required_smarty ) ) {
	$setuperror = TRUE;	
	$errors[] = "PHP template engine Smarty is not installed. This has to be manually installed at the parent dir at the following path:
	$required_smarty
";
}

// should also check if  AllowOverride all  is set for the directory. Or whatever value mod_rewrite requires.
// mod_rewrite requires  FileInfo  to be enabled.

if ( $setuperror === TRUE && $ignoreerrors != TRUE ) {
	header( "HTTP/1.1 503 Service Unavailable ");
	header( "Content-Type: text/plain" );
	print "Installation of Alexandria RPG database:

Alexandria is not configured correctly. For administrators, please fix the following issues in your webserver setup. For visitors, please have patience.
";
	foreach ( $errors AS $error ) {
		print PHP_EOL . "==============================" . PHP_EOL;
		print $error;
	}
	exit;
}

?>
