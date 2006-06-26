var currentApp = -1;
var currentIdApp = -1;

var updateInfosTimeout = 5 * 60 * 1000;

function initWebdesk() {
  getUnreadMsgCount();
  getWaitingEventCount();
  computefbodywh();
}
  
function getUnreadMsgCount() {
  var uu = 'index.php?sole=A&app=WEBDESK&action=SVCLOCALMAIL&oc=Y';
  updateInfos(uu, 'ureadmail', "getUnreadMsgCount()", updateInfosTimeout);
}
  

function getWaitingEventCount() {
  var uu = 'index.php?sole=Y&app=WGCAL&action=WGCAL_WAITRV&mo=L&oc=Y';
  updateInfos(uu, 'waitingrv', "getWaitingEventCount()", updateInfosTimeout);
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
	  eval(dreq.responseText);
	  if (result) sr= result;
	  document.getElementById(tag).innerHTML = sr;
	  setTimeout(fct, to);
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
    document.getElementById('wdcmdtext').src = "[IMG:wd_release_menu.gif]";
  } else {
    isMBarStatic = true;
    isOpen = true;
    document.getElementById('wdcmdtext').src = "[IMG:wd_fixe_menu.gif]";
  }
  computefbodywh();
}


function computefbodywh() {

  var bodH = getFrameHeight();
  var bodW = parseInt(getFrameWidth());

  var topH = getObjectHeight(document.getElementById('wdtitle'));
  var wor = (isOpen ? 0.2 : 0.0);

  var md = document.getElementById('wdmenu');
  md.style.height = 'auto';

  if (isIE) bodW -= 20;
  
  if (isMBarStatic) {
    md.style.float =  'left';
    md.style.position = 'relative';
    md.style.top = 0;
    md.style.left = 0;


    var rh = bodH - (topH + 0);
    var ibody = bodW - getObjectWidth(document.getElementById('wdmenu'));

    document.getElementById('wdmenu').style.height = rh;
    document.getElementById('wdbody').style.width = ibody - 15;//  - 18; 
    document.getElementById('wdbody').style.borderWidth = '0'; 
    if (currentApp>-1) {
      document.getElementById('fbody'+currentApp).style.height = rh;
      document.getElementById('fbody'+currentApp).style.width = getObjectWidth(document.getElementById('wdbody'));
//       alert('body='+getObjectWidth(document.getElementById('wdbody'))+' iframe='+getObjectWidth(document.getElementById('fbody'+currentApp)));
    }
    document.getElementById('wdmbarmng').src = (isOpen?"[IMG:wd_left_bar.gif]":"[IMG:wd_right_bar.gif]");
    document.getElementById('wdmenu').style.display = (isOpen?'block':'none');



  } else {

    md.style.float =  'left';
    md.style.position = 'absolute';
    md.style.top = topH;
    
    if (currentApp>-1) {
      document.getElementById('fbody'+currentApp).style.width = bodW;
      document.getElementById('fbody'+currentApp).style.height = bodH - topH;
   }    
    if (isOpen)  md.style.display = 'block';
    else md.style.display = 'none';

    document.getElementById('wdmbarmng').src = (isOpen?"[IMG:wd_close_bar.gif]":"[IMG:wd_open_bar.gif]");

  }

  setcurtime();
    
}

function runapp( idapp, sidapp, params) {
  
  if (!params) params='';

  if (currentApp>-1) {
    document.getElementById('app'+currentIdApp).className = 'app';
    document.getElementById('fbody'+currentApp).style.display = 'none';
  }

  currentIdApp = idapp;
  currentApp = idapp; //(idapp>10000?0:idapp);
  var fapp = document.getElementById('fbody'+currentApp);

  if (fapp.src=='about:blank' || currentApp==0) {
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
