<?php
namespace Ipolh\SDEK\Api\Entity;

class AbstractEntity
{
    public function __construct()
    {
    }

    /**
     * execute all get|is methods for all object properties
     * @return array with object properties as keys and internal values of this properties as values
     * return keys only for set properties
     * there is also getFields() method in abstractCollection, that does basically the same but for Collections
     */
    public function getFields()
    {
        $vars = array_filter(get_object_vars($this), function ($a) {
            return ($a !== null);
        }); //excluding all null properties from return
        return $this->parseFields($vars);
    }

    /**
     * execute all get|is methods for all object properties
     * @return array with all object properties as keys and internal values of this properties as values
     * returns keys for not set properties as well
     */
    public function getAllFields()
    {
        $vars = get_object_vars($this);
        return $this->parseFields($vars);
    }

    public function parseFields($fields)
    {
        $retFieldsArr = [];
        foreach ($fields as $key => $val) {
            $name = str_replace('__', '_', $key);
            $key = str_replace('__', '.', $key); //Thanks for this commas, Ozon-colleges :/

            $name = explode('_', $name);
            $name = implode(array_map('ucfirst', $name));
            $getMethod = 'get' . $name;
            $isMethod = 'is' . $name;
            if (method_exists($this, $getMethod)) {
                $retFieldsArr[$key] = $this->parseField($this->$getMethod());
            } elseif (method_exists($this, $isMethod)) {
                $retFieldsArr[$key] = $this->parseField($this->$isMethod());
            } else {
                $retFieldsArr[$key] = $this->parseField($val);
            }
        }
        return $retFieldsArr;
    }

    protected function parseField($val)
    {
        if (is_array($val)) {
            return $this->parseFields($val);
        } elseif (is_object($val) && method_exists($val, 'getFields')) {
            return $val->getFields();
        } else {
            return $val;
        }
    }

    public function setFields($fields)
    {
        if (!empty($fields)) {
            foreach ($fields as $field => $value) {
                if (is_object($value)) {
                    $value = (array)$value;
                }
                if (!is_object($value)) {
                    $field = explode('_', $field);
                    $field = implode(array_map('ucfirst', $field));
                    $method = 'set' . $field;
                    if (method_exists($this, $method)) {
                        $this->$method($value);
                    }
                }
            }
        }
        return $this;
    }

}