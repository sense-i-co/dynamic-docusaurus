setTimeout(function(){ // need to make this timeout better by polling for when #dynamic-content no longer exists
  var docusaurus = document.getElementById("__docusaurus");
  var footer = document.getElementsByTagName("footer")[0];
  docusaurus.insertBefore(DYNAMIC_DOCUSAURUS_CONTENT, footer);
}, 500);