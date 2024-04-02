<?php
$df = disk_free_space("./");
echo $df."<BR>";
//exec('tar -czvf ./upload.tar.gz ../upload');
echo "OK";
//exec('service mysql status > ./stat.txt');
