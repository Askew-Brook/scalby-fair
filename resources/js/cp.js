const bard = window.Statamic.$bard;
const { Mark } = bard.tiptap.core;
const palette = {
    red: { label: 'Barn red', hex: '#74332d' },
    blue: { label: 'Logo blue', hex: '#205185' },
    green: { label: 'Hedge green', hex: '#354d31' },
    gold: { label: 'Harvest gold', hex: '#765511' },
};
const mark = 'editorialTextColour';

bard.addExtension(() => Mark.create({
    name: mark,

    addAttributes() {
        return { colour: { default: null } };
    },

    parseHTML() {
        return [{
            tag: 'span[data-editorial-colour]',
            getAttrs: element => {
                const colour = element.dataset.editorialColour;
                return Object.hasOwn(palette, colour) ? { colour } : false;
            },
        }];
    },

    renderHTML({ HTMLAttributes }) {
        const { colour } = HTMLAttributes;
        return Object.hasOwn(palette, colour)
            ? ['span', { 'data-editorial-colour': colour, style: `color: ${palette[colour].hex}; background: #fffdf7` }, 0]
            : ['span', 0];
    },
}));

bard.buttons((_buttons, button) => Object.entries(palette).map(([colour, { label, hex }]) => button({
    name: `text_${colour}`,
    text: label,
    html: `<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><circle cx="10" cy="10" r="6" fill="${hex}" stroke="#fffdf7" /></svg>`,
    activeName: mark,
    args: { colour },
    command: (editor, attrs) => editor.chain().focus().toggleMark(mark, attrs).run(),
})));
