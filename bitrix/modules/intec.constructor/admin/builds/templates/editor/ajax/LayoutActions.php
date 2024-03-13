<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\constructor\models\build\layout\Zone;

class LayoutActions extends Actions
{
    /**
     * Действие. Возвращает список контейнеров.
     * @return array
     */
    public function actionGetList()
    {
        $data = [];
        $layouts = $this->build->getLayouts();

        foreach ($layouts as $layout) {
            $zones = [];

            /** @var Zone[] $layoutZones */
            $layoutZones = $layout->getZones();

            foreach ($layoutZones as $layoutZone) {
                $zones[] = [
                    'code' => $layoutZone->getCode(),
                    'name' => $layoutZone->getName()
                ];
            }

            $data[] = [
                'code' => $layout->getCode(),
                'name' => $layout->getName(),
                'picture' => $layout->getPicturePath()->toRelative()->asAbsolute()->getValue('/'),
                'zones' => $zones
            ];
        }

        return $this->successResponse($data);
    }

    /**
     * Действие. Устанавливает разметку шаблона.
     * @return array
     */
    public function actionSet()
    {
        $code = $this->request->post('code');
        $layouts = $this->build->getLayouts();

        foreach ($layouts as $layout) {
            if ($layout->getCode() === $code) {
                $this->template->layoutCode = $code;
                $this->template->save();

                return $this->successResponse();
            }
        }

        return $this->errorResponse();
    }
}