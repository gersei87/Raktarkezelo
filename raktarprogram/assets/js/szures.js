let activeCategory = "all";
let searchText = "";

//Szűrési logika alkalmazása a táblázatra és a mobil nézetre
function applyFilters() {
    document.querySelectorAll("#productTable tbody tr").forEach(row => {
        const rowCategory = (row.dataset.category || "").toLowerCase();
        const rowText = row.innerText.toLowerCase();

        const matchesCategory =
            activeCategory === "all" || rowCategory === activeCategory.toLowerCase();

        const matchesSearch =
            rowText.includes(searchText);

        row.style.display = (matchesCategory && matchesSearch) ? "" : "none";
    });

    //Itt kezdődik a mobil nézet szűrése
    document.querySelectorAll(".mobile-product-list .product-card").forEach(card => {
        const cardCategory = (card.dataset.category || "").toLowerCase();
        const cardText = card.innerText.toLowerCase();

        const matchesCategory =
            activeCategory === "all" || cardCategory === activeCategory.toLowerCase();

        const matchesSearch =
            cardText.includes(searchText);

        card.style.display = (matchesCategory && matchesSearch) ? "" : "none";
    });
}

//Kategória szűrő gombok eseménykezelői
document.querySelectorAll('.sidebar li[data-filter]').forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll('.sidebar li[data-filter]').forEach(item => {
            item.classList.remove("active");
        });

        btn.classList.add("active");
        activeCategory = btn.dataset.filter || "all";
        applyFilters();
    });
});

//Keresőmező eseménykezelője
document.getElementById("searchInput")?.addEventListener("keyup", function () {
    searchText = this.value.toLowerCase().trim();
    applyFilters();
});

//Alapértelmezett szűrés alkalmazása oldal betöltésekor
window.addEventListener("DOMContentLoaded", () => {
    const firstItem = document.querySelector('.sidebar li[data-filter="all"]');
    if (firstItem) {
        firstItem.classList.add("active");
    }

    applyFilters();
});