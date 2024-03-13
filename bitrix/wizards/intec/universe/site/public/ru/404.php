<?php include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

/**
 * @var CMain $APPLICATION
 */

CHTTP::SetStatus('404 Not Found');

@define('ERROR_404', 'Y');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Страница не найдена');

?>
<div class="intec-page intec-page-404 intec-content">
    <div class="intec-content-wrapper">
        <div class="intec-grid intec-grid-wrap intec-grid-a-h-center intec-grid-a-v-center intec-grid-i-20">
            <div class="intec-grid-item-2 intec-grid-item-768-1">
                <div class="intec-page-part-picture intec-ui-picture">
                    <img src="<?= SITE_DIR ?>images/404.png">
                </div>
            </div>
            <div class="intec-grid-item-2 intec-grid-item-768-1">
                <div class="intec-grid intec-grid-a-h-center">
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <div class="intec-page-part-content">
                            <div class="intec-page-part-title">
                                Ошибка 404
                            </div>
                            <div class="intec-page-part-description">
                                Страница не найдена
                            </div>
                            <div class="intec-page-part-tip">
                                Неправильно набран адрес или такой страницы не существует
                            </div>
                            <div class="intec-page-part-search">
                                <?php $APPLICATION->IncludeComponent(
                                    "bitrix:search.title",
                                    "input.1",
                                    array(
                                        "NUM_CATEGORIES" => "1",
                                        "TOP_COUNT" => "5",
                                        "ORDER" => "date",
                                        "USE_LANGUAGE_GUESS" => "N",
                                        "CHECK_DATES" => "N",
                                        "SHOW_OTHERS" => "Y",
                                        "INPUT_ID" => "page-title-search-input",
                                        "TIPS_USE" => "Y",
                                        "PAGE" => "#SITE_DIR#search/index.php",
                                        "CATEGORY_0_TITLE" => "",
                                        "CATEGORY_0" => array(
                                            0 => "no",
                                        ),
                                        "CATEGORY_OTHERS_TITLE" => ""
                                    ),
                                    false
                                ) ?>
                            </div>
                            <div class="intec-page-part-buttons intec-grid intec-grid-wrap intec-grid-i-5 intec-grid-a-h-center intec-grid-a-v-center">
                                <div class="intec-grid-item-auto">
                                    <a href="<?= SITE_DIR ?>" class="intec-page-part-button intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2 intec-ui-mod-round-3">
                                        Перейти на главную
                                    </a>
                                </div>
                                <div class="intec-grid-item-auto">
                                    <a href="<?= SITE_DIR ?>catalog/" class="intec-page-part-button intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2 intec-ui-mod-transparent intec-ui-mod-round-3">
                                        Вернуться в каталог
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
    </div>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php') ?>