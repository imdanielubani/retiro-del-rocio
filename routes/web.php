<?php

use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\ResetSuccess;
use App\Livewire\Admin\Auth\SetNewPassword;
use App\Livewire\Admin\Auth\VerifyCode;
use App\Livewire\Admin\Rooms\Edit;
use App\Livewire\Admin\Rooms\Index;
use App\Mail\BookingRequest;
use App\Mail\BookingReservation;
use App\Mail\ContactAcknowledgement;
use App\Mail\ContactEnquiry;
use App\Mail\PickupConfirmation;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Room;
use App\Models\SpaBooking;
use App\Models\SpaService;
use App\Models\User;
use App\Notifications\BookingReceived;
use App\Notifications\MessageReceived;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Public website.
Route::view('/', 'welcome')->name('home');
Route::view('spa-wellness', 'spa')->name('spa');
Route::view('rooms-apartment', 'rooms')->name('rooms');
Route::get('rooms-apartment/{room:slug}', function (Room $room) {
    abort_unless($room->is_published, 404);

    return view('room-detail', [
        'room' => $room,
        'offers' => Room::published()->where('id', '!=', $room->id)->ordered()->take(2)->get(),
    ]);
})->name('rooms.show');

// Live room availability for a date range (used by the room detail page).
Route::get('rooms-apartment/{room:slug}/availability', function (Room $room) {
    $checkIn = request('check_in');
    $checkOut = request('check_out');

    if (! $checkIn || ! $checkOut || Carbon::parse($checkOut)->lte(Carbon::parse($checkIn))) {
        return response()->json(['ok' => false, 'available' => null, 'count' => null]);
    }

    $count = $room->availableUnitsForDates($checkIn, $checkOut);

    return response()->json([
        'ok' => true,
        'available' => $count === null ? true : $count > 0,
        'count' => $count, // null = no inventory limit configured
    ]);
})->name('rooms.availability');

/*
|--------------------------------------------------------------------------
| Checkout flow (Paystack)
|--------------------------------------------------------------------------
*/

// Build the full priced booking summary from the raw reservation input.
$buildBooking = function (array $b): array {
    // Prefer the real room price; fall back to the submitted label, then a default.
    $room = ! empty($b['room_slug']) ? Room::where('slug', $b['room_slug'])->first() : null;
    $pricePerNight = $room?->price
        ?? (! empty($b['price']) ? (int) preg_replace('/[^0-9]/', '', $b['price']) : 0)
        ?: 350000;
    $checkIn = Carbon::parse($b['check_in']);
    $checkOut = Carbon::parse($b['check_out']);
    $nights = max(1, (int) $checkIn->diffInDays($checkOut));
    $roomSubtotal = $pricePerNight * $nights;
    $pickupPrice = ! empty($b['pickup_price']) ? (int) preg_replace('/[^0-9]/', '', $b['pickup_price']) : 0;
    $subtotal = $roomSubtotal + $pickupPrice;
    $vat = (int) round($subtotal * 0.075);
    $fees = 1250;
    $total = $subtotal + $vat + $fees;
    $naira = fn ($n) => '₦'.number_format($n);

    if ($checkIn->isSameMonth($checkOut) && $checkIn->year === $checkOut->year) {
        $dateRange = $checkIn->format('j').' - '.$checkOut->format('j M, Y');
    } else {
        $dateRange = $checkIn->format('j M').' - '.$checkOut->format('j M, Y');
    }

    return [
        'room' => $b['room'],
        'room_slug' => $b['room_slug'] ?? null,
        'price_per_night' => $pricePerNight,
        'price' => $naira($pricePerNight),
        'guests' => (int) $b['guests'],
        'check_in' => $b['check_in'],
        'check_out' => $b['check_out'],
        'date_range' => $dateRange,
        'nights' => $nights,
        'pickup_vehicle' => $b['pickup_vehicle'] ?? null,
        'pickup_price' => $pickupPrice ? $naira($pickupPrice) : null,
        'location' => $b['location'] ?? null,
        'passengers' => $b['passengers'] ?? null,
        'arrival_date' => $b['arrival_date'] ?? null,
        'pickup_time' => $b['pickup_time'] ?? null,
        'flight_number' => $b['flight_number'] ?? null,
        'room_subtotal_label' => $naira($roomSubtotal),
        'vat_label' => $naira($vat),
        'fees_label' => $naira($fees),
        'total' => $total,
        'total_label' => $naira($total),
        'total_kobo' => $total * 100,
    ];
};

