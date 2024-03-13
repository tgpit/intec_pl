var JHelpers = {

    letterList: [
        '', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ],

    GetRowLetter: function (number) {

        var result = '';

        var division = Math.trunc(number / 26);
        number = number - (division * 26);

        if (number === 0) {
            division = division - 1;
            number = 26;
        }

        result = result + this.letterList[number];

        if (division > 0) {
            result = this.GetRowLetter(division) + result;
        }

        return result;

    },
    SortBy: function (obj, field) {
        var result = '';
        result =  obj.sort(byField(field));
        return result;
    },
    PercentCalc: function (maxNumber, currentNumber, withSymbol = false) {
        if (withSymbol)
            return (currentNumber / maxNumber) * 100 + '%';
        else
            return (currentNumber / maxNumber) * 100;
    },
    BindResizeEvent: function ()
    {
        $(window).bind('resize', function(){
            JHelpers.SetWidthList();
        });

        BX.addCustomEvent('onAdminMenuResize', function(){
            $(window).trigger('resize');
        });
    },
    SetWidthList: function ()
    {
        console.log('resize');
        $('[data-role="main.scroll"]').each(function(){
            var div = $(this);
            var totalWidth = 0;
            var column = div.find('.tbody').find('.tr-column');
            column = $('.td-column', column[0]);

            $.each(column, function (index, item){
                totalWidth = totalWidth + $(item).outerWidth();
            });


            div.css('width', 0);
            div.prev('[data-role="up.scroll"]').css('width', 0);
            var timer = setInterval(function(){
                var width = div.parent().width();
                if(width > 0)
                {
                    div.css('width', width);
                    div.prev('[data-role="up.scroll"]').css('width', width).find('>div').css('width', totalWidth);

                    clearInterval(timer);
                }
            }, 100);
            setTimeout(function(){clearInterval(timer);}, 3000);
        });
    },
    GetGroupItems: function (groupName, items, selected) {
        var groupItems = [];
        var groupParams;

        $.each(items, function(index,value){
            if (groupName === value.code) {
                groupParams = value;
                return false;
            }
        });

        if (!groupParams)
            return [];

        var i = groupParams.groupItemsId.from;

        for (i; i <= groupParams.groupItemsId.to; i++) {
            if (arrayHas(selected, i))
                continue;

            groupItems.push(items[i])
        }

        return groupItems;
    },
    ShowColumnSettings: function(params, title, mode)
    {
        if (mode !== 'export' && mode !== 'import')
            mode = 'export';

        var dialogParams = {
            'title': title,
            'content_url':'/bitrix/admin/intec_importexport_' + mode + '_templates_column_settings.php?lang='+BX.message('LANGUAGE_ID'),
            'content_post': params,
            'width': '1050',
            'height': '420',
            'resizable':true
        };

        var dialog = new BX.CAdminDialog(dialogParams);

        dialog.SetButtons([
            dialog.btnCancel,
            new BX.CWindowButton(
                {
                    title: BX.message('JS_CORE_WINDOW_SAVE'),
                    id: 'savebtn',
                    name: 'savebtn',
                    className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
                    action: function () {
                        this.disableUntilError();
                        this.parentWindow.PostParameters();
                    }
                })
        ]);

        dialog.Show();
    },

    ShowCron: function(params, title, mode)
    {
        if (mode !== 'export' && mode !== 'import')
            mode = 'export';

        var dialog = new BX.CAdminDialog({
            'title': title,
            'content_url':'/bitrix/admin/intec_importexport_' + mode + '_cron_settings.php?lang='+BX.message('LANGUAGE_ID'),
            'content_post': params,
            'width': '1050',
            'height': '420',
            'resizable':true
        });

        dialog.SetButtons([
            dialog.btnCancel
        ]);

        dialog.Show();
    },

    SetExtraParams: function(id, data)
    {
        if(typeof data == 'object')
            data = JSON.stringify(data);

        //$('#settings_'+ id).val(data);

        if(BX.WindowManager.Get())
        {
            BX.WindowManager.Get().Close();
        }

        window.page.setSettings(id, data);
    },
    SetCreatedProperty: function(data)
    {
        if(typeof data == 'object')
            data = JSON.stringify(data);

        //$('#settings_'+ id).val(data);

        if(BX.WindowManager.Get())
        {
            BX.WindowManager.Get().Close();
        }

        window.page.setCreatedProperty(data);
    },
    ShowChooseVal: function(btn, arLines, selector)
    {
        var param = {
            'active_class': ''
        };

        $(selector).each(function () {
            $(this).removeClass('selected');
        });

        $(btn).addClass('selected');

        BX.adminShowMenu(btn, arLines, param);
    },
    applySettings: function (item, columnSetting, settings, delimiter)
    {
        var res = null;
        var offers = '';

        if (!!item[columnSetting.selected]) {
            try {
                offers = item[columnSetting.selected].split(delimiter);
            } catch (e) {
                offers = '';
            }
        }


        if (offers.constructor === Array) {
            var subRes = null;
            var thenValue;
            res = [];

            offers.forEach(function (offer) {
                settings.forEach(function (setting) {
                    if (setting.field === 'CURRENT') {
                        if (!subRes && subRes !== 0 && subRes !== false)
                            subRes = offer;

                        if (conditionsHelper[setting.when](subRes, setting.whenValue)) {
                            if (!subRes && subRes !== 0 && subRes !== false)
                                subRes = offer;

                            thenValue = JHelpers.ReplaceMacros(setting.thenValue, item);

                            subRes = conditionsActionHelper[setting.then](subRes, thenValue, setting.whenValue);
                        }
                    } else {
                        if (!subRes && subRes !== 0 && subRes !== false)
                            subRes = offer;

                        if (conditionsHelper[setting.when](item[setting.field], setting.whenValue)) {
                            if (!subRes && subRes !== 0 && subRes !== false)
                                subRes = offer;

                            thenValue = JHelpers.ReplaceMacros(setting.thenValue, item);

                            subRes = conditionsActionHelper[setting.then](subRes, thenValue, setting.whenValue);
                        }
                    }
                });

                res.push(subRes);
                subRes = null;
            });

            res = res.join(delimiter);
        } else {
            settings.forEach(function (setting) {
                var field = setting.field;

                if (field === 'CURRENT')
                    field = columnSetting.selected;

                if (!res && res !== 0 && res !== false)
                    res = item[columnSetting.selected];


                if (conditionsHelper[setting.when](item[field], setting.whenValue)) {
                    if (!res && res !== 0 && res !== false)
                        res = item[columnSetting.selected];

                    var thenValue = JHelpers.ReplaceMacros(setting.thenValue, item);

                    res  = conditionsActionHelper[setting.then](res, thenValue, setting.whenValue);

                }
            });
        }

        return res;
    },
    applyImportSettings: function (item, items, currentIndex, columnSetting, settings, delimiter)
    {
        var res = null;
        var offers = '';

        if (!!item) {
            try {
                offers = item.split(delimiter);
            } catch (e) {
                offers = '';
            }
        }

        if (offers.constructor === Array) {
            var subRes = null;
            var thenValue;
            res = [];

            offers.forEach(function (offer) {
                settings.forEach(function (setting) {
                    if (setting.field === 'CURRENT') {
                        if (!subRes && subRes !== 0 && subRes !== false)
                            subRes = offer;

                        if (conditionsHelper[setting.when](subRes, setting.whenValue)) {
                            if (!subRes && subRes !== 0 && subRes !== false)
                                subRes = offer;

                            thenValue = JHelpers.ReplaceMacros(setting.thenValue, items, true);

                            subRes = conditionsActionHelper[setting.then](subRes, thenValue, setting.whenValue);
                        }
                    } else {
                        if (!subRes && subRes !== 0 && subRes !== false)
                            subRes = offer;

                        if (conditionsHelper[setting.when](items[setting.field], setting.whenValue)) {
                            if (!subRes && subRes !== 0 && subRes !== false)
                                subRes = offer;

                            thenValue = JHelpers.ReplaceMacros(setting.thenValue, items, true);

                            subRes = conditionsActionHelper[setting.then](subRes, thenValue, setting.whenValue);
                        }
                    }
                });

                res.push(subRes);
                subRes = null;
            });

            res = res.join(delimiter);
        } else {
            settings.forEach(function (setting) {
                var field = setting.field;
                var itemUse;

                if (field === 'CURRENT')
                    field = currentIndex;

                if (setting.field === 'CURRENT' && (!res && res !== 0)) {
                    itemUse = items[currentIndex];
                } else if (setting.field === 'CURRENT') {
                    itemUse = res;
                } else {
                    itemUse = items[field];
                }

                if (!res && res !== 0 && res !== false)
                    res = items[currentIndex];

                if (conditionsHelper[setting.when](itemUse, setting.whenValue)) {
                    if (!res && res !== 0 && res !== false)
                        res = items[currentIndex];

                    var thenValue = JHelpers.ReplaceMacros(setting.thenValue, items, true);

                    res  = conditionsActionHelper[setting.then](res, thenValue, setting.whenValue);
                }

            });
        }

        return res;
    },
    CreateMacrosValues: function (values, prefix) {
        var result = [];

        $.each(function (index, value) {

        });

        return
    },
    ReplaceMacros: function (string, values, isImport)
    {
        var subString = {
            'status': false,
            'value': ''
        };

        if (!isImport)
            isImport = false;

        var limiters = '#';
        var result = string;

        string = string.split('');

        $.each(string, function (index, letter) {
            if (letter === limiters) {
                subString.status = !subString.status;

                if (!subString.status) {
                    var reg = new RegExp(limiters + subString.value + limiters, '');

                    if (!isImport) {
                        if (!values[subString.value])
                            result = result + limiters + subString.value + limiters;
                        else
                            result = result.replace(reg, values[subString.value]);
                    } else {
                        subString.value = subString.value.replace('CELL_', '');

                        if (!values[subString.value])
                            result = result + limiters + subString.value + limiters;
                        else
                            result = result.replace(reg, values[subString.value]);
                    }

                    subString.value = '';
                }

                return;
            }

            if (subString.status) {
                subString.value = subString.value + letter;
            }
        });

        return result;
    },

    ShowCreateProperty: function(params, title)
    {
        var dialogParams = {
            'title': title,
            'content_url':'/bitrix/admin/intec_importexport_import_templates_create_property.php?lang='+BX.message('LANGUAGE_ID'),
            'content_post': params,
            'width': '700',
            'height': '600',
            'resizable':true
        };

        var dialog = new BX.CAdminDialog(dialogParams);

        dialog.SetButtons([
            dialog.btnCancel,
            new BX.CWindowButton(
                {
                    title: BX.message('JS_CORE_WINDOW_SAVE'),
                    id: 'savebtn',
                    name: 'savebtn',
                    className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
                    action: function () {
                        this.disableUntilError();
                        this.parentWindow.PostParameters();
                    }
                })
        ]);

        BX.addCustomEvent(dialog, 'onWindowRegister', function(){
            $('input[type=checkbox]', this.DIV).each(function(){
                BX.adminFormTools.modifyCheckbox(this);
            });
            setTimeout(function(){
                JHelpers.BindConversionEvents();
            }, 500);
        });

        dialog.Show();
    }
};

