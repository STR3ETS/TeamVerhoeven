@php
    use App\Models\ClientTodoItem;

    /** @var \App\Models\User $client */
    $todos = $todos
        ?? ClientTodoItem::where('client_user_id', $client->id)->orderBy('position')->orderBy('id')->get();

    $open   = $todos->whereNull('completed_at');
    $done   = $todos->whereNotNull('completed_at');
@endphp

<div class="{{ $card }}">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">To-do’s voor {{ $client->name }}</h2>

  {{-- Add form --}}
  <form method="POST" action="{{ route('coach.clients.todos.store', $client) }}" class="mb-4">
    @csrf
    <div class="flex flex-col sm:flex-row gap-2">
      <input name="label" required maxlength="200" placeholder="Nieuwe taak..."
             class="w-full sm:w-96 rounded-xl border border-gray-300 hover:border-[#c7c7c7] transition p-3 text-sm focus:outline-none focus:ring-0">
      <button class="px-4 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded">
        Toevoegen
      </button>
    </div>
    @error('label') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </form>

  {{-- Open items --}}
  <div class="space-y-2" x-data="todoList('{{ route('coach.clients.todos.reorder', $client) }}')">
    <h3 class="text-sm font-semibold text-gray-700">Open</h3>

    <ul class="divide-y divide-gray-100" x-ref="list" data-sortable="true">
      @forelse ($open as $item)
        <li class="py-2 flex items-start gap-3" data-id="{{ $item->id }}">
          <form method="POST" action="{{ route('coach.clients.todos.toggle', [$client, $item]) }}">
            @csrf @method('PATCH')
            <button class="mt-0.5 w-5 h-5 border rounded flex items-center justify-center hover:bg-gray-50"
                    title="Afvinken">
              <span class="sr-only">Toggle</span>
              ✓
            </button>
          </form>

          <div class="flex-1">
            <div class="text-sm text-black">{{ $item->label }}</div>
            <div class="text-xs text-gray-500">
              @if($item->source === 'system') Systeemtaak @else Handmatig @endif
              @if($item->package) • {{ $item->package }}@endif
              @if($item->duration_weeks) • {{ $item->duration_weeks }} weken @endif
            </div>
          </div>

          <form method="POST" action="{{ route('coach.clients.todos.destroy', [$client, $item]) }}"
                onsubmit="return confirm('Taak verwijderen?')">
            @csrf @method('DELETE')
            <button class="text-xs text-gray-500 hover:text-red-600 font-semibold">Verwijder</button>
          </form>
        </li>
      @empty
        <li class="py-2 text-sm text-gray-500">Geen open taken.</li>
      @endforelse
    </ul>

    {{-- Done items --}}
    <h3 class="mt-4 text-sm font-semibold text-gray-700">Afgevinkt</h3>
    <ul class="divide-y divide-gray-100 opacity-70">
      @forelse ($done as $item)
        <li class="py-2 flex items-start gap-3">
          <form method="POST" action="{{ route('coach.clients.todos.toggle', [$client, $item]) }}">
            @csrf @method('PATCH')
            <button class="mt-0.5 w-5 h-5 border rounded flex items-center justify-center bg-gray-100 hover:bg-gray-50"
                    title="Markeer als open">
              <span class="sr-only">Toggle</span>
              ✓
            </button>
          </form>

          <div class="flex-1">
            <div class="text-sm text-black line-through">{{ $item->label }}</div>
            <div class="text-xs text-gray-500">
              Afgevinkt op {{ optional($item->completed_at)->format('d-m-Y H:i') ?? '—' }}
            </div>
          </div>

          <form method="POST" action="{{ route('coach.clients.todos.destroy', [$client, $item]) }}"
                onsubmit="return confirm('Taak verwijderen?')">
            @csrf @method('DELETE')
            <button class="text-xs text-gray-500 hover:text-red-600 font-semibold">Verwijder</button>
          </form>
        </li>
      @empty
        <li class="py-2 text-sm text-gray-500">Nog geen afgeronde taken.</li>
      @endforelse
    </ul>

    {{-- Drag reorder (optioneel, eenvoudige variant met HTML5 drag&drop of lib) --}}
    <template x-if="false">
      <div>Drag&drop komt hier indien je wil – API endpoint is al aanwezig.</div>
    </template>
  </div>
</div>

{{-- Alpine helper voor reorder (optioneel te activeren als je drag&drop toevoegt) --}}
<script>
function todoList(reorderUrl) {
  return {
    reorder(ids) {
      fetch(reorderUrl, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ ids })
      });
    }
  }
}
</script>