// Step 1 — "Make reservation" from the room detail page lands here.
Route::post('checkout', function () use ($buildBooking) {
    $data = request()->validate([
        'room' => ['required', 'string', 'max:190'],
        'room_slug' => ['nullable', 'string', 'max:190'],
        'price' => ['required', 'string', 'max:60'],
        'guests' => ['required', 'integer', 'min:1', 'max:30'],
        'check_in' => ['required', 'date'],
        'check_out' => ['required', 'date', 'after_or_equal:check_in'],
        'pickup_vehicle' => ['nullable', 'string', 'max:120'],
        'pickup_price' => ['nullable', 'string', 'max:60'],
        'location' => ['nullable', 'string', 'max:190'],
        'passengers' => ['nullable', 'integer', 'min:1', 'max:30'],
        'arrival_date' => ['nullable', 'date'],
        'pickup_time' => ['nullable', 'string', 'max:20'],
        'flight_number' => ['nullable', 'string', 'max:40'],
    ]);

    // Block booking when no room number is free for the requested dates.
    if (! empty($data['room_slug'])) {
        $room = Room::where('slug', $data['room_slug'])->first();
        if ($room && ! $room->isAvailableForDates($data['check_in'], $data['check_out'])) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Sorry, '.$room->name.' is fully booked for those dates. Please choose different dates.',
            ]);
        }
    }

    session(['booking' => $buildBooking($data)]);

    return redirect()->route('checkout');
})->name('checkout.start');

// Step 2 — the checkout page (customer details + summary + Paystack).
Route::get('checkout', function () {
    $booking = session('booking');

    if (! $booking) {
        return redirect()->route('rooms');
    }

    return view('checkout', [
        'booking' => $booking,
        'paystackKey' => config('services.paystack.public_key'),
    ]);
})->name('checkout');

