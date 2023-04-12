var sHostName = "https://astro-online.ru";
var sCityID=0;
var sCityID2=0;
var sSelectorID=0;
var sSelectorID2=0;
var sSetCitiesCheck=false;
var sSetCitiesCheck2=false;
var sGMTFoundFlag=false;
var GMT2=0;
var E2=0;
var N2=0;
var N=0;
var E=0;
var GMT=0;
var aGMTQueryArray = [];
var aAjaxFlag = [];
var sGMTQuery='';
sCityLocalDBExistFlag=false;

archive=true;
archive_desk=true;

$(document).ready(function(){
    $("#a_loader").html("<img src="+sHostName+"/img/a_loader.gif width=1 height=1>&nbsp;");
});

function fSetLonLatGMT(sID, GMT, E, N, num, def) {

    sCityLocalDBExistFlag=false;

    for (var prop in aGMTQueryArray) {
	if (Object.prototype.hasOwnProperty.call(aGMTQueryArray, prop)) {
	    if (parseInt(aGMTQueryArray[prop].city.id)==parseInt(sID)) {
		sCityLocalDBExistFlag=true;
	    }
	}
    }

    if (sCityLocalDBExistFlag==false) {
        sGMTQuery = {
            city: {id: sID, gmt:GMT, e:E, n:N}
        }
	aGMTQueryArray.push(sGMTQuery);
    }

    var inputs = document.getElementsByTagName("input");
    var selects = document.getElementsByTagName("select");

    if (sID==1089) {
	if (def==2) {
	} else {
	    if (sGMT.length>0) {
		GMT=sGMT;
	    }
	    if (sN.length>0) {
		N=sN;
	    }
	    if (sE.length>0) {
		E=sE;
	    }
	    if (num>=2) {
		if (typeof sE2 !== 'undefined') {
		    if (sE2.length>0) {
			E=sE2;
		    }
		}
		if (typeof sN2 !== 'undefined') {
		    if (sN2.length>0) {
			N=sN2;
		    }
		}
		if (typeof sGMT2 !== 'undefined') {
		    if (sGMT.length>0) {
			GMT=sGMT2;
		    }
		}
	    }
	}
    }

	    for(var l = 0; l < selects.length; l++) {
		if(selects[l].type == "select-one") {
		    id_select = selects[l].getAttribute('id');
    			if (num<2) {
        		    if (id_select=='time_gmt') {
				selects[l].value = GMT;
			    }
			} else {
        		    if (id_select=='time_gmt_syn') {
				selects[l].value = GMT;
			    }
			}
		}
	    }

	    for(var i = 0; i < inputs.length; i++) {
    		if(inputs[i].type == "text") {
        	    id_input = inputs[i].getAttribute('id');
    			if (num<2) {
        		    if (id_input=='E') {
				inputs[i].value = E;
			    }
        		    if (id_input=='N') {
				inputs[i].value = N;
			    }
			} else {
        		    if (id_input=='E2') {
				inputs[i].value = E;
			    }
        		    if (id_input=='N2') {
				inputs[i].value = N;
			    }
			}
		}
	    } 
}

function fChangeCityNew(val, num, def=0) {
    var sYear = new Date().getFullYear();
    var id_input="";
    var id_select="";
    var inputs = document.getElementsByTagName("input");
    var selects = document.getElementsByTagName("select");
    var sSelectorID=0;
    sGMTFoundFlag=false;
    if (document.getElementById("frm_birth_year")) {
        sYear=document.getElementById("frm_birth_year").value;
    }
    if (document.getElementById("frm_birth_year2")) {
        sYear=document.getElementById("frm_birth_year2").value;
    }

    for (var prop in aGMTQueryArray) {
	if (Object.prototype.hasOwnProperty.call(aGMTQueryArray, prop)) {
	    if (parseInt(aGMTQueryArray[prop].city.id)==parseInt(val)) {
		sGMTFoundFlag=true;
		fSetLonLatGMT(val, aGMTQueryArray[prop].city.gmt, aGMTQueryArray[prop].city.e, aGMTQueryArray[prop].city.n, num, def);
	    }
	}
    }

    if (val=="1089") {
	fSetLonLatGMT(val, '0', '0', '0', num, def);
	sGMTFoundFlag=true;
    }

    if ((sGMTFoundFlag==false) && (aAjaxFlag[val]!==true)) {

    aAjaxFlag[val]=true;

    action_loader();
    $.blockUI({
	    message: '',
		showOverlay: true,
		overlayCSS: {
		backgroundColor: '#fff',
		opacity: 0.3
	    },
		    css: {
		backgroundColor: 'none',
		border: 'none'
	    }
    });

    $.ajax({
	url:sHostName+'/ajax/ajax.city.php', 
	dataType:'json',
	data:"city="+val+"&birthyear="+sYear,
	success: function(json) {

	fSetLonLatGMT(val, json.GMT, json.E, json.N, num, def);

	    $("#a_loader").html("&nbsp;");
	    $.unblockUI();
	    if (document.getElementById("dd_div")) {
		$("#dd_div").fadeOut(200);
		$("#dd_div").css("display","none");
	    }
	},
	error: function() {
	    $.unblockUI();
	}
    });
    }
}

