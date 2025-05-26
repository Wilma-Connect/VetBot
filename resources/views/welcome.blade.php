{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>VetBot - Assistant vétérinaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    @if(!isset($step) || $step === 'init')
        <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Décrivez votre élevage</h2>
            <form action="{{ route('vetbot.start') }}" method="POST">
                @csrf

                <label class="block mb-2 font-medium">Quel est le type d’élevage ?</label>
                <select name="type_elevage" class="w-full p-2 mb-4 border rounded" required>
                    <option value="Vache">Vache</option>
                    <option value="Boeuf">Boeuf</option>
                    <option value="Mouton">Mouton</option>
                    <option value="Volaille">Volaille</option>
                </select>

                <label class="block mb-2 font-medium">Combien d’animaux ?</label>
                <input type="number" name="quantite" min="1" class="w-full p-2 mb-4 border rounded" required>

                <label class="block mb-2 font-medium">Où se trouve l’élevage ?</label>
                <input type="text" name="localisation" class="w-full p-2 mb-4 border rounded" placeholder="Ex: Korhogo, Bouaké..." required>

                <label class="block mb-2 font-medium">Surface de l’abri (en m²)</label>
                <input type="number" name="surface_m2" min="1" class="w-full p-2 mb-4 border rounded" required>

                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Continuer</button>
            </form>
        </div>
        @elseif($step === 'chat')
        <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Discussion avec VetBot</h2>

            <div class="space-y-3 max-h-96 overflow-y-auto mb-4">
                @foreach($messages as $message)
                    <div class="p-3 rounded relative flex flex-col gap-1
                        {{ $message->role === 'assistant' ? 'bg-green-100 text-left' : 'bg-blue-100 text-right' }}">

                        <p class="text-sm whitespace-pre-wrap ia-reponse">{{ $message->content }}</p>

                        @if($message->role === 'assistant')
                            <div class="flex items-center gap-2 justify-end">
                                <button onclick="readAloud(this)" class="text-gray-600 hover:text-black text-sm" title="Écouter">🔊 Écouter</button>
                                <button onclick="stopVoice()" class="text-red-600 hover:text-black text-sm" title="Arrêter">🔇 Stop</button>
                                <span id="play-timer" class="text-xs text-gray-500 hidden">0s</span>
                            </div>
                        @endif
                    </div>
                @endforeach


            </div>

            <form action="{{ route('vetbot.send', $conversation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="vocal-container" class="hidden mb-3">
                    <label class="block font-medium text-sm text-gray-700 mb-1">Texte dicté :</label>
                    <div class="p-2 border border-dashed rounded bg-gray-100 text-sm mb-2" id="vocal-preview"></div>
                    <button type="button" onclick="validateDictation()" class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">
                        ✅ Valider la dictée
                    </button>
                </div>

                <textarea name="content" rows="3" class="w-full p-2 border rounded mb-2" placeholder="Décrivez les symptômes ou posez une question…" required></textarea>
                <input type="file" name="image" accept="image/*" class="mb-3">
                @if(isset($message))
                <img src="{{ asset('storage/' . $message->image) }}" class="w-32 h-auto rounded border mt-2" alt="Image utilisateur">
                @endif

                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Envoyer</button>
                    <button type="button" onclick="startVoice()" class="bg-blue-600 text-white px-4 py-2 rounded">🎙 Parler</button>
                    <span id="timer" class="text-xs text-gray-500 hidden">0s</span>
                </div>
            </form>
        </div>


<script>
    let playTimerInterval = null;

    function readAloud(button) {
        const container = button.closest('div').parentElement;
        const message = container.querySelector('.ia-reponse')?.innerText || '';
        const timer = container.querySelector('#play-timer');

        if (!message) return;

        const utter = new SpeechSynthesisUtterance(message);
        utter.lang = 'fr-FR';

        // Choisir une voix
        const voices = speechSynthesis.getVoices();
        const frVoice = voices.find(v => v.lang === 'fr-FR');
        if (frVoice) utter.voice = frVoice;

        // Timer
        if (timer) {
            let seconds = 0;
            timer.classList.remove('hidden');
            timer.textContent = '0s';
            playTimerInterval = setInterval(() => {
                seconds++;
                timer.textContent = `${seconds}s`;
            }, 1000);
        }

        utter.onend = () => {
            if (timer) {
                clearInterval(playTimerInterval);
                timer.classList.add('hidden');
                timer.textContent = '0s';
            }
        };

        speechSynthesis.cancel();
        speechSynthesis.speak(utter);
    }

    function stopVoice() {
        speechSynthesis.cancel();
        clearInterval(playTimerInterval);
        document.querySelectorAll('#play-timer').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '0s';
        });
    }

</script>


<script>
    let recognition = null;
    let recordTimer = null;
    let recordSeconds = 0;
    let isRecording = false;

    function startVoice() {
        // Initialisation de la reconnaissance vocale
        recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'fr-FR';
        recognition.interimResults = false;

        // Affiche le conteneur de dictée
        const container = document.getElementById('vocal-container');
        const preview = document.getElementById('vocal-preview');
        container.classList.remove('hidden');
        preview.innerText = '🎙️ En écoute... Parlez maintenant.';

        recognition.onresult = function(event) {
            const result = event.results[0][0].transcript;
            preview.innerText = result;
        };

        recognition.onerror = function(event) {
            preview.innerText = '❌ Erreur vocale : ' + event.error;
        };

        recognition.onend = function() {
            if (!preview.innerText || preview.innerText.includes('En écoute')) {
                preview.innerText = 'Aucune dictée détectée.';
            }
        };

        recognition.start();
    }

    function stopRecording() {
        const timer = document.getElementById('record-timer');
        if (recognition) {
            recognition.stop();
            recognition = null;
        }

        if (recordTimer) {
            clearInterval(recordTimer);
            recordTimer = null;
        }

        if (timer) {
            timer.classList.add('hidden');
            timer.textContent = '0s';
        }

        isRecording = false;
    }

    function validateDictation() {
        const preview = document.getElementById('vocal-preview');
        const textarea = document.querySelector('textarea[name="content"]');
        if (preview && textarea) {
            textarea.value = preview.innerText.trim();
        }
    }

</script>

    @endif


</body>
</html>
 --}}
