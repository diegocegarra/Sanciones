<!-- AQUI VAN A IR LAS CONFIGURACIONES QUE EL USUARIO PUEDE HACER EN EL PERFIL -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    
    <!-- eliminar o agregar fechas festivas -->
     <div class= 'container' >
        <label >fechas festivas</label>
            <div id="tag-container" class="col"> </div>
        <input type="date" id="tag-input" placeholder="">
    </div>
    
    <table id="miTabla">
    <thead>
        <tr><th>Columna 1</th><th>Columna 2</th></tr>
    </thead>
    <tbody>
        <tr id="fila-a-ocultar">
            <td>Dato 1</td>
        </tr>
        <tr>
            <td>Dato 3</td>
            <td>Dato 4</td>
        </tr>
    </tbody>
</table>

      <script
        src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>

      <script type="module" src="../js/evaluar_trabajadores.js"></script>

      


</body>
</html>