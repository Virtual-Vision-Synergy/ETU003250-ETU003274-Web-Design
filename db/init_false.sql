-- ============================================================
-- Iran War News DB — Init Script
-- ============================================================
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS newsdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE newsdb;

-- ============================================================
-- Tables
-- ============================================================

CREATE TABLE IF NOT EXISTS categories
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(110) NOT NULL UNIQUE,
    description TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS authors
(
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    bio        TEXT,
    avatar     VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles
(
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(270) NOT NULL UNIQUE,
    summary      TEXT         NOT NULL,
    content      LONGTEXT     NOT NULL,
    image        VARCHAR(255),
    image_alt    VARCHAR(255),
    author_id    INT UNSIGNED,
    category_id  INT UNSIGNED,
    is_published TINYINT(1) DEFAULT 1,
    published_at DATETIME   DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
    INDEX        idx_slug(slug),
    INDEX        idx_category(category_id),
    INDEX        idx_published(is_published, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tags
(
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(90) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_tags
(
    article_id INT UNSIGNED NOT NULL,
    tag_id     INT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed: Categories
-- ============================================================
INSERT INTO categories (name, slug, description)
VALUES ('Politique & Diplomatie', 'politique-diplomatie',
        'Analyses des enjeux politiques et diplomatiques autour du conflit iranien.'),
       ('Opérations Militaires', 'operations-militaires', 'Suivi des opérations sur le terrain, stratégies et bilans.'),
       ('Impact Humanitaire', 'impact-humanitaire', 'Conséquences du conflit sur les populations civiles et l\'aide internationale.'),
('Économie & Sanctions',   'economie-sanctions',        'Effets économiques des sanctions et de la guerre sur la région.'),
('Géopolitique Régionale', 'geopolitique-regionale',    'Rôle des puissances régionales et mondiales dans le conflit.');

-- ============================================================
-- Seed: Authors
-- ============================================================
INSERT INTO authors (name, bio, avatar) VALUES
('Sophie Marchand',   'Correspondante de guerre basée à Beyrouth, spécialiste du Moyen-Orient depuis 15 ans.', NULL),
('Karim Al-Rashidi',  'Analyste géopolitique et ancien consultant des Nations Unies pour la région du Golfe.',  NULL),
('Elena Voronova',    'Journaliste d\'investigation spécialisée en droit international humanitaire.', NULL);

-- ============================================================
-- Seed: Tags
-- ============================================================
INSERT INTO tags (name, slug)
VALUES ('Iran', 'iran'),
       ('AIEA', 'aiea'),
       ('Sanctions', 'sanctions'),
       ('OTAN', 'otan'),
       ('Nucléaire', 'nucleaire'),
       ('Réfugiés', 'refugies'),
       ('Diplomatie', 'diplomatie'),
       ('Drones', 'drones'),
       ('Pétrole', 'petrole'),
       ('ONU', 'onu');

-- ============================================================
-- Seed: Articles
-- ============================================================
INSERT INTO articles (title, slug, summary, content, image, image_alt, author_id, category_id, is_published,
                      published_at)
VALUES ('Les négociations diplomatiques au bord de l\'impasse : vers une escalade inévitable ?',
    'negociations-diplomatiques-bord-impasse-escalade',
    'Après plusieurs rounds de négociations infructueuses à Genève, les chancelleries occidentales s\'interrogent sur la viabilité d\'un accord avec Téhéran.',
    '<p>Les discussions menées sous l\'égide des Nations Unies à Genève se sont soldées par un échec cuisant lors de leur dixième session consécutive. Les positions du gouvernement iranien et des puissances du P5+1 restent diamétralement opposées sur la question de l\'enrichissement de l\'uranium.</p>
<h2>Un contexte de méfiance persistante</h2>
<p>Depuis la sortie américaine de l\'accord de Vienne en 2018, la confiance entre Téhéran et Washington n\'a jamais été totalement restaurée. Les diplomates européens, qui jouent le rôle de médiateurs, reconnaissent en privé que la marge de manœuvre est extrêmement réduite.</p>
<h3>Les lignes rouges iraniennes</h3>
<p>Le gouvernement iranien a clairement fixé ses conditions : levée immédiate et totale des sanctions économiques avant tout engagement sur le dossier nucléaire. Cette position, jugée inacceptable par Washington, bloque tout progrès tangible.</p>
<h3>La réponse des alliés occidentaux</h3>
<p>La France, le Royaume-Uni et l\'Allemagne ont présenté une proposition dite de "séquençage progressif", prévoyant un allègement graduel des sanctions en échange de mesures vérifiables. Cette approche a été rejetée par Téhéran comme insuffisante.</p>
<h2>Les conséquences d\'un échec prolongé</h2>
<p>Les experts avertissent qu\'un blocage diplomatique prolongé pourrait précipiter une escalade militaire. Les États du Golfe,
        en première ligne, ont renforcé leurs alliances avec les États-Unis et accéléré leurs programmes de défense antimissile.</p>',
    NULL, 'Réunion diplomatique au Palais des Nations à Genève portant sur le dossier iranien', 1, 1, 1, '2026-03-10 09:00:00'
),

(
    'Frappes aériennes dans le détroit d\'Ormuz : analyse de la stratégie des forces iraniennes',
        'frappes-aeriennes-detroit-ormuz-strategie-iran',
        'Les forces armées iraniennes ont mené une série de frappes ciblées dans le golfe Persique, menaçant directement le transit de 20 % du pétrole mondial.',
        '<p>Le détroit d\'Ormuz, veine jugulaire de l\'économie pétrolière mondiale, est devenu le théâtre de tensions militaires sans précédent depuis le début du conflit ouvert. Des frappes de drones et de missiles de croisière ont perturbé plusieurs convois maritimes commerciaux.</p>
<h2>Cartographie des incidents</h2>
<p>Au cours des trente derniers jours, l\'Agence internationale de l\'énergie atomique (AIEA) et les observateurs militaires indépendants ont recensé dix-sept incidents significatifs impliquant des aéronefs non pilotés iraniens.</p>
<h3>L\'arsenal de drones iranien</h3>
<p>Les drones Shahed-136 et leur version améliorée Shahed-238 constituent désormais le fer de lance de la stratégie iranienne de déni d\'accès. Leur faible coût de production et leur efficacité opérationnelle en font une arme redoutable face aux systèmes de défense côteux.</p>
<h3>Réponse des forces de la coalition</h3>
<p>La Ve Flotte américaine, basée à Bahreïn, a déployé des destroyers équipés du système AEGIS ainsi que des escadrons supplémentaires de F/A-18 Super Hornet. Des patrouilles aériennes permanentes surveillent désormais le couloir maritime.</p>
<h2>Répercussions économiques immédiates</h2>
<p>Les compagnies d\'assurance maritime ont augmenté leurs primes de 400 % pour les passages en zone de conflit. Plusieurs armateurs majeurs ont suspendu temporairement leurs liaisons dans la région,
        provoquant une flambée du prix du baril de Brent.</p>',
    NULL, 'Vue aérienne du détroit d\'Ormuz avec navires militaires en patrouille', 2, 2, 1, '2026-03-12 11:30:00'),

       ('Crise humanitaire : deux millions de déplacés en six mois de conflit',
        'crise-humanitaire-deux-millions-deplaces-conflit-iran',
        'Le Haut Commissariat des Nations Unies pour les réfugiés alerte sur une situation humanitaire critique dans les provinces frontalières iraniennes.',
        '<p>Le conflit a provoqué l\'un des déplacements de populations les plus importants de la région depuis des décennies. Selon les dernières données du HCR,
        plus de deux millions de personnes ont fui leurs foyers depuis le début des hostilités,
        principalement dans les provinces du Khuzestan, du Kurdistan et du Kermanshah.</p>
<h2>Conditions de vie précaires dans les camps</h2>
<p>Les camps de réfugiés établis à la hâte dans les pays voisins — Irak, Turquie et Pakistan — peinent à absorber ces flux migratoires massifs. Les organisations humanitaires dénoncent un manque criant d\'eau potable, de nourriture et de soins médicaux.</p>
<h3>Le drame des populations civiles urbaines</h3>
<p>Dans les grandes villes comme Ahvaz et Sanandaj, les infrastructures civiles — hôpitaux, réseaux d\'eau et d\'électricité — ont été partiellement détruites par les combats. Médecins Sans Frontières signale une recrudescence de maladies infectieuses dans les zones touchées.</p>
<h3>L\'aide internationale insuffisante</h3>
<p>L\'appel humanitaire consolidé des Nations Unies, d\'un montant de 3, 2 milliards de dollars, n\'est financé qu\'à 38 %. Les donateurs traditionnels invoquent des contraintes budgétaires pour justifier leur retrait partiel.</p>
<h2>Témoignages de survivants</h2>
<p>Fatima, 43 ans,
        originaire de Khorramshahr : "En une nuit, nous avons tout perdu. Ma maison, mes souvenirs, mon quartier. Nous marchons depuis cinq jours." Ces récits illustrent la dimension humaine d\'un conflit trop souvent résumé à ses aspects géopolitiques.</p>',
        NULL, 'Camp de déplacés dans la région frontalière irano-irakienne avec tentes et familles', 3, 3, 1,
        '2026-03-14 08:15:00'),

       ('Sanctions économiques : l\'étranglement progressif de l\'économie iranienne',
        'sanctions-economiques-etranglement-economie-iranienne',
        'Les nouvelles vagues de sanctions imposées par Washington et Bruxelles ciblent désormais le système bancaire et les exportations pétrolières iraniennes avec une précision chirurgicale.',
        '<p>Le régime de sanctions contre l\'Iran a atteint un niveau d\'intensité inédit. Les mesures adoptées par le département du Trésor américain en janvier 2026 visent directement les actifs de la Banque centrale iranienne et les réseaux d\'exportation de pétrole brut,
        jusqu\'ici partiellement préservés.</p>
<h2>Indicateurs macroéconomiques en chute libre</h2>
<p>Le rial iranien a perdu 67 % de sa valeur depuis le début du conflit. L\'inflation annuelle a atteint 85 %, selon les estimations du Centre des Statistiques d\'Iran, rendant les produits de première nécessité inaccessibles pour une large partie de la population.</p>
<h3>Impact sur le secteur pétrolier</h3>
<p>La production pétrolière iranienne, qui atteignait 3,8 millions de barils par jour en 2023, est tombée à 1,2 million. Les principaux acheteurs traditionnels — Chine, Inde et Corée du Sud — ont drastiquement réduit leurs importations sous la pression américaine.</p>
<h3>Contournement des sanctions et marché noir</h3>
<p>L\'Iran a développé un réseau sophistiqué de contournement utilisant des "navires fantômes" battant pavillon de complaisance et des transactions en cryptomonnaies. Les services de renseignement occidentaux évaluent les pertes induites par ces mécanismes à environ 30 % du manque à gagner théorique.</p>
<h2>Conséquences sociales internes</h2>
<p>La classe moyenne iranienne, qui avait émergé dans les années 2000,
        est aujourd\'hui particulièrement affectée. Le chômage a bondi à 28 % et les universités constatent une fuite des cerveaux sans précédent vers les pays d\'Asie centrale et d\'Europe.</p>',
        NULL,
        'Bourse et indicateurs économiques illustrant l\'effondrement du rial iranien', 1, 4, 1, '2026-03-15 14:00:00'
),

(
    'Le rôle ambigu de la Russie et de la Chine dans le conflit iranien',
    'role-ambigu-russie-chine-conflit-iranien',
    'Moscou et Pékin multiplient les déclarations de soutien à Téhéran tout en maintenant des canaux de communication discrets avec les puissances occidentales.',
    '<p>Le conflit iranien met en lumière les contradictions profondes de la politique étrangère russe et chinoise. Ces deux membres permanents du Conseil de Sécurité de l\'ONU opposent systématiquement leur veto aux résolutions les plus contraignantes, tout en cherchant à préserver leurs intérêts économiques dans la région.</p>
<h2>L\'axe Moscou-Téhéran : une alliance de circonstance</h2>
<p>La Russie, qui a livré des systèmes S-400 à l\'Iran avant le début du conflit, maintient officiellement une position de "neutralité active". En réalité, le Kremlin profite de la diversion iranienne pour réduire la pression internationale sur ses propres aventures militaires.</p>
<h3>Les intérêts commerciaux russes en jeu</h3>
<p>Moscou exporte annuellement pour plus de 4 milliards de dollars de bien vers l\'Iran,
        incluant des équipements industriels et des composants technologiques à double usage. Ces échanges se poursuivent malgré les sanctions occidentales,
        via des itinéraires détournés.</p>
<h3>La Chine entre pragmatisme et solidarité idéologique</h3>
<p>Pékin a augmenté ses importations de pétrole iranien de 35 % depuis le début du conflit,
        profitant des prix bradés pratiqués par Téhéran. Simultanément, des diplomates chinois conduisent des discussions discrètes avec Washington pour éviter une escalade qui perturberait les routes maritimes vitales pour les exportations chinoises.</p>
<h2>L\'ONU paralysée</h2>
<p>Le Conseil de Sécurité a rejeté six résolutions depuis le début du conflit, chacune bloquée par un veto russe ou chinois. Cette paralysie institutionnelle contraint les acteurs occidentaux à agir en dehors du cadre onusien, ce qui fragilise davantage le droit international.</p>',
        NULL, 'Réunion du Conseil de Sécurité des Nations Unies sur le dossier iranien', 2, 5, 1,
        '2026-03-17 10:45:00'),

       ('Programme nucléaire iranien : l\'AIEA tire la sonnette d\'alarme',
        'programme-nucleaire-iranien-aiea-alerte',
        'L\'Agence Internationale de l\'Énergie Atomique révèle que l\'Iran a enrichi suffisamment d\'uranium pour constituer une capacité nucléaire militaire potentielle.',
        '<p>Le rapport de l\'AIEA publié le 16 mars 2026 constitue une alerte sans précédent dans l\'histoire de l\'agence onusienne. Pour la première fois,
        les inspecteurs estiment que l\'Iran dispose d\'une quantité suffisante d\'uranium enrichi à 90 % pour fabriquer potentiellement deux dispositifs nucléaires.</p>
<h2>Les chiffres qui inquiètent</h2>
<p>Selon le rapport, les stocks d\'uranium enrichi à haute teneur ont atteint 1 247 kilogrammes, soit une augmentation de 340 % par rapport aux niveaux enregistrés lors du précédent rapport semestriel. Les centrifugeuses avancées IR-6 et IR-8 fonctionnent à pleine capacité dans les installations de Natanz et de Fordow.</p>
<h3>Accès refusé aux inspecteurs</h3>
<p>L\'Iran a suspendu l\'accès aux inspecteurs de l\'AIEA dans trois sites non déclarés depuis le déclenchement du conflit. Cette opacité alimente les pires craintes des experts en non-prolifération quant aux activités qui s\'y déroulent.</p>
<h3>La doctrine de l\'ambiguïté nucléaire iranienne</h3>
<p>Téhéran maintient une position délibérément ambiguë : ni confirmer ni démentir la dimension militaire de son programme. Cette stratégie d\'ambiguïté calculée vise à maximiser le pouvoir de dissuasion tout en évitant une réaction militaire internationale.</p>
<h2>Les options militaires sur la table</h2>
<p>Face à cette menace,
        Israël et les États-Unis ont intensifié leurs consultations militaires. Des sources proches du Pentagone évoquent la mise à jour de plans opérationnels pour des frappes chirurgicales contre les sites nucléaires iraniens,
        similaires à l\'opération "Opera" conduite par Israël en 1981 contre le réacteur irakien Osirak.</p>',
        NULL, 'Centrifugeuses d\'enrichissement d\'uranium dans une installation nucléaire iranienne sous surveillance',
        3, 1, 1, '2026-03-18 07:30:00'),

       ('Les Gardiens de la Révolution : structure et stratégie de la force d\'élite iranienne',
    'gardiens-revolution-structure-strategie-force-elite',
    'Pilier incontournable du dispositif militaire iranien, les Corps des Gardiens de la Révolution islamique orchestrent la réponse militaire et le réseau de mandataires régionaux.',
    '<p>Le Corps des Gardiens de la Révolution islamique (CGRI, ou Pasdaran en persan) joue un rôle central dans la conduite des opérations militaires iraniennes. Cette force paramilitaire d\'élite, forte de 150 000 hommes, dispose de ses propres forces aériennes, navales et terrestres, indépendantes de l\'armée régulière.</p>
<h2>Organisation et chaîne de commandement</h2>
<p>Le CGRI est organisé en brigades spécialisées dont la redoutable Force Qods, chargée des opérations extérieures. C\'est cette unité qui coordonne le soutien aux mandataires régionaux — Hezbollah au Liban, factions armées en Irak, Houthis au Yémen et Hamas en Palestine.</p>
<h3>Capacités en matière de drones et missiles</h3>
<p>L\'investissement massif du CGRI dans les technologies de drones et de missiles balistiques a transformé la doctrine militaire iranienne. Les missiles Fattah-2,
        hypersoniques, représentent une menace crédible contre les systèmes de défense antimissile actuels, avec une portée déclarée de 1 400 kilomètres.</p>
<h3>Le réseau de mandataires comme multiplicateur de force</h3>
<p>La stratégie de "résistance" du CGRI repose sur l\'activation coordonnée de ses mandataires pour ouvrir plusieurs fronts simultanément. Cette approche place l\'adversaire face à des menaces dispersées,
        difficiles à contrer avec des moyens conventionnels.</p>
<h2>Financement et économie de guerre</h2>
<p>Le CGRI contrôle un empire économique estimé à 20 % du PIB iranien, couvrant les télécommunications,
        la construction et le pétrole. Ces ressources lui permettent de maintenir son effort de guerre indépendamment des contraintes budgétaires de l\'État.</p>',
        NULL, 'Parade militaire des Gardiens de la Révolution islamique à Téhéran', 1, 2, 1, '2026-03-20 09:00:00'),

       ('Médias et propagande : la guerre de l\'information autour du conflit iranien',
    'medias-propagande-guerre-information-conflit-iranien',
    'Des deux côtés du conflit, la guerre narrative est aussi intense que les opérations sur le terrain. Analyse des instruments de propagande déployés.',
    '<p>Le conflit iranien illustre de manière exemplaire la centralité de la guerre informationnelle dans les conflits modernes. Chaque acteur — Iran,
        États - Unis, Israël, puissances européennes — déploie des instruments sophistiqués pour façonner la perception internationale des événements.</p>
<h2>L\'arsenal médiatique iranien</h2>
<p>La chaîne Press TV, bien que bloquée dans de nombreux pays, continue de diffuser sur internet avec des audiences significatives en Amérique latine et en Afrique. Les Gardiens de la Révolution ont également développé des capacités avancées de cyberguerre et de désinformation sur les réseaux sociaux.</p>
<h3>Les narratives occidentales</h3>
<p>Les médias occidentaux, pour leur part, couvrent abondamment les violations des droits de l\'homme en Iran et les ambitions nucléaires iraniennes,
        tout en minimisant systématiquement les conséquences des sanctions sur la population civile, dénonce l\'ONG Reporters Sans Frontières.</p>
<h3>Le rôle amplificateur des réseaux sociaux</h3>
<p>X (anciennement Twitter), Telegram et TikTok jouent un rôle sans précédent dans la diffusion d\'informations — et de désinformations — concernant le conflit. Des études récentes révèlent que 68 % des vidéos virales relatives au conflit contiennent des éléments trompeurs ou hors contexte.</p>
<h2>Journalistes sous pression</h2>
<p>Le Comité de Protection des Journalistes recense 23 journalistes emprisonnés en Iran depuis le début du conflit,
        et 8 reporters étrangers tués dans des zones de combat. La liberté de la presse,
        pilier essentiel d\'une couverture équilibrée, subit une pression extrême.</p>',
        NULL, 'Journalistes en gilets pare-balles couvrant le conflit depuis une zone sécurisée', 3, 1, 1,
        '2026-03-22 11:00:00'),

       ('Les États du Golfe face au conflit iranien : entre prudence et repositionnement stratégique',
        'etats-golfe-conflit-iranien-prudence-repositionnement',
        'Les monarchies du Golfe, longtemps fer de lance de la résistance à l\'Iran,
        adoptent des positions plus nuancées face à l\'escalade du conflit.',
        '<p>La guerre a profondément redistribué les cartes dans la péninsule arabique. L\'Arabie saoudite,
        les Émirats arabes unis et le Qatar, traditionnellement alignés sur les positions américaines face à Téhéran,
        développent désormais des postures diplomatiques plus autonomes et pragmatiques.</p>
<h2>L\'Arabie Saoudite entre deux eaux</h2>
<p>Riyad, qui avait renoué des relations diplomatiques avec Téhéran en mars 2023 sous médiation chinoise, se retrouve dans une position délicate. Le royaume craint une déstabilisation régionale incontrôlable tout autant qu\'une victoire iranienne qui renforcerait l\'influence chiite dans la région.</p>
<h3>Les Émirats et le pragmatisme économique</h3>
<p>Dubaï continue d\'agir comme une plaque tournante commerciale, y compris pour des marchandises à destination de l\'Iran en contournement partiel des sanctions. Cette position est tolérée par Washington qui juge la stabilité des Émirats essentielle à ses opérations dans la région.</p>
<h3>Le Qatar, médiateur inattendu</h3>
<p>Doha, qui entretient des relations avec pratiquement tous les acteurs du conflit, s\'est positionnée comme médiateur discret. Des pourparlers indirects ont été facilités par le Qatar entre les représentants de Téhéran et des émissaires américains au cours des dernières semaines.</p>
<h2>Le risque d\'une régionalisation du conflit</h2>
<p>Les experts de la sécurité régionale alertent sur le risque d\'une extension du conflit à l\'ensemble du Moyen-Orient. Un incident majeur impliquant des infrastructures pétrolières saoudiennes pourrait entraîner une escalade aux conséquences imprévisibles pour l\'économie mondiale.</p>',
    NULL, 'Skyline de Dubaï représentant le dynamisme économique des États du Golfe', 2, 5, 1, '2026-03-24 13:30:00'
),

(
    'Jeunesse iranienne et résistance intérieure : une société en ébullition',
    'jeunesse-iranienne-resistance-interieure-societe-ebullition',
    'Malgré la répression, les mouvements de contestation populaire persistent en Iran, alimentés par une jeunesse urbaine connectée et une crise économique profonde.',
    '<p>Le conflit a paradoxalement renforcé certains mouvements de contestation populaire au sein même de l\'Iran. La jeunesse urbaine, héritière du mouvement "Femme, Vie, Liberté" de 2022, maintient une résistance silencieuse mais tenace face au régime des Gardiens de la Révolution.</p>
<h2>Mouvements de protestation sous surveillance</h2>
<p>Les manifestations de rue, rendues impossibles par l\'état d\'urgence et le déploiement massif des forces de l\'ordre,
        ont migré vers une résistance plus diffuse : désobéissance civile, satire en ligne et réseaux d\'entraide clandestins.</p>
<h3>Le rôle du VPN et de l\'internet libre</h3>
<p>Malgré les tentatives du gouvernement de bloquer l\'accès à internet, plus de 73 % des Iraniens de moins de 35 ans utilisent des VPN pour accéder aux réseaux sociaux étrangers, selon une étude de NetBlocks. L\'information circule,
        difficile à endiguer totalement.</p>
<h3>Les artistes et intellectuels en exil</h3>
<p>Une nouvelle vague d\'exil frappe les cercles intellectuels et artistiques iraniens. Des cinéastes, romanciers et universitaires de renom ont quitté le pays pour Paris, Berlin, Toronto et Los Angeles, emportant avec eux un patrimoine culturel vivant qui témoigne de l\'Iran profond.</p>
<h2>Une génération entre espoir et désespoir</h2>
<p>Navid, 24 ans, étudiant en génie informatique à Ispahan, résume le sentiment de sa génération : "Nous n\'avons pas choisi cette guerre. Nous voulons étudier, travailler, voyager, aimer. Mais chaque jour, le régime et les bombes réduisent notre horizon." Ce sentiment de génération sacrifiée traverse les frontières sociales et géographiques.</p>',
    NULL, 'Jeunes Iraniens rassemblés dans une rue de Téhéran lors d\'une manifestation silencieuse', 3, 3, 1,
        '2026-03-26 08:00:00'),

       ('Bilan après six mois : points de rupture et perspectives de paix',
        'bilan-six-mois-points-rupture-perspectives-paix',
        'Six mois après le début du conflit ouvert, dressons un bilan des pertes, des évolutions diplomatiques et des scénarios possibles pour la résolution du conflit.',
        '<p>Six mois se sont écoulés depuis que le conflit a basculé dans une phase ouverte. Le moment est venu de dresser un bilan lucide et d\'examiner les scénarios envisageables pour une sortie de crise.</p>
<h2>Bilan humain et matériel</h2>
<p>Les pertes militaires cumulées des différentes parties dépassent 12 000 combattants,
        selon les estimations croisées de plusieurs instituts de recherche stratégique. Les victimes civiles sont difficiles à chiffrer avec précision,
        mais le Comité International de la Croix-Rouge estime à plus de 45 000 le nombre de civils tués ou grièvement blessés.</p>
<h3>Destruction des infrastructures</h3>
<p>Les dommages aux infrastructures iranniennes — réseaux énergétiques, systèmes de distribution d\'eau, hôpitaux — sont évalués à 180 milliards de dollars par la Banque mondiale. La reconstruction prendra des décennies et nécessitera un accompagnement international massif.</p>
<h3>Impact économique régional</h3>
<p>Le prix du baril de Brent oscille entre 130 et 160 dollars, alimentant l\'inflation en Europe et en Asie. Les économies importatrices de pétrole enregistrent des récessions techniques,
        élargissant le conflit à une dimension économique mondiale.</p>
<h2>Scénarios de sortie de crise</h2>
<p>Les experts identifient trois scénarios principaux : un accord négocié sous pression internationale incluant un moratoire nucléaire iranien en échange d\'une levée partielle des sanctions ; une escalade jusqu\'à un conflit régional généralisé;
ou un épuisement progressif des belligérants conduisant à un cessez-le-feu fragile. Le deuxième scénario, le moins souhaitables, reste malheureusement le plus probable à court terme selon la majorité des analystes consultés.</p>',
    NULL, 'Ruines d\'un quartier touché par les combats dans une ville iranienne avec équipes de secours', 2, 1, 1, '2026-03-28 10:00:00'
);

-- ============================================================
-- Seed: Article Tags
-- ============================================================
INSERT INTO article_tags (article_id, tag_id)
VALUES (1, 1),
       (1, 7),
       (1, 10),
       (2, 1),
       (2, 8),
       (2, 9),
       (3, 1),
       (3, 6),
       (3, 10),
       (4, 1),
       (4, 3),
       (4, 9),
       (5, 1),
       (5, 7),
       (5, 10),
       (6, 1),
       (6, 2),
       (6, 5),
       (7, 1),
       (7, 4),
       (7, 8),
       (8, 1),
       (8, 7),
       (9, 1),
       (9, 7),
       (9, 9),
       (10, 1),
       (10, 6),
       (11, 1),
       (11, 7),
       (11, 10);