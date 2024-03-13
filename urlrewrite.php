<?php
$arUrlRewrite=array (
  8 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => NULL,
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  20 => 
  array (
    'CONDITION' => '#^/un/company/certificates/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/company/certificates/index.php',
    'SORT' => 100,
  ),
  67 => 
  array (
    'CONDITION' => '#^/info/tablica_razmerov/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/info/tablica_razmerov/index.php',
    'SORT' => 100,
  ),
  36 => 
  array (
    'CONDITION' => '#^/company/certificates/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/company/certificates/index.php',
    'SORT' => 100,
  ),
  17 => 
  array (
    'CONDITION' => '#^/un/company/articles/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/company/articles/index.php',
    'SORT' => 100,
  ),
  22 => 
  array (
    'CONDITION' => '#^/un/personal/profile/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/un/personal/profile/index.php',
    'SORT' => 100,
  ),
  27 => 
  array (
    'CONDITION' => '#^/un/contacts/stores/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/contacts/stores/index.php',
    'SORT' => 100,
  ),
  9 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  19 => 
  array (
    'CONDITION' => '#^/un/company/staff/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/company/staff/index.php',
    'SORT' => 100,
  ),
  38 => 
  array (
    'CONDITION' => '#^/personal/profile/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/profile/index.php',
    'SORT' => 100,
  ),
  46 => 
  array (
    'CONDITION' => '#^/acrit.export/(.*)#',
    'RULE' => 'path=$1',
    'ID' => NULL,
    'PATH' => '/acrit.export/index.php',
    'SORT' => 100,
  ),
  59 => 
  array (
    'CONDITION' => '#^/company/articles/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/blog/index.php',
    'SORT' => 100,
  ),
  16 => 
  array (
    'CONDITION' => '#^/un/company/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/company/news/index.php',
    'SORT' => 100,
  ),
  18 => 
  array (
    'CONDITION' => '#^/un/company/jobs/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/company/jobs/index.php',
    'SORT' => 100,
  ),
  43 => 
  array (
    'CONDITION' => '#^/contacts/stores/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/contacts/stores/index.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/personal/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/order/index.php',
    'SORT' => 100,
  ),
  12 => 
  array (
    'CONDITION' => '#^/un/collections/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/collections/index.php',
    'SORT' => 100,
  ),
  25 => 
  array (
    'CONDITION' => '#^/un/help/brands/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/help/brands/index.php',
    'SORT' => 100,
  ),
  26 => 
  array (
    'CONDITION' => '#^/un/help/client/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/help/client/index.php',
    'SORT' => 100,
  ),
  35 => 
  array (
    'CONDITION' => '#^/company/staff/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/company/staff/index.php',
    'SORT' => 100,
  ),
  44 => 
  array (
    'CONDITION' => '#^/company/jobs/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/company/jobs/index.php',
    'SORT' => 100,
  ),
  62 => 
  array (
    'CONDITION' => '#^/company/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/company/news/index.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/un/services/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/un/services/index.php',
    'SORT' => 100,
  ),
  23 => 
  array (
    'CONDITION' => '#^/un/projects/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/projects/index.php',
    'SORT' => 100,
  ),
  28 => 
  array (
    'CONDITION' => '#^/collections/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/collections/index.php',
    'SORT' => 100,
  ),
  41 => 
  array (
    'CONDITION' => '#^/help/brands/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/help/brands/index.php',
    'SORT' => 100,
  ),
  42 => 
  array (
    'CONDITION' => '#^/help/client/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/help/client/index.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/un/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/un/catalog/index.php',
    'SORT' => 100,
  ),
  13 => 
  array (
    'CONDITION' => '#^/un/imagery/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/imagery/index.php',
    'SORT' => 100,
  ),
  15 => 
  array (
    'CONDITION' => '#^/un/shares/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/shares/index.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/personal/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/index.php',
    'SORT' => 100,
  ),
  24 => 
  array (
    'CONDITION' => '#^/un/photo/#',
    'RULE' => '',
    'ID' => 'bitrix:photo',
    'PATH' => '/un/photo/index.php',
    'SORT' => 100,
  ),
  39 => 
  array (
    'CONDITION' => '#^/projects/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/projects/index.php',
    'SORT' => 100,
  ),
  66 => 
  array (
    'CONDITION' => '#^/services/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/services/index.php',
    'SORT' => 100,
  ),
  21 => 
  array (
    'CONDITION' => '#^/un/blog/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/un/blog/index.php',
    'SORT' => 100,
  ),
  29 => 
  array (
    'CONDITION' => '#^/imagery/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/imagery/index.php',
    'SORT' => 100,
  ),
  65 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  31 => 
  array (
    'CONDITION' => '#^/shares/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/shares/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/store/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog.store',
    'PATH' => '/store/index.php',
    'SORT' => 100,
  ),
  40 => 
  array (
    'CONDITION' => '#^/photo/#',
    'RULE' => '',
    'ID' => 'bitrix:photo',
    'PATH' => '/photo/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  61 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
);