// Step 3 — Paystack redirects/JS sends the user here after payment to verify.
Route::get('checkout/callback', function () {
    $reference = request('reference');
    $booking = session('booking');

    if (! $reference || ! $booking) {
        return redirect()->route('rooms');
    }

    $secret = config('services.paystack.secret_key');

    try {
        $response = Http::withToken($secret)
            ->acceptJson()
            ->get(rtrim(config('services.paystack.payment_url'), '/').'/transaction/verify/'.$reference);

        $body = $response->json();

        if (! $response->ok() || data_get($body, 'data.status') !== 'success') {
            return redirect()->route('checkout')->with('toast', [
                'type' => 'error',
                'message' => 'We could not verify your payment. If you were charged, please contact us with your reference: '.$reference,
            ]);
        }
    } catch (Throwable $e) {
        report($e);

        return redirect()->route('checkout')->with('toast', [
            'type' => 'error',
            'message' => 'Payment verification failed. Please try again or contact us.',
        ]);
    }

    $order = array_merge($booking, [
        'customer_name' => data_get($body, 'data.metadata.name'),
        'customer_phone' => data_get($body, 'data.metadata.phone'),
        'customer_email' => data_get($body, 'data.customer.email'),
        'reference' => $reference,
        'paid_at' => data_get($body, 'data.paid_at'),
    ]);

    session(['order' => $order]);
    session()->forget('booking');

    // Persist the paid booking so it appears in Admin → Apartments → Bookings.
    try {
        $room = Room::where('slug', $order['room_slug'] ?? null)
            ->orWhere('name', $order['room'] ?? null)
            ->first();

        $booking = Booking::updateOrCreate(
            ['reference' => $reference],
            [
                'room_id' => $room?->id,
                'room_name' => $order['room'] ?? null,
                'guests' => (int) ($order['guests'] ?? 1),
                'check_in' => $order['check_in'] ?? null,
                'check_out' => $order['check_out'] ?? null,
                'nights' => (int) ($order['nights'] ?? 1),
                'amount' => (int) ($order['total'] ?? 0),
                'customer_name' => $order['customer_name'] ?? null,
                'customer_email' => $order['customer_email'] ?? null,
                'customer_phone' => $order['customer_phone'] ?? null,
                'pickup_vehicle' => $order['pickup_vehicle'] ?? null,
                'pickup_price' => $order['pickup_price'] ?? null,
                'pickup_passengers' => ! empty($order['passengers']) ? (int) $order['passengers'] : null,
                'pickup_location' => $order['location'] ?? null,
                'pickup_arrival_date' => ! empty($order['arrival_date']) ? $order['arrival_date'] : null,
                'pickup_time' => $order['pickup_time'] ?? null,
                'pickup_flight_number' => $order['flight_number'] ?? null,
                'status' => 'paid',
                'payment_method' => data_get($body, 'data.channel'),
                'paid_at' => $order['paid_at'] ?? now(),
            ]
        );

        if ($booking->wasRecentlyCreated) {
            // Auto-allocate an available physical room number for the booked dates.
            try {
                $booking->autoAssignRoomUnit();
            } catch (Throwable $e) {
                report($e);
            }

            // Notify the admin bell of a brand-new booking.
            Notification::send(User::admins()->get(), new BookingReceived($booking));

            // Email the guest their reservation confirmation (with the room number),
            // plus a dedicated airport pick-up confirmation when one was booked.
            if ($booking->customer_email) {
                try {
                    Mail::to($booking->customer_email)->send(new BookingReservation($booking));

                    if ($booking->isPickup()) {
                        Mail::to($booking->customer_email)->send(new PickupConfirmation($booking));
                    }
                } catch (Throwable $e) {
                    report($e);
                }
            }
        }
    } catch (Throwable $e) {
        report($e);
    }

    // Notify the hotel of the confirmed, paid reservation.
    try {
        $recipient = config('mail.contact_to', config('mail.from.address'));
        Mail::to($recipient)->send(new BookingRequest([
            'room' => $order['room'],
            'price' => $order['total_label'],
            'guests' => $order['guests'],
            'check_in' => $order['check_in'],
            'check_out' => $order['check_out'],
            'name' => $order['customer_name'],
            'email' => $order['customer_email'],
            'phone' => $order['customer_phone'],
            'pickup_vehicle' => $order['pickup_vehicle'],
            'pickup_price' => $order['pickup_price'],
            'location' => $order['location'],
            'passengers' => $order['passengers'],
            'arrival_date' => $order['arrival_date'],
            'pickup_time' => $order['pickup_time'],
            'flight_number' => $order['flight_number'],
        ]));
    } catch (Throwable $e) {
        report($e);
    }

    return redirect()->route('checkout.success');
})->name('checkout.callback');

// Step 4 — reservation successful screen.
Route::get('reservation-successful', function () {
    $order = session('order');

    if (! $order) {
        return redirect()->route('rooms');
    }

    return view('reservation-success', ['order' => $order]);
})->name('checkout.success');

// Printable receipt.
Route::get('reservation-successful/receipt', function () {
    $order = session('order');

    if (! $order) {
        return redirect()->route('rooms');
    }

    return view('receipt', ['order' => $order]);
})->name('checkout.receipt');

/*
|--------------------------------------------------------------------------
| Spa & Wellness reservation flow (Paystack) — parallels the room checkout
|--------------------------------------------------------------------------
*/

