<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Details') }} - {{ $dailyReport->report_date->format('d M Y') }}
            </h2>
            <div class="flex gap-2">
                @if($dailyReport->supervisor_id === auth()->id())
                    <a href="{{ route('daily-reports.edit', $dailyReport) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                        Edit
                    </a>
                @endif
                <a href="{{ route('daily-reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Man Power -->
            @if($dailyReport->manPower)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Man Power</h3>
                    <p>Karyawan Masuk: {{ $dailyReport->manPower->employees_present }}</p>
                    <p>Karyawan Tidak Masuk: {{ $dailyReport->manPower->employees_absent }}</p>
                </div>
            @endif

            <!-- Stock Finish Good -->
            @if($dailyReport->stockFinishGood->count() > 0)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Stock Finish Good</h3>
                    <ul class="list-disc list-inside">
                        @foreach($dailyReport->stockFinishGood as $item)
                            <li>{{ $item->item_name }} - {{ $item->quantity }} karton</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Stock Raw Material -->
            @if($dailyReport->stockRawMaterial->count() > 0)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Stock Raw Material</h3>
                    <ul class="list-disc list-inside">
                        @foreach($dailyReport->stockRawMaterial as $item)
                            <li>{{ $item->item_name }} - {{ $item->quantity }} kg</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Warehouse Conditions -->
            @if($dailyReport->warehouseConditions->count() > 0)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Kondisi Gudang</h3>
                    <div class="grid grid-cols-6 gap-4">
                        @php
                            $checkLabels = [
                                1 => 'Sangat Bersih',
                                2 => 'Bersih',
                                3 => 'Cukup Bersih',
                                4 => 'Kurang Bersih',
                                5 => 'Tidak Bersih'
                            ];
                        @endphp
                        @foreach($dailyReport->warehouseConditions as $condition)
                            <div>
                                <h4 class="font-medium mb-2">{{ strtoupper($condition->warehouse) }}</h4>
                                @if($condition->check_1) <p class="text-sm">✓ {{ $checkLabels[1] }}</p> @endif
                                @if($condition->check_2) <p class="text-sm">✓ {{ $checkLabels[2] }}</p> @endif
                                @if($condition->check_3) <p class="text-sm">✓ {{ $checkLabels[3] }}</p> @endif
                                @if($condition->check_4) <p class="text-sm">✓ {{ $checkLabels[4] }}</p> @endif
                                @if($condition->check_5) <p class="text-sm">✓ {{ $checkLabels[5] }}</p> @endif
                                @if($condition->notes)
                                    <p class="text-sm text-gray-600 mt-2">{{ $condition->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Suppliers -->
            @if($dailyReport->suppliers->count() > 0)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Supplier Datang</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Barang</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($dailyReport->suppliers as $supplier)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->supplier_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->jenis_barang ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

