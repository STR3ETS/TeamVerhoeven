@php
    use App\Models\ClientTodoItem;

    /** @var \App\Models\User $client */
    $todos = $todos
        ?? ClientTodoItem::where('client_user_id', $client->id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

    $open = $todos->whereNull('completed_at');
    $done = $todos->whereNotNull('completed_at');
@endphp

<div class="{{ $card }} mb-6">
    {{-- Add form --}}
    <form method="POST" action="{{ route('coach.clients.todos.store', $client) }}" class="mb-4">
        @csrf
        <div class="flex flex-col sm:flex-row gap-2 sm:items-stretch">
            <input
                name="label"
                required
                maxlength="200"
                placeholder="Nieuwe taak..."
                class="w-full rounded-xl border border-gray-300 hover:border-[#c7c7c7] transition p-3 text-sm focus:outline-none focus:ring-0"
            >
            <button
                class="w-full sm:w-auto px-4 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded"
            >
                Toevoegen
            </button>
        </div>
        @error('label')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </form>

    {{-- Open items --}}
    <div class="space-y-2" x-data="todoList('{{ route('coach.clients.todos.reorder', $client) }}')">
        <h3 class="text-sm text-black font-semibold opacity-50 mb-2">Taken te voltooien</h3>

        <ul class="divide-y divide-gray-100" x-ref="list" data-sortable="true">
            @forelse ($open as $item)
                <li class="py-3 flex flex-col gap-3 sm:flex-row sm:items-start sm:gap-4" data-id="{{ $item->id }}">
                    {{-- Toggle --}}
                    <form method="POST" action="{{ route('coach.clients.todos.toggle', [$client, $item]) }}" class="order-2 sm:order-1">
                        @csrf
                        @method('PATCH')
                        <button
                            class="cursor-pointer w-8 h-8 sm:w-5 sm:h-5 border border-gray-400 rounded flex items-center justify-center hover:bg-gray-50"
                            title="Afvinken"
                            aria-label="Taak afvinken"
                        >
                            <i class="fa-solid fa-check text-xs sm:text-[10px] text-gray-400" aria-hidden="true"></i>
                        </button>
                    </form>

                    {{-- Content + Notes --}}
                    <div class="flex-1 order-1 sm:order-2" x-data="{ edit:false }">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                            <div class="min-w-0">
                                <div class="text-sm text-black font-semibold break-words">
                                    {{ $item->label }}
                                </div>
                                <div class="text-xs text-gray-500 flex flex-wrap gap-x-2 gap-y-1">
                                    <span>
                                        @if($item->source === 'system') Taak vanuit pakket @else Taak handmatig toegevoegd @endif
                                    </span>
                                    @if($item->due_date)
                                        <span>• deadline: {{ \Illuminate\Support\Carbon::parse($item->due_date)->format('d-m-Y') }}</span>
                                    @endif
                                    @if($item->is_optional)
                                        <span>• optioneel</span>
                                    @endif
                                </div>

                                @if($item->notes)
                                    <div class="mt-1 text-xs text-gray-600 whitespace-pre-line break-words">
                                        {{ $item->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button
                                    type="button"
                                    @click="edit = !edit"
                                    class="px-2 py-1 text-xs text-gray-700 hover:text-black font-semibold rounded border border-gray-300"
                                >
                                    Notitie
                                </button>
                                {{-- Delete (compact op mobiel) --}}
                                <form
                                    method="POST"
                                    action="{{ route('coach.clients.todos.destroy', [$client, $item]) }}"
                                    onsubmit="return confirm('Taak verwijderen?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-2 py-1 text-xs text-red-600 hover:text-red-700 font-semibold rounded border border-red-200 flex items-center gap-2"
                                        aria-label="Taak verwijderen"
                                    >
                                        <span class="sm:inline hidden">Verwijder</span>
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Notes editor --}}
                        <form
                            x-show="edit"
                            x-cloak
                            method="POST"
                            action="{{ route('coach.clients.todos.update', [$client, $item]) }}"
                            class="mt-2"
                        >
                            @csrf
                            @method('PATCH')
                            <textarea
                                name="notes"
                                rows="3"
                                maxlength="2000"
                                placeholder="Schrijf een notitie voor deze taak…"
                                class="w-full rounded-xl border border-gray-300 hover:border-[#c7c7c7] transition p-2 text-sm focus:outline-none focus:ring-0"
                            >{{ old('notes', $item->notes) }}</textarea>
                            <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                <button class="w-full sm:w-auto px-3 py-2 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-xs rounded">
                                    Opslaan
                                </button>
                                <button type="button" @click="edit=false" class="w-full sm:w-auto text-center text-xs text-gray-500">
                                    Annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </li>
            @empty
                <li class="py-2 text-sm text-gray-500">Geen open taken.</li>
            @endforelse
        </ul>

        {{-- Done items --}}
        <h3 class="mt-4 text-sm text-black font-semibold opacity-50 mb-2">Taken afgevinkt</h3>

        <ul class="divide-y divide-gray-100">
            @forelse ($done as $item)
                <li class="py-3 flex flex-col gap-3 sm:flex-row sm:items-start sm:gap-3">
                    {{-- Toggle --}}
                    <form method="POST" action="{{ route('coach.clients.todos.toggle', [$client, $item]) }}" class="order-2 sm:order-1">
                        @csrf
                        @method('PATCH')
                        <button
                            class="cursor-pointer w-8 h-8 sm:w-5 sm:h-5 border border-green-500 rounded flex items-center justify-center bg-green-100 hover:bg-green-50"
                            title="Markeer als open"
                            aria-label="Markeer taak als open"
                        >
                            <i class="fa-solid fa-check text-xs sm:text-[10px] text-green-600" aria-hidden="true"></i>
                        </button>
                    </form>

                    {{-- Content + Notes --}}
                    <div class="flex-1 order-1 sm:order-2" x-data="{ edit:false }">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                            <div class="min-w-0">
                                <div class="text-sm text-black line-through font-semibold break-words">
                                    {{ $item->label }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Afgevinkt op {{ optional($item->completed_at)->format('d-m-Y H:i') ?? '—' }}
                                </div>

                                @if($item->notes)
                                    <div class="mt-1 text-xs text-gray-600 whitespace-pre-line break-words">
                                        {{ $item->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button
                                    type="button"
                                    @click="edit = !edit"
                                    class="px-2 py-1 text-xs text-gray-700 hover:text-black font-semibold rounded border border-gray-300"
                                >
                                    Notitie
                                </button>
                                <form
                                    method="POST"
                                    action="{{ route('coach.clients.todos.destroy', [$client, $item]) }}"
                                    onsubmit="return confirm('Taak verwijderen?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-2 py-1 text-xs text-red-600 hover:text-red-700 font-semibold rounded border border-red-200 flex items-center gap-2"
                                        aria-label="Taak verwijderen"
                                    >
                                        <span class="sm:inline hidden">Verwijder</span>
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Notes editor --}}
                        <form
                            x-show="edit"
                            x-cloak
                            method="POST"
                            action="{{ route('coach.clients.todos.update', [$client, $item]) }}"
                            class="mt-2"
                        >
                            @csrf
                            @method('PATCH')
                            <textarea
                                name="notes"
                                rows="3"
                                maxlength="2000"
                                placeholder="Notitie toevoegen of aanpassen…"
                                class="w-full rounded-xl border border-gray-300 hover:border-[#c7c7c7] transition p-2 text-sm focus:outline-none focus:ring-0"
                            >{{ old('notes', $item->notes) }}</textarea>
                            <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                <button class="w-full sm:w-auto px-3 py-2 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-xs rounded">
                                    Opslaan
                                </button>
                                <button type="button" @click="edit=false" class="w-full sm:w-auto text-center text-xs text-gray-500">
                                    Annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </li>
            @empty
                <li class="py-2 text-sm text-gray-500">Nog geen afgeronde taken.</li>
            @endforelse
        </ul>
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
