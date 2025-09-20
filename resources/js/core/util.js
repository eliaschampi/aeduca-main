export const downl = (value, name, ext = ".pdf") => {
  const url = window.URL.createObjectURL(new Blob([value]));
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", name + ext);
  document.body.appendChild(link);
  link.click();
};

export const imagePerson = (image, gender) => {
  const suf = gender === "M" ? "men" : "women";
  return `/default/${image || `default_${suf}.png`}`;
};

export const currency = (value) => {
  return `S/: ${Number(value).toFixed(2)}`;
};
export function createTempCanvas(size) {
  const tempCanvas = document.createElement("canvas");
  tempCanvas.width = size;
  tempCanvas.height = size;
  return tempCanvas;
}

export function calculateScaleFactor(imageWidth, imageHeight, canvasWidth, canvasHeight) {
  const ratio = Math.min(canvasWidth / imageWidth, canvasHeight / imageHeight);
  const width = imageWidth * ratio;
  const height = imageHeight * ratio;
  const scaleFactor = imageWidth / width;
  return { width, height, scaleFactor };
}
