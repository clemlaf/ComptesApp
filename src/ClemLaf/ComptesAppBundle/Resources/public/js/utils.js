
function change_signe(ii){
    //alert("pr"+ii);
    var iidd="pr"+ii;
    var aa=document.getElementById(iidd).value;
    if(parseFloat(aa)<0){
        document.getElementById(iidd).value=aa.substr(1,aa.length);
        $(document.getElementById(iidd)).css({"color" : "#00AA00"});
    }else{
        document.getElementById(iidd).value="-"+aa;
        $(document.getElementById(iidd)).css({"color" : "#AA0000"});
    }
}
function clearform(){
    var aa=document.getElementById('entree');
    var cells=aa.getElementsByTagName('select');
    for(var i=0;i<cells.length;i++){
        var so=cells[i].selectedOptions;
        for(var j=0; j<so.length;j++){
            so[j].selected=false;
        }
    }
}
function duplicate(id,nid){
    var date=id.querySelectorAll("[name=date]")[0].value;
    var cp_s=id.querySelectorAll("[name=cp_s]")[0].value;
    var cp_d=id.querySelectorAll("[name=cp_d]")[0].value;
    var cat=id.querySelectorAll("[name=cat]")[0].value;
    var com=id.querySelectorAll("[name=com]")[0].value;
    var pr=id.querySelectorAll("[name=pr]")[0].value;
    //    var pt=id.querySelectorAll("[name=pt]")[0].checked;
    var moy=id.querySelectorAll("[name=moy]")[0].value;
    //id.querySelectorAll("[name=date]")[0].value=date;
    nid.querySelectorAll("[name=cp_s]")[0].value=cp_s;
    nid.querySelectorAll("[name=cp_d]")[0].value=cp_d;
    nid.querySelectorAll("[name=cat]")[0].value=cat;
    nid.querySelectorAll("[name=com]")[0].value=com;
    nid.querySelectorAll("[name=moy]")[0].value=moy;
    nid.querySelectorAll("[name=pr]")[0].value=pr;
    nid.querySelectorAll("[name=date]")[0].value=date;
    //    var pt=id.querySelectorAll("[name=pt]")[0].checked;


}

function pointage(nid){
    d=new Date();
    m=d.getMonth()+1;
    if(m<10)
	m="0"+m;
    nid.querySelectorAll("[name=date]")[0].value="01/"+m+"/"+d.getFullYear();
    console.log(nid.querySelectorAll("[name=cat]")[0].value);
    nid.querySelectorAll("[name=cat]")[0].value="";
    nid.querySelectorAll("[name=moy]")[0].value="";
    nid.querySelectorAll("[name=com]")[0].value="encours CB";
    nid.querySelectorAll("[name=pr]")[0].value=-1*parseFloat(document.getElementById("soldepointe").lastChild.textContent.trim());
}
function update_table(templ=null){
    var xhr=null;

    if (window.XMLHttpRequest) { 
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) 
    {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }
    ff=document.forms[0]
	param='';
    sp='';
    top=false;
    for(var i=0;i<ff.elements.length;i++){
        if(ff.elements[i].tagName=="SELECT"){
            for(var j=0;j<ff.elements[i].children.length;j++){
                if(ff.elements[i].children[j].selected){
                    param=param+sp+
                        encodeURIComponent(ff.elements[i].name)+
                        '='+encodeURIComponent(ff.elements[i].children[j].value);
                    sp='&';
                }
            }
        }else{
            param=param+sp+
                encodeURIComponent(ff.elements[i].name)+
                '='+encodeURIComponent(ff.elements[i].value);
        }
	sp='&';
    }
    xhr.onreadystatechange = function() { load_table(xhr,templ); };
    //    alert("on tente l'appel suivant "+"update_ajax.php?id="+nid+"&cp_s="+cp_s+"&cp_d="+cp_d+"&cat="+cat+"&com="+com+"&pr="+pr+"&pt="+pt+"&moy="+moy)
    //on appelle le fichier reponse.txt
    xhr.open("POST", "./get_table", true);
    //alert(com);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(param);
}
function load_table(xhr,templ=null){
    if (xhr.readyState==4) 
    {
	var data=JSON.parse(xhr.responseText);
        console.log(data);
        if (templ==null){
            var tempp=templ;}
        else{
        var tempp=twig({ref: "table"});}
        var tabHtml=tempp.render(data);
        document.getElementById("princ").innerHTML=tabHtml;
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