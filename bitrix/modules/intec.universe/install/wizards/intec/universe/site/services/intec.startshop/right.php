<? include(__DIR__.'/.begin.php') ?>
<?

use intec\core\base\Collection;

/**
 * @var Collection $data
 * @var array $languages
 * @var CWizardBase $wizard
 * @var CWizardStep $this
 */

$arGroup = CGroup::GetList(
    $by = '',
    $order = '',
    ['STRING_ID' => 'site_owners_group']
)->Fetch();

if ($arGroup) {

    $arParameters = [
        'STARTSHOP_SETTINGS_CATALOG' => 'E.V',
        'STARTSHOP_SETTINGS_SITES' => 'E.V',
        'STARTSHOP_SETTINGS_ORDER_PROPERTY' => 'E.V',
        'STARTSHOP_SETTINGS_ORDER_STATUS' => 'E.V',
        'STARTSHOP_SETTINGS_PRICE' => 'E.V',
        'STARTSHOP_SETTINGS_DELIVERY' => 'E.V',
        'STARTSHOP_SETTINGS_CURRENCY' => 'E.V',
        'STARTSHOP_SETTINGS_PAYMENT' => 'E.V',
        'STARTSHOP_SETTINGS_1C' => 'E.V',
        'STARTSHOP_ORDERS' => 'E.V',
        'STARTSHOP_FORMS' => 'E.V'
    ];

    foreach ($arParameters as $keyParam => $valueParam)
        CStartShopUtilsRights::SetRights(
            $arGroup['ID'],
            $keyParam,
            explode('.', $valueParam)
        );
}
?>
<? include(__DIR__.'/.end.php') ?>