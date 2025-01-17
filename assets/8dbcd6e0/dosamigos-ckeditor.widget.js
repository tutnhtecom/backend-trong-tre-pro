/**
 * @copyright Copyright (c) 2012-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
if (typeof dosamigos == "undefined" || !dosamigos) {
    var dosamigos = {};
}

dosamigos.ckEditorWidget = (function ($) {

    var pub = {
        registerOnChangeHandler: function (id) {
            CKEDITOR && CKEDITOR.instances[id] && CKEDITOR.instances[id].on('change', function () {
                CKEDITOR.instances[id].updateElement();
                $('#' + id).trigger('change');
                return false;
            });
        },
        registerCsrfImageUploadHandler: function () {
            yii & $(document).off('click', '.cke_dialog_tabs a[id^="cke_Upload_"]').on('click', '.cke_dialog_tabs a[id^="cke_Upload_"]', function () {
                var $forms = $('.cke_dialog_ui_input_file iframe').contents().find('form');
                var csrfName = yii.getCsrfParam();
                $forms.each(function () {
                    if (!$(this).find('input[name=' + csrfName + ']').length) {
                        var csrfTokenInput = $('<input/>').attr({
                            'type': 'hidden',
                            'name': csrfName
                        }).val(yii.getCsrfToken());
                        $(this).append(csrfTokenInput);
                    }
                });
            });
        }
    };
    return pub;
})(jQuery);
