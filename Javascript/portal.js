
/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 */

// $Id: portal.js,v 1.46 2007/10/11 10:12:50 marc Exp $

// portal
var portalRefreshInterval = 10;
var currentPage = 0;

function startRefresh() {
    var fapp = null;
  if (window.name) var fapp = parent.document.getElementById(window.name);
  if ((!fapp)  || (fapp.style.display=='block')) {
    var dat = new Date();
    var mdat = dat.getTime();
    for (var is=0; is<services.length; is++) {
      if (services[is].nextLoad==-1 || (services[is].rdel>0 && services[is].nextLoad>0 && services[is].nextLoad<=mdat)) {
	services[is].nextLoad = 0;
	trace(services[is].stitle);
	loadSvcAsync(services[is].snum);
      } else {
      } 
    }
  }
  setTimeout("startRefresh()", portalRefreshInterval*1000);
}  

function startUtempo() {
  globalcursor('progress');
}

function endUtempo() {
  unglobalcursor();
}

function addNewService(sid) {
  startUtempo(); 
  var xreq = null;
  if (window.XMLHttpRequest) xreq = new XMLHttpRequest();
  else xreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (xreq) {
    xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=ADDSERVICE&sid="+sid, false);
    xreq.send('');
    if (xreq.status!=200) {
      trace('[TEXT:wd error add service] (HTTP Code '+xreq.status+')');	   
    } else { 
      eval(xreq.responseText);
      if (svcnum && svcnum>-1) {
	xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=GETJSSERVICE&snum="+svcnum, false);
	xreq.send('');
	if (xreq.status!=200) {
	  trace('[TEXT:wd error getting service] (HTTP Code '+xreq.status+')');	   
	} else { 
	  eval(xreq.responseText);
	  services[services.length] = svc;
	  displayServices();
	}
      } else {
	trace('[TEXT:wd invalid service number returned by creation]');	   
      }
    }
  } else {
    trace('[TEXT:wd error add service] (XMLHttpRequest contruction)');	   
  }
  endUtempo();
}



var colsDesc = new Array();
function orderServices() {

  for (var icol=0; icol<colCount; icol++) {
    colsDesc[icol] = new Array();
  }

  var lcol=0;
  var mm = '';
  for (var is=0; is<services.length; is++) {
    mm += '['+is+'] num='+services[is].snum;
    if (!services[is].col || services[is].col<0 || services[is].col>=colCount) services[is].col=0; 
    lcol = services[is].col;
    if (services[is].lin<=0) { 
      services[is].lin = colsDesc[lcol].length;
    }
    colsDesc[lcol][services[is].lin] = is;
    mm += ' col:'+services[is].col+' line='+services[is].lin+' ==> '+is+'\n';
  }
//    alert(mm);
  return;
}


function displayServices() {


  orderServices();
  for (var ic=0;ic<colCount; ic++) {
    for (var il=0;il<colsDesc[ic].length; il++) {
      if (colsDesc[ic][il]>=0) {
	showService(colsDesc[ic][il]);
      }
    }
  }

}



