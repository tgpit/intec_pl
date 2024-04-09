<?php
use Bitrix\Main\Application;
use Ninja\Project\Catalog\CatalogReportAvailabilityGateway;
?>
	<? $time14 = microtime(true); ?>
<?php if ((CSite::InDir('/katalog/')) || (CSite::InDir('/katalog/aksessuary_/'))): ?>
  <div class="panel-group" id="accordion" style="clear: both;">
    <!-- 1 панель -->
    <div class="panel panel-default">
      <!-- Заголовок 1 панели -->
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Древовидное меню каталога&nbsp;&nbsp;<i class="fa fa-caret-square-o-down" aria-hidden="true"></i></a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse">
        <!-- Содержимое 1 панели -->
        <div class="panel-body">
          <p>
          <div class="col-xs-12">
            <ul id="tree1">
	<? $time15 = microtime(true); ?>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "catalog-all-menu-multi-lvl",
                    array(
                        "COMPONENT_TEMPLATE" => "catalog-all-menu-multi-lvl",
                        "ROOT_MENU_TYPE" => "catalogleft",
                        "MENU_CACHE_TYPE" => "N",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "3",
                        "CHILD_MENU_TYPE" => "left",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                );?>
	<? $time16 = microtime(true); ?>
            </ul>
          </div>
          </p>
        </div>
      </div>
    </div>
  </div>
<?php endif ?>

<!-- Modal add Basket -->
<!-- Modal -->
<div class="modal fade" id="AddBasket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Товар добавлен в корзину</h4>
      </div>
      <div class="modal-body text-center">
        <img width="65" height="56" src="<?= $APPLICATION->GetTemplatePath('img/confirm-window-basket.png'); ?>" alt="Товар добавлен в корзину">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default pull-left" data-dismiss="modal">Продолжить покупки</button>
        <a href="/cart/"><button type="button" class="btn btn-sm btn-warning pull-right">Перейти в корзину</button></a>
      </div>
    </div>
  </div>
</div>
<!-- /Modal add Basket -->

<!-- Modal report availability -->
	<? $time17 = microtime(true); ?>
<?php
$request = Application::getInstance()->getContext()->getRequest();
$reportAvailabilityData = unserialize($request->getCookie(CatalogReportAvailabilityGateway::EVENT_NAME), ['allowed_classes' => false]);
?>

<div class="modal fade" id="procatAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Получить договор</h4>
			</div>
			<div class="modal-body text-center">
	<? $time18 = microtime(true); ?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:form.result.new",
					"prokat",
					array(
						"COMPONENT_TEMPLATE" => "feedback",
						"WEB_FORM_ID" => "2",
						"IGNORE_CUSTOM_TEMPLATE" => "Y",
						"USE_EXTENDED_ERRORS" => "Y",
						"SEF_MODE" => "N",
						"SEF_FOLDER" => "/prokat/",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "3600",
						"LIST_URL" => "",
						"EDIT_URL" => "",
						"SUCCESS_URL" => "",
						"CHAIN_ITEM_TEXT" => "",
						"CHAIN_ITEM_LINK" => "",
						"USE_CAPTCHA" => "N",
						"AJAX_MODE" => "Y",
						"VARIABLE_ALIASES" => array(
							"WEB_FORM_ID" => "WEB_FORM_ID",
							"RESULT_ID" => "RESULT_ID",
						)
					),
					false
				);?>
	<? $time19 = microtime(true); ?>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="reportAvailabilityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Сообщить о наличии</h4>
      </div>
      <div class="modal-body text-center">
        <form method="post" id="reportAvailabilityForm">
          <input name="id" value="" type="hidden" />
          <div class="default-form">
            <div class="form-item">
              <div class="form-item__name">Имя</div>
              <div class="form-item__field">
                <input type="text" name="name" value="<?=$reportAvailabilityData['name'] ?? ''?>" class="form-field form-field_m-size" />
                <div class="form-item__error"></div>
              </div>
            </div>
            <div class="form-item">
              <div class="form-item__name">Телефон</div>
              <div class="form-item__field">
                <input type="text" name="phone" value="<?=$reportAvailabilityData['phone'] ?? ''?>" class="form-field form-field_m-size" />
                <div class="form-item__error"></div>
              </div>
            </div>
            <div class="form-item">
              <div class="form-item__name">Email</div>
              <div class="form-item__field">
                <input type="text" name="email" value="<?=$reportAvailabilityData['email'] ?? ''?>" class="form-field form-field_m-size" />
                <div class="form-item__error"></div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default pull-left js-submit-modal">Отправить</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal report availability -->

