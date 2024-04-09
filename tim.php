<?
$time = -microtime(true);
sleep(5);
$end = sprintf('%f', $time += microtime(true));
echo $end;
?>