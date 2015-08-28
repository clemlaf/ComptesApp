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
    var date=id.querySelectorAll("[name=last_date]")[0].value;
    var date=id.querySelectorAll("[name=end_date]")[0].value;
    var date=id.querySelectorAll("[name=mois]")[0].value;
    var date=id.querySelectorAll("[name=jours]")[0].value;
    var cp_s=id.querySelectorAll("[name=cp_s]")[0].value;
    var cp_d=id.querySelectorAll("[name=cp_d]")[0].value;
    var cat=id.querySelectorAll("[name=cat]")[0].value;
    var com=id.querySelectorAll("[name=com]")[0].value;
    var pr=id.querySelectorAll("[name=pr]")[0].value;
    var moy=id.querySelectorAll("[name=moy]")[0].value;

    //on définit l'appel de la fonction au retour serveur
	xhr.onreadystatechange = function() { alert_ajax(xhr); };
    //alert(com);
    xhr.open("POST", "./update_period", true);
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
    xhr.open("POST", "./delete_period", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("id="+nid);
}


function alert_ajax(xhr){
    if (xhr.readyState==4) 
    {
	$(document).ready(function(){
	    jSuccess(
		'données mises à jour',
		{
		    autoHide : true, // added in v2.0
		    clickOverlay : false, // added in v2.0
		    MinWidth : 250,
		    TimeShown : 400,
		    ShowTimeEffect : 100,
		    HideTimeEffect : 100,
		    LongTrip :00,
		    HorizontalPosition : 'right',
		    VerticalPosition : 'top',
		    ShowOverlay : false,
   		    ColorOverlay : '#000',
		    OpacityOverlay : 0.3,
		}
	    );
	}
			 );
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
	$(document).ready(
	    function(){
		jSuccess(
		    'enregistrement supprimé',
		    {
			autoHide : true, // added in v2.0
			clickOverlay : false, // added in v2.0
			MinWidth : 250,
			TimeShown : 400,
			ShowTimeEffect : 100,
			HideTimeEffect : 100,
			LongTrip :00,
			HorizontalPosition : 'right',
			VerticalPosition : 'top',
			ShowOverlay : false,
   			ColorOverlay : '#000',
			OpacityOverlay : 0.3,
		    }
		);
	    }
	);
    }
    
}
    
