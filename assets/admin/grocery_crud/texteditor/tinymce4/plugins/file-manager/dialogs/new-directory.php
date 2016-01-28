<?php
    require '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Nieuwe Map</title>

    <link rel="stylesheet" href="../css/main.min.css" />
    <link rel="stylesheet" href="../fonts/font-awesome/css/font-awesome.min.css" />

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

    <meta name="robots" content="noindex, nofollow" />
</head>
<body>

    <div id="fm-body-container" class="fm-gradient">
        <div id="fm-textbox-body-container">
            <div id="fm-textbox-body" class="fm-box">
                <label for="new-directory" class="fm-textbox-label">Naam:</label>
                <input id="new-directory" name="new-directory" type="text" class="fm-textbox-input" />
            </div>
        </div>
    </div>

    <div id="fm-footer" class="fm-box fm-gradient">
        <button name="cancel" type="button" class="fm-button fm-button-right fm-button-lonely">
            <span class="fm-no fa fa-fw fa-times"></span> Annuleren
        </button>
        <button name="new-directory" type="button" class="fm-button-primary fm-button-right fm-button-lonely fm-button-space-right">
            <span class="fm-yes fa fa-fw fa-check"></span> Map Aanmaken
        </button>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="../js/file-manager.min.js"></script>
    <script>
        'use strict';

        // Fix the main content container's height.
        $('#fm-body-container').css('height', window.innerHeight - 34);

        if (top.tinymce === undefined) {
            window.location.replace('<?= base_url(); ?>');
        } else {
            var fileManager = top.FileManager.prototype.getInstance();

            $('button[name="new-directory"]').click(function() {
                var directory = top.tinymce.activeEditor.windowManager.getParams().path +
                    '/' + $('#new-directory').val();

                top.FileManager.prototype.newDirectory(directory).then(function(value) {
                    if (value.error.code === 0) {
                        top.tinymce.activeEditor.windowManager.getParams().context
                            .FileManager.prototype.refreshFiles(window.document).then(
                                function() {
                                    top.tinymce.activeEditor.windowManager.getParams()
                                        .resolve();
                                    top.tinymce.activeEditor.windowManager.close();
                                }
                            ).catch(
                                function(error) {
                                    top.tinymce.activeEditor.windowManager.getParams()
                                        .reject(error.message);
                                    console.error(error);
                                }
                            );
                    } else {
                        top.tinymce.activeEditor.windowManager.alert(
                            value.error.message,
                            function() {
                                top.tinymce.activeEditor.windowManager.getParams()
                                    .reject(value.error.message);
                                top.tinymce.activeEditor.windowManager.close();
                            }
                        );
                    }
                }).catch(function(reason) {
                    top.tinymce.activeEditor.windowManager.alert(
                        reason,
                        function() {
                            top.tinymce.activeEditor.windowManager.getParams()
                                .reject(reason);
                            top.tinymce.activeEditor.windowManager.close();
                        }
                    );
                });
            });

            $('button[name="cancel"]').click(function() {
                top.tinymce.activeEditor.windowManager.getParams().reject(
                    'Directory creation cancelled.'
                );

                top.tinymce.activeEditor.windowManager.close();
            });
        }
    </script>
</body>
</html>
