<?php
function execute( $path_info ) 
{
	print( json_encode( collect_meta() ) );
}

function collect_meta() {
	$files = scandir( './application' );
	$applications = array();
	foreach( $files as $file )
	{
		if( $file == '.' ) continue;
		if( $file == '..' ) continue;
		if( $file == 'meta.php' ) continue;
		if( preg_match( '/\.php$/', $file ) ) {
			$app['name'] = basename( $file, '.php' );
			include( $file );
			$app['meta'] = meta();
			// "uninclude"???
			$applications[] = $app;
		}
	}
	return $applications;
}

?>