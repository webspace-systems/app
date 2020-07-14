
class template {


	constructor () {

		this.initialized = false

		this.has_loaded_class_x = class_exists('someclass..')
	}


	init () {

		if( this.initialized ) return console.trace('Already initialized');

		console.log('test');

	}

}
