import './bootstrap';

/*
 * Fast admin image uploads.
 *
 * Livewire normally uploads the full-size original file before the server can
 * optimise it — slow for big phone/camera photos. This Alpine helper resizes &
 * compresses the image in the browser first (max 1920px, JPEG ~82%), so only a
 * small web-ready file is sent over the network. The server-side ImageOptimizer
 * still runs as a safety net.
 *
 * Usage in Blade:
 *   <div x-data="cmsImageUpload('uploads.field__name')"> … @change="handle($event)"
 */
/*
 * Spa & Wellness "Book Session" reservation popup.
 * Multi-select services, guests, date & time → live priced summary → submit
 * to the spa checkout. Config (services, fees) is passed from Blade.
 */
window.spaReservation = function (config) {
    return {
        services: config.services || [],
        fees: config.fees || 2000,
        vatRate: 0.075,
        showModal: false,
        selected: [],
        guests: 2,
        date: '',
        time: '',
        special: '',

        init() {
            // Allow deep-linking straight to the booking popup, e.g. /spa-wellness#book
            if (window.location.hash === '#book') this.open();
        },
        open() { this.showModal = true; document.body.style.overflow = 'hidden'; },
        close() { this.showModal = false; document.body.style.overflow = ''; },
        toggle(slug) {
            const i = this.selected.indexOf(slug);
            if (i === -1) this.selected.push(slug); else this.selected.splice(i, 1);
        },
        isSelected(slug) { return this.selected.includes(slug); },

        get chosen() { return this.services.filter((s) => this.selected.includes(s.slug)); },
        get subtotal() { return this.chosen.reduce((t, s) => t + s.price * Math.max(1, this.guests), 0); },
        get taxes() { return Math.round(this.subtotal * this.vatRate); },
        get total() { return this.subtotal ? this.subtotal + this.fees + this.taxes : 0; },
        money(n) { return '₦' + (n || 0).toLocaleString(); },
        get canSubmit() { return this.chosen.length > 0 && !!this.date; },

        submit() {
            if (!this.canSubmit) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please select at least one service and choose a date.' } }));
                return;
            }
            this.$refs.form.submit();
        },
    };
};

window.cmsImageUpload = function (model) {
    return {
        uploading: false,
        progress: 0,

        handle(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            this.uploading = true;
            this.progress = 0;

            const send = (toUpload) => {
                this.$wire.upload(
                    model,
                    toUpload,
                    () => { this.uploading = false; this.progress = 0; },
                    () => { this.uploading = false; this.progress = 0; },
                    (e) => { this.progress = e.detail.progress; },
                );
            };

            const resizable = file.type.startsWith('image/')
                && file.type !== 'image/gif'
                && file.type !== 'image/svg+xml';

            if (!resizable) { send(file); return; }

            this.resize(file).then(send).catch(() => send(file));
        },

        resize(file) {
            return new Promise((resolve, reject) => {
                const url = URL.createObjectURL(file);
                const img = new Image();
                img.onload = () => {
                    URL.revokeObjectURL(url);
                    const maxW = 1920;
                    let { width, height } = img;
                    if (width > maxW) {
                        height = Math.round((height * maxW) / width);
                        width = maxW;
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    const type = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                    canvas.toBlob(
                        (blob) => {
                            if (!blob) { reject(); return; }
                            const name = file.name.replace(
                                /\.(png|jpe?g|webp|gif|bmp|tiff?)$/i,
                                type === 'image/png' ? '.png' : '.jpg',
                            );
                            resolve(new File([blob], name, { type, lastModified: Date.now() }));
                        },
                        type,
                        0.82,
                    );
                };
                img.onerror = reject;
                img.src = url;
            });
        },
    };
};
