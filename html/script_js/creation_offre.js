let id_ajout_tarif = 0;
let id_ajout_horraire = 0;

function ajoutTarif() {
    id_ajout_tarif++;
    document.getElementById('tarif_ajoute').innerHTML+='<br class="br_'+id_ajout_tarif+'"/><input id="nom_tarif_'+id_ajout_tarif+'" type="text" required name="nom_tarif_'+id_ajout_tarif+'" placeholder="Nom du tarif n°'+id_ajout_tarif+'"  />';
    document.getElementById('tarif_ajoute').innerHTML+='<br class="br_'+id_ajout_tarif+'"/><input id="prix_tarif_'+id_ajout_tarif+'" required type="number" name="prix_tarif_'+id_ajout_tarif+'" placeholder="Prix du tarif n°'+id_ajout_tarif+'"  />';
    document.getElementById('tarif_ajoute').innerHTML+='<br class="br_'+id_ajout_tarif+'"/><button type="button" id="sup_tarif_'+id_ajout_tarif+'" onClick="supTarif('+id_ajout_tarif+')">Supprimer tarif n°'+id_ajout_tarif+'</button>';
}

function supTarif(num){
    e = document.getElementsByClassName("br_"+num);
    for (let i = 0; i < e.length; i++) {
        e[i].remove();
        console.log(i);
    }

    document.getElementById("nom_tarif_"+num).remove();
    document.getElementById("prix_tarif_"+num).remove();
    document.getElementById("sup_tarif_"+num).remove();

    // me demandez pas pourquoi je dois le mettre avant et apres sinon il en reste un
    // il en reste un si je met que le premier et il en reste un si je ne met que celui la 
    //donc j'ai opté pour du quick and dirty et je le met deux fois... (problem solved ig)
    e = document.getElementsByClassName("br_"+num);
    for (let i = 0; i < e.length; i++) {
        e[i].remove();
        console.log(i);
    }
}

function ajoutHorraire(jour) {
    id_ajout_horraire++;
    //TODO

}