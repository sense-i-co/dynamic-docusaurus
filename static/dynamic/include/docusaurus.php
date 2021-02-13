<?php
  define("DOCUSAURUS_SITE_TITLE",     "My Site");
  define("DOCUSAURUS_TEMPLATE_FILE",  "../../index.html");

  function start_docusaurus_page() {
    global $DOCUSAURUS_PAGE_TITLE, $DOCUSAURUS_PAGE_DESCRIPTION;
    $template_page = file_get_contents(DOCUSAURUS_TEMPLATE_FILE);

    // select HTML before start of page content
    $start_idx = 0;
    $end_idx = strpos($template_page, "<div class=\"main-wrapper\">");
    $generated_page = substr($template_page, $start_idx, $end_idx-$start_idx);

    // set page title
    $generated_page = preg_replace("/<title data-react-helmet=\"true\">(.+)?\|(.+)?<\/title>/", "<title id=\"dynamic-title\" data-react-helmet=\"true\">" . $DOCUSAURUS_PAGE_TITLE . " | " . DOCUSAURUS_SITE_TITLE . "</title>", $generated_page);
    $generated_page = preg_replace("/<meta data-react-helmet=\"true\" property=\"og:title\" content=\"(.+)?\|([^>]+)?\">/", "<meta data-react-helmet=\"true\" property=\"og:title\" content=\"" . $DOCUSAURUS_PAGE_TITLE . " | " . DOCUSAURUS_SITE_TITLE . "\">", $generated_page);

    // set page description
    $generated_page = preg_replace("/<meta data-react-helmet=\"true\" name=\"description\" content=\"([^>]+)?\">/", "<meta id=\"dynamic-description\" data-react-helmet=\"true\" name=\"description\" content=\"" . $DOCUSAURUS_PAGE_DESCRIPTION . "\">", $generated_page);
    $generated_page = preg_replace("/<meta data-react-helmet=\"true\" property=\"og:description\" content=\"([^>]+)?\">/", "<meta id=\"dynamic-og:description\" data-react-helmet=\"true\" property=\"og:description\" content=\"" . $DOCUSAURUS_PAGE_DESCRIPTION . "\">", $generated_page);

    echo $generated_page;
    echo "<div id=\"dynamic-content\" class=\"main-wrapper\" style=\"display:none;\">";
  }

  function end_docusaurus_page() {
    $template_page = file_get_contents(DOCUSAURUS_TEMPLATE_FILE);

    // select HTML after end of page content
    $start_idx = strpos($template_page, "<footer");
    $generated_page = substr($template_page, $start_idx);

    // add page capture script
    $start_idx = strpos($generated_page, "<script");
    $generated_page = substr_replace($generated_page, "<script src=\"/dynamic/scripts/page-capture.js\"></script>", $start_idx, 0);

    // add page generate script
    $start_idx = strrpos($generated_page, "</script>") + 9;
    $generated_page = substr_replace($generated_page, "<script src=\"/dynamic/scripts/page-restore.js\"></script>", $start_idx, 0);
    
    echo "</div>";
    echo $generated_page;
  }
?>