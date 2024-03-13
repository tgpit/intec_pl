<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\core\helpers\ArrayHelper;
use intec\constructor\models\build\layout\Zone;
use intec\constructor\models\build\template\Container;
use intec\constructor\models\build\template\Containers;

class ContainerActions extends Actions
{
    /**
     * Действие. Возвращает список контейнеров.
     * @return array
     */
    public function actionGetList()
    {
        $data = [];
        $layouts = $this->build->getLayouts();
        $zones = ['default'];

        foreach ($layouts as $layout) {
            /** @var Zone[] $layoutZones */
            $layoutZones = $layout->getZones();

            foreach ($layoutZones as $layoutZone) {
                $layoutZoneCode = $layoutZone->getCode();

                if (!ArrayHelper::isIn($layoutZoneCode, $zones))
                    $zones[] = $layoutZoneCode;
            }
        }

        $areas = $this->build->getAreas(true);
        $areasId = [];

        foreach ($areas as $area)
            $areasId[] = $area->id;

        $conditions = [
            'or',
            ['templateId' => $this->template->id]
        ];

        if (!empty($areasId))
            $conditions[] = ['areaId' => $areasId];

        /** @var Containers $containers */
        $containers = Container::find()
            ->with([
                'link',
                'layoutZoneLink',
                'area',
                'component',
                'widget',
                'block',
                'variator',
                'variator.variants'
            ])
            ->where($conditions)
            ->indexBy('id')
            ->all();

        foreach ($zones as $zone) {
            /** @var Container $container */
            $container = $containers->getTree($this->build, $this->template, $zone);

            if (empty($container)) {
                $container = new Container();
                $container->display = 1;
                $container->type = Container::TYPE_NORMAL;
            }

            $container = $container->getStructure();
            $container['zone'] = $zone;
            $data[] = $container;
        }

        return $this->successResponse($data);
    }
}