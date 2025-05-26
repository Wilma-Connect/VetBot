<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>VetBot – Menu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('styles/header.css') }}">
  <style>
    .fade-in {
      animation: fadeIn 0.8s ease forwards;
      opacity: .5;
      transform: translateY(20px);
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

</head>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".grid > div");

    cards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add("fade-in");
      }, index * 150); // décalage progressif
    });
  });
</script>

<body class="bg-[#faf3e8] pt-28 text-white p-6 min-h-screen">
  <header class="header bg-[#faf3e8] mt-1">
    <div class="header-nav flex flex-row justify-between">
      <div class="logo p-0">
        <a href="index.html" class="p-0">
          <img src="assets/vetbot-logo.png" alt="VetBot Logo" class="w-14 h-auto">
          <!-- <h1>VetBot</h1> -->
        </a>
      </div>
      <div class="flex items-center gap-3">
        <button id="authButton" class="auth-button shadow-md">Se connecter</button>
        <button id="" class="b2 shadow-md">S'inscrire</button>
      </div>
    </div>
  </header>


  @include('includes.menu')


  <script src="{{ asset('script/script.js') }}"></script>
</body>
</html>
