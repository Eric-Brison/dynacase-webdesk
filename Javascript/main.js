var currentApp = -1;
var currentIdApp = -1;

var updateInfosTimeout = 5 * 60 * 1000;

function getUnreadMsgCount() {
  var corestandurl=window.location.pathname+'?sole=Y&';
  var uu = corestandurl+'app=WEBDESK&action=SVCLOCALMAIL&oc=Y';
  updateInfos(uu, 'ureadmail', "getUnreadMsgCount()", updateInfosTimeout);
}
  

function getWaitingEventCount() {
  var corestandurl=window.location.pathname+'?sole=Y&';
  var uu = corestandurl+'app=WGCAL&action=WGCAL_WAITRV&oc=Y';
  updateInfos(uu, 'waitingrv', "getWaitingEventCount()", updateInfosTimeout);
}
  
function getAffectDocCount() {
  var corestandurl=window.location.pathname+'?sole=Y&';
  var uu = corestandurl+'app=WEBDESK&action=COUNTAFFECTDOC';
  updateInfos(uu, 'docreceived', "getAffectDocCount()", updateInfosTimeout);
}

function updateInfos(url, tag, fct, to) {

  if (url && url!="" && document.getElementById(tag)) {
    var sr = '?';
    var dreq = null;
    if (window.XMLHttpRequest) dreq = new XMLHttpRequest();
    else dreq = new ActiveXObject("Microsoft.XMLHTTP");
    if (dreq) {
      dreq.onreadystatechange =  function() {
	if (dreq.readyState==4 && dreq.status==200) {
	  try {
 	    eval(dreq.responseText); 
	    if (result) {
	      document.getElementById(tag).innerHTML = result.text;
	      document.getElementById(tag).title = result.msg;
	    } else {
	      document.getElementById(tag).innerHTML = '?';
	      document.getElementById(tag).title = '********';
	    }
	    setTimeout(fct, to);
	  } catch(exception) {
	  }
	}
      }
      dreq.open("POST", url, true);
      dreq.send('');
    }
  }
}

function alternMBar() {

  if (isMBarStatic) {
    isMBarStatic = false;
    isOpen = false;
    document.getElementById('wdcmdtext').style.border = "1px outset [COLOR_A6]";
  } else {
    isMBarStatic = true;
    isOpen = true;
    document.getElementById('wdcmdtext').style.border = "1px inset [COLOR_A6]";
  }
  computefbodywh();
}


function computefbodywh() {

  var bodH = getFrameHeight();
  var bodW = parseInt(getFrameWidth());

  var topH = getObjectHeight(document.getElementById('wdtitle'));

  var md = document.getElementById('wdmenu');
  md.style.height = 'auto';
  if (isIE) bodW -= 20;
  if (isMBarStatic) {
    md.className='wdmenufixed';
    if ((currentApp>-1) ) { // undisplay before to avoid composition errors in mozilla
      document.getElementById('fbody'+currentApp).style.display='none';
    }
    document.getElementById('wdmbarmng').src = (isOpen?"[IMGF:wd_left_bar.gif:0,0,0|COLOR_BLACK]":"[IMGF:wd_right_bar.gif:0,0,0|COLOR_BLACK]");
    if (isNetscape) {
      md.style.display = 'none';
      md.style.display = 'block';
    }
    md.style.display = (isOpen?'block':'none');
    var rh = bodH - (topH + 0);
    var ibody = bodW - getObjectWidth(md);
    md.style.height = rh;
    document.getElementById('wdbody').style.width = ibody - 0;//  - 18; 
    document.getElementById('wdbody').style.borderWidth = '0'; 
    if (currentApp>-1) {      
      document.getElementById('fbody'+currentApp).style.height = rh;
      document.getElementById('fbody'+currentApp).style.width = '100%';;
      document.getElementById('fbody'+currentApp).style.display='';	
    }
  } else {
    md.className='wdmenu';
    md.style.top = topH;
    md.style.left = '0px';
    if (currentApp>-1) {
      document.getElementById('fbody'+currentApp).style.width = bodW;
      document.getElementById('fbody'+currentApp).style.height = bodH - topH;
    }    
    if (isOpen)  md.style.display = 'block';
    else md.style.display = 'none';
    document.getElementById('wdmbarmng').src = (isOpen?"[IMGF:wd_close_bar.gif:0,0,0|COLOR_BLACK]":"[IMGF:wd_open_bar.gif:0,0,0|COLOR_BLACK]");
  }
  setcurtime();
}

