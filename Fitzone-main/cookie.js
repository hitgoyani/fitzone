
function setCookie(name, value, days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function eraseCookie(name) {   
  document.cookie = name+'=; Max-Age=-99999999; path=/';  
}

window.addEventListener('DOMContentLoaded', function() {
  if (!getCookie('cookieConsent')) {
    var banner = document.createElement('div');
    banner.id = 'cookie-banner';
    banner.style.position = 'fixed';
    banner.style.bottom = '0';
    banner.style.left = '0';
    banner.style.width = '100%';
    banner.style.background = '#222';
    banner.style.color = '#fff';
    banner.style.padding = '15px 10px';
    banner.style.textAlign = 'center';
    banner.style.zIndex = '9999';
    banner.innerHTML = 'This website uses cookies to ensure you get the best experience. <button id="acceptCookies" style="margin-left:10px;padding:5px 15px;background:#4caf50;color:#fff;border:none;border-radius:3px;cursor:pointer;">Accept</button>';
    document.body.appendChild(banner);
    document.getElementById('acceptCookies').onclick = function() {
      setCookie('cookieConsent', 'true', 365);
      banner.remove();
    };
  }
});