/*
* a - значение в ячейке
* b - значение в настройках
*/
var conditionsHelper = {
    equal: function (a, b) {
        a = parseFloat(a);
        b = parseFloat(b);

        if (!a || !b)
            return false;

        return a == b;
    },
    nequal: function (a, b) {
        a = parseFloat(a);
        b = parseFloat(b);

        if (!a || !b)
            return false;

        return a != b;
    },
    more: function (a, b) {
        a = parseFloat(a);
        b = parseFloat(b);

        if (!a || !b)
            return false;

        return a > b;
    },
    less: function (a, b) {
        a = parseFloat(a);
        b = parseFloat(b);

        if (!a || !b)
            return false;

        return a < b;
    },
    moreq: function (a, b) {
        a = parseFloat(a);
        b = parseFloat(b);

        if (!a || !b)
            return false;

        return a >= b;
    },
    loreq: function (a, b) {
        a = parseFloat(a);
        b = parseFloat(b);

        if (!a || !b)
            return false;

        return a <= b;
    },
    between: function (a, b) {
        var values = b.split('-');
        var min, max;

        values.forEach(function (item) {
            item = parseInt(item);

            if (item <= min || (!min && min !== 0))
                min = item;

            if (item >= max || (!max && max !== 0))
                max = item;
        });

        return a >= min && a <= max;
    },
    substring: function (a, b) {
        return a.indexOf(b) >= 0;
    },
    nsubstring: function (a, b) {
        return a.indexOf(b) < 0;
    },
    empty: function (a) {
        return !a && a !== 0 && a !== false;
    },
    nempty: function (a) {
        return !(!a && a !== 0 && a !== false);
    },
    regularexp: function (a, b) {
        var reg = new RegExp(b); //fix regular expire. php and js expire not equal.

        return a.search(reg) >= 0;
    },
    nregularexp: function (a, b) {
        var reg = new RegExp(b); //fix regular expire. php and js expire not equal.

        return a.search(reg) < 0;
    },
    any: function () {
        return true;
    }
};

