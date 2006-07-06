// portal

function startRefresh() {
  var  sl = '';
  var dat = new Date();
  var mdat = dat.getTime();
  sl = '('+mdat+') ';
  for (var is=0; is<services.length; is++) {
    sl += '\n'+services[is].title+'('+services[is].rdel+')'+':'+services[is].nextLoad+' > ';
    if (services[is].rdel>0 && services[is].nextLoad>0 && services[is].nextLoad<=mdat) {
      services[is].nextLoad == 0;
      loadSvcAsync(services[is].snum, true);
      sl += 'reload';
    } else {
      sl += 'no';
    } 
  }
  setTimeout("startRefresh()", 60*1000);
}  

function startUtempo() {
  globalcursor('progress');
}

function endUtempo() {
  unglobalcursor();
}

function resizeCols() {
  var icol = 0;
  var bodW = getFrameWidth() - 20;
  var colW = parseInt(bodW/colCount);
  if (isIE) colW -= (colCount*3);
  for (icol=0; icol<colCount; icol++) {
    
    document.getElementById('wdcol'+icol).style.width = colW;
    document.getElementById('wdcol'+icol).style.display = 'block';
  }
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
      alert('[TEXT:wd error add service] (HTTP Code '+xreq.status+')');	   
    } else { 
      eval(xreq.responseText);
      if (svcnum && svcnum>-1) {
	xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=GETJSSERVICE&snum="+svcnum, false);
	xreq.send('');
	if (xreq.status!=200) {
	  alert('[TEXT:wd error getting service] (HTTP Code '+xreq.status+')');	   
	} else { 
	  eval(xreq.responseText);
	  services[services.length] = svc;
	  displayServices();
	}
      } else {
	alert('[TEXT:wd invalid service number returned by creation]');	   
      }
    }
  } else {
    alert('[TEXT:wd error add service] (XMLHttpRequest contruction)');	   
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
//   alert(mm);
//   var text = '';
//   for (var ic=0;ic<colCount; ic++) {
//     text += '['+ic+'] = {';
//     for (var il=0;il<colsDesc[ic].length; il++) {
//       if (services[colsDesc[ic][il]])
// 	text += ' '+colsDesc[ic][il]+':'+services[colsDesc[ic][il]].col+':'+services[colsDesc[ic][il]].lin;
//     }
//     text += ' }\n';
//   }
//   alert(text);
  return;
}


function displayServices() {


  orderServices();

  for (var ic=0;ic<colCount; ic++) {
    for (var il=0;il<colsDesc[ic].length; il++) {
      if (colsDesc[ic][il]) {
	showService(colsDesc[ic][il]);
      }
    }
  }

}



