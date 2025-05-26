<h1 class="text-2xl md:text-4xl font-bold mb-4 pt-3 border-t border-green-900 text-green-900">Que souhaitez-vous faire ?</h1>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="flex flex-col gap-7 bg-green-900 border border-green-700 p-4 rounded hover:bg-green-950">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-pulse-icon lucide-heart-pulse size-14"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/></svg>
      <h2 class="font-semibold text-2xl">Diagnostic</h2>
      <div class="">
        <p class="text-sm">Diagnostic animalier pour votre bétail</p>
        <p class="module-description text-sm">Posez vos questions sur la santé de vos animaux et obtenez des conseils vétérinaires instantanés.</p>
      </div>
      <a href="{{ route('diagnostic') }}" class="bg-white text-green-950 w-fit">Diagnostiquer</a>
    </div>

    <div href="context.html?next=conseils.html" class="block bg-green-900 border border-green-700 p-4 rounded hover:bg-green-950 flex flex-col gap-7">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-megaphone-icon lucide-megaphone size-14"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
      <h2 class="font-semibold text-2xl">Conseils</h2>
      <div class="">
        <p>Conseils animalier pour votre bétail</p>
        <p class="module-description">Posez vos questions sur la santé de vos animaux et obtenez des conseils vétérinaires instantanés.</p>
      </div>
      <a href="{{ route('conseil') }}" class="bg-white text-green-950 w-fit">Demander Conseils</a>
    </div>

    <div href="context.html?next=suivi.html" class="block bg-green-900 border border-green-700 p-4 rounded hover:bg-green-950 flex flex-col gap-7">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-ring-icon lucide-bell-ring size-14"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M22 8c0-2.3-.8-4.3-2-6"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/><path d="M4 2C2.8 3.7 2 5.7 2 8"/></svg>
      <h2 class="font-semibold text-2xl">Suivi</h2>
      <div class="">
        <p>Faites vous notifier les actions importantes à mener pour la survie de votre activité</p>
        <p class="module-description">Enregistrez vos actions pour vous les faire rappeler par un système de notifications ou .</p>
      </div>
      <a href="context.html?next=suivi.html"  class="bg-white text-green-950 w-fit">Créer des rappels</a>
    </div>
  </div>
