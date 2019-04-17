function set_language(symbol) 
{
	console.log("symbol");
	console.log(symbol);
	$.ajax ({
	type: "POST",
	url: 'includes/set_language.php',
	data: { 
	        'symbol':symbol,
	      },
	    success: function(respon)
	    {	   
	    	console.log(respon);
	    	window.location.reload();
	    }
	})
}