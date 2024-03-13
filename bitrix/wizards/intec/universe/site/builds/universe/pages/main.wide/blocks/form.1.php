<?php

return [
    'type' => 'simple',
    'code' => null,
    'component' => [
        'code' => 'intec.universe:main.form',
        'template' => 'template.1',
        'properties' => [
            'ID' => '#FORMS_FEEDBACK_ID#',
            'NAME' => 'Обратная связь',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CONSENT' => '#SITE_DIR#company/consent/',
            'TEMPLATE' => '.default',
            'TITLE' => 'Индивидуальный подход',
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_TEXT' => 'Наши специалисты свяжутся с вами и найдут оптимальные для вас условия сотрудничества',
            'BUTTON_TEXT' => 'Обратная связь',
            'THEME' => 'dark',
            'VIEW' => 'left',
            'BACKGROUND_COLOR' => '#f4f4f4',
            'BACKGROUND_IMAGE_USE' => 'Y',
            'BACKGROUND_IMAGE_PATH' => '#SITE_DIR#images/forms/form.1/background.jpg',
            'BACKGROUND_IMAGE_HORIZONTAL' => 'center',
            'BACKGROUND_IMAGE_VERTICAL' => 'center',
            'BACKGROUND_IMAGE_SIZE' => 'cover',
            'BACKGROUND_IMAGE_PARALLAX_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '3600000'
        ]
    ]
];
