
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
    document.getElementById('entree_date1').value='';
    document.getElementById('entree_date2').value='';
    document.getElementById('entree_com').value='';
    document.getElementById('entree_point').value='_';
    document.getElementById('entree_deb').value='0';
    update_table();
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

function clearline(id){
    id.querySelectorAll("[name=date]")[0].value=null;
    // id.querySelectorAll("[name=cp_s]")[0].value=null;
    id.querySelectorAll("[name=cp_d]")[0].value=null;
    id.querySelectorAll("[name=cat]")[0].value=null;
    id.querySelectorAll("[name=com]")[0].value=null;
    id.querySelectorAll("[name=pr]")[0].value=null;
    //    var pt=id.querySelectorAll("[name=pt]")[0].checked;
    id.querySelectorAll("[name=moy]")[0].value=null;

}

function get_param(){
    var ff=document.forms[0];
    param='';
    sp='';
    top=false;
    for(var i=0;i<ff.elements.length;i++){
	var el=ff.elements[i];
        if(el.tagName=="SELECT"){
            for(var j=0;j<el.children.length;j++){
                if(el.children[j].selected){
                    param=param+sp+
                        encodeURIComponent(el.name)+
                        '='+encodeURIComponent(el.children[j].value);
                    sp='&';
                }
            }
        }else{
	    if(el.type!="radio" || el.checked){
            param=param+sp+
                encodeURIComponent(el.name)+
                '='+encodeURIComponent(el.value);
	    }
        }
	sp='&';
    }
    return param;
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
    xhr.onreadystatechange = function() { load_table(xhr,templ); };
    //    alert("on tente l'appel suivant "+"update_ajax.php?id="+nid+"&cp_s="+cp_s+"&cp_d="+cp_d+"&cat="+cat+"&com="+com+"&pr="+pr+"&pt="+pt+"&moy="+moy)
    //on appelle le fichier reponse.txt
    xhr.open("POST", "./get_table", true);
    //alert(com);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(get_param());
}

function load_table(xhr,templ=null){
    if (xhr.readyState==4) 
    {
	var data=JSON.parse(xhr.responseText);
        console.log(data);
        if (templ!=null){
            var tempp=templ;}
        else{
	    var tempp=twig({ref: "table"});
	}
        var tabHtml=tempp.render(data);
        document.getElementById("princ").innerHTML=tabHtml;
	put_datepicker();
	show_msg('table chargée');
    }

}
function show_msg(msg){
	$.notify(msg, "success");
}
function put_datepicker(){
	jQuery("input[name='date']").each(function(){
		$("#"+this.id).datepicker({dateFormat:"dd/mm/yy" });
	});
	jQuery("input[name='end_date']").each(function(){
		$("#"+this.id).datepicker({dateFormat:"dd/mm/yy" });
	});
	jQuery("input[name='last_date']").each(function(){
		$("#"+this.id).datepicker({dateFormat:"dd/mm/yy" });
	});
}
$(function(){put_datepicker();});
