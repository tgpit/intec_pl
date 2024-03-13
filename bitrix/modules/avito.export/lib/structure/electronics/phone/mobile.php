<?php
namespace Avito\Export\Structure\Electronics\Phone;

use Avito\Export\Assert;
use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class Mobile implements Structure\Category, Structure\CategoryCompatible, Structure\CategoryLevel
{
	use Concerns\HasLocale;

	protected $name;
	protected $oldNames;
	protected $children;

	public function __construct(array $parameters)
	{
		Assert::notNull($parameters['name'], '$parameters[name]');

		$this->name = $parameters['name'];
		$this->oldNames = $parameters['oldNames'] ?? [];
		$this->children = $parameters['children'] ?? [];
	}

	public function categoryLevel() : ?string
	{
		return Structure\CategoryLevel::GOODS_TYPE;
	}

	public function name() : string
	{
		return $this->name;
	}

	public function oldNames() : array
	{
		return $this->oldNames;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Compound([
			new Dictionary\XmlCascade('electronics/phones.xml'),
			new Dictionary\Fixed([
				'Condition' => new Mobile\Condition(),
				'IMEI' => [],
			]),
		]);
	}

	public function children() : array
	{
		return $this->children;
	}
}