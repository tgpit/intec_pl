<?php
namespace Ipolh\SDEK\Bitrix\Entity;

use Ipolh\SDEK\SDEK\Entity\OptionsInterface;


/**
 * Class options
 * @package Ipolh\SDEK
 * ������ ��� �������� �������� ����� ����-������ � ������-�����������.
 * ��������� �������� �������� ����� �� ����, � ����� ������ ����������.
 * ���, ����� key ����� �������� ����� options->getOption(key) ��� �� options->getKey()
 * �������, ��� ��� ������������ option.php �� ������ ��������� � ����� ������ ������� ��� � ���������� ��������
 */
class Options implements OptionsInterface
{
    public static function fetchOption($code)
    {
        return \Ipolh\SDEK\option::get($code);
    }

    public function pushOption($option,$handle)
    {
        $this->$option = $handle;
    }

    public function __call($name, $arguments)
    {
        if(strpos($name,'fetch') !== false)
        {
            $option = lcfirst(substr($name,5));

            if(property_exists($this,$option))
                return $this->$option;
            else {
                $this->$option = self::fetchOption($option);
                return $this->$option;
            }
        }
        elseif(strpos($name,'push') !== false)
        {
            $option = lcfirst(substr($name,4));

            $this->pushOption($option,$arguments[0]);

            return $this;
        }
        else
            throw new \Exception('Call to unknown method '.$name);
    }
}