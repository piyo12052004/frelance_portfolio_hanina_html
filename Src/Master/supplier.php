<?php

$title = "Supplier";
$activeMenu = "supplier";

require_once "../../Helper/function_supplier.php";
require_once __DIR__ . '/../Layout/header.php';
?>

<div>

    

</div>

<template id="modal-master-created-katagori">

    <form method="POST" x-data="{ form: {...$store.modal.data} }">
        
    </form>

</template>

<?php
require_once __DIR__ . '/../Layout/footer.php';
?>