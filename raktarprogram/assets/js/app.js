function applyTheme(mode) { //Téma alkalmazása
    if (mode === "dark") {
        document.body.classList.add("dark");
    } else {
        document.body.classList.remove("dark");
    }
}

// Aktuális téma lekérése
function currentTheme() {
    return document.body.classList.contains("dark") ? "dark" : "light";
}

//Téma váltása
function toggleDark() {
    const next = currentTheme() === "dark" ? "light" : "dark";
    applyTheme(next);
    localStorage.setItem("theme", next);
}

window.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem("theme");
    applyTheme(savedTheme === "dark" ? "dark" : "light");

    const darkButton = document.getElementById("darkToggle");
    if (darkButton) {
        darkButton.addEventListener("click", toggleDark);
    }
});