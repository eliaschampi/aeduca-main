import { ref, computed } from "vue";

/**
 * Modern lightweight text editor composable
 * Provides simple HTML formatting with modern APIs
 * Mimics execCommand simplicity while being future-proof
 */
export function useSimpleEditor(initialContent = "") {
    const content = ref(initialContent);
    const editorRef = ref(null);

    /**
     * Core formatting function that handles all transformations
     * Provides execCommand-like simplicity with modern implementation
     */
    const applyFormat = (command, value) => {
        const selection = window.getSelection();
        if (!selection || selection.rangeCount === 0) return;

        const range = selection.getRangeAt(0);
        if (!editorRef.value?.contains(range.commonAncestorContainer)) return;

        switch (command) {
            case "bold":
                wrapSelection("strong", range, selection);
                break;

            case "italic":
                wrapSelection("em", range, selection);
                break;

            case "title":
                transformBlock("h3", range, selection);
                break;

            case "list":
                createList(range, selection);
                break;

            case "center":
                applyAlignment("center", range, selection);
                break;

            case "insertText":
                insertText(value || "", range, selection);
                break;

            default:
                console.warn(`Unknown command: ${command}`);
        }

        updateContent();
    };

    /**
     * Wrap selection with inline element (bold, italic)
     * Handles partial selections gracefully
     */
    const wrapSelection = (tagName, range, selection) => {
        const selectedText = range.toString();
        if (!selectedText) return;

        // Extract and delete current selection
        const fragment = range.extractContents();

        // Create wrapper element
        const wrapper = document.createElement(tagName);

        // Check if already wrapped with same tag
        const parent = range.commonAncestorContainer.parentElement;
        if (parent && parent.tagName.toLowerCase() === tagName.toLowerCase()) {
            // Unwrap: move children out
            while (parent.firstChild) {
                if (parent.parentNode) {
                    parent.parentNode.insertBefore(parent.firstChild, parent);
                }
            }
            parent.remove();
        } else {
            // Wrap the fragment
            wrapper.appendChild(fragment);
            range.insertNode(wrapper);

            // Restore selection on the wrapped content
            range.selectNodeContents(wrapper);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    };

    /**
     * Transform selection into block element (h3, p, div)
     */
    const transformBlock = (tagName, range) => {
        // Find the block-level parent
        let blockParent = range.commonAncestorContainer;
        if (blockParent.nodeType === Node.TEXT_NODE) {
            blockParent = blockParent.parentElement;
        }

        // Find the actual block element (p, div, h1-h6, etc)
        while (blockParent && !isBlockElement(blockParent) && blockParent !== editorRef.value) {
            blockParent = blockParent.parentElement;
        }

        if (!blockParent || blockParent === editorRef.value) {
            // No block parent, create new one
            const newBlock = document.createElement(tagName);
            const extractedContent = range.extractContents();
            newBlock.appendChild(extractedContent);
            range.insertNode(newBlock);
        } else if (blockParent.tagName.toLowerCase() === tagName.toLowerCase()) {
            // Already the target tag, convert to paragraph
            const p = document.createElement("p");
            p.innerHTML = blockParent.innerHTML;
            blockParent.replaceWith(p);
        } else {
            // Transform existing block
            const newBlock = document.createElement(tagName);
            newBlock.innerHTML = blockParent.innerHTML;
            blockParent.replaceWith(newBlock);
        }
    };

    /**
     * Create unordered list from selection
     */
    const createList = (range, selection) => {
        const selectedText = selection.toString();
        const lines = selectedText ? selectedText.split("\n").filter((line) => line.trim()) : [""];

        range.deleteContents();

        const ul = document.createElement("ul");
        lines.forEach((line) => {
            const li = document.createElement("li");
            li.textContent = line || "List item";
            ul.appendChild(li);
        });

        range.insertNode(ul);

        // Place cursor in first list item
        const firstLi = ul.querySelector("li");
        if (firstLi) {
            range.selectNodeContents(firstLi);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    };

    /**
     * Apply text alignment
     */
    const applyAlignment = (align, range) => {
        let container = range.commonAncestorContainer;
        if (container.nodeType === Node.TEXT_NODE) {
            container = container.parentElement;
        }

        // Find block-level parent
        while (container && !isBlockElement(container) && container !== editorRef.value) {
            container = container.parentElement;
        }

        if (container && container !== editorRef.value) {
            container.style.textAlign = container.style.textAlign === align ? "" : align;
        } else {
            // Wrap in div with alignment
            const div = document.createElement("div");
            div.style.textAlign = align;
            const extractedContent = range.extractContents();
            div.appendChild(extractedContent);
            range.insertNode(div);
        }
    };

    /**
     * Insert text at cursor position
     */
    const insertText = (text, range, selection) => {
        range.deleteContents();
        const textNode = document.createTextNode(text);
        range.insertNode(textNode);
        range.setStartAfter(textNode);
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
    };

    /**
     * Check if element is block-level
     */
    const isBlockElement = (el) => {
        if (!el || !el.tagName) return false;
        const blockTags = [
            "p",
            "div",
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6",
            "ul",
            "ol",
            "li",
            "blockquote",
        ];
        return blockTags.includes(el.tagName.toLowerCase());
    };

    // Public API - execCommand-like interface
    const formatBold = () => applyFormat("bold");
    const formatItalic = () => applyFormat("italic");
    const formatTitle = () => applyFormat("title");
    const formatList = () => applyFormat("list");
    const formatCenter = () => applyFormat("center");

    // Update content from contenteditable
    const updateContent = () => {
        if (editorRef.value) {
            content.value = editorRef.value.innerHTML;
        }
    };

    // Set content to contenteditable
    const setContent = (newContent) => {
        content.value = newContent;
        if (editorRef.value) {
            editorRef.value.innerHTML = newContent;
        }
    };

    // Clean text content
    const textContent = computed(() => {
        const div = document.createElement("div");
        div.innerHTML = content.value;
        return div.textContent || "";
    });

    // Event handlers
    const onInput = () => {
        updateContent();
    };

    const onPaste = (e) => {
        e.preventDefault();
        const clipboardData = e.clipboardData || window.clipboardData;
        const text = clipboardData ? clipboardData.getData("text/plain") : "";
        applyFormat("insertText", text);
    };

    // Alternative: Direct command execution (execCommand-like)
    const execCommand = (command, value) => {
        try {
            applyFormat(command, value);
            return true;
        } catch (error) {
            console.error(`Failed to execute command: ${command}`, error);
            return false;
        }
    };

    return {
        content,
        editorRef,
        textContent,
        // Individual format methods
        formatBold,
        formatItalic,
        formatTitle,
        formatList,
        formatCenter,
        // Generic command execution
        execCommand,
        applyFormat,
        // Content management
        setContent,
        updateContent,
        // Event handlers
        onInput,
        onPaste,
    };
}
