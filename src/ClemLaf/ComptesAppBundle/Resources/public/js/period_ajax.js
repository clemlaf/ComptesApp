function ajax(id)
{
    var xhr=null;
    
    if (window.XMLHttpRequest) { 
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) 
    {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
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

    data_array['id']=nid
    data_array['last_date']=ldate
    data_array['end_date']=edate
    data_array['mois']=mois
    data_array['jours']=jours
    data_array['cp_s']=cp_s
    data_array['cp_d']=cp_d
    data_array['cat']=cat
    data_array['com']=com
    data_array['pr']=pr
    data_array['moy']=moy
    //on définit l'appel de la fonction au retour serveur
    if (nid=='new'){
	    xhr.onreadystatechange= function(){after_ajout(xhr,data_array);};}
    else
	xhr.onreadystatechange = function() { alert_ajax(xhr); };
    //alert(com);
    xhr.open("POST", "./period/update", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("id="+nid+"&last_date="+ldate+"&end_date="+edate+"&jours="+jours+"&mois="+mois+"&cp_s="+cp_s+"&cp_d="+cp_d+"&cat="+cat+"&com="+com+"&pr="+pr+"&moy="+moy);
}
    
function supprime(id){
    var xhr=null;
    if (window.XMLHttpRequest) { 
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) 
    {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }

    //on définit l'appel de la fonction au retour serveur
    xhr.onreadystatechange = function() { after_suppr(xhr,nid); };
    var nid=id.querySelectorAll("[name=id]")[0].value;
    xhr.open("POST", "./period/delete", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("id="+nid);
}

function alert_ajax(xhr){
    if (xhr.readyState==4) 
    {
	show_msg( 'données mises à jour')
    }
}

function after_ajout(xhr, data_array){
    if (xhr.readyState==4) 
    {
	var ff=document.getElementById("rwnew");
	var newtr=document.createElement("tr");
	var newid=xhr.responseText;
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
	show_msg( 'enregistrement ajouté')
    }
}

function after_suppr(xhr,nid){
    if (xhr.readyState==4){
	//var ff=document.getElementById("ff"+nid);
	//for (var i=0;i<ff.length;i++){
	//   $(ff[i]).css( { "text-decoration" : "line-through"});
	//}
	var ff=document.getElementById("rw"+nid);
	$(ff).css({"display" : "none"});
	show_msg( 'enregistrement supprimé')
    }
    
}
    
