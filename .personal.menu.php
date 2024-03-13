<?

use Bitrix\Main\ModuleManager;

if (ModuleManager::isModuleInstalled('sale')) {
    $aMenuLinks = Array(
        Array(
            "Основная информация",
            "/personal/profile/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Заказы",
            "/personal/profile/orders/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Личный счет",
            "/personal/profile/account/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Личные данные",
            "/personal/profile/private/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "История заказов",
            "/personal/profile/orders/?filter_history=Y",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Профили заказов",
            "/personal/profile/profiles/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Корзина",
            "/personal/basket/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Подписки",
            "/personal/profile/subscribe/",
            Array(),
            Array(),
            ""
        )
    );
} else {
    $aMenuLinks = Array(
        Array(
            "Заказы",
            "/personal/profile/orders/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Личные данные",
            "/personal/profile/private/",
            Array(),
            Array(),
            ""
        ),
        Array(
            "Корзина",
            "/personal/basket/",
            Array(),
            Array(),
            ""
        )
    );
}

?>