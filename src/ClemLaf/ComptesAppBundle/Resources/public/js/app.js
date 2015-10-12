var template = twig({
	    id: "table",
	    href: "../bundles/clemlafcomptesapp/views/table2.html.twig",
	        // this example we'll block until the template is loaded
		 async: true,
		//
		//         // The default is to load asynchronously, and call the load function 
		//             //   when the template is loaded.
		//
		 load:function(template){ update_table(template); } 
});
var ff=document.forms[0];
for(var i=0; i<ff.elements.length;i++){
    ff.elements[i].onchange=function(){update_table(null); };
}
$("#entree_date1").datepicker({dateFormat:"yy-mm-dd"});
$("#entree_date2").datepicker({dateFormat:"yy-mm-dd"});
