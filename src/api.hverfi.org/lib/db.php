<?php
function connect_db() {
	//  hverfi@localhost identified by 'liuT(I&Tku6riukfKU%&yf'
	return new mysqli( 
		getConfig( 'database', 'server'), 
		getConfig( 'database','user'), 
		getConfig( 'database','password'), 
		getConfig( 'database','db' ) );
}

function hash2updatelist( $hash ) {
	$c = 'SET';
	foreach ($hash as $key => $value) {
		$updatelist .= "$c $key = '$value'";
		$c = ',';
	}
	return $updatelist;
}

?>
