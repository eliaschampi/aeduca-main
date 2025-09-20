import { Modal } from "bootstrap";
import { defineStore } from "pinia";
export const useModalStore = defineStore("modal", {
  state: () => ({
    modalInstance: null,
  }),
  actions: {
    showModal(id) {
      const modalElement = document.getElementById(id);
      if (modalElement) {
        this.modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
        this.modalInstance.show();
      }
    },
    hideModal(id) {
      if (this.modalInstance !== null) {
        this.modalInstance.hide();
        this.modalInstance = null;
      } else {
        const element = document.getElementById(id);
        let modalInstance = Modal.getInstance(element);
        if (!modalInstance) {
          modalInstance = new Modal(element);
        }
        modalInstance.hide();
      }
    },
  },
});
