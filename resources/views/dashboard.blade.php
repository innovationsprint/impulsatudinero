@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Resumen del Portafolio de Inversiones') }}
</h2>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @livewire('stock-transactions-table')

</div>
@endsection