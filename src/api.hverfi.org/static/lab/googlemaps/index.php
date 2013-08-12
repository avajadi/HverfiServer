<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        margin: 0;
        padding: 0;
        height: 100%;
      }
      #map-canvas {
      	width:100%;
      }
      #control {
      	width:130px;
      }
    </style>

    <script src="/lab/jquery.js"></script>
	<script src="/api/hverfi.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
		var map;
		var hverfi = new Hverfi();
		var boundingBox;
		var poiMap = {};
		var myLatlng = new google.maps.LatLng( 56.231, 13.456 );
		
		function onPoiChange() {
			var poiId = '';
			$("#poi_select option:selected").each(function () {
				poiId = $(this).val();
			});
			centerOn(poiMap[poiId]);
		}
		function centerOn( poi ) {
			var centrePoint = new google.maps.LatLng( poi.latitude, poi.longitude );
			placeMarker( centrePoint, map );
		}
		
		function initialize() {
			var mapOptions = {
				zoom: 12,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(document.getElementById('map-canvas'),
				mapOptions);
		  
			google.maps.event.addListener(map, 'click', function(e) {
				placeMarker(e.latLng, map);
			});

			hverfi.getPois( function(pois){
				boundingBox = getBoundingBox(pois);
				for(var i in pois) {
					$('#poi_select').append( new Option(pois[i].name, pois[i].id) );
					poiMap[pois[i].id] = pois[i];
				}
				var centrePoint = new google.maps.LatLng(boundingBox.center.latitude, boundingBox.center.longitude);
				map.panTo( centrePoint );
				$('#poi_select').change( onPoiChange );
				
			});
		}
		function placeMarker(position, map) {
		  var marker = new google.maps.Marker({
			position: position,
			map: map
		  });
		  map.panTo(position);
		}

		google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
  	<div id="control">
  		<select id="poi_select">
  		</select>
  	</div>
    <div id="map-canvas"></div>
  </body>
</html>