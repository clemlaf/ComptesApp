function supprime(type,id){
    var param=[];
    param['type']=type;
    param['id']=id;
    var myForm = document.createElement("form");
    myForm.method="post" ;
    myForm.action = "./delparam" ;
    for (var k in param) {
	var myInput = document.createElement("input") ;
	myInput.setAttribute("name", k) ;
	myInput.setAttribute("value", param[k]);
	myForm.appendChild(myInput) ;
    }
    document.body.appendChild(myForm) ;
    myForm.submit() ;
    document.body.removeChild(myForm) ;
}
