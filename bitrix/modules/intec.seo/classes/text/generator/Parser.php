<?php
namespace intec\seo\text\generator;

use Bitrix\Main\Localization\Loc;
use intec\core\base\BaseObject;
use intec\core\helpers\Type;
use intec\seo\text\generator\tokens\Group;
use intec\seo\text\generator\tokens\GroupItem;
use intec\seo\text\generator\tokens\Macro;
use intec\seo\text\generator\tokens\Optional;
use intec\seo\text\generator\tokens\Text;

Loc::loadMessages(__FILE__);

/**
 * Класс для разбора текста.
 * Class Parser
 * @package intec\seo\text\generator
 * @author apocalypsisdimon@gmail.com
 */
class Parser extends BaseObject
{
    /**
     * Разбирает текстовое выражение на токены.
     * @param string $text Текстовое выражение.
     * @return Tokens
     * @throws ParserException
     */
    public static function process($text)
    {
        $result = new Tokens();
        $text = Type::toString($text);
        $characters = str_split($text);
        $length = count($characters);
        $position = 0;
        $group = 0;
        $optional = 0;
        $macro = 0;

        if ($length == 0)
            return $result;

        $parse = function ($context = null) use (&$parse, &$parseGroup, &$parseOptional, &$parseMacro, &$characters, &$position, &$group, &$optional, &$macro, $length) {
            $result = [];
            $buffer = '';
            $escapes = 0;

            $flush = function () use (&$buffer, &$result) {
                if (!empty($buffer)) {
                    $result[] = new Text([
                        'content' => $buffer
                    ]);

                    $buffer = '';
                }
            };

            while (true) {
                $append = $position < $length;
                $move = true;
                $character = null;
                $escaped = $escapes !== 0 && $escapes % 2 === 1;

                if ($append)
                    $character = $characters[$position];

                if ($character === '\\') {
                    $append = false;
                    $escapes++;
                } else if ($escapes > 0) {
                    $buffer .= str_repeat('\\', floor(Type::toFloat($escapes) / 2));
                    $escapes = 0;
                }

                if (!$escaped) {
                    if ($character === '{') {
                        $flush();
                        $append = false;
                        $move = false;
                        $position++;
                        $result[] = $parseGroup();
                    } else if ($character === '}') {
                        if ($context instanceof Group) {
                            $flush();

                            return new GroupItem([
                                'tokens' => $result
                            ]);
                        } else {
                            throw new ParserException(Loc::getMessage('intec.seo.text.generator.parser.errors.groupClose', [
                                '#POSITION#' => $position
                            ]));
                        }
                    } else if ($character === '|') {
                        if ($context instanceof Group) {
                            $flush();

                            return new GroupItem([
                                'tokens' => $result
                            ]);
                        } else {
                            throw new ParserException(Loc::getMessage('intec.seo.text.generator.parser.errors.groupDelimiter', [
                                '#POSITION#' => $position
                            ]));
                        }
                    } else if ($character === '[') {
                        $flush();
                        $append = false;
                        $move = false;
                        $position++;
                        $result[] = $parseOptional();
                    } else if ($character === ']') {
                        if ($context instanceof Optional) {
                            $flush();
                            return $result;
                        } else {
                            throw new ParserException(Loc::getMessage('intec.seo.text.generator.parser.errors.optionalClose', [
                                '#POSITION#' => $position
                            ]));
                        }
                    } else if ($character === '#') {
                        if ($context instanceof Macro) {
                            $flush();
                            return $result;
                        } else {
                            $flush();
                            $append = false;
                            $move = false;
                            $position++;
                            $result[] = $parseMacro();
                        }
                    }
                }

                if ($append)
                    $buffer .= $character;

                if ($position >= $length) {
                    $flush();
                    break;
                }

                if ($move)
                    $position++;
            }

            if ($context !== null)
                if ($context instanceof Group) {
                    throw new ParserException(Loc::getMessage('intec.seo.text.generator.parser.errors.groupEnd', [
                        '#POSITION#' => $position,
                        '#GROUP#' => $group
                    ]));
                } else if ($context instanceof Optional) {
                    throw new ParserException(Loc::getMessage('intec.seo.text.generator.parser.errors.optionalEnd', [
                        '#POSITION#' => $position,
                        '#OPTIONAL#' => $optional
                    ]));
                } else if ($context instanceof Macro) {
                    throw new ParserException(Loc::getMessage('intec.seo.text.generator.parser.errors.macroEnd', [
                        '#POSITION#' => $position,
                        '#MACRO#' => $macro
                    ]));
                }

            return $result;
        };

        $parseGroup = function () use (&$parse, &$characters, &$position, &$group, $length) {
            $result = new Group();
            $group++;

            while (true) {
                $result->getItems()->add($parse($result));
                $character = $characters[$position];
                $position++;

                if ($character === '}')
                    break;
            }

            return $result;
        };

        $parseOptional = function () use (&$parse, &$position, &$optional, $length) {
            $result = new Optional();
            $optional++;
            $result->setTokens($parse($result));
            $position++;

            return $result;
        };

        $parseMacro = function () use (&$parse, &$position, &$macro, $length) {
            $result = new Macro();
            $macro++;
            $result->setTokens($parse($result));
            $position++;

            return $result;
        };

        $result->setRange($parse());

        return $result;
    }
}