<?php

use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Mail\BookingRequest;
use App\Mail\ContactEnquiry;
use Illuminate\Support\Facades\Mail;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\ResetSuccess;
use App\Livewire\Admin\Auth\SetNewPassword;
use App\Livewire\Admin\Auth\VerifyCode;
use Illuminate\Support\Facades\Route;

// Public website.
Route::view('/', 'welcome')->name('home');
Route::view('rooms-apartment', 'rooms')->name('rooms');
Route::view('rooms-apartment/pandoras-suite', 'room-detail')->name('rooms.show');

/*
|--------------------------------------------------------------------------
| Checkout flow (Paystack)
|--------------------------------------------------------------------------
*/

// Build the full priced booking summary from the raw reservation input.
$buildBooking = function (array $b): array {
    $pricePerNight = 350000;
    $checkIn = \Illuminate\Support\Carbon::parse($b['check_in']);
    $checkOut = \Illuminate\Support\Carbon::parse($b['check_out']);
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
        $response = \Illuminate\Support\Facades\Http::withToken($secret)
            ->acceptJson()
            ->get(rtrim(config('services.paystack.payment_url'), '/').'/transaction/verify/'.$reference);

        $body = $response->json();

        if (! $response->ok() || data_get($body, 'data.status') !== 'success') {
            return redirect()->route('checkout')->with('toast', [
                'type' => 'error',
                'message' => 'We could not verify your payment. If you were charged, please contact us with your reference: '.$reference,
            ]);
        }
    } catch (\Throwable $e) {
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
    } catch (\Throwable $e) {
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
Route::view('contact-us', 'contact')->name('contact');
Route::post('contact-us', function () {
    $data = request()->validate([
        'first_name' => ['required', 'string', 'max:120'],
        'last_name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190'],
        'phone' => ['nullable', 'string', 'max:40'],
        'message' => ['nullable', 'string', 'max:5000'],
    ]);

    try {
        $recipient = config('mail.contact_to', config('mail.from.address'));
        Mail::to($recipient)->send(new ContactEnquiry($data));
    } catch (\Throwable $e) {
        report($e);

        return back()
            ->withInput()
            ->with('toast', [
                'type' => 'error',
                'message' => 'Sorry, we could not send your message right now. Please try again or email us directly.',
            ]);
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
    });
});
