<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

?>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var currentUrl = new URL(document.location);
        var getParamsUrl = currentUrl.searchParams;

        BX.message({
            C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_COUNT: '<?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_COUNT') ?>',
            C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_NOT_SELECTED: '<?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_NOT_SELECTED') ?>'
        });

        root.form = $('[data-role="profile.form"]', root);
        root.properties = $('[data-role="property.row"]', root);
        root.ajaxUrl = '<?= CUtil::JSEscape($this->__component->GetPath().'/ajax.php') ?>';
        root.files = $('[data-role="file"]', root);
        root.personTypeChoice = $('[data-role="person.type.choice"]', root);
        root.clear = $('[data-role="clear"]', root);

        root.addCheckbox = function (event) {
            var parentInput = $('[data-role="parent.input"]', event.target.parentNode);
            var newInput = BX.create('div', {
                attrs: {
                    className: 'intec-grid-item-auto'
                },
                children: [
                    BX.create('label', {
                        attrs: {
                            className: 'intec-ui intec-ui-control-checkbox intec-ui-scheme-current intec-ui-size-2'
                        },
                        children: [
                            BX.create('input', {
                                attrs: {
                                    type: 'checkbox',
                                    name: event.target.getAttribute('data-add-name')
                                }
                            }),
                            BX.create('span', {
                                attrs: {
                                    className: 'intec-ui-part-selector'
                                }
                            })
                        ]
                    })
                ]
            });

            parentInput[0].appendChild(newInput);
        };

        root.addInputText = function (event) {
            var newInput = BX.create('input', {
                attrs: {
                    className: 'profile-add-form-field-text',
                    type: 'text',
                    name: event.target.getAttribute('data-add-name')
                }
            });

            event.target.parentNode.insertBefore(newInput, event.target);
        };

        root.addInputTextarea = function (event) {
            var newInput = BX.create('textarea', {
                attrs: {
                    className: 'profile-add-form-field-textarea',
                    name: event.target.getAttribute('data-add-name'),
                    rows: 4,
                    cols: 40
                }
            });

            event.target.parentNode.insertBefore(newInput, event.target);
        };

        root.addInputDate = function (event) {
            var parentInput = $('[data-role="parent.input"]', event.target.parentNode);
            var newInput = BX.create('div', {
                attrs: {
                    className: 'profile-add-form-field-text-date',
                    onclick: 'BX.calendar({node: this, field: this, bTime: true});'
                },
                children: [
                    BX.create('input', {
                        attrs: {
                            className: 'profile-add-form-field-text',
                            type: 'text',
                            name: event.target.getAttribute('data-add-name'),
                            readonly: 'readonly'
                        }
                    }),
                    BX.create('i', {
                        attrs: {
                            className: 'profile-add-form-field-text-date-icon-calendar'
                        }
                    })
                ]
            });

            parentInput[0].appendChild(newInput);
        };

        root.addInputLocation = function (event) {
            var newKey = parseInt(event.target.getAttribute('data-add-last-key')) + 1;
            BX.ajax({
                    method: 'POST',
                    dataType: 'html',
                    url: root.ajaxUrl,
                    data: {
                            sessid: BX.bitrix_sessid(),
                            params: {
                                LOCATION_NAME: event.target.getAttribute('data-add-name'),
                                LOCATION_TEMPLATE: event.target.getAttribute('data-add-template'),
                                LOCATION_KEY: newKey,
                                ACTION: 'getLocationHtml'
                            },
                            signedParamsString: this.signedParams
                    },
                    onsuccess: BX.proxy(function(result) {
                        var wrapper = BX.create('div', {
                            attrs: {
                                className: 'profile-add-form-field-location'
                            }
                        });
                        wrapper.innerHTML = result;
                        event.target.parentNode.insertBefore(wrapper,event.target);
                        event.target.setAttribute('data-add-last-key', newKey)
                    }, this),
                    onfailure: BX.proxy(function() {
                        return this;
                    }, this)
                }, this);
        };

        root.properties.each(function () {
            var buttonAddInput = $('[data-role="add.input"]', $(this));

            buttonAddInput.on('click', function () {
                switch (event.target.getAttribute('data-add-type')) {
                    case 'CHECKBOX' : root.addCheckbox(event);
                        break;
                    case 'TEXT' : root.addInputText(event);
                        break;
                    case 'TEXTAREA' : root.addInputTextarea(event);
                        break;
                    case 'DATE' : root.addInputDate(event);
                        break;
                    case 'LOCATION' : root.addInputLocation(event);
                        break;
                }
            });
        });

        root.files.each(function () {
            var fileInput = $('[data-role="file.load"]', $(this));
            var fileInfo = $('[data-role="file.load.info"]', $(this));
            var fileCancel = $('[data-role="file.load.cancel"]', $(this));
            var fileDefaultDeleteInput = $('[data-role="file.default.delete.input"]', $(this));
            var fileDefaultDelete = $('[data-role="file.default.delete"]', $(this));

            fileInput.on('change', function (event) {
                if (event.target.files.length > 1) {
                    fileInfo[0].innerHTML = BX.message('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_COUNT') + event.target.files.length;
                    fileCancel[0].dataset.active = 'true';
                } else if (event.target.files.length == 1) {
                    var fileName = event.target.files[0].name;

                    if (fileName.length > 40) {
                        fileInfo[0].innerHTML = fileName.substr(0,9) + '...' + fileName.substr(-9);
                    } else {
                        fileInfo[0].innerHTML = event.target.files[0].name;
                    }

                    fileCancel[0].dataset.active = 'true';
                } else {
                    fileCancel[0].dataset.active = 'false';
                    fileInfo[0].innerHTML = BX.message('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_NOT_SELECTED');
                }
            });

            fileCancel.on('click', function () {
                fileCancel[0].dataset.active = 'false';
                fileInfo[0].innerHTML = BX.message('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_NOT_SELECTED');
                fileInput[0].value = '';
            });

            fileDefaultDelete.each(function () {
                $(this).on('click', function (event) {
                    if (fileDefaultDeleteInput[0].value != '') {
                        var removeFilesId = fileDefaultDeleteInput[0].value.split(';');

                        if (removeFilesId.indexOf(event.target.value) === -1) {
                            fileDefaultDeleteInput[0].value = fileDefaultDeleteInput[0].value + ';' + event.target.value;
                        } else {
                            removeFilesId.splice(removeFilesId.indexOf(event.target.value), 1);
                            fileDefaultDeleteInput[0].value = removeFilesId.join(';');
                        }
                    } else {
                        fileDefaultDeleteInput[0].value = event.target.value;
                    }
                });
            });
        });

        root.clear.on('click', function () {
            root.form.find(':input').each(function () {
                if (this.type == 'text' || this.type == 'textarea' || this.type == 'date' || this.type == 'file') {
                    this.value = '';
                } else if (this.type == 'radio' || this.type == 'checkbox') {
                    this.checked = false;
                } else if (this.type == 'select-one' || this.type == 'select-multiple') {
                    this.value = '';
                } else if (this.type == 'hidden' && (this.name == 'filter_date_from' || this.name == 'filter_date_to')) {
                    this.value = '';
                }
            });
            root.form[0].submit();
        });

        if (root.personTypeChoice.length > 1) {
            root.personTypeChoice.on('change', function () {
                if (!!getParamsUrl.get('PERSON_ID')) {
                    getParamsUrl.set('PERSON_ID', $(this).val());
                    currentUrl.searchParams = getParamsUrl;
                    history.pushState(null, null, currentUrl);
                }

                root.form[0].submit();
            });
        }

    }, {
        'name': '[Component] intec.universe:profile.add (.template-1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>