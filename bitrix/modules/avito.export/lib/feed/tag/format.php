<?php
namespace Avito\Export\Feed\Tag;

use Avito\Export\Concerns;
use Avito\Export\Structure;
use Avito\Export\Dictionary\Listing;

class Format
{
	use Concerns\HasOnce;

	protected $category;

	public function __construct(Structure\Category $category = null)
	{
		$this->category = $category ?? new Structure\Index();
	}

	public function category() : Structure\Category
	{
		return $this->category;
	}

	public function tag(string $name) : ?Tag
	{
		$tags = $this->tags();

		return $tags[$name] ?? null;
	}

	/** @deprecated */
	public function hint(Tag $tag) : string
	{
		return $tag->hint();
	}

	/** @deprecated */
	public function title(Tag $tag) : string
	{
		return $tag->title();
	}

	/** @return Tag[] */
	public function tags() : array
	{
		return $this->once('tags', function() {
			$result = [];
			$tags = array_merge(
				$this->commonTags(),
				$this->contactTags(),
				$this->productTags(),
				$this->categoryTags(),
				$this->mediaTags(),
				$this->specialTags()
			);

			foreach ($tags as $tag)
			{
				$result[$tag->name()] = $tag;
			}

			return $result;
		});
	}

	protected function commonTags() : array
	{
		return [
			new Id([ 'required' => true ]),
			new DateBegin(),
			new DateEnd(),
			new Tag([
				'name' => 'ListingFee',
				'listing' => new Listing\ListingFee(),
			]),
			new Tag([
				'name' => 'AdStatus',
				'listing' => new Listing\AdStatus(),
			]),
			new Tag([
				'name' => 'AvitoId',
			]),
		];
	}

	protected function contactTags() : array
	{
		return [
			new Tag([
				'name' => 'ContactMethod',
				'listing' => new Listing\ContactMethod(),
			]),
			new Tag([
				'name' => 'ContactPhone',
			]),
			new ManagerName(),
			new Address([
				'required' => ['Latitude', 'Longitude'],
			]),
			new Latitude([
				'required' => ['Address'],
			]),
			new Longitude([
				'required' => ['Address'],
			]),
		];
	}

	protected function productTags() : array
	{
		return [
			new Category([ 'required' => true ]),
			new GoodsType([ 'deprecated' => true ]),
			new Title([ 'required' => true ]),
			new Description([ 'required' => true ]),
			new Price([ 'required' => true ]),
			new Tag([
				'name' => 'Condition',
				'required' => true,
				'listing' => new Listing\Condition(),
			]),
			new Tag([
				'name' => 'DisplayAreas',
				'wrapper' => true,
				'item' => 'Area',
				'multiple' => true,
			]),
		];
	}

	protected function categoryTags() : array
	{
		return []; // todo
	}

	protected function mediaTags() : array
	{
		return [
			new Images([ 'multiple' => true, 'required' => true ]),
			new VideoURL(),
		];
	}

	protected function specialTags() : array
	{
		return [
			new Characteristic([ 'multiple' => true ]),
			new Param([ 'multiple' => true ]),
		];
	}
}