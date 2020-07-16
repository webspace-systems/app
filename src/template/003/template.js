
class template {

	static init ( $config = {}, $callback = null  ) {

		return window.Template instanceof this ? Template : (

			window.Template = new template( ...arguments )
		)
	}

	constructor (  config = {},  $callback = null  ) {

		if ( typeof this.initialized != 'undefined' ) return console.error('template already constructed')

		this.initialized = true

		console.log('new', arguments)

		this.config = 
		{
			html_attr_val_double_quotes_may_fallback_to: "”",

			debug: true,

			...config
		}

		this.html_tags_valid = [
			'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base', 'bdi', 'bdo',
			'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col', 'colgroup',
			'data', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'div', 'dl', 'dt', 'em', 'embed',
			'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
			'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'label',
			'legend', 'li', 'link', 'main', 'map', 'mark', 'menu', 'meta', 'meter', 'nav', 'noscript',
			'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'picture', 'pre', 'progress',
			'q', 'rp', 'rt', 'ruby', 's', 'samp', 'script', 'section', 'select', 'slot', 'small', 'source',
			'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template',
			'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'track', 'u', 'ul', 'var', 'video', 'wbr'
		]

	}




	load (  path,  parameters = {}  ) {

		console.log('load', arguments)


		if ( typeof path != 'string' ) throw "Invalid argument 'path'. ...arguments: " + JSON.stringify({...arguments});


		if ( typeof module.contents == 'object' )
		{
			module.contents = this.render ( module.contents )
		}


		console.log( 'module', module )
	}






	render ( doc, depth = 0, path = [], ret_array = false ) {

		if ( typeof doc[0] == 'string' )
		{
			doc = [doc]
		}
		else if ( typeof doc[0] == 'object' && Object.keys(doc).length == 1 )
		{
			doc = doc[0]
			
			if ( typeof doc[0] == 'object' && Object.keys(doc).length == 1 )
			{
				doc = doc[0]
			}
		}

		var html = []

		var padding = ("	").repeat(depth)

		var tags_no_content = [ 'meta', 'br', 'input', 'link' ]

		var valid_types_of_attribute_value = [ 'string', 'integer', 'double', 'boolean' ]


		for ( var dk in doc )
		{
			var elem = doc[dk]

			if( typeof elem == 'string' )
			{
				html.push( padding + elem )

				continue
			}

			if ( typeof elem != 'object' )
			{
				console.log("Invalid doc elem must be string or array Got type: '", typeof(elem), elem)
				continue
			}

			if ( elem.length == 0  || typeof elem[0] != 'string' )
			{
				console.log("Invalid doc elem Missing tag name at index/key 0 in given elem: ", elem, {doc})
				continue
			}


			var e_contents = elem.filter( (k,v)=> typeof k == 'number' )

			if( e_contents.length == 0 )
			{
				continue
			}


			var e_str_contents = elem.filter((k,v)=> typeof v == 'string' )

			if( e_str_contents.length === elem.length && this.html_tags_valid.indexOf( elem[0] ) < 0 )
			{
				for ( var ck in e_str_contents )
				{
					html.push ( padding + e_str_contents[ck] )
				}

				continue
			}


			var e_name = e_contents[0]

			e_contents = array_slice(e_contents, 1)

			var e_path = [ ...path, e_name ]

			var e_attrs = array_filter(elem, 'is_string', ARRAY_FILTER_USE_KEY)



			if( e_name == 'style' || e_name == 'link' )
			{
				if( typeof e_attrs['src'] == 'string'  && ( e_attrs['href'] = e_attrs['src'] ) )
				{
					delete e_attrs['src']
				}

				e_name = typeof e_attrs['href'] == 'string' ? 'link' : 'style'
			}



			var elem_html = padding + '<' + e_name


			if ( e_attrs.length > 0 )
			{
				for ( var attr_name in e_attrs )
				{
					var attr_value = e_attrs[attr_name]

					if( valid_types_of_attribute_value.indexOf ( typeof ( attr_value ) ) < 0 )
					{
						console.error("Invalid type of attr. value: '"+typeof(attr_value)+"':", {attr_value}, " .. given for attr. name 'attr_name' in elem: ", elem)
					}

					var quote_char = attr_value.indexOf('"') >= 0 ? "'" : '"'

					if( quote_char == "'" && attr_value.indexOf("'") >= 0 )
					{
						if(this.config.html_attr_val_double_quotes_may_fallback_to)
						{
							attr_value = str_replace('"', this.config.html_attr_val_double_quotes_may_fallback_to, attr_value)
						}

						console.warn("Unable to set value value of attr. name 'attr_name', as it contains both single and double quotes: ", typeof(attr_value), ": 'attr_value' given for attr. name 'attr_name' of \elem: ", elem)
					}

					elem_html += ' ' + attr_name + '=' + quote_char + attr_value + quote_char
				}
			}
			
			
			if ( tags_no_content.indexOf(e_name) >= 0 )
			{
				html.push ( elem_html + ' />' )
			}
			else
			{
				if( Object.keys(e_contents).length > 0 )
				{
					content = this.render ( e_contents,  depth + 1,  e_path,  true )

					if ( Object.keys( content ).length > 0 )
					{
						html.push ( elem_html + '>' )

						for ( var clk in content )
						{
							html.push ( content[clk] )
						}

						html.push ( padding + '</'+e_name+'>' )
					}
					else if ( typeof content[0] == 'string' )
					{
						html.push ( elem_html + '>' + content[0].trim() + '</'+e_name+'>' )
					}
				}
				else
				{
					html.push( elem_html + '>' + '</'+e_name+'>' )
				}
			}
		}

		console.log('html', html)

		return ret_array ? html : html.join("\n")
	}

}
