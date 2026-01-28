<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingreso de Partidos</title>

<!-- CSS EMBEBIDO -->
<style>
* {
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}
body {
    background: #f4f6f8;
    padding: 40px;
}
.card {
    max-width: 500px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
}
label {
    font-weight: bold;
}
input, button {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
}
button {
    background: #222;
    color: white;
    border: none;
}
button:hover {
    background: #000;
}
</style>

</head>
<body>

<div class="card">
    <h2>Registrar Partido</h2>

    <form>
        <label>Equipo Local</label>
        <input type="text" placeholder="Colombia">

        <label>Bandera Local (co, ar, br)</label>
        <input type="text" placeholder="co">

        <label>Equipo Visitante</label>
        <input type="text" placeholder="Brasil">

        <label>Bandera Visitante</label>
        <input type="text" placeholder="br">

        <label>Fecha</label>
        <input type="date">

        <label>Hora</label>
        <input type="time">

        <button type="submit">Guardar</button>
    </form>
</div>

</body>
</html>
