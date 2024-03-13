<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/install/wizard_sol/wizard.php') ?>
<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;

Loc::loadMessages(__FILE__);

class BeginStep extends CWizardStep
{
    public static function GetId() { return 'Begin'; }

    public static function GetDependencies() {
        return [
            'intec.core',
            'intec.universe'
        ];
    }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetTitle(Loc::getMessage('wizard.steps.begin.title'));
        $this->content .= Loc::getMessage('wizard.steps.begin.description');
        $this->SetNextStep(SiteStep::GetId());

        $wizard = $this->GetWizard();
    }

    function ShowStep()
    {
        $next = true;
        $dependencies = static::GetDependencies();

        if (!Loader::includeModule('intec.constructor') && !Loader::includeModule('intec.constructorlite')) {
            $this->content = Loc::getMessage('wizard.steps.begin.noModule', [
                '#MODULE_ID#' => 'intec.constructor'
            ]);

            $next = false;
        }

        if ($next)
            foreach ($dependencies as $dependency) {
                if (!Loader::includeModule($dependency)) {
                    $this->content = Loc::getMessage('wizard.steps.begin.noModule', [
                        '#MODULE_ID#' => $dependency
                    ]);

                    $next = false;
                }
            }

        if (!$next)
            $this->SetNextStep(null);
    }
}

class SiteStep extends CSelectSiteWizardStep
{
    public static function GetId() { return 'Site'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetPrevStep(BeginStep::GetId());
        $this->SetNextStep(TemplateStep::GetId());
    }
}

class TemplateStep extends CSelectTemplateWizardStep
{
    public static function GetId() { return 'Template'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetPrevStep(SiteStep::GetId());
        $this->SetNextStep(ModeStep::GetId());
    }
}

class ModeStep extends CWizardStep
{
    public static function GetId() { return 'Mode'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetPrevStep(TemplateStep::GetId());

        $this->SetTitle(Loc::getMessage('wizard.steps.mode.title'));

        $wizard = $this->GetWizard();
        $wizard->SetDefaultVars([
            'systemReplaceTemplate' => 'N',
            'systemConfigureRegions' => 'Y',
            'systemImportIBlocks' => 'Y'
        ]);
    }

