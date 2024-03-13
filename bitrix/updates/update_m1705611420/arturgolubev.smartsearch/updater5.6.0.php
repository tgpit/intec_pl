<?
if(IsModuleInstalled('arturgolubev.smartsearch'))
{
	if (is_dir(dirname(__FILE__).'/install/components')){
		$updater->CopyFiles("install/components", "components/");
	}
}
?>