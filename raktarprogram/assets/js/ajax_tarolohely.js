document.addEventListener("DOMContentLoaded", function () { //csak a html betöltése után fut le a script
    const raktarSelect = document.getElementById("raktarSelect");//elemek kviválasztása
    const taroloSelect = document.getElementById("tarolohelySelect");//elemek kviválasztása

    if (!raktarSelect || !taroloSelect) return; //védelem hiányzó elemek ellen

    raktarSelect.addEventListener("change", function () { //raktár változás figyelése
        const raktarId = this.value; //kiválasztozz raktár

        taroloSelect.innerHTML = '<option value="">Betöltés...</option>'; //betöltés jelzése

        if (!raktarId) {
            taroloSelect.innerHTML = '<option value="">Tárolóhely kiválasztása</option>'; //Ha nincs kiválasztott raktár
            return;
        }

        fetch('ajax/get_tarolohelyek.php?raktar_id=' + encodeURIComponent(raktarId)) //AJAX kérés a tárolóhelyek lekéréséhez
            .then(response => response.json()) //jason válasz feldolgozása
            .then(data => { //Tárolohelyek betöltése
                taroloSelect.innerHTML = '<option value="">Tárolóhely kiválasztása</option>';

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.tarolohely_id;
                    option.textContent = item.kod;
                    taroloSelect.appendChild(option);
                });
            })
            .catch(() => { //Hibakezelés
                taroloSelect.innerHTML = '<option value="">Hiba a betöltés során</option>';
            });
    });
});