    function ShowStep()
    {
        parent::ShowStep();

        $wizard = $this->GetWizard();

        $this->content .= '<style type="text/css">
            .panel {
                display: block;
                overflow: hidden;
            }
            .panel .panel-wrapper {
                display: block;
                margin: -10px;
            }
            .panel .panel-button {
                display: block;
                width: 50%;
                padding: 10px;
                float: left;
                border: none;
                background: none;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }
            .panel .panel-button .panel-button-wrapper {
                display: block;
                height: 250px;
                cursor: pointer;
                font-size: 0;
                border-radius: 3px;
                border: 2px dashed #e1e1e1;
                background: #f7f7f7;
                color: #828282;
                -webkit-transition: 0.3s;
                -moz-transition: 0.3s;
                -ms-transition: 0.3s;
                -o-transition: 0.3s;
                transition: 0.3s;
                -webkit-transition-property: background, border;
                -moz-transition-property: background, border;
                -ms-transition-property: background, border;
                -o-transition-property: background, border;
                transition-property: background, border;
            }
            .panel .panel-button .panel-button-wrapper:hover,
            .panel .panel-button .panel-button-wrapper:focus {
                border-color: #c1c1c1;
                background: #f1f1f1;
                color: #424242;
            }
            .panel .panel-button .panel-button-aligner {
                display: inline-block;
                vertical-align: middle;
                height: 100%;
                width: 0;
                overflow: hidden;
            }
            .panel .panel-button .panel-button-text {
                display: inline-block;
                vertical-align: middle;
                color: inherit;
                font-size: 24px;
            }
        </style>';

        $this->content .= '<div class="panel">';
        $this->content .= '<div class="panel-wrapper">';
        $this->content .= '<button class="panel-button" name="'.$wizard->GetVarPrefix().'systemMode" type="submit" value="Install">
            <div class="panel-button-wrapper">
                <div class="panel-button-aligner"></div>
                <div class="panel-button-text">
                    '.Loc::getMessage('wizard.modes.install').'
                </div>
            </div>
        </button>';
        $this->content .= '<button class="panel-button" name="'.$wizard->GetVarPrefix().'systemMode" type="submit" value="Update">
            <div class="panel-button-wrapper">
                <div class="panel-button-aligner"></div>
                <div class="panel-button-text">
                    '.Loc::getMessage('wizard.modes.update').'
                </div>
            </div>
        </button>';
        $this->content .= '<div style="clear: both;"></div>';
        $this->content .= '</div>';
        $this->content .= '</div>';

        if (Loader::includeModule('intec.constructor')) {
            $this->content .= '
            <div style="margin-top: 20px;">
                ' . $this->ShowHiddenField('systemReplaceTemplate', 'N') . '
                ' . $this->ShowCheckboxField('systemReplaceTemplate', 'Y', [
                    'id' => 'systemReplaceTemplate'
                ]) . '
                <label for="systemReplaceTemplate" class="wizard-input-title">
                    ' . Loc::getMessage('wizard.fields.systemReplaceTemplate') . '
                </label>
            </div>';
        }

        if (Loader::includeModule('intec.regionality')) {
            $this->content .= '
            <div style="margin-top: 20px;">
                ' . $this->ShowHiddenField('systemConfigureRegions', 'N') . '
                ' . $this->ShowCheckboxField('systemConfigureRegions', 'Y', [
                    'id' => 'systemConfigureRegions'
                ]) . '
                <label for="systemConfigureRegions" class="wizard-input-title">
                    ' . Loc::getMessage('wizard.fields.systemConfigureRegions') . '
                </label>
            </div>';
        }

        $this->content .= '
            <div style="margin-top: 20px;">
                ' . $this->ShowHiddenField('systemImportIBlocks', 'N') . '
                ' . $this->ShowCheckboxField('systemImportIBlocks', 'Y', [
                'id' => 'systemImportIBlocks'
            ]) . '
                <label for="systemImportIBlocks" class="wizard-input-title">
                    ' . Loc::getMessage('wizard.fields.systemImportIBlocks') . '
                </label>
            </div>';
    }

    function OnPostForm()
    {
        parent::OnPostForm();

        $wizard = $this->GetWizard();

        if ($wizard->IsPrevButtonClick())
            return;

        $wizard->SetCurrentStep(static::GetId());
        $mode = $wizard->GetVar('systemMode');

        if (!empty($mode))
            if ($mode == 'Update') {
                $wizard->SetCurrentStep(InstallStep::GetId());
            } else {
                $wizard->SetCurrentStep(DataSiteStep::GetId());
            }
    }
}

class DataSiteStep extends CWizardStep
{
    public static function GetId() { return 'DataSite'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetPrevStep(ModeStep::GetId());
        $this->SetNextStep(InstallStep::GetId());

        if (Loader::includeModule('sale'))
            $this->SetNextStep(DataShopStep::GetId());

        $this->SetTitle(Loc::getMessage('wizard.steps.dataSite.title'));

        $wizard = $this->GetWizard();
        $wizard->SetDefaultVars([
            'siteName' => Loc::getMessage('wizard.fields.siteName.value'),
            'sitePhone' => Loc::getMessage('wizard.fields.sitePhone.value'),
            'siteAddress' => Loc::getMessage('wizard.fields.siteAddress.value'),
            'siteMail' => Loc::getMessage('wizard.fields.siteMail.value'),
            'siteMetaDescription' => Loc::getMessage('wizard.fields.siteMetaDescription.value'),
            'siteMetaKeywords' => Loc::getMessage('wizard.fields.siteMetaKeywords.value'),
            'shopLocation' => Loc::getMessage('wizard.fields.shopLocation.value')
        ]);
    }

