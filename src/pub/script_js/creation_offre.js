let id_ajout_tarif = 0;
function ajoutTarif() {
    id_ajout_tarif++;
    document.getElementById('tarif_ajoute').innerHTML+='<br class="''"/><input id="nom_tarif_'+id_ajout_tarif+'" type="text" required name="nom_tarif_'+id_ajout_tarif+'" placeholder="Nom du tarif n°'+id_ajout_tarif+'"  />';
    document.getElementById('tarif_ajoute').innerHTML+='<br/><input id="prix_tarif_'+id_ajout_tarif+'" required type="number" name="prix_tarif_'+id_ajout_tarif+'" placeholder="Prix du tarif n°'+id_ajout_tarif+'"  />';
    document.getElementById('tarif_ajoute').innerHTML+='<br/><button type="button" id="sup_tarif_'+id_ajout_tarif+'" onClick="supTarif('+id_ajout_tarif+')">Supprimer tarif n°'+id_ajout_tarif+'</button>';
}

function supTarif(num){
    document.getElementById("nom_tarif_"+num).remove();
    document.getElementById("prix_tarif_"+num).remove();
    document.getElementById("sup_tarif_"+num).remove();
}	