
$(document).on('keypress', 'form', (e)=>{

	if(e.keyCode == 13)
	{
		e.preventDefault();

		$(e.target).parents('form').first().submit()
	}

})