const input = document.getElementById("imageInput");
const preview = document.getElementById("preview");
const previewGallery = document.getElementById("previewGallery");

//Eseményfigyelő a fájlváltoztatásra
if (input) {
    input.addEventListener("change", function () {
        const files = Array.from(this.files || []);

        //Előnézet törlése új fájlok kiválasztásakor
        if (previewGallery) {
            previewGallery.innerHTML = "";
        }

        //Ha nincs fájl kiválasztva, töröljük az előnézetet
        if (files.length === 0) {
            if (preview) {
                preview.removeAttribute("src");
            }
            return;
        }

        //Első kép előnézetének megjelenítése
        const firstImage = files.find(file => file.type.startsWith("image/"));

        //Ha van kép, akkor megjelenítjük az első kép előnézetét
        if (firstImage && preview) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(firstImage);
        }

        //Galéria előnézet megjelenítése minden kiválasztott képhez
        if (previewGallery) {
            files.forEach(file => {
                if (!file.type.startsWith("image/")) return;

                //Kép beolvasása és előnézet létrehozása
                const reader = new FileReader();

                reader.onload = function (e) {
                    //Kép előnézetének létrehozása
                    const wrapper = document.createElement("div");
                    wrapper.classList.add("preview-box");

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("preview-item");
                    img.alt = file.name;

                    //Kép előnézetének hozzáadása a galériához
                    wrapper.appendChild(img);
                    previewGallery.appendChild(wrapper);
                };

                //Beolvasás
                reader.readAsDataURL(file);
            });
        }
    });
}