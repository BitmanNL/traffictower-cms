/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package   CMS\Core\Admin\FileManager
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2015-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

'use strict';

FileManager.prototype.instance;
FileManager.prototype.getConfigPromise;
FileManager.prototype.loadGetScrollbarWidthPromise;
FileManager.prototype.loadTinymcePromise;
FileManager.prototype.manualBrowsePromise;
FileManager.prototype.path = '';
FileManager.prototype.viewState = '';
FileManager.prototype.files = [];
FileManager.prototype.standalone = false;
FileManager.prototype.settings = {};

/**
 * Construct an object for file interactions like uploading, browsing, deleting,
 * etc.
 */
function FileManager()
{
}

/**
 * Get the singleton instance of the FileManager.
 *
 * @return FileManager The FileManager singleton instance.
 */
FileManager.prototype.getInstance = function() {
    if (FileManager.prototype.instance === undefined) {
        FileManager.prototype.instance = new FileManager();
        top.document.FileManager = FileManager.prototype.instance;
    }

    return FileManager.prototype.instance;
};

/**
 * Load the getScrollbarWidth function.
 *
 * @return Promise A promise which is fulfilled when the getScrollbarWidth
 * function has been defined.
 */
FileManager.prototype.loadGetScrollbarWidth = function() {
    if (FileManager.prototype.loadGetScrollbarWidthPromise) {
        return FileManager.prototype.loadGetScrollbarWidthPromise;
    }

    FileManager.prototype.loadGetScrollbarWidthPromise = new Promise(
        function(resolve, reject) {
            FileManager.prototype.getConfig().then(function(settings) {
                // Load the getScrollbarWidth() helper function.
                if (typeof getScrollbarWidth === 'undefined') {
                    var script = top.document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = settings.installationPath +
                        'js/get-scrollbar-width.min.js';
                    script.onload = function() {
                        resolve(getScrollbarWidth);
                    };

                    top.document.getElementsByTagName('body')[0].appendChild(script);
                }
            }).catch(function(error) {
                console.error(error);

                reject(error);
            });
        }
    );

    return FileManager.prototype.loadGetScrollbarWidthPromise;
};

/**
 * Inject a hidden TinyMCE editor to satisfy dependencies.
 *
 * @param function callback Optional callback to execute upon completion.
 *
 * @return Promise A promise which is fulfilled when the TinyMCE assets have
 *                 been loaded.
 *
 * @internal
 */
FileManager.prototype.loadTinymce = function(callback, context) {
    if (FileManager.prototype.loadTinymcePromise) {
        return FileManager.prototype.loadTinymcePromise;
    }

    if (!context) {
        context = window;
    }

    FileManager.prototype.loadTinymcePromise = new Promise(
        function(resolve, reject) {
            FileManager.prototype.getConfig().then(function(settings) {
                var initTinymce = function() {
                    var tinymceBaseUrl = settings.tinymceUrl
                        .substr(
                            0,
                            settings.tinymceUrl.lastIndexOf('/')
                        );

                    context.tinymce.baseURL = tinymceBaseUrl;
                    context.tinymce.suffix = '.min';
                    context.document.cms_tinymce_minimal.auto_focus = 'file-manager';

                    context.document.cms_tinymce_minimal.setup = function(editor) {
                        editor.on('init', function() {
                            if (typeof callback === 'function') {
                                callback();
                            }

                            console.debug('TinyMCE editor initialized.');

                            resolve();
                        });
                    };

                    context.tinymce.dom.Event.domLoaded = true;

                    context.tinymce.init(
                        context.document.cms_tinymce_minimal
                    );
                };

                if (typeof context.tinymce === 'undefined') {
                    $.getScript(
                        settings.tinymceUrl,
                        function() {
                            initTinymce();

                            /*if (typeof callback === 'function') {
                                callback();
                            }*/
                        }
                    );
                } else {
                    resolve();
                }
            }).catch(function(error) {
                console.error(error);

                reject(error.message);
            });
        }
    );

    return FileManager.prototype.loadTinymcePromise;
};

