<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\constructor\models\block\Template as BlockTemplate;
use intec\constructor\models\build\template\Block;

class BlockActions extends Actions
{
    public function actionCreate()
    {
        $request = $this->request;
        $template = $request->post('code');
        $template = Encoding::convert($template, null, Encoding::UTF8);
        $template = BlockTemplate::findOne($template);

        if (empty($template))
            return $this->errorResponse();

        $block = new Block();
        $block->containerId = 0;
        $block->templateId = $this->template->id;
        $block->populateRelation('template', $this->template);

        if (!$block->importFrom($template))
            return $this->errorResponse();

        return $this->successResponse($block->getStructure());
    }

    public function actionConvert()
    {
        $request = $this->request;

        $block = $request->post('id');
        $code = $request->post('code');
        $name = $request->post('name');

        $code = Encoding::convert($code, null, Encoding::UTF8);
        $name = Encoding::convert($name, null, Encoding::UTF8);
        $block = Encoding::convert($block, null, Encoding::UTF8);

        /** @var Block $block */
        $block = Block::findOne($block);

        if (empty($block))
            return $this->errorResponse('blockNotFound', 'Block not found');

        $template = new BlockTemplate();
        $template->code = $code;

        if ($block->exportTo($template)) {
            if (!empty($name)) {
                $template->name = $name;
                $template->save();
            }

            return $this->successResponse();
        }

        $error = $template->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);

        return $this->errorResponse(null, $error);
    }

    public function actionClone()
    {
        $request = $this->request;
        $block = $request->post('id');

        /** @var Block $block */
        $block = Block::findOne($block);

        if (empty($block))
            return $this->errorResponse();

        $clone = new Block();
        $clone->containerId = 0;
        $clone->populateRelation('template', $this->template);

        if (!$clone->importFrom($block))
            return $this->errorResponse();

        return $this->successResponse($clone->getStructure());
    }

    public function actionGetContent()
    {
        global $APPLICATION;

        $request = $this->request;
        $block = $request->post('id');

        /** @var Block $block */
        $block = Block::find()
            ->where([
                'id' => $block
            ])
            ->one();

        if (empty($block))
            exit();

        $block->populateRelation('template', $this->template);
        $APPLICATION->ShowAjaxHead();
        $block->render(true, true);

        exit();
    }
}