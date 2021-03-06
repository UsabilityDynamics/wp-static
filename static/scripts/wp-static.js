!function($) {
    function debug() {
        "function" == typeof console.debug && console.debug.apply(console, arguments);
    }
    var AceHtmlEditor = function(config) {
        $.extend(this, config), this.$elem = this.element, this.element = this.$elem.attr("id"), 
        this.$container = this.container ? $(this.container) : this.$elem.parent(), this.contWd = this.$container.width(), 
        this.loaded = !1, this.tinymce = !!window.tinymce, this.onInit && this.onInit.call(this);
    };
    AceHtmlEditor.prototype = {
        load: function() {
            if (this.loaded) return !1;
            var self = this;
            this.$elem.hide(), this.insertEditor(), this.editor = ace.edit(this.aceId), this.$editor = $("#" + this.aceId), 
            this.setEditorProps(), this.setEditorContent(), this.containerResizable(), this.editor.on("change", function() {
                self.synchronize.apply(self);
            }), this.editor.resize(!0), this.loaded = !0, this.onLoaded && this.onLoaded.call(this);
        },
        insertEditor: function() {
            $('<div id="' + this.aceId + '"></div>').css({
                left: 0,
                top: 0,
                bottom: 0,
                right: 0,
                zIndex: 1
            }).insertAfter(this.$elem);
        },
        setEditorProps: function() {
            this.editor.setTheme("ace/theme/" + this.theme), this.editor.getSession().setMode("ace/mode/html"), 
            this.editor.getSession().setUseSoftTabs(!0), this.editor.getSession().setTabSize(2), 
            this.editor.getSession().setWrapLimitRange();
        },
        setEditorContent: function() {
            this.editor.getSession().setValue(this.$elem.val());
        },
        containerResizable: function() {
            var self = this;
            this.$container.resizable({
                handles: "s"
            }).css({
                position: "relative",
                height: this.defaultHt,
                minHeight: "400px"
            }).on("resize.aceEditorResize", function() {
                self.editor.resize(!0);
            });
        },
        synchronize: function() {
            var val = this.editor.getValue();
            this.$elem.val(val), this.tinymce && tinyMCE.get(this.element) && tinyMCE.get(this.element).setContent(val);
        },
        destroy: function() {
            return this.loaded ? (this.$editor.remove(), this.editor.destroy(), this.$container.resizable("destroy").off("resize.aceEditorResize").css({
                height: ""
            }), this.$elem.show(), this.loaded = !1, void (this.onDestroy && this.onDestroy.apply(this, arguments))) : !1;
        }
    }, $.fn.AceHtmlEditor = function(option, value) {
        debug("wp-static", "AceHtmlEditor"), option = option || null;
        var data = $(this).data("AceEditor");
        if (data && "string" == typeof option && data[option]) data[option](value || null); else {
            if (!data) return this.each(function() {
                $(this).data("AceEditor", new AceHtmlEditor($.extend({
                    element: $(this),
                    aceId: "ace-editor",
                    theme: "wordpress",
                    defaultHt: "600px",
                    container: !1
                }, option))), $(this).attr("data-editor-status", "ready");
            });
            $.error('Method "' + option + '" does not exist on AceEditor!');
        }
    }, jQuery(document).ready(function($) {
        function ajaxCallback(response) {
            debug("wp-static", "The server responded", response);
            var ajaxMessage = jQuery("h2 span.ajax-message");
            response.ok ? ajaxMessage.removeClass("success").addClass("error") : ajaxMessage.removeClass("error").addClass("success"), 
            ajaxMessage.show().text(response.message || "Unknown error.").fadeIn(400, function() {
                debug("wp-static", "Callback message displayed.");
            });
        }
        function saveHtml() {
            return debug("wp-static", "Saving HTML."), jQuery.post(ajaxurl, {
                action: "static_save",
                data: {
                    data: editor.getValue(),
                    form: jQuery(this).serialize()
                }
            }, ajaxCallback), !1;
        }
        console.debug("wp-static", "Ready.");
        var editor = {};
        $("#static-html").AceHtmlEditor({
            setEditorContent: function() {
                var value = this.$elem.val();
                editor = this.editor, this.editor.getSession().setValue(value);
            },
            onInit: function() {
                this.load();
            },
            onLoaded: function() {},
            onDestroy: function() {}
        }), $("#static-html-form").submit(saveHtml);
    });
}(jQuery);