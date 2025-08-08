<script setup>
import { computed, onBeforeUnmount, toRef } from "vue";
import { useEditor, EditorContent, BubbleMenu, FloatingMenu } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Highlight from "@tiptap/extension-highlight";
import TextAlign from "@tiptap/extension-text-align";
import { useField } from "vee-validate";

const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    name: String,
});

const modelValue = defineModel({
    type: String,
    default: "",
});

const field = toRef(props, "name");

const { meta, handleChange, handleBlur } = useField(field, undefined, {
    initialValue: modelValue,
});

const editorClass = computed(() => {
    return {
        "form-control": true,
        "is-invalid": meta.dirty && !meta.valid,
        "is-valid": meta.dirty && meta.valid,
    };
});

const editor = useEditor({
    content: modelValue.value,
    extensions: [
        StarterKit,
        TextAlign.configure({
            types: ["heading", "paragraph"],
        }),
        Highlight,
    ],
    onUpdate: ({ editor: evalue }) => {
        modelValue.value = evalue.getHTML();
        handleChange(modelValue.value);
    },
    onBlur: (event) => {
        handleBlur(event);
    },
    editorProps: {
        attributes: {
            class: "editor_no_border",
        },
    },
});

onBeforeUnmount(() => {
    editor.value.destroy();
});
</script>

<template>
    <div
        v-if="editor"
        class="editor"
    >
        <BubbleMenu
            class="bubble-menu"
            :tippy-options="{ duration: 100 }"
            :editor="editor"
        >
            <div class="dropdown">
                <a
                    id="edDropdown"
                    class="dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="javascript:void(0)"
                >
                    <i class="icon ion-md-brush" />
                </a>
                <div
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="edDropdown"
                >
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('bold') }"
                        @click="editor.chain().focus().toggleBold().run()"
                    >
                        Negrita
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('italic') }"
                        @click="editor.chain().focus().toggleItalic().run()"
                    >
                        Cursiva
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('highlight') }"
                        @click="editor.chain().focus().toggleHighlight().run()"
                    >
                        Resalte
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive({ textAlign: 'left' }) }"
                        @click="editor.chain().focus().setTextAlign('left').run()"
                    >
                        Alineado
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive({ textAlign: 'center' }) }"
                        @click="editor.chain().focus().setTextAlign('center').run()"
                    >
                        Centrado
                    </a>
                </div>
            </div>
        </BubbleMenu>
        <FloatingMenu
            class="floating-menu"
            :tippy-options="{ duration: 100 }"
            :editor="editor"
        >
            <div class="dropdown">
                <a
                    id="ed2Dropdown"
                    class="dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="javascript:void(0)"
                >
                    <i class="icon ion-md-color-wand" />
                </a>
                <div
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="ed2Dropdown"
                >
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('heading', { level: 1 }) }"
                        @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                    >
                        Titulo
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('heading', { level: 2 }) }"
                        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    >
                        Subtitulo
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('bulletList') }"
                        @click="editor.chain().focus().toggleBulletList().run()"
                    >
                        Lista 1
                    </a>
                    <a
                        href="javascript:void(0)"
                        class="dropdown-item"
                        :class="{ active: editor.isActive('orderedList') }"
                        @click="editor.chain().focus().toggleOrderedList().run()"
                    >
                        Lista 2
                    </a>
                </div>
            </div>
        </FloatingMenu>
        <label
            class="fw-medium mb-2"
            for="mEditor"
        >
            <slot />
        </label>
        <div :class="editorClass">
            <span class="opacity-50">{{ props.label }}</span>
            <EditorContent
                id="mEditor"
                class="editor__content mt-2"
                :editor="editor"
            />
        </div>
    </div>
</template>
