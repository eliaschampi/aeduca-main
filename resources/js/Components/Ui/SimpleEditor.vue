<script setup>
import { computed, onMounted, toRef, watch, nextTick } from "vue";
import { useField } from "vee-validate";
import { useSimpleEditor } from "../../composables/useSimpleEditor.js";

const props = defineProps({
  label: {
    type: String,
    default: "",
  },
  name: String,
  placeholder: {
    type: String,
    default: "Escriba su contenido aquí...",
  },
  maxLength: {
    type: Number,
    default: 1500,
  },
  minLength: {
    type: Number,
    default: 0,
  },
  showPreview: {
    type: Boolean,
    default: false,
  },
});

const modelValue = defineModel({
  type: String,
  default: "",
});

const field = toRef(props, "name");

const { meta, handleChange, handleBlur, errors } = useField(field, undefined, {
  initialValue: modelValue.value,
});

const {
  content,
  editorRef,
  textContent,
  formatBold,
  formatItalic,
  formatTitle,
  formatList,
  formatCenter,
  setContent,
  updateContent, // Lo usamos para sincronización manual
  onInput: handleEditorInput,
  onPaste,
} = useSimpleEditor(modelValue.value || "");

// Computed para clases del editor
const editorClass = computed(() => {
  return {
    "form-control": true,
    "simple-editor__content": true,
    "is-invalid": (meta.touched || meta.dirty) && !meta.valid,
    "is-valid": meta.touched && meta.valid,
  };
});

// Computed para el contador de caracteres con límites
const characterCount = computed(() => {
  const count = textContent.value.length;
  const hasMax = props.maxLength > 0;

  if (hasMax) {
    return {
      text: `${count} / ${props.maxLength} caracteres`,
      isNearLimit: count > props.maxLength * 0.9,
      isOverLimit: count > props.maxLength,
      count,
    };
  }

  return {
    text: `${count} caracteres`,
    isNearLimit: false,
    isOverLimit: false,
    count,
  };
});

// Computed para clase del contador
const counterClass = computed(() => {
  if (characterCount.value.isOverLimit) return "text-danger";
  if (characterCount.value.isNearLimit) return "text-warning";
  return "text-muted";
});

// Función para limpiar el editor
const clearEditor = () => {
  if (confirm("¿Está seguro de que desea limpiar todo el contenido?")) {
    setContent("");
    modelValue.value = "";
    handleChange("");
    // Forzar actualización del DOM
    nextTick(() => {
      if (editorRef.value) {
        editorRef.value.focus();
      }
    });
  }
};

// Función para eliminar formato HTML
const removeFormat = () => {
  const plainText = textContent.value;
  if (plainText) {
    setContent(plainText);
    modelValue.value = plainText;
    handleChange(plainText);
  }
};

// Handler mejorado para input
const handleInput = () => {
  handleEditorInput(); // Llama al original del composable

  // Validación de longitud máxima
  if (props.maxLength > 0 && textContent.value.length > props.maxLength) {
    const truncated = textContent.value.substring(0, props.maxLength);
    setContent(truncated);
  }
};

// Watch mejorado para cambios en el contenido
watch(content, (newContent) => {
  if (newContent !== modelValue.value) {
    modelValue.value = newContent;
    handleChange(newContent);
  }
});

// Watch mejorado para cambios externos
watch(modelValue, (newValue) => {
  if (newValue !== content.value) {
    setContent(newValue || "");
  }
});

// Handler para blur con validación
const handleEditorBlur = (event) => {
  handleBlur(event);
  // Sincronización adicional si es necesario
  updateContent();
};

// Inicialización mejorada
onMounted(() => {
  if (modelValue.value) {
    setContent(modelValue.value);
  }

  // Configurar altura automática si es necesario
  if (editorRef.value) {
    // Auto-resize opcional
    const autoResize = () => {
      const editor = editorRef.value;
      if (editor) {
        editor.style.height = "auto";
        editor.style.height = `${editor.scrollHeight}px`;
      }
    };

    editorRef.value.addEventListener("input", autoResize);
  }
});

// Computed para verificar si hay contenido
const hasContent = computed(() => {
  return textContent.value.length > 0;
});

// Computed para el HTML sanitizado (para preview)
const sanitizedHTML = computed(() => {
  // Aquí podrías agregar sanitización de HTML si es necesario
  return content.value;
});
</script>

