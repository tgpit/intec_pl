<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);
$strHelpLang = 'ACRIT_CORE_NOTE_PROFILE_HELP_';

ob_start();
?>
<style>
	.acrit_note_profile_help a {
		text-decoration:none;
	}
	.acrit_note_profile_help span {
		vertical-align:middle;
	}
	.acrit_note_profile_help a img {
		vertical-align:middle;
	}
	.acrit_note_profile_help a span {
		text-decoration:underline;
		display:inline-block;
		line-height:100%;
		vertical-align:middle;
	}
	.acrit_note_profile_help a:hover span {
		text-decoration:none;
	}
</style>
<div class="acrit_note_profile_help">
	<img src="data:image/png;base64,<?=base64_encode(file_get_contents(__DIR__.'/help.png'));?>" alt="" style="vertical-align:middle;" />
	&nbsp;
	<span>
		<?=Helper::getMessage($strHelpLang.'MAIN_MESSAGE');?>
	</span>
	&nbsp;
	<a href="https://wa.me/+79270525930" target="_blank">
		<img src="data:image/png;base64,<?=base64_encode(file_get_contents(__DIR__.'/whatsapp.png'));?>" alt="" style="vertical-align:middle;" />
		<span>WhatsApp</span>
	</a>
	&nbsp;  &nbsp;
	<a href="https://t.me/+79270525930" target="_blank">
		<img src="data:image/png;base64,<?=base64_encode(file_get_contents(__DIR__.'/telegram.png'));?>" alt="" style="vertical-align:middle;" />
		<span>Telegram</span>
	</a>
</div>
<?
$strNote = ob_get_clean();

print Helper::showNote($strNote, true);
