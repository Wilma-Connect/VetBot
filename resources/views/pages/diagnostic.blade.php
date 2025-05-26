<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>VetBot – Diagnostic</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('styles/header.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-[#faf3e8] text-white min-h-screen items-center justify-center p-4">

    <header class="header">
        <div class="header-nav">
          <div class="flex items-center gap-3">
          </div>
          <div class="flex items-center gap-3">
            <button id="authButton" class="auth-button">Se connecter</button>
            <button id="" class="b2">S'inscrire</button>
          </div>
        </div>
      </header>

      @if(!isset($step) || $step === 'init')
      <div class="flex items-center justify-center min-h-screen">
        <form action="{{ route('vetbot.start') }}" method="POST" class="bg-white text-black w-full max-w-md p-6 rounded-lg shadow space-y-4">
            @csrf
            <div class="logo flex justify-center">
                <img src="assets/vetbot-logo.png" alt="VetBot Logo" class="w-16 h-auto">
            </div>
            <h1 class="text-xl font-bold text-center">Diagnostic</h1>
            <p class="text-sm text-center">Répondez à ces questions pour personnaliser votre expérience</p>
            <select name="experience" required class="w-full p-2 rounded bg-gray-100">
            <option value="">Votre niveau d'expérience</option>
            <option value="débutant">Débutant</option>
            <option value="intermédiaire">Intermédiaire</option>
            <option value="expérimenté">Expérimenté</option>
            </select>
            <select name="type_elevage" class="w-full p-2 rounded bg-gray-100" placeholder="Type d'animal" required>
                <option value="Poulets">Poulets</option>
                <option value="Pintades">Pintades</option>
                <option value="Canards">Canards</option>
                <option value="Dindes">Dindes</option>
                <option value="Oies">Oies</option>
                <option value="Pigeons">Pigeons</option>
                <option value="Paons">Paons</option>
                <option value="Cailles">Cailles</option>
            </select>
            {{-- <input name="animal" type="text" placeholder="Type d'animal" class="w-full p-2 rounded bg-gray-100" /> --}}
            <input name="quantite" type="number" placeholder="Quantité" class="w-full p-2 rounded bg-gray-100" />
            <input name="surface_m2" type="text" placeholder="Superficie" class="w-full p-2 rounded bg-gray-100" />
            <input name="localisation" type="text" placeholder="Localisation" class="w-full p-2 rounded bg-gray-100" />
            <button type="submit" class="bg-green-600 text-white w-full p-2 rounded hover:bg-green-700">Continuer</button>
        </form>
      </div>

  @elseif($step === 'chat')

  <header class="header">
    <div class="header-nav">
      <div class="flex items-center gap-3">
        <select id="pageSelect" class="page-select">
          <option value="suivi.html">Suivi</option>
          <option value="{{ route('diagnostic') }}" >Conseils</option>
          <option value="#" selected>Diagnostic</option>
        </select>
        <a href="historique.html">
          Historique
        </a>
      </div>
      <div class="flex items-center gap-3">
        <button id="authButton" class="auth-button">Se connecter</button>
        <button id="" class="b2">S'inscrire</button>
      </div>
    </div>
  </header>

  <input type="hidden" id="conversationId" value="{{ $conversation->id }}">

  <div class="p-4 border-t border-[#181818] fixed bottom-0 left-0 right-0">
    <div class="space-y-3 max-h-96 overflow-y-auto mb-4" id="chat">
        <div class="flex-1 overflow-y-auto p-4 space-y-4">

            @foreach($messages as $message)
                <div class="flex {{ $message->role === 'assistant' ? 'justify-start' : 'justify-end' }} mb-2">
                    <div class="
                        {{ $message->role === 'assistant' ? 'bg-green-800/70' : 'bg-gray-800' }}
                        max-w-[50%] p-1 rounded-lg break-words
                    ">
                        {{-- Message texte --}}
                        @if($message->content)
                            <p class="text-sm whitespace-pre-wrap break-words ia-reponse">
                                {{ $message->content }}
                            </p>
                        @endif

                        {{-- Image si elle existe --}}
                        @if($message->image)
                            <img src="{{ Storage::disk('s3')->url($message->image) }}"
                                alt="Image envoyée"
                                class="mt-2 w-32 h-42 rounded-lg border border-gray-600" />
                        @endif

                        {{-- Contrôles audio uniquement pour assistant --}}
                        @if($message->role === 'assistant')
                            <div class="flex items-center gap-2 justify-end mt-2">
                                <button onclick="readAloud(this)" class="text-gray-300 hover:text-white text-sm" title="Écouter">🔊 Écouter</button>
                                <button onclick="stopVoice()" class="text-red-300 hover:text-white text-sm" title="Arrêter">🔇 Stop</button>
                                <span id="play-timer" class="text-xs text-gray-400 hidden">0s</span>
                            </div>
                        @endif

                        {{-- Timestamp --}}
                        <div class="text-xs text-gray-400 text-right mt-1">
                            {{ \Carbon\Carbon::parse($message->created_at)->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    <div class="flex items-center gap-2 mb-2 text-sm">
      <span class="text-gray-400">Suggestions :</span>
      <button class="bg-[#181818] px-2 py-1 rounded">Tremblements</button>
      <button class="bg-[#181818] px-2 py-1 rounded">Perte d'appétit</button>
    </div>
    <form action="{{ route('vetbot.send', $conversation) }}" method="POST" enctype="multipart/form-data" id="chatForm" class="flex flex-col gap-2 p-2 bg-green-800/30 rounded-md">
        @csrf
        <div id="vocal-container" class="hidden mb-3">
            <label class="block font-medium text-sm text-gray-700 mb-1">Texte dicté :</label>
            <div class="p-2 border border-dashed rounded bg-white text-sm text-gray-700 mb-2" id="vocal-preview"></div>
            <button type="button" onclick="validateDictation()" class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">
                ✅ Valider la dictée
            </button>
        </div>
        <div id="previewContainer" class="relative inline-block mb-2 hidden">
            <img id="imagePreview"
                 src=""
                 alt="Aperçu"
                 class="max-w-xs w-32 rounded border border-gray-300">

            <button type="button"
                    onclick="removePreview()"
                    class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center hover:bg-red-700 shadow">
              ×
            </button>
          </div>
                <textarea id="chatInput" name="content" type="text" class="p-2 flex-1 bg-white rounded text-gray-700 text-sm resize-none" placeholder="Décrire les symptômes…" rows="5" ></textarea>

      <div class="flex items-center justify-between border-t pt-2 border-white/10">
        <label for="imgUpload" class="cursor-pointer p-2 rounded-full hover:bg-white/20">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paperclip-icon lucide-paperclip"><path d="M13.234 20.252 21 12.3"/><path d="m16 6-8.414 8.586a2 2 0 0 0 0 2.828 2 2 0 0 0 2.828 0l8.414-8.586a4 4 0 0 0 0-5.656 4 4 0 0 0-5.656 0l-8.415 8.585a6 6 0 1 0 8.486 8.486"/></svg>
        </label>
        <input id="imgUpload" name="image" type="file" class="hidden" />
        <div class="flex items-center gap-5">
          <button type="button" onclick="startVoice()" id="micBtn" class="p-2 rounded-full hover:bg-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mic-icon lucide-mic"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
          </button>
          <span id="timer" class="text-xs text-gray-500 hidden">0s</span>

          <button type="submit" class="p-2 rounded-full bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send-horizontal-icon lucide-send-horizontal text-[#181818]"><path d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"/><path d="M6 12h16"/></svg>
          </button>
        </div>
      </div>
    </form>
  </div>

  <style>
    .message-container {
        height: 400px; /* Hauteur fixe pour le conteneur de messages */
        overflow-y: auto; /* Activer le défilement vertical */
        padding-right: 10px; /* Espacement pour la barre de défilement */
    }
</style>

<script src="{{ asset('script/script.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Faire défiler jusqu'au dernier message lorsque la page est chargée
        const messageContainer = document.getElementById('chat');
        messageContainer.scrollTop = messageContainer.scrollHeight;
    });
</script>

  <script>
    document.getElementById('chatForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.currentTarget;
    const formData = new FormData(form);
    const chatInput = document.getElementById('chatInput');
    const chat = document.getElementById('chat');

    if (!chatInput.value.trim() && !formData.get('image')) {
        alert('Veuillez saisir un message ou sélectionner une image');
        return;
    }

    // Ajout message utilisateur (mimique blade)
    const userDiv = document.createElement('div');
    userDiv.className = 'p-3 rounded relative flex flex-col gap-1 bg-blue-100 text-right';
    userDiv.innerHTML = `<p class="text-sm whitespace-pre-wrap ia-reponse">${chatInput.value.trim()}</p>`;
    chat.appendChild(userDiv);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });

        if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);

        const result = await response.json();

        if (result.success) {
            const assistantDiv = document.createElement('div');
            assistantDiv.className = 'p-3 rounded relative flex flex-col gap-1 bg-green-100 text-left';
            assistantDiv.innerHTML = `
                <p class="text-sm whitespace-pre-wrap ia-reponse">${result.content}</p>
                <div class="flex items-center gap-2 justify-end">
                    <button onclick="readAloud(this)" class="text-gray-600 hover:text-black text-sm" title="Écouter">🔊 Écouter</button>
                    <button onclick="stopVoice()" class="text-red-600 hover:text-black text-sm" title="Arrêter">🔇 Stop</button>
                    <span id="play-timer" class="text-xs text-gray-500 hidden">0s</span>
                </div>
            `;
            chat.appendChild(assistantDiv);
        }

    } catch (error) {
        console.error("Erreur:", error);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-500 text-white p-3 rounded-lg mb-2';
        errorDiv.textContent = "Erreur lors de l'envoi du message";
        chat.appendChild(errorDiv);
    } finally {
        form.reset();
        chat.scrollTop = chat.scrollHeight;

        // ✅ Rafraîchit la page après un petit délai
        setTimeout(() => {
            location.reload();
        }, 500); // Tu peux augmenter/diminuer ce délai
    }
});

  </script>

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

<script>
    document.getElementById('imgUpload').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('imagePreview');
      const container = document.getElementById('previewContainer');

      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          preview.src = event.target.result;
          container.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      } else {
        removePreview();
      }
    });

    function removePreview() {
      const preview = document.getElementById('imagePreview');
      const container = document.getElementById('previewContainer');
      preview.src = '';
      container.classList.add('hidden');
      document.getElementById('imgUpload').value = ''; // reset input
    }
  </script>

  @endif
</body>
</html>

