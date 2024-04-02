<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;

$arResult['PROPERTIES'] = ArrayHelper::merge($arResult['PROPERTIES'], [
    'template-background-show' => [
        'grid' => ['size' => 2]
    ],
    'template-background-color' => [
        'grid' => ['size' => 2]
    ],
    'template-images-lazyload-use' => [
        'title' => 'inner'
    ],
    'template-menu-show' => [
        'title' => 'inner'
    ],
    'template-cache' => [
        'title' => 'inner'
    ],
    'base-regionality-use' => [
        'title' => 'inner'
    ],
    'base-consent' => [
        'title' => 'inner'
    ],
    'breadcrumb-dropdown-use' => [
        'title' => 'inner'
    ],
    'basket-use' => [
        'title' => 'inner'
    ],
    'basket-delay-use' => [
        'title' => 'inner'
    ],
    'basket-compare-use' => [
        'title' => 'inner'
    ],
    'basket-fixed-auto' => [
        'title' => 'inner'
    ],
    'basket-notifications-use' => [
        'title' => 'inner'
    ],
    'basket-total-fixed' => [
        'title' => 'inner'
    ],
    'basket-quick-view-use' => [
        'title' => 'inner'
    ],
    'basket-confirm-remove-product-use' => [
        'title' => 'inner'
    ],
    'profile-claims-use' => [
        'title' => 'inner'
    ],
    'profile-add-use' => [
        'title' => 'inner'
    ],
    'profile-crm-use' => [
        'title' => 'inner'
    ],
    'profile-viewed-product-use' => [
        'title' => 'inner'
    ],
    'catalog-detail-fast-order-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-sizes-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-information-shipment-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-information-payment-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-gift-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-gift-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-list-gift-show' => [
        'title' => 'inner'
    ],
    'catalog-list-timer-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-timer-show' => [
        'title' => 'inner'
    ],
    'catalog-quick-view-timer-show' => [
        'title' => 'inner'
    ],
    'catalog-list-interest-products-show' => [
        'title' => 'inner'
    ],
    'catalog-quick-view-use' => [
        'title' => 'inner'
    ],
    'catalog-quick-view-detail' => [
        'title' => 'inner'
    ],
    'catalog-quick-view-slider-use' => [
        'title' => 'inner'
    ],
    'catalog-detail-panel-show' => [
        'title' => 'inner'
    ],
    'catalog-menu-submenu-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-root-menu-show' => [
        'title' => 'inner'
    ],
    'catalog-sections-menu-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-menu-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-delivery-calculation-use' => [
        'title' => 'inner'
    ],
    'catalog-recalculation-price-use' => [
        'title' => 'inner'
    ],
    'catalog-detail-form-cheaper-show' => [
        'title' => 'inner'
    ],
    'catalog-section-articles-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-articles-show' => [
        'title' => 'inner'
    ],
    'catalog-detail-price-credit-show' => [
        'title' => 'inner'
    ],
    'catalog-filter-ajax' => [
        'title' => 'inner'
    ],
    'catalog-filter-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-root-layout' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-root-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-sections-layout' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-sections-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-elements-tile-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-elements-tile-image-aspect-ratio' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-detail-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-detail-main-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'catalog-quick-view-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'footer-products-viewed-show' => [
        'title' => 'inner'
    ],
    'footer-template' => [
        'view' => 'list.template'
    ],
    'basket-header-popup-show' => [
        'title' => 'inner'
    ],
    'header-template' => [
        'view' => 'list.template'
    ],
    'header-fixed-use' => [
        'title' => 'inner'
    ],
    'header-fixed-menu-popup-show' => [
        'title' => 'inner'
    ],
    'header-registration-use' => [
        'title' => 'inner'
    ],
    'header-menu-main-section-template' => [
        'view' => 'list.template'
    ],
    'header-menu-popup-template' => [
        'view' => 'list.template'
    ],
    'header-search-popup-template' => [
        'view' => 'list.template'
    ],
    'header-mobile-template' => [
        'view' => 'list.template'
    ],
    'header-menu-uppercase-use' => [
        'title' => 'inner'
    ],
    'header-menu-overlay-use' => [
        'title' => 'inner'
    ],
    'header-mobile-fixed' => [
        'title' => 'inner'
    ],
    'header-mobile-hidden' => [
        'title' => 'inner'
    ],
    'header-mobile-separated-text' => [
        'title' => 'inner'
    ],
    'header-mobile-menu-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'header-mobile-banner-view' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'pages-main-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-shares-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-staff-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-contacts-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-news-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-articles-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-articles-products-categories-show' => [
        'title' => 'inner'
    ],
    'sections-articles-products-show' => [
        'title' => 'inner'
    ],
    'sections-brands-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-brands-filter-use' => [
        'title' => 'inner'
    ],
    'sections-brands-sections-show' => [
        'title' => 'inner'
    ],
    'sections-brands-products-show' => [
        'title' => 'inner'
    ],
    'sections-jobs-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-blog-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-certificates-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-collections-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-photo-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'sections-collections-products-show' => [
        'title' => 'inner'
    ],
    'services-root-menu-show' => [
        'title' => 'inner'
    ],
    'services-root-sections-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 3
        ]
    ],
    'services-root-list-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 3
        ]
    ],
    'services-children-menu-show' => [
        'title' => 'inner'
    ],
    'services-filter-use' => [
        'title' => 'inner'
    ],
    'services-filter-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'services-children-sections-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 3
        ]
    ],
    'services-children-list-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 3
        ]
    ],
    'services-element-template' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'services-element-type' => [
        'view' => 'list.template',
        'view.grid' => [
            'size' => 2
        ]
    ],
    'mobile-panel-hidden' => [
        'title' => 'inner'
    ],
    'mobile-panel-breadcrumbs-compact' => [
        'title' => 'inner'
    ],
    'mobile-panel-breadcrumbs-compact-slider' => [
        'title' => 'inner'
    ],
    'mobile-catalog-detail-panel-show' => [
        'title' => 'inner'
    ]
]);