function showService(is, updates) {
  if (!services[is]) {
    trace('Internal error : no service defined ');
    return;
  }
  
  var snum = services[is].snum;
  if (document.getElementById('svc'+snum)) return; // Service already displayed

  var stitle = services[is].stitle;
  var vurl   = services[is].vurl;
  var eurl   = services[is].eurl;
  var iseditable   = services[is].e;
  var ismandatory  = services[is].m;
  var isinteractive  = services[is].i;
  var line  = services[is].lin;
  var col  = services[is].col;
  
  var root = document.getElementById('wdcol'+col);
  var rootw = getObjectWidth(root);
  if (root) {

    var svc = document.createElement('div');
    svc.id = 'svc'+snum;
    svc.setAttribute("svcid",is);
    svc.name = 'svc'+snum;
    svc.className = 'ui-widget ui-corner-all ui-widget-content wdsvc';
    if (!isNetscape)  {
      svc.style.display = 'block';
      svc.style.width = '100%';
    }
//     if (!isNetscape) svc.style.width = parseInt(rootw) - 6;
    root.appendChild(svc);
    addEvent(svc,'mouseover',mouseOverService);  
  
    var tsvc = document.createElement('div');
    tsvc.id = 'tsvc'+snum;
    tsvc.name = 'tsvc'+snum;
    
    var cnt = '';
    var imgcyc = '';
    if (services[is].rdel>0) {
      imgcyc = '<img src="[IMGF:wd_svc_cyclic.gif:0,0,0|COLOR_BLACK]" style="border:0px" title="[TEXT:automatic reload all] '+services[is].rdel+' minutes">';
    }
    cnt += '<table cellspacing="0" cellpadding="0" >';
    cnt += '<tr style="cursor:move;" onmousedown="return startMoveService(event, this, '+snum+');" onmouseup="endMoveService(event,'+snum+')"  onmouseover="mOverSvcTitle('+snum+')" onmouseout="mOutSvcTitle('+snum+')" onDblClick="showHideSvc(event, '+snum+',true); return false;">';
    cnt += '<td >';
      cnt += '<span id="ivsvc'+snum+'" class="ui-icon-left ui-icon ui-icon-pin-s widgetopenfullscreen small_button" open="'+(services[is].open?'1':'0')+'" onclick="showHideSvc(event, '+snum+',true); return false;" title="[TEXT:wd hide svc content]"></span>';
    cnt += '<span id="tsvcti'+snum+'">'+stitle+'</span> '+imgcyc+'</td>';
 
    cnt += '<td nowrap style="text-align:right">';

    cnt += '<span id="iconbox'+snum+'" class="wd-svc-icon-box">';

    if (!ismandatory)
	  cnt += '<span class="ui-icon-right ui-icon ui-icon-close widgetopenfullscreen small_button" onclick="deleteSvc(event, '+snum+'); return false" " title="[TEXT:wd delete svc]"></span>';
    if (eurl!='' && iseditable)
	  cnt += '<span id="iesvc'+snum+'" class="ui-icon-right  ui-icon ui-icon-wrench widgetopenfullscreen small_button" onclick="return editSvc(event, '+snum+');" " title="[TEXT:wd edit svc content]"></span>';
      if (vurl!='') 
	  cnt += '<span class="ui-icon-right  ui-icon ui-icon-refresh widgetopenfullscreen small_button" onclick="startUtempo( ); loadSvcAsync('+snum+');endUtempo(); " title="[TEXT:wd reload svc content]"></span>';
    cnt += '</span>';
    cnt += '</td></tr></table>';
    tsvc.innerHTML = cnt;
    tsvc.className = 'ui-widget-header ui-corner-all wdsvc_title';

    svc.appendChild(tsvc);
    
    var csvc = document.createElement('div');
    csvc.setAttribute('id','csvc'+snum);
    csvc.name = 'csvc'+snum;
    if (vurl=='') {
      csvc.innerHTML = '[TEXT:wd url for retrieving information not given]';
      csvc.className = 'ui-widget-content wdsvc_content wdsvc_warning';
    } else {
      csvc.innerHTML = '[TEXT:downloading content in progress...]';
      csvc.className = 'ui-widget-content wdsvc_content';
    }
    //    csvc.style.overflow = 'auto';

    if (isinteractive) {
      var fsvc = document.createElement('form');
      fsvc.id = 'fsvc'+snum;
      fsvc.name = 'fsvc'+snum;
      fsvc.style.display = 'inline';
      fsvc.method = 'POST';
      fsvc.action = vurl;
      fsvc.serviceId = snum;
      fsvc.onsubmit = function (event)  {
	submitService(event); 
	return false; 
      } ;
      svc.appendChild(fsvc);
      fsvc.appendChild(csvc);
    } else {
      svc.appendChild(csvc);
    }

    if (!services[is].open) {
      document.getElementById('csvc'+snum).style.display = 'none';
//      document.getElementById('ivsvc'+snum).src = '[IMGF:wd_svc_show.gif:0,0,0|COLOR_BLACK]';
      document.getElementById('ivsvc'+snum).title = '[TEXT:wd show svc content]';
    }

    var head = document.getElementsByTagName("head")[0];
    if (services[is].jslink!=''&& !document.getElementById('fref_'+services[is].jslinkmd5)) {
      script = document.createElement('script');
      script.id = 'fref_'+services[is].jslinkmd5;
      script.type = 'text/javascript';
      script.src = services[is].jslink;
      head.appendChild(script);
    }
    if (services[is].csslink!=''&& !document.getElementById('fref_'+services[is].csslinkmd5)) {
      script = document.createElement('link');
      script.id = 'fref_'+services[is].csslinkmd5;
      script.type = 'text/css';
      script.rel = 'stylesheet';
      script.href = services[is].csslink;
      head.appendChild(script);
    }
//     loadSvcAsync(snum);
  }
}



