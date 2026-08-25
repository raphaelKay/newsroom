import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', ({ content = '' }) => ({
        editor: null,
        content: content,

        init() {
            this.editor = new Editor({
                element: this.$refs.element,
                extensions: [StarterKit],
                content: this.content,
                onUpdate: () => {
                    this.content = this.editor.getHTML();
                },
            });
        },

        toggleBold() { this.editor.chain().focus().toggleBold().run(); },
        toggleItalic() { this.editor.chain().focus().toggleItalic().run(); },
        toggleHeading(level) { this.editor.chain().focus().toggleHeading({ level }).run(); },
        toggleBulletList() { this.editor.chain().focus().toggleBulletList().run(); },
        toggleBlockquote() { this.editor.chain().focus().toggleBlockquote().run(); },

        isActive(type, opts = {}) {
            return this.editor ? this.editor.isActive(type, opts) : false;
        }
    }));
});