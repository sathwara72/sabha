import './bootstrap';

import collapse from '@alpinejs/collapse';
import { Html5Qrcode, Html5QrcodeScanner } from 'html5-qrcode';

window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeScanner = Html5QrcodeScanner;

function registerAlpineFeatures() {
    if (!window.Alpine) return;

    try {
        window.Alpine.plugin(collapse);
    } catch (e) {}

    if (!window.Alpine.store('auth')) {
        window.Alpine.store('auth', {
            loginOpen: false,
            openLogin() {
                window.location.href = '/login';
            },
            closeLogin() {
                this.loginOpen = false;
            },
            openRegister() {
                window.location.href = '/register';
            },
        });
    }

    window.Alpine.data('chatThread', (config) => ({
        conversationId: config.conversationId,
        currentUserId: config.currentUserId,
        isGroup: config.isGroup ?? false,
        isGroupAdmin: config.isGroupAdmin ?? false,
        messages: config.initialMessages ?? [],
        editingId: null,
        editText: '',
        messageInput: '',
        isSending: false,
        showDeleteModal: false,
        deleteConfirmId: null,
        showDeleteChatModal: false,

        init() {
            this.$nextTick(() => this.scrollToBottom());

            if (window.Echo) {
                window.Echo.private('conversation.' + this.conversationId)
                    .listen('.message.sent', (e) => this.onMessageSent(e))
                    .listen('.message.updated', (e) => this.onMessageUpdated(e))
                    .listen('.message.deleted', (e) => this.onMessageDeleted(e));
            }

            this.syncHandler = (e) => this.mergeMessages(e.detail.messages);
            window.addEventListener('chat-messages-synced', this.syncHandler);
        },

        destroy() {
            window.removeEventListener('chat-messages-synced', this.syncHandler);
            if (window.Echo) window.Echo.leave('conversation.' + this.conversationId);
        },

        async sendMessage() {
            const text = (this.messageInput || '').trim();
            if (!text || this.isSending) return;

            const tempId = 'temp_' + Date.now();
            const tempMsg = {
                id: tempId,
                is_pending: true,
                is_mine: true,
                sender_id: this.currentUserId,
                sender_name: 'You',
                body: text,
                body_html: text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>'),
                created_at_human: 'Sending...',
                is_edited: false,
                is_deleted: false,
                editable: false,
                deletable: false,
            };

            this.messages.push(tempMsg);
            this.messageInput = '';
            this.isSending = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                await this.$wire.set('body', text);
                await this.$wire.send();
            } catch (err) {
                console.error('Failed to send message:', err);
            } finally {
                this.isSending = false;
                this.messages = this.messages.filter((m) => m.id !== tempId);
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        onMessageSent(e) {
            if (this.messages.some((m) => m.id === e.id)) return;
            const isMine = e.sender_id === this.currentUserId;
            this.messages.push({
                id: e.id,
                sender_id: e.sender_id,
                sender_name: e.sender_name,
                sender_avatar: e.sender_avatar,
                message_type: e.message_type,
                body: e.body,
                body_html: e.body_html,
                is_mine: isMine,
                is_edited: e.is_edited,
                is_deleted: e.is_deleted,
                editable: isMine && e.message_type === 'text',
                deletable: e.message_type === 'text' && (isMine || this.isGroupAdmin),
                created_at_human: e.created_at_human,
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        onMessageUpdated(e) {
            const msg = this.messages.find((m) => m.id === e.id);
            if (msg) {
                msg.body = e.body;
                msg.body_html = e.body_html;
                msg.is_edited = e.is_edited;
            }
            if (this.editingId === e.id) this.editingId = null;
        },

        onMessageDeleted(e) {
            const msg = this.messages.find((m) => m.id === e.id);
            if (msg) {
                msg.is_deleted = true;
                msg.body = null;
                msg.body_html = null;
                msg.editable = false;
                msg.deletable = false;
            }
        },

        mergeMessages(list) {
            const pending = this.messages.filter((m) => m.is_pending);
            list.forEach((incoming) => {
                const idx = this.messages.findIndex((m) => m.id === incoming.id);
                if (idx === -1) {
                    this.messages.push(incoming);
                } else {
                    this.messages[idx] = incoming;
                }
            });
            // Re-append pending messages at bottom if any
            pending.forEach((p) => {
                if (!this.messages.some((m) => m.id === p.id)) {
                    this.messages.push(p);
                }
            });
            this.messages.sort((a, b) => {
                if (typeof a.id === 'string' && a.id.startsWith('temp_')) return 1;
                if (typeof b.id === 'string' && b.id.startsWith('temp_')) return -1;
                return a.id - b.id;
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        startEdit(msg) {
            this.editingId = msg.id;
            this.editText = msg.body ?? '';
        },

        cancelEdit() {
            this.editingId = null;
            this.editText = '';
        },

        saveEdit() {
            if (!this.editingId || this.editText.trim() === '') return;
            this.$wire.saveEdit(this.editingId, this.editText.trim());
        },

        confirmDelete(id) {
            this.deleteConfirmId = id;
            this.showDeleteModal = true;
        },

        cancelDelete() {
            this.showDeleteModal = false;
            this.deleteConfirmId = null;
        },

        proceedDelete() {
            if (!this.deleteConfirmId) return;
            const id = this.deleteConfirmId;
            this.showDeleteModal = false;
            this.deleteConfirmId = null;
            this.$wire.deleteMessage(id);
        },

        confirmDeleteChat() {
            this.showDeleteChatModal = true;
        },

        cancelDeleteChat() {
            this.showDeleteChatModal = false;
        },

        async proceedDeleteChat() {
            this.showDeleteChatModal = false;
            await this.$wire.deleteConversation();
            window.location.href = '/chat';
        },

        deleteMsg(id) {
            this.confirmDelete(id);
        },

        scrollToBottom() {
            const el = this.$refs.messageList;
            if (el) el.scrollTop = el.scrollHeight;
        },
    }));

    window.Alpine.data('imageCropper', () => ({
        isOpen: false,
        imageSrc: '',
        title: 'Crop Image',
        componentId: null,
        target: 'logoFile',
        aspectRatio: 1,
        zoom: 1,
        rotation: 0,
        aspectMode: 'default',
        naturalSize: { width: 0, height: 0 },
        cropBox: { x: 0, y: 0, w: 100, h: 100 },
        activeHandle: null,
        dragStart: { clientX: 0, clientY: 0, crop: { x: 0, y: 0, w: 100, h: 100 } },
        imgBox: { left: 0, top: 0, width: 0, height: 0 },
        uploading: false,

        init() {
            window.addEventListener('open-cropper', (e) => this.open(e.detail));
        },

        open({ src, aspectRatio, title, target, componentId }) {
            this.imageSrc = src;
            this.aspectRatio = aspectRatio ?? 1;
            this.title = title ?? 'Adjust & Crop Image';
            this.target = target;
            this.componentId = componentId ?? null;
            this.zoom = 1;
            this.rotation = 0;
            this.aspectMode = 'default';
            this.isOpen = true;
            setTimeout(() => {
                this.updateImgBox();
                this.resetCropBox();
            }, 80);
        },

        close() {
            this.isOpen = false;
        },

        getTargetRatio() {
            switch (this.aspectMode) {
                case 'default': return this.aspectRatio;
                case '1:1': return 1;
                case '16:9': return 16 / 9;
                case '4:3': return 4 / 3;
                default: return 0;
            }
        },

        updateImgBox() {
            const img = this.$refs.cropperImage;
            const ws = this.$refs.cropperWorkspace;
            if (!img || !ws) return;
            const imgRect = img.getBoundingClientRect();
            const wsRect = ws.getBoundingClientRect();
            if (wsRect.width > 0 && wsRect.height > 0 && imgRect.width > 0 && imgRect.height > 0) {
                this.imgBox = {
                    left: imgRect.left - wsRect.left,
                    top: imgRect.top - wsRect.top,
                    width: imgRect.width,
                    height: imgRect.height,
                };
            }
        },

        resetCropBox() {
            const targetRatio = this.getTargetRatio();
            let displayW = this.imgBox.width;
            let displayH = this.imgBox.height;

            if (displayW === 0 || displayH === 0) {
                const img = this.$refs.cropperImage;
                if (img) {
                    const r = img.getBoundingClientRect();
                    displayW = r.width;
                    displayH = r.height;
                }
            }

            if (targetRatio > 0 && displayW > 0 && displayH > 0) {
                let wPercent = 95;
                let hPercent = ((displayW * 0.95) / targetRatio / displayH) * 100;

                if (hPercent > 95) {
                    hPercent = 95;
                    wPercent = ((displayH * 0.95) * targetRatio / displayW) * 100;
                }

                const xPercent = Math.max(0, (100 - wPercent) / 2);
                const yPercent = Math.max(0, (100 - hPercent) / 2);

                this.cropBox = { x: xPercent, y: yPercent, w: Math.min(100, wPercent), h: Math.min(100, hPercent) };
            } else {
                this.cropBox = { x: 0, y: 0, w: 100, h: 100 };
            }
        },

        onImageLoad() {
            const img = this.$refs.cropperImage;
            this.naturalSize = { width: img.naturalWidth, height: img.naturalHeight };
            this.updateImgBox();
            this.resetCropBox();
        },

        startDrag(handle, e) {
            e.stopPropagation();
            e.preventDefault();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            this.activeHandle = handle;
            this.dragStart = { clientX, clientY, crop: { ...this.cropBox } };

            const onMove = (ev) => this.onDragMove(ev);
            const onUp = () => {
                this.activeHandle = null;
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onUp);
                window.removeEventListener('touchmove', onMove);
                window.removeEventListener('touchend', onUp);
            };
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onUp);
            window.addEventListener('touchmove', onMove);
            window.addEventListener('touchend', onUp);
        },

        onDragMove(e) {
            if (!this.activeHandle) return;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const currentImgWidth = this.imgBox.width || (this.$refs.cropperImage ? this.$refs.cropperImage.getBoundingClientRect().width : 300);
            const currentImgHeight = this.imgBox.height || (this.$refs.cropperImage ? this.$refs.cropperImage.getBoundingClientRect().height : 200);
            if (currentImgWidth === 0 || currentImgHeight === 0) return;

            const deltaXPercent = ((clientX - this.dragStart.clientX) / currentImgWidth) * 100;
            const deltaYPercent = ((clientY - this.dragStart.clientY) / currentImgHeight) * 100;
            const start = this.dragStart.crop;

            if (this.activeHandle === 'move') {
                const newX = Math.max(0, Math.min(100 - start.w, start.x + deltaXPercent));
                const newY = Math.max(0, Math.min(100 - start.h, start.y + deltaYPercent));
                this.cropBox = { ...start, x: newX, y: newY };
                return;
            }

            let newX = start.x, newY = start.y, newW = start.w, newH = start.h;
            const targetRatio = this.getTargetRatio();
            const isFree = this.aspectMode === 'free';
            const handle = this.activeHandle;

            if (handle.includes('e')) {
                newW = Math.max(8, Math.min(100 - start.x, start.w + deltaXPercent));
            }
            if (handle.includes('w')) {
                const possibleW = start.w - deltaXPercent;
                if (possibleW >= 8 && start.x + deltaXPercent >= 0) {
                    newX = start.x + deltaXPercent;
                    newW = possibleW;
                }
            }
            if (handle.includes('s')) {
                newH = Math.max(8, Math.min(100 - start.y, start.h + deltaYPercent));
            }
            if (handle.includes('n')) {
                const possibleH = start.h - deltaYPercent;
                if (possibleH >= 8 && start.y + deltaYPercent >= 0) {
                    newY = start.y + deltaYPercent;
                    newH = possibleH;
                }
            }

            if (!isFree && targetRatio > 0 && currentImgWidth > 0 && currentImgHeight > 0) {
                if (handle === 'n' || handle === 's') {
                    newW = (newH * targetRatio * currentImgHeight) / currentImgWidth;
                    if (newX + newW > 100) {
                        newW = Math.max(5, 100 - newX);
                        newH = (newW * currentImgWidth) / (targetRatio * currentImgHeight);
                    }
                } else {
                    const calculatedH = (newW * currentImgWidth) / (targetRatio * currentImgHeight);
                    if (newY + calculatedH <= 100) {
                        newH = calculatedH;
                    } else {
                        newW = (newH * targetRatio * currentImgHeight) / currentImgWidth;
                    }
                }
            }

            this.cropBox = {
                x: Math.max(0, Math.min(100 - newW, newX)),
                y: Math.max(0, Math.min(100 - newH, newY)),
                w: Math.max(5, Math.min(100, newW)),
                h: Math.max(5, Math.min(100, newH)),
            };
        },

        selectFullImage() {
            this.aspectMode = 'free';
            this.cropBox = { x: 0, y: 0, w: 100, h: 100 };
        },

        lockDefaultRatio() {
            this.aspectMode = 'default';
            this.resetCropBox();
        },

        rotate() {
            this.rotation = (this.rotation + 90) % 360;
            setTimeout(() => this.updateImgBox(), 30);
        },

        resetAll() {
            this.zoom = 1;
            this.rotation = 0;
            this.resetCropBox();
        },

        onZoomChange() {
            setTimeout(() => this.updateImgBox(), 30);
        },

        applyCrop() {
            const img = this.$refs.cropperImage;
            if (!img) return;

            const NW = this.naturalSize.width || img.naturalWidth || 800;
            const NH = this.naturalSize.height || img.naturalHeight || 600;

            const isRotated90or270 = this.rotation === 90 || this.rotation === 270;
            const rotWidth = isRotated90or270 ? NH : NW;
            const rotHeight = isRotated90or270 ? NW : NH;

            const fullCanvas = document.createElement('canvas');
            fullCanvas.width = rotWidth;
            fullCanvas.height = rotHeight;
            const fullCtx = fullCanvas.getContext('2d');
            if (!fullCtx) return;

            fullCtx.translate(rotWidth / 2, rotHeight / 2);
            fullCtx.rotate((this.rotation * Math.PI) / 180);
            fullCtx.drawImage(img, -NW / 2, -NH / 2, NW, NH);

            const cropX = Math.round((this.cropBox.x / 100) * rotWidth);
            const cropY = Math.round((this.cropBox.y / 100) * rotHeight);
            const cropW = Math.round((this.cropBox.w / 100) * rotWidth);
            const cropH = Math.round((this.cropBox.h / 100) * rotHeight);

            const outputWidth = Math.max(10, cropW);
            const outputHeight = Math.max(10, cropH);

            let finalW = outputWidth;
            let finalH = outputHeight;

            if (this.aspectRatio > 0 && Math.abs(outputWidth / outputHeight - this.aspectRatio) > 0.08) {
                const currentRatio = outputWidth / outputHeight;
                if (currentRatio < this.aspectRatio) {
                    finalH = outputHeight;
                    finalW = Math.round(outputHeight * this.aspectRatio);
                } else {
                    finalW = outputWidth;
                    finalH = Math.round(outputWidth / this.aspectRatio);
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = finalW;
            canvas.height = finalH;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            if (finalW !== outputWidth || finalH !== outputHeight) {
                try {
                    ctx.filter = 'blur(24px)';
                    ctx.drawImage(fullCanvas, cropX, cropY, cropW, cropH, -30, -30, finalW + 60, finalH + 60);
                    ctx.filter = 'none';
                } catch (e) {
                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(0, 0, finalW, finalH);
                }
                ctx.fillStyle = 'rgba(15, 23, 42, 0.45)';
                ctx.fillRect(0, 0, finalW, finalH);
                const drawX = Math.round((finalW - outputWidth) / 2);
                const drawY = Math.round((finalH - outputHeight) / 2);
                ctx.drawImage(fullCanvas, cropX, cropY, cropW, cropH, drawX, drawY, outputWidth, outputHeight);
            } else {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, outputWidth, outputHeight);
                ctx.drawImage(fullCanvas, cropX, cropY, cropW, cropH, 0, 0, outputWidth, outputHeight);
            }

            canvas.toBlob((blob) => {
                if (!blob) return;
                const file = new File([blob], `cropped_${this.target}_${Date.now()}.jpg`, { type: 'image/jpeg' });
                this.uploading = true;

                let wire = this.$wire;
                if (!wire && window.Livewire) {
                    if (this.componentId) {
                        wire = window.Livewire.find(this.componentId);
                    }
                    if (!wire) {
                        const allComponents = window.Livewire.all();
                        if (allComponents && allComponents.length > 0) {
                            wire = allComponents[0];
                        }
                    }
                }

                if (wire && typeof wire.upload === 'function') {
                    wire.upload(
                        this.target,
                        file,
                        () => {
                            this.uploading = false;
                            this.close();
                        },
                        (err) => {
                            console.error('Cropper Livewire upload error:', err);
                            this.uploading = false;
                            alert('Upload failed. Please try again.');
                        },
                        () => {}
                    );
                } else {
                    console.error('Livewire component not found for cropper upload.');
                    this.uploading = false;
                    this.close();
                }
            }, 'image/jpeg', 0.93);
        },
    }));

    window.Alpine.data('animatedCounter', (rawValue) => ({
        displayValue: '0',
        targetNumber: 0,
        prefix: '',
        suffix: '',
        duration: 1800,
        hasAnimated: false,

        init() {
            const str = String(rawValue || '').trim();
            const match = str.match(/^([^\d]*)(\d+[\d,.]*)(.*)$/);
            if (match) {
                this.prefix = match[1] || '';
                this.suffix = match[3] || '';
                const numStr = match[2].replace(/,/g, '');
                this.targetNumber = parseFloat(numStr) || 0;
                this.displayValue = this.prefix + '0' + this.suffix;
            } else {
                this.displayValue = str;
                return;
            }

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.hasAnimated) {
                            this.hasAnimated = true;
                            this.animate();
                            observer.disconnect();
                        }
                    });
                }, { threshold: 0.15 });

                observer.observe(this.$el);
            } else {
                this.animate();
            }
        },

        animate() {
            const start = 0;
            const end = this.targetNumber;
            const startTime = performance.now();
            const isFloat = String(end).includes('.');

            const step = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / this.duration, 1);
                // Ease-out cubic curve: 1 - Math.pow(1 - progress, 3)
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                const current = start + (end - start) * easeProgress;

                const formatted = isFloat ? current.toFixed(1) : Math.floor(current).toLocaleString();
                this.displayValue = this.prefix + formatted + this.suffix;

                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    this.displayValue = this.prefix + (isFloat ? end.toFixed(1) : Math.floor(end).toLocaleString()) + this.suffix;
                }
            };

            requestAnimationFrame(step);
        }
    }));
}

document.addEventListener('alpine:init', registerAlpineFeatures);
if (window.Alpine) {
    registerAlpineFeatures();
}

let activeQrScanner = null;

window.startQrScanner = async function (elementId, onDecode) {
    try {
        const { Html5Qrcode } = await import('html5-qrcode');
        activeQrScanner = new Html5Qrcode(elementId);
        await activeQrScanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 150, height: 150 } },
            (decodedText) => {
                if (activeQrScanner && activeQrScanner.isScanning) {
                    activeQrScanner.stop().then(() => onDecode(decodedText)).catch(() => onDecode(decodedText));
                }
            },
            () => {},
        );
    } catch (e) {
        console.error('Camera access error:', e);
    }
};

window.stopQrScanner = function () {
    if (activeQrScanner && activeQrScanner.isScanning) {
        activeQrScanner.stop().catch((err) => console.error('Error stopping scanner', err));
    }
    activeQrScanner = null;
};
