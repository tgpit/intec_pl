<?php
namespace Avito\Export\Structure\PersonalBelongings\Clothing;

use Avito\Export\Assert;
use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class MensWear implements Structure\Category, Structure\CategoryLevel
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
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/mens_wear/mens_wear.xml'),
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/materials_odezhda.xml'),
		    new Dictionary\Decorator(
			    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/mens_wear/size.xml'),
				[
					'wait' => [
						'Apparel' => [
							self::getLocale('PANTS'),
							self::getLocale('OUTERWEAR'),
							self::getLocale('JACKETS_AND_SUITS'),
							self::getLocale('KNITWEAR_TSHIRTS'),
							self::getLocale('SHIRTS'),
							self::getLocale('TRACKSUITS'),
							self::getLocale('SHORTS'),
							self::getLocale('UNDERWEAR'),
							self::getLocale('SWEATSHIRTS_AND_TSHIRTS'),
							self::getLocale('OTHER'),
						]
					],
				]
		    ),
	    ]);
    }

    public function children() : array
    {
        return $this->children;
    }
}