/**
 * Display a file browser inside TinyMCE.
 *
 * @param string fieldName The id of the form element that the browser should
 *                         insert its URL into.
 * @param string url       Contains the URL value that is currently inside the
 *                         field.
 * @param string type      Contains what type of browser to present. This value
 *                         can be "file", "image", or "flash" depending on what
 *                         dialogue is calling the function.
 * @param object win       A reference to the dialog/window that executes the
 *                         function.
 *
 * @return void
 *
 * @internal
 */
FileManager.prototype.browse = function(fieldName, url, type, win) {
    FileManager.prototype.loadGetScrollbarWidth().then(function(getScrollbarWidth) {
        FileManager.prototype.getConfig().then(function(settings) {
            FileManager.prototype.loadTinymce().then(function() {
                top.tinymce.activeEditor.windowManager.open(
                    {
                        title: 'Bestanden',
                        url: settings.installationPath +
                            'dialogs/browse.php',
                        width: top.innerWidth - 72 - getScrollbarWidth(),
                        height: top.innerHeight - 72
                    },
                    {
                        fieldName: fieldName
                    }
                );
            }).catch(function(error) {
                console.error(error);
            });
        }).catch(function(error) {
            console.error(error);
        });
    }).catch(function(error) {
        console.error(error);
    });
};

/**
 * Display a file browser manually (outside TinyMCE).
 *
 * @param string fieldName  The id of the form element that the browser should
 *                          insert its URL into.
 * @param object data       Arbitrary data to pass along.
 * @param function callback Optional callback to be executed upon completion.
 * @param bool force        Forces the opening of the file manager, instead of
 *                          returning its promise before opening a new window.
 * @param bool quiet        If set to true FileManager.prototype.standalone will
 *                          always remain false.
 *
 * @return Promise A promise which will be fulfilled when the browser has been
 *                 opened.
 *
 * @api
 */
FileManager.prototype.manualBrowse = function(
    fieldName,
    data,
    callback,
    force,
    quiet
) {
    if (!quiet) {
        FileManager.prototype.standalone = true;
    } else {
        FileManager.prototype.standalone = false;
    }

    if (!force) {
        force = false;
    } else {
        force = true;
    }

    if (FileManager.prototype.manualBrowsePromise && !force) {
        return FileManager.prototype.manualBrowsePromise;
    }

    FileManager.prototype.manualBrowsePromise = new Promise(
        function(resolve, reject) {
            FileManager.prototype.loadGetScrollbarWidth().then(function(getScrollbarWidth) {
                FileManager.prototype.getConfig().then(function(settings) {

                    if (data && data.extensions) {
                        settings.allowedFileExtensions =
                            data.extensions.split(',');
                    }

                    FileManager.prototype.loadTinymce().then(function() {
                        // Add a setTimeout event to prevent the file manager
                        // from appearing halfway across the page. It works by
                        // creating a new call stack which will wait until the
                        // previous call stack has been completed, and waiting
                        // 500 milliseconds after that.
                        window.setTimeout(
                            function() {
                                top.tinymce.activeEditor.windowManager.open(
                                    {
                                        title: 'Bestanden',
                                        url: settings.installationPath +
                                            'dialogs/browse.php',
                                        width: top.innerWidth - 72 - getScrollbarWidth(),
                                        height: top.innerHeight - 72
                                    },
                                    {
                                        fieldName: fieldName,
                                        callback: callback
                                    }
                                );

                                $('.mce-close').click(function() {
                                    if (top.document.referrer !== '') {
                                        top.window.location = top.document.referrer;
                                    } else {
                                        top.window.location = top.document.cms_base_url;
                                    }
                                });
                            },
                            500
                        );

                        resolve();
                    });
                });
            }).catch(function(error) {
                console.error(error);

                reject(error.message);
            });
        }
    );

    return FileManager.prototype.manualBrowsePromise;
};

/**
 * Create a new directory.
 *
 * @param string directory The path to the directory you wish to create,
 *                         relative to the upload directory.
 *
 * @return Promise A promise which will be fulfilled when the directory has been
 *                 created.
 */
