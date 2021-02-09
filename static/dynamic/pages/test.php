<?php
  require '../include/docusaurus.php';
  $DOCUSAURUS_PAGE_TITLE = "Dynamic Page";
?>

<?php start_docusaurus_page(); ?>

<main>
  <p>The time is <?php echo date("h:i:sa"); ?></p>
  <p>The current file is <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
</main>

<?php end_docusaurus_page(); ?>