</div><!-- end general role -->

	<footer class="container-fluid nopadding" style="clear: both; background-color:#353535;">
		<div class="col-xs-12 text-center" style="padding: 10px 5px 10px 20px;">
			<a href="http://tvoygaraj.ru/novosti/shop/luchshiy-diler-stels-tvoygarazh-ru.html">
				<div class="col-xs-2 nopadding">
					<img src="<?= $APPLICATION->GetTemplatePath('img/site-emblem.png'); ?>" style="width: 100%;" alt="tvoygaraj.ru" class="site-footer__emblem">
				</div>
				<div class="col-xs-10 nopadding" style="color:#f8f6f8">
					Лучший официальный дилер STELS*<br>
					<span>© 2008 - <?=date('Y')?> <?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/copyright.php"), array(), array("MODE" => "php"))?></span>
				</div>
			</a>
		</div>
		
		<div style="padding:10px 0px 10px 0px">
		<div class="col-xs-12 text-center">
			<a href="mailto:info@tvoygaraj.ru"><span style="font-size: 18px;color:#f8f6f8">info@tvoygaraj.ru</span></a>
		</div>
		
		<div class="col-xs-12 text-center">
			<a href="tel:+74956424303"><span style="font-size: 18px;color:#f8f6f8">+7 (495) 642-43-03</span></a>
		</div>
		</div>
		
		<div class="col-xs-2"></div>
		<div class="text-center col-xs-8" style="padding-top: 10px;padding-bottom: 10px;">
			<a rel="nofollow" href="http://velomotors.ru" target="_blank"><img style="width: 100%;" src="/img/stels_y.png"></a>
		</div>
		<div class="col-xs-2"></div>
		
		<div class="col-xs-12" style="font-size: 16px;color:#f8f6f8;padding-top: 10px;padding-bottom: 10px;">
			<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i><br>
			МКАД 27 км<br>ТЦ Формула Х, 2 этаж<br>
			<span><a style="color:white;" href="/o-nas-new/policy.php">ПРАВОВАЯ ИНФОРМАЦИЯ</a></span>
 		</div>
		<div class="col-xs-12" style="font-size: 16px;color:#f8f6f8;padding-top: 10px;padding-bottom: 10px;">
