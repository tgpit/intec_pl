<?php
namespace intec\constructor\builds\templates\editor\ajax;

use intec\constructor\structure\Widgets;
use intec\constructor\structure\Widget;
use intec\constructor\structure\widget\Template;
use intec\core\helpers\Type;

class WidgetActions extends Actions
{
    /**
     * @param String $parameter
     * @return Widget|null
     */
    protected function getWidget($parameter = 'code')
    {
        $widget = $this->request->post($parameter);

        if (empty($widget))
            return null;

        $widgets = Widgets::all();

        return $widgets->get($widget);
    }

    /**
     * @param Widget $widget
     * @param String $parameter
     * @return Template|null
     */
    protected function getWidgetTemplate($widget, $parameter = 'template')
    {
        $template = $this->request->post($parameter);

        if (empty($template))
            return null;

        return $widget->getTemplate($template, $this->build);
    }

    public function actionGetList()
    {
        $data = [];
        $widgets = Widgets::all();

        foreach ($widgets as $widget) {
            /** @var Widget $widget */

            $widgetData = [
                'namespace' => $widget->getNamespace(),
                'id' => $widget->getId(),
                'icon' => $widget->getIconPath(true, '/'),
                'name' => $widget->getName(),
                'script' => $widget->getModel(),
                'messages' => $widget->getLanguage()->getMessages(),
                'templates' => []
            ];

            $templates = $widget->getTemplates($this->build);

            foreach ($templates as $template) {
                $templateData = [
                    'code' => $template->getCode(),
                    'script' => $template->getModel(),
                    'view' => $template->render([], $this->build, $this->template, false),
                    'settings' => $template->getSettings(),
                    'messages' => $template->getLanguage()->getMessages()
                ];

                $widgetData['templates'][] = $templateData;
            }

            $data[] = $widgetData;
        }

        return $this->successResponse($data);
    }

    public function actionGetHeaders()
    {
        global $APPLICATION;

        $widget = $this->getWidget();

        if ($widget === null)
            exit();

        $template = $this->getWidgetTemplate($widget);

        $APPLICATION->ShowAjaxHead();

        if ($template !== null) {
            $template->includeHeaders(['editor']);
        } else {
            $widget->includeHeaders(['editor']);
        }

        exit();
    }

    public function actionGetData()
    {
        $widget = $this->getWidget();

        if ($widget === null)
            return $this->errorResponse();

        $template = $this->getWidgetTemplate($widget);

        if ($template === null)
            return $this->errorResponse();

        $properties = $this->request->post('properties');

        if (!Type::isArray($properties))
            $properties = [];

        return $this->successResponse($template->getData($properties, $this->build, $this->template));
    }

    public function actionRequest()
    {
        $data = null;
        $widget = $this->getWidget();

        if ($widget === null)
            return $this->errorResponse();

        $template = $this->getWidgetTemplate($widget);
        $properties = $this->request->post('properties');
        $parameters = $this->request->post('parameters');

        if (!Type::isArray($properties))
            $properties = [];

        if (!Type::isArray($parameters))
            $parameters = [];

        if ($template === null) {
            $data = $widget->runHandler($parameters, $properties, $this->build, $this->template);
        } else {
            $data = $template->runHandler($parameters, $properties, $this->build, $this->template);
        }

        return $this->successResponse($data);
    }
}