<? include('.begin.php') ?>
<?

use intec\core\base\Collection;

/**
 * @var Collection $data
 * @var string $mode
 * @var CWizardBase $wizard
 * @var CWizardStep $this
 */

$macros = $data->get('macros');

/** CUSTOM START */

if ($mode == WIZARD_MODE_INSTALL) {
    /** Привязка услуг */
    if (!empty($macros['CATALOGS_SERVICES_IBLOCK_ID'])) {
        $arServices = [];
        $rsServices = CIBlockElement::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $macros['CATALOGS_SERVICES_IBLOCK_ID']
        ]);

        while ($arService = $rsServices->Fetch())
            $arServices[] = $arService;

        unset($rsServices, $arService);

        /** Привязка иконок */
        if (!empty($macros['CATALOGS_SERVICES_ICONS_IBLOCK_ID'])) {
            $arMap = [];
            $arIcons = [];
            $rsIcons = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CATALOGS_SERVICES_ICONS_IBLOCK_ID']
            ]);

            while ($arIcon = $rsIcons->Fetch())
                $arIcons[] = $arIcon;

            if (!empty($arIcons))
                foreach ($arServices as $arService) {
                    $iCount = 0;

                    if (empty($arService['CODE']))
                        continue;

                    $arMap[$arService['CODE']] = [];

                    foreach ($arIcons as $arIcon) {
                        if (empty($arIcon['CODE']))
                            continue;

                        $arMap[$arService['CODE']][] = $arIcon['CODE'];
                        $iCount++;

                        if ($iCount >= 5)
                            break;
                    }
                }

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'ICONS_ELEMENTS', $macros['CATALOGS_SERVICES_ICONS_IBLOCK_ID'], $arMap);

            unset($arService, $rsIcons, $arIcons, $arIcon, $arMap);
        }

        /** Привязка галереи */
        if (!empty($macros['CATALOGS_SERVICES_GALLERY_IBLOCK_ID'])) {
            $arMap = [];
            $arPictures = [];
            $rsPictures = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CATALOGS_SERVICES_GALLERY_IBLOCK_ID']
            ]);

            while ($arPicture = $rsPictures->Fetch())
                $arPictures[] = $arPicture;

            foreach ($arServices as $arService) {
                if (empty($arService['CODE']))
                    continue;

                $arMap[$arService['CODE']] = [];

                foreach ($arPictures as $arPicture) {
                    if (empty($arPicture['CODE']))
                        continue;

                    if (\intec\core\helpers\StringHelper::startsWith($arPicture['CODE'], $arService['CODE']))
                        $arMap[$arService['CODE']][] = $arPicture['CODE'];
                }
            }

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'GALLERY_ELEMENTS', $macros['CATALOGS_SERVICES_GALLERY_IBLOCK_ID'], $arMap);

            unset($arService, $rsPictures, $arPictures, $arPicture, $arMap);
        }

        /** Привязка видеогалереи */
        if (!empty($macros['CONTENT_VIDEO_IBLOCK_ID'])) {
            $arMap = [];
            $arVideos = [];
            $rsVideos = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CONTENT_VIDEO_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]);

            while ($arVideo = $rsVideos->Fetch())
                $arVideos[] = $arVideo;

            if (!empty($arVideos))
                foreach ($arServices as $arService) {
                    $iCount = 0;

                    if (empty($arService['CODE']))
                        continue;

                    $arMap[$arService['CODE']] = [];

                    foreach ($arVideos as $arVideo) {
                        if (empty($arVideo['CODE']))
                            continue;

                        $arMap[$arService['CODE']][] = $arVideo['CODE'];
                        $iCount++;

                        if ($iCount >= 7)
                            break;
                    }
                }

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'VIDEOS_ELEMENTS', $macros['CONTENT_VIDEO_IBLOCK_ID'], $arMap);

            unset($arService, $rsVideos, $arVideos, $arVideo, $arMap);
        }

        /** Привязка проектов */
        if (!empty($macros['CONTENT_PROJECTS_IBLOCK_ID'])) {
            $arMap = [
                'car_repair' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ],
                'locksmith_repair' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ],
                'installation_of_interior_doors' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ],
                'windows_installation' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ],
                'car_wash_service' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ],
                'realtor_services' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ],
                'sale_and_rent' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ]
            ];

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'PROJECTS_ELEMENTS', $macros['CONTENT_PROJECTS_IBLOCK_ID'], $arMap);

            foreach ($arMap as &$arMapPart)
                $arMapPart = array_slice($arMapPart, 0, 2, true);

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'PROJECTS_2_ELEMENTS', $macros['CONTENT_PROJECTS_IBLOCK_ID'], $arMap);

            unset($arMap, $arMapPart);
        }

        /** Привязка отзывов */
        if (!empty($macros['CATALOGS_SERVICES_REVIEWS_IBLOCK_ID'])) {
            $arMap = [];
            $arReviews = [];
            $rsReviews = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CATALOGS_SERVICES_REVIEWS_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]);

            while ($arReview = $rsReviews->Fetch())
                $arReviews[] = $arReview;

            if (!empty($arReviews))
                foreach ($arServices as $arService) {
                    $iCount = 0;

                    if (empty($arService['CODE']))
                        continue;

                    $arMap[$arService['CODE']] = [];

                    foreach ($arReviews as $arReview) {
                        if (empty($arReview['CODE']))
                            continue;

                        $arMap[$arService['CODE']][] = $arReview['CODE'];
                        $iCount++;

                        if ($iCount >= 3)
                            break;
                    }
                }

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'REVIEWS_ELEMENTS', $macros['CATALOGS_SERVICES_REVIEWS_IBLOCK_ID'], $arMap);

            unset($arService, $rsReviews, $arReviews, $arReview, $arMap);
        }

        /** Привязка сопутствующих услуг */
        $arMap = [];

        if (!empty($arServices)) {
            $arServicesChild = [];
            $iCount = 0;

            foreach ($arServices as $arService) {
                if (empty($arService['CODE']))
                    continue;

                $arServicesChild[] = $arService['CODE'];
                $iCount++;

                if ($iCount >= 4)
                    break;
            }

            foreach ($arServices as $arService) {
                if (empty($arService['CODE']))
                    continue;

                $arMap[$arService['CODE']] = $arServicesChild;
            }
        }

        $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'SERVICES_ELEMENTS', $macros['CATALOGS_SERVICES_IBLOCK_ID'], $arMap);
        $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'SERVICES_2_ELEMENTS', $macros['CATALOGS_SERVICES_IBLOCK_ID'], $arMap);

        unset($arService, $iCount, $arServicesChild, $arMap);

        /** Привязка сопутствующих товаров */
        if (!empty($macros['CATALOGS_PRODUCTS_IBLOCK_ID'])) {
            $arMap = [];
            $arProducts = [];
            $rsProducts = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CATALOGS_PRODUCTS_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]);

            while ($arProduct = $rsProducts->Fetch())
                $arProducts[] = $arProduct;

            if (!empty($arProducts)) {
                $arProductsChild = [];
                $iCount = 0;

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arProductsChild[] = $arProduct['CODE'];
                    $iCount++;

                    if ($iCount >= 4)
                        break;
                }

                foreach ($arServices as $arService) {
                    if (empty($arService['CODE']))
                        continue;

                    $arMap[$arService['CODE']] = $arProductsChild;
                }
            }

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'PRODUCTS_ELEMENTS', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], $arMap);

            unset($arService, $iCount, $arProduct, $arProducts, $arProductsChild, $arMap);
        }

        /** Привязка тарифов */
        if (!empty($macros['CONTENT_RATES_IBLOCK_ID'])) {
            $arMap = [
                'car_repair' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ],
                'locksmith_repair' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ],
                'installation_of_interior_doors' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ],
                'windows_installation' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ],
                'car_wash_service' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ],
                'realtor_services' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ],
                'sale_and_rent' => [
                    'shop',
                    'corporate',
                    'start',
                    'business'
                ]
            ];

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'RATES_ELEMENTS', $macros['CONTENT_RATES_IBLOCK_ID'], $arMap);
        }

        /** Привязка вопрос-ответ */
        if (!empty($macros['CONTENT_FAQ_IBLOCK_ID'])) {
            $arMap = [
                'car_repair' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ],
                'locksmith_repair' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ],
                'installation_of_interior_doors' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ],
                'windows_installation' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ],
                'car_wash_service' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ],
                'realtor_services' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ],
                'sale_and_rent' => [
                    'question_1',
                    'question_2',
                    'question_3',
                    'question_4',
                    'question_5'
                ]
            ];

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'FAQ_ELEMENTS', $macros['CONTENT_FAQ_IBLOCK_ID'], $arMap);
        }

        /** Привязка стадий */
        if (!empty($macros['CATALOGS_SERVICES_STAGES_IBLOCK_ID'])) {
            $arMap = [
                'car_repair' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ],
                'locksmith_repair' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ],
                'installation_of_interior_doors' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ],
                'windows_installation' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ],
                'car_wash_service' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ],
                'realtor_services' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ],
                'sale_and_rent' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4',
                    'stage_5',
                    'stage_6',
                    'stage_7',
                    'stage_8',
                    'stage_9'
                ]
            ];

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'STAGES_1_ELEMENTS', $macros['CATALOGS_SERVICES_STAGES_IBLOCK_ID'], $arMap);
        }

        /** Привязка стадий 2 */
        if (!empty($macros['CATALOGS_SERVICES_STAGES_2_IBLOCK_ID'])) {
            $arMap = [
                'car_repair' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ],
                'locksmith_repair' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ],
                'installation_of_interior_doors' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ],
                'windows_installation' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ],
                'car_wash_service' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ],
                'realtor_services' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ],
                'sale_and_rent' => [
                    'stage_1',
                    'stage_2',
                    'stage_3',
                    'stage_4'
                ]
            ];

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'STAGES_2_ELEMENTS', $macros['CATALOGS_SERVICES_STAGES_2_IBLOCK_ID'], $arMap);
        }

        /** Привязка сертификатов */
        if (!empty($macros['CONTENT_CERTIFICATES_IBLOCK_ID'])) {
            $arMap = [
                'car_repair' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ],
                'locksmith_repair' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ],
                'installation_of_interior_doors' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ],
                'windows_installation' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ],
                'car_wash_service' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ],
                'realtor_services' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ],
                'sale_and_rent' => [
                    'certificate_1',
                    'certificate_2',
                    'certificate_3',
                    'certificate_4',
                    'certificate_5'
                ]
            ];

            $linkPropertyElements($macros['CATALOGS_SERVICES_IBLOCK_ID'], 'CERTIFICATES_1_ELEMENTS', $macros['CONTENT_CERTIFICATES_IBLOCK_ID'], $arMap);
        }
    }

    /** Привязка товаров */
    if (!empty($macros['CATALOGS_PRODUCTS_IBLOCK_ID'])) {
        $arProducts = [];
        $rsProducts = CIBlockElement::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $macros['CATALOGS_PRODUCTS_IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]);

        while ($arProduct = $rsProducts->Fetch())
            $arProducts[] = $arProduct;

        $iProductsCount = count($arProducts);

        if (!empty($arProducts)) {
            /** Привязка брендов */
            if (!empty($macros['CONTENT_BRANDS_IBLOCK_ID'])) {
                $arMap = [];
                $arBrands = [];
                $arBrandsList = [];
                $rsBrands = CIBlockElement::GetList(['SORT' => 'ASC'], [
                    'IBLOCK_ID' => $macros['CONTENT_BRANDS_IBLOCK_ID']
                ]);

                while ($arBrand = $rsBrands->Fetch())
                    $arBrands[] = $arBrand;

                if (!empty($arBrands))
                    foreach ($arBrands as $arBrand) {
                        if (empty($arBrand['CODE']))
                            continue;

                        $arBrandsList[] = $arBrand['CODE'];
                    }

                $iBrandsCount = count($arBrandsList);

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arMap[$arProduct['CODE']] = $arBrandsList[mt_rand(0, $iBrandsCount - 1)];
                }

                $linkPropertyElements($macros['CATALOGS_PRODUCTS_IBLOCK_ID'], 'BRAND', $macros['CONTENT_BRANDS_IBLOCK_ID'], $arMap);

                unset($arProduct, $rsBrands, $arBrands, $arBrand, $iBrandsCount, $arBrandsList, $arMap);
            }

            /** Привязка сопутствующих услуг */

            if (!empty($arServices)) {
                $arMap = [];
                $arServicesChild = [];
                $iCount = 0;

                foreach ($arServices as $arService) {
                    if (empty($arService['CODE']))
                        continue;

                    $arServicesChild[] = $arService['CODE'];
                    $iCount++;

                    if ($iCount >= 4)
                        break;
                }

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arMap[$arProduct['CODE']] = $arServicesChild;
                }

                $linkPropertyElements($macros['CATALOGS_PRODUCTS_IBLOCK_ID'], 'SERVICES', $macros['CATALOGS_SERVICES_IBLOCK_ID'], $arMap);

                unset($arService, $iCount, $arProduct, $arServicesChild, $arMap);
            }

            /** Привязка сопутствующих товаров */

            if (!empty($arProducts)) {
                $arMap = [];
                $arProductsChild = [];
                $iCount = 0;

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arProductsChild[] = $arProduct['CODE'];
                    $iCount++;

                    if ($iCount >= 4)
                        break;
                }

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arMap[$arProduct['CODE']] = $arProductsChild;
                }

                $linkPropertyElements($macros['CATALOGS_PRODUCTS_IBLOCK_ID'], 'ASSOCIATED', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], $arMap);

                unset($arProduct, $iCount, $arProductsChild, $arMap);
            }

            /** Привязка рекомендуемых товаров */

            if (!empty($arProducts)) {
                $arMap = [];
                $arProductsChild = [];
                $iCount = 0;

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    if ($iCount < 4) {
                        $iCount++;
                        continue;
                    }

                    $arProductsChild[] = $arProduct['CODE'];
                    $iCount++;

                    if ($iCount >= 8)
                        break;
                }

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arMap[$arProduct['CODE']] = $arProductsChild;
                }

                $linkPropertyElements($macros['CATALOGS_PRODUCTS_IBLOCK_ID'], 'RECOMMENDED', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], $arMap);

                unset($arProduct, $iCount, $arProductsChild, $arMap);
            }

            /** Привязка видеогалереи */
            if (!empty($macros['CONTENT_VIDEO_IBLOCK_ID'])) {
                $arMap = [];
                $arVideos = [];
                $rsVideos = CIBlockElement::GetList(['SORT' => 'ASC'], [
                    'IBLOCK_ID' => $macros['CONTENT_VIDEO_IBLOCK_ID'],
                    'ACTIVE' => 'Y'
                ]);

                while ($arVideo = $rsVideos->Fetch())
                    $arVideos[] = $arVideo;

                if (!empty($arVideos))
                    foreach ($arProducts as $arProduct) {
                        $iCount = 0;

                        if (empty($arProduct['CODE']))
                            continue;

                        $arMap[$arProduct['CODE']] = [];

                        foreach ($arVideos as $arVideo) {
                            if (empty($arVideo['CODE']))
                                continue;

                            $arMap[$arProduct['CODE']][] = $arVideo['CODE'];
                            $iCount++;

                            if ($iCount >= 4)
                                break;
                        }
                    }

                $linkPropertyElements($macros['CATALOGS_PRODUCTS_IBLOCK_ID'], 'VIDEO', $macros['CONTENT_VIDEO_IBLOCK_ID'], $arMap);

                unset($arProduct, $rsVideos, $arVideos, $arVideo, $arMap);
            }
        }

        /** Привязка отзывов товаров */
        if (!empty($macros['CATALOGS_PRODUCTS_REVIEWS_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CATALOGS_PRODUCTS_REVIEWS_IBLOCK_ID'], 'ELEMENT_ID', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], []);

            $arProductsReviews = [];
            $rsProductsReviews = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CATALOGS_PRODUCTS_REVIEWS_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]);

            while ($arProductReview = $rsProductsReviews->Fetch()) {
                $arProduct = $arProducts[mt_rand(0, $iProductsCount)];

                CIBlockElement::SetPropertyValuesEx($arProductReview['ID'], $arProductReview['IBLOCK_ID'], [
                    'ELEMENT_ID' => [$arProduct['ID']]
                ]);
            }
        }

        unset($arProducts, $arProduct, $arProductsReviews, $arProductReview, $iProductsCount);
    }

    /** Привязка акций */
    if (!empty($macros['CONTENT_SHARES_IBLOCK_ID'])) {
        $arShares = \intec\core\collections\Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $macros['CATALOGS_SHARES_IBLOCK_ID']
        ]));

        /** Привязка промо */
        if (!empty($macros['CONTENT_SHARES_PROMO_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CONTENT_SHARES_IBLOCK_ID'], 'PROMO_ELEMENTS', $macros['CONTENT_SHARES_PROMO_IBLOCK_ID'], [
                'share_1' => [
                    'warranty',
                    'delivery'
                ],
                'share_2' => [
                    'warranty',
                    'delivery'
                ],
                'share_3' => [
                    'warranty',
                    'delivery'
                ],
                'share_4' => [
                    'warranty',
                    'delivery'
                ],
                'share_5' => [
                    'warranty',
                    'delivery'
                ],
                'share_6' => [
                    'warranty',
                    'delivery'
                ]
            ]);
        }

        /** Привязка условий */
        if (!empty($macros['CONTENT_SHARES_CONDITIONS_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CONTENT_SHARES_IBLOCK_ID'], 'CONDITIONS_ELEMENTS', $macros['CONTENT_SHARES_CONDITIONS_IBLOCK_ID'], [
                'share_1' => [
                    'condition_1',
                    'condition_2',
                    'condition_3'
                ],
                'share_2' => [
                    'condition_1',
                    'condition_2',
                    'condition_3'
                ],
                'share_3' => [
                    'condition_1',
                    'condition_2',
                    'condition_3'
                ],
                'share_4' => [
                    'condition_1',
                    'condition_2',
                    'condition_3'
                ],
                'share_5' => [
                    'condition_1',
                    'condition_2',
                    'condition_3'
                ],
                'share_6' => [
                    'condition_1',
                    'condition_2',
                    'condition_3'
                ]
            ]);
        }

        /** Привязка видео */
        if (!empty($macros['CONTENT_VIDEO_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CONTENT_SHARES_IBLOCK_ID'], 'VIDEOS_ELEMENTS', $macros['CONTENT_VIDEO_IBLOCK_ID'], [
                'share_1' => [
                    'video_1',
                    'video_2',
                    'video_3'
                ],
                'share_2' => [
                    'video_1',
                    'video_2',
                    'video_3'
                ],
                'share_3' => [
                    'video_1',
                    'video_2',
                    'video_3'
                ],
                'share_4' => [
                    'video_1',
                    'video_2',
                    'video_3'
                ],
                'share_5' => [
                    'video_1',
                    'video_2',
                    'video_3'
                ],
                'share_6' => [
                    'video_1',
                    'video_2',
                    'video_3'
                ]
            ]);
        }

        /** Привязка фото */
        if (!empty($macros['CONTENT_PHOTO_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CONTENT_SHARES_IBLOCK_ID'], 'PHOTO_ELEMENTS', $macros['CONTENT_PHOTO_IBLOCK_ID'], [
                'share_1' => [
                    'photo_1',
                    'photo_2',
                    'photo_3',
                    'photo_4',
                    'photo_5',
                    'photo_6',
                    'photo_7',
                    'photo_8'
                ],
                'share_2' => [
                    'photo_1',
                    'photo_2',
                    'photo_3',
                    'photo_4',
                    'photo_5',
                    'photo_6',
                    'photo_7',
                    'photo_8'
                ],
                'share_3' => [
                    'photo_1',
                    'photo_2',
                    'photo_3',
                    'photo_4',
                    'photo_5',
                    'photo_6',
                    'photo_7',
                    'photo_8'
                ],
                'share_4' => [
                    'photo_1',
                    'photo_2',
                    'photo_3',
                    'photo_4',
                    'photo_5',
                    'photo_6',
                    'photo_7',
                    'photo_8'
                ],
                'share_5' => [
                    'photo_1',
                    'photo_2',
                    'photo_3',
                    'photo_4',
                    'photo_5',
                    'photo_6',
                    'photo_7',
                    'photo_8'
                ],
                'share_6' => [
                    'photo_1',
                    'photo_2',
                    'photo_3',
                    'photo_4',
                    'photo_5',
                    'photo_6',
                    'photo_7',
                    'photo_8'
                ]
            ]);
        }

        if (!empty($macros['CATALOGS_PRODUCTS_IBLOCK_ID'])) {
            /** Привязка разделов каталога */
            $linkPropertySections($macros['CONTENT_SHARES_IBLOCK_ID'], 'CATALOG_SECTIONS', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], [
                'share_1' => [
                    'automotive',
                    'appliances',
                    'cosmetics',
                    'furniture',
                    'clothing'
                ],
                'share_2' => [
                    'automotive',
                    'appliances',
                    'cosmetics',
                    'furniture',
                    'clothing'
                ],
                'share_3' => [
                    'automotive',
                    'appliances',
                    'cosmetics',
                    'furniture',
                    'clothing'
                ],
                'share_4' => [
                    'automotive',
                    'appliances',
                    'cosmetics',
                    'furniture',
                    'clothing'
                ],
                'share_5' => [
                    'automotive',
                    'appliances',
                    'cosmetics',
                    'furniture',
                    'clothing'
                ],
                'share_6' => [
                    'automotive',
                    'appliances',
                    'cosmetics',
                    'furniture',
                    'clothing'
                ]
            ]);

            /** Привязка товаров каталога */
            $linkPropertyElements($macros['CONTENT_SHARES_IBLOCK_ID'], 'CATALOG_ELEMENTS', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], [
                'share_1' => [
                    'gps_navigator_digma_alldrive_500',
                    'avtomobilnye_kolonki_6_x9_pioneer_ts_r6951s',
                    'dukhovoy_shkaf_bosch_hbg43t320',
                    'dobrasil_krem_dlya_tela_maracuja_da_bahia',
                    'maslo_dragotsennoe_argany_dlya_litsa_100_ml',
                    'pidzhak_united_colors_of_benetton',
                    'pandora_lace_dress',
                    'dushevaya_kabina_domani_spa_light_88_high_vysokiy_poddon'
                ],
                'share_2' => [
                    'gps_navigator_digma_alldrive_500',
                    'avtomobilnye_kolonki_6_x9_pioneer_ts_r6951s',
                    'dukhovoy_shkaf_bosch_hbg43t320',
                    'dobrasil_krem_dlya_tela_maracuja_da_bahia',
                    'maslo_dragotsennoe_argany_dlya_litsa_100_ml',
                    'pidzhak_united_colors_of_benetton',
                    'pandora_lace_dress',
                    'dushevaya_kabina_domani_spa_light_88_high_vysokiy_poddon'
                ],
                'share_3' => [
                    'gps_navigator_digma_alldrive_500',
                    'avtomobilnye_kolonki_6_x9_pioneer_ts_r6951s',
                    'dukhovoy_shkaf_bosch_hbg43t320',
                    'dobrasil_krem_dlya_tela_maracuja_da_bahia',
                    'maslo_dragotsennoe_argany_dlya_litsa_100_ml',
                    'pidzhak_united_colors_of_benetton',
                    'pandora_lace_dress',
                    'dushevaya_kabina_domani_spa_light_88_high_vysokiy_poddon'
                ],
                'share_4' => [
                    'gps_navigator_digma_alldrive_500',
                    'avtomobilnye_kolonki_6_x9_pioneer_ts_r6951s',
                    'dukhovoy_shkaf_bosch_hbg43t320',
                    'dobrasil_krem_dlya_tela_maracuja_da_bahia',
                    'maslo_dragotsennoe_argany_dlya_litsa_100_ml',
                    'pidzhak_united_colors_of_benetton',
                    'pandora_lace_dress',
                    'dushevaya_kabina_domani_spa_light_88_high_vysokiy_poddon'
                ],
                'share_5' => [
                    'gps_navigator_digma_alldrive_500',
                    'avtomobilnye_kolonki_6_x9_pioneer_ts_r6951s',
                    'dukhovoy_shkaf_bosch_hbg43t320',
                    'dobrasil_krem_dlya_tela_maracuja_da_bahia',
                    'maslo_dragotsennoe_argany_dlya_litsa_100_ml',
                    'pidzhak_united_colors_of_benetton',
                    'pandora_lace_dress',
                    'dushevaya_kabina_domani_spa_light_88_high_vysokiy_poddon'
                ],
                'share_6' => [
                    'gps_navigator_digma_alldrive_500',
                    'avtomobilnye_kolonki_6_x9_pioneer_ts_r6951s',
                    'dukhovoy_shkaf_bosch_hbg43t320',
                    'dobrasil_krem_dlya_tela_maracuja_da_bahia',
                    'maslo_dragotsennoe_argany_dlya_litsa_100_ml',
                    'pidzhak_united_colors_of_benetton',
                    'pandora_lace_dress',
                    'dushevaya_kabina_domani_spa_light_88_high_vysokiy_poddon'
                ]
            ]);
        }

        /** Привязка услуг */
        if (!empty($macros['CATALOGS_SERVICES_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CONTENT_SHARES_IBLOCK_ID'], 'SERVICES_ELEMENTS', $macros['CATALOGS_SERVICES_IBLOCK_ID'], [
                'share_1' => [
                    'car_repair',
                    'car_wash_service',
                    'realtor_services',
                    'sale_and_rent'
                ],
                'share_2' => [
                    'car_repair',
                    'car_wash_service',
                    'realtor_services',
                    'sale_and_rent'
                ],
                'share_3' => [
                    'car_repair',
                    'car_wash_service',
                    'realtor_services',
                    'sale_and_rent'
                ],
                'share_4' => [
                    'car_repair',
                    'car_wash_service',
                    'realtor_services',
                    'sale_and_rent'
                ],
                'share_5' => [
                    'car_repair',
                    'car_wash_service',
                    'realtor_services',
                    'sale_and_rent'
                ],
                'share_6' => [
                    'car_repair',
                    'car_wash_service',
                    'realtor_services',
                    'sale_and_rent'
                ]
            ]);
        }
    }

    /** Привязка новостей */
    if (!empty($macros['CONTENT_NEWS_IBLOCK_ID'])) {
        $linkPropertyElements($macros['CONTENT_NEWS_IBLOCK_ID'], 'ASSOCIATED', $macros['CONTENT_NEWS_IBLOCK_ID'], [
            'news_1' => [
                'news_2',
                'news_3',
                'news_4',
                'news_5'
            ],
            'news_2' => [
                'news_3',
                'news_4',
                'news_5',
                'news_6'
            ],
            'news_3' => [
                'news_4',
                'news_5',
                'news_6',
                'news_7'
            ],
            'news_4' => [
                'news_5',
                'news_6',
                'news_7',
                'news_8'
            ],
            'news_5' => [
                'news_6',
                'news_7',
                'news_8',
                'news_1'
            ],
            'news_6' => [
                'news_7',
                'news_8',
                'news_1',
                'news_2'
            ],
            'news_7' => [
                'news_8',
                'news_1',
                'news_2',
                'news_3'
            ],
            'news_8' => [
                'news_1',
                'news_2',
                'news_3',
                'news_4'
            ]
        ]);
    }

    /** Привязка статей */
    if (!empty($macros['CONTENT_ARTICLES_IBLOCK_ID'])) {
        $linkPropertyElements($macros['CONTENT_ARTICLES_IBLOCK_ID'], 'ASSOCIATED', $macros['CONTENT_ARTICLES_IBLOCK_ID'], [
            'article_1' => [
                'article_2',
                'article_3',
                'article_4',
                'article_5'
            ],
            'article_2' => [
                'article_3',
                'article_4',
                'article_5',
                'article_6'
            ],
            'article_3' => [
                'article_4',
                'article_5',
                'article_6',
                'article_7'
            ],
            'article_4' => [
                'article_5',
                'article_6',
                'article_7',
                'article_1'
            ],
            'article_5' => [
                'article_6',
                'article_7',
                'article_1',
                'article_2'
            ],
            'article_6' => [
                'article_7',
                'article_1',
                'article_2',
                'article_3'
            ],
            'article_7' => [
                'article_1',
                'article_2',
                'article_3',
                'article_4'
            ]
        ]);
    }

    /** Привязка блога */
    if (!empty($macros['CONTENT_BLOG_IBLOCK_ID'])) {
        $linkPropertyElements($macros['CONTENT_BLOG_IBLOCK_ID'], 'ASSOCIATED', $macros['CONTENT_BLOG_IBLOCK_ID'], [
            'article_1' => [
                'article_2',
                'article_3',
                'article_4',
                'article_5'
            ],
            'article_2' => [
                'article_3',
                'article_4',
                'article_5',
                'article_6'
            ],
            'article_3' => [
                'article_4',
                'article_5',
                'article_6',
                'article_7'
            ],
            'article_4' => [
                'article_5',
                'article_6',
                'article_7',
                'article_8'
            ],
            'article_5' => [
                'article_6',
                'article_7',
                'article_8',
                'article_1'
            ],
            'article_6' => [
                'article_7',
                'article_8',
                'article_1',
                'article_2'
            ],
            'article_7' => [
                'article_8',
                'article_1',
                'article_2',
                'article_3'
            ],
            'article_8' => [
                'article_1',
                'article_2',
                'article_3',
                'article_4'
            ]
        ]);
    }

    /** Привязка отзывов */
    if (!empty($macros['CONTENT_REVIEWS_IBLOCK_ID'])) {

        /** Привязка видео */
        $linkPropertyElements($macros['CONTENT_REVIEWS_IBLOCK_ID'], 'VIDEO', $macros['CONTENT_VIDEO_IBLOCK_ID'], [
            'review_1' => 'video_1',
            'review_2' => 'video_1',
            'review_3' => 'video_1',
            'review_4' => 'video_1',
            'review_5' => 'video_1',
            'review_6' => 'video_1',
        ]);

        /** Привязка сотрудника */
        $linkPropertyElements($macros['CONTENT_REVIEWS_IBLOCK_ID'], 'STAFF', $macros['CONTENT_STAFF_IBLOCK_ID'], [
            'review_1' => 'staff_7',
        ]);
    }

    /** Привязка коллекций */
    if (!empty($macros['CONTENT_COLLECTIONS_IBLOCK_ID'])) {
        $arCollections = [];
        $rsCollections = CIBlockElement::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $macros['CONTENT_COLLECTIONS_IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]);

        while ($arCollection = $rsCollections->Fetch())
            $arCollections[] = $arCollection;

        /** Привязка сопутствующих товаров */
        if (!empty($macros['CATALOGS_PRODUCTS_IBLOCK_ID'])) {
            $arMap = [];
            $arProducts = [];
            $rsProducts = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CATALOGS_PRODUCTS_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]);

            while ($arProduct = $rsProducts->Fetch())
                $arProducts[] = $arProduct;

            if (!empty($arProducts)) {
                $arProductsChild = [];
                $iCount = 0;

                foreach ($arProducts as $arProduct) {
                    if (empty($arProduct['CODE']))
                        continue;

                    $arProductsChild[] = $arProduct['CODE'];
                    $iCount++;

                    if ($iCount >= 4)
                        break;
                }

                foreach ($arCollections as $arCollection) {
                    if (empty($arCollection['CODE']))
                        continue;

                    $arMap[$arCollection['CODE']] = $arProductsChild;
                }
            }

            $linkPropertyElements($macros['CONTENT_COLLECTIONS_IBLOCK_ID'], 'PRODUCTS', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], $arMap);

            unset($arCollection, $iCount, $arProduct, $arProducts, $arProductsChild, $arMap);
        }

        /** Привязка сопутствующих акций */
        if (!empty($macros['CONTENT_SHARES_IBLOCK_ID'])) {
            $arMap = [];
            $arShares = [];
            $rsShares = CIBlockElement::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $macros['CONTENT_SHARES_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]);

            while ($arShare = $rsShares->Fetch())
                $arShares[] = $arShare;

            if (!empty($arShares)) {
                $arSharesChild = [];
                $iCount = 0;

                foreach ($arShares as $arShare) {
                    if (empty($arShare['CODE']))
                        continue;

                    $arSharesChild[] = $arShare['CODE'];
                    $iCount++;

                    if ($iCount >= 4)
                        break;
                }

                foreach ($arCollections as $arCollection) {
                    if (empty($arCollection['CODE']))
                        continue;

                    $arMap[$arCollection['CODE']] = $arSharesChild;
                }
            }

            $linkPropertyElements($macros['CONTENT_COLLECTIONS_IBLOCK_ID'], 'SHARES', $macros['CONTENT_SHARES_IBLOCK_ID'], $arMap);

            unset($arCollection, $iCount, $arShare, $arShares, $arSharesChild, $arMap);
        }
    }

    /** Привязка готовых образов */
    if (!empty($macros['CONTENT_IMAGERY_IBLOCK_ID'])) {

        /** Привязка товаров */
        if (!empty($macros['CATALOGS_PRODUCTS_IBLOCK_ID'])) {

            $arMap = [
                'element_1' => [
                    'imagery_1',
                    'imagery_2',
                    'imagery_3',
                    'imagery_4'
                ],
                'element_2' => [
                    'project_1',
                    'project_2',
                    'project_3',
                    'project_4',
                    'project_5',
                    'project_6'
                ]
            ];

            $linkPropertyElements($macros['CONTENT_IMAGERY_IBLOCK_ID'], 'ELEMENTS', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], $arMap);

            unset($arImagery, $iCount, $arProduct, $arProducts, $arProductsChild, $arMap);
        }

    }

    /** Привязка о компании */
    if (!empty($macros['CONTENT_ABOUT_IBLOCK_ID'])) {
        $linkPropertyElements($macros['CONTENT_ABOUT_IBLOCK_ID'], 'ADVANTAGES', $macros['CONTENT_ADVANTAGES_3_IBLOCK_ID'], [
            'about_2' => [
                'advantage_1',
                'advantage_2',
                'advantage_3',
                'advantage_4'
            ]
        ]);
    }

    /** Привязка баннеров */
    if (!empty($macros['CONTENT_BANNERS_IBLOCK_ID'])) {

        /** Привязка товара */
        if (!empty($macros['CATALOGS_PRODUCTS_IBLOCK_ID'])) {
            $linkPropertyElements($macros['CONTENT_BANNERS_IBLOCK_ID'], 'PRODUCT', $macros['CATALOGS_PRODUCTS_IBLOCK_ID'], [
                'banner_2' => [
                    'qt107000U'
                ]
            ]);
        }
    }

}

/** CUSTOM END */

?>
<? include('.end.php') ?>