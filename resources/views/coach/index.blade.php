@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')

@php
    use App\Models\User;
    use App\Models\Order;
    use App\Models\SubscriptionRenewal;
    use Illuminate\Support\Facades\DB;

    $coach = auth()->user();
    $today = now()->toDateString();

    // Nieuwe clients van vandaag:
    // - users.role = 'client'
    // - users.created_at = vandaag
    // - EN óf nog geen coach (coach_id = null)
    //   óf al gekoppeld aan deze coach (coach_id = huidige user id)
    $newClients = DB::table('users')
        ->leftJoin('client_profiles', 'client_profiles.user_id', '=', 'users.id')
        ->where('users.role', 'client')
        ->whereDate('users.created_at', $today)
        ->where(function ($q) use ($coach) {
            $q->whereNull('client_profiles.coach_id')
              ->orWhere('client_profiles.coach_id', $coach->id);
        })
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            'client_profiles.coach_id'
        )
        ->orderByDesc('users.created_at')
        ->get();

    // Nieuwe threads van vandaag, alleen voor deze coach
    // threads.coach_user_id = huidige coach
    // threads.client_user_id = de klant
    // threads.created_at = vandaag
    $newThreads = DB::table('threads')
        ->leftJoin('users', 'users.id', '=', 'threads.client_user_id')
        ->where('threads.coach_user_id', $coach->id)
        ->whereDate('threads.created_at', $today)
        ->select(
            'threads.id',
            'threads.created_at',
            'users.name as client_name',
            'users.email as client_email'
        )
        ->orderByDesc('threads.created_at')
        ->get();

    // Statistieken
    $totalClients = User::where('role', 'client')->count();
    $totalRevenue = Order::where('status', 'paid')->sum('amount_cents') / 100;
    $asOf = $asOf ?? now()->format('d-m-Y H:i');
@endphp

<h1 class="text-2xl font-bold mb-2">Goededag {{ auth()->user()->name }}! 👋</h1>
<p class="text-sm text-black opacity-80 font-medium mb-10">
    Welkom op jouw persoonlijke training omgeving.<br>
    Beheer jouw klanten, chat met je klanten en maak trainingsschemas aan.
</p>

{{-- Nieuwe clients vandaag --}}
@if($newClients->count())
    <div>
        <h2 class="text-lg font-bold mb-2">Nieuwe aanmeldingen vandaag</h2>

        <div class="space-y-3">
            @foreach($newClients as $client)
                @php
                    $isRenewal = SubscriptionRenewal::hasRenewed($client->id);
                @endphp
                <div class="p-4 bg-[#fffaf0] border border-[#c8ab7a]/40 rounded-2xl flex flex-col md:flex-row items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[#c8ab7a]">
                                {{ $client->name ?? 'Onbekende klant' }}
                            </span>
                            @if($isRenewal)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700 border border-green-200">
                                    <i class="fa-solid fa-rotate-right mr-1 text-[8px]"></i>
                                    Verlenging
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-600">
                            Aangemeld op
                            {{ \Carbon\Carbon::parse($client->created_at)->format('d-m-Y H:i') }}
                            @if(!empty($client->email))
                                · {{ $client->email }}
                            @endif
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            @if(is_null($client->coach_id))
                                Deze klant heeft in de intake nog geen coach gekoppeld.
                            @else
                                Deze klant is gekoppeld aan jou als coach.
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        @if(is_null($client->coach_id))
                            {{-- Ongeclaimde client → naar claim-overzicht --}}
                            <a href="{{ url('/coach/claim-clients') }}"
                               class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-[#c8ab7a] hover:bg-[#a89067] text-white transition">
                                Bekijk in ongeclaimde clients
                            </a>
                        @else
                            {{-- Reeds gekoppeld aan deze coach → direct naar klant --}}
                            <a href="{{ url('/coach/clients/'.$client->id) }}"
                               class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold border border-[#c8ab7a] text-[#c8ab7a] hover:bg-[#c8ab7a] hover:text-white transition">
                                Ga naar klant
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Nieuwe gesprekken vandaag --}}
@if($newThreads->count())
    <div class="mt-2">
        <h2 class="text-lg font-bold mb-2">Nieuwe gesprekken vandaag</h2>

        <div class="space-y-3">
            @foreach($newThreads as $thread)
                <div class="p-4 bg-[#fffaf0] border border-[#c8ab7a]/40 rounded-2xl flex flex-col md:flex-row items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold text-[#c8ab7a]">
                            {{ $thread->client_name ?? 'Onbekende klant' }}
                        </div>

                        <p class="text-xs text-gray-600">
                            Gestart op
                            {{ \Carbon\Carbon::parse($thread->created_at)->format('d-m-Y H:i') }}
                            @if(!empty($thread->client_email))
                                · {{ $thread->client_email }}
                            @endif
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Er is een nieuw gesprek gestart in jouw inbox.
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <a href="{{ url('/coach/threads/'.$thread->id) }}"
                           class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold border border-[#c8ab7a] text-[#c8ab7a] hover:bg-[#c8ab7a] hover:text-white transition">
                            Open gesprek
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<h2 class="text-lg font-bold mb-2 mt-8">Snelkoppelingen</h2>
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
@endsection
