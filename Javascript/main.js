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
  var bodW = getFrameWidth();

  var topH = getObjectHeight(document.getElementById('wdtitle'));
  var wor = (isOpen ? 0.2 : 0.0);

  var md = document.getElementById('wdmenu');
  md.style.height = 'auto';

  var ww = parseInt(bodW);
  if (isIE) ww = parseInt(bodW) - 20;
  
  if (isMBarStatic) {
    md.style.position = 'relative';
    md.style.top = 1;
    md.style.left = 1;

    document.getElementById('wdmenu').style.height = bodH - topH;
    document.getElementById('wdmenu').style.width = parseInt(ww * wor); 
    document.getElementById('wdmenu').style.display = (isOpen?'block':'none');
    document.getElementById('wdbody').style.width = parseInt(ww * (1 - (wor)));
    if (currentApp>-1) {
      document.getElementById('fbody'+currentApp).style.height = bodH - topH;
      document.getElementById('fbody'+currentApp).style.width = parseInt(ww * (1 - (wor)));
    }
    document.getElementById('wdmbarmng').src = (isOpen?"[IMG:wd_left_bar.gif]":"[IMG:wd_right_bar.gif]");

  } else {

    md.style.float =  'left';
    md.style.position = 'absolute';
    md.style.top = topH;
    md.style.width =  parseInt(ww * wor);
    
    if (currentApp>-1) {
      document.getElementById('fbody'+currentApp).style.width = parseInt(ww);
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
