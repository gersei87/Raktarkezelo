document.addEventListener("DOMContentLoaded", () => {
    //Form és gomb kiválasztása
    const form = document.querySelector(".form-right");
    if (!form) return;

    const saveBtn = document.querySelector(".btn");
    if (!saveBtn) return;

    //Kattintás figyelése a mentés gombra
    saveBtn.addEventListener("click", (e) => {
        e.preventDefault();

        //Validáció indul
        let valid = true;
        clearErrors();

        const inputs = form.querySelectorAll("input, textarea, select");

        //Minden mező ellenőrzése
        inputs.forEach(input => {
            const type = (input.type || "").toLowerCase();
            const tag = input.tagName.toLowerCase();
            const value = (input.value || "").trim();

            // rejtett és file mezőt most kihagyjuk
            if (type === "hidden" || type === "file") {
                return;
            }

            // kötelező mezők
            if (input.hasAttribute("required") && value === "") {
                showError(input, "Kötelező mező");
                valid = false;
                return;
            }

            // szám mezők
            if (isNumberField(input)) {
                if (value !== "" && isNaN(value)) {
                    showError(input, "Számot adj meg");
                    valid = false;
                    return;
                }

                // Mennyiség nem lehet 0
                if (getFieldKey(input).includes("mennyiség") || getFieldKey(input).includes("mennyiseg")) {
                    if (value !== "" && parseInt(value, 10) < 1) {
                        showError(input, "Nem lehet 0");
                        valid = false;
                    }
                }
            }

            // select kötelező ellenőrzés
            if (tag === "select" && input.hasAttribute("required") && value === "") {
                showError(input, "Válassz kategóriát");
                valid = false;
            }
        });

        //Ha minden jó akkor submit
        if (valid) {
            form.closest("form")?.submit();
        }
    });

    //Segédfüggvények
    function getFieldKey(input) {
        const placeholder = input.placeholder || "";
        const name = input.name || "";
        return (placeholder + " " + name).toLowerCase();
    }

    //Eldönti, hogy számként kell-e kezelni a mezőt
    function isNumberField(input) {
        const key = getFieldKey(input);
        const type = (input.type || "").toLowerCase();

        return (
            type === "number" ||
            key.includes("ár") ||
            key.includes("ar") ||
            key.includes("mennyiség") ||
            key.includes("mennyiseg") ||
            key.includes("súly") ||
            key.includes("suly")
        );
    }

    //Hiba megjelenítése
    function showError(input, message) {
        input.style.border = "1px solid red";

        const error = document.createElement("small");
        error.classList.add("error-msg");
        error.innerText = message;

        input.parentNode.appendChild(error);
    }

    //Hibák törlése
    function clearErrors() {
        document.querySelectorAll(".error-msg").forEach(e => e.remove());
        document.querySelectorAll(".form-right input, .form-right textarea, .form-right select").forEach(i => {
            i.style.border = "1px solid #ddd";
        });
    }
});