<template>
  <div class="simple-editor">
    <!-- Label con slot -->
    <label v-if="$slots.default || props.label" class="fw-medium mb-2">
      <slot>{{ props.label }}</slot>
    </label>

    <!-- Formatting toolbar mejorada -->
    <div class="simple-editor__toolbar mb-2">
      <div class="d-flex justify-content-between align-items-center">
        <!-- Botones de formato -->
        <div class="btn-group btn-group-sm" role="group">
          <button type="button" class="btn btn-sm btn-primary" title="Negrita (Ctrl+B)" @click="formatBold">
            Bold
          </button>
          <button type="button" class="btn btn-sm btn-primary" title="Cursiva (Ctrl+I)" @click="formatItalic">
            Italic
          </button>
          <button type="button" class="btn btn-sm btn-primary" title="Título" @click="formatTitle">Titulo</button>
          <button type="button" class="btn btn-sm btn-primary" title="Lista" @click="formatList">Lista</button>
          <button type="button" class="btn btn-sm btn-primary" title="Centrar" @click="formatCenter">Center</button>
        </div>

        <!-- Botones de acción -->
        <div class="btn-group btn-group-sm" role="group">
          <button
            type="button"
            class="btn btn-sm btn-outline-info"
            title="Quitar formato"
            :disabled="!hasContent"
            @click="removeFormat"
          >
            Sin formato
          </button>
          <button
            type="button"
            class="btn btn-sm btn-outline-danger"
            title="Limpiar todo"
            :disabled="!hasContent"
            @click="clearEditor"
          >
            Limpiar
          </button>
        </div>
      </div>
    </div>

    <!-- Editor contenteditable -->
    <div
      ref="editorRef"
      :class="editorClass"
      contenteditable="true"
      :data-placeholder="placeholder"
      @input="handleInput"
      @paste="onPaste"
      @blur="handleEditorBlur"
      @focus="handleFocus"
    ></div>

    <!-- Footer con contador y validación -->
    <div class="simple-editor__footer mt-2">
      <div class="d-flex justify-content-between align-items-center">
        <!-- Contador de caracteres -->
        <small :class="counterClass">
          {{ characterCount.text }}
        </small>

        <!-- Indicador de estado -->
        <div class="d-flex align-items-center gap-2">
          <span v-if="meta.dirty && !meta.valid" class="badge bg-danger">
            <i class="bi bi-exclamation-circle"></i> Inválido
          </span>
          <span v-else-if="meta.valid && hasContent" class="badge bg-success">
            <i class="bi bi-check-circle"></i> Válido
          </span>
        </div>
      </div>

      <!-- Mensajes de error -->
      <div v-if="errors.length" class="invalid-feedback d-block mt-1">
        {{ errors[0] }}
      </div>
    </div>

    <!-- Preview opcional -->
    <div v-if="props.showPreview && hasContent" class="simple-editor__preview mt-3">
      <div class="card">
        <div class="card-header py-2">
          <small class="text-muted">Vista previa:</small>
        </div>
        <div class="card-body">
          <div v-html="sanitizedHTML"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.simple-editor__toolbar {
  padding-bottom: 0.5rem;
}

.simple-editor__content {
  min-height: 150px;
  max-height: 400px;
  padding: 0.75rem;
  border-radius: 0.375rem;
  outline: none;
  overflow-y: auto;
  transition:
    border-color 0.15s ease-in-out,
    box-shadow 0.15s ease-in-out;
}

.simple-editor__content.is-invalid {
  border-color: #dc3545;
}

.simple-editor__content.is-invalid:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.simple-editor__content.is-valid {
  border-color: #198754;
}

.simple-editor__content.is-valid:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.simple-editor__content:empty::before {
  content: attr(data-placeholder);
  color: #6c757d;
  font-style: italic;
  pointer-events: none;
}

/* Estilos para contenido formateado */
.simple-editor__content :deep(h3) {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0.5rem 0;
}

.simple-editor__content :deep(ul),
.simple-editor__content :deep(ol) {
  padding-left: 1.5rem;
  margin: 0.5rem 0;
}

.simple-editor__content :deep(li) {
  margin-bottom: 0.25rem;
}

.simple-editor__content :deep(p) {
  margin: 0.5rem 0;
}

.simple-editor__content :deep(strong) {
  font-weight: 600;
}

.simple-editor__content :deep(em) {
  font-style: italic;
}

/* Preview styles */
.simple-editor__preview .card {
  border: 1px dashed #dee2e6;
}

.simple-editor__preview .card-header {
  background-color: #f8f9fa;
}

/* Responsive */
@media (max-width: 576px) {
  .simple-editor__toolbar .btn-group {
    flex-wrap: wrap;
  }

  .simple-editor__content {
    min-height: 120px;
  }
}
</style>
