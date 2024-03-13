<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <lastBuildDate>Fri, 23 Jan 2015 23:26:19 +0000</lastBuildDate>
    <title>Примеры фида RSS в Google Новостях</title>
    <description>Это примеры фида RSS в Google Новостях.</description>
    <link>http://google-news-examples.blogspot.com/</link>
    <item>
      <guid isPermaLink="false">sample-post-478956386763692725</guid>
      <pubDate>Fri, 23 Jan 2015 23:17:00 +0000</pubDate>
      <title>Образец сообщения RSS № 1</title>
      <description>Пример статьи RSS.</description>
      <content:encoded>
        <![CDATA[<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras egestas mattis lectus eget porttitor. Nunc iaculis luctus libero, quis viverra mi ultricies sed. Nulla pellentesque dui sed maximus tristique. Sed tempor pulvinar ex in mattis.</p><p><a href="http://www.google.com/">Образец ссылки</a></p><p><a href="https://www.google.com/images/srpr/logo11w.png"><img border="0" height="113" src="https://www.google.com/images/srpr/logo11w.png" width="320" /></a></p><p>Nulla dictum magna orci, et accumsan velit elementum sit amet. Vestibulum egestas, nulla nec facilisis iaculis, elit metus molestie mi, et vulputate enim eros vitae sem. Aliquam eget sagittis dui. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam mattis ex sit amet lectus semper tempus.</p><iframe width="560" height="315" src="//www.youtube.com/embed/200E9e8_hHY" frameborder="0" allowfullscreen></iframe>]]>
          </content:encoded>
      <link>http://google-news-examples.blogspot.com/2015/01/sample-post.html</link>
      <author>pochta@example.com (Анна Иванова)</author>
    </item>
  </channel>
</rss>
XML;
if(!Helper::isUtf()){
	$strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
	<pre><code class="xml"><?=htmlspecialcharsbx($strExample);?></code></pre>
</div>
<script>
$('.acrit-exp-plugin-example pre code.xml').each(function(i, block) {
	highlighElement(block);
});
</script>
