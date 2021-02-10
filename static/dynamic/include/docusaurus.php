<?php
  $DOCUSAURUS_SITE_TITLE = "My Site";
  $DOCUSAURUS_TEMPLATE_FILE = "../../index.html";

  function start_docusaurus_page() {
    global $DOCUSAURUS_SITE_TITLE, $DOCUSAURUS_TEMPLATE_FILE;
    global $DOCUSAURUS_PAGE_TITLE;
    $template_page = file_get_contents($DOCUSAURUS_TEMPLATE_FILE);

    // select HTML before start of page content
    $start_idx = 0;
    $end_idx = strpos($template_page, "<div class=\"main-wrapper\">");
    $generated_page = substr($template_page, $start_idx, $end_idx-$start_idx);

    // set page title
    $generated_page = preg_replace("/<title data-react-helmet=\"true\">(.+)?\|(.+)?<\/title>/", "<title data-react-helmet=\"true\">" . $DOCUSAURUS_PAGE_TITLE . " | " . $DOCUSAURUS_SITE_TITLE . "</title>", $generated_page);

    // highlight page in navbar
    $start_idx = strpos($generated_page, $_SERVER['SCRIPT_NAME']);
    $temp_page = substr($generated_page, $start_idx);
    $start_idx = $start_idx + strpos($temp_page, "class=") + 7;
    $generated_page = substr_replace($generated_page, "navbar__link--active ", $start_idx, 0);

    echo $generated_page;
    echo "<div id=\"dynamic-content\" class=\"main-wrapper\">";
  }

  function end_docusaurus_page() {
    global $DOCUSAURUS_SITE_TITLE, $DOCUSAURUS_TEMPLATE_FILE;
    $template_page = file_get_contents($DOCUSAURUS_TEMPLATE_FILE);

    // select HTML after end of page content
    $start_idx = strpos($template_page, "<footer");
    $generated_page = substr($template_page, $start_idx);

    // add global variables and page capture script
    $start_idx = strpos($generated_page, "<script");
    $generated_page = substr_replace($generated_page, "<script>var DYNAMIC_DOCUSAURUS_CONTENT;</script><script src=\"/dynamic/scripts/page-capture.js\"></script>", $start_idx, 0);

    // add page generate script
    $start_idx = strrpos($generated_page, "</script>") + 9;
    $generated_page = substr_replace($generated_page, "<script src=\"/dynamic/scripts/page-generate.js\"></script>", $start_idx, 0);
    
    echo "</div>";
    echo $generated_page;
  }
?>