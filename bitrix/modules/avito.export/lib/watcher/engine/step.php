<?php
namespace Avito\Export\Watcher\Engine;

interface Step
{
	public function getName() : string;

	public function start(string $action, $offset = null) : void;

	public function afterChange() : void;

	public function afterRefresh() : void;
}