<?php
    require '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Wijzigen</title>

    <link rel="stylesheet" href="../css/main.min.css" />
    <link rel="stylesheet" href="../css/edit.min.css" />
    <link rel="stylesheet" href="../fonts/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../js/jquery-imgareaselect/css/imgareaselect-default.css" />

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

    <meta name="robots" content="noindex, nofollow" />
</head>
<body>
    <div id="fm-menu-container" class="fm-box fm-gradient">
        <div id="fm-menu">

            <button name="save"
                    type="button"
                    class="fm-button fm-button-left fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-floppy-o"></span>
                Opslaan
            </button>
            <button name="reset"
                    type="button"
                    class="fm-button fm-button-left fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-undo"></span>
                Resetten
            </button>

            <div class="fm-button-right">
                <span class="fm-zoom-label">Zoom:</span>
                <span id="fm-zoom-percentage" class="fm-zoom-label">100%</span>
                <input name="zoom"
                    type="range"
                    min="5"
                    max="700"
                    step="5"
                    value="100"
                    data-orientation="horizontal"
                    class="fm-button fm-button-right fm-button-lonely" />
            </div>

        </div>
    </div>

    <div id="fm-body-container" class="fm-gradient">

        <div id="fm-sidebar" class="fm-box">
            <div class="fm-sidebar-item-heading">Wijzigen</div>
            <div id="alter-size" class="fm-sidebar-item">Formaat</div>
            <div id="alter-crop" class="fm-sidebar-item">Bijsnijden</div>
            <div id="alter-flip-rotate" class="fm-sidebar-item">Spiegelen/Draaien</div>

            <div class="fm-sidebar-item-heading">Filters</div>
            <div id="filter-brightness" class="fm-sidebar-item">Helderheid</div>
            <div id="filter-contrast" class="fm-sidebar-item">Contrast</div>
            <div id="filter-exposure" class="fm-sidebar-item">Belichting</div>
            <div id="filter-gamma" class="fm-sidebar-item">Gamma</div>
            <div id="filter-hue" class="fm-sidebar-item">Tint</div>
            <div id="filter-saturate" class="fm-sidebar-item">Verzadigen</div>
            <div id="filter-sepia" class="fm-sidebar-item">Sepia</div>
            <div id="filter-vibrance" class="fm-sidebar-item">Levendigheid</div>
            <div id="filter-blur" class="fm-sidebar-item">Blur</div>
            <div id="filter-colorize" class="fm-sidebar-item">Inkleuren</div>
            <div id="filter-grayscale" class="fm-sidebar-item">Zwart-wit</div>
            <div id="filter-negative" class="fm-sidebar-item">Negatief</div>
            <div id="filter-sharpen" class="fm-sidebar-item">Verscherpen</div>
            <div id="filter-emboss" class="fm-sidebar-item">Reli&euml;f</div>
        </div>

        <div id="fm-body">
            <div id="fm-image-view-container">
                <img id="fm-image-view"
                    src="#"
                    alt="Een moment geduld alstublieft." />
            </div>
        </div>

    </div>

    <div id="fm-footer" class="fm-box fm-gradient">

        <div id="fm-action">Kies een bewerking</div>

        <button name="apply"
                type="button"
                class="fm-button fm-button-left fm-button-lonely fm-button-space-right">
            <span class="fa fa-fw fa-pencil-square-o"></span>
            Toepassen
        </button>

        <div class="fm-editor-arguments-separator fm-button-left"></div>

        <div id="fm-editor-controls"></div>

        <button name="close"
                type="button"
                class="fm-button fm-button-right fm-button-lonely">
            <span class="fa fa-fw fa-times"></span> Sluiten
        </button>

    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="../js/jquery-splendid-textchange/jquery.splendid.textchange.min.js"></script>
    <script src="../js/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>
    <script src="../js/rangeslider/rangeslider.min.js"></script>
    <script src="../js/get-scrollbar-width.min.js"></script>
    <script src="../js/file-manager.min.js"></script>
    <script>
        'use strict';

        // Fix the main content container's height.
        $('#fm-body-container').css('height', window.innerHeight - 68);

        function performAction(action, args, callback)
        {
            top.FileManager.prototype.getConfig().then(function(settings) {
                if (!action) {
                    throw new Error('No action defined for performAction().');
                }

                if (!args) {
                    args = {};
                }

                if (typeof callback !== 'function') {
                    callback = function() {};
                }

                ImageEditor.actionPromise = new Promise(function(resolveAction, rejectAction) {
                    $('body').append(
                        '<div id="fm-loader-overlay">' +
                            '<div class="fm-loader">' +
                                '<svg class="fm-loader-circular" viewBox="25 25 50 50">' +
                                    '<circle class="fm-loader-path" ' +
                                        'cx="50" ' +
                                        'cy="50" ' +
                                        'r="20" ' +
                                        'fill="none" ' +
                                        'stroke-width="2" ' +
                                        'stroke-miterlimit="10" />' +
                                '</svg>' +
                            '</div>' +
                        '</div>'
                    );

                    var formData = new FormData();

                    if (action === 'init') {
                        formData.append(
                            'path',
                            top.tinymce.activeEditor.windowManager.getParams()
                                .dataset.path
                        );

                        $('body').append($(
                            '<input id="image-original-path" ' +
                                'type="hidden" ' +
                                'value="' + top.tinymce.activeEditor.windowManager.getParams()
                                    .dataset.path + '" />'
                        ));
                    } else {
                        var path = $('#fm-image-view').attr('src');

                        if (path.indexOf('?') === -1) {
                            formData.append(
                                'path',
                                path
                            );
                        } else {
                            formData.append(
                                'path',
                                path.substr(0, path.indexOf('?'))
                            );
                        }
                    }

                    formData.append(
                        'name',
                        top.tinymce.activeEditor.windowManager.getParams().dataset.name
                    );

                    formData.append('action', action);
                    formData.append('arguments', JSON.stringify(args));

                    formData.append('originalPath', $('#image-original-path').val());

                    formData.append(
                        settings.csrf.tokenName,
                        settings.csrf.hash
                    );

                    $.ajax({
                        data: formData,
                        dataType: 'json',
                        method: 'POST',
                        url: settings.installationPath +
                            'ajax/edit.php',
                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(data) {
                            if (data.error.code === 0) {
                                // Keep the previous position when replacing the image
                                // with an altered one.
                                var css;

                                if (action === 'init') {
                                    css = $('#fm-image-view')
                                        .css(['top', 'left']);
                                } else {
                                    css = $('#fm-image-view')
                                        .css(['top', 'left', 'width', 'height']);
                                }

                                var image;

                                var imageGenerationPromise = new Promise(
                                    function(resolve, reject) {
                                        image = $('<img />')
                                            .attr({
                                                'id': 'fm-image-view',
                                                'src': data.path + '?t=' + (new Date()).getTime().toString(),
                                                'alt': top.tinymce.activeEditor.windowManager
                                                    .getParams().dataset.name
                                            }).css(css)
                                            .load(function() {
                                                var originalDimensionsPromise = new Promise(
                                                    function(resolve, reject) {
                                                        if ($('#image-original-width').length === 0) {
                                                            // Store the dimensions for use in editing.
                                                            $('body').append($(
                                                                '<input id="image-original-width" ' +
                                                                    'type="hidden" ' +
                                                                    'value="' + data.width + '" />'
                                                            )).append($(
                                                                '<input id="image-original-height" ' +
                                                                    'type="hidden" ' +
                                                                    'value="' + data.height + '" />'
                                                            ));
                                                        } else {
                                                            $('#image-original-width')
                                                                .val(data.width);
                                                            $('#image-original-height')
                                                                .val(data.height);
                                                        }

                                                        var dimensions = {};

                                                        dimensions.width = data.width;
                                                        dimensions.height = data.height;

                                                        resolve(dimensions);
                                                    }
                                                );

                                                originalDimensionsPromise.then(function(dimensions) {
                                                    $('input[name="zoom"]').on('textchange', function() {
                                                        var percentage = $(this).val();

                                                        $('#fm-zoom-percentage').text(percentage + '%');

                                                        percentage = parseInt(percentage);

                                                        var originalWidth = parseInt(
                                                            dimensions.width
                                                        );

                                                        var originalHeight = parseInt(
                                                            dimensions.height
                                                        );

                                                        $('#fm-image-view').css({
                                                            'width': Math.round(originalWidth * (percentage / 100))
                                                                .toString() + 'px',
                                                            'height': Math.round(originalHeight * (percentage / 100))
                                                                .toString() + 'px'
                                                        });
                                                    });

                                                    resolve();
                                                }).catch(function(error) {
                                                    console.error(error);
                                                    reject(error);
                                                });
                                            });
                                    }
                                );

                                imageGenerationPromise.then(function() {
                                    $('#fm-image-view').remove();

                                    $('#fm-image-view-container').html(
                                        image.prop('outerHTML')
                                    );

                                    $('input[type="range"]').trigger('textchange');
                                    $('#fm-image-view').draggable();

                                    $('#fm-loader-overlay').remove();
                                    callback();
                                    resolveAction(action);
                                }).catch(function(error) {
                                    console.error(error);

                                    callback();
                                    rejectAction(error);
                                });
                            } else {
                                $('#fm-loader-overlay').remove();

                                console.error(data.error.message);

                                top.tinymce.activeEditor.windowManager.alert(
                                    data.error.message
                                );

                                callback();
                                rejectAction(data.error);
                            }
                        },
                        error: function(error) {
                            console.error(
                                'The XHR request to "' +
                                settings.installationPath +
                                'edit.php" failed.'
                            );

                            console.error(error);

                            top.tinymce.activeEditor.windowManager.alert(
                                'Er is een fout opgetreden. ' +
                                    'Probeer het later alstublieft opnieuw.',
                                top.tinymce.activeEditor.windowManager.close
                            );

                            callback();
                            rejectAction(data.error);
                        }
                    });
                });

                return ImageEditor.actionPromise;
            }).catch(function(error) {
                console.error(error);
            });
        }

        var ImageEditor = {};

        if (top.tinymce === undefined) {
            window.location.replace('<?= base_url(); ?>');
        } else {
            top.FileManager.prototype.getConfig().then(function(settings) {
                if (
                    $.inArray(
                        top.tinymce.activeEditor.windowManager.getParams()
                            .dataset.type,
                        settings.allowedFileTypes.images
                    ) === -1
                ) {
                    top.tinymce.activeEditor.windowManager.alert(
                        'Het bestand "' + top.tinymce.activeEditor.windowManager.getParams()
                            .dataset.name + '" kan niet worden bewerkt, ' +
                            'omdat het geen geldige afbeelding is.',
                        top.tinymce.activeEditor.windowManager.close
                    );
                }

                performAction('init');

                $('input[type="range"]').rangeslider({
                    polyfill: true
                });

                $('button[name="save"]').click(function() {
                    // For some reason, the promise handlers cause
                    // performAction to be 'undefined'.
                    performAction(
                        'save',
                        {},
                        function() {
                            top.tinymce.activeEditor.windowManager.close();
                        }
                    );
                });

                $('button[name="reset"]').click(function() {
                    performAction('reset');
                    $('#fm-action').text('Kies een bewerking');
                    $('#fm-editor-controls').empty();
                    $('#fm-editor-controls').data('action', null);
                });

                $('button[name="apply"]').click(function() {
                    var action = $('#fm-editor-controls').data().action;
                    var args = ImageEditor.arguments;

                    if (action) {
                        if (!args) {
                            performAction(action);
                        } else {
                            performAction(action, args);
                        }
                    } else {
                        top.tinymce.activeEditor.windowManager.alert(
                            'U heeft geen bewerking geselecteerd.'
                        );
                    }
                });

                $('.fm-sidebar-item').click(function() {
                    var editorControls;
                    var image = $('#fm-image-view');

                    switch (this.id) {
                        case 'alter-size':
                            ImageEditor.arguments = {
                                width: image.width(),
                                height: image.height()
                            };

                            var widthInput = $(
                                '<input name="width" ' +
                                    'type="number" ' +
                                    'value="' + image.width() + '" ' +
                                    'class="fm-textbox-input fm-editor-argument-input" />'
                            ).on('textchange', function() {
                                if (
                                    this.value !== '' &&
                                    $('input[name="keep-proportions"]')[0].checked
                                ) {
                                    var width = parseInt(this.value);

                                    var height = Math.round(
                                        width * (
                                            parseInt($('#image-original-height').val()) /
                                            parseInt($('#image-original-width').val())
                                        )
                                    );

                                    $('input[name="height"]').val(height);

                                    ImageEditor.arguments = {
                                        width: width,
                                        height: height
                                    };
                                } else {
                                    ImageEditor.arguments = {
                                        width: parseInt(this.value),
                                        height: parseInt($('input[name="height"]').val())
                                    };
                                }
                            });

                            var heightInput = $(
                                '<input name="height" ' +
                                    'type="number" ' +
                                    'value="' + image.height() + '" ' +
                                    'class="fm-textbox-input fm-editor-argument-input" />'
                            ).on('textchange', function() {
                                if (
                                    this.value !== '' &&
                                    $('input[name="keep-proportions"]')[0].checked
                                ) {
                                    var height = parseInt(this.value);

                                    var width = Math.round(
                                        height * (
                                            parseInt($('#image-original-width').val()) /
                                            parseInt($('#image-original-height').val())
                                        )
                                    )

                                    $('input[name="width"]').val(width);

                                    ImageEditor.arguments = {
                                        width: width,
                                        height: height
                                    };
                                } else {
                                    ImageEditor.arguments = {
                                        width: parseInt($('input[name="width"]').val()),
                                        height: parseInt(this.value)
                                    };
                                }
                            });

                            editorControls =
                                $('<span class="fm-textbox-label">Breedte:</span>')
                                .add(widthInput[0])
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>' +
                                    '<span class="fm-textbox-label">Hoogte:</span>'
                                )
                                .add(heightInput[0])
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>' +
                                    '<span class="fm-textbox-label">Proporties behouden:</span>' +
                                    '<input name="keep-proportions" ' +
                                        'type="checkbox" ' +
                                        'value="keep-proportions" ' +
                                        'class="fm-checkbox-input" ' +
                                        'checked="checked" />'
                                );

                            break;
                        case 'alter-crop':
                            image.draggable('disable');

                            ImageEditor.arguments = {
                                x: 0,
                                y: 0,
                                width: parseInt($('#image-original-width').val()),
                                height: parseInt($('#image-original-height').val())
                            };

                            var onSelectChangeHandler = function(currentImage, selection) {
                                var zoom = parseInt(
                                    $('#fm-zoom-percentage').text()
                                ) / 100;

                                var x = Math.round(selection.x1 / zoom);
                                var y = Math.round(selection.y1 / zoom);
                                var width = Math.round(selection.width / zoom);
                                var height = Math.round(selection.height / zoom);

                                $('input[name="x"]').val(x);
                                $('input[name="y"]').val(y);
                                $('input[name="width"]').val(width);
                                $('input[name="height"]').val(height);

                                ImageEditor.arguments = {
                                    x: x,
                                    y: y,
                                    width: width,
                                    height: height
                                };
                            };

                            ImageEditor.imgAreaSelect = image.imgAreaSelect({
                                handles: true,
                                //autoHide: true,
                                instance: true,
                                onSelectChange: onSelectChangeHandler
                            });

                            $('#fm-action').on('contentchange', function() {
                                if (
                                    $('#fm-editor-controls').data().action !==
                                    'alter-crop'
                                ) {
                                    var image = $('#fm-image-view');

                                    image.imgAreaSelect({
                                        remove: true
                                    });

                                    image.draggable('enable');
                                }
                            });

                            $('button[name="apply"]').click(function() {
                                ImageEditor.actionPromise.then(function() {
                                    if (
                                        $('#fm-editor-controls').data().action ===
                                        'alter-crop'
                                    ) {
                                        ImageEditor.imgAreaSelect.remove();

                                        var image = $('#fm-image-view');

                                        image.draggable('disable');

                                        ImageEditor.imgAreaSelect = image
                                            .imgAreaSelect({
                                                handles: true,
                                                instance: true,
                                                onSelectChange: onSelectChangeHandler
                                            });

                                        $('input[name="x"]').attr(
                                            'max',
                                            image.width()
                                        );

                                        $('input[name="y"]').attr(
                                            'max',
                                            image.height()
                                        );

                                        $('input[name="width"]').attr(
                                            'max',
                                            image.width()
                                        );

                                        $('input[name="height"]').attr(
                                            'max',
                                            image.height()
                                        );
                                    }
                                });
                            });

                            var inputTextchangeHandler = function() {
                                var zoom = parseInt(
                                    $('#fm-zoom-percentage').text()
                                ) / 100;

                                var x1 = parseInt($('input[name="x"]').val());
                                var y1 = parseInt($('input[name="y"]').val());
                                var width = parseInt($('input[name="width"]').val());
                                var height = parseInt($('input[name="height"]').val());

                                ImageEditor.imgAreaSelect.setOptions({
                                    x1: x1 * zoom,
                                    y1: y1 * zoom,
                                    x2: (x1 + width) * zoom,
                                    y2: (y1 + height) * zoom,
                                    width: width * zoom,
                                    height: height * zoom
                                });

                                ImageEditor.arguments = {
                                    x: x1,
                                    y: y1,
                                    width: width,
                                    height: height
                                };
                            };

                            var xInput = $(
                                '<input name="x" ' +
                                    'type="number" ' +
                                    'value="0" ' +
                                    'min="0" ' +
                                    'max="' + image.width() + '" ' +
                                    'class="fm-textbox-input fm-editor-argument-input" />'
                            ).on('textchange', inputTextchangeHandler);

                            var yInput = $(
                                '<input name="y" ' +
                                    'type="number" ' +
                                    'value="0" ' +
                                    'min="0" ' +
                                    'max="' + image.height() + '" ' +
                                    'class="fm-textbox-input fm-editor-argument-input" />'
                            ).on('textchange', inputTextchangeHandler);

                            var widthInput = $(
                                '<input name="width" ' +
                                    'type="number" ' +
                                    'value="' + image.width() + '" ' +
                                    'min="0" ' +
                                    'max="' + image.width() + '" ' +
                                    'class="fm-textbox-input fm-editor-argument-input" />'
                            ).on('textchange', inputTextchangeHandler);

                            var heightInput = $(
                                '<input name="height" ' +
                                    'type="number" ' +
                                    'value="' + image.height() + '" ' +
                                    'min="0" ' +
                                    'max="' + image.height() + '" ' +
                                    'class="fm-textbox-input fm-editor-argument-input" />'
                            ).on('textchange', inputTextchangeHandler);

                            editorControls = $('<span class="fm-textbox-label">X:</span>')
                                .add(xInput)
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>' +
                                    '<span class="fm-textbox-label">Y:</span>'
                                )
                                .add(yInput)
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>' +
                                    '<span class="fm-textbox-label">Breedte:</span>'
                                )
                                .add(widthInput)
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>' +
                                    '<span class="fm-textbox-label">Hoogte:</span>'
                                )
                                .add(heightInput);

                            break;
                        case 'alter-flip-rotate':
                            $(
                                'button[name="apply"], ' +
                                '#fm-footer > .fm-editor-arguments-separator'
                            ).addClass('fm-hidden');

                            $('#fm-action').on('contentchange', function() {
                                if (
                                    $('#fm-editor-controls').data().action !==
                                    'alter-flip-rotate'
                                ) {
                                    $(
                                        'button[name="apply"], ' +
                                        '#fm-footer > .fm-editor-arguments-separator'
                                    ).removeClass('fm-hidden');
                                }
                            });

                            var flipHorizontalButton = $(
                                '<button name="flip-horizontal" ' +
                                        'type="button" ' +
                                        'class="fm-button fm-button-left fm-button-lonely fm-button-space-right" ' +
                                        'title="Horizontaal spiegelen">' +
                                    '<span class="fa fa-fw fa-arrows-h"></span>' +
                                '</button>'
                            ).click(function() {
                                ImageEditor.arguments = {
                                    flip: 'horizontal'
                                };

                                $('button[name="apply"]').click();
                            });

                            var flipVerticalButton = $(
                                '<button name="flip-vertical" ' +
                                        'type="button" ' +
                                        'class="fm-button fm-button-left fm-button-lonely fm-button-space-right" ' +
                                        'title="Verticaal spiegelen">' +
                                    '<span class="fa fa-fw fa-arrows-v"></span>' +
                                '</button>'
                            ).click(function() {
                                ImageEditor.arguments = {
                                    flip: 'vertical'
                                };

                                $('button[name="apply"]').click();
                            });

                            var rotateLeftButton = $(
                                '<button name="rotate-left" ' +
                                        'type="button" ' +
                                        'class="fm-button fm-button-left fm-button-lonely fm-button-space-right" ' +
                                        'title="Linksom draaien">' +
                                    '<span class="fa fa-fw fa-rotate-left"></span>' +
                                '</button>'
                            ).click(function() {
                                ImageEditor.arguments = {
                                    rotate: 'left'
                                };

                                $('button[name="apply"]').click();
                            });

                            var rotateRightButton = $(
                                '<button name="rotate-right" ' +
                                        'type="button" ' +
                                        'class="fm-button fm-button-left fm-button-lonely fm-button-space-right" ' +
                                        'title="Rechtsom draaien">' +
                                    '<span class="fa fa-fw fa-rotate-right"></span>' +
                                '</button>'
                            ).click(function() {
                                ImageEditor.arguments = {
                                    rotate: 'right'
                                };

                                $('button[name="apply"]').click();
                            });

                            editorControls = flipHorizontalButton
                                .add(flipVerticalButton)
                                .add(rotateLeftButton)
                                .add(rotateRightButton);

                            break;
                        case 'filter-brightness':
                            ImageEditor.arguments = {
                                level: 0
                            };

                            editorControls = $(
                                '<span id="brightness-level" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="brightness" ' +
                                    'type="range" ' +
                                    'min="-100" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    level: parseInt(event.target.value)
                                };

                                $('#brightness-level').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-contrast':
                            ImageEditor.arguments = {
                                level: 0
                            };

                            editorControls = $(
                                '<span id="contrast-level" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="contrast" ' +
                                    'type="range" ' +
                                    'min="-100" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    level: parseInt(event.target.value)
                                };

                                $('#contrast-level').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-exposure':
                            ImageEditor.arguments = {
                                level: 100
                            };

                            editorControls = $(
                                '<span id="exposure-level" class="fm-editor-percentage-label">' +
                                    '100%' +
                                '</span>' +
                                '<input name="exposure" ' +
                                    'type="range" ' +
                                    'min="0" ' +
                                    'max="200" ' +
                                    'step="1" ' +
                                    'value="100" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    level: parseInt(event.target.value)
                                };

                                $('#exposure-level').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-gamma':
                            ImageEditor.arguments = {
                                correction: 1.0
                            };

                            editorControls = $(
                                '<span id="gamma-correction" class="fm-editor-percentage-label">' +
                                    '1' +
                                '</span>' +
                                '<input name="gamma" ' +
                                    'type="range" ' +
                                    'min="0" ' +
                                    'max="2" ' +
                                    'step="0.01" ' +
                                    'value="1" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    correction: parseFloat(event.target.value)
                                };

                                $('#gamma-correction').text(
                                    event.target.value
                                );
                            });

                            break;
                        case 'filter-hue':
                            ImageEditor.arguments = {
                                hue: 0
                            };

                            editorControls = $(
                                '<span id="hue" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="hue" ' +
                                    'type="range" ' +
                                    'min="-100" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    hue: parseInt(event.target.value)
                                };

                                $('#hue').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-saturate':
                            ImageEditor.arguments = {
                                level: 0
                            };

                            editorControls = $(
                                '<span id="saturation-level" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="saturation" ' +
                                    'type="range" ' +
                                    'min="-100" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    level: parseInt(event.target.value)
                                };

                                $('#saturation-level').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-vibrance':
                            ImageEditor.arguments = {
                                level: 0
                            };

                            editorControls = $(
                                '<span id="vibrance-level" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="vibrance" ' +
                                    'type="range" ' +
                                    'min="-100" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    level: parseInt(event.target.value)
                                };

                                $('#vibrance-level').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-blur':
                            ImageEditor.arguments = {
                                amount: 0
                            };

                            editorControls = $(
                                '<span id="blur-amount" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="blur" ' +
                                    'type="range" ' +
                                    'min="0" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    amount: parseInt(event.target.value)
                                };

                                $('#blur-amount').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                        case 'filter-colorize':
                            ImageEditor.arguments = {
                                red: 0,
                                green: 0,
                                blue: 0
                            };

                            var redInput = $(
                                '<div class="fm-editor-argument-container">' +
                                    '<span class="fm-textbox-label">Rood:</span>' +
                                    '<span id="red-amount" class="fm-editor-percentage-label">' +
                                        '0%' +
                                    '</span>' +
                                    '<input name="colorize-red" ' +
                                        'type="range" ' +
                                        'min="-100" ' +
                                        'max="100" ' +
                                        'step="1" ' +
                                        'value="0" ' +
                                        'data-orientation="horizontal" ' +
                                        'class="fm-button fm-button-right fm-button-lonely fm-short-range" />' +
                                '</div>'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments.red =
                                    parseInt(event.target.value);

                                $('#red-amount').text(
                                    event.target.value + '%'
                                );
                            });

                            var greenInput = $(
                                '<div class="fm-editor-argument-container">' +
                                    '<span class="fm-textbox-label">Groen:</span>' +
                                    '<span id="green-amount" class="fm-editor-percentage-label">' +
                                        '0%' +
                                    '</span>' +
                                    '<input name="colorize-green" ' +
                                        'type="range" ' +
                                        'min="-100" ' +
                                        'max="100" ' +
                                        'step="1" ' +
                                        'value="0" ' +
                                        'data-orientation="horizontal" ' +
                                        'class="fm-button fm-button-right fm-button-lonely fm-short-range" />' +
                                '</div>'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments.green =
                                    parseInt(event.target.value);

                                $('#green-amount').text(
                                    event.target.value + '%'
                                );
                            });

                            var blueInput = $(
                                '<div class="fm-editor-argument-container">' +
                                    '<span class="fm-textbox-label">Blauw:</span>' +
                                    '<span id="blue-amount" class="fm-editor-percentage-label">' +
                                        '0%' +
                                    '</span>' +
                                    '<input name="colorize-blue" ' +
                                        'type="range" ' +
                                        'min="-100" ' +
                                        'max="100" ' +
                                        'step="1" ' +
                                        'value="0" ' +
                                        'data-orientation="horizontal" ' +
                                        'class="fm-button fm-button-right fm-button-lonely fm-short-range" />' +
                                '</div>'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments.blue =
                                    parseInt(event.target.value);

                                $('#blue-amount').text(
                                    event.target.value + '%'
                                );
                            });

                            editorControls = redInput
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>'
                                )
                                .add(greenInput)
                                .add(
                                    '<div class="fm-editor-arguments-separator"></div>'
                                )
                                .add(blueInput);

                            break;
                        case 'filter-sharpen':
                            ImageEditor.arguments = {
                                amount: 0
                            };

                            editorControls = $(
                                '<span id="sharpen-amount" class="fm-editor-percentage-label">' +
                                    '0%' +
                                '</span>' +
                                '<input name="sharpen" ' +
                                    'type="range" ' +
                                    'min="0" ' +
                                    'max="100" ' +
                                    'step="1" ' +
                                    'value="0" ' +
                                    'data-orientation="horizontal" ' +
                                    'class="fm-button fm-button-right fm-button-lonely" />'
                            ).on('textchange', function(event) {
                                ImageEditor.arguments = {
                                    amount: parseInt(event.target.value)
                                };

                                $('#sharpen-amount').text(
                                    event.target.value + '%'
                                );
                            });

                            break;
                    }

                    $('#fm-editor-controls').empty()
                        .data('action', this.id)
                        .append(editorControls);

                    $('#fm-action').trigger('contentchange').text($(this).text());
                });

                $('button[name="close"]').click(
                    top.tinymce.activeEditor.windowManager.close
                );
            }).catch(function(error) {
                console.error(error);
            });
        }
    </script>
</body>
</html>
