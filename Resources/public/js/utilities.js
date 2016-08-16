/**
 * Archivo de funciones comunes utilizadas en los formularios de la aplicacion
 */
$('.upper-case').keyup(function () {
    var text = $(this).val();
    text = text.toUpperCase();
    $(this).val(text);
});

$('.lower-case').keyup(function () {
    var text = $(this).val();
    text = text.toLowerCase();
    $(this).val(text);
});

$('.title-case').keyup(function () {
    var text = $(this).val();
    text = text.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1);
    });
    $(this).val(text);
});

/*
 * Clase para validar solo numeros en el evento key down
 */
$(document).on("keydown", '.only_numbers', function (e)
{
    var key;
    if (window.event) {
        key = window.event.keyCode;   /*IE*/
    } else {
        key = e.which;                /*firefox*/
    }
    if (!((key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 8 || key == 9 || key == 0 || key == 46 || key == 59)) {
        return false;
    }
    else {
        return true;
    }
});

/**
 * Comas para todos los campos numericos
 */
$(document).on("keyup", '.input_number', function ()
{
    var val = $(this).val();
    val = val.replace(/[\D]+/g, "");
    val = val.replace(/$0+/g, "");
    if (val.length > 0) {
        $(this).val($.number(val));
    }
});

function showFlashSuccessMessage(msg, hide, timeToHide) {
    $("#flash-message-success span").html(msg);
    $("#flash-message-success").show(10, function () {
        if (hide) {
            $("#flash-message-success").fadeOut(timeToHide);
        }
    });
}

function showFlashWarningMessage(msg, hide, timeToHide) {
    $("#flash-message-warning span").html(msg);
    $("#flash-message-warning").show(10, function () {
        if (hide) {
            $("#flash-message-warning").fadeOut(timeToHide);
        }
    });
}

function showFlashErrorMessage(msg, hide, timeToHide) {
    $("#flash-message-danger span").html(msg);
    $("#flash-message-danger").show(10, function () {
        if (hide) {
            $("#flash-message-danger").fadeOut(timeToHide);
        }
    });
}

function tynyMceToElement(element, height) {
    tinymce.init({
        selector: element,
        height: height,
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
        ],
        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",
        menubar: false,
        toolbar_items_size: 'small',
        style_formats: [{
                title: 'Bold text',
                inline: 'b'
            }, {
                title: 'Red text',
                inline: 'span',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Red header',
                block: 'h1',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Example 1',
                inline: 'span',
                classes: 'example1'
            }, {
                title: 'Example 2',
                inline: 'span',
                classes: 'example2'
            }, {
                title: 'Table styles'
            }, {
                title: 'Table row 1',
                selector: 'tr',
                classes: 'tablerow1'
            }],
        templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            }, {
                title: 'Test template 2',
                content: 'Test 2'
            }],
        content_css: [
            '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
            '//www.tinymce.com/css/codepen.min.css'
        ]
    });

}


