
if(typeof Error == 'function')
{
	try {
		Error.stackTraceLimit = 25;
	}
	catch(_unused) {  }
}




// The error event is fired on a Window object when a resource failed to load or couldn't be used
// - for example if a script has an execution error.


window.addEventListener('error', function(e){

	console.log('error arguments', arguments)




})


