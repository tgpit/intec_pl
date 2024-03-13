<? include(__DIR__.'/.begin.php') ?>
<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\base\Collection;

/**
 * @var Collection $data
 * @var array $languages
 * @var CWizardBase $wizard
 * @var Closure($code, $localization, $fields, $statuses) $import, ���
 * $fields ������ �����, ������� ����� ��������� ����:
 * - string name - ��� ���� (������������).
 * - string code - ��� ���� (������������).
 * - string send - ��������� ����� ��������.
 * - string type - ��� ���� (������������).
 * - string required - �������� ������������ �����.
 * - string readonly - ���� ������ ��� ������.
 * - array values - ��������� �������� ���� (���� ��� checkbox ��� radio).
 * - array localization - �������� ���������.
 * @var CWizardStep $this
 */

Loc::loadMessages(__FILE__);

$code = 'REQUEST';
$fields = [[
    'code' => 'PRODUCT',
    'type' => 0,
    'required' => 'Y',
    'readonly' => 'N'
], [
    'code' => 'INITIALS',
    'type' => 0,
    'required' => 'Y',
    'readonly' => 'N'
], [
    'code' => 'EMAIL',
    'type' => 0,
    'required' => 'N',
    'readonly' => 'N'
], [
    'code' => 'PHONE',
    'type' => 0,
    'required' => 'Y',
    'readonly' => 'N'
]];

include(__DIR__.'/.form.import.php');
/** @var array $form */

if (!Loader::includeModule('form'))
    if (!empty($form)) {
        $macros = $data->get('macros');
        $macros['FORMS_'.$code.'_ID'] = $form['ID'];
        $macros['FORMS_'.$code.'_FIELDS_PRODUCT_ID'] = '';

        $field = CStartShopFormProperty::GetList([], [
            'CODE' => 'PRODUCT',
            'FORM' => $form['ID']
        ])->Fetch();

        if (!empty($field))
            $macros['FORMS_'.$code.'_FIELDS_PRODUCT_ID'] = $field['ID'];

        $data->set('macros', $macros);
    }

?>
<? include(__DIR__.'/.end.php') ?>