FileManager.prototype.newDirectory = function(directory) {
    return new Promise(function(resolve, reject) {
        FileManager.prototype.getConfig().then(function(settings) {
            var postData = {};

            postData['directory'] = directory;
            postData[settings.csrf.tokenName] = settings.csrf.hash;

            $.ajax({
                data: postData,
                dataType: 'json',
                method: 'POST',
                url: settings.installationPath +
                    'ajax/new-directory.php',
                success: function(data) {
                    if (data.error.code === 0) {
                        resolve(data);
                    } else {
                        reject(
                            data.error.message
                        );
                    }
                },
                error: function() {
                    reject(
                        'The XHR request to "' +
                        settings.installationPath +
                        'new-directory.php" failed.'
                    );
                }
            });
        }).catch(function(error) {
            console.error(error);

            reject(error.message);
        });
    });
};

/**
 * Remove a file or directory.
 *
 * @param string filePath   The path of the file, relative to the upload
 *                          directory.
 * @param function callback The callback to execute upon completion.
 * @param object context    The context in which to execute.
 */
FileManager.prototype.removeFile = function(filePath, callback, context) {
    if (filePath === undefined) {
        throw 'FileManager.removeFile(): filePath is undefined.';
    }

    if (callback === undefined) {
        throw 'FileManager.removeFile(): callback is undefined.';
    }

    if (!context) {
        context = window.document;
    }

    FileManager.prototype.getConfig().then(function(settings) {
        var postData = {};

        postData['filePath'] = filePath;
        postData[settings.csrf.tokenName] = settings.csrf.hash;

        $.ajax({
            data: postData,
            dataType: 'json',
            method: 'POST',
            url: settings.installationPath +
                'ajax/remove-file.php',
            success: function(data) {
                if (data.error.code === 0) {
                    FileManager.prototype.refreshFiles(context)
                        .then(callback)
                        .catch(function(error) {
                            console.error(error);
                        });
                } else {
                    top.tinymce.activeEditor.windowManager.alert(
                        data.error.message
                    );
                }
            },
            error: function(error) {
                top.tinymce.activeEditor.windowManager.alert(
                    'Er is een fout opgetreden. ' +
                    'Probeer het later alstublieft opnieuw.'
                );

                console.error(error);
            }
        });
    }).catch(function(error) {
        console.error(error);
    });
};

/**
 * Refresh the file listing.
 *
 * @param object context The context in which to execute.
 *
 * @return Promise A promise which will be fulfilled when the files and the html
 *                have been updated.
 */
FileManager.prototype.refreshFiles = function(context) {
    if (!context) {
        context = window.document;
    }

    return new Promise(function(resolve, reject) {
        top.FileManager.prototype.getConfig().then(function(settings) {
            switch (top.FileManager.prototype.viewState) {
                case 'list':
                    top.FileManager.prototype.viewFileList(undefined, context)
                        .then(resolve)
                        .catch(reject);

                    break;
                case 'thumbnails':
                    top.FileManager.prototype.viewFileThumbnails(undefined, context)
                        .then(resolve)
                        .catch(reject);

                    break;
                default:
                    if (settings.defaultFileView === 'thumbnails') {
                        top.FileManager.prototype.viewFileThumbnails(undefined, context)
                            .then(resolve)
                            .catch(reject);
                    } else {
                        top.FileManager.prototype.viewFileList(undefined, context)
                            .then(resolve)
                            .catch(reject);
                    }

                    top.FileManager.prototype.viewState = settings.defaultFileView;

                    break;
            }
        }).catch(function(error) {
            console.error(error);

            reject(error.message);
        });
    });
};

/**
 * View the file listing as a vertical list, showing the file names, sizes,
 * types and last modification times.
 *
 * @param bool update    Retrieve the files again or not.
 * @param object context The context in which to execute.
 *
 * @return Promise A promise which will be fulfilled when the html has been
 *                 updated.
 */
