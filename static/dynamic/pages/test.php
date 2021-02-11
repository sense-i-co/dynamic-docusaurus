<?php
  require '../include/docusaurus.php';
  $DOCUSAURUS_PAGE_TITLE = "Dynamic Page";
  $DOCUSAURUS_PAGE_DESCRIPTION = "This is a dynamic page in Docusaurus!";
?>

<?php start_docusaurus_page(); ?>

<main>
  <div class="container" style="padding-top:20px;">
    <h1>Dynamic Page</h1>
    <p>The time is <?php echo date("h:i:sa"); ?></p>
    <p>The current file is <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
  </div>
</main>

<?php end_docusaurus_page(); ?>