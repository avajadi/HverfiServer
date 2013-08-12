// Every class that is to be initialised from json data needs the clone method
// and it needs to be added to the type map
function PointOfInterest( longitude, latitude, name, tags ) {
	this.longitude = longitude;
	this.latitude = latitude;
	this.name = name;
	this.setTags( tags );
}

PointOfInterest.prototype = {
	clone:function( original ) {
		this.id = original.id;
		this.longitude = original.longitude;
		this.latitude = original.latitude;
		this.name = original.name;
		this.description = original.description;
		this.setTags( original.tags );
	},
	setTags:function( tags ){
		this.tags = new Array();
		if( typeof( tags ) === 'string'	)
			this.tags = tags.split( / *, */);
		if( tags instanceof Array )
			this.tags = tags;
	}
};

function Mapper(){
}

Mapper.prototype = {
	typeMap:{
		"PointOfInterest" : PointOfInterest
	},
	mapFromObject:function( object ) {
		var constructor = this.typeMap[object.type];
		var res = new constructor();
		res.clone( object );
		return res;
	}
};

function getBoundingBox( pois ) {
		var xmax = 0;
		var xmin = 500;
		var ymax = 0;
		var ymin = 500;
		for( var i in pois ) {
			var poi = pois[i];
			xmax = Math.max(xmax, poi.longitude);
			xmin = Math.min(xmin, poi.longitude);
			ymax = Math.max(ymax,poi.latitude);
			ymin = Math.min(ymin,poi.latitude);
		}
		return {
			longitude:{max:xmax,min:xmin},
			latitude:{max:ymax,min:ymin},
			center:{longitude:(xmax+xmin)/2,latitude:(ymax+ymin)/2}
		};
}

function Hverfi(){
	this.mapper = new Mapper();
}

Hverfi.prototype = {
	base_url:'http://api.hverfi.org',
	getPois:function( callback ){
		$.get( this.base_url + '/poi/find', function(data) {
			var data_json = JSON.parse( data );
			var pois = new Array();
			for (var i in data_json) {
				var obj = data_json[i];
				var poi = new Mapper().mapFromObject( obj );
				pois.push( poi );
			}
			callback(pois); 
		} );
	}
};