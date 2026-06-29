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

/*
 * Gym membership popup. Subscribe / Renewal tabs, customer details + plan
 * selection + Paystack payment in one modal. On a successful charge it submits
 * a hidden POST form (so personal data never goes in the URL) to gym/subscribe,
 * which verifies the payment, records the membership and reopens at success.
 */
window.gymMembership = function (config) {
    return {
        plans: config.plans || [],
        paystackKey: config.paystackKey || '',
        showModal: false,
        step: config.successData ? 'success' : 'form',
        type: 'subscribe', // subscribe | renewal
        name: '', email: '', phone: '', dob: '',
        planSlug: config.defaultPlan || (config.plans[0] ? config.plans[0].slug : ''),
        channel: 'card',
        success: config.successData || null,
        payReference: '',

        init() {
            if (this.success) { this.step = 'success'; this.open(); }
            else if (window.location.hash === '#subscribe') { this.open('subscribe'); }
        },
        open(type, planSlug) {
            // A Subscribe/Renew click always opens the fresh form, even right
            // after a successful subscription (clears the consumed success).
            if (type) { this.type = type; this.success = null; this.step = 'form'; }
            if (planSlug) this.planSlug = planSlug;
            this.showModal = true; document.body.style.overflow = 'hidden';
        },
        close() { this.showModal = false; document.body.style.overflow = ''; },

        selectPlan(slug) { this.planSlug = slug; },
        isPlan(slug) { return this.planSlug === slug; },
        get selectedPlan() { return this.plans.find((p) => p.slug === this.planSlug) || null; },
        get amountKobo() { return this.selectedPlan ? this.selectedPlan.price * 100 : 0; },
        money(n) { return '₦' + (n || 0).toLocaleString(); },

        pay() {
            if (!this.name || !this.email) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please enter your name and email to continue.' } }));
                return;
            }
            if (!this.selectedPlan) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please choose a membership plan.' } }));
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
                amount: this.amountKobo,
                currency: 'NGN',
                channels: [channelMap[this.channel] || 'card'],
                metadata: {
                    name: this.name,
                    phone: this.phone ? '+234' + this.phone : '',
                    custom_fields: [
                        { display_name: 'Plan', variable_name: 'plan', value: this.selectedPlan.name },
                        { display_name: 'Type', variable_name: 'type', value: this.type },
                    ],
                },
                callback: (response) => { this.payReference = response.reference; this.$nextTick(() => this.$refs.form.submit()); },
                onClose: () => { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment window closed before completing your membership.' } })); },
            });
            handler.openIframe();
        },
    };
};

/*
 * Restaurant reservation popup. Steps: reserve -> checkout -> success.
 * Table/Lounge tabs, occasion/guests/date/time + preferred table selection,
 * then customer details + Paystack for the refundable reservation fee. On a
 * successful charge it submits a hidden POST form to restaurant/reserve, which
 * verifies the payment, records the reservation and reopens at success.
 */
window.restaurantReservation = function (config) {
    return {
        tables: config.tables || [],
        paystackKey: config.paystackKey || '',
        fee: config.fee || 10000,
        showModal: false,
        step: config.successData ? 'success' : 'reserve',
        area: 'dining', // dining | lounge
        occasion: 'Casual Dining',
        guests: 2,
        date: '',
        time: '',
        tableId: null,
        specialRequest: '',
        name: '', email: '', phone: '',
        channel: 'card',
        success: config.successData || null,
        payReference: '',

        init() {
            // Only show success right after a redirect (consumed-once data).
            if (this.success) { this.step = 'success'; this.showModal = true; document.body.style.overflow = 'hidden'; }
            this.pickFirstTable();
        },
        open() {
            // A Reserve click always opens the fresh form, even right after a
            // successful reservation (clears the consumed success screen).
            this.success = null;
            this.step = 'reserve';
            this.showModal = true; document.body.style.overflow = 'hidden';
        },
        close() { this.showModal = false; document.body.style.overflow = ''; },

        setArea(area) { this.area = area; this.pickFirstTable(); },
        get areaTables() { return this.tables.filter((t) => t.area === this.area); },
        pickFirstTable() { const list = this.areaTables; this.tableId = list.length ? list[0].id : null; },
        selectTable(id) { this.tableId = id; },
        isTable(id) { return this.tableId === id; },
        get selectedTable() { return this.tables.find((t) => t.id === this.tableId) || null; },
        get amountKobo() { return this.fee * 100; },
        money(n) { return '₦' + (n || 0).toLocaleString(); },
        get prettyDate() {
            if (!this.date) return '—';
            try { return new Date(this.date).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' }); }
            catch (e) { return this.date; }
        },
        get prettyTime() {
            if (!this.time) return '—';
            const parts = String(this.time).split(':');
            let h = parseInt(parts[0], 10);
            const m = parts[1] || '00';
            if (isNaN(h)) return this.time;
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return h + ':' + m + ' ' + ampm;
        },

        goCheckout() {
            if (!this.date || !this.time) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please choose a date and time for your reservation.' } }));
                return;
            }
            if (!this.selectedTable) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please select a preferred ' + (this.area === 'lounge' ? 'lounge space' : 'table') + '.' } }));
                return;
            }
            this.step = 'checkout';
        },
        backToReserve() { this.step = 'reserve'; },

        pay() {
            if (!this.name || !this.email) {
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
                amount: this.amountKobo,
                currency: 'NGN',
                channels: [channelMap[this.channel] || 'card'],
                metadata: {
                    name: this.name,
                    phone: this.phone ? '+234' + this.phone : '',
                    custom_fields: [
                        { display_name: 'Reservation', variable_name: 'area', value: this.area === 'lounge' ? 'Lounge' : 'Table Reservation' },
                        { display_name: 'Guests', variable_name: 'guests', value: String(this.guests) },
                    ],
                },
                callback: (response) => { this.payReference = response.reference; this.$nextTick(() => this.$refs.form.submit()); },
                onClose: () => { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment window closed before completing your reservation.' } })); },
            });
            handler.openIframe();
        },
    };
};

