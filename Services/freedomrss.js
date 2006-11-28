function RssGetReturn(event) {
  var evt = (evt) ? evt : ((event) ? event : null );
  var cc = (evt.keyCode) ? evt.keyCode : evt.charCode;
  if (cc==13) return false;
  return true;
}

var sreq = null;
function RssRunSearch(event) {

  // Abort previous request
  if (sreq!=null) sreq.abort();
    
  var evt = (evt) ? evt : ((event) ? event : null );
  var cc = (evt.keyCode) ? evt.keyCode : evt.charCode;

  var forceSearch = false; 
  if (cc==13) forceSearch = true;

  var str = document.getElementById('rss').value;
  var sys = (document.getElementById('sysrss').checked ? 1 : 0);

  if (str.length<3 && !forceSearch) {
    RssResetSearch();
    return false;
  }

  var url = "[CORE_BASEURL]sole=Y&app=WEBDESK&action=GSVC&sname=freedomrss_search";
  var param = "&str="+str+"&sys="+sys;

  if (window.XMLHttpRequest) sreq = new XMLHttpRequest();
  else sreq = new ActiveXObject('Microsoft.XMLHTTP');

  sreq.onreadystatechange =  function() {

    if (sreq.readyState == 4) {
      try {
        if (sreq.status!=200) {
	  document.getElementById('trace').innerHTML = 'status='+sreq.status+' '+sreq.responseText;
        } else {
	  RssClearResults();
	  if (!document.getElementById('rsslist')) {
	    var rdiv = document.createElement('div');
	    rdiv.id = 'rsslist';
	    document.body.appendChild(rdiv);
	  } else {
	    var rdiv = document.getElementById('rsslist');
	  }
          var xy=getAnchorPosition('rss');
	  var h=getObjectHeight(document.getElementById('rss'));
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
  globalcursor('progress');
  sreq.open('POST', url, true);
  sreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  sreq.send(param);
  return false;
}

function RssSet(id, title) {
  document.getElementById('rssid').value = id;
  document.getElementById('rsstitle').innerHTML = title;
  RssResetSearch();
  document.getElementById('rss').value = '';
}

function RssResetSearch() {
  if (document.getElementById('rsslist')) {
    document.getElementById('rsslist').innerHTML = '';
    document.getElementById('rsslist').style.visibility = 'hidden';
  }
  unglobalcursor();
}

function RssClearResults() {
  if (!document.getElementById('rsslist')) return;
  var felt = document.getElementById('rsslist');
  var rmax = felt.childNodes.length - 1;
  for (var i=rmax; i>=0; i--) felt.removeChild(felt.childNodes[i]);
  document.getElementById('rsslist').style.visibility = 'hidden';
}
 
function showHideOptions() {
  var bo = document.getElementById('blockoptions');
  if (bo.style.visibility!='visible') bo.style.visibility = 'visible';
  else bo.style.visibility = 'hidden';
}

function rssMouseOver(event, elt, id) {
  elt.className='rss_result rss_result_hover';
}

function rssMouseOut(event, elt, id) {
  elt.className='rss_result';
}

