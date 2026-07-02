<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse($data['paymentGateway'] as $gateway)
        @php($configurations = json_decode($gateway->config, true))
        {!! Form::model($gateway, ['route' => [$base_route.'.update', $gateway->id], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'space-y-4 p-4 rounded-lg border shadow-sm bg-white']) !!}

        <div class="space-y-2">
            <div class="text-center">
                <a href="{{ $gateway->link }}" target="_blank">
                    <img src="{{ asset('assets/images/paymenticon/' . $gateway->logo . '.png') }}"
                         alt="{{ $gateway->identity }}"
                         class="mx-auto max-h-24 object-contain">
                </a>
                <div class="text-lg font-semibold mt-2">{{ $gateway->identity }}</div>
            </div>

            <div class="space-y-3">
                @if(isset($configurations))
                    @foreach($configurations as $key => $conf)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $key }}</label>
                            <input type="text" name="{{ $key }}" value="{{ $conf }}"
                                   {{ $gateway->status == 'active' ? '' : 'disabled' }}
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-200">
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="flex justify-between items-center mt-4">
                <div>
                    <div class="inline-flex items-center gap-2">
                        <span class="text-sm font-semibold">Status:</span>
                        <span class="px-2 py-1 rounded text-white text-xs {{ $gateway->status == 'active' ? 'bg-green-500' : 'bg-yellow-500' }}">
                            {{ ucfirst($gateway->status) }}
                        </span>
                    </div>
                    <div class="mt-2 space-x-1">
                        <a href="{{ route($base_route.'.active', ['id' => $gateway->id]) }}"
                           class="text-green-600 hover:underline text-sm">Activate</a>
                        <a href="{{ route($base_route.'.in-active', ['id' => $gateway->id]) }}"
                           class="text-red-600 hover:underline text-sm">Deactivate</a>
                    </div>
                </div>

                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    @empty
        <div class="col-span-full text-center text-gray-500">No payment gateway configured yet.</div>
    @endforelse
</div>
