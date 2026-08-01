import { CKEditor } from '@ckeditor/ckeditor5-react';
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

export default function RichEditor({ value, onChange, placeholder }) {
    return (
        <div className="rich-editor">
            <CKEditor
                editor={ClassicEditor}
                data={value}
                config={{
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
                }}
                onChange={(_event, editor) => onChange(editor.getData())}
            />
        </div>
    );
}
