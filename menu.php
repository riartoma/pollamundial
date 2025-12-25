<?php
// menu.php - Menú de navegación común
// Colócalo en la raíz del proyecto e inclúyelo con: include 'menu.php';
?>

<nav class="navbar">
    <div class="nav-container">
        <h2 class="nav-title">⚽ Polla mundialista</h2>
        <ul class="nav-links">
            <li><a href="comparacion.php" <?= basename($_SERVER['PHP_SELF']) == 'comparacion.php' ? 'class="active"' : '' ?>>👍👎 comparacion</a></li>
            <li><a href="partidos.php" <?= basename($_SERVER['PHP_SELF']) == 'partidos.php' ? 'class="active"' : '' ?>>⚽ Partidos</a></li>
            <li><a href="predicciones.php" <?= basename($_SERVER['PHP_SELF']) == 'predicciones.php' ? 'class="active"' : '' ?>>🎯 Mis Predicciones</a></li>
            <li><a href="resultados_reales.php" <?= basename($_SERVER['PHP_SELF']) == 'resultados_reales.php' ? 'class="active"' : '' ?>>📊 Resultados Reales</a></li>
            <!-- Agrega más enlaces aquí en el futuro, por ejemplo:
            <li><a href="ranking.php" <?= basename($_SERVER['PHP_SELF']) == 'ranking.php' ? 'class="active"' : '' ?>>🏆 Ranking</a></li>
            <li><a href="mis_puntos.php" <?= basename($_SERVER['PHP_SELF']) == 'mis_puntos.php' ? 'class="active"' : '' ?>>⭐ Mis Puntos</a></li>
            -->
        </ul>
    </div>
</nav>

<style>
.navbar {
    background: #0f172a;
    padding: 15px 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    margin-bottom: 40px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.nav-title {
    color: #38bdf8;
    margin: 0;
    font-size: 24px;
}

.nav-links {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.nav-links li {
    margin: 0;
}

.nav-links a {
    color: #e2e8f0;
    text-decoration: none;
    font-weight: bold;
    padding: 10px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.nav-links a:hover {
    background: #1f2937;
    color: #38bdf8;
}

.nav-links a.active {
    background: #38bdf8;
    color: #0f172a;
}

/* Responsive */
@media (max-width: 768px) {
    .nav-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .nav-links {
        justify-content: center;
    }
}
</style>