function submitService(event) {
  if (!event) event=window.event;
  var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);
  if (e.nodeName!="FORM") e = e.form;
  if (!e) return false;
  var snum = e.serviceId;
  var is = getSvc(snum);
  if (is===false) return false; 

  var params = ''
  var fsend = document.getElementById('fsvc'+snum);
  for (var ie=0; ie<fsend.elements.length; ie++) {
    if (fsend.elements[ie].name != "") {// params += '&'+fsend.elements[ie].name+'='+encodeURI(fsend.elements[ie].value);
    	var elmt = fsend.elements[ie];
		if(elmt.type == "select-multiple"){
			var nextparams = '';
			for( var i in elmt.options ){
				if(elmt.options[i].selected){
					nextparams += (nextparams==''?'':'&')+elmt.name+'='+encodeURIComponent(elmt.options[i].value);
				}
			}
		} else {
			var nextparams = elmt.name+'='+encodeURIComponent(elmt.value)
		}
	    params += '&'+nextparams;
		}
  }
  loadSvcAsync(snum,  params);
  return false;
}

function getSvc(snum) {
  for (var ix=0; ix<services.length; ix++) {
    if (services[ix].snum == snum) return ix;
  }
  return false;
}

var ereq = null;
var editSnum = -1;

function reloadEditSvc(event) {
  var snum=editSnum;
  sendForm();
  editSvc(event,snum );
}


function editSvc(event, snum) {

  cancelForm(); 
  if (snum==editSnum)  return;

  var is = getSvc(snum);
  if (is===false) return;

  if (services[is].eurl=='') {
    document.getElementById('editsvc_c').innerHTML = '[TEXT:edition url not given]';
    return;
  }

  // initialize form
  trace('Modification des paramètres...');
  editSnum = snum;

  var esvc = document.getElementById('editsvc').cloneNode(true);
  esvc.id = 'editsvc'+snum;
  esvc.style.top = esvc.style.left = 0; 
  esvc.style.display = 'block';
  document.getElementById('tsvc'+snum).appendChild(esvc);
  document.getElementById('editsvc_c').innerHTML = '[TEXT:wd loading edition forms]';
  if (services[is].eurl!='') {
    if (window.XMLHttpRequest) ereq = new XMLHttpRequest();
    else ereq = new ActiveXObject("Microsoft.XMLHTTP");
    if (ereq) {
      ereq.open("POST", services[is].eurl+'&'+services[is].purl, false);
      ereq.send('');
      if (ereq.status!=200) {
	document.getElementById('editsvc_c').innerHTML = '[TEXT:wd error retrieving edit form] (HTTP Code '+ereq.status+')';	   
      } else { 
 	document.getElementById('editsvc_c').innerHTML = '<div>'+ereq.responseText+'</div>';
	
	if (services[is].purl != '') {
		var tpurl = services[is].purl.split('&');
		var fedit = document.getElementById('editsvcf');
		for (var ie = 0; ie < fedit.elements.length; ie++) {
			for (var ip = 0; ip < tpurl.length; ip++) {
				if (tpurl[ip] != '') {
					var thisp = tpurl[ip].split('=');
		    		if (fedit.elements[ie].name == thisp[0]) {
						var thispv = decodeURIComponent(thisp[1]);
						if (fedit.elements[ie].type == 'select-multiple'){
							for (var io = 0; io < fedit.elements[ie].options.length; io++) {
								if(fedit.elements[ie].options[io].value == thispv){
									fedit.elements[ie].options[io].selected = true;
									continue;
								}
							}
						} else {
							fedit.elements[ie].value = thispv;
						}
					}
				}
			}
		}
	}
      }
    } else {
      document.getElementById('editsvc_c').innerHTML = '[TEXT:wd error retrieving edit form] (XMLHttpRequest contruction)';	    
    }
  }
  if (event) stopPropagation(event);
  return false;
}

