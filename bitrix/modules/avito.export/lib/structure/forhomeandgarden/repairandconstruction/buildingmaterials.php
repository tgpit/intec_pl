<?php
namespace Avito\Export\Structure\ForHomeAndGarden\RepairAndConstruction;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Custom;
use Avito\Export\Structure\ForHomeAndGarden\RepairAndConstruction\Properties\Piles\PileMaterial;

class BuildingMaterials extends Custom
{
	use Concerns\HasLocale;

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_TYPE;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Compound(array_merge(
			$this->groupDictionaryLumberType(),
			$this->groupDictionaryIronmongery(),
			$this->groupDictionaryPiles()
		));
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryLumberType() : array
	{
		return array_merge(
			$this->constructDictionariesForMaterials([
				'all' => [
					'other_tags' => 'forhomeandgarden/buildingmaterials/buildingmaterials.xml',
				],
				'GoodsSubType-->' . self::getLocale('GOODS_SUBTYPE_LUMBER') => [
					'LumberType' => 'forhomeandgarden/buildingmaterials/pilomaterialy/lumbertype.xml',
					'TypeOfWood' => 'forhomeandgarden/buildingmaterials/pilomaterialy/pilomaterialy.xml',
				],
				'LumberType-->' . self::getLocale('LUMBER_TYPE_BOARD') => [ $this->constructDictionariesEdgeType() ],
			]),
			$this->groupDictionarySortOfWood(),
			$this->groupDictionaryMoistureContent(),
			$this->groupDictionaryProcessing(),
			$this->groupDictionaryPurpose(),
			$this->groupDictionaryIsProfiled(),
			$this->groupDictionaryTypeOfStructure(),
			$this->groupDictionaryHeightWidth(),
			$this->groupDictionaryLength(),
			$this->groupDictionaryDiameter()
		);
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionarySortOfWood() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_WAINSCOTING',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_IMITATION_LOG_BLOCK_HOUSE',
				'LUMBER_TYPE_BRUSH',
				'LUMBER_TYPE_PLANKEN',
				'LUMBER_TYPE_FURNITURE_BOARD',
				'LUMBER_TYPE_PLINTH',
				'LUMBER_TYPE_FLAP',
				'LUMBER_TYPE_SKIRTING',
				'LUMBER_TYPE_SHELF',
				'LUMBER_TYPE_SKIRTING_FILLET',
				'LUMBER_TYPE_BASEBOARD',
				'LUMBER_TYPE_TRIM',
				'LUMBER_TYPE_CORNER',
			],
			'TypeOfWood' => [
				'TYPE_OF_WOOD_ABACHA',
				'TYPE_OF_WOOD_ACACIA',
				'TYPE_OF_WOOD_AMARANTH',
				'TYPE_OF_WOOD_BAKAUT',
				'TYPE_OF_WOOD_BALSA',
				'TYPE_OF_WOOD_BAMBOO',
				'TYPE_OF_WOOD_BIRCH',
				'TYPE_OF_WOOD_BEECH',
				'TYPE_OF_WOOD_WENGE',
				'TYPE_OF_WOOD_CHERRY',
				'TYPE_OF_WOOD_HORNBEAM',
				'TYPE_OF_WOOD_GRENADILLA',
				'TYPE_OF_WOOD_WALNUT',
				'TYPE_OF_WOOD_PEAR',
				'TYPE_OF_WOOD_OTHER',
				'TYPE_OF_WOOD_OAK',
				'TYPE_OF_WOOD_ZEBRANO',
				'TYPE_OF_WOOD_SPRUCE',
				'TYPE_OF_WOOD_IROKO',
				'TYPE_OF_WOOD_KARAGACH',
				'TYPE_OF_WOOD_CHESTNUT',
				'TYPE_OF_WOOD_CEDAR',
				'TYPE_OF_WOOD_MAPLE',
				'TYPE_OF_WOOD_MAHOGANY',
				'TYPE_OF_WOOD_LIME',
				'TYPE_OF_WOOD_LARCH',
				'TYPE_OF_WOOD_MAGNOLIA',
				'TYPE_OF_WOOD_MERANTI',
				'TYPE_OF_WOOD_MERBAU',
				'TYPE_OF_WOOD_ALDER',
				'TYPE_OF_WOOD_NUT',
				'TYPE_OF_WOOD_ASPEN',
				'TYPE_OF_WOOD_PADUK',
				'TYPE_OF_WOOD_PALISANDER',
				'TYPE_OF_WOOD_FIR',
				'TYPE_OF_WOOD_ROSEWOOD',
				'TYPE_OF_WOOD_BOXWOOD',
				'TYPE_OF_WOOD_PINE',
				'TYPE_OF_WOOD_TEAK',
				'TYPE_OF_WOOD_POPLAR',
				'TYPE_OF_WOOD_CIRICOTE',
				'TYPE_OF_WOOD_EBONY',
				'TYPE_OF_WOOD_ASH',
			],
		];

		return $this->constructMultipleDictionaries($map, $this->constructDictionariesSortOfWood());
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryMoistureContent() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BASEBOARD',
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_BRUSH',
				'LUMBER_TYPE_PLANKEN',
				'LUMBER_TYPE_FIREWOOD',
				'LUMBER_TYPE_TRIM',
				'LUMBER_TYPE_SLAB',
			],
			'TypeOfWood' => [
				'TYPE_OF_WOOD_ABACHA',
				'TYPE_OF_WOOD_ACACIA',
				'TYPE_OF_WOOD_AMARANTH',
				'TYPE_OF_WOOD_BAKAUT',
				'TYPE_OF_WOOD_BALSA',
				'TYPE_OF_WOOD_BAMBOO',
				'TYPE_OF_WOOD_BIRCH',
				'TYPE_OF_WOOD_BEECH',
				'TYPE_OF_WOOD_WENGE',
				'TYPE_OF_WOOD_CHERRY',
				'TYPE_OF_WOOD_HORNBEAM',
				'TYPE_OF_WOOD_GRENADILLA',
				'TYPE_OF_WOOD_WALNUT',
				'TYPE_OF_WOOD_PEAR',
				'TYPE_OF_WOOD_OTHER',
				'TYPE_OF_WOOD_OAK',
				'TYPE_OF_WOOD_ZEBRANO',
				'TYPE_OF_WOOD_SPRUCE',
				'TYPE_OF_WOOD_IROKO',
				'TYPE_OF_WOOD_KARAGACH',
				'TYPE_OF_WOOD_CHESTNUT',
				'TYPE_OF_WOOD_CEDAR',
				'TYPE_OF_WOOD_MAPLE',
				'TYPE_OF_WOOD_MAHOGANY',
				'TYPE_OF_WOOD_LIME',
				'TYPE_OF_WOOD_LARCH',
				'TYPE_OF_WOOD_MAGNOLIA',
				'TYPE_OF_WOOD_MERANTI',
				'TYPE_OF_WOOD_MERBAU',
				'TYPE_OF_WOOD_ALDER',
				'TYPE_OF_WOOD_WALNUT',
				'TYPE_OF_WOOD_ASPEN',
				'TYPE_OF_WOOD_PADUK',
				'TYPE_OF_WOOD_ROSEWOOD',
				'TYPE_OF_WOOD_ROSEWOOD',
				'TYPE_OF_WOOD_BOXWOOD',
				'TYPE_OF_WOOD_PINE',
				'TYPE_OF_WOOD_TEAK',
				'TYPE_OF_WOOD_POPLAR',
				'TYPE_OF_WOOD_CIRICOTE',
				'TYPE_OF_WOOD_EBONY',
				'TYPE_OF_WOOD_ASH',
			],
		];

		return $this->constructMultipleDictionaries($map, $this->constructDictionariesMoistureContent());
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryProcessing() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BOARD',
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_WAINSCOTING',
				'LUMBER_TYPE_IMITATION_LOG_BLOCK_HOUSE',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_BRUSH',
				'LUMBER_TYPE_PLANKEN',
				'LUMBER_TYPE_CYLINDRICAL_LOG',
				'LUMBER_TYPE_FURNITURE_BOARD',
				'LUMBER_TYPE_PLINTH',
				'LUMBER_TYPE_SKIRTING',
				'LUMBER_TYPE_SHELF',
				'LUMBER_TYPE_SKIRTING_FILLET',
				'LUMBER_TYPE_TRIM',
				'LUMBER_TYPE_CORNER',
			],
		];

		return $this->constructMultipleDictionaries($map, $this->constructDictionariesProcessing());
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryPurpose() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_WAINSCOTING',
				'LUMBER_TYPE_BOARD',
				'LUMBER_TYPE_IMITATION_LOG_BLOCK_HOUSE',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_CHIPS',
				'LUMBER_TYPE_PLANKEN',
			],
		];

		return $this->constructMultipleDictionaries($map, $this->constructDictionariesPurpose());
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryHeightWidth() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_BRUSH',
				'LUMBER_TYPE_WAINSCOTING',
				'LUMBER_TYPE_BOARD',
				'LUMBER_TYPE_IMITATION_LOG_BLOCK_HOUSE',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_PLANKEN',
				'LUMBER_TYPE_FURNITURE_BOARD',
				'LUMBER_TYPE_PLINTH',
				'LUMBER_TYPE_FLAP',
				'LUMBER_TYPE_DECKING',
				'LUMBER_TYPE_SHELF',
				'LUMBER_TYPE_SKIRTING',
				'LUMBER_TYPE_PALLET',
				'LUMBER_TYPE_SKIRTING_FILLET',
				'LUMBER_TYPE_BASEBOARD',
				'LUMBER_TYPE_TRIM',
				'LUMBER_TYPE_SLAB',
				'LUMBER_TYPE_CORNER',
				'LUMBER_TYPE_GLAZING_BEAD',
			],
		];

		return $this->constructMultipleDictionaries($map, new Dictionary\Fixed([
			'Height' => [],
			'Width' => [],
		]));
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryLength() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_BRUSH',
				'LUMBER_TYPE_WAINSCOTING',
				'LUMBER_TYPE_BOARD',
				'LUMBER_TYPE_IMITATION_LOG_BLOCK_HOUSE',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_PLANKEN',
				'LUMBER_TYPE_FURNITURE_BOARD',
				'LUMBER_TYPE_PLINTH',
				'LUMBER_TYPE_FLAP',
				'LUMBER_TYPE_DECKING',
				'LUMBER_TYPE_SHELF',
				'LUMBER_TYPE_SKIRTING',
				'LUMBER_TYPE_PALLET',
				'LUMBER_TYPE_SKIRTING_FILLET',
				'LUMBER_TYPE_BASEBOARD',
				'LUMBER_TYPE_TRIM',
				'LUMBER_TYPE_SLAB',
				'LUMBER_TYPE_CORNER',
				'LUMBER_TYPE_GLAZING_BEAD',
				'LUMBER_TYPE_CYLINDRICAL_LOG',
				'LUMBER_TYPE_SAWN_TIMBER',
				'LUMBER_TYPE_POST_FOR_THE_FENCE',
				'LUMBER_TYPE_SCANTLE'
			],
		];

		return $this->constructMultipleDictionaries($map, new Dictionary\Fixed([
			'Length' => [],
		]));
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryDiameter() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_FIREWOOD',
				'LUMBER_TYPE_SAWN_TIMBER',
				'LUMBER_TYPE_CYLINDRICAL_LOG',
				'LUMBER_TYPE_POST_FOR_THE_FENCE',
				'LUMBER_TYPE_SCANTLE',
				'LUMBER_TYPE_GLAZING_BEAD'
			],
		];

		return $this->constructMultipleDictionaries($map, new Dictionary\Fixed([
			'Diameter' => [],
		]));
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryIsProfiled() : array
	{
		return $this->constructMultipleDictionaries([
			'LumberType' => [
				'LUMBER_TYPE_BAR',
			],
		], $this->constructDictionariesIsProfiled());
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryTypeOfStructure() : array
	{
		$map = [
			'LumberType' => [
				'LUMBER_TYPE_BAR',
				'LUMBER_TYPE_WAINSCOTING',
				'LUMBER_TYPE_IMITATION_BAR_ROW_HOUSE',
				'LUMBER_TYPE_BRUSH',
				'LUMBER_TYPE_PLINTH',
				'LUMBER_TYPE_FLAP',
				'LUMBER_TYPE_SKIRTING',
				'LUMBER_TYPE_BASEBOARD',
				'LUMBER_TYPE_TRIM',
				'LUMBER_TYPE_CORNER',
			],
		];

		return $this->constructMultipleDictionaries($map, $this->constructDictionariesTypeOfStructure());
	}

	protected function groupDictionaryIronmongery() : array
	{
		return $this->constructDictionariesForMaterials([
			'GoodsSubType-->' . self::getLocale('GOODS_SUBTYPE_IRONMONGERY') => [
				'RCProduct' => 'forhomeandgarden/buildingmaterials/ironmongery/rcproduct.xml',
			],
			'RCProduct-->' . self::getLocale('RCPRODUCT_PLATE') => [
				new Dictionary\Fixed([
					'RCSlabType' => new Properties\Ironmongery\RCSlabType()
				]),
			],
			'RCProduct-->' . self::getLocale('RCPRODUCT_WALL') => [
				new Dictionary\Fixed([
					'RCWallType' => new Properties\Ironmongery\RCWallType()
				]),
			],
			'RCProduct-->' . self::getLocale('RCPRODUCT_WELL_ELEMENT') => [
				new Dictionary\Fixed([
					'RCElementType' => new Properties\Ironmongery\RCElementType()
				]),
			],
		]);
	}

	protected function groupDictionaryPiles() : array
	{
		return $this->constructDictionariesForMaterials([
			'GoodsSubType-->' . self::getLocale('GOODS_SUBTYPE_PILES') => [
				new Dictionary\Fixed([
					'PileType' => new Properties\Piles\PileType(),
					'PileMaterial' => new PileMaterial()
				]),
			],
		]);
	}

	protected function constructDictionariesEdgeType() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'EdgeType' => [
				self::getLocale('EDGE_TYPE_CROPPED'),
				self::getLocale('EDGE_TYPE_UNCUT'),
				self::getLocale('EDGE_TYPE_TONGUE_AND_GROOVE'),
				self::getLocale('EDGE_TYPE_TAPPED'),
			]
		]);
	}

	protected function constructDictionariesSortOfWood() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'SortOfWood' => [
				self::getLocale('SORT_OF_WOOD_SELECTED_EXTRA'),
				self::getLocale('SORT_OF_WOOD_1_A'),
				self::getLocale('SORT_OF_WOOD_1_2_AB'),
				self::getLocale('SORT_OF_WOOD_1_3_ABC'),
				self::getLocale('SORT_OF_WOOD_2_B'),
				self::getLocale('SORT_OF_WOOD_2_3_BC'),
				self::getLocale('SORT_OF_WOOD_3_C'),
				self::getLocale('SORT_OF_WOOD_3_4_CD'),
				self::getLocale('SORT_OF_WOOD_4_D'),
			]
		]);
	}

	protected function constructDictionariesMoistureContent() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'MoistureContent' => [
				self::getLocale('MOISTURE_CONTENT_DRY'),
				self::getLocale('MOISTURE_CONTENT_NATURAL'),
			]
		]);
	}

	protected function constructDictionariesProcessing() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'Processing' => [
				self::getLocale('PROCESSING_PLANING'),
				self::getLocale('PROCESSING_GRINDING'),
				self::getLocale('PROCESSING_CALIBRATING'),
				self::getLocale('PROCESSING_GROOVING'),
				self::getLocale('PROCESSING_BRUSHING'),
				self::getLocale('PROCESSING_HEAT_TREATMENT'),
				self::getLocale('PROCESSING_THERMOFORMING_CHAMBER_DRYING'),
				self::getLocale('PROCESSING_ANTISEPTIC'),
				self::getLocale('PROCESSING_IMPREGNATION'),
				self::getLocale('PROCESSING_PAINTING'),
			]
		]);
	}

	protected function constructDictionariesPurpose() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'Purpose' => [
				self::getLocale('PURPOSE_BATH'),
				self::getLocale('PURPOSE_DOOR'),
				self::getLocale('PURPOSE_HOUSE'),
				self::getLocale('PURPOSE_FENCE'),
				self::getLocale('PURPOSE_ROOFING'),
				self::getLocale('PURPOSE_STAIRS'),
				self::getLocale('PURPOSE_FURNITURE'),
				self::getLocale('PURPOSE_WINDOWS'),
				self::getLocale('PURPOSE_DECKING'),
				self::getLocale('PURPOSE_PALLETS'),
				self::getLocale('PURPOSE_FLOOR'),
				self::getLocale('PURPOSE_SHELF'),
				self::getLocale('PURPOSE_CEILING'),
				self::getLocale('PURPOSE_WALL'),
				self::getLocale('PURPOSE_ROOF_RAFTERS'),
				self::getLocale('PURPOSE_TERRACE'),
				self::getLocale('PURPOSE_FACADE'),
			]
		]);
	}

	protected function constructDictionariesIsProfiled() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'IsProfiled' => [
				self::getLocale('IS_PROFILED_YES'),
				self::getLocale('IS_PROFILED_NO'),
			]
		]);
	}

	protected function constructDictionariesTypeOfStructure() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'TypeOfStructure' => [
				self::getLocale('TYPE_OF_STRUCTURE_WHOLE'),
				self::getLocale('TYPE_OF_STRUCTURE_GLUED'),
			]
		]);
	}

	/** @return Dictionary\Dictionary[] */
	protected function constructMultipleDictionaries(array $map, Dictionary\Dictionary $dictionary) : array
	{
		$this->dictionaryLangPhares($map);

		return [
			new Dictionary\Decorator($dictionary, [
				'wait' => $map,
			]),
		];
	}

	protected function dictionaryLangPhares(array &$map) : void
	{
		foreach ($map as &$names)
		{
			foreach ($names as &$name)
			{
				$name = self::getLocale($name);
			}
			unset($name);
		}
		unset($names);
	}

	/** @return Dictionary\Dictionary[] */
	protected function constructDictionariesForMaterials(array $map) : array
	{
		$result = [];
		$await = [
			'wait' => []
		];

		foreach ($map as $type => $groupType)
		{
			foreach ($groupType as $param)
			{
				$await['wait'] = array_merge($await['wait'], $this->makeWait($type));

				if ($param instanceof Dictionary\Dictionary)
				{
					$result[] = new Dictionary\Decorator(
						$param,
						$await
					);
				}
				else if (is_string($param) && preg_match('/(\w+.)\/\w+.\.xml/', $param))
				{
					$result[] = new Dictionary\Decorator(
						new Dictionary\XmlTree($param),
						$await
					);
				}
			}
		}

		return $result;
	}
}