/*
 * Cinema movie booking (movie detail page). Pick date/time, ticket counts,
 * seats and snacks on the page; "Complete Booking" opens the checkout popup
 * (customer details + Paystack). On a successful charge it submits a hidden
 * POST to cinema/book, which records the booking and reopens at success.
 */
window.cinemaBooking = function (config) {
    return {
        movie: config.movie || {},
        snacks: config.snacks || [],
        paystackKey: config.paystackKey || '',
        seatsUrl: config.seatsUrl || '',
        holdUrl: config.holdUrl || '',
        releaseUrl: config.releaseUrl || '',
        csrf: config.csrf || '',
        dates: [],
        selectedDate: '',
        selectedTime: config.movie && config.movie.showtimes && config.movie.showtimes[0] ? config.movie.showtimes[0] : '',
        rows: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
        cols: 12,
        seats: [],
        takenSeats: [],
        adult: 1,
        child: 0,
        snackQty: {},
        name: '', email: '', phone: '',
        channel: 'card',
        showCheckout: false,
        holding: false,
        step: config.successData ? 'success' : 'checkout',
        success: config.successData || null,
        payReference: '',
        // Unique token tying this guest's seat hold to their Paystack reference.
        token: 'CIN-' + Date.now().toString(36).toUpperCase() + '-' + Math.random().toString(36).slice(2, 8).toUpperCase(),

        init() {
            // Build the next 7 selectable dates.
            const today = new Date();
            for (let i = 0; i < 7; i++) {
                const d = new Date(today); d.setDate(today.getDate() + i);
                this.dates.push({ iso: d.toISOString().slice(0, 10), dow: d.toLocaleDateString('en-US', { weekday: 'short' }), day: d.getDate(), mon: d.toLocaleDateString('en-US', { month: 'short' }) });
            }
            this.selectedDate = this.dates[0].iso;
            this.snacks.forEach((s) => { this.snackQty[s.id] = 0; });
            if (this.success) { this.step = 'success'; this.showCheckout = true; document.body.style.overflow = 'hidden'; }

            // Keep the seat map in sync with what's already taken.
            this.fetchTaken();
            this.$watch('selectedDate', () => this.fetchTaken());
            this.$watch('selectedTime', () => this.fetchTaken());
            // Best-effort release of our hold if the guest leaves mid-checkout.
            window.addEventListener('beforeunload', () => {
                if (this.step !== 'success' && this.seats.length) { this.releaseHold(true); }
            });
        },

        fetchTaken() {
            if (!this.seatsUrl || !this.selectedDate || !this.selectedTime) { this.takenSeats = []; return; }
            const url = this.seatsUrl + '?date=' + encodeURIComponent(this.selectedDate) + '&time=' + encodeURIComponent(this.selectedTime);
            fetch(url, { headers: { Accept: 'application/json' } })
                .then((r) => r.json())
                .then((j) => {
                    this.takenSeats = Array.isArray(j.taken) ? j.taken : [];
                    // Drop any selected seat that is now taken by someone else.
                    this.seats = this.seats.filter((s) => !this.takenSeats.includes(s));
                })
                .catch(() => { this.takenSeats = []; });
        },

        toggleSeat(id) {
            if (this.isTaken(id)) { return; }
            const i = this.seats.indexOf(id);
            if (i === -1) { this.seats.push(id); } else { this.seats.splice(i, 1); }
        },
        isSeat(id) { return this.seats.includes(id); },
        isTaken(id) { return this.takenSeats.includes(id); },

        releaseHold(beacon) {
            if (!this.releaseUrl) { return; }
            // Form-encoded so sendBeacon (which can't set headers) still passes CSRF via _token.
            const body = 'token=' + encodeURIComponent(this.token) + '&_token=' + encodeURIComponent(this.csrf);
            if (beacon && navigator.sendBeacon) {
                navigator.sendBeacon(this.releaseUrl, new Blob([body], { type: 'application/x-www-form-urlencoded' }));
                return;
            }
            fetch(this.releaseUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': this.csrf }, body, keepalive: true }).catch(() => {});
        },

        get ticketsCount() { return this.adult + this.child; },
        get ticketsTotal() { return this.adult * (this.movie.adult_price || 0) + this.child * (this.movie.child_price || 0); },
        get snacksTotal() { return this.snacks.reduce((t, s) => t + (this.snackQty[s.id] || 0) * s.price, 0); },
        get grandTotal() { return this.ticketsTotal + this.snacksTotal; },
        get amountKobo() { return this.grandTotal * 100; },
        money(n) { return '₦' + (n || 0).toLocaleString(); },
        prettyDate(iso) { if (!iso) return '—'; try { return new Date(iso).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' }); } catch (e) { return iso; } },
        get chosenSnacks() { return this.snacks.filter((s) => (this.snackQty[s.id] || 0) > 0).map((s) => ({ name: s.name, qty: this.snackQty[s.id], price: s.price })); },

        async openCheckout() {
            if (this.ticketsCount < 1) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please add at least one ticket.' } })); return; }
            if (!this.selectedDate || !this.selectedTime) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please choose a date and showtime.' } })); return; }
            if (this.seats.length !== this.ticketsCount) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please select ' + this.ticketsCount + ' seat' + (this.ticketsCount > 1 ? 's' : '') + ' to match your tickets.' } })); return; }

            // Reserve the seats before payment so they can't be taken by someone else.
            if (this.holdUrl) {
                this.holding = true;
                try {
                    const res = await fetch(this.holdUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf },
                        body: JSON.stringify({ movie: this.movie.slug, date: this.selectedDate, time: this.selectedTime, seats: this.seats, token: this.token }),
                    });
                    const j = await res.json();
                    if (!j.ok) {
                        const taken = (j.conflicts || []).join(', ');
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Seat' + ((j.conflicts || []).length > 1 ? 's' : '') + ' ' + taken + ' just got taken. Please pick another.' } }));
                        this.fetchTaken();
                        this.holding = false;
                        return;
                    }
                } catch (e) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Could not reserve your seats. Please try again.' } }));
                    this.holding = false;
                    return;
                }
                this.holding = false;
            }

            this.success = null; this.step = 'checkout';
            this.showCheckout = true; document.body.style.overflow = 'hidden';
        },
        closeCheckout() {
            // Free the held seats unless the booking already succeeded.
            if (this.step !== 'success') { this.releaseHold(false); this.fetchTaken(); }
            this.showCheckout = false; document.body.style.overflow = '';
        },

        pay() {
            if (!this.name || !this.email) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please enter your name and email to continue.' } })); return; }
            if (!this.paystackKey) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payments are not configured yet. Please add your Paystack keys.' } })); return; }
            if (typeof PaystackPop === 'undefined') { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment library failed to load. Check your connection and try again.' } })); return; }
            const channelMap = { card: 'card', bank: 'bank', transfer: 'bank_transfer' };
            const handler = PaystackPop.setup({
                key: this.paystackKey,
                email: this.email,
                amount: this.amountKobo,
                currency: 'NGN',
                ref: this.token, // tie the payment to our seat hold
                channels: [channelMap[this.channel] || 'card'],
                metadata: { name: this.name, phone: this.phone ? '+234' + this.phone : '', custom_fields: [{ display_name: 'Movie', variable_name: 'movie', value: this.movie.title }] },
                callback: (response) => { this.payReference = response.reference; this.$nextTick(() => this.$refs.form.submit()); },
                onClose: () => { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment window closed before completing your booking.' } })); },
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
