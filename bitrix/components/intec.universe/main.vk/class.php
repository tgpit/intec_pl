<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\base\InvalidParamException;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\core\net\http\Request;

if (!Loader::includeModule('iblock')) {
    ShowError(Loc::getMessage('IC_VK_ERROR_MODULE_IBLOCK'));
    return;
}

if (!Loader::includeModule('intec.core')) {
    ShowError(Loc::getMessage('IC_VK_ERROR_MODULE_INTEC_CORE'));
    return;
}

/**
 * Компонент списка записей из страницы ВКонтакте
 * @version 1.0.0
 */
class IntecVKComponent extends CBitrixComponent {
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams) {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'ACCESS_TOKEN' => null,
            'USER_ID' => null,
            'DOMAIN' => null,
            'ITEMS_OFFSET' => null,
            'ITEMS_COUNT' => null,
            'FILTER' => null,
            'DATE_FORMAT' => 'd.m.Y',
            'CACHE_TYPE' => null,
            'CACHE_TIME' => null
        ], $arParams);

        $arParams['FILTER'] = ArrayHelper::fromRange(['all', 'owner', 'others', 'suggests', 'postponed'], $arParams['FILTER']);

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent() {
        if (empty($this->arParams['ACCESS_TOKEN'])) {
            echo $this->generateErrorMessage(Loc::getMessage('IC_VK_ERROR_NOT_ACCESS_TOKEN'));
            return null;
        }

        $this->arResult = [
            'ITEMS' => []
        ];

        if ($this->startResultCache(false, $this->generateCacheId())) {
            $url = new Url();

            $query = $url->setScheme('https')
                ->setHost('api.vk.com')
                ->setPathString('/method/wall.get')
                ->getQuery()
                ->set('v', '5.131')
                ->set('access_token', $this->arParams['ACCESS_TOKEN'])
                ->set('filter', $this->arParams['FILTER']);

            if (!empty($this->arParams['USER_ID']))
                $query->set('owner_id', $this->arParams['USER_ID']);

            if (!empty($this->arParams['DOMAIN']))
                $query->set('domain', $this->arParams['DOMAIN']);

            if (!empty($this->arParams['ITEMS_OFFSET']))
                $query->set('offset', $this->arParams['ITEMS_OFFSET']);

            if (!empty($this->arParams['ITEMS_COUNT']))
                $query->set('count', $this->arParams['ITEMS_COUNT']);

            $request = new Request();
            $request->setJumps(100);
            $response = $request->send($url->build());
            $json = $response->getContent();

            try {
                $data = Json::decode($json);
            } catch (InvalidParamException $exception) {
                $data = null;
            }

            if (empty($data)) {
                echo $this->generateErrorMessage(Loc::getMessage('IC_VK_ERROR_REQUEST_EMPTY'));
                return null;
            }

            $errorCode = ArrayHelper::getValue($data, ['error', 'error_code']);
            $errorMessage = ArrayHelper::getValue($data, ['error', 'error_msg']);

            if (!empty($errorCode) && !empty($errorMessage)) {
                echo $this->generateErrorMessage(Loc::getMessage('IC_VK_ERROR_REQUEST') . '. ' . $errorCode . ': ' . $errorMessage);
                return null;
            }

            $respList = ArrayHelper::getValue($data, ['response', 'items']);

            if (!Type::isArray($respList))
                $respList = [];

            foreach($respList as $respItem) {
                $item = [
                    'ID' => ArrayHelper::getValue($respItem, 'id'),
                    'URL' => null,
                    'DATE' => ArrayHelper::getValue($respItem, 'date'),
                    'TEXT' => ArrayHelper::getValue($respItem, 'text'),
                    'PICTURES' => []
                ];

                $respAttachments = ArrayHelper::getValue($respItem, 'attachments');
                $ownerId = ArrayHelper::getValue($respItem, 'owner_id');

                if (empty($ownerId))
                    $ownerId = ArrayHelper::getValue($respItem, 'to_id');

                if (!empty($ownerId))
                    $item['URL'] = 'https://vk.com/wall' . $ownerId . '_' . $item['ID'];

                if (!Type::isArray($respAttachments))
                    $respAttachments = [];

                $copyHistory = ArrayHelper::getValue($respItem, 'copy_history');

                if (!Type::isArray($copyHistory))
                    $copyHistory = [];

                foreach($copyHistory as $copyHistoryItem) {
                    $copyHistoryAttachments = ArrayHelper::getValue($copyHistoryItem, 'attachments');

                    if (!Type::isArray($copyHistoryAttachments))
                        $copyHistoryAttachments = [];

                    if (!empty($copyHistoryAttachments))
                        $respAttachments = ArrayHelper::merge($respAttachments, $copyHistoryAttachments);

                    $copyHistoryText = ArrayHelper::getValue($copyHistoryItem, 'text');

                    if (!empty($copyHistoryText)) {
                        if (!empty($item['TEXT'])) {
                            $item['TEXT'] = $item['TEXT'] . ' ' . $copyHistoryText;
                        } else {
                            $item['TEXT'] = $copyHistoryText;
                        }
                    }
                }

                foreach ($respAttachments as $respAttachment) {
                    $type = ArrayHelper::getValue($respAttachment, 'type');

                    if ($type == 'photo') {
                        $respSizes = ArrayHelper::getValue($respAttachment, [$type, 'sizes']);
                    } elseif ($type == 'link') {
                        $respSizes = ArrayHelper::getValue($respAttachment, [$type, 'photo', 'sizes']);
                    } else {
                        $respSizes = null;
                    }

                    if (!Type::isArray($respSizes))
                        $respSizes = [];

                    if (!empty($respSizes)) {
                        $original = null;
                        $thumb = null;
                        $isThumb = false;

                        foreach (['w', 'z', 'y', 'x', 'r', 'q', 'p', 'o', 'm', 's'] as $sizeType) {
                            if ($sizeType == 'r')
                                $isThumb = true;

                            foreach ($respSizes as $respSize) {
                                $respSizeType = ArrayHelper::getValue($respSize, 'type');

                                if ($respSizeType == $sizeType) {
                                    if ($original === null)
                                        $original = ArrayHelper::getValue($respSize, 'url');

                                    if ($isThumb && $thumb === null)
                                        $thumb = ArrayHelper::getValue($respSize, 'url');

                                    break;
                                }
                            }
                        }

                        if ($original && !$thumb)
                            $thumb = $original;

                        $item['PICTURES'][] = [
                            'ORIGINAL' => $original,
                            'THUMB' => $thumb
                        ];
                    }
                }

                $this->arResult['ITEMS'][] = $item;
            }

            $this->arResult['ITEMS'] = ArrayHelper::convertEncoding(
                $this->arResult['ITEMS'],
                Encoding::getDefault(),
                Encoding::UTF8
            );

            foreach($this->arResult['ITEMS'] as &$item) {
                if (!empty($item['DATE']))
                    $item['DATE'] = CIBlockFormatProperties::DateFormat($this->arParams['DATE_FORMAT'], $item['DATE']);
            }

            $this->includeComponentTemplate();
        }

        return null;
    }

    /**
     * Возвращает уникальный массив-идентификатор для кеширования
     * @return array
     */
    public function generateCacheId() {
        global $USER;

        $result = [
            $USER->GetGroups()
        ];

        return $result;
    }

    /**
     * Генератор сообщений об ошибках
     * @param string $message
     * @return string
     */
    public function generateErrorMessage($message) {
        return StringHelper::replaceMacros(Loc::getMessage('IC_VK_ERROR_TEMPLATE'), [
            'COMPONENT' => $this->getName(),
            'TEMPLATE' => $this->getTemplateName(),
            'MESSAGE' => $message
        ]);
    }
}