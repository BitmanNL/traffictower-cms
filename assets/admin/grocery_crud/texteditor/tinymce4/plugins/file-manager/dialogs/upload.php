<?php
    namespace Bitman\Cms\FileManager;

    require '../config.php';
    require '../src/formatBytes.php';
    require '../src/getMaxFileUploadSize.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Uploaden</title>

    <link rel="stylesheet" href="../css/main.min.css" />
    <link rel="stylesheet" href="../css/upload.min.css" />
    <link rel="stylesheet" href="../fonts/font-awesome/css/font-awesome.min.css" />

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

    <meta name="robots" content="noindex, nofollow" />
</head>
<body>
    <div id="fm-menu-container" class="fm-box fm-gradient">
        <div id="fm-menu">

            <button name="choose-files"
                type="button"
                class="fm-button fm-button-left fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-file"></span> Bestanden Kiezen
            </button>

        </div>
    </div>

    <div id="fm-body-container" class="fm-gradient">

        <div class="fm-upload-item">
            <p class="fm-small">
                Toegestane bestandsextensies: <?php
                    $extensions = array_merge(
                        $config['allowedFileExtensions']['archives'],
                        $config['allowedFileExtensions']['documents'],
                        $config['allowedFileExtensions']['images']
                    );

                    natsort($extensions);

                    echo implode(', ', $extensions), '.';
                ?>
            </p>
        </div>
        <div class="fm-upload-item">
            <p class="fm-small">
                Bestanden (maximaal <?=
                    formatBytes(getMaxFileUploadSize(), 0, true);
                ?>):
            </p>
        </div>
        <div id="fm-upload-body"></div>
        <div id="fm-upload-progress-container" class="fm-box">
            <div id="fm-upload-progress">
                <div id="fm-upload-progress-bar"></div>
                <p></p>
            </div>
        </div>

    </div>

    <div id="fm-footer" class="fm-box fm-gradient">
        <button name="cancel" type="button" class="fm-button fm-button-right fm-button-lonely">
            <span class="fa fa-fw fa-times fm-no"></span> Annuleren
        </button>
        <form enctype="multipart/form-data">
            <input name="MAX_FILE_SIZE"
                type="hidden"
                value="<?= getMaxFileUploadSize(); ?>" />
            <input name="files[]"
                type="file"
                multiple="multiple"
                accept="<?php
                        echo implode(
                            ',',
                            array_merge(
                                $config['allowedFileTypes']['archives'],
                                $config['allowedFileTypes']['documents'],
                                $config['allowedFileTypes']['images']
                            )
                        );
                    ?>"
                class="fm-hidden" />
            <button name="upload"
                type="button"
                class="fm-button-primary fm-button-right fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-check fm-yes"></span> Uploaden
            </button>
        </form>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?= asset_url('assets/admin/grocery_crud/texteditor/tinymce4/plugins/file-manager/js/jquery-dotdotdot/src/js/jquery.dotdotdot.min.js'); ?>"></script>
    <script src="../js/file-manager.min.js"></script>
    <script src="../js/format-bytes.min.js"></script>
    <script>
        'use strict';

        // Fix the main content container's height.
        $('#fm-body-container').css('height', window.innerHeight - 68);

        if (top.tinymce === undefined) {
            window.location.replace('<?= base_url(); ?>');
        } else {
            var fileManager = top.FileManager.prototype.getInstance();

            $('button[name="choose-files"]').click(function() {
                $('input[name="files[]"]').click();
            });

            $('#fm-allowed-extensions').dotdotdot({
                height: 30,
                callback: function(isTruncated, orgContent) {
                        if (isTruncated) {
                            this.title = orgContent[0].textContent.trim();
                        }
                    }
            });

            $('button[name="upload"]').click(function() {
                top.FileManager.prototype.getConfig().then(function(settings) {
                    var files = $('input[name="files[]"]')[0].files;
                    var formData = new FormData();
                    var errorPromise = new Promise(function(resolve, reject) {
                        if (files.length === 0) {
                            reject('U heeft geen bestand(en) gekozen.');
                        } else {
                            var overwritePromise;

                            for (var i = 0, m = files.length; i < m; ++i) {
                                var file = files[i];

                                if (
                                    top.tinymce.activeEditor.windowManager.getParams()
                                        .context.FileManager.prototype.files
                                        .find(function(element) {
                                            return element.name === file.name;
                                        })
                                    !== undefined
                                ) {
                                    overwritePromise = new Promise(function(resolve, reject) {
                                        top.tinymce.activeEditor.windowManager.confirm(
                                            'Het bestand "' + file.name + '" bestaat al. ' +
                                                'Weet u zeker dat u door wilt gaan? ' +
                                                'Het bestand wordt overschreven.',
                                            function(isConfirmed) {
                                                if (isConfirmed) {
                                                    resolve();
                                                } else {
                                                    reject();
                                                }
                                            }
                                        );
                                    });
                                }
                            }

                            var validate = function() {
                                var totalSize = 0;

                                for (var i = 0, m = files.length; i < m; ++i) {
                                    var file = files[i];

                                    var extension = file.name.substr(
                                        file.name.lastIndexOf('.') + 1
                                    ).toLowerCase();

                                    if (
                                        !(
                                            settings.allowedFileExtensions.archives.concat(
                                                settings.allowedFileExtensions.documents,
                                                settings.allowedFileExtensions.images
                                            ).indexOf(extension) !== -1
                                        ) || !(
                                            settings.allowedFileTypes.archives.concat(
                                                settings.allowedFileTypes.documents,
                                                settings.allowedFileTypes.images
                                            ).indexOf(file.type) !== -1
                                        )
                                    ) {
                                        reject(
                                            'Het bestand "' + file.name + '" kan niet geüpload worden, ' +
                                            'omdat het bestandstype (' + file.type + ') niet toegestaan wordt. '
                                        );

                                        break;
                                    }

                                    totalSize += file.size;
                                    formData.append('file-' + i, file, file.name);
                                }

                                formData.append(
                                    'path',
                                    top.tinymce.activeEditor.windowManager.getParams().path
                                );

                                if (totalSize > Number($('input[name="MAX_FILE_SIZE"]')[0].value)) {
                                    reject(
                                        'De bestanden kunnen niet worden geüpload, ' +
                                        'omdat deze samen groter dan <?=
                                            formatBytes(getMaxFileUploadSize(), 0, true);
                                        ?> zijn. '
                                    );
                                }

                                formData.append(
                                    settings.csrf.tokenName,
                                    settings.csrf.hash
                                );
                            };

                            if (overwritePromise === undefined) {
                                validate()
                                resolve();
                            } else {
                                overwritePromise.then(function() {
                                    validate();
                                    resolve();
                                });
                            }
                        }
                    });

                    var errorHandler = function(error) {
                        return new Promise(function(resolve, reject) {
                            if (error === undefined) {
                                $.ajax({
                                    data: formData,
                                    method: 'POST',
                                    url: settings.installationPath +
                                            'ajax/upload.php',
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    xhr: function() {
                                        var xhr = $.ajaxSettings.xhr();

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener(
                                                'progress',
                                                function(progressEvent) {
                                                    if (progressEvent.lengthComputable) {
                                                        var progress = Math.round(
                                                            (
                                                                progressEvent.loaded /
                                                                progressEvent.total
                                                            ) * 100
                                                        );

                                                        $('#fm-upload-progress-bar').width(
                                                            progress + '%'
                                                        );

                                                        $('#fm-upload-progress > p').text(
                                                            progress + '% (' +
                                                            formatBytes(
                                                                progressEvent.loaded,
                                                                1,
                                                                true
                                                            ) +
                                                            '/' +
                                                            formatBytes(
                                                                progressEvent.total,
                                                                1,
                                                                true
                                                            ) + ')'
                                                        );

                                                        if (progress >= 50) {
                                                            $('#fm-upload-progress > p').css(
                                                                'color',
                                                                '#fff'
                                                            );
                                                        } else {
                                                            $('#fm-upload-progress > p').css(
                                                                'color',
                                                                '#000'
                                                            );
                                                        }

                                                        if (progress === 100) {
                                                            $('#fm-upload-progress-bar').css({
                                                                '-webkit-border-radius': '0',
                                                                '-moz-border-radius': '0',
                                                                'border-radius': '0'
                                                            });
                                                        }
                                                    }
                                                },
                                                false
                                            );
                                        }

                                        return xhr;
                                    },
                                    success: function(data) {
                                        if (data.error.code === 0) {
                                            top.tinymce.activeEditor.windowManager
                                                .getParams().context
                                                .FileManager
                                                .prototype
                                                .refreshFiles(window.document).then(function() {
                                                    top.tinymce.activeEditor.windowManager
                                                        .getParams().resolve();
                                                    top.tinymce.activeEditor.windowManager
                                                        .close();
                                                }).catch(function(error) {
                                                    console.error(error);
                                                });
                                        } else {
                                            top.tinymce.activeEditor.windowManager.alert(
                                                data.error.message
                                            );

                                            top.tinymce.activeEditor.windowManager
                                                .getParams().reject(data.error);
                                        }
                                    },
                                    error: function(error) {
                                        console.error(error);

                                        top.tinymce.activeEditor.windowManager.alert(
                                            'Er is een fout opgetreden. ' +
                                            'Probeer het later alstublieft opnieuw.'
                                        );

                                        top.tinymce.activeEditor.windowManager
                                            .getParams().reject(error);
                                    }
                                });
                            } else {
                                top.tinymce.activeEditor.windowManager.alert(error);
                                top.tinymce.activeEditor.windowManager
                                    .getParams().reject(error);
                            }
                        });
                    };

                    errorPromise.then(function() {
                        errorHandler();
                    }).catch(top.tinymce.activeEditor.windowManager.alert);
                }).catch(function(error) {
                    console.error(error);
                });
            });

            $('input[name="files[]"]').change(function() {
                var html = '';

                for (var i = 0, m = this.files.length; i < m; ++i) {
                    html += '<div class="fm-upload-item">' +
                            '<p>' + this.files[i].name + '</p>' +
                        '</div>';
                }

                $('#fm-upload-body').html(html);

                $('.fm-upload-item').dotdotdot({
                    height: 30,
                    callback: function(isTruncated, orgContent) {
                            if (isTruncated) {
                                this.title = orgContent[0].textContent;
                            }
                        }
                });
            });

            $('button[name="cancel"]').click(function() {
                top.tinymce.activeEditor.windowManager.getParams().reject();
                top.tinymce.activeEditor.windowManager.close();
            });

            // String.prototype.trim() polyfill.
            if (!String.prototype.trim) {
                String.prototype.trim = function() {
                    return this.replace(
                        /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
                        ''
                    );
                };
            }

            // Array.prototype.find() polyfill.
            if (!Array.prototype.find) {
                Array.prototype.find = function(predicate) {
                    if (this === null) {
                        throw new TypeError(
                            'Array.prototype.find called on null or undefined'
                        );
                    }

                    if (typeof predicate !== 'function') {
                        throw new TypeError('predicate must be a function');
                    }

                    var list = Object(this);
                    var length = list.length >>> 0;
                    var thisArg = arguments[1];
                    var value;

                    for (var i = 0; i < length; i++) {
                        value = list[i];

                        if (predicate.call(thisArg, value, i, list)) {
                            return value;
                        }
                    }

                    return undefined;
                };
            }
        }
    </script>
</body>
</html>
