<?php
namespace intec\constructor\models\build;

use CFile;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;
use intec\constructor\models\block\Category;
use intec\constructor\models\block\Template as BlockTemplate;
use intec\constructor\models\Build;
use intec\constructor\models\build\preset\Group;
use intec\constructor\models\build\presets\Block as BlockPreset;
use intec\constructor\models\build\presets\Component as ComponentPreset;
use intec\constructor\models\build\presets\Widget as WidgetPreset;
use intec\constructor\structure\Widget;
use intec\constructor\structure\widget\Template as WidgetTemplate;
use intec\constructor\structure\Widgets;

class Presets extends Collection
{
    public static function all($build = null, $collection = true)
    {
        $result = [];
        $files = [];

        /** @var BlockTemplate[] $blocks */
        $blocks = BlockTemplate::find()
            ->with(['category'])
            ->all();

        foreach ($blocks as $block) {
            /** @var Category $category */
            $category = $block->getCategory(true);

            $preset = new BlockPreset();
            $preset->code = $block->code;
            $preset->name = $block->name;
            $preset->sort = $block->sort;

            if (!empty($block->image)) {
                $files[] = $block->image;
                $preset->picturePath = $block->image;
            }

            if (!empty($category)) {
                $preset->group = new Group();
                $preset->group->code = $category->code;
                $preset->group->name = $category->name;
                $preset->group->sort = $category->sort;
            }

            $result[] = $preset;
        }

        if (!empty($files)) {
            $files = Arrays::fromDBResult(CFile::GetList([], [
                '@ID' => implode(',', $files)
            ]))->each(function ($index, &$file) {
                $file['SRC'] = CFile::GetFileSRC($file);
            })->indexBy('ID');
        } else {
            $files = Arrays::from([]);
        }

        foreach ($result as $preset) {
            /** @var Preset $preset */
            if (!empty($preset->picturePath)) {
                $file = $files->get($preset->picturePath);

                if (!empty($file)) {
                    $preset->picturePath = $file['SRC'];
                } else {
                    $preset->picturePath = null;
                }
            }
        }

        $widgets = Widgets::all();
        $count = 0;

        foreach ($widgets as $index => $widget) {
            /** @var Widget $widget */

            $preset = new WidgetPreset();
            $preset->code = $widget->getCode();
            $preset->template = '.default';
            $preset->name = $widget->getName();
            $preset->sort = (++$count) * 100;

            if ($widget->hasIcon())
                $preset->picturePath = $widget->getIconPath(true, '/');

            $preset->properties = [];

            $result[] = $preset;
        }

        if ($build instanceof Build) {
            $presets = $build->getMetaValue(['components', 'presets']);

            if (Type::isArray($presets))
                foreach ($presets as $preset) {
                    if (!($preset instanceof ComponentPreset))
                        continue;

                    $result[] = $preset;
                }

            unset($presets, $preset);
        }

        if ($collection)
            return new static($result);

        return $result;
    }
}
