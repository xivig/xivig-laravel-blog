/**
 * XIVIG Maison Gazette - Admin WYSIWYG Editor Module
 * ==============================================================================
 * Shared-hosting compatible rich text editing engine.
 * Supports TinyMCE 7 (with CDN) and Summernote-BS5 (local offline bundle).
 * Provides drag-and-drop image uploads, dark/light theme awareness, and clean HTML.
 * ==============================================================================
 */

(function(window, document, $) {
    'use strict';

    const MaisonWYSIWYG = {
        uploadEndpoint: '/admin/media/upload',

        getCSRFToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        getCurrentTheme: function() {
            return document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        },

        /**
         * Initialize all WYSIWYG editors on the page
         * @param {string} selector 
         */
        init: function(selector = '.wysiwyg-editor') {
            const elements = document.querySelectorAll(selector);
            if (!elements.length) return;

            // Check if TinyMCE 7 is present and accessible
            if (window.tinymce && typeof window.tinymce.init === 'function') {
                this.initTinyMCE(selector);
            } else if ($ && typeof $.fn.summernote === 'function') {
                this.initSummernote(selector);
            } else {
                console.warn('MaisonWYSIWYG: Neither TinyMCE nor Summernote was detected.');
            }
        },

        /**
         * Initialize Summernote-BS5 (Local offline-first mode)
         */
        initSummernote: function(selector) {
            const self = this;
            const $targets = $(selector);

            $targets.each(function() {
                const $textarea = $(this);
                if ($textarea.data('summernote-initialized')) return;

                const minHeight = parseInt($textarea.attr('data-editor-height') || 380, 10);
                const placeholder = $textarea.attr('placeholder') || 'Compose rich editorial content...';

                $textarea.summernote({
                    placeholder: placeholder,
                    tabsize: 2,
                    height: minHeight,
                    minHeight: 250,
                    maxHeight: null,
                    focus: false,
                    dialogsInBody: true,
                    dialogsFade: true,
                    disableDragAndDrop: false,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            for (let i = 0; i < files.length; i++) {
                                self.uploadMediaFile(files[i], function(imageUrl) {
                                    $textarea.summernote('insertImage', imageUrl, function($img) {
                                        $img.addClass('img-fluid rounded-3 my-3 shadow-xs');
                                    });
                                });
                            }
                        },
                        onChange: function(contents) {
                            $textarea.val(contents);
                        }
                    }
                });

                $textarea.data('summernote-initialized', true);
            });
        },

        /**
         * Initialize TinyMCE 7 (CDN or local bundle)
         */
        initTinyMCE: function(selector) {
            const self = this;
            const isDark = this.getCurrentTheme() === 'dark';

            window.tinymce.init({
                selector: selector,
                height: 450,
                menubar: true,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                         'alignleft aligncenter alignright alignjustify | ' +
                         'bullist numlist outdent indent | link image media table | ' +
                         'removeformat code fullscreen',
                skin: isDark ? 'oxide-dark' : 'oxide',
                content_css: isDark ? 'dark' : 'default',
                images_upload_handler: function(blobInfo, progress) {
                    return new Promise(function(resolve, reject) {
                        const file = blobInfo.blob();
                        self.uploadMediaFile(file, function(imageUrl) {
                            resolve(imageUrl);
                        }, function(err) {
                            reject(err);
                        });
                    });
                },
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });
        },

        /**
         * Asynchronously upload an image to the Media Library
         */
        uploadMediaFile: function(file, onSuccess, onError) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', this.getCSRFToken());

            fetch(this.uploadEndpoint, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('Upload failed with status ' + res.status);
                return res.json();
            })
            .then(data => {
                const url = data.url || data.location;
                if (data.success && url) {
                    if (typeof onSuccess === 'function') onSuccess(url);
                } else {
                    const msg = data.message || 'Image upload rejected by server.';
                    if (typeof onError === 'function') onError(msg);
                    else alert(msg);
                }
            })
            .catch(err => {
                console.error('MaisonWYSIWYG Upload Error:', err);
                if (typeof onError === 'function') onError(err.message);
                else alert('Failed to upload image: ' + err.message);
            });
        }
    };

    // Auto-initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        MaisonWYSIWYG.init();
    });

    window.MaisonWYSIWYG = MaisonWYSIWYG;

})(window, document, window.jQuery || window.$);
