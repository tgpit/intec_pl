<? include(__DIR__.'/.begin.php') ?>
<?

use Bitrix\Main\ModuleManager;
use Bitrix\Iblock\ElementTable;
use intec\core\base\Collection;

/**
 * @var Collection $data
 * @var array $languages
 * @var string $solution
 * @var CWizardBase $wizard
 * @var Closure($code, $type, $file, $fields = []) $import
 * @var CWizardStep $this
 */

$code = $solution.'_panel_'.WIZARD_SITE_ID;
$type = 'content';
$iBlock = $import($code, $type, 'content.panel');

if (!empty($iBlock)) {
    $macros = $data->get('macros');
    $macros['CONTENT_PANEL_IBLOCK_TYPE'] = $type;
    $macros['CONTENT_PANEL_IBLOCK_ID'] = $iBlock['ID'];
    $macros['CONTENT_PANEL_IBLOCK_CODE'] = $iBlock['CODE'];
    $data->set('macros', $macros);

    if (!ModuleManager::isModuleInstalled('sale')) {
        $item = ElementTable::query()
            ->setSelect(['ID', 'CODE', 'IBLOCK_ID'])
            ->where([
                ['IBLOCK_ID', '=', $iBlock['ID']],
                ['CODE', '=', 'favorite']
            ])
            ->setLimit(1)
            ->fetch();

        if (!empty($item)) {
            ElementTable::update($item['ID'], [
                'ACTIVE' => 'N'
            ]);
        }

        unset($item);
    }
}

?>
<? include(__DIR__.'/.end.php') ?>