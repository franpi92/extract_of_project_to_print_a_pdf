<?php

/**
 * M098695
 * Classe de génération de  PDF
 */



class Generer_PDF  extends  FPDF
{
    public function __construct($data, $fichier_pdf) {
    $this->police_standard      = 'Arial';
    $this->taille_police_std    = 9;
    $this->interligne_standard  = 4;
    $this->_fichier_pdf         = $fichier_pdf;
    parent::__construct('P','mm','A4');
    $this->AddPage();
    $this->buildPdf($data);
    }

    var $widths;
    var $aligns;
    var $_fichier_pdf;

    function SetWidths($w)
    {
        //Tableau des largeurs de colonnes
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Tableau des alignements de colonnes
        $this->aligns=$a;
    }


        function Row($data)
    {
        //Calcule la hauteur de la ligne
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Effectue un saut de page si nécessaire
         $this->CheckPageBreak($h);

        //Dessine les cellules
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Sauve la position courante
            $x=$this->GetX();
            $y=$this->GetY();
            //Dessine le cadre
            $this->Rect($x,$y,$w,$h);
            //Imprime le texte
            $this->MultiCell($w, 5, $data[$i],0,$a);
            //Repositionne à droite
            $this->SetXY($x+$w,$y);
        }
        //Va à la ligne
         $this->Ln($h);
    }

        function CheckPageBreak($h)
    {
        //Si la hauteur h provoque un débordement, saut de page manuel
        if($this->GetY()+$h>$this->PageBreakTrigger)
        {
            $this->AddPage($this->CurOrientation);
            $this->Ln();
            $this->Ln();
        }
    }

    function NbLines($w,$txt)
    {
        //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    /**
     * Corps du PDF
     * colonnes : 1 à 200
     * lignes :   1 à 300 (entre header : 14 et footer : 275)
     */
    public function buildPdf($data) {
        /* ****************************************************************************************
         * Page 1
         * **************************************************************************************** */
        $this->SetTextColor( 0, 0, 0 );                                                                     // Couleur noir
        $this->SetFont( $this->police_standard, '', $this->taille_police_std );
        
        // Image de conseiller qui dialogue
        $ligne_pdf = 22;             
        $this->Image( PUBLIC_PATH . "imgs/conseiller.png", 13, $ligne_pdf, 190 );                           // string, x, y, largeur
        // Raison sociale                   
        $ligne_pdf += 18;                                                                                   // $ligne_pdf = 40                   
        $this->SetXY( 100, $ligne_pdf );                    
        $this->Cell(  200, 5, "Raison Sociale : " . utf8_decode($data[9][11]) );                            // largeur, hauteur, texte, [bordure L|T|R|B ] OK le 02/01/2020
        // Adresse mel                  
        $ligne_pdf += 5;                                                                                    // $ligne_pdf = 45                   
        $this->SetXY( 100, $ligne_pdf );                            
        $this->Cell(  200, 5, "A l'attention de : " . utf8_decode($data[10][9]) );                         // largeur, hauteur, texte, [bordure L|T|R|B ] OK le 02/01/2020
        // Mme, Mr                      
        $ligne_pdf = 100;                       
	    $this->SetXY( 20, $ligne_pdf );                         
        $this->Cell( 200, 5, utf8_decode("Madame, Monsieur,") );                                            // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 7;
        $this->SetXY( 20, $ligne_pdf );                                                                     // $ligne_pdf = 107
        $texte_a_afficher  = "Suite à votre demande, nous avons le plaisir de vous adresser le récapitulatif de la solution de financement adaptée à votre projet établie au regard";
        $texte_a_afficher .= " des éléments transmis.";
        $this->MultiCell( 180, 5, utf8_decode($texte_a_afficher), 0, 'L' );                                 // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += $this->interligne_standard;                                                                                    // $ligne_pdf = 111

        //-----------------------------------------------------------------------------------------
        // Votre besoin
        //-----------------------------------------------------------------------------------------
        $ligne_pdf += 9;                                                                                    // $ligne_pdf = 120
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
        $this->SetFont( 'Arial', '', 12 );
	    $this->SetXY( 20, $ligne_pdf);
        $this->Cell( 100, 15, utf8_decode("Votre besoin") );                                                // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Ligne bleu canard épaisse                    
        $ligne_pdf += 11;                                                                                   // $ligne_pdf = 131                 
        $this->SetDrawColor( 4, 139, 154 );                                                                 // Couleur bleu canard
        $this->SetLineWidth( 0.3 );                 
        $this->Line(11, $ligne_pdf, 200, $ligne_pdf );                                                      // x1, y1, x2, y2
        // Besoin à financer                    
        $this->SetTextColor( 0, 0, 0 );                                                                     // Couleur noir
        $this->SetFont( $this->police_standard, 'B', $this->taille_police_std );                  
	    $this->SetXY( 20, ++$ligne_pdf);                                                                    // $ligne_pdf = 132
        $this->Cell( 100, 5, utf8_decode("Besoin à financer") );                                            // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += $this->interligne_standard;                                                           // $ligne_pdf = 136                
        $this->SetFont( $this->police_standard, '', $this->taille_police_std );                   
	    $this->SetXY( 20,$ligne_pdf);                                                                       
        $this->Cell( 100, 5, utf8_decode($data[11][10]) );                                                  // largeur, hauteur, texte, [bordure L|T|R|B ] OK le 02/01/2020
        // Ligne noire fine                 
        $this->SetDrawColor( 0, 0, 0 );                                                                     // Couleur noir 
        $this->SetLineWidth( 0.1 );                 
        $ligne_pdf += 7;                                                                                    // $ligne_pdf = 143                   
        $this->Line( 11, $ligne_pdf, 200, $ligne_pdf );                                                     // x1, y1, x2, y2
        // Montant                  
        $euro = chr( 128 );                 
        $this->SetFont( $this->police_standard, 'B', $this->taille_police_std );                   
	    $this->SetXY( 20, ++$ligne_pdf);                                                                    // $ligne_pdf = 144
        $this->Cell( 100, 5, utf8_decode("Montant HT") );                                                   // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur                   
        $ligne_pdf += $this->interligne_standard;                                                                                    // $ligne_pdf = 148
        $this->SetFont( $this->police_standard, '', $this->taille_police_std );                   
        $this->SetXY( 20, $ligne_pdf );                 
        $montant = (int) $data[7][3];                                                                       // Mis à jour le 02/01/2020
        $this->Cell( 100, 5, number_format($montant, 2, ',', ' ') . ' ' . $euro );                          // largeur, hauteur, texte, [bordure L|T|R|B ]
        // TVA                              
        /* $offset_tva = 70;                               
        $ligne_pdf = 144;                   
        $this->SetFont( 'Arial', 'B', 7 );                  
	    $this->SetXY( $offset_tva, $ligne_pdf );                                
        $this->Cell( 100, 5, utf8_decode("Montant TVA") );                                                  // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur                                       
        $ligne_pdf += $this->interligne_standard;                                                                                    // $ligne_pdf = 148
        $this->SetFont( $this->police_standard, '', $this->taille_police_std );                   
        $this->SetXY($offset_tva, $ligne_pdf);                              
        $tva = $montant * 0.2;                              
        $this->Cell(100, 5, number_format($tva, 2, ',', ' ') . ' ' . $euro);                                // largeur, hauteur, texte, [bordure L|T|R|B ] */
        // Ligne bleu canard épaisse                                
        $this->SetDrawColor( 4, 139, 154 );                                                                 // Couleur bleu canard
        $this->SetLineWidth( 0.3 );                                 
        $ligne_pdf += 6;                                                                                    // $ligne_pdf = 154
        $this->Line( 11, $ligne_pdf, 200, $ligne_pdf );                                                     // x1, y1, x2, y2
        //-----------------------------------------------------------------------------------------
        // Votre solution 
        //-----------------------------------------------------------------------------------------
        $ligne_pdf += 20;                                                                                   // $ligne_pdf = 160
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
        $this->SetFont( 'Arial', '', 12 );
	    $this->SetXY( 20, $ligne_pdf );                                                                     // $ligne_pdf = 171
        $this->Cell( 100, 15 , utf8_decode("Votre solution") );                                             // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Ligne bleu canard épaisse            
        $this->SetDrawColor(4,139,154); // Couleur bleu canard              
        $this->SetLineWidth(0.3);             
        $ligne_pdf += 11;             
        $this->Line( 11, $ligne_pdf, 200, $ligne_pdf );                                                     // x1, y1, x2, y2
        // Solution de financement
        $this->SetFont( 'Arial', 'B', 11 );
	    $this->SetXY( 20, $ligne_pdf );             
        $this->Cell( 100, 14, utf8_decode("Le ".$data[12][10]) );                                           // largeur, hauteur, texte, [bordure L|T|R|B ] OK le 02/01/2020
        $ligne_pdf += 8;             
        $this->SetFont( 'Arial', '', 11 );
	    $this->SetXY( 20, $ligne_pdf );             
        $this->Cell( 100, 12, utf8_decode("Crédit pour un montant et une durée fixés par avance") );        // largeur, hauteur, texte, [bordure L|T|R|B ]
        //-----------------------------------------------------------------------------------------
        // Encadré : rectangle
        //-----------------------------------------------------------------------------------------
        $ligne_pdf += $this->interligne_standard;             
        $ligne_pdf = $this->Encadre( 11, $ligne_pdf, $data, $euro );
        // Ligne bleu canard épaisse            
        $this->SetDrawColor( 4, 139, 154 ); // Couleur bleu canard              
        $this->SetLineWidth( 0.3 );             
        $ligne_pdf += 10;             
        $this->Line( 11, $ligne_pdf, 200, $ligne_pdf );                                                     // x1, y1, x2, y2







        /* **********************************************************************************************************************************************************
         * Page 2
         * ********************************************************************************************************************************************************** */

        $this->AddPage();
        //-----------------------------------------------------------------------------------------
        // Solution 1
        //-----------------------------------------------------------------------------------------
        /* $ligne_pdf = 17;             
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
        $this->SetFont( 'Arial', 'B', 14 );                 
	    $this->SetXY( 85, $ligne_pdf );                                 
        $this->Cell( 100, 14 , utf8_decode("Solution 1") );                                                 // largeur, hauteur, texte, [bordure L|T|R|B ] */
        // Ligne bleu canard épaisse 
        $offset_vertical_volets = 27;                               
        $ligne_pdf = $offset_vertical_volets;                                                               // $ligne_pdf = 27
        $this->SetDrawColor( 4, 139, 154 ); // Couleur bleu canard                                  
        $this->SetLineWidth( 0.3 );                                 
        $this->Line( 11, $ligne_pdf, 200, $ligne_pdf );                                                     // x1, y1, x2, y2
        //-----------------------------------------------------------------------------------------
        // volet gauche : EN SAVOIR PLUS
        //-----------------------------------------------------------------------------------------
        $offset_icone_horiz = 30;
        $offset_icone_vertic = 13;
        $largeur_volet_gauche = 55;
        $this->SetTextColor( 0, 0, 0 );     // Couleur noir
        $this->SetFont( 'Arial', 'B', 7 );
	    $this->SetXY(25,$ligne_pdf);                                                                        // $ligne_pdf = 32
        $this->Cell($largeur_volet_gauche,15,utf8_decode("EN SAVOIR PLUS") );                               // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += $offset_icone_vertic;             
        $this->SetFont( 'Arial', '', 7 );
	    $this->SetXY($offset_icone_horiz, $ligne_pdf);
        $this->Image(PUBLIC_PATH . 'imgs/en_savoir_plus.png', $offset_icone_horiz, $ligne_pdf, 12);         // string, x, y, largeur
        $ligne_pdf += $offset_icone_vertic+4;             
        $this->SetXY( 11, $ligne_pdf );
        $texte_a_afficher  = "Un crédit à moyen terme sert à financer un investissement, que ce soit un bien corporel (bâtiment, matériel, véhicule,";
        $texte_a_afficher .= " ordinateur, ...), ou un besoin incorporel (fonds de commerce, financement de licence, brevet, ...) pour une durée de 2";
        $texte_a_afficher .= " à 15 ans, avec une périodicité de remboursement mensuelle, trimestrielle, semestrielle ou annuelle, à taux fixe ou révisable.";
        $this->MultiCell($largeur_volet_gauche,4,utf8_decode($texte_a_afficher) );                          // largeur, hauteur, texte, [bordure L|T|R|B ]
        $this->Cell($largeur_volet_gauche,5,utf8_decode("") );       // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 30;                
        //-----------------------------------------------------------------------------------------
        // volet gauche : Des financements sur mesure
        //-----------------------------------------------------------------------------------------
        $ligne_pdf += $offset_icone_vertic-4;             
	    $this->SetXY($offset_icone_horiz, $ligne_pdf);
        $this->Image(PUBLIC_PATH . 'imgs/pouce.png', $offset_icone_horiz, $ligne_pdf, 12);                  // string, x, y, largeur
        $this->SetFont( 'Arial', '', 7 );
        $ligne_pdf += $offset_icone_vertic+4;             
        $this->SetXY( 11, $ligne_pdf );
        $this->MultiCell($largeur_volet_gauche,4,utf8_decode("* Des financements sur mesure,") );           // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += $this->interligne_standard;                
	    $this->SetXY( 11, $ligne_pdf );
        $texte_a_afficher  = "* Le prêt à taux fixe pour privilégier la sécurité. Vous connaissez le coût total de votre financement dès la signature du contrat";
        $this->MultiCell($largeur_volet_gauche,4,utf8_decode($texte_a_afficher) );   // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 12;                
	    $this->SetXY( 11, $ligne_pdf );
        $this->MultiCell($largeur_volet_gauche,4,utf8_decode("* Le prêt à taux révisable pour bénéficier de l'évolution des taux") );    // largeur, hauteur, texte, [bordure L|T|R|B ]
        //-----------------------------------------------------------------------------------------
        // volet gauche : L'analyse d'une demande
        //-----------------------------------------------------------------------------------------
        $ligne_pdf += $offset_icone_vertic;             
	    $this->SetXY($offset_icone_horiz, $ligne_pdf);
        $this->Image(PUBLIC_PATH . 'imgs/triangle.png', $offset_icone_horiz, $ligne_pdf, 12);               // string, x, y, largeur
        $this->SetFont( 'Arial', '', 7 );
        $ligne_pdf += $offset_icone_vertic+4;             
        $this->SetXY( 11, $ligne_pdf );
        $texte_a_afficher  = "L'analyse d'une demande de crédit à moyen terme repose principalement sur l'étude de différents éléments :";
        $this->MultiCell( $largeur_volet_gauche,  4, utf8_decode($texte_a_afficher) , 0, 'L');                      // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 8;        
        $texte_a_afficher = "* Situation économique, Situation financière, et plus particulèrement rentabilité de l'entreprise avant l'opération,";
        $texte_a_afficher .= " pendant et après l'opération,";
        $this->MultiCell( $largeur_volet_gauche,  4, utf8_decode($texte_a_afficher) , 0, 'L');                      // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 12;        
        $texte_a_afficher  = "* Garanties offertes (personnelles ou/et réelles choisies en fonction des biens financés et de la situation de l'emprunteur).";
        $this->MultiCell( $largeur_volet_gauche,  4, utf8_decode($texte_a_afficher) , 0, 'L');                      // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 12;        
        //---------------------------------------------------------
        // volet droit : récap de Solution 1
        //---------------------------------------------------------
        $offset_volet_droit = 70;
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
        // Solution de financement
        $ligne_pdf = $offset_vertical_volets;                                                               // $ligne_pdf = 27
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->SetFont( 'Arial', 'B', 11 );
        $this->Cell(100,14,utf8_decode("Le ".$data[12][10]));                                               // largeur, hauteur, texte, [bordure L|T|R|B ] OK le 02/01/2020
        $ligne_pdf += 8;             
        $this->SetFont( 'Arial', '', 10 );
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->Cell( 100, 12, utf8_decode("Crédit pour un montant et une durée fixés par avance") );         // largeur, hauteur, texte, [bordure L|T|R|B ]
        //-----------------------------------------------------------------------------------------
        // Encadré : rectangle
        //-----------------------------------------------------------------------------------------
        $ligne_pdf = $this->Encadre( $offset_volet_droit, 40, $data, $euro );
        //---------------------------------------------------------
        // volet droit : détails de Solution 1
        //---------------------------------------------------------
        $offset_teg = 103;
        $offset_frais_garantie = 136;
        $this->SetFont( 'Arial', '', 8 );
        // TAUX D'INTERET
        $ligne_pdf += 10;
        $ligne_taux_interet = $ligne_pdf;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->Cell(  30,  8, utf8_decode("TAUX D'INTERET") );                                              // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur du TAUX D'INTERET
        $ligne_pdf += $this->interligne_standard;
        $taux_interet = (float) $data[8][3];                                                                // Mis à jour le 02/01/2020
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->Cell(  30,  8, number_format($taux_interet, 2, ',', ' ') . ' ' . '%' );                    // largeur, hauteur, texte, [bordure L|T|R|B ]
        // FRAIS DE DOSSIER
        $ligne_pdf += 8;
        $ligne_teg = $ligne_pdf;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->Cell(  30,  8, utf8_decode("FRAIS DE DOSSIER") );                                            // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur des FRAIS DE DOSSIER
        $ligne_pdf += $this->interligne_standard;
        $frais_dossier = (float) $data[17][3];                                                              // Mis à jour le 02/01/2020
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->Cell(  30,  8, number_format($frais_dossier, 2, ',', ' ') . ' ' . $euro );                   // largeur, hauteur, texte, [bordure L|T|R|B ]
        // ASSURANCE EMPRUNTEUR RECOMMANDEE
        /* $ligne_pdf = $ligne_taux_interet;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_teg, $ligne_pdf );             
        $this->Cell(  30,  8, utf8_decode("ASSURANCE EMPRUNTEUR RECOMMANDEE") );                            // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur de l'ASSURANCE EMPRUNTEUR RECOMMANDEE
        $ligne_pdf += $this->interligne_standard;
        $assurance = (float) $data[22][8];                                                                  // Mis à jour le 02/01/2020
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
	    $this->SetXY( $offset_teg, $ligne_pdf );             
        $this->Cell(  30,  8, number_format($assurance, 2, ',', ' ') . ' ' . $euro );                       // largeur, hauteur, texte, [bordure L|T|R|B ] */
        // TEG
        $ligne_pdf = $ligne_teg;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_teg, $ligne_pdf );             
        $this->Cell(  30,  8, utf8_decode("TEG") );                                                         // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur du TEG
        $ligne_pdf += $this->interligne_standard;
        $teg = 100 * (float) $data[12][6];                                                                  // Mis à jour le 02/01/2020
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
	    $this->SetXY( $offset_teg, $ligne_pdf );             
        $this->Cell(  30,  8, number_format($teg, 2, ',', ' ') . ' ' . '%' );                             // largeur, hauteur, texte, [bordure L|T|R|B ]
        // FRAIS DE GARANTIE
        $ligne_pdf = $ligne_teg;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_frais_garantie, $ligne_pdf );             
        $this->Cell(  30,  8, utf8_decode("FRAIS DE GARANTIE") );                                           // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Valeur des FRAIS DE GARANTIE
        $ligne_pdf += $this->interligne_standard;
        $frais_garantie = (float) $data[16][3];
        $this->SetTextColor( 4, 139, 154 );                                                                 // Couleur bleu canard
	    $this->SetXY( $offset_frais_garantie, $ligne_pdf );             
        $this->Cell(  30,  8, number_format($frais_garantie, 2, ',', ' ') . ' ' . $euro );                                                           // largeur, hauteur, texte, [bordure L|T|R|B ]
        //---------------------------------------------------------
        // volet droit : paragraphe additionnel 1
        //---------------------------------------------------------
        $ligne_pdf += 10;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->SetFont( $this->police_standard, 'I', $this->taille_police_std );
        $texte_a_afficher  = "Hors frais de garantie : L'analyse de votre dossier peut amener la Caisse Régionale à demander la";
        $texte_a_afficher .= " constitution d'une garantie. Ces montants sont estimatifs. Les montants définitifs seront indiqués dans";        
        $texte_a_afficher .= " le contrat et le tableau d'amortissement.";        
        $this->MultiCell( 130,  5, utf8_decode($texte_a_afficher), 0, 'L' );                               // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 8;

        //---------------------------------------------------------
        // volet droit : paragraphe additionnel 2
        //---------------------------------------------------------
        $ligne_pdf += 10;
        $this->SetTextColor( 175, 175, 175 );                                                               // Couleur gris
	    $this->SetXY( $offset_volet_droit, $ligne_pdf );             
        $this->SetFont( $this->police_standard, 'I', $this->taille_police_std );
        $texte_a_afficher  = "* La souscription aux produits et services présentés dans le cadre de cet outil est possible sous";
        $texte_a_afficher .= " conditions et sous réserve d'acceptation de votre dossier par un conseiller Crédit Agricole et/ou par ses";        
        $texte_a_afficher .= " partenaires. Les tarifs présentés dans cet outil seront ceux en vigueur au jour de la simulation et sont";        
        $texte_a_afficher .= " consultables sur la page tarifs.";        
        $this->MultiCell( 130,  5, utf8_decode($texte_a_afficher), 0, 'L' );                               // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 12;

        //---------------------------------------------------------
        // Paragraphe additionnel 3
        //---------------------------------------------------------
        $ligne_pdf = 240;
        $this->SetTextColor( 0, 0, 0 );                                                                     // Couleur noir
	    $this->SetXY( 11, $ligne_pdf );             
        $this->SetFont( $this->police_standard, '', $this->taille_police_std );
        $texte_a_afficher  = "Cette simulation ne constitue pas une proposition commerciale et ne saurait engager la Caisse Régionale. La souscription aux solutions";
        $texte_a_afficher .= " de financement qui vous seront présentées est réservée aux personnes agissant pour des besoins professionnels. Elle est possible sous";        
        $texte_a_afficher .= " conditions et sous réserve d'acceptation de votre dossier par la Caisse Régionale et/ou par ses partenaires, prêteur. Les principales";        
        $texte_a_afficher .= " conditions tarifaires applicables sont consultables sur notre site internet ou en agence.";        
        $this->MultiCell( 191,  5, utf8_decode($texte_a_afficher), 0, 'L' );                                // largeur, hauteur, texte, [bordure L|T|R|B ]
        $ligne_pdf += 12;
    }

    /**
     * Surcharge de la méthode pour mettre l'entete
     * colonnes : 1 à 200
     * lignes :   1 à 300 (on utilise celles du haut)
     */
    public function Header()
    {
        // Logo
        $this->Image(PUBLIC_PATH . 'imgs/cadif_logo.png',11,4,12);                                          // string, x, y, largeur

        // Nom du fichier
        $offset_infos_simu = 160;
        /*$this->SetXY(93,2);
        $this->SetTextColor(4,139,154); // Couleur bleu canard
        $this->SetFont('Arial','B',12);  //Police Arial Italic 7 */
        //$this->Cell($offset_infos_simu,12,"Raison Sociale : ".utf8_decode($this->_fichier_pdf), 'R');       // largeur, hauteur, texte, [bordure L|T|R|B ]

        // Date
        $annee = substr($this->_fichier_pdf, 18, 4);
        $mois  = substr($this->_fichier_pdf, 16, 2);
        $jour  = substr($this->_fichier_pdf, 14, 2);
        $date_du_jour = $jour . '/' . $mois . '/' . $annee;
        $this->SetTextColor(0,0,0);                                                                         // Couleur noir (format RVB)
        $this->SetXY(166,2);                                
        $this->SetFont('Arial','',10);                                                                      // Police Arial 10
        $this->Cell($offset_infos_simu,10,'le '.$date_du_jour, 'R');                                        // largeur, hauteur, texte, [bordure L|T|R|B ]

        // Durée de validité                            
        $this->SetTextColor(175,175,175);                                                                   // Couleur gris
        $this->SetXY(145,9);                           
        $this->Cell($offset_infos_simu,10,'Simulation valable 15 jours', 'R');                              // largeur, hauteur, texte, [bordure L|T|R|B ]

        // Image de document (string, x, y, largeur)
        $this->Image(PUBLIC_PATH . 'imgs/document.png',190,6,14);
    }

    /**
     * Surcharge de la méthode pour générer le pied de page
     * colonnes : 1 à 200
     * lignes :   1 à 300 (on utilise celles du bas)
     */
    public function Footer()
    {
        $offset_mentions_juridiques = 60;
        $taille_Mentions_juridiques = 4;
        $ligne_pdf = 270;

        // Ligne bleu canard épaisse
        $this->SetDrawColor(4,139,154);                                             // Couleur bleu canard
        $this->SetLineWidth(0.3);
        $this->Line(11,$ligne_pdf,200,$ligne_pdf);                                  // x1, y1, x2, y2

        // Logo
        $ligne_pdf += 1;
        $this->Image(PUBLIC_PATH . 'imgs/cadif_logo.png',11,$ligne_pdf,14);         // File, x, y, largeur

        // Sur 2 lignes : Slogan Toute une banque \r pour vous
        $ligne_pdf += 1;
        $this->SetTextColor( 0, 0, 0 );                                             // Couleur noir
        $this->SetFont( $this->police_standard, 'B', $this->taille_police_std );    // Police Arial gras
        $this->SetXY( 30, $ligne_pdf);
        $this->MultiCell( 30, $taille_Mentions_juridiques,utf8_decode("Toute une banque pour vous"),0,'L');

        // Sur 3 lignes : mentions juridiques obligatoires
        $this->SetFont( $this->police_standard,'',$this->taille_police_std);
        $this->SetXY(   $offset_mentions_juridiques, $ligne_pdf);
        $texte_a_afficher  = "Caisse régionale de Crédit Agricole Mutuel de Paris et d'Ile de France, société coopérative à capital variable, agréée en tant qu'établissement de crédit";
        $texte_a_afficher .= " - siège social situé 26 quai de la Râpée 75012 PARIS - RCS Paris - 775 665 615.";
        $texte_a_afficher .= " Société de courtage d'assurance immatriculée au Registre des Intermédiaires en Assurance sous le n° 07 008 015.";
        $ligne_pdf += (3 * $this->interligne_standard);             
        $this->MultiCell(   140, $taille_Mentions_juridiques, utf8_decode($texte_a_afficher), 0, 'L' );
        $this->SetXY(       $offset_mentions_juridiques, $ligne_pdf);
        
        //Numéro de page
        $this->SetFont( $this->police_standard, 'I', $this->taille_police_std );      // Police Arial gras
        $this->SetXY(   180, $ligne_pdf );
        $this->write(   8, 'Page ' . $this->PageNo() . '/2' );

        }

    /**
     * Sauvegarde du fichier pdf
     * @param unknown $fileName
     */
    public function save($fileName) {
        $this->Output($fileName,'F');
    }

    private function Encadre($offset_rectangle, $ligne_pdf, $data, $euro) {
        //Rectangle bleu canard
        $this->SetFillColor(4,139,154);                                                 // Couleur bleu canard
        $this->Rect($offset_rectangle,$ligne_pdf+6,130,21, 'F');                        // x, y, largeur, hauteur
        // Mensualité 
        $this->SetTextColor(255,255,255);                                               // Couleur blanc
        $this->SetFont('Arial','B',12); 
        $ligne_pdf += 3;             
	    $this->SetXY($offset_rectangle+54,$ligne_pdf);
        $ligne_pdf += $this->interligne_standard;             
        $mensualite = (float) $data[11][3];                                             // Mis à jour le 02/01/2020
        $this->Cell( 100, 15, number_format($mensualite, 2, ',', ' ') . ' ' . $euro );  // largeur, hauteur, texte, [bordure L|T|R|B ]
        $this->SetFont( $this->police_standard, '', $this->taille_police_std );
        // Valeur
        $ligne_pdf += 1;             
	    $this->SetXY( $offset_rectangle+74, $ligne_pdf );
        $this->Cell( 30, 5, utf8_decode(" /mois*") );                                   // largeur, hauteur, texte, [bordure L|T|R|B ]
        // Durée
        $ligne_pdf += $this->interligne_standard+1;             
	    $this->SetXY( $offset_rectangle+57, $ligne_pdf );
        $this->Cell( 30, 5, utf8_decode("PENDANT " . $data[9][3] . " mois") );          // largeur, hauteur, texte, [bordure L|T|R|B ] Mis à jour le 02/01/2020
        $emprunt = (float) $data[7][3];                                                 // Mis à jour le 02/01/2020
        $ligne_pdf += $this->interligne_standard;             
	    $this->SetXY( $offset_rectangle+49, $ligne_pdf );
        $this->Cell( 30, 5, utf8_decode("Montant emprunté ") .      number_format($emprunt, 2, ',', ' ') . ' ' . $euro);   // largeur, hauteur, texte, [bordure L|T|R|B ]
        $cout = (float) $data[9][6];                                                    // Mis à jour le 02/01/2020
        $ligne_pdf += $this->interligne_standard;             
	    $this->SetXY( $offset_rectangle+30, $ligne_pdf );
        $this->Cell( 30, 5, utf8_decode("Coût global du crédit hors Assurance Emprunteur ") . number_format($cout, 2, ',', ' ')    . ' ' . $euro);   // largeur, hauteur, texte, [bordure L|T|R|B ]
        return $ligne_pdf;        
    }

}


