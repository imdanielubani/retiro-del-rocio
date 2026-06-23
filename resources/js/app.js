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
        // step: 'select' (choose services) | 'checkout' (pay) | 'success'
        step: config.step || 'select',

        // selection state
        selected: config.bookingSelected || [],
        guests: config.bookingGuests || 2,
        date: config.bookingDate || '',
        time: config.bookingTime || '',
        special: '',

        // checkout state
        firstName: '', lastName: '', email: '', phone: '', channel: 'card',
        paystackKey: config.paystackKey || '',
        callbackUrl: config.callbackUrl || '',
        bookingKobo: config.bookingKobo || 0,
        bookingServices: config.bookingServices || '',
        bookingDateLabel: config.bookingDateLabel || '',

        init() {
            // Server drives the step: reopen the popup at checkout / success after a redirect.
            if (this.step === 'checkout' || this.step === 'success') {
                this.open();
            } else if (window.location.hash === '#book') {
                this.open();
            }
        },
        open() { this.showModal = true; document.body.style.overflow = 'hidden'; },
        close() { this.showModal = false; document.body.style.overflow = ''; },
        editSelection() { this.step = 'select'; },

        toggle(slug) {
            const i = this.selected.indexOf(slug);
            if (i === -1) this.selected.push(slug); else this.selected.splice(i, 1);
        },
        isSelected(slug) { return this.selected.includes(slug); },

        // Format the chosen time as 12-hour with AM/PM, e.g. "15:00" -> "3:00 PM".
        get timeLabel() {
            if (!this.time) return '';
            const [h, m] = this.time.split(':');
            let hh = parseInt(h, 10);
            if (isNaN(hh)) return this.time;
            const ap = hh >= 12 ? 'PM' : 'AM';
            hh = hh % 12 || 12;
            return hh + ':' + (m || '00') + ' ' + ap;
        },

        get chosen() { return this.services.filter((s) => this.selected.includes(s.slug)); },
        get subtotal() { return this.chosen.reduce((t, s) => t + s.price * Math.max(1, this.guests), 0); },
        get taxes() { return Math.round(this.subtotal * this.vatRate); },
        get total() { return this.subtotal ? this.subtotal + this.fees + this.taxes : 0; },
        money(n) { return '₦' + (n || 0).toLocaleString(); },
        get canSubmit() { return this.chosen.length > 0 && !!this.date; },

        // Step 1 → submit selection to the server (sets session, redirects back to checkout step).
        submit() {
            if (!this.canSubmit) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please select at least one service and choose a date.' } }));
                return;
            }
            this.$refs.form.submit();
        },

        // Step 2 → Paystack payment.
        pay() {
            if (!this.firstName || !this.lastName || !this.email) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please enter your name and email to continue.' } }));
                return;
            }
            if (!this.paystackKey) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payments are not configured yet. Please add your Paystack keys.' } }));
                return;
            }
            if (typeof PaystackPop === 'undefined') {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment library failed to load. Check your connection and try again.' } }));
                return;
            }
            const channelMap = { card: 'card', bank: 'bank', transfer: 'bank_transfer' };
            const handler = PaystackPop.setup({
                key: this.paystackKey,
                email: this.email,
                amount: this.bookingKobo,
                currency: 'NGN',
                channels: [channelMap[this.channel] || 'card'],
                metadata: {
                    name: (this.firstName + ' ' + this.lastName).trim(),
                    phone: this.phone ? '+234' + this.phone : '',
                    custom_fields: [
                        { display_name: 'Services', variable_name: 'services', value: this.bookingServices },
                        { display_name: 'Date', variable_name: 'date', value: this.bookingDateLabel },
                    ],
                },
                callback: (response) => { window.location.href = this.callbackUrl + '?reference=' + response.reference; },
                onClose: () => { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment window closed before completing your reservation.' } })); },
            });
            handler.openIframe();
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
