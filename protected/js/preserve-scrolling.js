// Store scroll position in the session storage on unload and set it back on page load
$(window).on('beforeunload', function() {
  sessionStorage.setItem('scrollPosition', $(window).scrollTop());
});

$(document).ready(function() {
  if (sessionStorage.scrollPosition) {
      $(window).scrollTop(sessionStorage.getItem('scrollPosition'));
      sessionStorage.removeItem('scrollPosition');
  }
});
