<?php
require_once 'model/Offre.php';
require_once 'auth.php';
require_once 'model/Compte.php';
require('../fpdf186/fpdf.php');




// Récupérer ls information du compte 

$compte = notfalse(valeur: Compte::from_db(id_compte: Auth\id_compte_connecte()));

if(!$compte instanceof Professionnel){
echo "error";
}



// Créer une classe pour la facture (facultatif)
class FacturePDF extends FPDF {
    // En-tête
    function Header() {
        // Logo
        $this->Image('../images/logo.jpg', 10, 10); // Chemin du logo, x, y, largeur
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Facture', 0, 1, 'C'); // Titre centré

        $this->Ln(10); // Saut de ligne
    }

    // Pied de page
    function Footer() {
        $this->SetY(-15); // À 15 mm du bas
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C'); // Numéro de page
    }

    // Table des produits/services
    function Table($header, $data)//ImprovedTable
{
    // Column widths
    $w = array(40, 35, 40, 45);
    // Header
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    // Data
    foreach($data as $row)
    {
        $this->Cell($w[0],6,$row[0],'LR');
        $this->Cell($w[1],6,$row[1],'LR');
        $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
        $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
        $this->Ln();
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
}
}

// Créer une instance de la classe
$pdf = new FacturePDF();
$pdf->AliasNbPages(); // Pour afficher le nombre total de pages
$pdf->AddPage(); // Ajouter une page

//information PacteS
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, "Pacte", 0, 1);
$pdf->Cell(100, 10, "Adresse : 1 rue Edouard Branly", 0, 1,"R");
$pdf->Cell(100, 10, "Email : xxxx@.com", 0, 1,"R");
$pdf->Cell(100, 10, "Tel. : XXXXXXXXXX", 0, 1,"R");
$pdf->Cell(100, 10, "Site : https://413.ventsdouest.dev", 0, 1,"R");
// Informations sur le client
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, "Client : $compte->denomination", 0, 1);
$pdf->Cell(100, 10, "Adresse : ".$compte->adresse->format(), 0, 1);
$pdf->Cell(100, 10, "Email : ".$compte->email, 0, 1);
$pdf->Cell(100, 10, "Tel. : $compte->telephone", 0, 1);


$pdf->Cell(100, 10, "Date : " . date('d/m/Y'), 0, 1);
$pdf->Ln(10); // Saut de ligne

// Tableau des offre
$header = ['Titre', "Type d'abonnement", 'Catégorie', 'Jours en ligne',"Prix TTC"];
$data = [];
$resultat_global = 0;
$id_professionnel = Auth\exiger_connecte_pro();
$offres= Offre::from_db_all($id_professionnel);
foreach ($offres as $offre) {
    $resultat_offre  = $offre->en_ligne_ce_mois_pendant->days * $offre->abonnement->prix_journalier;
    $resultat_offre += $resultat_offre * 0.2;
    $resultat_global += $resultat_offre;


    $data[] = [$offre->titre, $offre->abonnement->libelle,$offre->categorie,$offre->en_ligne_ce_mois_pendant->days,$resultat_offre];
}

$pdf->Table($header, $data);
$pdf->Ln(10); // Saut de ligne

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(144, 10, 'Total', 1, 0, 'R');
$pdf->Cell(48, 10, $resultat_global, 1, 1, 'C');

// Générer et afficher le PDF
$pdf->Output('I', 'facture.pdf'); // I = afficher dans le navigateur, D = télécharger
?>





