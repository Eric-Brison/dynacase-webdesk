
/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 */

var currentApp = -1;
var currentIdApp = -1;

var updateInfosTimeout = 5 * 60 * 1000;

function alternMBar() {

  if (isMBarStatic) {
    isMBarStatic = false;
    isOpen = false;
    document.getElementById('wdcmdtext').style.borderStyle = "outset";
  } else {
    isMBarStatic = true;
    isOpen = true;
    document.getElementById('wdcmdtext').style.borderStyle = "inset";
  }
  computefbodywh();
}


function computefbodywh() {

  var bodH = getFrameHeight();
  var bodW = parseInt(getFrameWidth());

  var topH = getObjectHeight(document.getElementById('wdtitle'));

  var md = document.getElementById('wdmenu');
  md.style.height = 'auto';
  if (isIE8) bodW -= 5;

  if (isMBarStatic) {  // Menu fixed

    md.className='clipsed '+((isIE6)?' select-free':'');
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
    if (isIE8) rh-=5;
    md.style.height = rh+'px';
    document.getElementById('wdbody').style.width = (ibody - 0)+'px';//  - 18; 
    document.getElementById('wdbody').style.borderWidth = '0'; 
    document.getElementById('wdbody').className = 'clipsed'; 
    if (currentApp>-1) {      
      document.getElementById('fbody'+currentApp).style.height = rh+'px';
      document.getElementById('fbody'+currentApp).style.width = '100%';;
      document.getElementById('fbody'+currentApp).style.display='';	
    }

  } else { // Menu float

    md.className= ((isIE6)?' select-free':'');
    md.style.top = topH+'px';
    md.style.left = '0px';
    if (currentApp>-1) {
        var delta=0;
        if (isIE8) delta-=5;
      document.getElementById('fbody'+currentApp).style.width = bodW+'px';
      document.getElementById('fbody'+currentApp).style.height = (bodH - topH+delta)+'px';
      document.getElementById('wdbody').className = ''; 
      md.style.display = (isOpen?'block':'none');
    }    
    document.getElementById('wdmbarmng').src = (isOpen?"[IMGF:wd_close_bar.gif:0,0,0|COLOR_BLACK]":"[IMGF:wd_open_bar.gif:0,0,0|COLOR_BLACK]");
  }
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


function delInBar(idapp) {
  var appDel = -1;
  for (var ia=0; ia<inBarApp.length; ia++) {
    if (idapp==inBarApp[ia].id) appDel = ia;
  }  
  if (appDel!=-1) {
    inBarApp.splice(appDel,1);
    reloadBarApp();
    saveBarApp();
  }
}  

function adddelAppShortCut(idapp, sidapp, sname,  ico, params) {
  var alreadyInBar = false;
  for (var ia=0; ia<inBarApp.length; ia++) {
    if (idapp==inBarApp[ia].id) alreadyInBar = true;
  }  
  if (alreadyInBar) delInBar(idapp);
  else addInBar(idapp, sidapp, sname,  ico, params);
}  

function setDefaultApp(idapp, sidapp, sname,  ico, params) {
  setparamu("WEBDESK", "WDK_DEFAPP", sidapp);
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
    bcontent += '&nbsp;<img needresize="1" id="bappid'+inBarApp[ia].id+'" class="appbar_button" ';
    bcontent += '   onclick="clickBarApp(event, '+inBarApp[ia].id+')" ';
    bcontent += '   oncontextmenu="openAppMenu(event, '+inBarApp[ia].id+', \''+inBarApp[ia].code+'\', \''+inBarApp[ia].name+'\', \''+inBarApp[ia].ico+'\', \''+inBarApp[ia].prm+'\' ); return false" ';
    bcontent += '   src="'+inBarApp[ia].ico+'" title="'+inBarApp[ia].name+'">';
  }
  if (bcontent!='') document.getElementById('appbar').innerHTML = bcontent;
  if (resizeImages) resizeImages();
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
    delInBar(idapp);
  } else {
    runapp(inBarApp[capp].id, inBarApp[capp].code, inBarApp[capp].prm, force);
  }
  return false;
}

function saveBarApp() {
  var valp = '';
  if (inBarApp.length>0) {
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

  if (/1x1.gif/i.test(fapp.src) || currentApp==0 || force) {
    fapp.src = '[CORE_BASEURL]app='+sidapp+params;
  }
  fapp.style.display = 'block';
  
  document.getElementById('app'+idapp).className = 'app app_selected';
  document.getElementById('wdappsel').innerHTML = document.getElementById('app'+idapp).innerHTML;

  if (!isMBarStatic) isOpen = false;
  computefbodywh();

}   


function pad(s,l,p) {
  var str = String(s);
  if (str.length<l) str = pad(p+s, l, p);
  return str;
}

var mcurApp = { id:-1, sid:'', name:'', ico:'', prm:'' };
function openAppMenu(event, idapp, sidapp, sname, icon, params ) {
  mcurApp = { id:idapp, sid:sidapp, name:sname, ico:icon, prm:params};
  if (!document.getElementById('ctxappmenu')) return false;

  document.getElementById('appmenu_ico').src = icon;
  document.getElementById('appmenu_title').innerHTML = sname;

  GetXY(event);
  with (document.getElementById('ctxappmenu')) {
    style.left = (parseInt(Xpos) - 10) + 'px';
    style.top = (parseInt(Ypos) - 10) + 'px';
    style.display = 'inline';
  }
  return false;
}

var menuAppTempo = -1;
function setTempoAppMenu() {
  if (!document.getElementById('ctxappmenu')) return false;
  menuAppTempo = self.setTimeout("closeAppMenu()", 500);
}
function cancelTempoAppMenu() {
  if (menuAppTempo!=-1) clearTimeout(menuAppTempo);
  menuAppTempo = -1;
}

function closeAppMenu() {
  cancelTempoAppMenu();
  if (!document.getElementById('ctxappmenu')) return false;
  document.getElementById('ctxappmenu').style.display = 'none';
  return false;
}
var initContent = true;
function searchOnBlur(ob) {
    if (ob.value=='') {
	ob.className = 'unsetter';
	ob.value = '[TEXT:top bar search]';
	initContent = true;
    }
}
function searchOnFocus(ob) {
    if (initContent) {
	ob.className = 'setter';
	ob.value = '';
	initContent = false;
    }
}