<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetBot - L'intelligence Artificielle au service des éleveurs</title>
    <link rel="stylesheet" href="{{ asset('styles/styles.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/vetbot-logo.png" alt="VetBot Logo" class="w-auto h-16">
            <!-- <h1>VetBot</h1> -->
        </div>
        <div class="menu-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu text-green-900 size-10"><path d="M4 12h16"/><path d="M4 18h16"/><path d="M4 6h16"/></svg>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="/" class="active">Accueil</a></li>
                <li><a href="{{ route('menu') }}">VetBot</a></li>
                <!-- <li><a href="agricast.html">AgriCast</a></li> -->
                <li><a href="support.html">Aide & Support</a></li>
                {{-- <li><a href="login.html" class="login-btn">Connexion</a></li> --}}
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero bg-[url('assets/hero.png')] bg-cover bg-center bg-no-repeat">
            <div class="hero-content bg-black/30">
                <h1>VetBot – L'intelligence artificielle au service des éleveurs</h1>
                <p>Aides et suivi pour maintenir un rendement positif de son élevage</p>
            </div>
        </section>

        <section class="modules gap-10 grid">
            <div class="module-card vetbot">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="">
                        <img src="assets/diagnostic.jpg" alt="" class="w-full h-[350px] rounded-md">
                    </div>
                    <div class="">
                        <div class="module-icon flex justify-center p-3 rounded-full bg-green-600/10 w-fit mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-pulse-icon lucide-heart-pulse size-14"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/></svg>
                        </div>
                        <h2 class="text-3xl font-semibold">Diagnostic</h2>
                        <p>Diagnostic animalier pour votre bétail</p>
                        <p class="module-description">Posez vos questions sur la santé de vos animaux et obtenez des conseils vétérinaires instantanés.</p>
                        <a href="diagnostic.html" class="btn primary-btn">Diagnostiquer</a>
                    </div>
                </div>
            </div>
            <div class="module-card vetbot">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="">
                        <div class="module-icon flex justify-center p-3 rounded-full bg-green-600/10 w-fit mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-megaphone-icon lucide-megaphone size-14"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                        </div>
                        <h2 class="text-3xl font-semibold">Conseils</h2>
                        <p>Conseils animalier pour votre bétail</p>
                        <p class="module-description">Posez vos questions sur la santé de vos animaux et obtenez des conseils vétérinaires instantanés.</p>
                        <a href="conseils.html" class="btn primary-btn">Demander Conseils</a>
                    </div>
                    <div class="">
                        <img src="assets/conseil.jpg" alt="" class="w-full h-[350px] rounded-md">
                    </div>
                </div>
            </div>
            <div class="module-card vetbot">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="">
                        <img src="assets/suivi2.jpg" alt="" class="w-full h-[350px] rounded-md">
                    </div>
                    <div class="">
                        <div class="module-icon flex justify-center p-3 rounded-full bg-green-600/10 w-fit mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-ring-icon lucide-bell-ring size-14"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M22 8c0-2.3-.8-4.3-2-6"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/><path d="M4 2C2.8 3.7 2 5.7 2 8"/></svg>
                        </div>
                        <h2 class="text-3xl font-semibold">Suivi</h2>
                        <p>Faites vous notifier les actions importantes à mener pour la survie de votre activité</p>
                        <p class="module-description">Enregistrez vos actions pour vous les faire rappeler par un système de notifications ou .</p>
                        <a href="suivi.html" class="btn primary-btn">Créer des rappels</a>
                    </div>
                </div>
            </div>
            <!-- <div class="module-card agricast">
                <div class="module-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h2>AgriCast</h2>
                <p>Aide à la culture et gestion agricole</p>
                <p class="module-description">Obtenez des prévisions et des conseils personnalisés pour optimiser vos cultures.</p>
                <a href="agricast.html" class="btn primary-btn">Essayer AgriCast</a>
            </div> -->
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>VetBot</h3>
                <p>L'intelligence artificielle au service des éleveurs</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: contact@wilma-connect.com</p>
                <p>Téléphone: +225 07 87 85 63 89</p>
            </div>
            <div class="footer-section">
                <h3>Liens utiles</h3>
                <ul>
                    <li><a href="support.html">Aide & Support</a></li>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 VetBot. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="{{ asset('script/script.js') }}"></script>
</body>
</html>
