<?php 
//define('NINJA_PAGE_LANDING', true);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Приглашаем тебя в Мир Питбайков - PitLand!");
$APPLICATION->SetPageProperty('codePage', 'page-about');

?><div class="container">
	<div class="page-about__container">
		<div class="page-about__header">
			<div class="page-about__name">
				<h1>Ты готов стать частью команды?</h1>
			</div>
			<div class="page-about__desc">
				 Приглашаем тебя в Мир Питбайков - PITLAND!
			</div>
		</div>
		<div class="page-about__row">
			<div class="row">
				<div class="col-sm-7">
					 Привет! Давай знакомиться, мы — команда сети магазинов PITLAND. Каждый из нас живет мототемой и верит, что сейчас время безграничных возможностей для самореализации. Наше видение, идеи и стремления мы реализовали в этот проект — мир питбайков, магазин мечты — PITLAND. <br>
					 Создавая PITLAND, мы хотели сделать самый крутой и удобный магазин питбайков в России. Место, где не просто есть все для питбайкера, но и куда нам самим было бы приятно приходить каждый день. На работу нашей мечты, в идеальный магазин питбайков.
				</div>
				<div class="col-sm-5">
 <img src="/local/build/img/pages/about/001.jpg" class="img-rounded img-responsive">
				</div>
			</div>
		</div>
		<div class="page-about__row">
			<div class="row">
				<div class="col-sm-5">
 <img src="/local/build/img/pages/about/002.jpg" class="img-rounded img-responsive">
				</div>
				<div class="col-md-7">
					<div class="page-about__block-title">
						 Магазин «Формула-Х»
					</div>
					 Что-то купить и пообщаться, решить свои проблемы с техникой, узнать новости и «позалипать» на новый экип или видео на мониторах. Важен не только бизнес, но и атмосфера места, мнение и настроение наших клиентов. Поэтому у нас есть собственная трасса для тест-драйва. И самое главное — мы продвигаем тему мотокросса и питбайка в соц-сетях. Ты можешь заценить наш обзор, прокомментировать новый безумный проект, увидеть единомышленников и быть с нами в контакте, разделяя радость от нашего общего хобби.
				</div>
			</div>
		</div>
		<div class="page-about__row">
			<div class="row">
				<div class="col-sm-7">
					<div class="page-about__block-title">
						 Магазин «DEXTER»
					</div>
					<p>
						 Нам приятно осознавать, что в стенах нашего шоу рума, среди грубого дерева, хулиганских скетчей на стенах, яркой и четкой экипировки, запчастей и питбайков рождается интерес к нашей теме у начинающих спортсменов. Для тех же, кто давно в теме, наших старых друзей — это атмосферное место, куда они приходят как в любимый бар или гараж.
					</p>
				</div>
				<div class="col-sm-5">
 <img src="/local/build/img/pages/about/003.jpg" class="img-rounded img-responsive">
				</div>
			</div>
		</div>
		<div class="page-about__row">
			<div class="row">
				<div class="col-sm-5">
 <img src="/local/build/img/pages/about/004.jpg" class="img-rounded img-responsive">
				</div>
				<div class="col-md-7">
					<div class="page-about__block-title">
						 Сервис
					</div>
					<p>
						 Что такое Моточасы? — мы считаем, что это время, проведенное с удовольствием, а не потраченное на ремонт твоего питбайка или мотоцикла.
					</p>
					<p>
						 Залогом долговечной службы аппарата является его правильное и своевременное обслуживание. Мы сами являемся владельцами мототехники и тестируем все, что продаем и обслуживаем. Это помогает нам эффективнее решать вопросы сервиса, с которыми к нам обращаются клиенты.
					</p>
					<p>
						 Мы стремимся сделать так, чтобы твоё увлечение приносило только положительные эмоции. Экономя твоё время, мы выполним любые виды работ по ремонту и обслуживанию мототехники всех марок и моделей. Не важно, будет ли это простое ТО, шиномонтаж, регулировки или полная переборка двигателя, сварочные работы или нестандартный ремонт — мы гарантируем эффективное решение.
					</p>
				</div>
			</div>
		</div>
		<div class="page-about__row">
			<div class="page-about__block-title">
				 <?$APPLICATION->IncludeComponent(
	"intec.universe:main.widget", 
	"zapis", 
	array(
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "zapis",
		"WEB_FORM_ID" => "23",
		"SETTINGS_USE" => "N",
		"LAZYLOAD_USE" => "N",
		"CONSENT_SHOW" => "N",
		"WEB_FORM_TITLE_SHOW" => "Y",
		"WEB_FORM_DESCRIPTION_SHOW" => "Y",
		"WEB_FORM_BACKGROUND" => "theme",
		"WEB_FORM_BACKGROUND_OPACITY" => "80",
		"WEB_FORM_TEXT_COLOR" => "light",
		"WEB_FORM_POSITION" => "left",
		"WEB_FORM_ADDITIONAL_PICTURE_SHOW" => "N",
		"BLOCK_BACKGROUND" => "/local/build/img/feed-back/bg.jpg",
		"BLOCK_BACKGROUND_PARALLAX_USE" => "N",
		"BLOCK_BACKGROUND_PARALLAX_RATIO" => "10"
	),
	false
);?><br>
				 &nbsp; &nbsp; &nbsp; 8 причин приехать в наши салоны на Юге и Севере Москвы:
			</div>
			<div class="page-about__list">
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/001.jpg" alt="">
					</div>
					<div class="page-about__list-text">
						 Премия «Лучший Мотосалон Avantis, JMC, BSE». В наличии все модели Питбайков, которые представлены на рынке: <b>Kayo, BSE, JMC, YCF, Apollo, Motoland, Wels, Virus Moto, Racer, Pitrace</b>. Также у нас вы найдете квадроциклы <b>Motax, Avantis</b> и полноразмерные мотоциклы <b>KAYO, BSE, Motoland, Baltmotors</b>. Мы можем дать вам возможность выбрать, а консультация от наших менеджеров сделает этот выбор обоснованным.
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/002.jpg" alt="">
					</div>
					<div class="page-about__list-text">
						 В нашем магазине огромный выбор экипировки и большой ассортимент запчастей, резины и аксессуаров. Вам не придется ехать в другой магазин, вы купите все в одном месте.
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/003.jpg" alt="">
					</div>
					<div class="page-about__list-text">
						 Мы доставим вам технику по Москве и области, и в любой регион России, и это будет бесплатно<br>
 <sup>*</sup>(<a href="/help/delivery/">есть регионы-исключения</a>)
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/004.jpg" alt="">
					</div>
					<div class="page-about__list-text">
						 Возможность купить в кредит и в рассрочку, за час, в том числе он-лайн.
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/009.jpg" alt="">
					</div>
					<div class="page-about__list-text">
						 Есть возможность заказать Премиальную сборку в нашем сервисе
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/006.jpg" alt="">
					</div>
					<div class="page-about__list-text">
 <b>PITLAND</b> является авторизированным мотосервисом всех марок, представленных в нашем магазине. Вы сможете делать ТО и любые ремонтные и гарантийные работы. В правильном месте и по приемлемым ценам.
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/007.jpg" alt="">
					</div>
					<div class="page-about__list-text">
 <b>Более 1000</b> довольных покупателей.
					</div>
				</div>
				<div class="page-about__list-item">
					<div class="page-about__list-icon">
 <img src="/local/build/img/pages/about/icons/008.jpg" alt="">
					</div>
					<div class="page-about__list-text">
 <b>Мы действительно знаем о Питбайках все!</b>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <br><?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php') ?>