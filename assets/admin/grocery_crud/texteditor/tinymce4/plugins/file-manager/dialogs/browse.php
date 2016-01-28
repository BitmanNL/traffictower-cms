<?php
    require '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bestanden</title>

    <link rel="stylesheet" href="../css/main.min.css" />
    <link rel="stylesheet" href="../fonts/font-awesome/css/font-awesome.min.css" />

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

    <meta name="robots" content="noindex, nofollow" />
</head>
<body>
    <div id="fm-menu-container" class="fm-box fm-gradient">
        <div id="fm-menu">

            <button name="upload"
                type="button"
                class="fm-button fm-button-left fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-upload"></span> Uploaden
            </button>
            <button name="new-directory"
                type="button"
                class="fm-button fm-button-left fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-folder"></span> Nieuwe Map
            </button>

            <button name="thumbnails"
                type="button"
                title="Miniaturen"
                class="fm-button fm-button-right fm-button-last">
                <span class="fa fa-fw fa-th"></span>
            </button>
            <button name="list"
                type="button"
                title="Lijst"
                class="fm-button fm-button-right fm-button-first">
                <span class="fa fa-fw fa-list"></span>
            </button>

            <button name="refresh"
                type="button"
                title="Verversen"
                class="fm-button fm-button-right fm-button-lonely fm-button-space-right">
                <span class="fa fa-fw fa-refresh"></span>
            </button>

        </div>
    </div>

    <div id="fm-body-container" class="fm-gradient">

        <div id="fm-sidebar" class="fm-box">
            <div id="shortcut-files" class="fm-sidebar-item">Bestanden</div>
            <!--<div class="fm-sidebar-item">Favorieten</div>-->
        </div>

        <div id="fm-body"></div>

    </div>

    <div id="fm-footer" class="fm-box fm-gradient">

        <button name="close"
                type="button"
                class="fm-button fm-button-right fm-button-lonely">
            <span class="fa fa-fw fa-times"></span> Sluiten
        </button>

        <button name="remove"
                type="button"
                disabled="disabled"
                class="fm-button fm-button-right fm-button-lonely fm-button-space-right">
            <span class="fa fa-fw fa-times-circle"></span> Verwijderen
        </button>

    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?= asset_url('assets/admin/grocery_crud/texteditor/tinymce4/plugins/file-manager/js/jquery-dotdotdot/src/js/jquery.dotdotdot.min.js'); ?>"></script>
    <script src="../js/get-scrollbar-width.min.js"></script>
    <script src="../js/file-manager.min.js"></script>
    <script>
        'use strict';

        // Fix the main content container's height.
        $('#fm-body-container').css('height', top.innerHeight - 68);

        if (top.tinymce === undefined) {
            top.location.replace('<?= base_url(); ?>');
        } else {
            top.FileManager.prototype.loadGetScrollbarWidth().then(function(getScrollbarWidth) {
                top.FileManager.prototype.getConfig().then(function(settings) {
                    // Load the files in the appropriate view.
                    top.FileManager.prototype.getFiles().then(
                        function() {
                            switch (settings.defaultFileView) {
                                case 'list':
                                    top.FileManager.prototype.viewState = 'list';

                                    top.FileManager.prototype.viewFileList(false, window.document).then(
                                        setClickContainerHandler
                                    ).catch(function(error) {
                                        console.error(error);
                                    });

                                    break;
                                case 'thumbnails':
                                    top.FileManager.prototype.viewState = 'thumbnails';

                                    top.FileManager.prototype.viewFileThumbnails(false, window.document).then(
                                        setClickContainerHandler
                                    ).catch(function(error) {
                                        console.error(error);
                                    });

                                    break;
                                default:
                                    settings.defaultFileView = 'list';
                                    top.FileManager.prototype.viewState =
                                        settings.defaultFileView;

                                    top.FileManager.prototype.viewFileList(false, window.document).then(
                                        setClickContainerHandler
                                    ).catch(function(error) {
                                        console.error(error);
                                    });

                                    break;
                            }
                        }
                    ).catch(function(error) {
                        console.error(error);

                        top.tinymce.activeEditor.windowManager.alert(
                            'Er is een fout opgetreden. ' +
                            'Probeer het later alstublieft opnieuw.'
                        );
                    });

                    $('button[name="upload"]').click(function() {
                        var width = 600 - getScrollbarWidth();
                        var height = 400;

                        if (top.innerWidth < width || top.innerHeight < height) {
                            width = top.innerWidth - 72 - getScrollbarWidth();
                            height = top.innerHeight - 72;
                        }

                        (new Promise(function(resolve, reject) {
                            top.tinymce.activeEditor.windowManager.open(
                                {
                                    title: 'Uploaden',
                                    url: settings.installationPath +
                                        'dialogs/upload.php',
                                    width: width,
                                    height: height
                                },
                                {
                                    context: top,
                                    path: top.FileManager.prototype.path,
                                    resolve: resolve,
                                    reject: reject
                                }
                            );
                        })).then(function() {
                            setClickContainerHandler();
                        }).catch(function(error) {
                            console.error(error);
                        });
                    });

                    // Set event handlers.
                    $('button[name="new-directory"]').click(function() {
                        var width = 400 - getScrollbarWidth();
                        var height = 120;

                        if (top.innerWidth < width || top.innerHeight < height) {
                            width = top.innerWidth - 72 - getScrollbarWidth();
                            height = top.innerHeight - 72;
                        }

                        (new Promise(function(resolve, reject) {
                            top.tinymce.activeEditor.windowManager.open(
                                {
                                    title: 'Nieuwe Map',
                                    url: settings.installationPath +
                                        'dialogs/new-directory.php',
                                    width: width,
                                    height: height
                                },
                                {
                                    context: top,
                                    path: top.FileManager.prototype.path,
                                    resolve: resolve,
                                    reject: reject
                                }
                            );
                        })).then(function() {
                            setClickContainerHandler();
                        }).catch(function(error) {
                            console.error(error);
                        });
                    });

                    $('button[name="refresh"]').click(function() {
                        top.FileManager.prototype.refreshFiles(window.document).then(
                            setClickContainerHandler
                        ).catch(function(error) {
                            console.error(error);

                            top.tinymce.activeEditor.windowManager.alert(
                                'Er is een fout opgetreden. ' +
                                'Probeer het later alstublieft opnieuw.'
                            );
                        });
                    });

                    $('button[name="list"]').click(function() {
                        if (top.FileManager.prototype.viewState !== 'list') {
                            top.FileManager.prototype.viewState = 'list';

                            top.FileManager.prototype.viewFileList(undefined, window.document).then(
                                setClickContainerHandler
                            ).catch(function(error) {
                                console.error(error);

                                top.tinymce.activeEditor.windowManager.alert(
                                    'Er is een fout opgetreden. ' +
                                    'Probeer het later alstublieft opnieuw.'
                                );
                            });
                        }
                    });

                    $('button[name="thumbnails"]').click(function() {
                        if (top.FileManager.prototype.viewState !== 'thumbnails') {
                            top.FileManager.prototype.viewState = 'thumbnails';

                            FileManager.prototype.viewFileThumbnails(undefined, window.document).then(
                                setClickContainerHandler
                            ).catch(function(error) {
                                console.error(error);

                                top.tinymce.activeEditor.windowManager.alert(
                                    'Er is een fout opgetreden. ' +
                                    'Probeer het later alstublieft opnieuw.'
                                );
                            });
                        }
                    });

                    $('#shortcut-files').click(function() {
                        top.FileManager.prototype.path = '';
                        $('button[name="refresh"]').click();
                    });

                    $('#shortcut-favorites').click(function() {
                        /// @todo Add favorites feature.
                    });

                    $('button[name="remove"]').click(function() {
                        if (top.FileManager.selectedFile !== undefined) {
                            if (top.FileManager.selectedFile.dataset.name !== '..') {
                                top.tinymce.activeEditor.windowManager.confirm(
                                    'Het verwijderen van "' +
                                        top.FileManager.selectedFile.dataset.name +
                                        '" kan niet ongedaan worden. ' +
                                        'Weet u zeker dat u door wilt gaan?',
                                    function(isConfirmed) {
                                        if (isConfirmed) {
                                            top.FileManager.prototype.removeFile(
                                                top.FileManager.prototype.path + '/' +
                                                top.FileManager.selectedFile.dataset.name,
                                                setClickContainerHandler,
                                                window.document
                                            );
                                        }
                                    }
                                );
                            } else {
                                top.tinymce.activeEditor.windowManager.alert(
                                    'De referentie naar de bovenliggende map ("..") ' +
                                    'kan niet verwijderd worden.'
                                );
                            }
                        }
                    });

                    if (!top.FileManager.prototype.standalone) {
                        $('button[name="close"]').click(
                            top.tinymce.activeEditor.windowManager.close
                        );
                    } else {
                        $('button[name="close"]').click(function() {
                            if (top.document.referrer !== '') {
                                top.window.location = top.document.referrer;
                            } else {
                                top.window.location = top.document.cms_base_url;
                            }
                        });
                    }
                }).catch(function(error) {
                    console.error(error);

                    reject(error.message);
                });
            }).catch(function(error) {
                console.error(error);
            });
        }

        function setClickContainerHandler(self)
        {
            var clickHandler = function(self) {
                if (self !== undefined) {
                    if (self.dataset.type !== 'dir') {
                        if (!top.FileManager.prototype.standalone) {
                            top.document.getElementById(
                                top.tinymce.activeEditor.windowManager.getParams()
                                    .fieldName
                            ).value = self.dataset.path;

                            if (
                                typeof top.tinymce.activeEditor.windowManager
                                    .getParams().callback === 'function'
                            ) {
                                top.tinymce.activeEditor.windowManager.getParams()
                                    .callback();
                            }

                            top.tinymce.activeEditor.windowManager.close();
                        }
                    } else {
                        if (self.dataset.name === '..') {
                            top.FileManager.prototype.path = top.FileManager.prototype.path
                                .substr(
                                    0,
                                    top.FileManager.prototype.path.lastIndexOf('/')
                                );
                        } else {
                            top.FileManager.prototype.path += '/' + self.dataset.name;
                        }

                        top.FileManager.prototype.refreshFiles(window.document).then(function() {
                            setClickContainerHandler(undefined);
                        }).catch(function(reason) {
                            console.error(reason);

                            top.tinymce.activeEditor.windowManager.alert(
                                'Er is een fout opgetreden. ' +
                                'Probeer het later alstublieft opnieuw.'
                            );
                        });
                    }
                }
            };

            $('.fm-body-item, .fm-body-thumbnail').dblclick(function() {
                clickHandler(this);
            });

            $('.fm-body-item').mousedown(function() {
                top.FileManager.selectedFile = this;

                $('.fm-body-item').removeClass('fm-body-item-selected');
                $(this).addClass('fm-body-item-selected');

                $('button[name="remove"]').prop(
                    'disabled',
                    false
                );
            });

            $('.fm-body-thumbnail').mousedown(function() {
                top.FileManager.selectedFile = this;

                $('.fm-body-thumbnail-title').removeClass(
                    'fm-body-thumbnail-title-selected'
                );

                $(this).find('.fm-body-thumbnail-title').addClass(
                    'fm-body-thumbnail-title-selected'
                );

                $('button[name="remove"]').prop(
                    'disabled',
                    false
                );
            });

            // Truncate long names and add an elipsis.
            $('.fm-body-item-cell').dotdotdot({
                height: 30,
                callback: function(isTruncated, orgContent) {
                    if (isTruncated) {
                        this.title = orgContent[0].textContent;
                    }
                }
            });

            $('.fm-body-thumbnail-title').dotdotdot({ height: 17 });

            $('.fm-body-item, .fm-body-thumbnail').contextmenu(
                function(event) {
                    if (event.preventDefault) {
                        event.preventDefault();
                    }

                    top.FileManager.prototype.getConfig().then(function(settings) {
                        var contextMenu = $('<div id="fm-context-menu"></div>')
                            .css({
                                'top': event.clientY,
                                'left': event.clientX
                            });

                        /*html += '<div id="fm-context-menu-cut" class="fm-context-menu-item">' +
                                '<span class="fa fa-fw fa-scissors ' +
                                    'fm-context-menu-icon"></span>' +
                                '<p class="fm-context-menu-label">Knippen</p>' +
                            '</div>' +
                            '<div id="fm-context-menu-copy" class="fm-context-menu-item">' +
                                '<span class="fa fa-fw fa-files-o ' +
                                    'fm-context-menu-icon"></span>' +
                                '<p class="fm-context-menu-label">Kopi&euml;ren</p>' +
                            '</div>' +
                            '<div id="fm-context-menu-paste" class="fm-context-menu-item">' +
                                '<span class="fa fa-fw fa-clipboard ' +
                                    'fm-context-menu-icon"></span>' +
                                '<p class="fm-context-menu-label">Plakken</p>' +
                            '</div>' +
                            '<hr />';*/

                        var html = '';

                        html += '<div id="fm-context-menu-download" class="fm-context-menu-item">' +
                                '<span class="fa fa-fw fa-download ' +
                                    'fm-context-menu-icon"></span>' +
                                '<p class="fm-context-menu-label">Downloaden</p>' +
                            '</div>' +
                            '<hr />';

                        if (
                            settings.allowedFileTypes.images
                                .indexOf(
                                    event.target.parentElement
                                    .dataset.type
                            ) !== -1 ||
                            settings.allowedFileTypes.images
                                .indexOf(
                                    event.target.parentElement.parentElement
                                    .dataset.type
                            ) !== -1
                        ) {
                            html += '<div id="fm-context-menu-edit" class="fm-context-menu-item">' +
                                    '<span class="fa fa-fw fa-pencil-square-o ' +
                                        'fm-context-menu-icon"></span>' +
                                    '<p class="fm-context-menu-label">Bewerken</p>' +
                                '</div>' +
                                '<hr />';
                        }

                        html += '<div id="fm-context-menu-remove" class="fm-context-menu-item">' +
                                '<span class="fa fa-fw fa-times-circle ' +
                                    'fm-context-menu-icon"></span>' +
                                '<p class="fm-context-menu-label">Verwijderen</p>' +
                            '</div>';

                        if (html.length !== 0) {
                            contextMenu.html(html);

                            var contextMenuOverlay = $(
                                '<div id="fm-context-menu-overlay"></div>'
                            ).mousedown(function(event) {
                                switch (event.which) {
                                    case 1:
                                    case 2:
                                    case 3:
                                        $('#fm-context-menu').remove();
                                        $(this).remove();

                                        break;
                                    default:
                                        console.error(
                                            'Unknown mouse key: ' +
                                            event.which + '.'
                                        );
                                }
                            });

                            $('body').append(contextMenu, contextMenuOverlay);

                            // Make sure the context menu is inside the window.
                            var contextMenuRight =
                                event.clientX + contextMenu.outerWidth();

                            if (contextMenuRight > top.innerWidth) {
                                contextMenu.css(
                                    'left',
                                    top.innerWidth - contextMenu.outerWidth()
                                );
                            }

                            var contextMenuBottom =
                                event.clientY + contextMenu.outerHeight();

                            if (contextMenuBottom > top.innerHeight) {
                                contextMenu.css(
                                    'top',
                                    top.innerHeight - contextMenu.outerHeight()
                                );
                            }

                            if (top.FileManager.prototype.viewState == 'list') {
                                var dataset = event.target
                                    .parentElement.parentElement.dataset;
                            } else {
                                var dataset = event.target
                                    .parentElement.dataset;
                            }

                            $('.fm-context-menu-item').click(function() {
                                $('#fm-context-menu').remove();
                                $('#fm-context-menu-overlay').remove();
                            });

                            $('#fm-context-menu-edit').click(function() {
                                var width = top.innerWidth - 72 - getScrollbarWidth();
                                var height = top.innerHeight - 72;

                                (new Promise(function(resolve, reject) {
                                    top.tinymce.activeEditor.windowManager.open(
                                        {
                                            title: 'Bewerken',
                                            url: settings.installationPath +
                                                'dialogs/edit.php',
                                            width: width,
                                            height: height
                                        },
                                        {
                                            context: top,
                                            path: top.FileManager.prototype.path,
                                            dataset: dataset,
                                            resolve: resolve,
                                            reject: reject
                                        }
                                    );
                                })).then(function() {
                                    setClickContainerHandler();
                                }).catch(function(error) {
                                    console.error(error);
                                });
                            });

                            $('#fm-context-menu-remove').click(function() {
                                $('button[name="remove"]').click();
                            });

                            $('#fm-context-menu-download').click(function(event) {
                                var path = $('.fm-body-item-selected').data('path');

                                if (path === null) {
                                    path = $('.fm-body-thumbnail-title-selected')
                                        .parent().find('img').data('path');
                                }

                                if (path === null) {
                                    path = $('.fm-body-thumbnail-title-selected')
                                        .parent().data('path');
                                }

                                window.open(
                                    path,
                                    '_blank'
                                );
                            });
                        }
                    }).catch(function(error) {
                        console.error(error);
                    });
                }
            );
        }
    </script>
</body>
</html>
