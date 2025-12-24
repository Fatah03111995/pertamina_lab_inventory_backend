<div class="space-y-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Companies</div>
            <div class="text-2xl font-semibold">{{ $this->companies }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Locations</div>
            <div class="text-2xl font-semibold">{{ $this->locations }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Cylinders</div>
            <div class="text-2xl font-semibold">{{ $this->cylinders }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Events</div>
            <div class="text-2xl font-semibold">{{ $this->events }}</div>
        </div>
    </div>

    <div class="mt-4 bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600 mb-2">Cylinders Per Type</div>
        <ul class="space-y-2">
            @foreach($this->cylindersPerType as $t)
                <li class="flex justify-between">
                    <span>{{ $t['name'] }}</span>
                    <span class="font-medium">{{ $t['count'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
