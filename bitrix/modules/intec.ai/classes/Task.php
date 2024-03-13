<?php
namespace intec\ai\models;

use intec\core\db\ActiveRecord;
use Bitrix\Main\Localization\Loc;

class Task extends ActiveRecord {
    protected static $_current;
    public static function tableName()
    {
        return 'ai_tasks';
    }

    public function taskUpdate() {
        if ($this->done === 'N') {
            \CAdminMessage::ShowMessage(array(
                "TYPE" => "ERROR",
                "MESSAGE" => Loc::getMessage('intec.ai.task.index.taskNumber', array('#TASK_NUMBER#'=> $this->id)).
                    Loc::getMessage('intec.ai.task.index.notDoneMessage'),
                "DETAILS" => "",
                "HTML" => true
            ));
            return;           
        }

        $taskGenerationResult = trim($this->generationResult);
        $taskIblockProperty = $this->iblockProperty;
        $taskElementFields = Array();

        $res = \CIBlockElement::GetList(Array(), Array("ID" => $this->elementId), false, Array(), Array("IBLOCK_ID"));
        if ($arFields = $res->GetNext()) {
            $taskElementFields = $arFields;
        } else {
            \CAdminMessage::ShowMessage(array(
                "TYPE" => "ERROR",
                "MESSAGE" => Loc::getMessage('intec.ai.task.index.taskNumber', array('#TASK_NUMBER#'=> $this->id)).
                    Loc::getMessage('intec.ai.task.index.elementDoesNotExist'),
                "DETAILS" => "",
                "HTML" => true
            ));
            return; 
        }

        if (strpos($taskIblockProperty, '[CUSTOM_PROPERTY]') !== false) {
            $taskIblockProperty = str_replace('[CUSTOM_PROPERTY]', '', $taskIblockProperty);
                        
            $iblockId = $taskElementFields['IBLOCK_ID'];
            $arFilter = Array("IBLOCK_ID" => $iblockId, "CODE" => $taskIblockProperty);
            $dbRes = \CIBlockProperty::GetList(Array(), $arFilter);

            if ($arRes = $dbRes->GetNext()) {
                \CIBlockElement::SetPropertyValuesEx($this->elementId, false, array($taskIblockProperty => $taskGenerationResult));
            } else {
                \CAdminMessage::ShowMessage(array(
                    "TYPE" => "ERROR",
                    "MESSAGE" => Loc::getMessage('intec.ai.task.index.taskNumber', array('#TASK_NUMBER#'=> $this->id)).
                        Loc::getMessage('intec.ai.task.index.propertyDoesNotExist'),
                    "DETAILS" => "",
                    "HTML" => true
                ));
            }
                    
        } else {
            $el = new \CIBlockElement;
            $arLoadProductArray = Array(
                $taskIblockProperty => $taskGenerationResult,
            );
            $el->Update($this->elementId, $arLoadProductArray);
        }
    }

    public function taskSendBack() {
        $this->done = 'N';
        $this->error = '';
        $this->save();
    }
}