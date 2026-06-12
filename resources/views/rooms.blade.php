<x-layouts.web title="Rooms & Apartment — Retiro Del Rocio"
    description="Browse rooms and apartments at Retiro Del Rocio in Jos — Rocio Rooms, Alba Suites, Rocio Loft Residence, and Brisa Residence. Luxury suites with curated comfort and amenities.">

    @php
        $filters = [
            ['label' => 'Rocio Rooms', 'active' => false],
            ['label' => 'Alba suites', 'active' => false],
            ['label' => 'Rocio Loft Residence', 'active' => true],
            ['label' => 'Brisa Residence', 'active' => false],
        ];

        $rooms = [
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 8.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 9.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 10.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 2.jpg'],
        ];

        $offers = [
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 11.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 12.png'],
        ];
    @endphp

    {{-- ============================ HEADER / FILTER ============================ --}}
    <section class="w-full pt-10 pb-12 lg:pt-12 lg:pb-16">
        <x-layouts.container class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between lg:gap-10">
            <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:whitespace-nowrap lg:text-[52px] xl:text-[60px] lg:leading-none">
                Rocio Loft Residence
            </h1>

            <div class="flex min-w-0 flex-col gap-4 lg:shrink-0 lg:items-end">
                <button type="button" class="flex items-center gap-2 self-start text-[21px] font-medium tracking-tight text-white lg:self-end">
                    Filter search
                    <svg class="size-[27px] shrink-0" viewBox="0 0 27 27" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-miterlimit="10">
                        <path d="M23.9063 13.5H10.0069M5.10075 13.5H3.09375M5.10075 13.5C5.10075 12.8496 5.35914 12.2258 5.81907 11.7658C6.279 11.3059 6.90281 11.0475 7.55325 11.0475C8.20369 11.0475 8.8275 11.3059 9.28743 11.7658C9.74736 12.2258 10.0058 12.8496 10.0058 13.5C10.0058 14.1504 9.74736 14.7742 9.28743 15.2342C8.8275 15.6941 8.20369 15.9525 7.55325 15.9525C6.90281 15.9525 6.279 15.6941 5.81907 15.2342C5.35914 14.7742 5.10075 14.1504 5.10075 13.5ZM23.9063 20.9329H17.4398M17.4398 20.9329C17.4398 21.5835 17.1807 22.208 16.7207 22.668C16.2607 23.1281 15.6367 23.3865 14.9861 23.3865C14.3357 23.3865 13.7119 23.127 13.2519 22.6671C12.792 22.2071 12.5336 21.5833 12.5336 20.9329M17.4398 20.9329C17.4398 20.2823 17.1807 19.6589 16.7207 19.1989C16.2607 18.7388 15.6367 18.4804 14.9861 18.4804C14.3357 18.4804 13.7119 18.7388 13.2519 19.1987C12.792 19.6586 12.5336 20.2824 12.5336 20.9329M12.5336 20.9329H3.09375M23.9063 6.06713H20.4131M15.507 6.06713H3.09375M15.507 6.06713C15.507 5.41668 15.7654 4.79288 16.2253 4.33295C16.6853 3.87301 17.3091 3.61462 17.9595 3.61462C18.2816 3.61462 18.6005 3.67806 18.898 3.80131C19.1956 3.92456 19.4659 4.10521 19.6937 4.33295C19.9214 4.56068 20.1021 4.83104 20.2253 5.12859C20.3486 5.42614 20.412 5.74506 20.412 6.06713C20.412 6.38919 20.3486 6.70811 20.2253 7.00566C20.1021 7.30321 19.9214 7.57357 19.6937 7.8013C19.4659 8.02904 19.1956 8.20969 18.898 8.33294C18.6005 8.45619 18.2816 8.51962 17.9595 8.51962C17.3091 8.51962 16.6853 8.26124 16.2253 7.8013C15.7654 7.34137 15.507 6.71757 15.507 6.06713Z"/>
                    </svg>
                </button>

                {{-- Single straight row; scrolls horizontally on small screens instead of wrapping --}}
                <div class="no-scrollbar flex max-w-full gap-[14px] overflow-x-auto lg:justify-end">
                    @foreach ($filters as $filter)
                        <button type="button" @class([
                            'flex h-[58px] shrink-0 items-center justify-center whitespace-nowrap rounded-[8px] border-[0.5px] border-black/20 px-[26px] text-[18px] font-bold tracking-tight text-black transition lg:text-[20px]',
                            'bg-[#f38c00]' => $filter['active'],
                            'bg-[#f6f6f6]/75 hover:bg-[#f6f6f6]' => ! $filter['active'],
                        ])>
                            {{ $filter['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </x-layouts.container>
    </section>

    {{-- ============================ ROOMS GRID ============================ --}}
    <section class="w-full">
        <x-layouts.container class="grid grid-cols-1 gap-[15px] md:grid-cols-2">
            @foreach ($rooms as $room)
                <x-layouts.room-card :image="$room['image']" :name="$room['name']" :price="$room['price']" />
            @endforeach
        </x-layouts.container>
    </section>

    {{-- ===================== EXPLORE OUR EXCLUSIVE OFFERS ===================== --}}
    <section class="w-full py-16 lg:py-24">
        <x-layouts.container class="flex flex-col gap-[60px] lg:gap-[90px]">
            <h2 class="text-center text-2xl tracking-tight text-white sm:text-3xl lg:text-[35px]">Explore our exclusive offers</h2>

            <div class="grid grid-cols-1 gap-[15px] md:grid-cols-2">
                @foreach ($offers as $room)
                    <x-layouts.room-card :image="$room['image']" :name="$room['name']" :price="$room['price']" />
                @endforeach
            </div>
        </x-layouts.container>
    </section>
</x-layouts.web>
