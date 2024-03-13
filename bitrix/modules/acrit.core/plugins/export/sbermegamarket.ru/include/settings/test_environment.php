<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div style="display:inline-block;">
	<select name="PROFILE[PARAMS][ENVIRONMENT]" data-role="acrit_exp_sbermegamarket_environment">
		<option value="test" <?if($this->arParams['ENVIRONMENT'] != 'prod'):?>selected<?endif?>>
			<?=static::getMessage('SETTINGS_NAME_ENVIRONMENT_TEST');?>
		</option>
		<option value="prod" <?if($this->arParams['ENVIRONMENT'] == 'prod'):?>selected<?endif?>>
			<?=static::getMessage('SETTINGS_NAME_ENVIRONMENT_PROD');?>
		</option>
	</select>
</div>
