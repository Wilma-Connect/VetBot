// Exemple de script global
console.log("Chargement du contexte VetBot");
const context = JSON.parse(localStorage.getItem("vetbot_context") || "{}");

// Vérification de l'état de connexion
function checkAuth() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const authButton = document.getElementById('authButton');
    if (authButton) {
        authButton.textContent = isLoggedIn ? 'Se déconnecter' : 'Se connecter';
        authButton.onclick = () => {
            if (isLoggedIn) {
                localStorage.removeItem('isLoggedIn');
                window.location.href = 'login.html';
            } else {
                window.location.href = 'login.html';
            }
        };
    }
}

// Gestion de la navigation
function handleNavigation() {
    const pageSelect = document.getElementById('pageSelect');
    if (pageSelect) {
        pageSelect.addEventListener('change', (e) => {
            const selectedPage = e.target.value;
            if (selectedPage) {
                window.location.href = selectedPage;
            }
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    checkAuth();
    handleNavigation();
});

document.addEventListener("DOMContentLoaded", () => {
    // Mobile menu toggle
    const menuToggle = document.querySelector(".menu-toggle")
    const mainNav = document.querySelector(".main-nav")
  
    if (menuToggle && mainNav) {
      menuToggle.addEventListener("click", () => {
        mainNav.classList.toggle("active")
      })
    }
  
    // Close menu when clicking outside
    document.addEventListener("click", (event) => {
      if (
        mainNav &&
        mainNav.classList.contains("active") &&
        !event.target.closest(".main-nav") &&
        !event.target.closest(".menu-toggle")
      ) {
        mainNav.classList.remove("active")
      }
    })
  
    // Accordion functionality for support page
    const accordionHeaders = document.querySelectorAll(".accordion-header")
  
    if (accordionHeaders.length > 0) {
      accordionHeaders.forEach((header) => {
        header.addEventListener("click", function () {
          const content = this.nextElementSibling
          const isOpen = content.style.display === "block"
  
          // Close all accordion items
          document.querySelectorAll(".accordion-content").forEach((item) => {
            item.style.display = "none"
          })
  
          document.querySelectorAll(".accordion-header i").forEach((icon) => {
            icon.className = "fas fa-chevron-down"
          })
  
          // Open the clicked item if it was closed
          if (!isOpen) {
            content.style.display = "block"
            this.querySelector("i").className = "fas fa-chevron-up"
          }
        })
      })
    }
  
    // Support page category tabs
    const categoryLinks = document.querySelectorAll(".category-list a")
  
    if (categoryLinks.length > 0) {
      categoryLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
          e.preventDefault()
  
          const targetId = this.getAttribute("href").substring(1)
  
          // Hide all sections
          document.querySelectorAll(".support-section").forEach((section) => {
            section.classList.remove("active")
          })
  
          // Show target section
          document.getElementById(targetId).classList.add("active")
  
          // Update active category
          document.querySelectorAll(".category-list li").forEach((item) => {
            item.classList.remove("active")
          })
  
          this.parentElement.classList.add("active")
        })
      })
    }
  
    // Profile tabs
    const profileTabs = document.querySelectorAll(".tab-btn")
  
    if (profileTabs.length > 0) {
      profileTabs.forEach((tab) => {
        tab.addEventListener("click", function () {
          const tabId = this.getAttribute("data-tab")
  
          // Hide all tab contents
          document.querySelectorAll(".tab-content").forEach((content) => {
            content.classList.remove("active")
          })
  
          // Show selected tab content
          document.getElementById(tabId + "-tab").classList.add("active")
  
          // Update active tab
          document.querySelectorAll(".tab-btn").forEach((btn) => {
            btn.classList.remove("active")
          })
  
          this.classList.add("active")
        })
      })
    }
  })
  
  <script>
  document.querySelectorAll(".grid > div").forEach(card => {
    const link = card.querySelector("a");

    link.addEventListener("click", (e) => {
      e.preventDefault();

      // Fade out toutes les cartes
      document.querySelectorAll(".grid > div").forEach(el => el.classList.add("fade-out"));

      // Aller à la page après l'animation
      setTimeout(() => {
        window.location.href = link.getAttribute("href");
      }, 500); // Doit correspondre à la durée de fadeOut
    });
  });
</script>
