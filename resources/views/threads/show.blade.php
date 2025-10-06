@extends('layouts.app')
@section('title','Gesprek #'.$thread->id)

@section('content')
@php
    /** @var \App\Models\Thread $thread */
    $user   = auth()->user();
    $role   = $role ?? ($user?->isCoach() ? 'coach' : 'client');

    // Zorg dat relaties geladen zijn (mocht controller het vergeten)
    $thread->loadMissing(['clientUser', 'coachUser', 'messages.sender']);

    $coachUser  = $thread->coachUser;   // \App\Models\User | null
    $clientUser = $thread->clientUser;  // \App\Models\User | null

    // Avatar-fallback: eigen url -> ui-avatars -> placeholder
    $coachAvatar = $coachUser?->coachProfile?->avatar_url
        ?: ($coachUser?->name
            ? 'https://ui-avatars.com/api/?name='.urlencode($coachUser->name).'&size=80'
            : 'https://placehold.co/80x80?text=C');
@endphp

<div class="flex flex-col md:flex-row gap-4">
    @if($role === 'client')
        <div class="min-w-[250px] max-w-[250px] h-fit p-6 bg-white rounded-3xl md:mt-[6.5rem]">
            @if($coachUser)
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-black bg-cover bg-center relative"
                         style="background-image:url('{{ $coachAvatar }}')">
                        <div class="w-3 h-3 bg-green-500 animate-ping rounded-full absolute left-0 top-0"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full absolute left-0 top-0"></div>
                    </div>
                    <div>
                        <h2 class="text-xs text-black font-semibold">{{ $coachUser->name }}</h2>
                        <h3 class="text-xs text-black/50 font-medium">Jouw coach</h3>
                    </div>
                </div>
            @else
                <h2 class="text-xs text-black font-semibold">Nog geen coach gekoppeld</h2>
                <p class="text-xs text-black/50">Neem contact op met support.</p>
            @endif
        </div>
    @endif

    <div class="flex-1">
        @if($role === 'client')
            <a href="{{ route('client.threads.index') }}" class="text-xs opacity-25 hover:opacity-50 transition duration-300">Terug naar overzicht</a>
        @elseif($role === 'coach')
            <a href="{{ route('coach.threads.index') }}" class="text-xs opacity-25 hover:opacity-50 transition duration-300">Terug naar overzicht</a>
        @endif

        <h1 class="text-2xl font-bold mb-8 mt-4">
            <i class="fa-solid fa-file-signature mr-4"></i>
            {{ $thread->subject ?? 'Zonder onderwerp' }}
        </h1>

        <div class="w-full p-6 bg-white rounded-3xl min-h-[400px]">
            @if($thread->messages->isEmpty())
                <p class="text-sm text-gray-500">Nog geen berichten in dit gesprek.</p>
            @else
                <div class="flex flex-col gap-4">
                    @foreach($thread->messages as $m)
                        @php
                            $sender = $m->sender; // \App\Models\User | null
                            $isCoach = $sender?->isCoach() ?? false;
                        @endphp

                        <div class="flex {{ $isCoach ? 'justify-end' : 'justify-start' }}">
                            <div class="rounded-2xl p-[1.5rem] min-w-[80%] max-w-[80%] {{ $isCoach ? 'bg-[#c8ab7a]/20 text-gray-900' : 'bg-gray-100 text-gray-900' }}">
                                <div class="text-sm mb-3">
                                    {!! nl2br(e($m->body)) !!}
                                </div>
                                <div class="text-xs font-semibold {{ $isCoach ? 'text-gray-800' : 'text-gray-500' }}">
                                    {{ $m->created_at->format('d-m-Y H:i') }}<br>
                                    {{ $sender->name ?? ('User #'.$m->sender_id) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @can('reply', $thread)
            <form method="POST"
                  action="{{ $role==='coach' ? route('coach.threads.messages.store',$thread) : route('client.threads.messages.store',$thread) }}"
                  class="mt-4 w-full">
                @csrf
                <textarea
                    name="body" rows="3"
                    class="w-full rounded-xl border-[#ededed] hover:border-[#c7c7c7] transition duration-300
                           p-3 focus:outline-none focus:ring-0 focus-visible:outline-none focus-visible:ring-0
                           focus:border-[#c8ab7a] text-sm"
                    placeholder="Typ je bericht..."></textarea>
                <button class="mt-4 px-6 py-3 bg-[#c8ab7a] text-white font-medium text-sm rounded">
                    Versturen
                </button>
            </form>
        @endcan
    </div>
</div>
@endsection
