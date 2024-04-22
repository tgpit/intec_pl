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
.container-598 {
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

	<!-- calltouch -->
	<script>
	(function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n]["callbacks"].push(arguments)};if(!w[n]["callbacks"]){w[n]["callbacks"]=[]}w[n]["loaded"]=false;if(typeof c!=="object"){c=[c]}w[n]["counters"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName("script")[0],s=d.createElement("script"),i=function(){a.parentNode.insertBefore(s,a)},m=typeof Array.prototype.find === 'function',n=m?"init-min.js":"init.js";s.async=true;s.src="https://mod.calltouch.ru/"+n+"?id="+cId;if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",i,false)}else{i()}}})(window,document,"ct","5g9dkgum");
	</script>
	<!-- calltouch -->

    </body>
</html>
<?php if (FileHelper::isFile($directory.'/parts/custom/end.php')) include($directory.'/parts/custom/end.php') ?>
