// function to replace video src
function playVideo(target) {
  //this.preventDefault();
  console.log(target);
  var e = document.getElementById(target);
  var iframe = e.querySelector('iframe');
  var newSrc = iframe.getAttribute("data-src") + '?autoplay=1&dnt=1&modestbranding=1&rel=0&showinfo=0';
  iframe.src=newSrc;
  var overlay = e.getElementsByClassName("video-overlay")[0];
  overlay.remove();
}