function runappm(event, idapp, sidapp, sname, ico, params, normalapp) {
  var evt = (evt) ? evt : ((event) ? event : null );
  var addb = evt.shiftKey ? true : false;
  var force = evt.ctrlKey ? true : false;
  if (addb && normalapp) addInBar(idapp, sidapp, sname, ico, params);
  else runapp(idapp, sidapp, params, force);
}

var inBarApp = new Array;
function addInBar(idapp, sidapp, sname,  ico, params) {
  for (var ia=0; ia<inBarApp.length; ia++) {
    if (idapp==inBarApp[ia].id) return;
  }  
  inBarApp[inBarApp.length] = { id:idapp, code:sidapp, name:sname, ico:ico, prm:params };
  reloadBarApp();
  saveBarApp();
  if (!isMBarStatic) isOpen = false;
  computefbodywh();
}

function reloadBarApp() {
  if (!document.getElementById('appbar')) {
    alert('appbar not present!');
    return;
  }
  document.getElementById('appbar').innerHTML = '';
  if (inBarApp.length<1) return;
  var bcontent = '';
  for (var ia=0; ia<inBarApp.length; ia++) {
    bcontent += '&nbsp;<img id="bappid'+inBarApp[ia].id+'" class="appbar_button" ';
    bcontent += '   onclick="clickBarApp(event, '+inBarApp[ia].id+')" ';
    bcontent += '   src="'+inBarApp[ia].ico+'" title="'+inBarApp[ia].name+'">';
  }
  if (bcontent!='') document.getElementById('appbar').innerHTML = bcontent;
  return false;
}

function clickBarApp(event, idapp) {
  var evt = (evt) ? evt : ((event) ? event : null );
  var delb = evt.shiftKey ? true : false;
  var force = evt.ctrlKey ? true : false;
  var capp = -1;

  for (var ia=0; ia<inBarApp.length; ia++) {
    if (idapp==inBarApp[ia].id) capp = ia;
  }
  if (capp==-1) return false;
  
  if (delb) {
    inBarApp.splice(capp,1);
    reloadBarApp();
    saveBarApp();
  } else {
    runapp(inBarApp[capp].id, inBarApp[capp].code, inBarApp[capp].prm, force);
  }
  return false;
}

function saveBarApp() {
  var valp = '';
  if (inBarApp.length>1) {
    for (var ia=0; ia<inBarApp.length; ia++) {
      valp += (valp==''?'':'|')+inBarApp[ia].code;
    }
  }
  setparamu("WEBDESK", "WDK_BARAPP", valp);
}


function runapp(idapp, sidapp, params, force) {

  if (!params) params='';

  if (currentApp>-1) {
    document.getElementById('app'+currentIdApp).className = 'app';
    document.getElementById('fbody'+currentApp).style.display = 'none';
  }

  currentIdApp = idapp;
  currentApp = idapp; //(idapp>10000?0:idapp);
  var fapp = document.getElementById('fbody'+currentApp);

  if (fapp.src=='about:blank' || currentApp==0 || force) {
    fapp.src = '[CORE_BASEURL]app='+sidapp+params;
  }
  fapp.style.display = 'block';
  
  document.getElementById('app'+idapp).className = 'app app_selected';
  document.getElementById('wdappsel').innerHTML = document.getElementById('app'+idapp).innerHTML;

  if (!isMBarStatic) isOpen = false;
  computefbodywh();

}   


function setcurtime() {
  var dat = new Date();
  var day = dat.getDate();
  var month = dat.getMonth()+1;
  var year = dat.getFullYear();
  var hour = dat.getHours();
  var min = dat.getMinutes();
  var dstr = pad(day,2,'0')+"/"+pad(month,2,'0')+"/"+year+" "+pad(hour,2,'0')+":"+pad(min,2,'0');
  document.getElementById('curdate').innerHTML = dstr;
  setTimeout("setcurtime()", 20*1000);
}

function pad(s,l,p) {
  var str = String(s);
  if (str.length<l) str = pad(p+s, l, p);
  return str;
}
