
window.URL_BASE_API = typeof window.URL_BASE_API == 'string' ? window.URL_BASE_API : '/'

function api_ajax(endp, method, data = {}, cb = null){

	if(typeof endp != 'string') return console.error("Funct. api_ajax expects 1st param 'endp' to be string")
	
	if(typeof method != 'string') return console.error("Funct. api_ajax expects 2nd param 'method' to be string")


	let async = typeof cb == 'function' ? true : false


	let url_base = URL_BASE_API

	if(url_base.substring(url_base.length-1)=='/') url_base = substring(0,url_base.length-1)


	var req = new XMLHttpRequest();

	req.addEventListener('load', function(){

		console.log(this)

		console.log(this.responseText)

	})

	req.open('GET', url_base+'/'+endp, async)

	req.send()

}