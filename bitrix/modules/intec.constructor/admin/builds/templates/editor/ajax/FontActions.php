<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\constructor\models\Font;

class FontActions extends Actions
{
    /**
     * Действие. Возвращает список шрифтов.
     * @return array
     */
    public function actionGetList()
    {
        $data = [];

        /** @var Font[] $fonts */
        $fonts = Font::findAvailable();

        foreach ($fonts as $font) {
            $family = $font->getStyleCode();

            if (empty($family))
                continue;

            $data[] = [
                'code' => $font->code,
                'name' => $font->name,
                'sort' => $font->sort,
                'family' => $family,
                'style' => $font->getStyle()
            ];
        }

        return $this->successResponse($data);
    }
}