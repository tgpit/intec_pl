<?php
namespace Avito\Export\Structure\PersonalBelongings\Clothing;

use Avito\Export\Assert;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class WomanShoes implements Structure\Category, Structure\CategoryLevel
{
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
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/woman_shoes/woman_shoes.xml'),
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/materials_odezhda.xml'),
		    new Dictionary\XmlTree('personalbelongings/clothingshoesaccessories/woman_shoes/size.xml'),
	    ]);
    }

    public function children() : array
    {
        return $this->children;
    }
}