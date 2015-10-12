function ajax(id)
{
    data_array={} 
    //on affiche le message d'acceuil
    var nid=id.querySelectorAll("[name=id]")[0].value;
    console.log(nid);
    var ldate=id.querySelectorAll("[name=last_date]")[0].value;
    var edate=id.querySelectorAll("[name=end_date]")[0].value;
    var mois=id.querySelectorAll("[name=mois]")[0].value;
    var jours=id.querySelectorAll("[name=jours]")[0].value;
    var cp_s=id.querySelectorAll("[name=cp_s]")[0].value;
    var cp_d=id.querySelectorAll("[name=cp_d]")[0].value;
    var cat=id.querySelectorAll("[name=cat]")[0].value;
    var com=id.querySelectorAll("[name=com]")[0].value;
    var pr=id.querySelectorAll("[name=pr]")[0].value;
    var moy=id.querySelectorAll("[name=moy]")[0].value;

    data_array['id']=nid;
    data_array['last_date']=ldate;
    data_array['end_date']=edate;
    data_array['mois']=mois;
    data_array['jours']=jours;
    data_array['cp_s']=cp_s;
    data_array['cp_d']=cp_d;
    data_array['cat']=cat;
    data_array['com']=com;
    data_array['pr']=pr;
    data_array['moy']=moy;
    $.ajax({url: "./period/update",
	method: "POST",
	data: data_array,
    })
    .done(function(data){
	    if (nid=='new')
		after_ajout(data, data_array);
	    else
		show_msg( 'données mises à jour');
	    load_table(data,templ);})
    .fail(function(){showerr();})
    ;
}
    
function supprime(id){
    var nid=id.querySelectorAll("[name=id]")[0].value;
    $.ajax({url: "./period/delete",
	method: "POST",
	data: "id="+nid,
    })
    .done(function(){after_suppr(nid);})
    .fail(function(){showerr();})
    ;
}


function after_ajout(data,data_array){
	var ff=document.getElementById("rwnew");
	var newtr=document.createElement("tr");
	var newid=data;
	ff.id="rw"+newid;
	newtr.id="rwnew";
	var htmltext=ff.innerHTML;
	htmltext=htmltext.replace("rwnew",newid).replace("ldatnew","ldat"+newid).replace("monew","mo"+newid).replace("xnew","x"+newid).replace("prnew","pr"+newid).replace("jonew","jo"+newid).replace("edatnew","edat"+newid).replace('value="new"','value="'+newid+'"');
	newtr.innerHTML=ff.innerHTML;
	ff.innerHTML=htmltext;
	for(var key in data_array){
	    ff.querySelectorAll("[name="+key+"]")[0].value=data_array[key];
	}
	ff.querySelectorAll("[name=id]")[0].value=newid;
    ff.parentNode.appendChild(newtr);
	show_msg( 'enregistrement ajouté');
}

function after_suppr(nid){
	var ff=document.getElementById("rw"+nid);
	$(ff).css({"display" : "none"});
	show_msg( 'enregistrement supprimé');
}
    
