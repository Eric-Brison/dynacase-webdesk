var appSelected = -1;
function selectApp(id, url) {
  if (appSelected!=-1) {
    document.getElementById('app'+appSelected).className = 'app';
  }
  appSelected = id;
  document.getElementById('appzone').src = url;
  document.getElementById('app'+id).className = 'app app_selected';

  document.getElementById('appzone').style.height = getFrameHeight()-20;
  document.getElementById('appzone').style.display = 'block';
  
}
function selectFirstApp() {
  var da=document.getElementById('divapp');
  if (da) {
    var ds=da.getElementsByTagName('div');
    if (ds.length > 0) {
      var ds1=ds[0];
      ds1.onclick.apply(ds1,[]);
    }
  }
}

