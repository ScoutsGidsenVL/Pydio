"use strict";

/* menubalk */

function to_home() {
  if (ajaxplorer.user.id == 'admin') {
    ajaxplorer.actionBar.actions.get('switch_to_settings').apply();
  } else {
    ajaxplorer.triggerRepositoryChange('ajxp_home');
  }
  return false;
}

function logout() {
  ajaxplorer.actionBar.actions.get('logout').apply();
  return false;
}

function update_account() {
  var loggedIn = typeof ajaxplorer != 'undefined' && ajaxplorer && ajaxplorer.user;
  document.querySelector('.item_registratie').style.display = loggedIn ? 'none' : 'initial';
  document.querySelector('.item_username a').innerHTML = loggedIn ? ajaxplorer.user.getPreference('USER_DISPLAY_NAME') : '';
  document.querySelector('.item_username').style.display = loggedIn ? 'initial' : 'none';
  document.querySelector('.item_home').style.display = loggedIn ? 'initial' : 'none';
  document.querySelector('.item_afmelden').style.display = loggedIn ? 'initial' : 'none';
}

// "You can't avoid polling, there isn't any event for href change."
// http://stackoverflow.com/a/3522154
setInterval(update_account, 100);

/* toolbar */

function update_vertical_splitter() {
  var vertical_splitter = document.querySelector('#vertical_splitter');
  var file_list_header = document.querySelector('#files_list_header');
  if (vertical_splitter && file_list_header) {
    var in_use = file_list_header.clientHeight;
    vertical_splitter.style.cssText = 'height: calc(100% - ' + in_use + 'px) !important;';
  }
}

function update_toolbar() {
    update_vertical_splitter();
    var a_email = document.querySelector('#buttons_bar a.email');
    if (a_email) {
        var email = get_email();
        a_email.innerHTML = email;
        a_email.setAttribute('href', 'mailto:' + email);
    }
}

setInterval(update_toolbar, 300);

/* utility */

// Copied to plugins/access.fs/fsActions.xml, because that code is using 'eval'. Please keep both versions in sync.
function get_email() {
    if (ajaxplorer && ajaxplorer.user && ajaxplorer.user.repositories && ajaxplorer.repositoryId) {
        var repoName = ajaxplorer.user.repositories.get(ajaxplorer.repositoryId).label;
        var mailRepoName = repoName.toLowerCase().replace(/[\$#@~!&*()\[\];.,:?^ `'\\\/ ]+/g, '_').replace('_gouw_', '_');
        return mailRepoName + '_' + ajaxplorer.repositoryId.toLowerCase() + '@scoutsengidsenvlaanderen.org';
    }
}