/*
* a - значение в ячейке
* b - значение в настройках
* substr - подстрока
*/
var conditionsActionHelper = {
    replaceto: function (a, b) {
        return b;
    },
    removesubs: function (a, b) {
        if (!a)
            return '';

        var reg = new RegExp(b, 'g');
        return a.replace(reg, '');
    },
    replacesubsto: function (a, b, substr) {
        if (!substr && substr !== 0 && substr !== false)
            return b;

        var reg = new RegExp(substr, 'g');

        return a.replace(reg, b);
    },
    addtobegin: function (a, b) {
        if (!a)
            return b;

        return b + a;
    },
    addtoend: function (a, b) {
        if (!a)
            return b;

        return a + b;
    },
    translit: function (a) {
        return BX.translit(a);
        //return BX.translit(decodeURIComponent(unicodeToWin1251_UrlEncoded(a)));
    },
    striptags: function (a) {
        return a.replace(/(<([^>]+)>)/gi, "");
    },
    cleartags: function (a, b) {
        //finish later
    },
    round: function (a) {
        a = parseFloat(a.replace(/,/g, '.'));

        if (!a && a !== false)
            a = 0;

        return Math.round(a);
    },
    multiply: function (a, b) {
        a = parseFloat(a.replace(/,/g, '.'));
        b = parseFloat(b.replace(/,/g, '.'));

        if (!a && a !== false)
            a = 0;

        if (!b && b !== false)
            b = 0;

        return a * b;
    },
    divide: function (a, b) {
        a = parseFloat(a.replace(/,/g, '.'));
        b = parseFloat(b.replace(/,/g, '.'));

        if (!a && a !== false)
            a = 0;

        if (!b && b !== false)
            b = 0;

        return a / b;
    },
    add: function (a, b) {
        a = parseFloat(a.replace(/,/g, '.'));
        b = parseFloat(b.replace(/,/g, '.'));

        if (!a && a !== false)
            a = 0;

        if (!b && b !== false)
            b = 0;

        return a + b;
    },
    subtract: function (a, b) {
        a = parseFloat(a.replace(/,/g, '.'));
        b = parseFloat(b.replace(/,/g, '.'));

        if (!a && a !== false)
            a = 0;

        if (!b && b !== false)
            b = 0;

        return a - b;
    },
    removefromfile: function (a, b) {

    },
    setbg: function (a, b) {
        //finish later
    },
    settext: function (a, b) {
        //finish later
    },
    addlink: function (a, b) {
        //finish later
    },
    php: function (a, b) {
        //finish later
    }
};

function arrayHas(arr, el) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] === el)
            return true;
    }

    return false;
}

function byField(field) {
    return (a, b) => a[field] > b[field] ? 1 : -1;
}