$buildSpaBooking = function (array $data): array {
    $guests = max(1, (int) $data['guests']);
    $naira = fn ($n) => '₦'.number_format($n);

    $services = SpaService::active()
        ->whereIn('slug', (array) $data['services'])
        ->ordered()->get()
        ->map(fn ($s) => [
            'name' => $s->name,
            'slug' => $s->slug,
            'price' => $s->price,
            'guests' => $guests,
            'subtotal' => $s->price * $guests,
            'price_label' => $naira($s->price),
            'subtotal_label' => $naira($s->price * $guests),
        ])->values()->all();

    $subtotal = collect($services)->sum('subtotal');
    $fees = 2000;                              // convenience fee
    $taxes = (int) round($subtotal * 0.075);   // VAT 7.5%
    $total = $subtotal + $fees + $taxes;
    $date = ! empty($data['date']) ? Carbon::parse($data['date']) : null;

    return [
        'services' => $services,
        'guests' => $guests,
        'date' => $date?->toDateString(),
        'date_label' => $date?->format('F j, Y') ?? '—',
        'time' => $data['time'] ?? null,
        'special_request' => $data['special_request'] ?? null,
        'subtotal' => $subtotal,
        'subtotal_label' => $naira($subtotal),
        'fees' => $fees,
        'fees_label' => $naira($fees),
        'taxes' => $taxes,
        'taxes_label' => $naira($taxes),
        'total' => $total,
        'total_label' => $naira($total),
        'total_kobo' => $total * 100,
    ];
};

// Step 1 — "Complete Reservation" from the spa popup lands here.
Route::post('spa-wellness/reserve', function () use ($buildSpaBooking) {
    $data = request()->validate([
        'services' => ['required', 'array', 'min:1'],
        'services.*' => ['string', 'exists:spa_services,slug'],
        'guests' => ['required', 'integer', 'min:1', 'max:30'],
        'date' => ['required', 'date'],
        'time' => ['nullable', 'string', 'max:20'],
        'special_request' => ['nullable', 'string', 'max:1000'],
    ]);

    $booking = $buildSpaBooking($data);
    if (empty($booking['services'])) {
        return back()->with('toast', ['type' => 'error', 'message' => 'Please choose at least one spa service.']);
    }

    session(['spa_booking' => $booking]);

    return redirect()->route('spa.checkout');
})->name('spa.checkout.start');

// Step 2 — the spa checkout page (customer details + summary + Paystack).
Route::get('spa-wellness/checkout', function () {
    $booking = session('spa_booking');
    if (! $booking) {
        return redirect()->route('spa');
    }

    return view('spa-checkout', [
        'booking' => $booking,
        'paystackKey' => config('services.paystack.public_key'),
    ]);
})->name('spa.checkout');

// Step 3 — Paystack verification + persist the spa booking.
Route::get('spa-wellness/callback', function () {
    $reference = request('reference');
    $booking = session('spa_booking');

    if (! $reference || ! $booking) {
        return redirect()->route('spa');
    }

    $secret = config('services.paystack.secret_key');

    try {
        $response = Http::withToken($secret)->acceptJson()
            ->get(rtrim(config('services.paystack.payment_url'), '/').'/transaction/verify/'.$reference);
        $body = $response->json();

        if (! $response->ok() || data_get($body, 'data.status') !== 'success') {
            return redirect()->route('spa.checkout')->with('toast', [
                'type' => 'error',
                'message' => 'We could not verify your payment. If you were charged, contact us with reference: '.$reference,
            ]);
        }
    } catch (Throwable $e) {
        report($e);

        return redirect()->route('spa.checkout')->with('toast', [
            'type' => 'error', 'message' => 'Payment verification failed. Please try again or contact us.',
        ]);
    }

    $order = array_merge($booking, [
        'customer_name' => data_get($body, 'data.metadata.name'),
        'customer_phone' => data_get($body, 'data.metadata.phone'),
        'customer_email' => data_get($body, 'data.customer.email'),
        'reference' => $reference,
        'paid_at' => data_get($body, 'data.paid_at'),
    ]);

    session(['spa_order' => $order]);
    session()->forget('spa_booking');

    try {
        SpaBooking::updateOrCreate(
            ['reference' => $reference],
            [
                'services' => $order['services'],
                'guests' => (int) $order['guests'],
                'date' => $order['date'] ?? null,
                'time' => $order['time'] ?? null,
                'special_request' => $order['special_request'] ?? null,
                'subtotal' => (int) $order['subtotal'],
                'fees' => (int) $order['fees'],
                'taxes' => (int) $order['taxes'],
                'total' => (int) $order['total'],
                'customer_name' => $order['customer_name'] ?? null,
                'customer_email' => $order['customer_email'] ?? null,
                'customer_phone' => $order['customer_phone'] ?? null,
                'status' => 'paid',
                'payment_method' => data_get($body, 'data.channel'),
                'paid_at' => $order['paid_at'] ?? now(),
            ]
        );
    } catch (Throwable $e) {
        report($e);
    }

    return redirect()->route('spa.checkout.success');
})->name('spa.checkout.callback');