function action_loader() {
    $("#a_loader").html("<img src=../img/a_loader.gif>&nbsp;");
}

function fBigCityNew(val,def=0) {
    sSetCitiesCheck=false;
    sSetCitiesCheck2=false;
    var inputs = document.getElementsByTagName("input");
    var selects = document.getElementsByTagName("select");

		    var sFillSelectsToBig=false;
		    var sFillSelectsToBig2=false;

		    if ((val==1) || (val==3)) {
			for(var k = 0; k < inputs.length; k++) {
    			    if ((inputs[k].type=="checkbox") && ((inputs[k].getAttribute('id')=='big_city')) && (val==1)) {
				if (inputs[k].className!==cid) {
            			    if (inputs[k].checked==false) {
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city')) {
						inputs[m].checked=true; 
						sFillSelectsToBig=true;
					    }
					}
				    } else { 
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city')) {
						inputs[m].checked=false; 
						sFillSelectsToBig=false;
					    }
					}
				    }
				} 
			    }
    			    if ((inputs[k].type=="checkbox") && ((inputs[k].getAttribute('id')=='big_city2')) && (val==3)) {
				if (inputs[k].className!==cid) {
            			    if (inputs[k].checked==false) {
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city2')) {
						inputs[m].checked=true; 
						sFillSelectsToBig2=true;
					    }
					}
				    } else { 
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city2')) {
						inputs[m].checked=false; 
						sFillSelectsToBig2=false;
					    }
					}
				    }
				}
			    }
			}
	    	    }

		    if ((val==0) || (val==2)) {
			for(var k = 0; k < inputs.length; k++) {
    			    if ((inputs[k].type=="checkbox") && (inputs[k].getAttribute('id')=='big_city') && (val==0)) {
				if (inputs[k].className!==cid) {
            			    if (inputs[k].checked==false) {
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city')) {
						inputs[m].checked=false; 
						sFillSelectsToBig=false;
					    }
					}
				    } else {
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city')) {
						inputs[m].checked=true; 
						sFillSelectsToBig=true;
					    }
					}
				    }
				}
			    }
    			    if ((inputs[k].type=="checkbox") && (inputs[k].getAttribute('id')=='big_city2') && (val==2)) {
				if (inputs[k].className!==cid) {
            			    if (inputs[k].checked==false) {
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city2')) {
						inputs[m].checked=false; 
						sFillSelectsToBig2=false;
					    }
					}
				    } else {
					for(var m = 0; m < inputs.length; m++) {
    					    if ((inputs[m].type=="checkbox") && (inputs[m].getAttribute('id')=='big_city2')) {
						inputs[m].checked=true; 
						sFillSelectsToBig2=true;
					    }
					}
				    }
				}
			    }
			}
		    }

			for(var l = 0; l < selects.length; l++) {
    				if(selects[l].type == "select-one") {
        			    var id_select = selects[l].getAttribute('id');
            			    if ((id_select=='frm_birth_city') && ((val==0) || (val==1))) {
					if (sFillSelectsToBig==true) {
					    selects[l].innerHTML=sCitiesBig;
					} else {
					    selects[l].innerHTML=sCities;
	    				}
					sSetCitiesCheck=true;
					sSelectorID=l;
					sCityID=selects[l].options[0].value;
				    }
            			    if ((id_select=='frm_birth_city2') && ((val==2) || (val==3))) {
					if (sFillSelectsToBig2==true) {
					    selects[l].innerHTML=sCitiesBig;
					} else {
					    selects[l].innerHTML=sCities;
	    				}
					sSetCitiesCheck2=true;
					sSelectorID2=l;
					sCityID2=selects[l].options[0].value;
				    }
				}
			}

		if (sSetCitiesCheck==true) {
            	    selects[sSelectorID].selectedIndex = 0;
			if (def>0) {
			    fChangeCityNew(def, 1);
			} else {
			    fChangeCityNew(sCityID, 1);
			}
		}
		if (sSetCitiesCheck2==true) {
            	    selects[sSelectorID2].selectedIndex = 0;
			if (def>0) {
			    fChangeCityNew(def, 2);
			} else {
			    fChangeCityNew(sCityID2, 2);
			}
		}
}

