<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Daily Report') }} - {{ $todoList->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('daily-reports.store') }}" id="reportForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="todo_list_id" value="{{ $todoList->id }}">

                @php
                    $todoType = $todoList->type;

                    // Determine which sections to show based on todo type
                    $showManPower = $todoType === 'man_power';
                    $showFinishGood = $todoType === 'finish_good';
                    $showRawMaterial = $todoType === 'raw_material';
                    $showWarehouse = $todoType === 'gudang';
                    $showSupplier = $todoType === 'supplier_datang';

                    // If type is 'daily', check items or show all by default
                    if ($todoType === 'daily') {
                        // Assuming daily report includes all sections, or we can check items if loaded
                        // For now, let's enable all sections for daily report
                        $showManPower = true;
                        $showFinishGood = true;
                        $showRawMaterial = true;
                        $showWarehouse = true;
                        $showSupplier = true;
                    }
                @endphp

                <!-- Session and Photo Upload (Only for Daily Reports or if needed) -->
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Informasi Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Report</label>
                            <input type="date" name="report_date" value="{{ old('report_date', date('Y-m-d')) }}"
                                required class="w-full rounded-md border-gray-300">
                            @error('report_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($todoType === 'daily')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sesi</label>
                                <select name="session" class="w-full rounded-md border-gray-300">
                                    @php
                                        $hour = (int) date('H');
                                        $defaultSession = 'morning';
                                        if ($hour >= 12 && $hour < 16) {
                                            $defaultSession = 'afternoon';
                                        }
                                        if ($hour >= 16) {
                                            $defaultSession = 'evening';
                                        }
                                    @endphp
                                    <option value="morning"
                                        {{ old('session', $defaultSession) == 'morning' ? 'selected' : '' }}>Pagi (00:00
                                        - 11:59)</option>
                                    <option value="afternoon"
                                        {{ old('session', $defaultSession) == 'afternoon' ? 'selected' : '' }}>Siang
                                        (12:00 - 15:59)</option>
                                    <option value="evening"
                                        {{ old('session', $defaultSession) == 'evening' ? 'selected' : '' }}>Sore (16:00
                                        - 23:59)</option>
                                </select>
                                @error('session')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Bukti (Umum)</label>
                            <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png"
                                class="w-full rounded-md border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB.</p>
                            @error('photo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <!-- Image Preview -->
                            <div id="imagePreviewContainer" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                <img id="imagePreview" src="#" alt="Image Preview"
                                    class="max-w-xs rounded-lg shadow-md border border-gray-200">
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    const photoInput = document.getElementById('photoInput');
                    const previewContainer = document.getElementById('imagePreviewContainer');
                    const previewImage = document.getElementById('imagePreview');

                    if (photoInput) {
                        photoInput.addEventListener('change', function() {
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    previewImage.src = e.target.result;
                                    previewContainer.classList.remove('hidden');
                                }
                                reader.readAsDataURL(file);
                            } else {
                                previewContainer.classList.add('hidden');
                                previewImage.src = '#';
                            }
                        });
                    }
                </script>

                @if ($showManPower)
                    <!-- Man Power Section -->
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Man Power</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan Masuk</label>
                                <input type="number" name="man_power[employees_present]"
                                    value="{{ old('man_power.employees_present', 0) }}" required min="0"
                                    class="w-full rounded-md border-gray-300">
                                @error('man_power.employees_present')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan Tidak Masuk</label>
                                <input type="number" name="man_power[employees_absent]"
                                    value="{{ old('man_power.employees_absent', 0) }}" required min="0"
                                    class="w-full rounded-md border-gray-300">
                                @error('man_power.employees_absent')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showFinishGood)
                    <!-- Stock Finish Good Section -->
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Stock Finish Good (Karton)</h3>
                        <div id="stockFinishGoodContainer">
                            <div class="stock-finish-good-item mb-4 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                                    <input type="text" name="stock_finish_good[0][item_name]"
                                        class="w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity
                                        (Karton)</label>
                                    <input type="number" name="stock_finish_good[0][quantity]" min="0"
                                        class="w-full rounded-md border-gray-300">
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addStockFinishGood()"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Add More
                        </button>
                    </div>
                @endif

                @if ($showRawMaterial)
                    <!-- Stock Raw Material Section -->
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Stock Raw Material (Kg)</h3>
                        <div id="stockRawMaterialContainer">
                            <div class="stock-raw-material-item mb-4 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                                    <input type="text" name="stock_raw_material[0][item_name]"
                                        class="w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity (Kg)</label>
                                    <input type="number" step="0.01" name="stock_raw_material[0][quantity]"
                                        min="0" class="w-full rounded-md border-gray-300">
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addStockRawMaterial()"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Add More
                        </button>
                    </div>
                @endif

                @if ($showWarehouse)
                    <!-- Warehouse Condition Section -->
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Kondisi Gudang</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                            @foreach (['cs1', 'cs2', 'cs3', 'cs4', 'cs5', 'cs6'] as $index => $warehouse)
                                <div class="border p-3 rounded-md">
                                    <h4 class="font-medium mb-2 text-center">{{ strtoupper($warehouse) }}</h4>
                                    <div class="space-y-1">
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                name="warehouse_conditions[{{ $index }}][sangat_bersih]"
                                                value="1" class="rounded border-gray-300">
                                            <span class="ml-2 text-sm">Sangat Bersih</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                name="warehouse_conditions[{{ $index }}][bersih]"
                                                value="1" class="rounded border-gray-300">
                                            <span class="ml-2 text-sm">Bersih</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                name="warehouse_conditions[{{ $index }}][cukup_bersih]"
                                                value="1" class="rounded border-gray-300">
                                            <span class="ml-2 text-sm">Cukup Bersih</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                name="warehouse_conditions[{{ $index }}][kurang_bersih]"
                                                value="1" class="rounded border-gray-300">
                                            <span class="ml-2 text-sm">Kurang Bersih</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                name="warehouse_conditions[{{ $index }}][tidak_bersih]"
                                                value="1" class="rounded border-gray-300">
                                            <span class="ml-2 text-sm">Tidak Bersih</span>
                                        </label>
                                    </div>
                                    <input type="hidden" name="warehouse_conditions[{{ $index }}][warehouse]"
                                        value="{{ $warehouse }}">
                                    <div class="mt-2">
                                        <label for="notes_{{ $warehouse }}"
                                            class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea name="warehouse_conditions[{{ $index }}][notes]" id="notes_{{ $warehouse }}" rows="2"
                                            class="w-full rounded-md border-gray-300"></textarea>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto</label>
                                        <input type="file" name="warehouse_conditions[{{ $index }}][photo]"
                                            accept="image/jpeg,image/png"
                                            class="w-full text-sm text-gray-500 rounded-md border border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('warehouse_conditions')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('warehouse_conditions.*.warehouse')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @if ($showSupplier)
                    <!-- Suppliers Section -->
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">5. Supplier Datang</h3>
                        <div id="suppliersContainer">
                            <div class="supplier-item mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                                    <input type="text" name="suppliers[0][supplier_name]"
                                        class="w-full rounded-md border-gray-300">
                                    @error('suppliers.0.supplier_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                                    <input type="text" name="suppliers[0][jenis_barang]"
                                        placeholder="Contoh: Bahan Baku A, Produk B, dll"
                                        class="w-full rounded-md border-gray-300">
                                    @error('suppliers.0.jenis_barang')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addSupplier()"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Add More
                        </button>
                    </div>
                @endif

                <!-- Report Date (Removed as it is moved to top) -->
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Report</label>
                    <input type="date" name="report_date_hidden" value="{{ old('report_date', date('Y-m-d')) }}"
                        class="w-full rounded-md border-gray-300">
                </div>

                @error('todo_list_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                @if ($errors->any())
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded">
                        <p class="text-red-800 font-semibold mb-2">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('supervisor.todos') }}"
                class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Save Report
            </button>
        </div>
        </form>
    </div>
    </div>

    <script>
        let stockFinishGoodIndex = 1;
        let stockRawMaterialIndex = 1;
        let supplierIndex = 1;

        function addStockFinishGood() {
            const container = document.getElementById('stockFinishGoodContainer');
            const div = document.createElement('div');
            div.className = 'stock-finish-good-item mb-4 grid grid-cols-2 gap-4';
            div.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                    <input type="text" name="stock_finish_good[${stockFinishGoodIndex}][item_name]" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity (Karton)</label>
                    <input type="number" name="stock_finish_good[${stockFinishGoodIndex}][quantity]" min="0" class="w-full rounded-md border-gray-300">
                </div>
            `;
            container.appendChild(div);
            stockFinishGoodIndex++;
        }

        function addStockRawMaterial() {
            const container = document.getElementById('stockRawMaterialContainer');
            const div = document.createElement('div');
            div.className = 'stock-raw-material-item mb-4 grid grid-cols-2 gap-4';
            div.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                    <input type="text" name="stock_raw_material[${stockRawMaterialIndex}][item_name]" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity (Kg)</label>
                    <input type="number" step="0.01" name="stock_raw_material[${stockRawMaterialIndex}][quantity]" min="0" class="w-full rounded-md border-gray-300">
                </div>
            `;
            container.appendChild(div);
            stockRawMaterialIndex++;
        }

        function addSupplier() {
            const container = document.getElementById('suppliersContainer');
            const div = document.createElement('div');
            div.className = 'supplier-item mb-4 grid grid-cols-1 md:grid-cols-2 gap-4';
            div.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                    <input type="text" name="suppliers[${supplierIndex}][supplier_name]" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text" name="suppliers[${supplierIndex}][jenis_barang]" placeholder="Contoh: Bahan Baku A, Produk B, dll" class="w-full rounded-md border-gray-300">
                </div>
            `;
            container.appendChild(div);
            supplierIndex++;
        }
    </script>
</x-app-layout>