function showService(is) {

  if (!services[is]) {
    alert('Internal error : no service defined ');
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
  if (root) {

    var svc = document.createElement('div');
    svc.id = 'svc'+snum;
    svc.name = 'svc'+snum;
    svc.className = 'wdsvc';
    root.appendChild(svc);

      
      
    var tsvc = document.createElement('div');
    tsvc.id = 'tsvc'+snum;
    tsvc.name = 'tsvc'+snum;
    
    var cnt = '';
    var imgcyc = '';
    if (services[is].rdel>0) {
      imgcyc = '<img src="[IMG:wd_svc_cyclic.gif]" style="border:0px" title="[TEXT:automatic reload all] '+services[is].rdel+' minutes">';
    }
    cnt += '<table cellspacing="0" cellpadding="0" style="width:100%; border:0px">';
    cnt += '<tr onclick="showHideSvc('+snum+', true);" onmouseover="showSvcIcons('+snum+')" onmouseout="hideSvcIcons('+snum+')">';
    cnt += '<td><span id="tsvcti'+snum+'">'+stitle+'</span> '+imgcyc+'</td>';
 
    cnt += '<td style="text-align:right">';
    cnt += '<span id="iconbox'+snum+'" style="visibility:hidden">';

    cnt += '<img id="gotoL'+snum+'" style="display:none" class="small_button" onclick="moveSvc('+snum+',-1,0)" src="[IMG:wd_go_left.gif]" title="[TEXT:wd go left]">';
    cnt += '<img id="gotoD'+snum+'" style="display:none" class="small_button" onclick="moveSvc('+snum+',0,1)" src="[IMG:wd_go_down.gif]" title="[TEXT:wd go down]">';
    cnt += '<img id="gotoU'+snum+'" style="display:none" class="small_button" onclick="moveSvc('+snum+',0,-1)" src="[IMG:wd_go_up.gif]" title="[TEXT:wd go up]">';
    cnt += '<img id="gotoR'+snum+'" style="display:none" class="small_button" onclick="moveSvc('+snum+',1,0)" src="[IMG:wd_go_right.gif]" title="[TEXT:wd go right]">';
    cnt += '&nbsp;';
    cnt += '<img id="ivsvc'+snum+'" style="margin-left:2px" class="small_button" onclick="showHideSvc('+snum+',true);" src="[IMG:wd_svc_hide.gif]" title="[TEXT:wd hide svc content]">';
    if (vurl!='')
      cnt += '<img id="irsvc'+snum+'" style="margin-left:2px" class="small_button" onclick="startUtempo(); loadSvcAsync('+snum+', true);endUtempo(); " src="[IMG:wd_svc_reload.gif]" title="[TEXT:wd reload svc content]">';
    if (eurl!='' && iseditable)
      cnt += '<img id="iesvc'+snum+'" style="margin-left:2px" class="small_button" onclick="editSvc('+snum+');" src="[IMG:wd_svc_edit.gif]" title="[TEXT:wd edit svc content]">';
    if (!ismandatory)
      cnt += '<img id="idsvc'+snum+'" style="margin-left:2px" class="small_button" onclick="deleteSvc('+snum+');" src="[IMG:wd_svc_delete.gif]" title="[TEXT:wd delete svc]">';
    cnt += '</span>';
    cnt += '</td></tr></table>';
    tsvc.innerHTML = cnt;
    tsvc.className = 'wdsvc_title';

    svc.appendChild(tsvc);
    computeMoveIconV(is);
    
    var csvc = document.createElement('div');
    csvc.setAttribute('id','csvc'+snum);
    csvc.name = 'csvc'+snum;
    if (vurl=='') {
      csvc.innerHTML = '[TEXT:wd url for retrieving information not given]';
      csvc.className = 'wdsvc_content wdsvc_warning';
    } else {
      csvc.className = 'wdsvc_content';
    }
    csvc.style.overflow = 'auto';

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
      document.getElementById('ivsvc'+snum).src = '[IMG:wd_svc_show.gif]';
      document.getElementById('ivsvc'+snum).title = '[TEXT:wd show svc content]';
    }

    loadSvcAsync(snum);
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
    if (fsend.elements[ie].name!="") params += '&'+fsend.elements[ie].name+'='+escape(fsend.elements[ie].value);
  }

  loadSvcAsync(snum, true, params);
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
function editSvc(snum) {

  if (snum==editSnum) return;

  var is = getSvc(snum);
  if (is===false) return;

  if (services[is].eurl=='') {
    document.getElementById('editsvc_c').innerHTML = '[TEXT:edition url not given]';
    return;
  }

  // initialize form
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
      ereq.open("POST", services[is].eurl, false);
      ereq.send('');
      if (ereq.status!=200) {
	document.getElementById('editsvc_c').innerHTML = '[TEXT:wd error retrieving edit form] (HTTP Code '+ereq.status+')';	   
      } else { 
 	document.getElementById('editsvc_c').innerHTML = '<div>'+ereq.responseText+'</div>';
	if (services[is].purl!='') {
	  var tpurl = services[is].purl.split('&');
	  var fedit = document.getElementById('editsvcf');
	  for (var ie=0; ie<fedit.elements.length; ie++) {
	    for (var ip=0; ip<tpurl.length; ip++) {
	      if (tpurl[ip]!='') {
		var thisp = tpurl[ip].split('=');
		if (fedit.elements[ie].name==thisp[0]) {
		  fedit.elements[ie].value = thisp[1];
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
}

function cancelForm() {
  if (editSnum===-1) return;
  var snum = editSnum;
  var is = getSvc(snum);
  if (is===false) return;
  var fedit = document.getElementById('editsvc'+snum);
  fedit.parentNode.removeChild(fedit);
  editSnum = -1;
}

function sendForm() {
  var fedit = document.getElementById('editsvcf');
  if (editSnum===-1) return;
  var snum = editSnum;

  var is = getSvc(snum);
  if (is===false) return;

  var purl = '';
  for (var ie=0; ie<fedit.elements.length; ie++) {
    purl += '&'+fedit.elements[ie].name+'='+fedit.elements[ie].value;
  }
  if (window.XMLHttpRequest) ereq = new XMLHttpRequest();
  else ereq = new ActiveXObject("Microsoft.XMLHTTP");
  if (ereq) {
    ereq.open("POST", encodeURI("[CORE_STANDURL]&app=WEBDESK&action=SAVESVC&snum="+editSnum+"&params="+escape(purl)), false);
    ereq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ereq.send('');
    if (ereq.status!=200) {
      document.getElementById('csvc'+snum).innerHTML = '[TEXT:wd error saving edit form] (HTTP Code '+ereq.status+')';	   
    } else { 
      services[is].purl = purl;
      loadSvcAsync(snum);
    }
  } else {
    document.getElementById('csvc'+snum).innerHTML = '[TEXT:wd error saving edit form] (XMLHttpRequest contruction)';	    
  }
  var fedit = document.getElementById('editsvc'+snum);
  fedit.parentNode.removeChild(fedit);
  editSnum = -1;
}

function showHideSvc(sid, savegeo) {
  var is = getSvc(sid);
  if (is===false) return;
  if (document.getElementById('csvc'+sid)) {
    if (services[is].open) {
      document.getElementById('csvc'+sid).style.display = 'none';
      document.getElementById('ivsvc'+sid).src = '[IMG:wd_svc_show.gif]';
      document.getElementById('ivsvc'+sid).title = '[TEXT:wd show svc content]';
      services[is].open = false;
    } else {
      document.getElementById('csvc'+sid).style.display = 'block';
      document.getElementById('ivsvc'+sid).src = '[IMG:wd_svc_hide.gif]';
      document.getElementById('ivsvc'+sid).title = '[TEXT:wd hide svc content]';
      services[is].open = true;
    }
    if (savegeo) saveGeometry();
  }
}

function computeMoveIconV(sid) {
  var snum = services[sid].snum;
  document.getElementById('gotoL'+snum).style.display = (services[sid].col>0 ? 'inline' : 'none');
  document.getElementById('gotoR'+snum).style.display = (services[sid].col<(colCount-1) ? 'inline' : 'none');
  document.getElementById('gotoU'+snum).style.display = (services[sid].lin>0 ? 'inline' : 'none');
  document.getElementById('gotoD'+snum).style.display = (services[sid].lin<(colsDesc[services[sid].col].length-1) ? 'inline' : 'none');
}

function saveGeometry() {
  var geo='';
  for (var ic=0; ic<colsDesc.length; ic++) {
    for (var il=0; il<colsDesc[ic].length; il++) {
      if (services[colsDesc[ic][il]]) geo += (geo==''?'':'|')+services[colsDesc[ic][il]].snum+':'+ic+':'+il+':'+(services[colsDesc[ic][il]].open?"1":0);
    }
  }
  var xreq = null;
  if (window.XMLHttpRequest) xreq = new XMLHttpRequest();
  else xreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (xreq) {
    xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=GEOSERVICE&sgeo="+geo, false);
    xreq.send('');
    if (xreq.status!=200) alert('[TEXT:wd error geo service] (HTTP Code '+xreq.status+')');	   
  } else {
    alert('[TEXT:wd error geo service] (XMLHttpRequest contruction)');	   
  }
  return;
}

function moveSvc(snum,c,l) {
  var is = getSvc(snum);
  if (is===false) return;
  var geo = '';
  if (c==-1||c==1) {
    if (services[is].col+c>=0&&services[is].col+c<colCount) {

      var ocol = services[is].col;
      var olin = services[is].lin;

      services[is].col += c;
      services[is].lin = colsDesc[services[is].col].length;
      var ncol = services[is].col;
      var clin = services[is].lin;

      colsDesc[ncol][colsDesc[ncol].length] = is;
      colsDesc[ocol].splice(olin,1);

      var childo = document.getElementById('svc'+services[is].snum);
      var insertCol = document.getElementById('wdcol'+ncol);

      insertCol.insertBefore(childo,null);
      computeMoveIconV(is);
      if (colsDesc[ocol].length>0) computeMoveIconV(colsDesc[ocol][colsDesc[ocol].length-1]);
      if (colsDesc[ncol].length>1) computeMoveIconV(colsDesc[ncol][colsDesc[ncol].length-2]);

      saveGeometry();

    } else {
      return;
    } 
  } else if (l==-1||l==+1) {
    var col = services[is].col;
    if (services[is].lin+l<0) return;
    var oli=services[is].lin;
    if (l==-1) {
      if (services[is].lin>0) {
	var svc = colsDesc[col][services[is].lin-1];
	colsDesc[col][services[is].lin] = svc;
	colsDesc[col][services[is].lin-1] = is;
	services[svc].lin = services[is].lin;
	services[is].lin--;
	var childo = document.getElementById('svc'+services[is].snum);
	var brotho = document.getElementById('svc'+services[svc].snum);
        childo.parentNode.insertBefore(childo,brotho);
	computeMoveIconV(is);
 	computeMoveIconV(svc);

	saveGeometry();
      }
    } else {
      if (services[is].lin<colsDesc[col].length) {
	var svc = colsDesc[col][services[is].lin+1];
	colsDesc[col][services[is].lin] = svc;
	colsDesc[col][services[is].lin+1] = is;
	services[svc].lin = services[is].lin;
	services[is].lin++;
	var childo = document.getElementById('svc'+services[is].snum);
	var brotho = document.getElementById('svc'+services[svc].snum);
        childo.parentNode.insertBefore(brotho,childo);
 	computeMoveIconV(is);
 	computeMoveIconV(svc);

	saveGeometry();
      }
    }
  } else {
    alert('moveSvc:: invalid line='+l+', column='+c);
    return;
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


function deleteSvc(snum) {
  var is = getSvc(snum);
  if (is===false) return;
  if (!confirm('[TEXT:wd confirm supress of] ['+services[is].stitle+']')) return false;
  unDisplaySvc(snum);
  services.splice(is,1);
  var xreq = null;
  if (window.XMLHttpRequest) xreq = new XMLHttpRequest();
  else xreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (xreq) {
    xreq.open("POST", "[CORE_STANDURL]app=WEBDESK&action=DELSERVICE&snum="+snum, false);
    xreq.send('');
    if (xreq.status!=200) alert('[TEXT:wd error add service] (HTTP Code '+xreq.status+')');	   
  } else {
    alert('[TEXT:wd error add service] (XMLHttpRequest contruction)');	   
  }
}

function setWS(sid) {
  var value = 40;
  if (document.getElementById('svc'+sid)) {
    var o = document.getElementById('svc'+sid);
    o.style.opacity = value/100;
    o.style.filter = 'alpha(opacity=' + value + ')';
  }
}
function unsetWS(sid) {
  var value = 100;
  if (document.getElementById('svc'+sid)) {
    var o = document.getElementById('svc'+sid);
    o.style.opacity = value/100;
    o.style.filter = 'alpha(opacity=' + value + ')';
  }
}


function loadSvc(sid, shl, params) {
  var dreq = null;
  var is = getSvc(sid);
  if (is===false) return;

  if (services[is].vurl=='') return;

  if (shl) setWS(sid);

  if (window.XMLHttpRequest) dreq = new XMLHttpRequest();
  else dreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (dreq) {
    dreq.open("POST", services[is].vurl+services[is].purl, false);
    dreq.send('');
    if (dreq.status!=200) {
      document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (HTTP Code '+dreq.status+')';	   
    } else { 
      document.getElementById('csvc'+sid).innerHTML = '<div>'+dreq.responseText+'</div>';
    }
  } else {
    document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (XMLHttpRequest contruction)';	    
  }
  if (shl) unsetWS(sid);
}

var timerOn = new Array();
function loadSvcAsync(sid, shl, params) {
  var dreq = null;
  var is = getSvc(sid);
  if (is===false) return;

  if (services[is].vurl=='') return;

  if (window.XMLHttpRequest) dreq = new XMLHttpRequest();
  else dreq = new ActiveXObject("Microsoft.XMLHTTP");
  if (dreq) {
    if (shl) setWS(sid);
    dreq.onreadystatechange =  function() {
      if (dreq.readyState == 4) {
	if (dreq.status!=200) {
	  document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (HTTP Code '+dreq.status+')';	   
	} else { 
	  document.getElementById('csvc'+sid).innerHTML = '<div>'+dreq.responseText+'</div>';
	  if (services[is].rdel>0) {
	    var dat = new Date();
	    services[is].nextLoad = dat.getTime() + (services[is].rdel*60*1000);
	  } else {
	    timerOn[is] = -1;
	  }	    
	}
	if (shl) unsetWS(sid);
      }
    }
    var url = services[is].vurl+services[is].purl;
    if (params) url += params;
    dreq.open("POST", url, true);
    dreq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    dreq.send('');
  } else {
    document.getElementById('csvc'+sid).innerHTML = '[TEXT:wd error retrieving content] (XMLHttpRequest contruction)';	    
  }
}

// Params 
var paramIsOpen = false;
function opencloseParams() {
  if (document.getElementById('wdparamset')) {
    var dp = document.getElementById('wdparamset');
    var dpi = document.getElementById('wdparamimg');
    if (paramIsOpen) {
      if (dpi) dpi.src = "[IMG:wd_open_services.gif]";
      dp.style.display = 'none';
      paramIsOpen = false;
    } else {
      if (dpi) dpi.src = "[IMG:wd_close_services.gif]";
      dp.style.display = 'block';
      paramIsOpen = true;
   }
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



