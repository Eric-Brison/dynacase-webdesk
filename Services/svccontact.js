function showDoc(doc) {
  var card = 'carddetails';
  if (document.getElementById('fcard').value==1) card += doc;
  subwindow(300, 550, card, 'index.php?sole=Y&app=FDL&action=FDL_CARD&zone=USERCARD:VIEWPERSON:T&id='+doc);
}

function getReturn(event) {
  var evt = (evt) ? evt : ((event) ? event : null );
  var cc = (evt.keyCode) ? evt.keyCode : evt.charCode;
  if (cc==13) return false;
  return true;
}

var sreq = null;
function runsearch(event) {

  // Abort previous request
  if (sreq!=null) sreq.abort();
    
  var evt = (evt) ? evt : ((event) ? event : null );
  var cc = (evt.keyCode) ? evt.keyCode : evt.charCode;

  var forceSearch = false;
  if (cc==13) forceSearch = true;

  var fam = "USER";
  var str = document.getElementById('str').value;
  var soc = (document.getElementById('soc').checked ? 1 : 0);
  var beg = (document.getElementById('begin').checked ? 1 : 0);

  var maxc = document.getElementById('maxc').value;
  var maxl = document.getElementById('maxl').value;
  var fcard = document.getElementById('fcard').value;

  if (str.length<3 && !forceSearch) {
    resetsearch();
    return false;
  }

  var url = 'index.php?sole=Y&&sole=Y&app=WEBDESK&action=GSVC&sname=svccontact_search&fam='+fam+'&str='+str+'&soc='+soc+'&csz=0&dcl=clearResults&hcl=clickH&hov=mouseoverH&hmo=mouseoverH&hou=mouseoutH&beg='+beg+'&maxc='+maxc+'&maxl='+maxl;

  if (window.XMLHttpRequest) sreq = new XMLHttpRequest();
  else sreq = new ActiveXObject('Microsoft.XMLHTTP');

  sreq.onreadystatechange =  function() {

    if (sreq.readyState == 4) {
      try {
        if (sreq.status!=200) {
	  document.getElementById('trace').innerHTML = 'status='+sreq.status+' '+sreq.responseText;
        } else {
	  clearResults();
	  if (!document.getElementById('srresult')) {
	    var rdiv = document.createElement('div');
	    rdiv.id = 'srresult';
	    document.body.appendChild(rdiv);
	  } else {
	    var rdiv = document.getElementById('srresult');
	  }
          var xy=getAnchorPosition('str');
	  var h=getObjectHeight(document.getElementById('str'));
	  rdiv.style.top = (parseInt(xy.y)+parseInt(h))+'px';
	  rdiv.style.left = parseInt(xy.x)+'px';
	  rdiv.style.position = 'absolute';
	  rdiv.style.visibility = 'visible';
	  rdiv.innerHTML = sreq.responseText;
        }
      } catch(e) {
//          alert('Exception : ' + e);
      }
      unglobalcursor();
      sreq = null;
    }
  }
  sreq.open('POST', url, true);
  sreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  globalcursor('progress');
  sreq.send('');
  return false;
}

function resetsearch() {
  if (document.getElementById('srresult')) {
    document.getElementById('srresult').innerHTML = '';
    document.getElementById('srresult').style.visibility = 'hidden';
  }
  unglobalcursor();
}

function mouseoverH(evt, elt, id) {
  elt.className='sr_result sr_result_hover';
  if (!document.getElementById('m'+id)) return; 
  GetXY(evt);
  var dnav = 0;
  if (isNetscape) dnav=20;
  with (document.getElementById('m'+id).style) {
    left = (dnav+parseInt(Xpos))+'px';
	    top = (dnav+parseInt(Ypos))+'px';  
    visibility='visible';
  }
}

function mouseoutH(event, elt, id) {
  elt.className='sr_result';
  if (!document.getElementById('m'+id)) return; 
  document.getElementById('m'+id).style.visibility = 'hidden';
}

function clickH(event, elt, id) {
  showDoc(id);
}

function clearResults() {
  if (!document.getElementById('srresult')) return;
  var felt = document.getElementById('srresult');
  var rmax = felt.childNodes.length - 1;
  for (var i=rmax; i>=0; i--) felt.removeChild(felt.childNodes[i]);
  document.getElementById('srresult').style.visibility = 'hidden';
}

function showHideOptions() {
  var bo = document.getElementById('blockoptions');
  if (bo.style.visibility!='visible') bo.style.visibility = 'visible';
  else bo.style.visibility = 'hidden';
}
