export default function () {
    const Mark = window.FilamentRichEditor.tiptap.core.Mark

    return Mark.create({
        name: 'fontSize',

        addAttributes() {
            return {
                size: {
                    default: null,
                    parseHTML: (element) => {
                        const size = element.style?.fontSize

                        return size ? size.trim() : null
                    },
                    renderHTML: (attributes) => {
                        if (!attributes.size) {
                            return {}
                        }

                        return {
                            style: `font-size: ${attributes.size}`,
                        }
                    },
                },
            }
        },

        parseHTML() {
            return [
                {
                    tag: 'span[style*="font-size"]',
                },
            ]
        },

        renderHTML({ HTMLAttributes }) {
            return ['span', HTMLAttributes, 0]
        },

        addCommands() {
            return {
                setFontSize:
                    (size) =>
                    ({ commands }) =>
                        commands.setMark(this.name, { size }),
                unsetFontSize:
                    () =>
                    ({ commands }) =>
                        commands.unsetMark(this.name),
            }
        },
    })
}
