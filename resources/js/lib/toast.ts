import ANotif, { AlertVariant } from '@/components/app/ANotif.vue';
import { createApp, h, ref } from 'vue';

let toastRef: any = null;
let app: any = null;

function initToastHost() {
    if (toastRef) return toastRef;

    const container = document.createElement('div');
    document.body.appendChild(container);

    const Root = {
        setup() {
            toastRef = ref();
            return () => h(ANotif, { ref: toastRef });
        },
    };

    app = createApp(Root);
    app.mount(container);

    return toastRef;
}

function showAlert(
    message: string,
    type: AlertVariant = 'info',
    title?: string,
    autoHide = 3000,
) {
    const toast = initToastHost();

    toast.value?.addToast({
        id: Date.now(),
        type,
        title: title ?? type.toUpperCase(),
        message,
        autoHide,
    });
}

export const showInfo = (msg: string, autoHide?: number, title?: string) =>
    showAlert(msg, 'info', title, autoHide);

export const showSuccess = (msg: string, autoHide?: number, title?: string) =>
    showAlert(msg, 'success', title, autoHide);

export const showError = (msg: string, autoHide?: number, title?: string) =>
    showAlert(msg, 'error', title, autoHide);
