<?php

/**
 * @var Closure $templateWidgetsLoad
 */
include(__DIR__.'/../.begin.php');

return [
    'code' => 'main',
    'name' => 'Главная',
    'default' => 1,
    'layout' => 'wide',
    'sort' => 400,
    'containers' => [[
        'containers' => [[
            'order' => 0,
            'properties' => [
                'background' => [
                    'color' => '#f8f9fb'
                ]
            ],
            'area' => 'header'
        ]],
        'zone' => 'header'
    ], [
        'containers' => $templateBlocksLoad('pages-main-blocks.', $pageBlocksGet('main.wide', [
            'icons',
            'advantages',
            'sections',
            'products',
            'product-day',
            'services',
            'products-reviews',
            'categories',
            'collections',
            'images',
            'form.1',
            'stories',
            'reviews',
            'shares',
            'articles',
            'about',
            'stages',
            'staff',
            'certificates',
            'projects',
            'video',
            'gallery',
            'rates',
            'faq',
            'videos',
            'brands',
            'vk',
            'instagram',
            'news',
            'contacts'
        ])),
        'zone' => 'default'
    ], [
        'containers' => [[
            'order' => 0,
            'area' => 'footer'
        ]],
        'zone' => 'footer'
    ]]
];