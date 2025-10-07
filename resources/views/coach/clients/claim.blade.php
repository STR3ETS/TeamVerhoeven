@extends('layouts.app')
@section('title','Team Verhoeven — Ongeclaimde klanten')

@section('content')
    <h1 class="text-2xl font-bold mb-2">Ongeclaimde klanten</h1>
    <p class="text-sm text-black opacity-80 font-medium mb-6">
        Klanten zonder toegewezen coach. Claim een klant om het traject te starten.
    </p>

    <form method="GET" action="{{ route('coach.clients.claim') }}" class="mb-4">
        <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Zoek op naam of e-mail"
                   class="w-full sm:w-96 rounded-xl border border-gray-300 hover:border-[#c7c7c7] transition p-3 text-sm focus:outline-none focus:ring-0">
            <button class="px-4 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded">
                Zoeken
            </button>
            @if(($q ?? '') !== '')
                <a href="{{ route('coach.clients.claim') }}" class="text-xs font-semibold opacity-60 hover:opacity-100 transition">Reset</a>
            @endif
        </div>
    </form>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 p-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 p-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="p-5 bg-white rounded-3xl border border-gray-300">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold">Totaal: {{ $profiles->total() }}</h2>
            @if($profiles->hasPages())
                <div class="text-xs text-black/60 font-medium">{{ $profiles->firstItem() }}–{{ $profiles->lastItem() }} van {{ $profiles->total() }}</div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-sm">
                <thead>
                    <tr class="text-left text-[12px] text-black/60 font-semibold">
                        <th class="py-2">Naam</th>
                        <th class="py-2">E-mail</th>
                        <th class="py-2">Aangemaakt</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody class="align-top">
                    @forelse($profiles as $profile)
                        <tr class="border-t border-gray-100">
                            <td class="py-3 font-medium">{{ $profile->user?->name ?? '—' }}</td>
                            <td class="py-3">{{ $profile->user?->email ?? '—' }}</td>
                            <td class="py-3 text-black/70">{{ $profile->created_at?->format('d-m-Y H:i') }}</td>
                            <td class="py-3 text-right">
                                <form method="POST" action="{{ route('coach.clients.claim.store', $profile) }}"
                                      onsubmit="return confirm('Klant aan jezelf toewijzen?');" class="inline">
                                    @csrf
                                    <button class="px-3 py-2 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-xs rounded">
                                        Claim klant
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-100">
                            <td colspan="4" class="py-8 text-center text-black/60">Geen ongeclaimde klanten gevonden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($profiles->hasPages())
            <div class="mt-4">
                {{ $profiles->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
