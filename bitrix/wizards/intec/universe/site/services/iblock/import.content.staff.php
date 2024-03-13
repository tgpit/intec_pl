<? include(__DIR__.'/.begin.php') ?>
<?

use intec\core\base\Collection;

/**
 * @var Collection $data
 * @var array $languages
 * @var string $solution
 * @var CWizardBase $wizard
 * @var Closure($code, $type, $file, $fields = []) $import
 * @var CWizardStep $this
 */

$code = $solution.'_staff_'.WIZARD_SITE_ID;
$type = 'content';
$iBlock = $import($code, $type, 'content.staff');

if (!empty($iBlock)) {
    $macros = $data->get('macros');
    $macros['CONTENT_STAFF_IBLOCK_TYPE'] = $type;
    $macros['CONTENT_STAFF_IBLOCK_ID'] = $iBlock['ID'];
    $macros['CONTENT_STAFF_IBLOCK_CODE'] = $iBlock['CODE'];

    $element = CIBlockElement::GetList([], [
        'IBLOCK_ID' => $iBlock['ID'],
        'CODE' => 'staff_1'
    ])->Fetch();

    $macros['CONTENT_STAFF_ELEMENT_ID'] = $element['ID'];
    unset($element);

    $data->set('macros', $macros);
}

?>
<? include(__DIR__.'/.end.php') ?>