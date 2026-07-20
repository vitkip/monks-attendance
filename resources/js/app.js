import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Strikethrough,
    Link,
    List,
    BlockQuote,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

document.addEventListener('alpine:init', () => {
    Alpine.data('richEditor', (initialContent = '', placeholder = '') => {
        // Kept outside the returned reactive object on purpose: Alpine deep-proxies
        // every property of x-data state, and CKEditor's Emitter mixin defines a
        // non-configurable `_events` property that violates the Proxy invariants
        // once wrapped, throwing "'get' on proxy" errors. A closure variable never
        // gets proxied.
        let editor = null;

        return {
            async init() {
                editor = await ClassicEditor.create(this.$refs.body, {
                    licenseKey: 'GPL',
                    plugins: [Essentials, Paragraph, Heading, Bold, Italic, Strikethrough, Link, List, BlockQuote],
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'strikethrough', '|',
                        'bulletedList', 'numberedList', 'blockQuote', '|',
                        'link', '|',
                        'undo', 'redo',
                    ],
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        ],
                    },
                    placeholder,
                    initialData: initialContent,
                });

                editor.model.document.on('change:data', () => this.syncToHiddenInput());

                // Sync once on mount so a freshly-opened "create" modal starts empty/valid.
                this.syncToHiddenInput();
            },

            syncToHiddenInput() {
                const html = editor.getData();
                const input = this.$refs.hiddenInput;
                if (input.value === html) return;
                input.value = html;
                input.dispatchEvent(new Event('input'));
            },

            destroy() {
                editor?.destroy();
            },
        };
    });
});
