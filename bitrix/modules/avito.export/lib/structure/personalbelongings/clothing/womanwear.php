<?php
namespace Avito\Export\Structure\PersonalBelongings\Clothing;

use Avito\Export\Assert;
use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class WomanWear implements Structure\Category, Structure\CategoryLevel
{
	use Concerns\HasLocale;

	protected $name;
    protected $children;

    public function __construct(array $parameters)
    {
        Assert::notNull($parameters['name'], '$parameters[name]');

        $this->name = $parameters['name'];
        $this->children = $parameters['children'] ?? [];
    }

    public function name() : string
    {
        return $this->name;
    }

	public function categoryLevel() : ?string
	{
		return Structure\CategoryLevel::GOODS_TYPE;
	}

    public function dictionary() : Dictionary\Dictionary
    {
	    /** @noinspection SpellCheckingInspection */
	    return new Dictionary\Compound([
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/women_wear/women_wear.xml'),
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/materials_odezhda.xml'),
		    new Dictionary\Decorator(
			    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/women_wear/size.xml'),
			    [
					'wait' => [
						'Apparel' => [
							self::getLocale('PANTS'),
							self::getLocale('SWIMSUITS'),
							self::getLocale('JACKETS_AND_SUITS'),
							self::getLocale('SHIRTS_AND_BLOUSES'),
							self::getLocale('WEDDING_DRESSES'),
							self::getLocale('TOPS_AND_T_SHIRTS'),
							self::getLocale('JUMPERS_SWEATERS_CARDIGANS'),
							self::getLocale('UNDERWEAR'),
							self::getLocale('OUTERWEAR'),
							self::getLocale('DRESSES'),
							self::getLocale('SKIRTS'),
							self::getLocale('HOODIES_AND_SWEATERS'),
							self::getLocale('OTHER'),
						]
					]
			    ]
		    ),
	    ]);
    }

    public function children() : array
    {
        return $this->children;
    }
}