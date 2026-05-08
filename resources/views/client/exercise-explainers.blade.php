@extends('layouts.app')
@section('title', 'Oefening uitleg')

@section('content')
<div x-data="{
    search: '',
    videos: [
        { file: 'HYX_9090_HipTransition_V1.mp4', name: '90/90 Hip Transition' },
        { file: 'HYX_BarbellDeadlift_V1.mp4', name: 'Barbell Deadlift' },
        { file: 'HYX_BoxJump_V1.mp4', name: 'Box Jump' },
        { file: 'HYX_BurpeeBroadJumpV1.mp4', name: 'Burpee Broad Jump' },
        { file: 'HYX_DevilPress_V1.mp4', name: 'Devil Press' },
        { file: 'HYX_DumbellPushPress_V1.mp4', name: 'Dumbbell Push Press' },
        { file: 'HYX_DumbellSnatch_V1.mp4', name: 'Dumbbell Snatch' },
        { file: 'HYX_DumbellThrusters_V1.mp4', name: 'Dumbbell Thrusters' },
        { file: 'HYX_FarmerCarry_V1.mp4', name: 'Farmer Carry' },
        { file: 'HYX_FrontRacksquat_V1.mp4', name: 'Front Rack Squat' },
        { file: 'HYX_HandReleasePushUps_V1.mp4', name: 'Hand Release Push-Ups' },
        { file: 'HYX_HorizontalJump_V1.mp4', name: 'Horizontal Jump' },
        { file: 'HYX_KettlebellSwings_V1.mp4', name: 'Kettlebell Swings' },
        { file: 'HYX_PullUps_V1.mp4', name: 'Pull-Ups' },
        { file: 'HYX_RowErg_V1.mp4', name: 'Row Erg' },
        { file: 'HYX_SandbagLunges_V1.mp4', name: 'Sandbag Lunges' },
        { file: 'HYX_SkiErg_V1.mp4', name: 'Ski Erg' },
        { file: 'HYX_SledBackwardDrag_V1.mp4', name: 'Sled Backward Drag' },
        { file: 'HYX_SledPull_V1.mp4', name: 'Sled Pull' },
        { file: 'HYX_SledPush_V1.mp4', name: 'Sled Push' },
        { file: 'HYX_StepUps_V1.mp4', name: 'Step-Ups' },
        { file: 'HYX_VerticalJumpSquat_V1.mp4', name: 'Vertical Jump Squat' },
        { file: 'HYX_WalkingLunges_V1.mp4', name: 'Walking Lunges' },
        { file: 'HYX_WallBalls_V1.mp4', name: 'Wall Balls' },
        { file: 'HYX_WorldGreatestStretch_V1.mp4', name: 'World\'s Greatest Stretch' },
    ],
    modal: null,
    openModal(video) {
        this.modal = video;
        this.$nextTick(() => {
            const v = document.getElementById('modal-video');
            if (v) v.play();
        });
    },
    closeModal() {
        const v = document.getElementById('modal-video');
        if (v) v.pause();
        this.modal = null;
    },
    get filtered() {
        if (!this.search.trim()) return this.videos;
        const q = this.search.toLowerCase();
        return this.videos.filter(v => v.name.toLowerCase().includes(q));
    }
}" @keydown.escape.window="closeModal()">
    <div class="mb-8">
        <h1 class="text-2xl font-bold mb-1">Oefening uitleg</h1>
        <p class="text-sm text-black/60 font-medium">Bekijk hier uitlegvideo's van oefeningen uit jouw trainingsschema.</p>
    </div>

    {{-- Zoekbalk --}}
    <div class="mb-6 relative">
        <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-black/30 text-sm"></i>
        <input type="text" x-model="search" placeholder="Zoek een oefening..."
               class="w-full sm:w-80 pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-medium placeholder-black/30 focus:outline-none focus:ring-2 focus:ring-[#c8ab7a]/50 focus:border-[#c8ab7a] transition">
    </div>

    {{-- Geen resultaten --}}
    <div x-show="filtered.length === 0" x-cloak class="text-center py-16">
        <i class="fa-solid fa-video-slash text-3xl text-black/20 mb-3"></i>
        <p class="text-sm text-black/40 font-medium">Geen oefeningen gevonden voor "<span x-text="search"></span>"</p>
    </div>

    {{-- Video grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <template x-for="video in filtered" :key="video.file">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
                 x-data="{ playing: false }">
                {{-- Video --}}
                <div class="relative cursor-pointer group aspect-[9/16] bg-black/5"
                     @click="
                        const v = $el.querySelector('video');
                        if (v.paused) { v.play(); playing = true; }
                        else { v.pause(); playing = false; }
                     ">
                    <video class="w-full h-full object-cover"
                           :src="'/assets/videos/uitleg/' + video.file"
                           preload="metadata"
                           playsinline
                           @ended="playing = false"></video>
                    <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                         :class="playing ? 'opacity-0 group-hover:opacity-100' : 'opacity-100'">
                        <div class="w-12 h-12 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center">
                            <i class="fa-solid text-white text-base" :class="playing ? 'fa-pause' : 'fa-play ml-0.5'"></i>
                        </div>
                    </div>
                    <button class="absolute bottom-2 right-2 w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10"
                            @click.stop="const v = $el.closest('.group').querySelector('video'); v.pause(); playing = false; openModal(video)"
                            title="Fullscreen">
                        <i class="fa-solid fa-expand text-white text-xs"></i>
                    </button>
                </div>
                {{-- Titel --}}
                <div class="px-4 py-3">
                    <p class="font-semibold text-sm" x-text="video.name"></p>
                </div>
            </div>
        </template>
    </div>

    {{-- Aantal oefeningen --}}
    <p class="text-xs text-black/30 font-medium mt-6 text-center" x-text="filtered.length + ' oefening' + (filtered.length !== 1 ? 'en' : '')"></p>

    {{-- Fullscreen modal --}}
    <div x-show="modal" x-cloak
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/95 p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        {{-- Sluit knop boven --}}
        <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition z-10"
                @click="closeModal()">
            <i class="fa-solid fa-xmark text-white text-lg"></i>
        </button>
        {{-- Titel --}}
        <p class="text-white text-sm font-semibold mb-3 text-center" x-text="modal ? modal.name : ''"></p>
        {{-- Video --}}
        <div class="flex-1 min-h-0 w-full flex items-center justify-center">
            <video x-show="modal" id="modal-video"
                   class="max-h-full max-w-full rounded-lg"
                   style="aspect-ratio: 9/16;"
                   :src="modal ? '/assets/videos/uitleg/' + modal.file : ''"
                   controls
                   playsinline
                   @ended="closeModal()"></video>
        </div>
        {{-- Sluiten knop onder --}}
        <button class="mt-3 px-5 py-2 rounded-full bg-white/20 backdrop-blur-sm text-white text-sm font-medium hover:bg-white/30 transition"
                @click="closeModal()">
            Sluiten
        </button>
    </div>
</div>
@endsection