    function ShowStep()
    {
        parent::ShowStep();

        $this->content .= '<div class="wizard-input-form">';
        $this->content .= '
		<div class="wizard-input-form-block">
		    <div>
			    <label for="siteName" class="wizard-input-title">
			        '.Loc::getMessage('wizard.fields.siteName').'
                </label>
			</div>
			'.$this->ShowInputField('text', 'siteName', [
			    'id' => 'siteName',
                'class' => 'wizard-field'
            ]).'
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
		    <div>
			    <label for="sitePhone" class="wizard-input-title">
			        '.Loc::getMessage('wizard.fields.sitePhone').'
                </label>
            </div>
			'.$this->ShowInputField('text', 'sitePhone', [
                'id' => 'sitePhone',
                'class' => 'wizard-field'
            ]).'
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
		    <div>
			    <label for="siteAddress" class="wizard-input-title">
			        '.Loc::getMessage('wizard.fields.siteAddress').'
                </label>
            </div>
			'.$this->ShowInputField('text', 'siteAddress', [
                'id' => 'siteAddress',
                'class' => 'wizard-field'
            ]).'
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
		    <div>
			    <label for="siteMail" class="wizard-input-title">
			        '.Loc::getMessage('wizard.fields.siteMail').'
                </label>
            </div>
			'.$this->ShowInputField('text', 'siteMail', [
                'id' => 'siteMail',
                'class' => 'wizard-field'
            ]).'
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
		    <div>
			    <label for="siteMetaDescription" class="wizard-input-title">
			        '.Loc::getMessage('wizard.fields.siteMetaDescription').'
                </label>
            </div>
			'.$this->ShowInputField('text', 'siteMetaDescription', [
                'id' => 'siteMetaDescription',
                'class' => 'wizard-field'
            ]).'
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
		    <div>
			    <label for="siteMetaKeywords" class="wizard-input-title">
			        '.Loc::getMessage('wizard.fields.siteMetaKeywords').'
                </label>
            </div>
			'.$this->ShowInputField('text', 'siteMetaKeywords', [
                'id' => 'siteMetaKeywords',
                'class' => 'wizard-field'
            ]).'
		</div>';
        $this->content .= '</div>';
    }

    function OnPostForm()
    {
        parent::OnPostForm();

        $wizard = $this->GetWizard();

        if ($wizard->IsPrevButtonClick())
            return;

        $errors = [];
        $variables = [
            'siteName',
            'sitePhone',
            'siteAddress'
        ];

        foreach ($variables as $variable) {
            $value = $wizard->GetVar($variable);

            if (empty($value))
                $errors[] = Loc::getMessage('wizard.fields.errors.empty', [
                    '#NAME#' => Loc::getMessage('wizard.fields.'.$variable)
                ]);
        }

        if (!empty($errors))
            $this->SetError(implode('<br />', $errors));
    }
}

class DataShopStep extends CWizardStep
{
    public static function GetId() { return 'DataShopStep'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetPrevStep(DataSiteStep::GetId());
        $this->SetNextStep(PersonTypesStep::GetId());

        $this->SetTitle(Loc::getMessage('wizard.steps.dataShop.title'));

        $wizard = $this->GetWizard();
        $wizard->SetDefaultVars([
            'shopLocalization' => 'ru',
            'shopEmail' => 'sale@'.$_SERVER['SERVER_NAME'],
            'shopOfName' => Loc::getMessage('wizard.fields.shopOfName.value'),
            'shopLocation' => Loc::getMessage('wizard.fields.shopLocation.value'),
            'shopAdr' => Loc::getMessage('wizard.fields.shopAdr.value'),
            'shopINN' => '1234567890',
            'shopKPP' => '123456789',
            'shopNS' => '0000 0000 0000 0000 0000',
            'shopBANK' => Loc::getMessage('wizard.fields.shopBANK.value'),
            'shopBANKREKV' => Loc::getMessage('wizard.fields.shopBANKREKV.value'),
            'shopKS' => '30101 810 4 0000 0000225',
            'siteStamp' => '',

            'shopOfName_ua' => Loc::getMessage('wizard.fields.shopOfName.valueUa'),
            'shopLocation_ua' => Loc::getMessage('wizard.fields.shopLocation.valueUa'),
            'shopAdr_ua' => Loc::getMessage('wizard.fields.shopAdr.valueUa'),
            'shopEGRPU_ua' => '',
            'shopINN_ua' => '',
            'shopNDS_ua' => '',
            'shopNS_ua' => '',
            'shopBank_ua' => '',
            'shopMFO_ua' => '',
            'shopPlace_ua' => '',
            'shopFIO_ua' => '',
            'shopTax_ua' => '',

            'installPriceBASE' => 'Y'
        ]);
    }

