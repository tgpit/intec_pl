<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var messages = root.find('[data-role="messages"]');
        var messageQoute = messages.find('[data-role="quote"]');
        var formClaim = root.find('[data-role="form"]');
        var insertTag = formClaim.find('[data-role="insertTag"]');
        var translitButton = formClaim.find('[data-role="translit"]');
        var uploadButton = formClaim.find('[data-role="upload"]');

        function SupQuoteMessage(id) {
            var selection;

            if (document.getSelection) {
                var selection = "" + document.getSelection();
                selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_");
                selection = selection.replace(/\r\n/gi, " ");
                selection = selection.replace(/  /gi, "");
                selection = selection.replace(/_newstringhere_/gi, "\r\n\r\n");
            } else {
                selection = document.selection.createRange().text;
            }

            if (selection!="") {
                document.forms["support_edit"].elements["MESSAGE"].value += "<QUOTE>"+selection+"</QUOTE>\n";
            } else {
                var el = document.getElementById(id);
                var textData = (el.innerText) ? el.innerText : el.textContent;

                if(el) {
                    var str = textData
                    str = str.replace(/\r\n\r\n/gi, "_newstringhere_");
                    str = str.replace(/\r\n/gi, " ");
                    str = str.replace(/<br[^>]*>/gi, "");
                    str = str.replace(/<\/p[^>]*>/gi, "\r\n");
                    str = str.replace(/<li[^>]*>/gi, "\r\n");
                    str = str.replace(/<[^>]*>/gi, " ");
                    str = str.replace(/  /gi, "");
                    str = str.replace(/_newstringhere_/gi, "\r\n");
                    document.forms["support_edit"].elements["MESSAGE"].value += "<QUOTE>"+str+"</QUOTE>\n";
                }
            }
        }

        var QUOTE_open = 0;
        var CODE_open = 0;
        var B_open = 0;
        var I_open = 0;
        var U_open = 0;
        var myAgent   = navigator.userAgent.toLowerCase();
        var myVersion = parseInt(navigator.appVersion);
        var myVersion = parseInt(navigator.appVersion);
        var is_ie  = ((myAgent.indexOf("msie") != -1)  && (myAgent.indexOf("opera") == -1));
        var is_nav = ((myAgent.indexOf('mozilla')!=-1) && (myAgent.indexOf('spoofer')==-1)
            && (myAgent.indexOf('compatible') == -1) && (myAgent.indexOf('opera')==-1)
            && (myAgent.indexOf('webtv')==-1) && (myAgent.indexOf('hotjava')==-1));
        var is_win = ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));
        var is_mac = (myAgent.indexOf("mac")!=-1);

        function insert_tag(thetag, objTextarea) {
            var tagOpen = eval(thetag + "_open");

            if (tagOpen == 0) {
                if (DoInsert(objTextarea, "<"+thetag+">", "</"+thetag+">")) {
                    eval(thetag + "_open = 1");
                    eval("document.forms['support_edit'].elements['"+thetag+"'].value += '*'");
                }
            } else {
                DoInsert(objTextarea, "</"+thetag+">", "");
                var buttonText = eval("document.forms['support_edit'].elements['"+thetag+"'].value");
                eval("document.forms['support_edit'].elements['"+thetag+"'].value = '"+(buttonText.slice(0,-1))+"'");
                eval(thetag + "_open = 0");
            }
        }

        function mozillaWr(textarea, open, close) {
            var selLength = textarea.textLength;
            var selStart = textarea.selectionStart;
            var selEnd = textarea.selectionEnd;

            if (selEnd == 1 || selEnd == 2)
                selEnd = selLength;

            var s1 = (textarea.value).substring(0,selStart);
            var s2 = (textarea.value).substring(selStart, selEnd);
            var s3 = (textarea.value).substring(selEnd, selLength);
            textarea.value = s1 + open + s2 + close + s3;
            textarea.selectionEnd = 0;
            textarea.selectionStart = selEnd + open.length + close.length;
            return;
        }

        function DoInsert(objTextarea, Tag, closeTag) {
            var isOpen = false;

            if ( myVersion >= 4 && is_ie && is_win && objTextarea.isTextEdit) {
                objTextarea.focus();
                var sel = document.selection;
                var rng = sel.createRange();
                rng.colapse;

                if ((sel.type=="Text" || sel.type=="None") && rng != null) {
                    if (closeTag!="") {
                        if (rng.text.length > 0) {
                            Tag += rng.text + closeTag;
                        } else {
                            isOpen = true;
                        }
                    }

                    rng.text = Tag;
                }
            } else {
                if (is_nav && document.getElementById) {
                    if (closeTag!="" && objTextarea.selectionEnd > objTextarea.selectionStart) {
                        mozillaWr(objTextarea, Tag, closeTag);
                        isOpen = false;
                    } else {
                        mozillaWr(objTextarea, Tag, '');
                        isOpen = true;
                    }
                } else {
                    objTextarea.value += Tag;
                    isOpen = true;
                }
            }

            objTextarea.focus();
            return isOpen;
        }

        var TRANSLIT_way = 0;
        var smallEngLettersReg = new Array(/e'/g, /ch/g, /sh/g, /yo/g, /jo/g, /zh/g, /yu/g, /ju/g, /ya/g, /ja/g, /a/g, /b/g, /v/g, /g/g, /d/g, /e/g, /z/g, /i/g, /j/g, /k/g, /l/g, /m/g, /n/g, /o/g, /p/g, /r/g, /s/g, /t/g, /u/g, /f/g, /h/g, /c/g, /w/g, /~/g, /y/g, /'/g);
        var capitEngLettersReg = new Array( /E'/g, /CH/g, /SH/g, /YO/g, /JO/g, /ZH/g, /YU/g, /JU/g, /YA/g, /JA/g, /A/g, /B/g, /V/g, /G/g, /D/g, /E/g, /Z/g, /I/g, /J/g, /K/g, /L/g, /M/g, /N/g, /O/g, /P/g, /R/g, /S/g, /T/g, /U/g, /F/g, /H/g, /C/g, /W/g, /~/g, /Y/g, /'/g);
        var smallRusLetters = new Array("ý", "÷", "ø", "¸", "¸", "æ", "þ", "þ", "ÿ", "ÿ", "à", "á", "â", "ã", "ä", "å", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ù", "ú", "û", "ü");
        var capitRusLetters = new Array( "Ý", "×", "Ø", "¨", "¨", "Æ", "Þ", "Þ", "\ß", "\ß", "À", "Á", "Â", "Ã", "Ä", "Å", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ù", "Ú", "Û", "Ü");
        var smallEngLetters = new Array("e", "ch", "sh", "yo", "jo", "zh", "yu", "ju", "ya", "ja", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "w", "~", "y", "'");
        var capitEngLetters = new Array("E", "CH", "SH", "YO", "JO", "ZH", "YU", "JU", "YA", "JA", "A", "B", "V", "G", "D", "E", "Z", "I", "J", "K", "L", "M", "N", "O", "P", "R", "S", "T", "U", "F", "H", "C", "W", "~", "Y", "'");
        var smallRusLettersReg = new Array(/ý/g, /÷/g, /ø/g, /¸/g, /¸/g,/æ/g, /þ/g, /þ/g, /ÿ/g, /ÿ/g, /à/g, /á/g, /â/g, /ã/g, /ä/g, /å/g, /ç/g, /è/g, /é/g, /ê/g, /ë/g, /ì/g, /í/g, /î/g, /ï/g, /ð/g, /ñ/g, /ò/g, /ó/g, /ô/g, /õ/g, /ö/g, /ù/g, /ú/g, /û/g, /ü/ );
        var capitRusLettersReg = new Array(/Ý/g, /×/g, /Ø/g, /¨/g, /¨/g, /Æ/g, /Þ/g, /Þ/g, /ß/g, /ß/g, /À/g, /Á/g, /Â/g, /Ã/g, /Ä/g, /Å/g, /Ç/g, /È/g, /É/g, /Ê/g, /Ë/g, /Ì/g, /Í/g, /Î/g, /Ï/g, /Ð/g, /Ñ/g, /Ò/g, /Ó/g, /Ô/g, /Õ/g, /Ö/g, /Ù/g, /Ú/g, /Û/g, /Ü/);

        function translit(objTextarea) {
            var i;
            var textbody = objTextarea.value;
            var selected = false;

            if (objTextarea.isTextEdit) {
                objTextarea.focus();
                var sel = document.selection;
                var rng = sel.createRange();
                rng.colapse;

                if (sel.type=="Text" && rng != null) {
                    textbody = rng.text;
                    selected = true;
                }
            }

            if (textbody) {
                if (TRANSLIT_way==0) {
                    for (i=0; i<smallEngLettersReg.length; i++) textbody = textbody.replace(smallEngLettersReg[i], smallRusLetters[i]);
                    for (i=0; i<capitEngLettersReg.length; i++) textbody = textbody.replace(capitEngLettersReg[i], capitRusLetters[i]);
                } else {
                    for (i=0; i<smallRusLetters.length; i++) textbody = textbody.replace(smallRusLettersReg[i], smallEngLetters[i]);
                    for (i=0; i<capitRusLetters.length; i++) textbody = textbody.replace(capitRusLettersReg[i], capitEngLetters[i]);
                }

                if (!selected) {
                    objTextarea.value = textbody;
                } else {
                    rng.text = textbody;
                }
            }

            if (TRANSLIT_way==0) {
                document.forms['support_edit'].elements['TRANSLIT'].value += " *";
                TRANSLIT_way = 1;
            } else {
                document.forms['support_edit'].elements['TRANSLIT'].value = document.forms['support_edit'].elements['TRANSLIT'].value.slice(0,-2);
                TRANSLIT_way = 0;
            }

            objTextarea.focus();
        }

        messageQoute.on('click', function () {
            SupQuoteMessage($(this)[0].dataset.id);
        });

        insertTag.on('click', function () {
            insert_tag($(this)[0].dataset.type, document.forms['support_edit'].elements['MESSAGE']);
        });

        translitButton.on('click', function () {
            translit(document.forms['support_edit'].elements['MESSAGE']);
        });

        uploadButton.on('change', function () {
            var labelFile = formClaim.find('[for="' + $(this)[0].id + '"]');

            if ($(this).length !== 0) {
                labelFile.html('<?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_FILE_UPLOADED') ?>');
            } else {
                labelFile.html('<?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_FILE') ?>');
            }
        });

        formClaim.on('reset', function () {
            var labelFile = formClaim.find('[for="' + uploadButton[0].id + '"]');
            labelFile.html('<?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_FILE') ?>');
        });

    }, {
        'name': '[Component] bitrix:support.ticket.edit (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