FileManager.prototype.viewFileList = function(update, context) {
    if (update === undefined) {
        update = true;
    }

    if (!context) {
        context = window.document;
    }

    var viewFileList = function() {
        top.FileManager.prototype.viewState = 'list';

        var files = top.FileManager.prototype.files;
        var html = '';
        var icon = '';
        var extension = '';

        for (var key in files) {
            var icon = top.FileManager.prototype.fileTypeToIconClass(
                files[key].type
            );

            html += '<tr class="fm-body-item" ' +
                        'data-path="' + files[key].filePath + '" ' +
                        'data-type="' + files[key].type + '" ' +
                        'data-name="' + files[key].name + '">' +
                    '<td class="fm-body-item-cell fm-body-item-cell-icon">' +
                        '<span class="fa fa-fw fa-' + icon + '" /></span>' +
                    '</td>' +
                    '<td class="fm-body-item-cell"><div>' +
                        files[key].name +
                    '</div></td>' +
                    '<td class="fm-body-item-cell">' +
                        files[key].size +
                    '</td>' +
                    '<td class="fm-body-item-cell">';

            if (files[key].type === 'dir') {
                html += 'map';
            } else {
                html += files[key].type.substr(files[key].type.indexOf('/') + 1);
            }

            html += '</td>' +
                    '<td class="fm-body-item-cell">' +
                        files[key].modified +
                    '</td>' +
                '</tr>';
        }

        // Add the rest of the table.
        $('#fm-body', context).html(
            '<table id="fm-body-item-container">' +
                '<thead class="fm-gradient">' +
                    '<th id="fm-body-item-icon" scope="col"></th>' +
                    '<th id="fm-body-item-name" scope="col">Naam</th>' +
                    '<th id="fm-body-item-size" scope="col">Grootte</th>' +
                    '<th id="fm-body-item-type" scope="col">Type</th>' +
                    '<th id="fm-body-item-modified" scope="col">Laatst aangepast</th>' +
                '</thead>' +
                '<tbody>' + html + '</tbody>' +
            '</table>'
        );
    };

    return new Promise(function(resolve, reject) {
        if (update) {
            top.FileManager.prototype.getFiles().then(function(files) {
                viewFileList();
                resolve(files);
            }).catch(reject);
        } else {
            viewFileList();
            resolve();
        }
    });
};

/**
 * View the file listing as a grid of thumbnails.
 *
 * @param bool update    Retrieve the files again or not.
 * @param object context The context in which to execute.
 *
 * @return Promise A promise which will be fulfilled when the html has been
 *                 updated.
 */
FileManager.prototype.viewFileThumbnails = function(update, context) {
    if (update === undefined) {
        update = true;
    }

    if (!context) {
        context = window.document;
    }

    var viewFileThumbnails = function() {
        top.FileManager.prototype.getConfig().then(function(settings) {
            top.FileManager.prototype.viewState = 'thumbnails';

            var files = top.FileManager.prototype.files;

            var html = '';
            var item = '';

            for (var key in files) {
                if (
                    settings.allowedFileTypes.images
                        .indexOf(files[key].type) !==
                    -1
                ) {
                    // Image thumbnail.
                    item = '<img src="' + files[key].thumbnailPath + '?t=' + (new Date()).getTime().toString() + '" ' +
                            'alt="' + files[key].name + '" ' +
                            'title="' + files[key].name + '" ' +
                            'class="fm-body-thumbnail-image" ' +
                            'data-path="' + files[key].filePath + '" />';
                } else {
                    // File/directory icon.
                    item = '<div title="' + files[key].name + '" ' +
                                'class="fm-body-thumbnail-image">' +
                            '<span class="fa fa-fw fa-' +
                                top.FileManager.prototype.fileTypeToIconClass(files[key].type) +
                                '"></span>' +
                        '</div>';
                }

                html += '<div class="fm-body-thumbnail" ' +
                                'data-path="' + files[key].filePath + '" ' +
                                'data-name="' + files[key].name + '" ' +
                                'data-type="' + files[key].type + '">' +
                            item +
                            '<p title="' + files[key].name + '" class="fm-body-thumbnail-title">' +
                                files[key].name +
                            '</p>' +
                    '</div>';
            }

            $('#fm-body', context).html(
                '<div class="fm-box">' +
                    html +
                '</div>'
            );
        }).catch(function(error) {
            console.error(error);
        });
    };

    return new Promise(function(resolve, reject) {
        if (update) {
            top.FileManager.prototype.getFiles().then(function(value) {
                viewFileThumbnails();
                resolve(value);
            }).catch(reject);
        } else {
            viewFileThumbnails();
            resolve();
        }
    });
};

