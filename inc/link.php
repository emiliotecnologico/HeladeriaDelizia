<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSS -->
<link rel="stylesheet" href="./css/bootstrap.min.css">
<link rel="stylesheet" href="./css/font-awesome.min.css">
<link rel="stylesheet" href="./css/normalize.css">
<link rel="stylesheet" href="./css/bootstrap-material-design.min.css">
<link rel="stylesheet" href="./css/ripples.min.css">
<link rel="stylesheet" href="./css/sweetalert.css">
<link rel="stylesheet" href="./css/media.css">
<link rel="stylesheet" href="./css/style.css">

<!-- JAVASCRIPT - ORDEN CRÍTICO -->
<script src="./js/jquery.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script src="./js/material.min.js"></script>
<script src="./js/ripples.min.js"></script>
<script src="./js/autohidingnavbar.min.js"></script>
<script src="./js/sweetalert.min.js"></script>
<script src="./js/jquery.dataTables.min.js"></script>
<script src="./js/main.js"></script>

<script>
    $(document).ready(function(){
        $.material.init();
        console.log('Librerías inicializadas - Bootstrap modal:', typeof $.fn.modal);
    });
</script>