function fBigCity(val) {
    if (val==1) {
	if ($("#big_city").is(':checked')==false) {
	    $("#big_city").attr('checked', 'checked');
	} else {
	    $("#big_city").attr('checked', false);
	}
	if ($("#big_city").is(':checked')==false) {
	    $("#frm_birth_city").html(sCities);
	} else {
    	    $("#frm_birth_city").html(sCitiesBig);
	}
        change_city($("#frm_birth_city option:selected").val(), 1);
    } 
    if (val==3) {
	if ($("#big_city2").is(':checked')==false) {
	    $("#big_city2").attr('checked', 'checked');
	} else {
	    $("#big_city2").attr('checked', false);
	}
	if ($("#big_city2").is(':checked')==false) {
	    $("#frm_birth_city2").html(sCities);
	} else {
    	    $("#frm_birth_city2").html(sCitiesBig);
	}
        change_city($("#frm_birth_city2 option:selected").val(), 2);
    }
    if (val==0) {
	if ($("#big_city").is(':checked')==false) {
	    $("#frm_birth_city").html(sCities);
	} else {
    	    $("#frm_birth_city").html(sCitiesBig);
	}
        change_city($("#frm_birth_city option:selected").val(), 1);
    }
    if (val==2) {
	if ($("#big_city2").is(':checked')==false) {
	    $("#frm_birth_city2").html(sCities);
	} else {
    	    $("#frm_birth_city2").html(sCitiesBig);
	}
        change_city($("#frm_birth_city2 option:selected").val(), 2);
    }
}


function fAutoGMTNew(val) {
    cid = fCheckPage(0);
    var inputs = document.getElementsByTagName("input");
    for(var i = 0; i < inputs.length; i++) {
        if(inputs[i].type == "checkbox") {
            var id_input = inputs[i].getAttribute('id');
		if (val==1) {
            	    if (inputs[i].checked==false) {
			if (id_input=='frm_autogmt') {
			    inputs[i].checked = true; 
			}
		    } else {
			if (id_input=='frm_autogmt') {
			    inputs[i].checked = false; 
			}
		    }
		}
		if (val==2) {
            	    if (inputs[i].checked==false) {
			if (id_input=='frm_autogmt2') {
			    inputs[i].checked = true; 
			}
		    } else {
			if (id_input=='frm_autogmt2') {
			    inputs[i].checked = false; 
			}
		    }
		}
	}
    }
}

function fAutoGMT(val) {
    if (val==1) {
	if ($("#frm_autogmt").is(':checked')==false) {
	    $("#frm_autogmt").attr('checked', 'checked');
	} else {
	    $("#frm_autogmt").attr('checked', false);
	}
    } 
    if (val==2) {
	if ($("#frm_autogmt2").is(':checked')==false) {
	    $("#frm_autogmt2").attr('checked', 'checked');
	} else {
	    $("#frm_autogmt2").attr('checked', false);
	}
    } 
}

function fTabs(evt, cityName) {
  var i, tabcontent, tablinks;

  tabcontent = document.getElementsByClassName("natal_tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  tablinks = document.getElementsByClassName("natal_tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}