    function ShowStep()
    {
        parent::ShowStep();

        $wizard =& $this->GetWizard();
        $siteStamp = $wizard->GetVar("siteStamp", true);
        $firstStep = 'N';

        if (!CModule::IncludeModule("catalog"))
        {
            $this->content .= "<p style='color:red'>".Loc::getMessage('wizard.steps.dataShop.errors.catalog')."</p>";
            $this->SetNextStep(static::GetId());
        }
        else
        {
            $this->content .=
                '<div class="wizard-catalog-title">'.Loc::getMessage('wizard.fields.shopLocalization').'</div>
				<div class="wizard-input-form-block" >'.
                $this->ShowSelectField("shopLocalization", array(
                    "ru" => GetMessage("wizard.fields.shopLocalization.values.ru"),
                    "ua" => GetMessage("wizard.fields.shopLocalization.values.ua"),
                    "kz" => GetMessage("wizard.fields.shopLocalization.values.kz"),
                    "bl" => GetMessage("wizard.fields.shopLocalization.values.bl")
                ), array("onchange" => "langReload()", "id" => "localization_select","class" => "wizard-field", "style"=>"padding:0 0 0 15px")).'
				</div>';

            $currentLocalization = $wizard->GetVar("shopLocalization");
            if (empty($currentLocalization))
                $currentLocalization = $wizard->GetDefaultVar("shopLocalization");

            $this->content .= '<div class="wizard-catalog-title">'.Loc::getMessage('wizard.steps.dataShop.categories.common').'</div>
				<div class="wizard-input-form">';

            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopEmail">'.Loc::getMessage("wizard.fields.shopEmail").'</label>
					'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail", "class" => "wizard-field")).'
				</div>';

            //ru
            $this->content .= '<div id="ru_bank_details" class="wizard-input-form-block" style="display:'.(($currentLocalization == "ru" || $currentLocalization == "kz" || $currentLocalization == "bl") ? 'block':'none').'">
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopOfName">'.Loc::getMessage('wizard.fields.shopOfName').'</label>'
                .$this->ShowInputField('text', 'shopOfName', array("id" => "shopOfName", "class" => "wizard-field")).'
				</div>';

            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopLocation">'.Loc::getMessage('wizard.fields.shopLocation').'</label>'
                .$this->ShowInputField('text', 'shopLocation', array("id" => "shopLocation", "class" => "wizard-field")).'
				</div>';

            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopAdr">'.Loc::getMessage('wizard.fields.shopAdr').'</label>'
                .$this->ShowInputField('textarea', 'shopAdr', array("rows"=>"3", "id" => "shopAdr", "class" => "wizard-field")).'
				</div>';

            if($firstStep != "Y")
            {
                $this->content .= '
					<div class="wizard-catalog-title">'.Loc::getMessage('wizard.steps.dataShop.categories.bank').'</div>
					<table class="wizard-input-table">
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopINN').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopINN', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopKPP').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopKPP', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopNS').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNS', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopBANK').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBANK', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopBANKREKV').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBANKREKV', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopKS').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopKS', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.siteStamp').':</td>
							<td class="wizard-input-table-right">'.$this->ShowFileField("siteStamp", Array("show_file_info" => "N", "id" => "siteStamp")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</td>
						</tr>
					</table>
				</div><!--ru-->
				';
            }
            $this->content .= '<div id="ua_bank_details" class="wizard-input-form-block" style="display:'.(($currentLocalization == "ua") ? 'block':'none').'">
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopOfName_ua">'.Loc::getMessage("wizard.fields.shopOfName").'</label>'
                .$this->ShowInputField('text', 'shopOfName_ua', array("id" => "shopOfName_ua", "class" => "wizard-field")).'
				</div>';

            $this->content .= '<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopLocation_ua">'.Loc::getMessage('wizard.fields.shopLocation').'</label>'
                .$this->ShowInputField('text', 'shopLocation_ua', array("id" => "shopLocation_ua", "class" => "wizard-field")).'
				</div>';


            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopAdr_ua">'.Loc::getMessage('wizard.fields.shopAdr').'</label>'.
                $this->ShowInputField('textarea', 'shopAdr_ua', array("rows"=>"3", "id" => "shopAdr_ua", "class" => "wizard-field")).'
				</div>';

            if($firstStep != "Y")
            {
                $this->content .= '
					<div class="wizard-catalog-title">'.Loc::getMessage('wizard.steps.dataShop.categories.bankUa').'</div>
					<p>'.Loc::getMessage('wizard.steps.dataShop.categories.bankUa.description').'</p>
					<table class="wizard-input-table">
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopEGRPU_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopEGRPU_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopINN_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopINN_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopNDS_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNDS_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopNS_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNS_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopBank_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBank_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopMFO_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopMFO_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopPlace_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopPlace_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopFIO_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopFIO_ua', array("class" => "wizard-field")).'</td>
						</tr>
						<tr>
							<td class="wizard-input-table-left">'.Loc::getMessage('wizard.fields.shopTax_ua').':</td>
							<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopTax_ua', array("class" => "wizard-field")).'</td>
						</tr>
					</table>
				</div>
				';
            }

            if (CModule::IncludeModule("catalog"))
            {
                $db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
                if (!$db_res->Fetch())
                {
                    $this->content .= '
					<div class="wizard-input-form-block">
						<div class="wizard-catalog-title">'.Loc::getMessage('wizard.fields.shopPriceBase').'</div>
						<div class="wizard-input-form-block-content">
							'. Loc::getMessage('wizard.fields.shopPriceBase.text1') .'<br><br>
							'. $this->ShowCheckboxField("installPriceBASE", "Y",
                            (array("id" => "install-demo-data")))
                        . ' <label for="install-demo-data">'.Loc::getMessage('wizard.fields.shopPriceBase.text2').'</label><br />

						</div>
					</div>';
                }
            }

            $this->content .= '</div>';

            $this->content .= '
				<script>
					function langReload()
					{
						var objSel = document.getElementById("localization_select");
						var locSelected = objSel.options[objSel.selectedIndex].value;
						document.getElementById("ru_bank_details").style.display = (locSelected == "ru" || locSelected == "kz" || locSelected == "bl") ? "block" : "none";
						document.getElementById("ua_bank_details").style.display = (locSelected == "ua") ? "block" : "none";
						/*document.getElementById("kz_bank_details").style.display = (locSelected == "kz") ? "block" : "none";*/
					}
				</script>
			';
        }
    }

    function OnPostForm()
    {
        $wizard =& $this->GetWizard();
        $res = $this->SaveFile("siteStamp", ["extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"]);
    }
}

