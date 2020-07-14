



function api_ajax ( ...args ) {

	let err = null

	if( typeof(window.valid_arg_types)=='function' && ! valid_arg_types(arguments,arg_spec_f_class_exists,err) )
	{
		console.log('ERR',err)
		return err
	}

	console.log(args)

	return new api_ajax_request({
		url: endpoint_path,
		method: method,
		data: data,
		async: async,
		cb: cb
	})
}



class api_ajax_request {


	constructor( settings = { } ){


		this.settings_required = 
		{
			method: 'string',
			url: 'string',
			data: 'object',
			async: 'boolean'
		}

		for(var k in settings) this.settings[k] = settings[k]


		this.xhr = new XMLHttpRequest();

		oReq.addEventListener("load", reqListener);
		
		oReq.open("GET", "http://www.example.org/example.txt");
		
		oReq.send();


	}

}