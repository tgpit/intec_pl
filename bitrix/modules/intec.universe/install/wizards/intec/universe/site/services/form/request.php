<? include('.begin.php') ?>
<?

use Bitrix\Main\Localization\Loc;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;

/**
 * @var Collection $data
 * @var CWizardBase $wizard
 * @var Closure($code, $localization, $fields, $statuses) $import, ���
 * $fields ������ �����, ������� ����� ��������� ����:
 * - string name - ��� ���� (������������).
 * - string code - ��� ���� (������������).
 * - string type - ��� ���� (������������).
 * - bool required - �������� ������������ �����.
 * - array values - ��������� �������� ���� (���� ��� checkbox ��� radio).
 * $statuses ������ ��������, ������� ����� ��������� ����:
 * - string name - ��� ������� (������������).
 * - string description - �������� �������.
 * - bool default - �������� �������� �� ���������.
 * @var CWizardStep $this
 */

Loc::loadMessages(__FILE__);

$code = 'REQUEST';
$form = $import($code, [
    'name' => Loc::getMessage('wizard.request.form.name'),
    'button' => Loc::getMessage('wizard.request.form.button'),
    'description' => Loc::getMessage('wizard.request.form.description')
], [[
    'name' => Loc::getMessage('wizard.request.form.fields.PRODUCT.name'),
    'code' => 'PRODUCT',
    'type' => 'text',
    'required' => true
], [
    'name' => Loc::getMessage('wizard.request.form.fields.INITIALS.name'),
    'code' => 'INITIALS',
    'type' => 'text',
    'required' => true
], [
    'name' => Loc::getMessage('wizard.request.form.fields.EMAIL.name'),
    'code' => 'EMAIL',
    'type' => 'email',
    'required' => false
], [
    'name' => Loc::getMessage('wizard.request.form.fields.PHONE.name'),
    'code' => 'PHONE',
    'type' => 'text',
    'required' => true
]], [[
    'name' => Loc::getMessage('wizard.request.form.status'),
    'description' => '',
    'default' => true
]]);

if (!empty($form)) {
    $macros = $data->get('macros');
    $macros['FORMS_'.$code.'_ID'] = $form['ID'];
    $macros['FORMS_'.$code.'_FIELDS_PRODUCT_ID'] = null;

    $answer = ArrayHelper::getValue($form, ['FIELDS', 'PRODUCT', 'ANSWERS', 0]);

    if (!empty($answer))
        $macros['FORMS_'.$code.'_FIELDS_PRODUCT_ID'] = 'form_'.$answer['FIELD_TYPE'].'_'.$answer['ID'];

    $data->set('macros', $macros);
}

?>
<? include('.end.php') ?>