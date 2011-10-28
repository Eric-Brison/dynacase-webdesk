
/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 */

function ardoiseSaveNeeded(event) {
  var evt = (evt) ? evt : ((event) ? event : null );
  intKeyCode = event.which;
  if (!intKeyCode) intKeyCode= event.keyCode;
  if (((intKeyCode == 115)||(intKeyCode == 83)) && (evt.ctrlKey)) {
    submitService(event);
    if (stopPropagation) stopPropagation(event);
    else if (window.parent.stopPropagation) window.parent.stopPropagation(event);
  }
}

