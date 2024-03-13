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

$code = 'RATE';
$form = $import($code, [
    'name' => Loc::getMessage('wizard.rate.form.name'),
    'button' => Loc::getMessage('wizard.rate.form.button'),
    'description' => Loc::getMessage('wizard.rate.form.description')
], [[
    'name' => Loc::getMessage('wizard.rate.form.fields.RATE.name'),
    'code' => 'RATE',
    'type' => 'text',
    'required' => true
], [
    'name' => Loc::getMessage('wizard.rate.form.fields.NAME.name'),
    'code' => 'NAME',
    'type' => 'text',
    'required' => true
], [
    'name' => Loc::getMessage('wizard.rate.form.fields.EMAIL.name'),
    'code' => 'EMAIL',
    'type' => 'email',
    'required' => false
], [
    'name' => Loc::getMessage('wizard.rate.form.fields.PHONE.name'),
    'code' => 'PHONE',
    'type' => 'text',
    'required' => true
]], [[
    'name' => Loc::getMessage('wizard.rate.form.status'),
    'description' => '',
    'default' => true
]]);

if (!empty($form)) {
    $macros = $data->get('macros');
    $macros['FORMS_'.$code.'_ID'] = $form['ID'];
    $macros['FORMS_'.$code.'_FIELDS_RATE_ID'] = null;

    $answer = ArrayHelper::getValue($form, ['FIELDS', 'RATE', 'ANSWERS', 0]);

    if (!empty($answer))
        $macros['FORMS_'.$code.'_FIELDS_RATE_ID'] = 'form_'.$answer['FIELD_TYPE'].'_'.$answer['ID'];

    $data->set('macros', $macros);
}

?>
<? include('.end.php') ?>