function cancelForm() {
  if (editSnum===-1) return;
  var snum = editSnum;
  var is = getSvc(snum);
  if (is===false) return;
  var fedit = document.getElementById('editsvc'+snum);
  fedit.parentNode.removeChild(fedit);
  editSnum = -1;
  return false;
}

function sendForm() {
  trace('Sauvegarde des paramètres...');
  var fedit = document.getElementById('editsvcf');
  if (editSnum===-1) return;
  var snum = editSnum;

  var is = getSvc(snum);
  if (is===false) return;

  var purl = '';
  for (var ie=0; ie<fedit.elements.length; ie++) {
  	var elmt = fedit.elements[ie];
  	if(elmt.type == "select-multiple"){
		var nextpurl = '';
		for( var i in elmt.options ){
			if(elmt.options[i].selected){
				nextpurl += (nextpurl==''?'':'&')+elmt.name+'='+encodeURIComponent(elmt.options[i].value);
			}
		}
	} else {
		var nextpurl = elmt.name+'='+encodeURIComponent(elmt.value)
	}
    purl += (purl==''?'':'&')+nextpurl;
  }
  if (window.XMLHttpRequest) ereq = new XMLHttpRequest();
  else ereq = new ActiveXObject("Microsoft.XMLHTTP");
  if (ereq) {
    globalcursor('progress');
//     alert(editSnum);
    ereq.open("POST", "[CORE_STANDURL]&app=WEBDESK&action=SAVESVC&snum="+editSnum, false);
    ereq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ereq.send(purl);
    if (ereq.status!=200) {
      document.getElementById('csvc'+snum).innerHTML = '[TEXT:wd error saving edit form] (HTTP Code '+ereq.status+')';	   
    } else { 
      services[is].purl = purl;
      loadSvcAsync(snum);
    }
    unglobalcursor();
  } else {
    document.getElementById('csvc'+snum).innerHTML = '[TEXT:wd error saving edit form] (XMLHttpRequest contruction)';	    
  }
  var fedit = document.getElementById('editsvc'+snum);
  fedit.parentNode.removeChild(fedit);
  editSnum = -1;
}


function openAllSvc() {
  for (var ix=0; ix<services.length; ix++) {
    if (!services[ix].open) showHideSvc(false, services[ix].snum, false);
  }
  return false;
}
function closeAllSvc() {
  for (var ix=0; ix<services.length; ix++) {
    if (services[ix].open) showHideSvc(false, services[ix].snum, false);
  }
  return false;
}

function showHideSvc(event, sid, savegeo) {
  var is = getSvc(sid);
  if (is===false) return;
  if (document.getElementById('csvc'+sid)) {
    if (services[is].open) {
      document.getElementById('csvc'+sid).style.display = 'none';
      document.getElementById('ivsvc'+sid).title = '[TEXT:wd show svc content]';
      services[is].open = false;
    } else {
      document.getElementById('csvc'+sid).style.display = 'block';
      document.getElementById('ivsvc'+sid).title = '[TEXT:wd hide svc content]';
      services[is].open = true;
    }
    if (savegeo) saveGeometry();
  }
  if (event) stopPropagation(event);
}

