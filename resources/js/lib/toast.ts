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

export function showInfo(msg: any, autoHide?: number, title?: string) {
    let messages = '';

    if (typeof msg === 'string') {
        messages = msg;
    } else if (msg && Object.keys(msg).length) {
        messages = Object.values(msg).flat().join('\n');
    } else {
        messages = 'No Information';
    }

    showAlert(messages, 'info', title, autoHide);
}

export function showSuccess(msg: any, autoHide?: number, title?: string) {
    let messages = '';

    if (typeof msg === 'string') {
        messages = msg;
    } else if (msg && Object.keys(msg).length) {
        messages = Object.values(msg).flat().join('\n');
    } else {
        messages = 'Success';
    }

    console.log(messages);
    showAlert(messages, 'success', title, autoHide);
}

export function showError(msg: any, autoHide?: number, title?: string) {
    let messages = '';

    if (typeof msg === 'string') {
        messages = msg;
    } else if (msg && Object.keys(msg).length) {
        messages = Object.values(msg).flat().join('\n');
    } else {
        messages = 'Error Occured';
    }

    showAlert(messages, 'error', title, autoHide);
}
