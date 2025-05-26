@foreach ($messages as $message)
    @if ($message->from_user_id === auth()->id())
        {{-- Message utilisateur (à droite) --}}
        <div class="p-3 rounded relative flex flex-col gap-1 bg-blue-100 text-right">
            <p class="text-sm whitespace-pre-wrap ia-reponse">{{ $message->content }}</p>
        </div>
    @else
        {{-- Message assistant (à gauche) --}}
        <div class="p-3 rounded relative flex flex-col gap-1 bg-green-100 text-left">
            <p class="text-sm whitespace-pre-wrap ia-reponse">{{ $message->content }}</p>
            <div class="flex items-center gap-2 justify-end">
                <button onclick="readAloud(this)" class="text-gray-600 hover:text-black text-sm" title="Écouter">🔊 Écouter</button>
                <button onclick="stopVoice()" class="text-red-600 hover:text-black text-sm" title="Arrêter">🔇 Stop</button>
                <span id="play-timer" class="text-xs text-gray-500 hidden">0s</span>
            </div>
        </div>
    @endif
@endforeach