function saveGeometry() {
  var geo = '';
  for (var ic=0; ic<colCount; ic++) { 
    var curcol = document.getElementById('wdcol'+ic);
    var rsvc = 0;
    for (var id=0; id<curcol.childNodes.length; id++) {
      if (curcol.childNodes[id].nodeName=='DIV' && curcol.childNodes[id].getAttribute('svcid')) {
        var svcid = curcol.childNodes[id].getAttribute('svcid');
	geo += (geo==''?'':'|')+services[svcid].snum+':'+ic+':'+id+':'+(services[svcid].open?"1":0);
      }
    }
  }
  var xreq = null;
  if (window.XMLHttpRequest) xreq = new XMLHttpRequest();
  else xreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (xreq) {
    xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=GEOSERVICE&sgeo="+geo, false);
     xreq.send('');
     if (xreq.status!=200) trace('[TEXT:wd error geo service] (HTTP Code '+xreq.status+')');	   
  } else {
    trace('[TEXT:wd error geo service] (XMLHttpRequest contruction)');	   
  }
  return;
}


function unDisplaySvc(snum) {
  var is = getSvc(snum);
  if (is===false) return;
  if (document.getElementById('svc'+snum)) {
    var svc = document.getElementById('svc'+snum);
    svc.parentNode.removeChild(svc);
    services[is].display = false;

  }
}


function deleteSvc(event, snum) {
  var is = getSvc(snum);
  if (is===false) return;
  if (!confirm('[TEXT:wd confirm supress of] ['+services[is].stitle+']')) return false;
  trace('Service '+services[is].stitle+' supprimé.');
  unDisplaySvc(snum);
  services.splice(is,1);
  var xreq = null;
  if (window.XMLHttpRequest) xreq = new XMLHttpRequest();
  else xreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (xreq) {
    xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=DELSERVICE&snum="+snum, false);
    xreq.send('');
    if (xreq.status!=200) trace('[TEXT:wd error add service] (HTTP Code '+xreq.status+')');	   
  } else {
    trace('[TEXT:wd error add service] (XMLHttpRequest contruction)');	   
  }
  if (event) stopPropagation(event);
}

function setWS(sid) {
  var value = 40;
  if (document.getElementById(sid)) {
    var o = document.getElementById(sid);
    o.style.opacity = value/100;
    o.style.filter = 'alpha(opacity=' + value + ')';
  }
}
function unsetWS(sid) {
  var value = 100;
  if (document.getElementById(sid)) {
    var o = document.getElementById(sid);
    o.style.opacity = value/100;
    o.style.filter = 'alpha(opacity=' + value + ')';
  }
}




function loadSvcAsync(sid, params) {
  var dreq = null;
  var is = getSvc(sid);
  if (is===false) return;

  if (services[is].vurl=='') return;

  if (window.XMLHttpRequest) dreq = new XMLHttpRequest();
  else dreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (dreq) {
    trace('Mise à jour de '+services[is].stitle+'...');
    setWS('svc'+sid);
    dreq.onreadystatechange =  function() {
      if (dreq.readyState == 4) {
	try {
	  if (dreq.status!=200) {
	    document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (HTTP Code '+dreq.status+')';	   
	  } else { 
	    var isxml = false;
	    if (dreq.responseXML) {
	      var elts = dreq.responseXML.getElementsByTagName("freedomsvc");
	      if ((elts.length>0) && (typeof elts[0] == "object")) {
		var elts = dreq.responseXML.getElementsByTagName("freedomsvc");
		var uptime = elts[0].getAttribute("uptime");
		var title = elts[0].getAttribute("title");
 		if (title) document.getElementById('tsvcti'+sid).innerHTML = title;
 		if (uptime) document.getElementById('tsvcti'+sid).title = 'Mise à jour : '+uptime;
 		document.getElementById('csvc'+sid).innerHTML = '<div style="padding:0; margin:0; display:block; border:0px; width:100%;">'+elts[0].firstChild.nodeValue+'</div>';
		isxml = true;
	      }
	    } 
	    
	    if (!isxml) {
	      trace('no valid XML content received\n'+dreq.responseText);
	    }
	    
	    if (services[is].rdel>0) {
	      var dat = new Date();
	      services[is].nextLoad = dat.getTime() + (services[is].rdel*60*1000);
	    } else {
	      timerOn[is] = -1;
	    }	    
	  }
	  unsetWS('svc'+sid);
	} catch(e) {
	  //          alert('Exception : ' + e);
	}
      }
    }
    var url = services[is].vurl ;
    var purl = services[is].purl;
    if (params) purl += params;
    dreq.open("POST", url, true);
    dreq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    dreq.send(purl);
  } else {
    document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (XMLHttpRequest contruction)';	    
  }
}




