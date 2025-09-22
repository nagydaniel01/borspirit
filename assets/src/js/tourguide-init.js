import { TourGuideClient } from "@sjmc11/tourguidejs";

document.addEventListener("DOMContentLoaded", function () {
    const tg = new TourGuideClient({
        debug: true,
        exitOnClickOutside: true,
        autoScroll: true,
        autoScrollSmooth: true,
        autoScrollOffset: 20,
        backdropClass: "",
        backdropColor: "rgba(20,20,21,0.5)",
        targetPadding: 0,
        backdropAnimate: true,
        dialogClass: "",
        dialogZ: 999,
        dialogWidth: 0,
        dialogMaxWidth: 340,
        dialogAnimate: true,
        closeButton: false,
        nextLabel: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"/>
                  <polyline points="12 5 19 12 12 19"/>
                </svg>`,
        prevLabel: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="19" y1="12" x2="5" y2="12"/>
                  <polyline points="12 19 5 12 12 5"/>
                </svg>`,
        finishLabel: "Finish",
        hidePrev: false,
        hideNext: false,
        completeOnFinish: true,
        showStepDots: true,
        stepDotsPlacement: "footer",
        showButtons: true,
        showStepProgress: false,
        keyboardControls: true,
        exitOnEscape: true,
        rememberStep: true,
        steps: [
            {
                target: ".navbar-brand",
                title: "Brand Logo",
                content: "Welcome! This is our brand logo. Click it anytime to return to the homepage.",
            },
            {
                target: "#menu-fomenu",
                title: "Navigation Menu",
                content: "These are the main navigation links. Use them to explore different sections like services, events, blog, shop, and subscriptions.",
            },
            {
                target: ".header-actions",
                title: "Account Actions",
                content: "On desktop, this button is for logging in or registering your account. Click it to access your profile or create a new account.",
            }
        ],
    });

    const btn = document.getElementById("start-tour");
    if (btn) {
        btn.addEventListener("click", () => tg.start());
    }

    // Auto-start tour after 3 seconds, only once per session
    if (!sessionStorage.getItem("tourShown")) {
        setTimeout(() => {
            tg.start();
            sessionStorage.setItem("tourShown", "true");
        }, 3000);
    }
});
