@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')
<h1 class="text-2xl font-bold mb-2">Goededag {{ auth()->user()->name }}! 👋</h1>
<p class="text-sm text-black opacity-80 font-medium mb-10">Welkom op jouw persoonlijke training omgeving.<br>Zie hier jouw trainingsschema, chat met je coach of bekijk onze supplementen.</p>

<h2 class="text-lg font-bold mb-2">Snelkoppelingen</h2>
<div class="grid gap-3 grid-cols-1 sm:grid-cols-4 mb-6">
    <a href="#"
       class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
        <div class="font-semibold text-white">Mijn trainingsplan</div>
        <p class="text-sm text-white">Bekijk je trainingsplan</p>
    </a>
    <a href="#"
       class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
        <div class="font-semibold text-white">Nieuwe chat met coach</div>
        <p class="text-sm text-white">Start een nieuw gesprek</p>
    </a>
    <a href="#"
       class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
        <div class="font-semibold text-white">Alle gesprekken met coach</div>
        <p class="text-sm text-white">Overzicht & zoeken</p>
    </a>
</div>

<h2 class="text-lg font-bold mb-2">Informatie</h2>
<section class="grid gap-4 grid-cols-1 md:grid-cols-2">
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
        <div class="text-sm text-black font-semibold opacity-50 mb-1">Informatie card 1</div>
        <div class="text-3xl font-bold text-[#c8ab7a] mb-4">
            12345.67
        </div>
    </div>
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
        <div class="text-sm text-black font-semibold opacity-50 mb-1">Informatie card 2</div>
        <div class="text-3xl font-bold text-[#c8ab7a] mb-4">
            12345.67
        </div>
    </div>
</section>
@endsection