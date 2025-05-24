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
