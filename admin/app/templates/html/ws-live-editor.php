<style type="text/css">
    *[data-live-editor] {border: solid 3px #447df7;}
</style>
<script type="text/javascript">
    if(typeof(window.jQuery) == 'undefined') {
        var j = document.createElement("script"),
        head = document.getElementsByTagName('head')[0]
        j.setAttribute('type', "text/javascript")
        j.setAttribute('src', './js/jquery-2.2.0.min.js')
        head.insertBefore(j,head.firstChild)
    }
    var j = document.createElement("script"),
    head = document.getElementsByTagName('head')[0]
    j.setAttribute('type', "text/javascript")
    j.setAttribute('src', './includes/plugins/CKeditor/ckeditor.js')
    head.insertBefore(j,head.firstChild)
    setTimeout(function() {
        var element = document.querySelector("body");
        var elChild = document.createElement('div');
        elChild.innerHTML = '<div style="position: relative;background-color: #ebf0f7;text-align: center;color: #7d86d0;font-family: Myriad Pro, Arial,sans-serif;z-index: 10000;font-size: 15px;height: 44px;border-bottom: solid 5px #2579d0;padding-top: 10px;">Você está no modo de edição, <a href="/admin" style="color: #2579d0;font-weight: 900;">» Clique aqui</a> para voltar ao painel<div style="position: relative;float: right;background-color:#d05225;color: #FFF;padding: 7px 20px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;margin: -6px 2px;cursor: pointer;" id="closeLiveEditor">Sair do modo designer</div></div>';
        element.insertBefore(elChild, element.firstChild);
        CKEDITOR.dtd.$editable.editor = 1
        CKEDITOR.plugins.registered['save'] = {
            init: function(editor) {
                var command = editor.addCommand('save', {
                    modes: {wysiwyg: 1,source: 1},
                    exec: function(editor) {

                        var EDITOR 				= jQuery("[data-live-editor][title='" + editor.title + "']")
                        var attrLiveEditor 		= EDITOR.attr('data-live-editor').split(',');
                        var LiveEditorColum 	= attrLiveEditor[0];
                        var LiveEditorToken 	= attrLiveEditor[1];
                        var LiveEditorContent 	= EDITOR.html();
                        console.log("Live Editor:")
                        jQuery.ajax({
                            type: "POST",
                            url: "./app/ws-modules/ws-model-tool/functions.php",
                            data: {
                                "function": "SaveLiveEditor",
                                "colum": LiveEditorColum,
                                "token": LiveEditorToken,
                                "content": encodeURIComponent(LiveEditorContent)
                            },
                            async: true,
                            success: function(data) {
                                if(data == false) {
                                    alert("Falha ao gravar a coluna '" + LiveEditorColum + "' ");
                                } else {
                                    alert('"'+ LiveEditorColum + '" salva com sucesso!');
                                }
                            }
                        });



                    }
                });
                editor.ui.addButton('Save', {
                    label: 'Save',
                    command: 'save'
                });
            }
        }

        jQuery('*[data-live-editor]').attr('contenteditable', 'true').ckeditor({
            disableAutoInline: false,
            forcePasteAsPlainText: true,
            fillEmptyBlocks: false,
            basicEntities: false,
            entities_greek: false,
            entities_latin: false,
            enterMode: 2,
            entities_additional: '',
            toolbarStartupExpanded: 0,
            toolbarCanCollapse: 1,
            tabSpaces: 0,
            entities: 0,
            forceSimpleAmpersand: 1,
            allowedContent: true,
            entities_additional: "",
            toolbar: [

                {
                    name: 'document',
                    items: ['Source', 'Save', 'DocProps', 'Preview', 'Print', '-', 'Templates']
                }, {
                    name: 'editing',
                    items: ['SelectAll']
                }, {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                }, {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']
                }, {
                    name: 'links',
                    items: ['Link', 'Unlink', 'Anchor']
                }, {
                    name: 'styles',
                    items: ['FontSize']
                }, {
                    name: 'colors',
                    items: ['TextColor', 'BGColor']
                }, {
                    name: 'tools',
                    items: ['Maximize', 'ShowBlocks']
                }
            ]


        })
        jQuery("#closeLiveEditor").bind("click tap press", function() {
            $.ajax({
                type: "POST",
                url: "./app/ws-modules/ws-tools/functions.php",
                data: {"function": "detroyEditorActive"},
                async: true,
                success: function(data) {location.reload();}
            });
        })


    }, 1500);
</script>