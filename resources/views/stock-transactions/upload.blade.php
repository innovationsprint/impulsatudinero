@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Upload CSV') }}
    </h2>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <!-- Nueva sección para cargar el archivo CSV -->
    <div class="mt-10">
        <h3 class="text-lg font-medium text-gray-900">Cargar archivo CSV generado por IBKR</h3>
        <form action="{{ route('process-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded-lg shadow-md mt-4">
            @csrf
            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700">Busca tu archivo y súbelo...</label><br>
                <input type="file" name="csv_file" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                @error('csv_file')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-center">
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto">Cargar CSV</button>
            </div>
        </form>
    </div>
</div>
@endsection
