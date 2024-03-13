<?

use Bitrix\Main\ModuleManager;

if (ModuleManager::isModuleInstalled('sale')) {
    $aMenuLinks = Array(
        Array(
            "Основная информация",
            "#SITE_DIR#personal/profile/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Заказы",
            "#SITE_DIR#personal/profile/orders/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Личный счет",
            "#SITE_DIR#personal/profile/account/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Личные данные",
            "#SITE_DIR#personal/profile/private/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "История заказов",
            "#SITE_DIR#personal/profile/orders/?filter_history=Y",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Профили заказов",
            "#SITE_DIR#personal/profile/profiles/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Корзина",
            "#SITE_DIR#personal/basket/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Подписки",
            "#SITE_DIR#personal/profile/subscribe/",
            Array(),
            Array(),
            ""
        )
    );
} else {
    $aMenuLinks = Array(
        Array(
            "Заказы",
            "#SITE_DIR#personal/profile/orders/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Личные данные",
            "#SITE_DIR#personal/profile/private/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Корзина",
            "#SITE_DIR#personal/basket/",
            Array(),
            Array(),
            ""
        )
    );
}

?>