class PersonTypesStep extends CWizardStep
{
    public static function GetId() { return 'PersonTypes'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
        $this->SetPrevStep(DataShopStep::GetId());
        $this->SetNextStep(ServicesStep::GetId());

        $this->SetTitle(Loc::getMessage('wizard.steps.personTypes.title'));
    }

    function ShowStep()
    {
        parent::ShowStep();

        $this->content .= '<div class="wizard-input-form">';
        $this->content .= '<div class="wizard-input-form-block">';
        $this->content .= '<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
        $this->content .=
        '<div class="wizard-catalog-form-item">
            '.$this->ShowCheckboxField('personType[fiz]', 'Y', [
                'id' => 'personTypeF'
            ]).'<label for="personTypeF">'.Loc::getMessage('wizard.persons.fiz').'</label>
        </div>';
        $this->content .=
        '<div class="wizard-catalog-form-item">
            '.$this->ShowCheckboxField('personType[ur]', 'Y', [
                'id' => 'personTypeU'
            ]).'<label for="personTypeU">'.Loc::getMessage('wizard.persons.ur').'</label>
        </div>';
        $this->content .= '</div>';
        $this->content .=
        '<div class="wizard-catalog-form-item">
            '.Loc::getMessage('wizard.steps.locations.description')
        .'<div>';
        $this->content .= '</div>';
        $this->content .= '</div>';
    }
}

class ServicesStep extends CWizardStep
{
    public static function GetId() { return 'ServicesStep'; }

    function InitStep()
    {
        $this->SetStepID(static::GetId());
        $this->SetTitle(Loc::getMessage('wizard.steps.services.title'));
        $this->SetPrevStep(PersonTypesStep::GetId());
        $this->SetNextStep(InstallStep::GetId());

        $wizard =& $this->GetWizard();

        if(LANGUAGE_ID == "ru")
        {
            $shopLocalization = $wizard->GetVar("shopLocalization", true);

            if ($shopLocalization == "ua")
                $wizard->SetDefaultVars(
                    Array(
                        "paysystem" => Array(
                            "cash" => "Y",
                            "oshad" => "Y",
                            "bill" => "Y",
                        ),
                        "delivery" => Array(
                            "courier" => "Y",
                            "self" => "Y",
                        )
                    )
                );
            else
                $wizard->SetDefaultVars(
                    Array(
                        "paysystem" => Array(
                            "cash" => "Y",
                            "sber" => "Y",
                            "bill" => "Y",
                            "collect" => "Y"  //cash on delivery
                        ),
                        "delivery" => Array(
                            "courier" => "Y",
                            "self" => "Y",
                            "rus_post" => "N",
                            "ua_post" => "N",
                            "kaz_post" => "N"
                        )
                    )
                );
        }
        else
        {
            $wizard->SetDefaultVars(
                Array(
                    "paysystem" => Array(
                        "cash" => "Y",
                        "paypal" => "Y",
                    ),
                    "delivery" => Array(
                        "courier" => "Y",
                        "self" => "Y",
                        "dhl" => "Y",
                        "ups" => "Y",
                    )
                )
            );
        }
    }

