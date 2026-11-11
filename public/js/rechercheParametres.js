/**
 * Ce script aide a synchroniser les cases à cocher avec les paramètres sélectionnés.
 * Permet de sélectionner un paramètre depuis la datalist ou avec le bouton et
 * met à jour automatiquement la liste des codes sandre à transmettre au ParametresController.
 * Nous permet, également, de reagir aux action de l'utilisateur sans recharger la page.
 *
 */
const form = document.getElementById('formParametres');

if (form) {
    const recherche = document.getElementById('rechercheParam');
    const champParametres = document.getElementById('parametresSelectionnes');

    const checkboxes = [...form.querySelectorAll('.js-parametre')];
    const options = [...document.querySelectorAll('#listeParametres option')];

    // On synchronise les cases à cocher sélectionnées avec le champ masqué(hidden)
    function synchroniserParametres() {
        const codes = checkboxes
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.dataset.code);

        champParametres.value = codes.join(',');
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', synchroniserParametres);
    });

    // on selectionne un parametre de la datalist
    recherche.addEventListener('change', () => {
        const option = options.find(option => option.value === recherche.value);

        if (!option) return;

        const checkbox = checkboxes.find(
            checkbox => checkbox.dataset.code === option.dataset.code
        );

        if (!checkbox) return;

        checkbox.checked = true;
        recherche.value = '';

        synchroniserParametres();

        form.requestSubmit();
    });

    // On s'assure que le champ hidden est a jour avant le submit de la form
    form.addEventListener('submit', synchroniserParametres);
}
