<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arServices = [
    'main' => [
        'NAME' => Loc::getMessage('wizard.services.main'),
        'STAGES' => [
            'files.php',
            'search.php',
            'menu.php',
            'url.php',
            'template.php',
            'fonts.php',
            'blocks.php',
            'theme.php',
            'group.php'
        ]
    ],
    'form' => [
        'NAME' => Loc::getMessage('wizard.services.form'),
        'STAGES' => [
            'call.php',
            'feedback.php',
            'cheaper.php',
            'rate.php',
            'specialist.php',
            'product.php',
            'request.php',
            'project.php',
            'question.php',
            'service.php',
            'vacancy.php'
        ]
    ],
    'intec.startshop' => [
        'NAME' => Loc::getMessage('wizard.services.sale'),
        'STAGES' => [
            'shop.currencies.php',
            'shop.prices.php',
            'shop.orders.properties.php',
            'shop.orders.statuses.php',
            'shop.deliveries.php',
            'shop.payments.php',
            'form.call.php',
            'form.feedback.php',
            'form.cheaper.php',
            'form.rate.php',
            'form.specialist.php',
            'form.product.php',
            'form.request.php',
            'form.project.php',
            'form.question.php',
            'form.service.php',
            'form.vacancy.php',
            'right.php'
        ]
    ],
    'highloadblock' => [
        'NAME' => Loc::getMessage('wizard.services.highloadblock'),
        'STAGES' => []
    ],
    'iblock' => [
        'NAME' => Loc::getMessage('wizard.services.iblock'),
        'STAGES' => [
            'types.php',
            'link.files.php',
            'link.template.php',
            'link.iblocks.php'
        ]
    ],
    'sale' => [
        'NAME' => Loc::getMessage('wizard.services.sale'),
        'STAGES' => [
            'locations.php',
            'step1.php',
            'step2.php',
            'step3.php'
        ]
    ],
    'intec.regionality' => [
        'NAME' => Loc::getMessage('wizard.services.regionality'),
        'STAGES' => [
            'regions.php'
        ]
    ]
];

/** CUSTOM START */

$arServices['iblock']['STAGES'] = [
    $arServices['iblock']['STAGES'][0],

    'import.content.banners.php',
    'import.content.banners.small.php',
    'import.content.icons.php',
    'import.content.about.php',
    'import.content.advantages.php',
    'import.content.advantages.2.php',
    'import.content.advantages.3.php',
    'import.content.advantages.4.php',
    'import.content.banners.categories.php',
    'import.content.banners.categories.2.php',
    'import.content.banners.categories.3.php',
    'import.content.rates.php',
    'import.catalogs.products.php',
    'import.catalogs.products.offers.php',
    'import.catalogs.products.reviews.php',
    'import.catalogs.services.php',
    'import.catalogs.services.icons.php',
    'import.catalogs.services.gallery.php',
    'import.catalogs.services.reviews.php',
    'import.catalogs.services.stages.php',
    'import.catalogs.services.stages.2.php',
    'import.content.shares.php',
    'import.content.shares.promo.php',
    'import.content.shares.conditions.php',
    'import.content.news.php',
    'import.content.articles.php',
    'import.content.reviews.php',
    'import.content.jobs.php',
    'import.content.staff.php',
    'import.content.certificates.php',
    'import.content.client.help.php',
    'import.content.collections.php',
    'import.content.imagery.php',
    'import.content.blog.php',
    'import.content.projects.php',
    'import.content.video.php',
    'import.content.photo.php',
    'import.content.faq.php',
    'import.content.brands.php',
    'import.content.stories.php',
    'import.content.contacts.php',
    'import.content.panel.php',

    $arServices['iblock']['STAGES'][1],
    $arServices['iblock']['STAGES'][2],
    $arServices['iblock']['STAGES'][3],
];

/** CUSTOM END */