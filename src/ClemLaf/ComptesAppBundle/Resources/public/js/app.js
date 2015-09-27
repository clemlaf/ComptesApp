var template = twig({
	    id: "table",
	    href: "/bundles/clemlafcomptesapp/views/table2.html.twig",
	        // this example we'll block until the template is loaded
		 async: true,
		//
		//         // The default is to load asynchronously, and call the load function 
		//             //   when the template is loaded.
		//
		 load:function(template){ update_table(template); } 
});