var timerOn = new Array();




function loadSvcSync(sid, shl, params) {
  var dreq = null;
  var is = getSvc(sid);
  if (is===false) return;

  if (services[is].vurl=='') return;

  if (window.XMLHttpRequest) dreq = new XMLHttpRequest();
  else dreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (dreq) {
    trace('Mise à jour (S) de '+services[is].stitle+'...');
    setWS('svc'+sid);

    var url = services[is].vurl ;
    var purl = services[is].purl;
    if (params) purl += params;
    dreq.open("POST", url, false);
    dreq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    dreq.send(purl);
    if (dreq.status!=200) {
      document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (HTTP Code '+dreq.status+')';	   
    } else { 
      var isxml = false;
      if (dreq.responseXML) {
	var elts = dreq.responseXML.getElementsByTagName("freedomsvc");
	if ((elts.length>0) && (typeof elts[0] == "object")) {
	  var elts = dreq.responseXML.getElementsByTagName("freedomsvc");
	  var uptime = elts[0].getAttribute("uptime");
	  var title = elts[0].getAttribute("title");
	  if (title) document.getElementById('tsvcti'+sid).innerHTML = title;
	  if (uptime) document.getElementById('tsvcti'+sid).title = 'Mise à jour : '+uptime;
	  document.getElementById('csvc'+sid).innerHTML = '<div style="padding:0; margin:0; display:block; border:0px; width:100%;">'+elts[0].firstChild.nodeValue+'</div>';
	  isxml = true;
	}
      } 
      
      if (!isxml) {
	trace('no valid XML content received\n'+dreq.responseText);
      }
      
      if (services[is].rdel>0) {
	var dat = new Date();
	services[is].nextLoad = dat.getTime() + (services[is].rdel*60*1000);
      } else {
	timerOn[is] = -1;
      }	    
    }
    unsetWS('svc'+sid);
  } else {
    document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (XMLHttpRequest contruction)';	    
  }
}

    

var tags = new Array( 'div', 'span', 'td','tr','p','b','table','strong','emphasis','a','h1','h2','h3','pre','sub','sup','i','th','cp','ul','ol','li','dt','dd');
function fontSizer(inc) {
  if (!document.getElementById) return;
  var size = initSize;
  size += parseInt(inc);
  initSize = size;
  getBody = document.getElementsByTagName('body')[0];
  for (i = 0 ; i < tags.length ; i++ ) {
    getallTags = getBody.getElementsByTagName(tags[i]);
    for (k = 0 ; k < getallTags.length ; k++)
      getallTags[k].style.fontSize = size+'pt';
  }
}



function mOverSvcTitle(snum) {
  showSvcIcons(snum);
}
function mOutSvcTitle(snum) {
  hideSvcIcons(snum);
}


var svcIconsDisplayed = -1;

function showSvcIcons(snum) {
  if (snum==svcIconsDisplayed) return;
  if (snum>-1) hideSvcIcons(snum);
  if (document.getElementById('iconbox'+snum)) document.getElementById('iconbox'+snum).style.visibility = 'visible';
  svcIconsDisplayed = snum;
}
function hideSvcIcons(snum) {
  if (document.getElementById('iconbox'+snum)) document.getElementById('iconbox'+snum).style.visibility = 'hidden';
  svcIconsDisplayed = -1;
}


