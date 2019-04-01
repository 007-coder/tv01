$(document).ready(function(){

    $('.custom-control-input.protectiusChbx').each(function() {
        var inputType = $( this ).attr('data-input-type');
        var inputId = $( this ).attr('data-input-id');

            $( this ).click(function(){

                if (inputType == 'boolean') {
                    var targetTrue = $('input#'+ inputId +'_true');
                    var targetFalse = $('input#'+ inputId +'_false');
                    var targetDataType = $('input#'+ inputId +'_hiddenDataType');

                    if (
                        targetTrue.hasClass('disabled')
                        || targetFalse.hasClass('disabled')
                        || targetDataType.hasClass('disabled')
                    ) {
                        targetTrue.prop("disabled", false);
                        targetFalse.prop("disabled", false);
                        targetDataType.prop("disabled", false);

                        targetTrue.prop("readonly", false);
                        targetFalse.prop("readonly", false);
                        targetDataType.prop("readonly", false);

                        targetTrue.removeClass('disabled');
                        targetFalse.removeClass('disabled');
                        targetDataType.removeClass('disabled');

                        targetTrue.removeClass('readonly');
                        targetFalse.removeClass('readonly');
                        targetDataType.removeClass('readonly');

                        targetTrue.removeAttr('readonly');
                        targetFalse.removeAttr('readonly');
                        targetDataType.removeAttr('readonly');

                        targetTrue.removeAttr('disabled');
                        targetFalse.removeAttr('disabled');
                        targetDataType.removeAttr('disabled');
                    } else {
                        targetTrue.prop("disabled", true);
                        targetFalse.prop("disabled", true);
                        targetDataType.prop("disabled", true);

                        targetTrue.prop("readonly", true);
                        targetFalse.prop("readonly", true);
                        targetDataType.prop("readonly", true);

                        targetTrue.attr('disabled','disabled');
                        targetFalse.attr('disabled','disabled');
                        targetDataType.attr('disabled','disabled');

                        targetTrue.attr('readonly','readonly');
                        targetFalse.attr('readonly','readonly');
                        targetDataType.attr('readonly','readonly');

                        targetTrue.addClass('readonly');
                        targetFalse.addClass('readonly');
                        targetDataType.addClass('readonly');

                        targetTrue.addClass('disabled');
                        targetFalse.addClass('disabled');
                        targetDataType.addClass('disabled');


                    }

                } else {
                    var target = $('input#'+ inputId);
                    var targetDataType = $('input#'+ inputId +'_hiddenDataType');

                    if (target.hasClass('disabled'))
                    {
                        target.prop("disabled", false);
                        target.prop("readonly", false);
                        targetDataType.prop("disabled", false);
                        targetDataType.prop("readonly", false);

                        target.removeAttr('readonly');
                        target.removeAttr('disabled');
                        targetDataType.removeAttr('readonly');
                        targetDataType.removeAttr('disabled');

                        target.removeClass('disabled');
                        target.removeClass('readonly');
                        targetDataType.removeClass('disabled');
                        targetDataType.removeClass('readonly');

                    } else {
                        target.prop("disabled", true);
                        target.prop("readonly", true);
                        targetDataType.prop("disabled", true);
                        targetDataType.prop("readonly", true);

                        target.attr('readonly','readonly');
                        target.attr('readonly','readonly');
                        targetDataType.attr('readonly','readonly');
                        targetDataType.attr('readonly','readonly');

                        target.addClass('disabled');
                        target.addClass('readonly');
                        targetDataType.addClass('disabled');
                        targetDataType.addClass('readonly');
                    }
                }
            });
        }

    );


});