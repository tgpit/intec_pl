<?
use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="2017-02-05 17:22">
    <shop>
    <name>BestSeller</name>
    <company>Tne Best inc.</company>
    <url>http://best.seller.ru</url>
    <currencies>
      <currency id="RUR" rate="1"/>
      <currency id="USD" rate="60"/>
    </currencies>
    <categories>
      <category id="1">Бытовая техника</category>
      <category id="10" parentId="1">Мелкая техника для кухни</category>
      <category id="101" parentId="10">Сэндвичницы и приборы для выпечки</category>
      <category id="102" parentId="10">Мороженицы</category>
    </categories>
    <delivery-options>
      <option cost="300" days="0" order-before="12"/>
    </delivery-options>
    <offers>
      <offer id="12346" bid="80">
        <name>Вафельница First FA-5300</name>
        <vendor>First</vendor>
        <vendorCode>A1234567B</vendorCode>
        <url>http://best.seller.ru/product_page.asp?pid=12348</url>
        <price>1490</price>
        <oldprice>1620</oldprice>
        <currencyId>RUR</currencyId>
        <categoryId>101</categoryId>
        <picture>http://best.seller.ru/img/large_12348.jpg</picture>
        <store>false</store>
        <pickup>true</pickup>
        <delivery>true</delivery>
        <delivery-options>
          <option cost="300" days="0" order-before="12"/>
        </delivery-options>
        <description>
        <![CDATA[
          <p>Отличный подарок для любителей венских вафель.</p>
        ]]>
        </description>
        <sales_notes>Необходима предоплата.</sales_notes>
        <manufacturer_warranty>true</manufacturer_warranty>
        <country_of_origin>Россия</country_of_origin>
        <barcode>0156789012</barcode>
        <weight>3.6</weight>
        <dimensions>20.1/20.551/22.5</dimensions>
      </offer>
      <offer id="9012" type="vendor.model" bid="80">
        <typePrefix>Мороженица</typePrefix>
        <vendor>Brand</vendor>
        <model>3811</model>
        <url>http://best.seller.ru/product_page.asp?pid=12345</url>
        <price>8990</price>
        <oldprice>9900</oldprice>
        <currencyId>RUR</currencyId>
        <categoryId>102</categoryId>
        <picture>http://best.seller.ru/img/model_12345.jpg</picture>
        <store>false</store>
        <pickup>false</pickup>
        <delivery>true</delivery>
        <delivery-options>
          <option cost="300" days="1" order-before="18"/>
        </delivery-options>
        <description>
        <![CDATA[
          <h3>Мороженица Brand 3811</h3>
          <p>Это прибор, который придётся по вкусу всем любителям десертов и сладостей, ведь с его помощью вы сможете делать вкусное домашнее мороженое из натуральных ингредиентов.</p>
        ]]>
        </description>
        <param name="Цвет">белый</param>
        <sales_notes>Необходима предоплата.</sales_notes>
        <manufacturer_warranty>true</manufacturer_warranty>
        <country_of_origin>Китай</country_of_origin>
        <barcode>0123456789379</barcode>
        <weight>2.7</weight>
        <dimensions>22.1/20.551/22.5</dimensions>
      </offer>
    </offers>
  </shop>
</yml_catalog>
XML;
if (!Helper::isUtf())
{
    $strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
    <pre><code class="xml"><?= htmlspecialcharsbx($strExample); ?></code></pre>
</div>
<script>
    $('.acrit-exp-plugin-example pre code.xml').each(function (i, block) {
        highlighElement(block);
    });
</script> 