    function OnPostForm()
    {
        $wizard = &$this->GetWizard();
        $paysystem = $wizard->GetVar("paysystem");

        if (
            empty($paysystem["cash"])
            && empty($paysystem["sber"])
            && empty($paysystem["bill"])
            && empty($paysystem["paypal"])
            && empty($paysystem["oshad"])
            && empty($paysystem["collect"])
        )
            $this->SetError(Loc::getMessage('wizard.steps.services.paySystems.errors.unselected'));
        /*payer type
                if(LANGUAGE_ID == "ru")
                {
                    $personType = $wizard->GetVar("personType");

                    if (empty($personType["fiz"]) && empty($personType["ur"]))
                        $this->SetError(GetMessage('WIZ_NO_PT'));
                }
        ===*/
    }

    function ShowStep()
    {

        $wizard =& $this->GetWizard();
        $shopLocalization = $wizard->GetVar("shopLocalization", true);
        $personType = $wizard->GetVar("personType");
        $arAutoDeliveries = array();
        $isModuleSaleIncluded = \Bitrix\Main\Loader::includeModule("sale");

        if ($isModuleSaleIncluded)
        {
            $dbRes = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                'filter' => array(
                    '=CLASS_NAME' => array(
                        '\Sale\Handlers\Delivery\SpsrHandler',
                        '\Bitrix\Sale\Delivery\Services\Automatic',
                        '\Sale\Handlers\Delivery\AdditionalHandler'
                    )
                ),
                'select' => array('ID', 'CODE', 'ACTIVE', 'CLASS_NAME')
            ));

