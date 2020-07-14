
function valid_arg_types( arguments = {}, spec = {}, err = null ) {


	console.log('spec', spec)
	console.log('args', arguments)

	for ( var k in spec ) { let v = spec[v]

		for ( var pos in arg_spec.positions )

		let arg = args[arg_spec.pos[0]] undefined

		console.log(k,v,arg)
	}

	err = 'tester';


}


const arg_spec_f_class_exists = {
	cls: {
		type: 'string',
		pos: [ 0 ]
	}
}

function class_exists( cls = '' ) {

	let err = null

	if( ! valid_arg_types(arguments,arg_spec_f_class_exists,err) )
	{
		console.log('ERR',err)
		return err
	}

  return eval("typeof " + cls + " === 'function'");
}

function is_object( variable ){

}

