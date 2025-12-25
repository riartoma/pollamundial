<?php
$paginas = [
    [
        "titulo" => "Clasificaciones de Grupos",
        "archivo" => "clasificaciones_grupos.php",
        "descripcion" => "Visualiza las tablas de posiciones de cada grupo."
    ],
    [
        "titulo" => "Comparación",
        "archivo" => "comparacion.php",
        "descripcion" => "Comparación estadística entre equipos."
    ],
    [
        "titulo" => "Configuración",
        "archivo" => "config.php",
        "descripcion" => "Parámetros generales del sistema."
    ],
    [
        "titulo" => "Fase Eliminatoria",
        "archivo" => "fase_eliminatoria.php",
        "descripcion" => "Llaves y cruces de la fase final."
    ],
    [
        "titulo" => "Generar Partidos",
        "archivo" => "generar_partidos.php",
        "descripcion" => "Generación automática de partidos."
    ],
    [
        "titulo" => "Generar Resultados",
        "archivo" => "generar_resultados_simulados.php",
        "descripcion" => "Simulación de resultados deportivos."
    ],
    [
        "titulo" => "Listado de Partidos",
        "archivo" => "partidos.php",
        "descripcion" => "Consulta general de partidos."
    ],
    [
        "titulo" => "Predicciones",
        "archivo" => "predicciones.php",
        "descripcion" => "Predicciones basadas en estadísticas."
    ],
    [
        "titulo" => "Resultados Reales",
        "archivo" => "resultados_reales.php",
        "descripcion" => "Resultados oficiales de los encuentros."
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Navegación</title>
    <style>
        body {
            font-family: Calibri, Arial, sans-serif;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            margin: 0;
            padding: 40px;
            color: #fff;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .contenedor {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .card {
            background: #1f2937;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,.3);
            transition: transform .2s, box-shadow .2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,.5);
        }

        .card h2 {
            margin-top: 0;
            font-size: 20px;
            color: #38bdf8;
        }

        .card p {
            font-size: 14px;
            color: #cbd5f5;
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 16px;
            background: #38bdf8;
            color: #0f172a;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background .2s;
        }

        .card a:hover {
            background: #0ea5e9;
        }

        footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<h1>📊 Panel de Gestión de Partidos</h1>

<div class="contenedor">
    <?php foreach ($paginas as $pagina): ?>
        <div class="card">
            <h2><?= $pagina["titulo"] ?></h2>
            <p><?= $pagina["descripcion"] ?></p>
            <a href="<?= $pagina["archivo"] ?>">Ir al módulo</a>
        </div>
    <?php endforeach; ?>
</div>

<footer>
    © <?= date("Y") ?> — Sistema PHP de Gestión Deportiva
</footer>

</body>
</html>
