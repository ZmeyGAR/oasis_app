<div class="relative flex rounded-md">
    <div class="flex">
        <div class="px-2 py-3">
            {{-- <div class="h-10 w-10">
                <img src="{{ url('/storage/'.$image.'') }}" alt="{{ $name }}" role="img" class="h-full w-full rounded-full overflow-hidden shadow object-cover" />
            </div> --}}
        </div>

        <div class="flex flex-col justify-center py-2 pl-3">
            <p class="pb-1 text-sm font-bold">{{ $customer_name }}</p>

            <div class="flex flex-col items-start">
                <p class="text-xs leading-5">{{ $address_name }}</p>
            </div>
            <div class="flex flex-col items-start">
                <p class="text-xs leading-5">{{ $full_address }}</p>
            </div>
            <div class="flex flex-col items-start">
                <p class="text-xs leading-5">{{ $phone }}</p>
            </div>

        </div>
    </div>
</div>