// Step 4 — spa reservation success.
Route::get('spa-wellness/reservation-successful', function () {
    $order = session('spa_order');
    if (! $order) {
        return redirect()->route('spa');
    }

    return view('spa-success', ['order' => $order]);
})->name('spa.checkout.success');

Route::view('contact-us', 'contact')->name('contact');
Route::post('contact-us', function () {
    $data = request()->validate([
        'first_name' => ['required', 'string', 'max:120'],
        'last_name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190'],
        'phone' => ['nullable', 'string', 'max:40'],
        'message' => ['nullable', 'string', 'max:5000'],
    ]);

    // Persist the enquiry so it appears in Admin → Website CMS → Messages,
    // and notify the admin bell.
    try {
        $message = ContactMessage::create($data + ['status' => 'new']);
        Notification::send(User::admins()->get(), new MessageReceived($message));
    } catch (Throwable $e) {
        report($e);
    }

    try {
        $recipient = config('mail.contact_to', config('mail.from.address'));
        Mail::to($recipient)->send(new ContactEnquiry($data));

        // Automated acknowledgement to the guest.
        Mail::to($data['email'])->send(new ContactAcknowledgement($data));
    } catch (Throwable $e) {
        report($e);
        // The message is already saved; surface a soft notice but don't lose it.
    }

    return back()->with('toast', [
        'type' => 'success',
        'message' => 'Thanks '.$data['first_name'].'! Your message has been received — we will get back to you shortly.',
    ]);
})->name('contact.submit');

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only authentication screens.
    Route::middleware('guest')->group(function () {
        Route::get('login', Login::class)->name('login');
        Route::get('forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('verify-code', VerifyCode::class)->name('password.verify');
        Route::get('set-password', SetNewPassword::class)->name('password.set');
        Route::get('password-reset-success', ResetSuccess::class)->name('password.success');
    });

    // Authenticated admin portal.
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::post('logout', LogoutController::class)->name('logout');

        // Apartments — Rooms (full-page Livewire components)
        Route::get('apartments/rooms', Index::class)->name('rooms.index');
        Route::get('apartments/rooms/create', Edit::class)->name('rooms.create');
        Route::get('apartments/rooms/{room}/edit', Edit::class)->name('rooms.edit');
        Route::get('apartments/rooms/{room}/calendar', \App\Livewire\Admin\Rooms\Calendar::class)->name('rooms.calendar');

        // Apartments — Bookings
        Route::get('apartments/bookings', App\Livewire\Admin\Bookings\Index::class)->name('bookings.index');
        Route::get('apartments/bookings/{booking}', App\Livewire\Admin\Bookings\Show::class)->name('bookings.show');

        // Website CMS — page hub + per-page editor + contact messages
        Route::get('website-cms', App\Livewire\Admin\Cms\Index::class)->name('cms.index');
        Route::get('website-cms/page/{page}', App\Livewire\Admin\Cms\Edit::class)->name('cms.edit');
        Route::get('website-cms/messages', App\Livewire\Admin\Messages\Index::class)->name('messages.index');

        // Airport Pickups — Vehicles (fleet shown on the website pick-up popup)
        Route::get('airport-pickups/vehicles', App\Livewire\Admin\Vehicles\Index::class)->name('vehicles.index');
        Route::get('airport-pickups/bookings', App\Livewire\Admin\Vehicles\Bookings::class)->name('vehicles.bookings');

        // Spa & Wellness — services fleet + reservations
        Route::get('spa-wellness/services', App\Livewire\Admin\Spa\Services::class)->name('spa.services');
        Route::get('spa-wellness/bookings', App\Livewire\Admin\Spa\Bookings::class)->name('spa.bookings');

        // Payment — transactions captured from checkout
        Route::get('payment', App\Livewire\Admin\Payment\Index::class)->name('payment.index');
    });
});
