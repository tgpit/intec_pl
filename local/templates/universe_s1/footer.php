<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\FileHelper;

global $APPLICATION;
global $USER;
global $directory;
global $properties;
global $template;
global $part;

if (empty($template))
    return;

?>
        <?php include($directory.'/parts/'.$part.'/footer.php'); ?>
        <?php if (FileHelper::isFile($directory.'/parts/custom/body.end.php')) include($directory.'/parts/custom/body.end.php') ?>
<script>
    $('#tablModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var img = button.data('img');
        var modal = $(this);
        modal.find('.modal-img').html("<img src='"+img+"' class='img-fluid'/>");
    })
</script>
<style>
.container-903 {
  padding: 0px !important;
  margin: 0px !important;
}
.container-597 {
  padding: 0px !important;
  margin: 0px !important;
}
.container-1121 {
  margin-bottom: 10px !important;
  margin-top: 10px !important;
}
.container-1062 {
  margin-bottom: 0px !important;
  margin-top: 0px !important;
}
.catalog-element-gallery-pictures-slider-item {
  margin-bottom: 0px !important;
  margin-top: 0px !important;
  height: 320px !important;
}

.bld {
  font-style: bold;
}

</style>
    </body>
</html>
<?php if (FileHelper::isFile($directory.'/parts/custom/end.php')) include($directory.'/parts/custom/end.php') ?>
