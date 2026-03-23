@extends('layouts.app')
@section('title', 'Oefening uitleg')

@section('content')
<h1 class="text-2xl font-bold mb-2">Oefening uitleg</h1>
<p class="text-sm text-black opacity-80 font-medium mb-10">Bekijk hier uitlegvideo's van oefeningen uit jouw trainingsschema.<br>Zo weet je altijd hoe je een oefening correct uitvoert.</p>

<div class="grid grid-cols-2 gap-4">
    <div class="p-5 bg-white rounded-3xl border border-gray-300 flex gap-4">
        <div class="min-w-[200px] max-w-[200px] relative cursor-pointer group" x-data="{ playing: false }"
             @click="const v = $refs.vid; if (v.paused) { v.play(); playing = true; } else { v.pause(); playing = false; }">
            <video class="rounded-2xl w-full" x-ref="vid"
                   @ended="playing = false"
                   src="/assets/videos/videotheek/HYX_9090_HipTransition_V1.mp4"></video>
            <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                 :class="playing ? 'opacity-0 group-hover:opacity-100' : 'opacity-100'">
                <div class="w-14 h-14 rounded-full bg-black/50 flex items-center justify-center">
                    <i class="fa-solid text-white text-lg" :class="playing ? 'fa-pause' : 'fa-play ml-1'"></i>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <p class="text-lg font-bold mb-2">90/90 Hip Transition (Variant 1)</p>
            <p class="font-medium text-sm">Plaats de voeten 30-40 cm uit elkaar.</p>
            <p class="font-medium text-sm mt-2">Houd beide voeten op dezelfde plek en maak een transitie van links naar rechts vanuit de heup.</p>
            <p class="font-medium text-sm mt-2">Bij variant 2 ga je door vanuit de extensie en duw je vanuit je heup jezelf compleet omhoog.</p>
            <p class="font-semibold text-sm flex-1 flex items-end text-[#c8ab7a]">Uitleg door Nicky & Roy</p>
        </div>
    </div>
    <div class="p-5 bg-white rounded-3xl border border-gray-300 flex gap-4">
        <div class="min-w-[200px] max-w-[200px] relative cursor-pointer group" x-data="{ playing: false }"
             @click="const v = $refs.vid; if (v.paused) { v.play(); playing = true; } else { v.pause(); playing = false; }">
            <video class="rounded-2xl w-full" x-ref="vid"
                   @ended="playing = false"
                   src="/assets/videos/videotheek/HYX_BarbellDeadlift_V1.mp4"></video>
            <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                 :class="playing ? 'opacity-0 group-hover:opacity-100' : 'opacity-100'">
                <div class="w-14 h-14 rounded-full bg-black/50 flex items-center justify-center">
                    <i class="fa-solid text-white text-lg" :class="playing ? 'fa-pause' : 'fa-play ml-1'"></i>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <p class="text-lg font-bold mb-2">Barbell Deadlift</p>
            <p class="font-medium text-sm">Plaats je voeten parallel naast elkaar.</p>
            <p class="font-medium text-sm mt-2">Plaats je handen op de barbell op heup lengte en zorg dat je je rug recht houdt.</p>
            <p class="font-medium text-sm mt-2">Trek de barbell vanuit de bilspier en de hamstring recht omhoog.</p>
            <p class="font-medium text-sm mt-2">Zorg dat je de stang over de schenen laadt glijden.</p>
            <p class="font-medium text-sm mt-2">Strek de heup tot volledige extensie.</p>
            <p class="font-medium text-sm mt-2">Houd 3 seconden zakken en 3 seconden strekken aan.</p>
            <p class="font-semibold text-sm flex-1 flex items-end text-[#c8ab7a]">Uitleg door Nicky & Roy</p>
        </div>
    </div>
    <div class="p-5 bg-white rounded-3xl border border-gray-300 flex gap-4">
        <div class="min-w-[200px] max-w-[200px] relative cursor-pointer group" x-data="{ playing: false }"
             @click="const v = $refs.vid; if (v.paused) { v.play(); playing = true; } else { v.pause(); playing = false; }">
            <video class="rounded-2xl w-full" x-ref="vid"
                   @ended="playing = false"
                   src="/assets/videos/videotheek/HYX_BoxJump_V1.mp4"></video>
            <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                 :class="playing ? 'opacity-0 group-hover:opacity-100' : 'opacity-100'">
                <div class="w-14 h-14 rounded-full bg-black/50 flex items-center justify-center">
                    <i class="fa-solid text-white text-lg" :class="playing ? 'fa-pause' : 'fa-play ml-1'"></i>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <p class="text-lg font-bold mb-2">Box Jump</p>
            <p class="font-medium text-sm">Vanuit een squat positie spring je 2 benen tegelijk op de box en laat je je armen mee zwaaien.</p>
            <p class="font-medium text-sm mt-2">Bij deze oefening is het belangrijk dat je 2 benen op de box volledig uitstrekken en gecontroleerd weer met 1 been van de box afstapt.</p>
            <p class="font-semibold text-sm flex-1 flex items-end text-[#c8ab7a]">Uitleg door Eline</p>
        </div>
    </div>
    <div class="p-5 bg-white rounded-3xl border border-gray-300 flex gap-4">
        <div class="min-w-[200px] max-w-[200px] relative cursor-pointer group" x-data="{ playing: false }"
             @click="const v = $refs.vid; if (v.paused) { v.play(); playing = true; } else { v.pause(); playing = false; }">
            <video class="rounded-2xl w-full" x-ref="vid"
                   @ended="playing = false"
                   src="/assets/videos/videotheek/HYX_BurpeeBroadJumpV1.mp4"></video>
            <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                 :class="playing ? 'opacity-0 group-hover:opacity-100' : 'opacity-100'">
                <div class="w-14 h-14 rounded-full bg-black/50 flex items-center justify-center">
                    <i class="fa-solid text-white text-lg" :class="playing ? 'fa-pause' : 'fa-play ml-1'"></i>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <p class="text-lg font-bold mb-2">Burpee Broad Jump</p>
            <p class="font-medium text-sm">Je bent met de borst naar de grond.</p>
            <p class="font-medium text-sm mt-2">Je kunt 1 been bijstappen, plaats 1 voet tegelijk terug</p>
            <p class="font-semibold text-sm flex-1 flex items-end text-[#c8ab7a]">Uitleg door Eline</p>
        </div>
    </div>
</div>
@endsection
