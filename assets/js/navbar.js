// Gestion du menu mobile
document.addEventListener("DOMContentLoaded", function () {
    const mobileMenuToggle = document.getElementById("mobile-menu-toggle");
    const mobileMenuOverlay = document.getElementById("mobile-menu-overlay");
    const mobileMenuClose = document.getElementById("mobile-menu-close");
    const mobileMenu = document.querySelector(".mobile-menu");

    // Ouvrir le menu mobile
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener("click", function () {
            mobileMenuOverlay.classList.remove("opacity-0", "invisible");
            mobileMenuOverlay.classList.add("opacity-100", "visible");
            mobileMenu.classList.remove("-translate-x-full");
            mobileMenu.classList.add("translate-x-0");
            document.body.style.overflow = "hidden";
        });
    }

    // Fermer le menu mobile
    function closeMobileMenu() {
        mobileMenuOverlay.classList.add("opacity-0", "invisible");
        mobileMenuOverlay.classList.remove("opacity-100", "visible");
        mobileMenu.classList.add("-translate-x-full");
        mobileMenu.classList.remove("translate-x-0");
        document.body.style.overflow = "";
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener("click", closeMobileMenu);
    }

    // Fermer en cliquant sur l'overlay
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener("click", function (e) {
            if (e.target === mobileMenuOverlay) {
                closeMobileMenu();
            }
        });
    }

    // Fermer avec la touche Escape
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeMobileMenu();
        }
    });

    // Gestion des menus déroulants desktop avec un meilleur contrôle
    const dropdownTriggers = document.querySelectorAll(".group");

    dropdownTriggers.forEach((trigger) => {
        let timeoutId;
        const dropdown = trigger.querySelector(
            ".mega-menu, .dropdown-menu, .user-dropdown"
        );

        if (dropdown) {
            trigger.addEventListener("mouseenter", function () {
                clearTimeout(timeoutId);
                dropdown.classList.add("show");
            });

            trigger.addEventListener("mouseleave", function () {
                timeoutId = setTimeout(() => {
                    dropdown.classList.remove("show");
                }, 150); // Petit délai pour éviter les fermetures accidentelles
            });
        }
    });

    // Animation de la barre de recherche - supprimée
    const searchInput = document.querySelector(".search-input");
    // Focus animation supprimée selon demande utilisateur

    // Animation du panier au survol - supprimée selon demande utilisateur
    const cartButton = document.querySelector(".cart-button");
    // Transform animation supprimée

    // Smooth scroll pour les liens d'ancre
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });

    // Indicateur de scroll pour la navbar
    let lastScrollTop = 0;
    const navbar = document.querySelector(".navbar-modern");

    window.addEventListener("scroll", function () {
        const scrollTop =
            window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scroll vers le bas
            navbar.style.transform = "translateY(-100%)";
        } else {
            // Scroll vers le haut
            navbar.style.transform = "translateY(0)";
        }

        lastScrollTop = scrollTop;
    });
});