<div align="right"><iframe src="https://yandex.ru/sprav/widget/rating-badge/1340896685?type=rating" width="150" height="50" frameborder="0"></iframe></div>
</div>
		<div class="text-center col-xs-8 nopadding">
			<a href="https://vk.com/tvoygaraj" style="margin: 0px -10px 0px -10px;">
				<span class="fa-stack fa-2x" style="margin-left: -30px;">
					<i class="fa fa-circle fa-stack-1x" style="font-size: 45px;color:#000"></i>
					<i class="fa fa-vk fa-stack-1x fa-inverse"></i>
				</span>
			</a>
			<a href="https://www.youtube.com/user/TheUnknown0105" style="margin: 0px -10px 0px -10px;">
				<span class="fa-stack fa-2x">
					<i class="fa fa-circle fa-stack-1x" style="font-size: 45px;color:#000"></i>
					<i class="fa fa-youtube fa-stack-1x fa-inverse"></i>
				</span>
			</a>
			
		</div>
		<div class="col-xs-4 nopadding" style="font-size: 16px;color:#f8f6f8;">
			<div style="margin-left: -30px;padding-top: 15%;">Подписывайся!</div>
		</div>

        <div class="text-center col-xs-12" style="color:#f8f6f8">
        * Обращаем Ваше внимание, что вся информация, опубликованная на сайте, носит исключительно справочно-информационный характер и ни при каких обстоятельства не является публичной офертой, определяемой положениями ч.2 ст.437 Гражданского кодекса Российской Федерации. Для получения подробной информации воспользуйтесь контактами, указанными на сайте. 
        </div>

    <div class="text-center col-xs-12" style="color: white; margin-bottom: 20px">
      <div class="col-xs-6 text-left"><button type="button" id="JivoChat" class="btn btn-success">Консультант</button></div>
      <div class="col-xs-6 text-right"><button id="toTop" type="button" class="btn btn-info">^ Наверх</button></div>
    </div>


	</footer>

	<div id="space_basket" style="background: #353535;"></div>
	
	<!— BEGIN JIVOSITE CODE {literal} —>
	<script type='text/javascript'>
	(function(){ var widget_id = 'mtsIb6Ns5G';
	var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
	<!— {/literal} END JIVOSITE CODE —>

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-73903174-1', 'auto');
	  ga('require', 'displayfeatures'); // это для ремаркетинга
	  
	  if (location.hostname === 'tvoygaraj.ru') { // это для того, чтобы не ключил вебвизор
		  $.ajax({
		  url: '/getip.php',    // код этого скрипта ниже  
		  dataType : "html",                     
		  success: function (data, textStatus) {
			 ga('set', 'dimension1', data); // тут мы записали IP в переменную Google.Universal 
			 ga('send', 'pageview');
		  } 
	   });
	 setTimeout (function() { // тут мы каждые 15 секунд сообщаем гуглу, что человек на сайте. Чтобы процент отказов был ближе к Яндекс.Метрике
	  ga('send', 'event', 'New Visitor', location.pathname);
	 }, 15000);
	  } else {
		  ga('send', 'pageview');
	  }
	
	</script>
	
	
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function (d, w, c) {
			(w[c] = w[c] || []).push(function() {
				try {
					w.yaCounter35940850 = new Ya.Metrika({
						id:35940850,
						clickmap:true,
						trackLinks:true,
						accurateTrackBounce:true,
						webvisor:true
					});
				} catch(e) { }
			});
	
			var n = d.getElementsByTagName("script")[0],
				s = d.createElement("script"),
				f = function () { n.parentNode.insertBefore(s, n); };
			s.type = "text/javascript";
			s.async = true;
			s.src = "https://mc.yandex.ru/metrika/watch.js";
	
			if (w.opera == "[object Opera]") {
				d.addEventListener("DOMContentLoaded", f, false);
			} else { f(); }
		})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript><div><img src="https://mc.yandex.ru/watch/35940850" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
	<? $time20 = microtime(true); ?>
  </body>
</html>
<?
$filename = '1/timlog.txt';
$data  = "mob: 1  - ".$time1-$time0;
$data .= " | 2  - ".$time2-$time1;
$data .= " | 3  - ".$time3-$time2;
$data .= " | 4  - ".$time4-$time3;
$data .= " | 5  - ".$time5-$time4;
$data .= " | 6  - ".$time6-$time5;
$data .= " | 7  - ".$time7-$time6;
$data .= " | 8  - ".$time8-$time7;
$data .= " | 9  - ".$time9-$time8;
$data .= " | 10 - ".$time10-$time9;
$data .= " | 11 - ".$time11-$time10;
$data .= " | 12 - ".$time12-$time11;
$data .= " | 13 - ".$time13-$time12;
$data .= " | 14 - ".$time14-$time13;
$data .= " | 15 - ".$time15-$time14;
$data .= " | 16 - ".$time16-$time15;
$data .= " | 17 - ".$time17-$time16;
$data .= " | 18 - ".$time18-$time17;
$data .= " | 19 - ".$time19-$time18;
$data .= " | 20 - ".$time20-$time19;
$data .= " | All - ".$time20-$time0;

$fl = fopen($filename, 'a');
flock($fl, LOCK_EX);
$data .= "\n";
fwrite($fl, $data);
flock($fl, LOCK_UN);
fclose($fl);

//file_put_contents($filename, $data);
?>