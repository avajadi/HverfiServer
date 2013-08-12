<?php
// Always load config first!
include( 'config.php' );
include( 'db.php' );
include( 'util.php' );

$path_info = explode( '/', $_SERVER['REQUEST_URI'] );

// Throw away the first, always empty, element
array_shift( $path_info );

// First actual element is the application name
$application = array_shift( $path_info );

// get, post, put or delete?
$request_method = strtolower($_SERVER['REQUEST_METHOD']);  

// Read POST data as a JSON structure
$post_data = json_decode( file_get_contents("php://input"), true );

if( file_exists( "./application/$application.php" ) )
{
	include( "./application/$application.php" );
	try {
		$result = execute( $request_method, $path_info, $post_data );
		header( 'HTTP/1.1 ' . $result['code'] . ' ' . getStatus($result['code']) );
		header( 'Content-Type: application/json');
		print( json_encode( $result['data'] ) );
	} catch( Exception $e ) {
		header( 'HTTP/1.1 ' . $e->getCode() . ' ' . getStatus($e->getCode()) );
		header( 'Content-Type: text/plain');
		print( $e->getMessage() );		
	}
} else {
		header( 'HTTP/1.1 404 ' . getStatus(404) );
}
?>
