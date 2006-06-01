function postCreate() {
  $this->uportPostModify(true);
}
function postModify() {
  $this->uportPostModify(false);
}



function uportPostModify($mod) {
  $this->lock();
  $change = false;
  $numt = $this->getTValue("uport_svcnum");
  foreach ($numt as $k => $v ) {
    if ($v=="" || $v<0) {
      $change = true;
      $numt[$k] = $this->getNumSequence();
    }
  }
  if ($change) {
    $this->setValue("uport_svcnum", $numt);
    if ($mod) $this->modify();
  }
}

function getNumSequence() {
  $cnum = 0;
  $numt = $this->getTValue("uport_svcnum");
  foreach ($numt as $k => $v ) {
    if ($v!="" && $v>=0) $cnum = ($cnum<=$v ? $v : $cnum);
  }
  $cnum++;
  return $cnum;
}
