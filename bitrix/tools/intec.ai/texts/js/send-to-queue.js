function sendToQuene(elements, promptMask, iblockProperty) {
    for (var key in elements) {
        $.ajax({
            url: "/bitrix/tools/intec.ai/texts/ajax/send_to_quene.php",
            type: "post",
            data: {
                'id': key,
                'name': elements[key],
                'promptMask': promptMask,
                'iblockProperty': iblockProperty
            },
            dataType: 'json',
            success: function(json) {
                if (json['OK'] == "Y") {
                    let progressDone = $('#gpt-progress .gpt-progress-done');
                    let progressTotal = $('#gpt-progress .gpt-progress-total');
                    let elementsDone = parseInt(progressDone.text()) + 1;
                    let elementsTotal = parseInt(progressTotal.text());
                    progressDone.text(elementsDone);
                    let progressPercent = Math.ceil(elementsDone / elementsTotal * 100);
                    $('#gpt-progress .gpt-progressbar-inner').css('width', progressPercent + '%');
                    if (elementsTotal == elementsDone) $('#gpt-progress .gpt-progress-finish').fadeIn(200);
                } else if ('ERROR' in json) {
                    $('#gpt-progress .gpt-progress-errors').append(function() {
                        return $('<div>', {
                          'text': json['ERROR']
                        })
                    });
                }
            }
        });
    }
}