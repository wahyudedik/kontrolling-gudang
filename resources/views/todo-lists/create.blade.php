<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create To Do List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('todo-lists.store') }}">
                @csrf
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full rounded-md border-gray-300">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" required class="w-full rounded-md border-gray-300">
                            <option value="">Select Type</option>
                            <option value="man_power" {{ old('type') == 'man_power' ? 'selected' : '' }}>Man Power</option>
                            <option value="finish_good" {{ old('type') == 'finish_good' ? 'selected' : '' }}>Finish Good</option>
                            <option value="raw_material" {{ old('type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                            <option value="gudang" {{ old('type') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                            <option value="supplier_datang" {{ old('type') == 'supplier_datang' ? 'selected' : '' }}>Supplier Datang</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" value="{{ old('date') }}" 
                               class="w-full rounded-md border-gray-300">
                        @error('date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" required 
                               class="w-full rounded-md border-gray-300">
                        @error('due_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Supervisors</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3">
                            @foreach($supervisors as $supervisor)
                                <label class="flex items-center">
                                    <input type="checkbox" name="supervisor_ids[]" value="{{ $supervisor->id }}" 
                                           {{ in_array($supervisor->id, old('supervisor_ids', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">{{ $supervisor->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Pilih supervisor yang akan menerima To Do List ini</p>
                        @error('supervisor_ids')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('todo-lists.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                            Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