// Move service
// -----------------------------

var svcMove = '';
var overElt = null;

function startMoveService(event, elt, snum) {
  globalcursor('move');
  addEvent(document,'mousemove',mouseMoveService);  
  addEvent(document,'mouseup',endMoveService);  
  stopPropagation(event);
  svcMove = 'svc'+snum;

  var svccur = document.getElementById(svcMove);
  var xy = getAnchorPosition('svc'+snum);
  var h=getObjectHeight(svccur);
  var w=getObjectWidth(svccur);
 
  setWS(svcMove);
  overElt = null;

  return false;
}

function endMoveService(event) {
  event || (event = window.event);
  var srcel = (event.target) ? event.target : event.srcElement;
  if (svcMove!='') {
    var svccur = document.getElementById(svcMove);
    unsetWS(svcMove);
    if (overElt!=null) {
      var svccur = document.getElementById(svcMove);
      if (overElt.nodeName=='TD') overElt.appendChild(svccur);
      else overElt.parentNode.insertBefore(svccur, overElt);
      stopPropagation(event);
      saveGeometry();
    }
    svccur.style.display = 'block';
    svcMove = '';
    overElt = null;
    delEvent(document,'mouseup',endMoveService);
    stopPropagation(event);
  }
  unglobalcursor();
}

function mouseMoveService(event) {
  return false;
}

function mouseOverService(event) {
  event || (event = window.event);
  var srcel = (event.target) ? event.target : event.srcElement;
  var srcorg = srcel;
  var efound = false;
  var foundCol = -1;
//   if (svcMove!='' && srcel.id!='ghost' && srcel.id!=svcMove) {
   if (svcMove!='' && srcel.id!=svcMove) {
    var emove = document.getElementById(svcMove);
    while (!efound && srcel.nodeName!='BODY') {
     if (srcel.getAttribute('svcid')) {
	efound = true;
	overElt = srcel;
	stopPropagation(event);
	srcel.parentNode.insertBefore(emove, srcel);
     }else {
       if (srcel.getAttribute('wdcol')) foundCol = srcel.getAttribute('wdcol');
     }
     srcel = srcel.parentNode;
    }
    if (foundCol>-1 && !efound) {
      //      trace('foundCol='+foundCol+' efound='+efound);
      overElt = document.getElementById('wdcol'+foundCol);
      stopPropagation(event);
      overElt.appendChild(emove);
    }
  }
}



var traceTempo = -1;
function trace(tt) {
  if (document.getElementById('trace')) {
    closeTrace();
    document.getElementById('trace').innerHTML = tt;
    traceTempo = setTimeout("closeTrace()", 5000);
    document.getElementById('trace').style.display = 'block';    
    document.getElementById('trace').style.top = '5px';    
 }
}
function closeTrace() {
  if (traceTempo!=-1) clearTimeout(traceTempo);
  document.getElementById('trace').style.display = 'none';
  menuTempo = -1;
}
  
    


// ----- Sub service menu 
function openSubService(event, elt, mid) {
  closeSubService();
  if (!document.getElementById(mid)) return;
  menuId = mid;
  var os = getAnchorPosition(elt.id);
  var h = getObjectHeight(elt);
  var w = getObjectWidth(elt);
  document.getElementById(mid).style.left = os.x + (w/2);
  document.getElementById(mid).style.top = os.y - (h+h/2);
  document.getElementById(mid).style.display = 'inline';
  return;
}
var menuId = '';
var menuTempo = -1;

function tempoCloseSubService(event, elt, mid) {
  if (!document.getElementById(mid)) return;
  if (mid!='' && mid!=menuId) closeSubService();
  menuId = mid;
  menuTempo = self.setTimeout("closeSubService()", 500);
}

function closeSubService() {
  if (!document.getElementById(menuId)) return; 
  document.getElementById(menuId).style.display = 'none';
  menuId = '';
  menuTempo = -1;
  return;
}

