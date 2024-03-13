<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\constructor\models\build\Preset;

class PresetActions extends Actions
{
    /**
     * Действие. Возвращает список пресетов.
     * @return array
     */
    public function actionGetList()
    {
        $data = [];
        $presets = $this->build->getPresets();

        foreach ($presets as $preset) {
            /**
             * @var Preset $preset
             */

            $data[] = $preset->getStructure();
        }

        return $this->successResponse($data);
    }
}