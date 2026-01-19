@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')
@php
  $card      = 'p-5 bg-white rounded-3xl border border-gray-300';
  $btn       = 'px-4 py-2 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded text-white font-semibold text-sm';
  $btnGhost  = 'text-xs cursor-pointer opacity-50 hover:opacity-100 transition duration-300 font-semibold';
  $inputBase = 'w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]';
@endphp

<h1 class="text-2xl font-bold mb-2">Jouw klanten</h1>
<p class="text-sm text-black opacity-80 font-medium mb-6">
  Beheer je klanten, bekijk profielen en open details met UHV-berekening.
</p>

<div class="{{ $card }} overflow-hidden">
  <div class="text-sm text-black font-semibold opacity-50 mb-2">Klantenoverzicht</div>
  <form method="get" class="mb-3">
    <div class="flex gap-2">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Zoek op naam of e-mail"
             class="{{ $inputBase }}">
      <button class="cursor-pointer px-6 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded">Zoeken</button>
      @if(($q ?? '') !== '')
        <a href="{{ route('coach.clients.index') }}" class="px-7 py-2 text-sm font-semibold rounded border border-gray-300 hover:bg-gray-50 transition duration-300 text-gray-700 flex items-center justify-center">
          Reset
        </a>
      @endif
    </div>
  </form>
  <div class="rounded-2xl border border-gray-200 overflow-hidden overflow-x-auto">
    <table class="min-w-[780px] w-full text-sm">
      <thead class="bg-gray-50 text-gray-700">
        <tr>
          <th class="px-3 py-2 text-left">Klant</th>
          <th class="px-3 py-2 text-left">E-mail</th>
          <th class="px-3 py-2 text-left">Telefoonnummer</th>
          <th class="px-3 py-2 text-left">Status</th>
          <th class="px-3 py-2 text-left">Verloopt op</th>
        </tr>
      </thead>
  
      <tbody class="divide-y divide-gray-100">
        @forelse ($clients as $c)
          @php
            $p = $c->clientProfile;
            $status = $c->subscription_status ?? ['is_active' => false, 'label' => 'Onbekend', 'end_date' => null];
          @endphp
          <tr class="cursor-pointer hover:bg-gray-50 transition duration-150 group"
              onclick="window.location='{{ route('coach.clients.show', $c) }}'"
              tabindex="0"
              role="button"
              aria-label="Bekijk details van {{ $c->name }}"
              onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location='{{ route('coach.clients.show', $c) }}'; }">
            {{-- Klant --}}
            <td class="px-3 py-2">
              <div class="font-medium flex items-center gap-2">
                {{ $c->name }}
                <i class="fa-solid fa-arrow-right text-[#c8ab7a] text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-150"></i>
              </div>
            </td>
  
            {{-- E-mail --}}
            <td class="px-3 py-2">
              <span class="text-gray-700">{{ $c->email }}</span>
            </td>

            <td class="px-3 py-2">
              <span class="text-gray-700"> {{ $c->email }}</span>
            </td>

            {{-- Telefoonnummer --}}
            <td class="px-3 py-2">
              <span class="text-gray-700">{{ $c->clientProfile->phone_e164 }}</span>
            </td>

            {{-- Status Label --}}
            <td class="px-3 py-2 text-left">
              @if($status['is_pending'] ?? false)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100/80 text-orange-700 backdrop-blur-sm border border-orange-200/50"
                      title="Intake nog niet afgerond">
                  <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                  Bezig
                </span>
              @elseif($status['is_active'])
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100/80 text-green-700 backdrop-blur-sm border border-green-200/50"
                      title="Verloopt op {{ $status['end_date'] ?? 'onbekend' }}">
                  <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                  Actief
                </span>
              @else

              {{-- Dit moet nog verbeterd worden --}}
              @if($status['end_date'])

                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100/80 text-red-700 backdrop-blur-sm border border-red-200/50"
                  title="Verlopen op {{ $status['end_date'] ?? 'onbekend' }}">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                  Verlopen
                </span>

              @elseif($status['is_pending'] ?? false)

                <span class="text-orange-600 text-xs font-medium">Intake bezig</span>

              @else

                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100/80 text-orange-700 backdrop-blur-sm border border-orange-200/50"
                      title="Intake nog niet afgerond">
                  <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                  Bezig
                </span>

              @endif

                {{-- <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100/80 text-red-700 backdrop-blur-sm border border-red-200/50"
                      title="Verlopen op {{ $status['end_date'] ?? 'onbekend' }}">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                  Verlopen
                </span> --}}
                
              @endif
            </td>
  
            {{-- Verloopt op (einddatum) --}}
            <td class="px-3 py-2 text-left">
              @if($status['end_date'])
                <span class="text-gray-700 font-medium">{{ $status['end_date'] }}</span>
              @elseif($status['is_pending'] ?? false)
                <span class="text-orange-600 text-xs font-medium">Intake bezig</span>
              @else
                <span class="text-gray-400 text-xs">Bezig met de intake</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-3 py-6 text-center text-gray-500">
              Geen cliënten gevonden.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>
    {{ $clients->withQueryString()->links() }}
  </div>
</div>
@endsection
