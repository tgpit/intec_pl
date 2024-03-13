<?php

use Bitrix\Iblock\Component\Tools as IblockComponentTools;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\io\Path;
use intec\core\net\Url;

class IntecSalePersonalSectionComponent extends CBitrixComponent
{
    /**
     * Страницы, поддерживаемые компонентом.
     * @var array
     */
    protected $_arPages;

    /**
     * Шаблоны ЧПУ.
     * @var array
     */
    protected $arSEFTemplates;
    /**
     * Переменные ЧПУ.
     * @var array
     */
    protected $arSEFVariables;
    /**
     * Алиасы переменных ЧПУ.
     * @var array
     */
    protected $arSEFVariablesAliases;

    /**
     * Возвращает страницы, поддерживаемые компонентом.
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    protected function getPages()
    {
        if ($this->_arPages === null) {
            $this->_arPages = [
                'main' => [
                    'code' => 'main',
                    'name' => Loc::getMessage('C_SALE_PERSONAL_SECTION_PAGES_MAIN'),
                    'path' => null,
                    'linked' => false
                ],
                'private' => [
                    'code' => 'private',
                    'name' => !empty($this->arParams['PRIVATE_PAGE_NAME']) ? $this->arParams['PRIVATE_PAGE_NAME'] : Loc::getMessage('C_SALE_PERSONAL_SECTION_PAGES_PRIVATE'),
                    'path' => 'private/',
                    'linked' => true
                ],
                'order' => [
                    'code' => 'order',
                    'name' => Loc::getMessage('C_SALE_PERSONAL_SECTION_PAGES_ORDER'),
                    'path' => 'orders/#ORDER_ID#/',
                    'variables' => [
                        '#ORDER_ID#'
                    ],
                    'linked' => false
                ],
                'orders' => [
                    'code' => 'orders',
                    'name' => !empty($this->arParams['ORDERS_PAGE_NAME']) ? $this->arParams['ORDERS_PAGE_NAME'] : Loc::getMessage('C_SALE_PERSONAL_SECTION_PAGES_ORDERS'),
                    'path' => 'orders/',
                    'linked' => true
                ]
            ];

            if (!Loader::includeModule('intec.startshop')) {
                unset(
                    $this->_arPages['order'],
                    $this->_arPages['orders']
                );
            }
        }

        return $this->_arPages;
    }

    /**
     * Подключение используемых модулей.
     * @return boolean
     * @throws \Bitrix\Main\LoaderException
     */
    protected function includeModules()
    {
        return Loader::includeModule('iblock') && Loader::includeModule('intec.core');
    }

    /**
     * Инициализация ЧПУ, если включено.
     * @return boolean
     */
    protected function initSEFMode()
    {
        $arParams = &$this->arParams;

        if ($arParams['SEF_MODE'] !== 'Y')
            return false;

        $arPages = $this->getPages();
        $arSEFTemplates = &$this->arSEFTemplates;
        $arSEFVariables = &$this->arSEFVariables;
        $arSEFVariablesAliases = &$this->arSEFVariablesAliases;

        $arSEFTemplates = [];
        $arSEFVariables = [];
        $arSEFVariablesAliases = [];

        foreach ($arPages as $arPage) {
            if (!empty($arPage['variables']))
                foreach ($arPage['variables'] as $sVariable) {
                    if (!ArrayHelper::isIn($sVariable, $arSEFVariables))
                        $arSEFVariables[] = $sVariable;
                }

            $arSEFTemplates[$arPage['code']] = !empty($arPage['path']) ? $arPage['path'] : '';
        }

        $arSEFTemplates = CComponentEngine::makeComponentUrlTemplates(
            $arSEFTemplates,
            $arParams['SEF_URL_TEMPLATES']
        );

        $arSEFVariablesAliases = CComponentEngine::makeComponentVariableAliases(
            $arSEFVariablesAliases,
            $arParams['VARIABLE_ALIASES']
        );

        return true;
    }

    /**
     * Нормализация значений параметров.
     */
    protected function normalizeParams()
    {
        $arParams = &$this->arParams;

        if (empty($arParams['PAGE_VARIABLE']))
            $arParams['PAGE_VARIABLE'] = 'page';

        if (!empty($arParams['FILE_404']))
            $arParams['FILE_404'] = $this->replaceMacros($arParams['FILE_404']);
    }

