<?php
// Conexión (igual que antes)
$conn = new mysqli('localhost', 'root', '', 'worldcup_predictions');
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

// Grupos y equipos (igual)
$groups = [
    'A' => ['Mexico', 'South Africa', 'Korea Republic', 'DEN/MKD/CZE/IRL'],
    'B' => ['Canada', 'ITA/NIR/WAL/BIH', 'Qatar', 'Switzerland'],
    'C' => ['Brazil', 'Morocco', 'Haiti', 'Scotland'],
    'D' => ['USA', 'Paraguay', 'Australia', 'TUR/ROU/SVK/KOS'],
    'E' => ['Germany', 'Curaçao', 'Côte d\'Ivoire', 'Ecuador'],
    'F' => ['Netherlands', 'Japan', 'UKR/SWE/POL/ALB', 'Tunisia'],
    'G' => ['Belgium', 'Egypt', 'IR Iran', 'New Zealand'],
    'H' => ['Spain', 'Cabo Verde', 'Saudi Arabia', 'Uruguay'],
    'I' => ['France', 'Senegal', 'BOL/SUR/IRQ', 'Norway'],
    'J' => ['Argentina', 'Algeria', 'Austria', 'Jordan'],
    'K' => ['Portugal', 'NCL/JAM/COD', 'Uzbekistan', 'Colombia'],
    'L' => ['England', 'Croatia', 'Ghana', 'Panama']
];

// Generar partidos de grupos (IDs 1-72)
$group_matches = [];
$match_id = 1;
foreach ($groups as $gl => $teams) {
    $pairs = [
        [0,1], [0,2], [0,3],
        [1,2], [1,3],
        [2,3]
    ];
    foreach ($pairs as $p) {
        $t1 = $teams[$p[0]];
        $t2 = $teams[$p[1]];
        $group_matches[$match_id] = ['group' => $gl, 'team1' => $t1, 'team2' => $t2];
        $match_id++;
    }
}

// Todos los equipos para knockout
$all_teams = array_unique(array_merge(...array_values($groups)));
sort($all_teams);

// Knockout matches (igual que antes, IDs 73-104)
$knockout_matches = [
    73 => 'A 2° vs B 2°', 74 => 'E 1° vs 3° (A/B/C/D/F)', /* ... acorta descripciones para compactar ... */
    // (mantén la lista completa como en tu versión anterior, pero aquí la abrevio por espacio)
    // ... copia el array completo de knockout_matches de tu código anterior ...
    104 => 'Final'
];

// Procesar POST (igual que antes, solo copiar tu lógica actual de guardado)

// Cargar datos (igual: $standings, $group_preds, $preds)