            while($dlv = $dbRes->fetch())
            {
                if($dlv['CLASS_NAME'] == '\Sale\Handlers\Delivery\SpsrHandler')
                {
                    $arAutoDeliveries['spsr'] = $dlv['ACTIVE'];
                }
                elseif($dlv['CLASS_NAME'] == '\Sale\Handlers\Delivery\AdditionalHandler' && $dlv['CONFIG']['MAIN']['SERVICE_TYPE'] == 'RUSPOST')
                {
                    $arAutoDeliveries['ruspost'] = $dlv['ACTIVE'];
                }
                elseif(!empty($dlv['CODE']))
                {
                    $arAutoDeliveries[$dlv['CODE']] = $dlv['ACTIVE'];
                }
            }
        }

        $this->content .= '<div class="wizard-input-form">';
        $this->content .= '
		<div class="wizard-input-form-block">
			<div class="wizard-catalog-title">'.Loc::getMessage("wizard.steps.services.paySystems.title").'</div>
			<div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					<div class="wizard-catalog-form-item">
						'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).
            ' <label for="paysystemC">'.Loc::getMessage("wizard.steps.services.paySystems.items.cash").'</label>
					</div>';

        if(LANGUAGE_ID == "ru")
        {
            if($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
                $this->content .=
                    '<div class="wizard-catalog-form-item">'.
                    $this->ShowCheckboxField('paysystem[oshad]', 'Y', (array("id" => "paysystemO"))).
                    ' <label for="paysystemO">'.Loc::getMessage("wizard.steps.services.paySystems.items.oshad").'</label>
							</div>';
            if ($shopLocalization == "ru")
            {
                if ($personType["fiz"] == "Y")
                    $this->content .=
                        '<div class="wizard-catalog-form-item">'.
                        $this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))).
                        ' <label for="paysystemS">'.Loc::getMessage("wizard.steps.services.paySystems.items.sberbank").'</label>
								</div>';
                if ($personType["fiz"] == "Y" || $personType["ur"] == "Y")
                    $this->content .=
                        '<div class="wizard-catalog-form-item">'.
                        $this->ShowCheckboxField('paysystem[collect]', 'Y', (array("id" => "paysystemCOL"))).
                        ' <label for="paysystemCOL">'.Loc::getMessage("wizard.steps.services.paySystems.items.cod").'</label>
								</div>';
            }
            if($personType["ur"] == "Y")
            {
                $this->content .=
                    '<div class="wizard-catalog-form-item">'.
                    $this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))).
                    ' <label for="paysystemB">';
                if ($shopLocalization == "ua")
                    $this->content .= Loc::getMessage("wizard.steps.services.paySystems.items.billUa");
                else
                    $this->content .= Loc::getMessage("wizard.steps.services.paySystems.items.bill");
                $this->content .= '</label>
							</div>';
            }
        }
        else
        {
            $this->content .=
                '<div class="wizard-catalog-form-item">'.
                $this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))).
                ' <label for="paysystemP">PayPal</label>
						</div>';
        }
        $this->content .= '</div>
			</div>
			<div class="wizard-catalog-form-item">'.Loc::getMessage("wizard.steps.services.paySystems.description").'</div>
		</div>';

        if (
            LANGUAGE_ID != "ru" ||
            LANGUAGE_ID == "ru" &&
            (
                $shopLocalization == "ru" && ($arAutoDeliveries["rus_post"] != "Y")
                || $shopLocalization == "ua" && ($arAutoDeliveries["ua_post"] != "Y")
                || $shopLocalization == "kz" && ($arAutoDeliveries["kaz_post"] != "Y")
            )
        )
        {
            $deliveryNotes = array();
            $deliveryContent = '<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
            $deliveryContent .= '<div class="wizard-catalog-form-item">
                '.$this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))).
                ' <label for="deliveryC">'.Loc::getMessage("wizard.steps.services.deliveries.items.courier").'</label>
            </div>
            <div class="wizard-catalog-form-item">
                '.$this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))).
                ' <label for="deliveryS">'.Loc::getMessage("wizard.steps.services.deliveries.items.selfpick").'</label>
            </div>';

            if(LANGUAGE_ID == "ru")
            {
                if ($shopLocalization == "ru")
                {
                    if ($arAutoDeliveries["ruspost"] != "Y" && $isModuleSaleIncluded)
                    {
                        \Bitrix\Sale\Delivery\Services\Manager::getHandlersList();
                        $res = \Sale\Handlers\Delivery\AdditionalHandler::getSupportedServicesList();

                        if(!empty($res['NOTES']) && is_array($res['NOTES']))
                        {
                            $deliveryNotes = $res['NOTES'];
                        }
                        else
                        {
                            $deliveryContent .= '									
								<div class="wizard-catalog-form-item">'.
                                $this->ShowCheckboxField('delivery[ruspost]', 'Y', (array("id" => "deliveryR"))).
                                ' <label for="deliveryR">'.Loc::getMessage("wizard.steps.services.deliveries.items.rupost").'</label>
								</div>';
                        }
                    }

                    if ($arAutoDeliveries["rus_post"] != "Y")
                    {
                        $deliveryContent .=
                            '<div class="wizard-catalog-form-item">'.
                            $this->ShowCheckboxField('delivery[rus_post]', 'Y', (array("id" => "deliveryR2"))).
                            ' <label for="deliveryR2">'.Loc::getMessage("wizard.steps.services.deliveries.items.rupost2").'</label>
							</div>';
                    }

                    if ($arAutoDeliveries["rus_post_first"] != "Y")
                    {
                        $deliveryContent .=
                            '<div class="wizard-catalog-form-item">'.
                            $this->ShowCheckboxField('delivery[rus_post_first]', 'Y', (array("id" => "deliveryRF"))).
                            ' <label for="deliveryRF">'.Loc::getMessage("wizard.steps.services.deliveries.items.rf").'</label>
							</div>';
                    }
                }
                elseif ($shopLocalization == "ua")
                {
                    if ($arAutoDeliveries["ua_post"] != "Y")
                        $deliveryContent .=
                            '<div class="wizard-catalog-form-item">'.
                            $this->ShowCheckboxField('delivery[ua_post]', 'Y', (array("id" => "deliveryU"))).
                            ' <label for="deliveryU">'.Loc::getMessage("wizard.steps.services.deliveries.items.ua").'</label>
							</div>';
                }
                elseif ($shopLocalization == "kz")
                {
                    if ($arAutoDeliveries["kaz_post"] != "Y")
                        $deliveryContent .=
                            '<div class="wizard-catalog-form-item">'.
                            $this->ShowCheckboxField('delivery[kaz_post]', 'Y', (array("id" => "deliveryK"))).
                            ' <label for="deliveryK">'.Loc::getMessage("wizard.steps.services.deliveries.items.kz").'</label>
							</div>';
                }
            }
            else
            {
                $deliveryContent .=
                    '<div class="wizard-catalog-form-item">'.
                    $this->ShowCheckboxField('delivery[dhl]', 'Y', (array("id" => "deliveryD"))).
                    ' <label for="deliveryD">DHL</label>
					</div>';
                $deliveryContent .=
                    '<div class="wizard-catalog-form-item">'.
                    $this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))).
                    ' <label for="deliveryU">UPS</label>
					</div>
				</div>';
            }

            if(!empty($deliveryNotes))
            {
                $deliveryContent ='
					<link rel="stylesheet" type="text/css" href="/bitrix/wizards/bitrix/eshop/css/style.css">
					<div class="eshop-wizard-info-note-wrap">
						<div class="eshop-wizard-info-note">
							'.implode("<br>\n", $deliveryNotes).'
						</div>
					</div>'.
                    $deliveryContent;
            }

            $this->content  .=
                '<div class="wizard-input-form-block">
					<div class="wizard-catalog-title">'.Loc::getMessage("wizard.steps.services.deliveries.title").'</div>
					<div>'.
                $deliveryContent.
                '</div>
					<div class="wizard-catalog-form-item">'.Loc::getMessage("wizard.steps.services.deliveries.description").'</div>
				</div>';
        }

        $this->content .= '
		<div>
			<div class="wizard-catalog-title">'.Loc::getMessage("wizard.steps.services.locations.title").'</div>
			<div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
        if(in_array(LANGUAGE_ID, array("ru", "ua")))
        {
            $this->content .=
                '<div class="wizard-catalog-form-item">'.
                $this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
                ." <label for=\"loc_ussr\">".Loc::getMessage('wizard.steps.services.locations.items.ussr')."</label>
				</div>";
            $this->content .=
                '<div class="wizard-catalog-form-item">'.
                $this->ShowRadioField("locations_csv", "loc_ua.csv", array("id" => "loc_ua"))
                ." <label for=\"loc_ua\">".Loc::getMessage('wizard.steps.services.locations.items.ua')."</label>
				</div>";
            $this->content .=
                '<div class="wizard-catalog-form-item">'.
                $this->ShowRadioField("locations_csv", "loc_kz.csv", array("id" => "loc_kz"))
                ." <label for=\"loc_kz\">".Loc::getMessage('wizard.steps.services.locations.items.kz')."</label>
				</div>";
        }
        $this->content .=
            '<div class="wizard-catalog-form-item">'.
            $this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
            ." <label for=\"loc_usa\">".Loc::getMessage('wizard.steps.services.locations.items.usa')."</label>
			</div>";
        $this->content .=
            '<div class="wizard-catalog-form-item">'.
            $this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
            ." <label for=\"loc_cntr\">".Loc::getMessage('wizard.steps.services.locations.items.country')."</label>
			</div>";
        $this->content .=
            '<div class="wizard-catalog-form-item">'.
            $this->ShowRadioField("locations_csv", "", array("id" => "none"))
            ." <label for=\"none\">".Loc::getMessage('wizard.steps.services.locations.items.none')."</label>
			</div>";

        $this->content .= '
				</div>
			</div>
		</div>';

        $this->content .= '<div class="wizard-catalog-form-item">'.Loc::GetMessage("wizard.steps.services.deliveries.hint").'</div>';

        $this->content .= '</div>';
    }
}

class InstallStep extends CDataInstallWizardStep
{
    public static function GetId() { return 'Install'; }

    function InitStep()
    {
        parent::InitStep();

        $this->SetStepID(static::GetId());
    }
}

class FinishStep extends CFinishWizardStep
{
    public static function GetId() { return 'End'; }

    function InitStep()
    {
        parent::InitStep();
    }

    function ShowStep()
    {
        parent::ShowStep();

        $wizard = $this->GetWizard();
        $sSiteID = WizardServices::GetCurrentSiteID($wizard->GetVar('siteID'));
        $sSiteDir = '/';
        $arSite = CSite::GetByID($sSiteID);
        $arSite = $arSite->GetNext();

        if (!empty($arSite))
            $sSiteDir = $arSite['DIR'];

        if ($wizard->GetVar('systemMode') !== 'Update')
            $this->CreateNewIndex();

        COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $sSiteID);

        $path = $_SERVER['DOCUMENT_ROOT'].$sSiteDir.'.wizard.json';

        if (is_file($path))
            unlink($path);
    }
}