    /**
     * Заменяет необходимые макросы в строке.
     * @param string $value
     * @return string
     */
    protected function replaceMacros($value)
    {
        return StringHelper::replaceMacros($value, [
            'SITE_DIR' => SITE_DIR
        ]);
    }

    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!$this->includeModules())
            return parent::onPrepareComponentParams($arParams);

        $arParams = ArrayHelper::merge([
            'CHAIN_MAIN_NAME' => null,
            'PAGE_VARIABLE' => 'page',
            'SEF_FOLDER' => null,
            'SEF_MODE' => 'N',
            'SEF_URL_TEMPLATES' => [],
            'TITLE_SET' => 'N',
            'VARIABLE_ALIASES' => [],
            'MESSAGE_404' => null,
            'SET_STATUS_404' => 'N',
            'SHOW_404' => 'N',
            'FILE_404' => '#SITE_DIR#404.php',
        ], $arParams);

        return parent::onPrepareComponentParams($arParams);
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        global $APPLICATION;

        if (!$this->includeModules())
            return;

        $this->normalizeParams();

        $arParams = &$this->arParams;
        $arResult = &$this->arResult;
        $arPages = $this->getPages();
        $sPage = null;

        $arResult['PAGE'] = [];
        $arResult['ROOT'] = [];

        if ($this->initSEFMode()) {
            $arVariables = [];
            $oEngine = new CComponentEngine($this);
            $sRoot = StringHelper::replace($arParams['SEF_FOLDER'], [
                '\\' => '/'
            ]);

            foreach ($this->arSEFVariables as $sVariable)
                $oEngine->addGreedyPart($sVariable);

            $bError = false;
            $sPage = $oEngine->guessComponentPath($arParams['SEF_FOLDER'], $this->arSEFTemplates, $arVariables);

            if (empty($sPage)) {
                $sPage = 'main';
                $bError = true;
            }

            if ($bError) {
                $sFolder = $sRoot;

                if ($sFolder !== '/')
                    $sFolder = '/'.trim($sFolder, '/ \t\n\r\0\x0B').'/';

                if (mb_substr($sFolder, -1) === '/')
                    $sFolder .= 'index.php';

                if ($sFolder !== $APPLICATION->GetCurPage(true)) {
                    IblockComponentTools::process404(
                        $arParams['MESSAGE_404'],
                        $arParams['SET_STATUS_404'] === 'Y',
                        $arParams['SET_STATUS_404'] === 'Y',
                        $arParams['SHOW_404'] === 'Y',
                        $arParams['FILE_404']
                    );
                }
            }

            CComponentEngine::initComponentVariables($sPage, $this->arSEFVariables, $this->arSEFVariablesAliases, $arVariables);

            $arResult['SEF'] = [
                'FOLDER' => $arParams['SEF_FOLDER'],
                'TEMPLATES' => $this->arSEFTemplates,
                'VARIABLES' => $arVariables,
                'ALIASES' => $this->arSEFVariablesAliases
            ];

            $arResult['ROOT']['URL'] = $sRoot;
            $arResult['PAGE']['URL'] = $APPLICATION->GetCurPage(false);
        } else {
            $bError = false;
            $arPagesCodes = [];
            $sPage = Core::$app->request->get($arParams['PAGE_VARIABLE']);

            foreach ($arPages as $arPage)
                $arPagesCodes[] = $arPage['code'];

            if (empty($sPage)) {
                $sPage = reset($arPagesCodes);
            } else if (!ArrayHelper::isIn($sPage, $arPagesCodes)) {
                $sPage = reset($arPagesCodes);
                $bError = true;
            }

            if ($bError)
                IblockComponentTools::process404(
                    $arParams['MESSAGE_404'],
                    $arParams['SET_STATUS_404'] === 'Y',
                    $arParams['SET_STATUS_404'] === 'Y',
                    $arParams['SHOW_404'] === 'Y',
                    $arParams['FILE_404']
                );

            $oUrl = Core::$app->request->getUrl();
            $oUrl = new Url($oUrl);
            $oUrl->getQuery()->removeAll();

            $arResult['ROOT']['URL'] = $oUrl->build();
            $arResult['PAGE']['URL'] = $APPLICATION->GetCurPageParam('', [], false);

            unset($oUrl);
        }

        $arResult['PAGE']['CODE'] = $sPage;
        $arResult['ITEMS'] = [];

        if (!empty($arResult['SEF'])) {
            foreach ($arPages as $arPage) {
                if (!$arPage['linked'])
                    continue;

                $sItemPath = Path::normalize($arResult['ROOT']['URL'].'/'.$arPage['path'], false, '/');

                if ($sItemPath !== '/' && StringHelper::endsWith($arPage['path'], '/'))
                    $sItemPath .= '/';

                $oUrl = new Url();
                $oUrl->setPathString($sItemPath);

                $arResult['ITEMS'][] = [
                    'CODE' => $arPage['code'],
                    'NAME' => $arPage['name'],
                    'LINK' => $oUrl->build()
                ];
            }
        } else {
            foreach ($arPages as $arPage) {
                if (!$arPage['linked'])
                    continue;

                $oUrl = Core::$app->request->getUrl();
                $oUrl = new Url($oUrl);
                $oUrl->getQuery()->removeAll();
                $oUrl->getQuery()->set($arParams['PAGE_VARIABLE'], $arPage['code']);

                $arResult['ITEMS'][] = [
                    'CODE' => $arPage['code'],
                    'NAME' => $arPage['name'],
                    'LINK' => $oUrl->build()
                ];
            }
        }

        foreach ($arPages as $arPage) {
            if ($arPage['code'] === $sPage)
                break;

            unset($arPage);
        }

        if (!empty($arParams['CHAIN_MAIN_NAME']))
            $APPLICATION->AddChainItem(Html::encode($arParams['CHAIN_MAIN_NAME']), $arResult['ROOT']['URL']);

        if (!empty($arPage) && $arPage['code'] !== 'main')
            $APPLICATION->AddChainItem(Html::encode($arPage['name']), $arResult['PAGE']['URL']);

        if ($arParams['TITLE_SET'] === 'Y') {
            if (!empty($arPage) && $arPage['code'] !== 'main') {
                $APPLICATION->SetTitle($arPage['name']);
            } else if (!empty($arParams['CHAIN_MAIN_NAME'])) {
                $APPLICATION->SetTitle($arParams['CHAIN_MAIN_NAME']);
            }
        }

        unset($arParams, $arResult);

        $this->includeComponentTemplate($sPage);
    }
}
