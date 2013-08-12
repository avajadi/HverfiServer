<?php
function execute( $request_method, $path_info, $post_data = array() ) {
	$old = array (
		'id' => 'old id',
		'name' => 'old name',
		'type' => 'old type',
		'where' => 'only old',
		'description' => 'old description'
	);	
	$new = array (
		'id' => 'new id',
		'name' => 'new name',
		'type' => 'new type',
		'description' => 'new description'
	);
	$locked = array( 'id', 'type' );
	$tests = array(
		'old' => $old,
		'new' => $new,
		'unlocked' => hash_diff( $old, $new ),
		'locked' => hash_diff( $old, $new, $locked ),
		'updater' => hash2updatelist(hash_diff( $old, $new, $locked ))
	);
	return array(
		'code' => 200,
		'data' => $tests
	);
}
?>