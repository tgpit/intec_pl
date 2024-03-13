<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\constructor\models\build\Area;
use intec\constructor\models\build\template\Container;
use intec\constructor\models\build\template\Containers;

class AreaActions extends Actions
{
    /**
     * Действие. Возвращает список зон синхронизации.
     * @return array
     */
    public function actionGetList()
    {
        $data = [];

        /** @var Area[] $areas */
        $areas = $this->build->getAreas()
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        foreach ($areas as $area) {
            $data[] = [
                'id' => $area->id,
                'code' => $area->code,
                'name' => $area->name,
                'sort' => $area->sort
            ];
        }

        return $this->successResponse($data);
    }

    /**
     * Действие. Возвращает структуру зоны синхронизации.
     * @return array
     */
    public function actionGetStructure()
    {
        $request = $this->request;
        $code = $request->post('code');

        /** @var Area $areaCurrent */
        $areaCurrent = Area::find()->where([
            'code' => $code
        ])->one();

        if (empty($areaCurrent))
            return $this->errorResponse();

        $areas = $this->build->getAreas(true);
        $areasId = [];

        foreach ($areas as $area)
            $areasId[] = $area->id;

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
            ->where([
                'areaId' => $areasId
            ])
            ->indexBy('id')
            ->all();

        $container = $containers->getTree($this->build, $areaCurrent, null);

        if (empty($container)) {
            $container = new Container();
            $container->display = 1;
            $container->type = Container::TYPE_NORMAL;
        }

        $areaCurrent->populateRelation('container', $container);

        return $this->successResponse($areaCurrent->getStructure());
    }
}