@extends('layouts.app')
@section('title','Nieuwe thread')

@section('content')
<div class="w-full flex flex-col items-center">
    <h1 class="text-2xl font-bold mb-2">Nieuw gesprek beginnen</h1>
    <p class="text-sm text-black opacity-80 font-medium mb-10 text-center">Chat snel en gemakkelijk met jouw coach.<br>Beschrijf wat je nodig hebt of geef blessures door.</p>
    <form method="POST" action="{{ route('client.threads.store') }}" class="max-w-lg">
        @csrf
        <label class="block text-sm mb-1 font-medium">Onderwerp</label>
        <input name="subject" class="bg-white w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c8ab7a] focus:border-[#c8ab7a]" placeholder="Bijv. Vraag over schema">
        <button class="w-full px-6 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded mt-4">Aanmaken</button>
    </form>
</div>
@endsection