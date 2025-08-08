<script setup>
import { onMounted, onUnmounted, reactive, ref, watch } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import Empty from "../../Components/Ui/Empty.vue";
import { useSnackbarStore } from "../../pinia/snackbar";
import { createTempCanvas, calculateScaleFactor } from "../../core/util";

const emit = defineEmits(["done"]);
const canvas = ref(null);
const cropOverlay = ref(null);
const context = ref(null);
const imageData = ref(null);
const dragging = ref(false);
const cropArea = reactive({ x: 0, y: 0, size: 200 });
const { warningSnack } = useSnackbarStore();
const isLoading = ref(false);

onMounted(() => {
    setupCanvas();
});

watch(imageData, drawImage);

function setupCanvas() {
    context.value = canvas.value.getContext("2d");

    cropOverlay.value.style.width = `${cropArea.size}px`;
    cropOverlay.value.style.height = `${cropArea.size}px`;

    const canvasContainer = canvas.value.parentElement;
    const containerWidth = parseFloat(getComputedStyle(canvasContainer).width);
    const containerHeight = parseFloat(getComputedStyle(canvasContainer).height);

    canvas.value.width = containerWidth;
    canvas.value.height = containerHeight;

    window.addEventListener("resize", handleResize);
}

function handleResize() {
    const canvasContainer = canvas.value.parentElement;
    const containerWidth = parseFloat(getComputedStyle(canvasContainer).width);
    const containerHeight = parseFloat(getComputedStyle(canvasContainer).height);

    canvas.value.width = containerWidth;
    canvas.value.height = containerHeight;
    drawImage();
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) {
        warningSnack("No se seleccionó ningún archivo");
        return;
    }

    if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
        warningSnack("El archivo no es una imagen");
        return;
    }

    if (file.size > 3 * 1024 * 1024) {
        warningSnack("La imagen tiene un peso mayor a 3mb");
        return;
    }

    readImage(file);
}

function handlePaste() {
    navigator.clipboard
        .read()
        .then((clipboardItems) => {
            const item = clipboardItems.find((item) => item.types.includes("image/png"));
            if (!item) {
                warningSnack("No se encontró una imagen en el portapapeles");
                return;
            }
            return item.getType("image/png");
        })
        .then((blob) => {
            const file = new File([blob], "avatar.png", { type: blob.type });
            readImage(file);
        })
        .catch((error) => {
            warningSnack("Error al pegar la imagen");
            console.error(error);
        });
}

function readImage(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        imageData.value = new Image();
        imageData.value.onload = () => drawImage();
        imageData.value.src = e.target.result;
        cropOverlay.value.style.transform = "translate(0, 0)";
    };
    reader.readAsDataURL(file);
}

function drawScaledImage(img, canvas) {
    context.value.clearRect(0, 0, canvas.width, canvas.height);
    const { width, height } = calculateScaleFactor(img.width, img.height, canvas.width, canvas.height);
    context.value.drawImage(img, (canvas.width - width) / 2, (canvas.height - height) / 2, width, height);
}

function drawImage() {
    if (canvas.value && context.value && imageData.value) {
        drawScaledImage(imageData.value, canvas.value);
    }
}

function startDragging() {
    dragging.value = true;
}

function handleDrag(e) {
    if (dragging.value) {
        const event = e.type === "touchmove" ? e.touches[0] : e;
        const x = event.clientX - canvas.value.getBoundingClientRect().left - cropArea.size / 2;
        const y = event.clientY - canvas.value.getBoundingClientRect().top - cropArea.size / 2;
        cropArea.x = x;
        cropArea.y = y;
        updateCropOverlayPosition();
    }
}

function updateCropOverlayPosition() {
    cropOverlay.value.style.transform = `translate(${cropArea.x}px, ${cropArea.y}px)`;
}

function stopDragging() {
    dragging.value = false;
}

function createCroppedCanvas() {
    const scaleFactor = 500 / cropArea.size;
    const tempCanvas = createTempCanvas(cropArea.size * scaleFactor);
    const tempContext = tempCanvas.getContext("2d");
    const canvasWidth = canvas.value.width;
    const canvasHeight = canvas.value.height;
    const {
        width,
        height,
        scaleFactor: imgScaleFactor,
    } = calculateScaleFactor(imageData.value.width, imageData.value.height, canvasWidth, canvasHeight);

    tempContext.drawImage(
        imageData.value,
        (cropArea.x - (canvasWidth - width) / 2) * imgScaleFactor,
        (cropArea.y - (canvasHeight - height) / 2) * imgScaleFactor,
        cropArea.size * imgScaleFactor,
        cropArea.size * imgScaleFactor,
        0,
        0,
        cropArea.size * scaleFactor,
        cropArea.size * scaleFactor,
    );

    return tempCanvas;
}

function handleOk() {
    isLoading.value = true;
    const croppedCanvas = createCroppedCanvas();
    croppedCanvas.toBlob((blob) => {
        isLoading.value = false;
        emit("done", blob);
    }, "image/png");
}

function handleCancel() {
    imageData.value = null;
    dragging.value = false;
}

// Cleanup
onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
});
</script>
<template>
    <Dialog
        id="avatarManagerModal"
        title="Subir Foto"
        :disabled="!imageData || isLoading"
        btn-name="subir"
        @ok="handleOk"
        @cancel="handleCancel"
    >
        <div class="d-flex justify-content-between align-items-center">
            <label class="btn btn-sm btn-inverse-success btn-rounded">
                <span>Seleccionar</span>
                <input
                    id="logo"
                    class="d-none"
                    type="file"
                    accept="image/*"
                    @change="handleFileSelect"
                >
            </label>
            <m-button
                size="btn-sm"
                color="btn-warning"
                @ok="handlePaste"
            >
                Pegar
            </m-button>
        </div>
        <div class="d-flex flex-column align-items-center mt-4">
            <div
                v-show="imageData"
                class="canvas-container"
            >
                <canvas ref="canvas" />
                <div
                    ref="cropOverlay"
                    class="crop-overlay"
                    @mousedown.stop="startDragging"
                    @mousemove.stop="handleDrag"
                    @mouseup.stop="stopDragging"
                    @touchstart.stop="startDragging"
                    @touchmove.stop="handleDrag"
                    @touchend.stop="stopDragging"
                />
            </div>
            <div
                v-show="imageData"
                class="opacity-50"
            >
                Ubique el recuadro en el centro de la imagen
            </div>
        </div>
        <Empty
            v-if="!imageData"
            title="Seleccione una imagen"
        />
    </Dialog>
</template>
