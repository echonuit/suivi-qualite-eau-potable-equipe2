/**
 * Avec ce code on peut retrouver la commune sélectionnée par l'utilisateur
 * et de copier son code insee dans un champ masqué (input type="hidden") avant la soumission du formulaire.
 * Ça permet de comparer uniquement le code insee pour identifier une commune sélectionnée
 * sans que l'utilisateur n'ait besoin de connaître le code insee non plus).
 */
const inputCommune = document.getElementById('choix-commune');
const listeCommunes = document.getElementById('liste-communes');
const inseeCommune = document.getElementById('insee-commune');

inputCommune.addEventListener('input', function () {
    inseeCommune.value = '';

    for (const commune of listeCommunes.options){
        if (commune.value === inputCommune.value){

        inseeCommune.value = commune.dataset.insee;
        break;
    }
}
});