/**
 * Retrieve a list of the available configuration items.
 *
 * @return Promise A promise which will be fulfilled when the configuration has
 *                 been loaded.
 */
FileManager.prototype.getConfig = function() {
    if (FileManager.prototype.getConfigPromise) {
        return FileManager.prototype.getConfigPromise;
    }

    FileManager.prototype.getConfigPromise = new Promise(function(resolve, reject) {
        // Load the configuration and inject it into FileManager.prototype.settings.
        // Start by finding the installation path.
        var scripts = top.document.getElementsByTagName('script');
        var scriptsLength = scripts.length;

        while (scriptsLength--) {
            var knownPathPosition = scripts[scriptsLength].src.indexOf(
                'file-manager/js/file-manager.min.js'
            );

            if (scripts[scriptsLength].src && knownPathPosition > -1) {
                // Add the length of the directory name "file-manager" at the end
                // (+1 for trailing slash).
                FileManager.prototype.settings.installationPath =
                    scripts[scriptsLength].src.substr(0, knownPathPosition + 13);

                break;
            }
        }

        // Retrieve the configuration stored in config.php.
        $.ajax({
            dataType: 'json',
            method: 'GET',
            url: FileManager.prototype.settings.installationPath +
                'ajax/get-config.php',
            success: function(data) {
                // Keep installationPath in the settings object.
                data.installationPath = FileManager.prototype.settings.installationPath;
                FileManager.prototype.settings = data;

                resolve(data);
            },
            error: function() {
                console.error(
                    'The XHR request to "' +
                    FileManager.prototype.settings.installationPath +
                    'ajax/get-config.php" failed.'
                );

                reject(
                    'The XHR request to "' +
                    FileManager.prototype.settings.installationPath +
                    'ajax/get-config.php" failed.'
                );
            }
        });
    });

    return FileManager.prototype.getConfigPromise;
};

/**
 * Retrieve a list of the available files from the server.
 *
 * @return Promise A promise which is fulfilled when the files have been found.
 */
FileManager.prototype.getFiles = function() {
    return new Promise(function(resolve, reject) {
        FileManager.prototype.getConfig().then(
            function(settings) {
                var postData = {};

                postData['path'] = FileManager.prototype.path;
                postData[settings.csrf.tokenName] = settings.csrf.hash;

                $.ajax({
                    data: postData,
                    dataType: 'json',
                    method: 'POST',
                    url: settings.installationPath +
                        'ajax/get-files.php',
                    success: function(data) {
                        if (data.error.code === 0) {
                            FileManager.prototype.files = data.files;
                            resolve(data.files);
                        } else {
                            reject(data.error.message);
                        }
                    },
                    error: function() {
                        reject(
                            'The XHR request to "' +
                            settings.installationPath +
                            'get-files.php" failed.'
                        );
                    }
                });
            },
            function(reason) {
                console.error(reason);
            }
        ).catch(function(error) {
            console.error(error);
        });
    });
};

FileManager.prototype.fileTypeToIconClass = function(type) {
    var icon = '';

    switch (type) {
        case '':
        case 'dir':
            icon = 'folder-o';

            break;
        case 'link':
            icon = 'external-link';

            break;
        case 'application/msword':
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            icon = 'file-word-o';

            break;
        case 'application/pdf':
        case 'application/x-bzpdf':
        case 'application/x-gzpdf':
        case 'application/x-pdf':
            icon = 'file-pdf-o';

            break;
        case 'application/zip':
            icon = 'file-archive-o';

            break;
        case 'image/gif':
        case 'image/jpeg':
        case 'image/png':
            icon = 'file-image-o';

            break;
        case 'text/html':
            icon = 'file-code-o';

            break;
        case 'text/plain':
            icon = 'file-text-o';

            break;
        default:
            icon = 'file-o';

            break;
    }

    return icon;
};
