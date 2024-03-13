<?php
namespace Avito\Export\Structure\PersonalBelongings;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;
use Avito\Export\Structure\Category;

class GoodsForChildrenAndToys implements Category, CategoryLevel
{
    use Concerns\HasOnce;
    use Concerns\HasLocale;

    public function name() : string
    {
        return self::getLocale('NAME');
    }

	public function categoryLevel() : ?string
	{
		return CategoryLevel::CATEGORY;
	}

    public function dictionary() : Dictionary\Dictionary
    {
		return new Dictionary\Fixed([
            'Condition' => new Props\ChildrenConditionProduct(),
            'NDS' => new Props\Nds()
        ]);
    }

    public function children() : array
    {
        return $this->once('children', static function() {
            self::includeLocale();

            $customFactory = new Factory(self::getLocalePrefix());

	        /** @noinspection SpellCheckingInspection */
	        return $customFactory->make([
                'Car seats',
                'Bicycles and scooters',
                'Children furniture',
                'Baby strollers' => [
                    'dictionary' => new Dictionary\Compound([
                        new Dictionary\XmlTree('personalbelongings/babystrollers.xml'),
	                    new Dictionary\Fixed([
		                    'Color' => new Props\Color(),
		                    'Type' => new Props\Type(),
	                    ]),
                    ])
                ],
                'Toys',
                'Bedding',
                'Feeding Products',
                'Bathing Products',
                'School Supplies',
            ]);
        });
    }
}