function unsetTempoCloseSubService(event, mid) {
  if (menuId==mid && menuTempo!=-1) clearTimeout(menuTempo);
  menuTempo = -1;
}


// Resizing columns
var colResize = -1;
var colElt = null;
var pWidth = 0; 
var cWidth = new Array();
var cLeft = new Array();
function initResizePortalCol(event, ncol) {
  event || (event = window.event);
  var srcel = (event.target) ? event.target : event.srcElement;
  if (colResize==-1) {
    srcel.style.borderRight = '3px outset [COLOR_A8]';
    globalcursor('ew-resize');
  }
  return false;
}

function startResizePortalCol(event, ncol) {
  event || (event = window.event);
  var srcel = (event.target) ? event.target : event.srcElement;

  srcel.style.borderRight = '3px outset [COLOR_A8]';
  globalcursor('ew-resize');

  colResize = ncol;
  colElt = document.getElementById('wdcol'+ncol);

  pWidth = getFrameWidth();

  for (var icol=0; icol<colCount; icol++) {
    cWidth[icol] = getObjectWidth(document.getElementById('wdcol'+icol));
    cLeft[icol]  = getAnchorPosition('wdcol'+icol).x;
  }

  addEvent(document,'mousemove',mouseResizeCols);  
  addEvent(document,'mouseup',endResizeCols);  
  stopPropagation(event);

  return;
}

function mouseResizeCols(event) {
  var r = 1.0;
  if (colResize>=0) {
    GetXY(event);

    if (Xpos>cLeft[colResize] && Xpos<pWidth) {

     var dv = Xpos - (cLeft[colResize]+cWidth[colResize]);
      var nw = new Array();

      var oldW = pWidth - cWidth[colResize];
      var newW = pWidth - (cWidth[colResize] + dv);
      
      nw[colResize] = parseInt(100 / (pWidth / (cWidth[colResize] + dv)));
      for (var icol=(colResize+1); icol<colCount; icol++) {
	var r = 1.0 * (oldW / cWidth[icol]);
	nw[icol] = parseInt(100 / (pWidth / (newW / r)));
      }
      for (var icol=colResize; icol<colCount; icol++) {
 	document.getElementById('wdcol'+icol).style.width = nw[icol]+'%';
 	cWidth[icol] = getObjectWidth(document.getElementById('wdcol'+icol));
      }
    }
  }
  return false;
}

function resetColsSize() {
  
  pWidth = getFrameWidth();
  var colw = parseInt(100/(pWidth/(pWidth / colCount)));
  var pval = pages[currentPage].name+'|';
  for (var icol=0; icol<colCount; icol++) {
    document.getElementById('wdcol'+icol).style.width = colw+'%';
    cWidth[icol] = getObjectWidth(document.getElementById('wdcol'+icol));
    pval += colw;
    if (icol<(colCount-1)) pval+=':';
  }
  setparamu('WEBDESK', 'WDK_PORTALSPEC', pval);
}

function endResizeCols(event) {
  event || (event = window.event);
  var srcel = (event.target) ? event.target : event.srcElement;
  if (colResize>=0) {
    document.getElementById('handcol'+colResize).style.borderRight = '3px solid [COLOR_A9]';
    delEvent(document,'mouseup',endResizeCols);
    var pval = pages[currentPage].name+'|';
    for (var icol=0; icol<colCount; icol++) {
      pval += parseInt(100/(pWidth/cWidth[icol]));
      if (icol<(colCount-1)) pval += ':';
    }
    unglobalcursor();
    colElt = null;
    colResize = -1;
    setparamu('WEBDESK', 'WDK_PORTALSPEC', pval);
  }
  return false;
}
function endResizePortalCol(event, ncol) {
  event || (event = window.event);
  var srcel = (event.target) ? event.target : event.srcElement;
  if (colResize==-1) {
    srcel.style.borderRight = '3px solid [COLOR_A9]';
    unglobalcursor();
  }
  return false;
}
