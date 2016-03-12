var template = twig({
	    id: "table",
	    href: $("#princ")[0].getAttribute("twigtemplate"),
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
$("#entree_cpS").on("mouseleave",function(){
	setTimeout(function(){
		$s=$('#entree_cpS');
		if($s.find(':selected').length>0){
			opttop=$s.find(':selected').offset().top;
			stop=$s.offset().top;
			$s.scrollTop($s.scrollTop()+(opttop-stop));
		}
	},100);
});
$("#entree_cpD").on("mouseleave",function(){
	setTimeout(function(){
		$s=$('#entree_cpD');
		if($s.find(':selected').length>0){
			opttop=$s.find(':selected').offset().top;
			stop=$s.offset().top;
			$s.scrollTop($s.scrollTop()+(opttop-stop));
		}
	},100);
});
$("#entree_point").on('click',function(){
	if(this.value=='_')
		this.value='x';
	else if(this.value=='x')
		this.value=' ';
	else
		this.value='_';
	this.onchange();
})
$("#entree_point").attr('autocomplete','off');
$("#entree_point").attr('readonly','');
$("#entree_type").parent().addClass('type_row');
