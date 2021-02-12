var mutationInterval = setInterval(tryMutate, 10);

function tryMutate() {

  // wait until docusaurus's scripts have removed the dynamic content
  if(document.getElementById("dynamic-content") == null) {

    // once the dynamic content has been removed, we can stop polling and execute the page mutation
    clearInterval(mutationInterval);

    // get the parent container for all docusaurus content on the page
    var elDocusaurus = document.getElementById("__docusaurus");

    // add the dynamic page title back to the document
    document.getElementsByTagName("title")[0].innerHTML = DYNAMIC_DOCUSAURUS_TITLE;
    elMetas = document.getElementsByTagName("meta");
    for(var i = 0; i < elMetas.length; i++) {
      if(elMetas[i].getAttribute("property") == "og:title") {
        elMetas[i].setAttribute("content", DYNAMIC_DOCUSAURUS_TITLE);
      }
    }

    // add the dynamic page description back to the document
    var elHead = document.getElementsByTagName("head")[0];
    elHead.appendChild(DYNAMIC_DOCUSAURUS_DESCRIPTION);
    elHead.appendChild(DYNAMIC_DOCUSAURUS_OG_DESCRIPTION);

    // add the dynamic page content back to the document just before the footer and unhide it (i.e. remove style="display:none;")
    var elFooter = document.getElementsByTagName("footer")[0];
    elDocusaurus.insertBefore(DYNAMIC_DOCUSAURUS_CONTENT, elFooter);
    DYNAMIC_DOCUSAURUS_CONTENT.removeAttribute("style");
  
    // loop through all links on the page
    var elLinks = document.getElementsByTagName("a");
    for(var i = 0; i < elLinks.length; i++) {
      // highlight navigation links for the current page
      if(elLinks[i].getAttribute("href") == window.location.href) {
        elLinks[i].classList.add("navbar__link--active")
      }
      // make sure dynamic content is removed on page change
      elLinks[i].onclick = function(e) {
        DYNAMIC_DOCUSAURUS_CONTENT.remove();
      };
    }

  }

}