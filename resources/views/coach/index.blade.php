@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')
<h1 class="text-2xl font-bold mb-2">Goededag {{ auth()->user()->name }}! 👋</h1>
<p class="text-sm text-black opacity-80 font-medium mb-10">Welkom op jouw persoonlijke training omgeving.<br>Beheer jouw klanten, chat met je klanten en maak trainingsschemas aan.</p>

<h2 class="text-lg font-bold mb-2">Snelkoppelingen</h2>
<div class="grid gap-3 grid-cols-1 sm:grid-cols-3 mb-6">
    <a href="{{ url('/coach/threads') }}"
       class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
        <div class="font-semibold text-white">Alle gesprekken</div>
        <p class="text-sm text-white">Bekijk en beantwoord gesprekken</p>
    </a>
    <a href="{{ url('/coach/claim-clients') }}"
       class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
        <div class="font-semibold text-white">Ongeclaimde clients</div>
        <p class="text-sm text-white">Claim nieuwe aanmeldingen</p>
    </a>
    <a href="{{ url('/coach/clients') }}"
       class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
        <div class="font-semibold text-white">Mijn klanten</div>
        <p class="text-sm text-white">Bekijk alle klanten die aan jou gekoppeld zijn</p>
    </a>
</div>

@php
    use App\Models\User;
    use App\Models\Order;

    $totalClients = User::where('role', 'client')->count();
    $totalRevenue = Order::where('status','paid')->sum('amount_cents') / 100;
    $asOf = $asOf ?? now()->format('d-m-Y H:i');
@endphp
<h2 class="text-lg font-bold mb-2">Informatie</h2>
<section class="grid gap-4 grid-cols-1 md:grid-cols-2 mb-4">
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
        <div class="text-sm text-black font-semibold opacity-50 mb-1">Totaal klanten</div>
        <div class="text-3xl font-bold text-[#c8ab7a] mb-2">
            {{ number_format($totalClients, 0, ',', '.') }}
        </div>
        <div class="text-[12px] text-gray-500">
            Status t/m {{ $asOf }}
        </div>
    </div>
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
        <div class="text-sm text-black font-semibold opacity-50 mb-1">Totale omzet</div>
        <div class="text-3xl font-bold text-[#c8ab7a] mb-2">
            € {{ number_format($totalRevenue, 2, ',', '.') }}
        </div>
        <div class="text-[12px] text-gray-500">
            Status t/m {{ $asOf }}
        </div>
    </div>
</section>
@endsection