"use strict";

class Toasts {
    show(message, type = "info", duration = 5000) {
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
        <div class="flex items-center">
            <div class="toast-content">${message}</div>
            <button class="toast-close">&times;</button>
            </div>
        `;

        // Close btn
        toast.querySelector(".toast-close").onclick = () => this.close(toast);

        document.body.appendChild(toast);

        // Animation
        requestAnimationFrame(() => toast.classList.add("toast-show"));

        // Auto-close
        if (duration > 0) {
            setTimeout(() => this.close(toast), duration);
        }

        return toast;
    }

    close(toast) {
        toast.classList.remove("toast-show");
        toast.classList.add("toast-hide");

        toast.addEventListener("transitionend", () => toast.remove(), {
            once: true,
        });
    }

    // Types
    success(msg, duration = 5000) {
        return this.show(msg, "success", duration);
    }
    error(msg, duration = 8000) {
        return this.show(msg, "error", duration);
    }
    warning(msg, duration = 6000) {
        return this.show(msg, "warning", duration);
    }
    info(msg, duration = 5000) {
        return this.show(msg, "info", duration);
    }
}

// Init
window.toasts = new Toasts();
