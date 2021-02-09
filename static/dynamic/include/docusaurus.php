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

    // remove page scripts
    $generated_page = preg_replace("/<link rel=\"preload\" href=\"(.)+\" as=\"script\">/", "", $generated_page);

    echo $generated_page;
    echo "<div class=\"main-wrapper\">";
  }

  function end_docusaurus_page() {
    global $DOCUSAURUS_SITE_TITLE, $DOCUSAURUS_TEMPLATE_FILE;
    $template_page = file_get_contents($DOCUSAURUS_TEMPLATE_FILE);

    // select HTML after end of page content
    $start_idx = strpos($template_page, "<footer");
    $generated_page = substr($template_page, $start_idx);

    // remove page scripts
    $generated_page = preg_replace("/<script src=\"(.)+\"><\/script>/", "", $generated_page);
    
    echo "</div>";
    echo $generated_page;
  }
?>