// Cerrar conexión al final
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predicciones Mundial 2026</title>
    <!-- <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 20px; line-height: 1.5; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 10px; }
        details { margin: 12px 0; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        summary { padding: 12px 16px; background: #eff6ff; cursor: pointer; font-weight: bold; color: #1e40af; }
        .section-content { padding: 16px; }
        .group-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .group { background: #f9fafb; padding: 12px; border-radius: 6px; }
        .group h3 { margin: 0 0 10px; color: #1e40af; font-size: 1.1rem; }
        .match-row { display: flex; align-items: center; justify-content: space-between; margin: 8px 0; flex-wrap: wrap; gap: 8px; }
        .match-row span { font-weight: bold; min-width: 40px; text-align: center; }
        .team-name { min-width: 100px; font-size: 0.95rem; }
        .score-input { width: 50px; text-align: center; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; }
        .standings { display: flex; flex-wrap: wrap; gap: 12px; }
        .standing-item { flex: 1 1 120px; }
        .standing-item label { display: block; font-size: 0.9rem; margin-bottom: 4px; }
        .standing-item select { width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.95rem; }
        .knockout-section h3 { margin: 16px 0 8px; color: #1e40af; }
        .match { display: flex; align-items: center; gap: 8px; margin: 10px 0; flex-wrap: wrap; }
        .match select { flex: 1 1 140px; padding: 6px; }
        .match input { width: 60px; text-align: center; }
        .match span { font-weight: bold; }
        .save-btn { position: sticky; bottom: 20px; left: 50%; transform: translateX(-50%); background: #1e40af; color: white; border: none; padding: 12px 32px; border-radius: 50px; font-size: 1.1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 10; }
        .save-btn:hover { background: #1e3a8a; }
        @media (max-width: 600px) { .match-row { flex-direction: column; align-items: flex-start; } }
    </style> -->
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to bottom, #f0f4f8, #e2e8f0);
        margin: 0;
        padding: 20px 10px;
        color: #1f2937;
        line-height: 1.6;
    }

    h1 {
        color: #1d4ed8;
        text-align: center;
        margin: 0 0 24px;
        font-size: 2.2rem;
        font-weight: 700;
    }

    details {
        margin: 16px 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    summary {
        padding: 16px 20px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
        font-size: 1.15rem;
        cursor: pointer;
        user-select: none;
        border-bottom: 1px solid #e5e7eb;
    }

    summary:hover {
        background: #dbeafe;
    }

    .section-content {
        padding: 20px;
    }

    .group-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }

    .group {
        background: #f8fafc;
        padding: 16px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .group h3 {
        margin: 0 0 12px;
        color: #1e40af;
        font-size: 1.25rem;
        border-bottom: 2px solid #bfdbfe;
        padding-bottom: 6px;
    }

    .match-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        gap: 10px;
        flex-wrap: wrap;
    }

    .match-row:last-child {
        border-bottom: none;
    }

    .team-name {
        flex: 1 1 140px;
        font-weight: 500;
        font-size: 0.98rem;
    }

    .score-input {
        width: 60px;
        text-align: center;
        padding: 8px 6px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 1rem;
        background: white;
    }

    .score-input:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    .dash {
        font-weight: bold;
        color: #64748b;
        min-width: 20px;
        text-align: center;
    }

    .standings {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px 16px;
    }

    .standing-item label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: #475569;
    }

    .standing-item select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: white;
        font-size: 0.95rem;
    }

    .standing-item select:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    .knockout-section h3 {
        color: #1e40af;
        margin: 24px 0 12px;
        font-size: 1.3rem;
        border-left: 5px solid #bfdbfe;
        padding-left: 12px;
    }

    .match {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 12px 0;
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }

    .match span.desc {
        flex: 1 1 220px;
        font-weight: 500;
        color: #334155;
    }

    .match select {
        flex: 1 1 160px;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        min-width: 140px;
    }

    .match input[type="number"] {
        width: 65px;
        text-align: center;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }

    .match .vs {
        font-weight: bold;
        color: #64748b;
        min-width: 30px;
        text-align: center;
    }

    .save-btn {
        position: sticky;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: #1d4ed8;
        color: white;
        border: none;
        padding: 14px 40px;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 6px 16px rgba(29,78,216,0.3);
        z-index: 100;
        transition: all 0.2s;
    }

    .save-btn:hover {
        background: #1e40af;
        transform: translateX(-50%) translateY(-2px);
        box-shadow: 0 10px 20px rgba(29,78,216,0.4);
    }

    @media (max-width: 768px) {
        .group-grid { grid-template-columns: 1fr; }
        .match { flex-direction: column; align-items: stretch; }
        .match select, .match input { width: 100%; }
        .match .vs { order: 2; margin: 8px 0; }
        h1 { font-size: 1.8rem; }
    }

    @media (max-width: 480px) {
        .section-content { padding: 16px; }
        .save-btn { width: 90%; padding: 14px; }
    }
</style>
</head>
<body>

<h1>Predicciones Mundial 2026</h1>

<form method="post">

    <details open>
        <summary>1. Resultados Fase de Grupos (Partidos)</summary>
        <div class="section-content">
            <div class="group-grid">
                <?php foreach ($groups as $gl => $teams): ?>
                    <div class="group">
                        <h3>Grupo <?= $gl ?></h3>
                        <?php
                        $g_matches = array_filter($group_matches, fn($m) => $m['group'] === $gl);
                        foreach ($g_matches as $mid => $m):
                        ?>
                            <div class="match-row">
                                <div class="team-name"><?= htmlspecialchars($m['team1']) ?></div>
                                <input type="number" min="0" class="score-input" name="groupmatch<?= $mid ?>_score1" value="<?= $group_preds[$mid]['score1'] ?? '' ?>">
                                <span>-</span>
                                <input type="number" min="0" class="score-input" name="groupmatch<?= $mid ?>_score2" value="<?= $group_preds[$mid]['score2'] ?? '' ?>">
                                <div class="team-name"><?= htmlspecialchars($m['team2']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </details>

    <details>
        <summary>2. Posiciones Finales Grupos</summary>
        <div class="section-content">
            <div class="group-grid">
                <?php foreach ($groups as $gl => $teams): ?>
                    <div class="group">
                        <h3>Grupo <?= $gl ?></h3>
                        <div class="standings">
                            <?php foreach (['first'=>'1°', 'second'=>'2°', 'third'=>'3°', 'fourth'=>'4°'] as $pos => $label): ?>
                                <div class="standing-item">
                                    <label><?= $label ?></label>
                                    <select name="group<?= $gl ?>_<?= $pos ?>">
                                        <?php foreach ($teams as $t): ?>
                                            <option value="<?= htmlspecialchars($t) ?>" <?= ($standings[$gl][$pos] ?? '') === $t ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($t) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </details>

    <details>
        <summary>3. Fases Eliminatorias</summary>
        <div class="section-content">
            <?php
            $stages = [
                'Dieciseisavos (73-88)' => range(73,88),
                'Octavos (89-96)' => range(89,96),
                'Cuartos (97-100)' => range(97,100),
                'Semis (101-102)' => range(101,102),
                'Tercer lugar (103)' => [103],
                'Final (104)' => [104]
            ];
            foreach ($stages as $stageName => $ids):
            ?>
                <h3><?= $stageName ?></h3>
                <?php foreach ($ids as $mid):
                    $desc = $knockout_matches[$mid] ?? "Partido $mid";
                ?>
                    <div class="match">
                        <span><?= $desc ?></span>
                        <select name="match<?= $mid ?>_team1">
                            <option value="">—</option>
                            <?php foreach ($all_teams as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>" <?= ($preds[$mid]['team1'] ?? '') === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" min="0" name="match<?= $mid ?>_score1" value="<?= $preds[$mid]['score1'] ?? '' ?>">
                        <span>-</span>
                        <input type="number" min="0" name="match<?= $mid ?>_score2" value="<?= $preds[$mid]['score2'] ?? '' ?>">
                        <select name="match<?= $mid ?>_team2">
                            <option value="">—</option>
                            <?php foreach ($all_teams as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>" <?= ($preds[$mid]['team2'] ?? '') === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </details>

    <button type="submit" class="save-btn">Guardar Predicciones</button>

</form>

</body>
</html>