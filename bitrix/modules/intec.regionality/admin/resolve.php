<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\Html;
use intec\regionality\services\locator\Service as Locator;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;
global $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$address = $_SERVER['REMOTE_ADDR'];
$extensions = Locator::getInstance()->getExtensions();
$results = [];

if ($request->getIsPost()) {
    $address = $request->post('address');

    foreach ($extensions as $extension) {
        if (!$extension->getIsAvailable()) {
            $results[] = [
                'extension' => $extension,
                'availability' => false,
                'data' => null
            ];
        } else {
            $results[] = [
                'extension' => $extension,
                'availability' => true,
                'data' => $extension->resolve($address, true)
            ];
        }
    }
}

$APPLICATION->SetTitle(Loc::getMessage('title'));


$oTabControl = new CAdminTabControl('Resolve', [[
    'DIV' => 'common',
    'TAB' => Loc::getMessage('tabs.common'),
    'ICON' => 'settings',
    'TITLE' => Loc::getMessage('tabs.common')
]]);

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<form method="POST">
    <?= bitrix_sessid_post() ?>
    <?php $oTabControl->Begin() ?>
    <?php $oTabControl->BeginNextTab() ?>
        <tr>
            <td width="40%"><?= Loc::getMessage('fields.address') ?>:</td>
            <td>
                <?= Html::textInput('address', $address) ?>
            </td>
        </tr>
        <?php if (!empty($results)) { ?>
            <tr>
                <td colspan="2">
                    <table class="internal" style="margin: 0 auto; width: 100%;">
                        <thead>
                            <tr class="heading">
                                <td style="white-space: nowrap; width: 1px;">
                                    <?= Loc::getMessage('table.columns.service') ?>
                                </td>
                                <td style="width: 1px;">
                                    <?= Loc::getMessage('table.columns.availability') ?>
                                </td>
                                <td>
                                    <?= Loc::getMessage('table.columns.data') ?>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result) { ?>
                                <tr>
                                    <td style="white-space: nowrap; width: 1px;">
                                        <?= $result['extension']->getName() ?>
                                    </td>
                                    <td style="width: 1px;">
                                        <?= $result['availability'] ? Loc::getMessage('answers.yes') : Loc::getMessage('answers.no') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($result['data'])) { ?>
                                            <code>
                                                <pre><?php var_dump($result['data']) ?></pre>
                                            </code>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php } ?>
    <?php $oTabControl->Buttons() ?>
        <input type="submit" class="adm-btn-save" name="Apply" value="<?= Loc::getMessage('buttons.apply') ?>" />
    <?php $oTabControl->End() ?>
</form>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
