# Description des besoins (fonctionnels et non-fonctionnels)

### **Exigences fonctionnelles (Functional requirements)**

\*FR \= functional requirements (exigences fonctionnelles)						\*NFR \= non-functional requirements (exigences non-fonctionnelles)

| ID | Fonction | Exigences | Priorité |
| ----- | ----- | ----- | ----- |
| **FR-01** | **Recherche de commune**  | Rechercher une commune par son nom, avec suggestions et distinction entre départements. | **MUST** |
| **FR-02** | **Choix du réseau**  | Dressez la liste des réseaux de distribution d’eau associés à la commune sélectionnée, en incluant les quartiers desservis lorsque ces données sont disponibles. | **MUST** |
| **FR-03** | **Liste des paramètres**  | Afficher les paramètres mesurés sur un réseau, le nombre de mesures associées ainsi que les dates des mesures les plus récentes, avec une fonction de recherche par nom. | **MUST** |
| **FR-04** | **Choix de la période**  | Sélectionnez une période prédéfinie ou des dates de début et de fin personnalisées. Raccourcis (12 mois, 3 ans, tout) et dates libres  | **MUST** |
| **FR-05** | **Graphique d'évolution**  | Affichez les valeurs mesurées au fil du temps, en les situant par rapport à la limite réglementaire applicable et au seuil de qualité de référence (lignes horizontales).. | **MUST** |
| **FR-06** | **Tableau des valeurs**  | Afficher un tableau indiquant la date d’échantillonnage, la valeur publiée, l’unité et des informations sur la conformité. | **MUST** |
| **FR-07** | **Adresses partageables**  | Encodez le réseau, la commune, les paramètres et la période sélectionnés dans l’URL, puis restaurez la vue à partir de cette URL. | **MUST** |
| **FR-08** | **Export**  | Exporter les mesures affichées au format CSV. | **MUST** |
| FR-09 | Plusieurs paramètres  | Afficher plusieurs courbes, un graphique par unité ou des petits graphiques alignés sur le même axe du temps. Afficher plusieurs paramètres, en gérant les différentes unités de manière appropriée. | SHOULD |
| FR-10 | Familles de paramètres  | Regroupez les paramètres en familles lisibles : bactériologie, minéralisation, nitrates et azote, métaux, pesticides  | SHOULD |
| FR-11 | Lien E-Phy  | Associer les paramètres des pesticides à E-Phy et résumer les informations pertinentes relatives aux autorisations. Pour un pesticide : fiche E-Phy, statut d'autorisation, cultures autorisées | SHOULD |
| FR-12 | Dernier bulletin  | Afficher un résumé lisible du dernier bulletin d’échantillonnage/prélèvement ; équivalent lisible du site actuel  | SHOULD |
| FR-13 | Comparaison de réseaux  | Comparer un paramètre entre plusieurs réseaux ou communes. | COULD |
| FR-14 | Carte  | Afficher une carte, au niveau du département, colorée selon la valeur la plus récente pour un paramètre. | COULD |
| FR-15 | Alertes  | Signaler les tendances qui approchent d’un seuil réglementaire / limite | COULD |
| FR-16 | Partie connectée | \[ Possible découper en plusieurs…\] \-FR-16 Partie connectée, sauvegarde des favoris et  \-FR-17 configuration personnalisée de chaque utilisateur… etc | COULD |

### **Exigences non-fonctionnelles (Non-functional requirements)**

| ID | Exigence | Critères d’acceptation |
| ----- | ----- | ----- |
| NFR-01 | Facilité d’utilisation/ Ergonomie | Lors d’un test d’ergonomie représentatif, un utilisateur novice est en mesure de trouver un réseau et d’afficher trois années de données sur les nitrates (d'abord) en moins de 60 secondes. |
| NFR-02 | Partageabilité | L'ouverture d'une URL copiée dans une nouvelle session de navigateur permet de retrouver le même réseau, les mêmes paramètres et la même période. |
| NFR-03 | Intégrité des données | Les valeurs, dates, unités et seuils affichés correspondent aux enregistrements sources validés. |
| NFR-04 | Performance | \*\* Manque : Définir un objectif pour les suggestions de communes, le chargement des résultats, etc. puis le mesurer dans des conditions de test documentées pertinents \*\* |
| NFR-05 | Accessibilité | Les éléments de navigation principaux, les formulaires et les graphiques disposent de commandes accessibles au clavier ainsi que d’étiquettes explicites, au moins en correspondance avec l’outil WAVE (Web Accessibility Evaluation Tool) selon les WCAG (Web Content Accessibility Guideline). |
| NFR-06 | Sécurité  | Les entrées contrôlées par l’utilisateur sont validées ; les requêtes en base de données utilisent des instructions paramétrées ; les données exportées sont traitées de manière sécurisée. |
| NFR-07 | Maintenabilité  | L'accès aux données, la logique métier et la présentation sont séparés, respectant l’architecture MVC. |
| NFR-08 | Traçabilité | Chaque jeu de données affiché indique correctement sa source ainsi que, le cas échéant, la date de sa dernière mise à jour. |

*\*\* Ces exigences devraient, dans la mesure du possible, être mesurables…*

*\*\* Le critère des 60 secondes est issu du cahier des charges du projet				     
\*\* Les objectifs et critères d'acceptation pour les autres besoins non-fonctionnels restent encore à définir avec le client / intermédiaire* 
