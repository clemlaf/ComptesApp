function ajax(id) {
    var nid=id.querySelectorAll("[name=id]")[0].value;
    console.log(nid);
    var date=id.querySelectorAll("[name=date]")[0].value;
    var cp_s=id.querySelectorAll("[name=cp_s]")[0].value;
    var cp_d=id.querySelectorAll("[name=cp_d]")[0].value;
    var cat=id.querySelectorAll("[name=cat]")[0].value;
    var com=id.querySelectorAll("[name=com]")[0].value;
    var pr=id.querySelectorAll("[name=pr]")[0].value;
    if(nid!='new')
	var pt=id.querySelectorAll("[name=pt]")[0].checked;
    else
	var pt=false;
    var moy=id.querySelectorAll("[name=moy]")[0].value;
    //on définit l'appel de la fonction au retour serveur
    $.ajax({url: "./update",
	method: "POST",
	data: get_param()+"&id="+nid+"&date="+date+"&cp_s="+cp_s+"&cp_d="+cp_d+"&cat="+cat+"&com="+com+"&pr="+pr+"&pt="+pt+"&moy="+moy,
	dataType:"json",
    })
    .done(function(data){
	    if (nid=='new')
		show_msg('enregistrement ajouté');
	    else
		show_msg('données mises à jour');
	    load_table(data,null);})
    .fail(function(){showerr();})
    ;
}

function supprime(id){
    var nid=id.querySelectorAll("[name=id]")[0].value;
    $.ajax({url: "./delete",
	method: "POST",
	data: get_param()+"&id="+nid,
	dataType:"json",
    })
    .done(function(data){
	    show_msg('enregistrement supprimé');
	    load_table(data,templ);})
    .fail(function(){showerr();})
    ;
}


