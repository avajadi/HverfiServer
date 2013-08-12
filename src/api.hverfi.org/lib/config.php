<?php
$config = parse_ini_file('hverfi.ini',TRUE);
function getStatus( $status_code ) {
	return getConfig( 'status', $status_code );
}

function getConfig( $section, $key, $default = '' ) {
	global $config;	
	if( !isset($config[$section]) )
		throw new Exception( "No such configuration section: $section" );
	return isset( $config[$section][$key] ) ? $config[$section][$key] : $default;
}
?>