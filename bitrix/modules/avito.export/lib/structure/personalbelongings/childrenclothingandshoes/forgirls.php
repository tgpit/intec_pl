<?php
namespace Avito\Export\Structure\PersonalBelongings\ChildrenClothingAndShoes;

use Avito\Export\Assert;
use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class ForGirls implements Structure\Category, Structure\CategoryLevel
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
		    new Dictionary\XmlTree('personalbelongings/childrenclothingandshoes/for_girls/for_girls.xml'),
		    new Dictionary\Decorator(
			    new Dictionary\XmlTree('personalbelongings/childrenclothingandshoes/for_girls/size.xml'),
			    [
					'wait' => [
						'Apparel' => [
							self::getLocale('PANTS'),
							self::getLocale('OUTERWEAR'),
							self::getLocale('JUMPSUITS_AND_BODYSUITS'),
							self::getLocale('PAJAMAS'),
							self::getLocale('DRESSES_AND_SKIRTS'),
							self::getLocale('KNITWEAR'),
							self::getLocale('OTHER'),
						]
					],
			    ]
		    ),
		    new Dictionary\Decorator(
			    new Dictionary\XmlTree('personalbelongings/childrenclothingandshoes/for_girls/size_shoes.xml'),
			    [ 'wait' => [ 'Apparel' => self::getLocale('SHOES') ], ]
		    ),
	    ]);
    }

    public function children() : array
    {
        return $this->children;
    }
}