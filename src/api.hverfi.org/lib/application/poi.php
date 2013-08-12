<?php
function execute( $request_method, $path_info, $post_data = array() ) {
	switch( $request_method ) {
		case 'get':
			$command = array_shift( $path_info );
			switch( $command ) {
				case "find":
					$response = find_poi( $post_data );
					break;
				case "meta":
					// For lab tools and automated API client generation 
					$response = generate_meta();
					break;
				default:
					if( $command != '' ) {
						// The basic single poi get where the command part 
						// of the URL is actually the poi ID 
						$data['id'] = $command;
						$response = find_poi( $data );
						$response['data'] = $response['data'][0];
					} else {
						// Get all pois
						$response = find_poi();
					}
			}
			break;
		case 'post':
			$response = add_poi( $post_data );
			break;
		case 'put':
			$id = array_shift( $path_info );
			$response = update_poi( $id, $post_data );
			break;
		case 'delete':
			$id = array_shift( $path_info );
			$response = delete_poi( $id );
			break;
		default:
			throw new Exception('Unknown method: ' . $request_method, 400 );
	}
	return $response;
}

function add_poi( $post_data ) {
	/*
	| id          | char(36)
	| location    | point 
	| name        | varchar(200)
	| description | varchar(500) 
	| tags        | varchar(500) 
	*/
	$db = connect_db();
	$post_data['id'] = generate_uuid_v4();
	$post_data['type'] = 'PointOfInterest';
	
	$query = "INSERT INTO poi(id,location,name,description,tags) VALUES('%s',pointFromText( 'POINT(%s %s)'),'%s','%s','%s')";
	// Suppress error messages from missing attributes in posted data.
	@$db->query( sprintf( $query, $post_data['id'], $post_data['longitude'], $post_data['latitude'], $post_data['name'], $post_data['description'], $post_data['tags']));
	if( $db->errno )
		throw new Exception($db->error, 500);
	$response['code'] = 201;
	$response['data'] = $post_data;
	return $response;
}

function find_poi( $post_data = array() ){
	$pois = array();
	$where = '';
	if( isset( $post_data['id'] ) ) {
		$where = " WHERE id='" . $post_data['id'] . "'";
	} else if( isset( $post_data['name'] ) ) {
		$where = " WHERE name LIKE '%" . $post_data['name'] . "%'";
	} else if( isset( $post_data['description'] ) ) {
		$where = " WHERE description LIKE '%" . $post_data['description'] . "%'";
	}

	$db = connect_db();
	$result = $db->query( "SELECT id,name,description,x(location) longitude,y(location) latitude FROM poi$where" );
	if( $db->errno ) 
		throw new Exception( $db->error, 500 );
	while( $row = $result->fetch_assoc() ) {
		$row['type'] = 'PointOfInterest';
		$pois[] = $row;
	}
	$response['code'] = 200;
	$response['data'] = $pois;
	return $response;
}

function update_poi( $id, $new ) {
	if( $id != $new['id'] )
		throw new Exception( 'Updated poi data id mismatch');

	$db = connect_db();

	// Get current data
	$result = $db->query( "SELECT id,name,description,x(location) longitude,y(location) latitude FROM poi WHERE id='$id'" );
	if( $db->errno ) 
		throw new Exception( $db->error, 500 );
	$old = $result->fetch_assoc();
	
	// Sort out the difference, making sure id and type stay unaltered
	// NB Map coordinates need special care, so we'll do them separately
	$diff = hash_diff($old, $new, array('id', 'type', 'longitude', 'latitude'));
	
	$update = '';
	if( count($diff) ) {
		$update = hash2updatelist($diff);
	}

	if( ( isset( $new['longitude']) && $new['longitude'] != $old['longitude'] ) || 
		(isset( $new['latitude']) && $new['latitude'] != $old['latitude'] ) ) {
			$update .= ( $update != '' ) ? ',': 'SET ';
			$update .= sprintf( "location = pointFromText('POINT(%s %s)')", $new['longitude'], $new['latitude'] );
	}

	
	if( $update != '' ) {	
		$result = $db->query( "UPDATE poi " . $update . " WHERE id='$id'" );
		if( $db->errno ) 
			throw new Exception( $db->error, 500 );
	}
	return array(
		'code' => 200,
		'data' => $new
	);
}

function delete_poi( $id ) {
	$db = connect_db();

	// Get current data
	$result = $db->query( "DELETE FROM poi WHERE id='$id'" );
	if( $db->errno ) 
		throw new Exception( $db->error, 500 );

	return array(
		'code' => 200,
		'data' => ''
	);
}

function generate_meta() {
	return array (
		'code' => 200,
		'data' => array(
			array( 
				"name" => "add",
				"description" => "Add a single point of interest",
				"post_data" => array(
					"name" => "Name",
					"description" => "Description",
					"tags" => "Tags",
					"longitude" => "Longitude, decimal degrees",
					"latitude" => "Latitude, decimal degrees"
				)
			), 
			array(
				"name" => "find", 
				"description" => "Find points of interest matching criteria",
				"post_data" => array(
					"id" => "Find by poi id",
					"name" => "Find by name match",
					"description" => "Find by description match"
				)
			)
		)
	);
}
?>