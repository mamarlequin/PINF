<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=reserver");
	die("");
}

$machines = [
    ['id' => 1, 'nom' => 'IMPRIMANTE 3D 1', 'enMaintenance' => 0],
    ['id' => 2, 'nom' => 'IMPRIMANTE 3D 2', 'enMaintenance' => 0],
    ['id' => 3, 'nom' => 'DECOUPE LASER 1', 'enMaintenance' => 0],
    ['id' => 4, 'nom' => 'DECOUPE LASER 2', 'enMaintenance' => 1], // Hachuré (maintenance)
    ['id' => 5, 'nom' => 'IMPRIMANTE RESINE 1', 'enMaintenance' => 0],
    ['id' => 6, 'nom' => 'IMPRIMANTE RESINE 2', 'enMaintenance' => 1], // Hachuré (maintenance)
];

$reservations = [
    ['id' => 101, 'idEquipement' => 1, 'dateDebut' => '2025-12-01 10:30:00', 'dateFin' => '2025-12-01 12:30:00'],
    ['id' => 102, 'idEquipement' => 1, 'dateDebut' => '2025-12-03 10:30:00', 'dateFin' => '2025-12-03 12:30:00'],
    ['id' => 103, 'idEquipement' => 2, 'dateDebut' => '2025-12-02 10:30:00', 'dateFin' => '2025-12-02 12:30:00'],
    ['id' => 104, 'idEquipement' => 2, 'dateDebut' => '2025-12-02 15:00:00', 'dateFin' => '2025-12-02 16:30:00'],
    ['id' => 105, 'idEquipement' => 3, 'dateDebut' => '2025-12-02 10:30:00', 'dateFin' => '2025-12-02 12:30:00'],
    ['id' => 106, 'idEquipement' => 5, 'dateDebut' => '2025-12-01 10:30:00', 'dateFin' => '2025-12-01 12:30:00'],
];

$creneaux = [
    ['id' => 1, 'idAdmin' => 1, 'dateDebut' => '2026-02-02 09:00:00', 'dateFin' => '2026-02-02 12:00:00'],
    ['id' => 2, 'idAdmin' => 1, 'dateDebut' => '2026-02-02 13:30:00', 'dateFin' => '2026-02-02 18:00:00'],
    ['id' => 3, 'idAdmin' => 2, 'dateDebut' => '2026-02-03 09:00:00', 'dateFin' => '2026-02-03 18:00:00'],
    ['id' => 4, 'idAdmin' => 1, 'dateDebut' => '2026-02-04 14:00:00', 'dateFin' => '2026-02-04 20:00:00'],
    ['id' => 5, 'idAdmin' => 2, 'dateDebut' => '2026-02-05 08:30:00', 'dateFin' => '2026-02-05 17:30:00'],
    ['id' => 6, 'idAdmin' => 1, 'dateDebut' => '2026-02-06 09:00:00', 'dateFin' => '2026-02-06 16:00:00'],
];

$planningAdmin = [];
foreach ($creneaux as $c) {
    $jour = date('Y-m-d', strtotime($c['dateDebut']));
    $planningAdmin[$jour][] = [
        'debut' => date('H:i', strtotime($c['dateDebut'])),
        'fin'   => date('H:i', strtotime($c['dateFin'])),
        'idAdmin' => $c['idAdmin']
    ];
}

$planning = [];
foreach ($reservations as $res) {
    $jour = date('Y-m-d', strtotime($res['dateDebut']));
    $planning[$res['idEquipement']][$jour][] = $res;





$offset = isset($_GET['week']) ? (int)$_GET['week'] : 0;

$dateCurseur = new DateTime('monday this week');
if ($offset !== 0) {
    $dateCurseur->modify("$offset weeks");
}

}
?>

<div class="max-w-full bg-white border border-gray-400 rounded-lg shadow-sm overflow-hidden font-sans select-none">
    <div class="flex border-b border-gray-400 text-xs font-bold text-gray-500 uppercase">

        <div class="w-56 p-4 border-r border-gray-400 flex items-center justify-between">
            <a href="index.php?view=reserver&week=<?= $offset - 1 ?>" class="hover:bg-gray-100 p-1 px-2 rounded border border-gray-400 transition-colors">
            <
            </a>
            <span class="text-gray-700"><?php echo $dateCurseur->format('M Y'); ?></span>
            <a href="index.php?view=reserver&week=<?= $offset + 1 ?>" class="hover:bg-gray-100 p-1 px-2 rounded border border-gray-400 transition-colors">
            >
            </a>
        </div>

        <?php
        $noms = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'];

        $dateJour = clone $dateCurseur;
        foreach ($noms as $i => $nom): ?>
            <div class="flex-1 p-4 text-center border-r border-gray-400 last:border-r-0">
                <?= $nom ?> 
                <?= $dateJour->format('d') 
                ?>

                <?php
                $cle = $dateJour->format('Y-m-d'); 
                ?>

                <div class="mt-2 flex flex-col gap-1">
                    <?php if (isset($planningAdmin[$cle])): ?>
                        <?php foreach ($planningAdmin[$cle] as $c): ?>
                            <span class="text-[9px] bg-green-100 text-green-700 px-1 py-0.5 rounded border border-green-200">
                                Admin <?= $c['idAdmin'] ?>: <?= $c['debut'] ?>-<?= $c['fin'] ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-[9px] text-red-400 italic">Pas d'admin</span>
                    <?php endif; ?>
                </div>
                <?php $dateJour->modify('+1 day'); ?>

            </div>
        <?php endforeach; ?>
    </div>

    <?php foreach ($machines as $m): ?>
    <div class="flex border-b border-gray-400 h-auto min-h-[60px]">
        
        <div class="w-56 p-4 border-r border-gray-400 flex items-center font-bold text-gray-700 text-xs uppercase ">
            <?= $m['nom'] ?>
        </div>
        <?php
        $dateStock = (clone $dateCurseur);
        for($i=0; $i<6; $i++): 
            
            $date = $dateStock->format('Y-m-d');
            $dateStock->modify("+1 day");
            if($m['enMaintenance'] == 1){
                $bg = "bg-stripes";
            }elseif(!isset($planningAdmin[$date])){
                $bg = "bg-gray-200";
            }else{
                $bg = '';
            }
            
            ?>
            <div class="flex-1 p-2 border-r border-gray-400 flex flex-col gap-2 <?= $bg ?>">
                <?php 
                if (isset($planning[$m['id']][$date])): 
                    foreach ($planning[$m['id']][$date] as $res): 
                        $heure = date('H:i', strtotime($res['dateDebut'])) . '-' . date('H:i', strtotime($res['dateFin']));
                        $color = ($m['id'] % 2 == 0) ? 'bg-blue-400' : 'bg-purple-500';
                        ?>
                        <div class="<?= $color ?> text-white text-[11px] py-3 px-2 rounded-lg shadow-sm font-medium cursor-pointer hover:opacity-90 text-center">
                            <?= $heure ?>
                        </div>
                        <?php 
                    endforeach; 
                endif; 
                ?>
            </div>
        <?php endfor; ?>
    </div>
<?php endforeach; ?>
</div>