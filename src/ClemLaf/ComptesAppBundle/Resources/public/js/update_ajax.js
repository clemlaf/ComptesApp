function ajax(id) {
  nid=id.querySelectorAll("[name=id]")[0].value;
  var entree={id:nid,
    date:id.querySelectorAll("[name=date]")[0].value,
    cp_s:id.querySelectorAll("[name=cp_s]")[0].value,
    cp_d:id.querySelectorAll("[name=cp_d]")[0].value,
    cat:id.querySelectorAll("[name=cat]")[0].value,
    com:id.querySelectorAll("[name=com]")[0].value,
    moy:id.querySelectorAll("[name=moy]")[0].value,
    pr:id.querySelectorAll("[name=pr]")[0].value,
    pt:(nid!='new' ? id.querySelectorAll("[name=pt]")[0].value : false)};
    console.log(nid);
    var arr=new Array();
    arr.push(entree)
    var par=get_param();
    par['entrees']=arr;
    //on définit l'appel de la fonction au retour serveur
    $.ajax({url: "./update",
	method: "POST",
	data: par,
  //processData: false,
  //contentType: "application/json; charset=UTF-8",
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
    var par=get_param();
    par['id']=nid
    $.ajax({url: "./delete",
	method: "POST",
	data: par,
	dataType:"json",
    })
    .done(function(data){
	    show_msg('enregistrement supprimé');
	    load_table(data,null);})
    .fail(function(){showerr();})
    ;
}
