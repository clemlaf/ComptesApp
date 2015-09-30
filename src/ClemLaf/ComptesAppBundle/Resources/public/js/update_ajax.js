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

    //on affiche le message d'acceuil
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
    if (nid=='new')
	xhr.onreadystatechange = function() { after_ajout(xhr,ff.elements[3].value);load_table(xhr); };
    else
	xhr.onreadystatechange = function() { alert_ajax(xhr);load_table(xhr); };
    //    alert("on tente l'appel suivant "+"update_ajax.php?id="+nid+"&cp_s="+cp_s+"&cp_d="+cp_d+"&cat="+cat+"&com="+com+"&pr="+pr+"&pt="+pt+"&moy="+moy)
    //on appelle le fichier reponse.txt
    xhr.open("POST", "./update", true);
    //alert(com);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(get_param()+"&id="+nid+"&date="+date+"&cp_s="+cp_s+"&cp_d="+cp_d+"&cat="+cat+"&com="+com+"&pr="+pr+"&pt="+pt+"&moy="+moy);
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
    xhr.onreadystatechange = function() { after_suppr(xhr,nid);load_table(xhr); };
    var nid=id.querySelectorAll("[name=id]")[0].value;
    xhr.open("POST", "./delete", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(get_param()+"&id="+nid);
}

function alert_ajax(xhr){
    if (xhr.readystate==4) 
    {
	/*document.getelementbyid("soldepointe").innerhtml=xhr.responsexml.documentelement.getelementsbytagname("soldepointe")[0].firstchild.nodevalue;
	document.getelementbyid("sonew").innerhtml=xhr.responsexml.documentelement.getelementsbytagname("soldefiltre")[0].firstchild.nodevalue;*/
	show_msg('données mises à jour');
    }
}

function after_suppr(xhr,nid){
    if (xhr.readyState==4){
	/*var ff=document.getElementById("rw"+nid);
	$(ff).css({"display" : "none"});
	document.getElementById("soldepointe").innerHTML=xhr.responseXML.documentElement.getElementsByTagName('soldepointe')[0].firstChild.nodeValue;
	document.getElementById("sonew").innerHTML=xhr.responseXML.documentElement.getElementsByTagName('soldefiltre')[0].firstChid.nodeValue;*/
	show_msg('enregistrement supprimé');
    }

}

function after_ajout(xhr,top=false,nb){
    if (xhr.readyState==4){
	//var ff=document.getElementById("ff"+nid);
	//for (var i=0;i<ff.length;i++){
	//   $(ff[i]).css( { "text-decoration" : "line-through"});
	//}
	/*var ff=document.getElementById("princ").lastChild;
	var tot=ff.childNodes.length;
	var i=0;
	while(i<tot && (typeof ff.childNodes[top?tot-i-1:i].id == "undefined" || ff.childNodes[top?tot-i-1:i].id.indexOf("rw")<0 || ff.childNodes[top?tot-i-1:i].style['display']=='none')){
	    i++;
	}
	if(tot>nb){
	    $(ff.childNodes[top?tot-i-1:i]).css({"display" : "none"});
	}
	var ff=document.getElementById("rwnew");
	var newtr=document.createElement("tr");
	var tt=xhr.responseXML;
	newid=tt.documentElement.getElementsByTagName("id")[0].firstChild.nodeValue;
	newentry=tt.documentElement.getElementsByTagName("newentry")[0];
	ff.id="rw"+newid;
	newtr.id="rwnew";
	var htmltext=ff.innerHTML;
	htmltext=htmltext.replace("rwnew",newtr.id).replace("sonew","so"+newid).replace("ptnew","pt"+newid).replace("xnew","x"+newid).replace("datnew","dat"+newid).replace('value="new"','value="'+newid+'"');
	newtr.innerHTML=ff.innerHTML;
	ff.innerHTML=htmltext;
	for(var i=0;i<newentry.childNodes.length;i++){

	    aa=newentry.childNodes[i];
	    if(aa.nodeName!="#text" && aa.firstChild!=null){
		console.log("[name="+aa.tagName+"]")
		    ff.querySelectorAll("[name="+aa.nodeName+"]")[0].value=aa.firstChild.nodeValue;
	    }
	}
	if(top){
	    ff.parentNode.insertBefore(newtr,ff.nextSibling);}
	else{
	    ff.parentNode.insertBefore(newtr,ff);
	}*/
	//clearline(document.getElementById('rwnew'));
	show_msg('enregistrement ajouté');
    }

}

