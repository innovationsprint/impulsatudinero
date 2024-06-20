@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Detalles de la Acción') }} {{ $stock_symbol }}
</h2>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-end mb-4">
        <a href="{{ route('dashboard') }}"
            class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto">
            {{ __('Volver al Dashboard') }}
        </a>

    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="mb-4">
        @if($transactions->isNotEmpty() && $transactions->first()->logo_path)
        <div class="mt-4">
            <h4 class="text-md font-semibold text-gray-700 mb-2">{{ __('Logo Actual') }}</h4>
            <img src="{{ Storage::url($transactions->first()->logo_path) }}" alt="{{ $stock_symbol }} Logo"
                class="h-20 w-20">
        </div>
        @endif
    </div>

    @if($transactions->isNotEmpty())
    <div class="mb-4">
        <p><strong># de Acciones:</strong> {{ $totalQuantity }}</p>
        <p><strong>Precio Promedio por Acción:</strong> {{ number_format($averagePrice, 2) }}</p>
        <p><strong>Comisión Total Pagada:</strong> {{ number_format($totalCommission, 2) }}</p>
    </div>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th
                        class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipo de Transacción
                    </th>
                    <th
                        class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cantidad
                    </th>
                    <th
                        class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Precio por Acción
                    </th>
                    <th
                        class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Comisión
                    </th>
                    <th
                        class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha de Transacción
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{
                        ucfirst($transaction->transaction_type) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{
                        $transaction->formatted_quantity }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{
                        number_format($transaction->price_per_share, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{
                        number_format($transaction->commission, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{
                        \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No hay transacciones para esta acción.</p>
    @endif
</div>
@endsection