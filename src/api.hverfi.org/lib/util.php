<?php
function generate_uuid_v4()
{
    $data = openssl_random_pseudo_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0010
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Get the values that distinguish $new from $old, returning a hash
 * of only those present in both $old and $new
 * @param $old Original hash
 * @param $new Hash with new values
 * @param $locked List of keys to ignore
 */
function hash_diff( $old, $new, $locked = array() ) {
	$diff = array();
	foreach ($old as $key => $value) {
		if( in_array($key, $locked) )
			continue;
		if( isset($new[$key]) && $new[$key] != $value ) 
			$diff[$key] = $new[$key];
	}
	return $diff;
}
?>