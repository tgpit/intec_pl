<?php
namespace intec\constructor\builds\templates\editor\ajax;

use CSite;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

class SiteActions extends Actions
{
    /**
     * Действие. Возвращает список сайтов.
     * @return array
     */
    public function actionGetList()
    {
        $data = [];
        $sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'));

        foreach ($sites as $site) {
            $data[] = [
                'id' => $site['LID'],
                'active' => $site['ACTIVE'] === 'Y',
                'name' => !empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'],
                'directory' => $site['DIR'],
                'sort' => Type::toInteger($site['SORT'])
            ];
        }

        return $this->successResponse($data);
    }
}