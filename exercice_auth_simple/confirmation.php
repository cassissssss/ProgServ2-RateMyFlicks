<?php

require_once('./config/autoload.php');
use functionnalities\DbManagerCRUD;

$isAccountActivated = false;

if(filter_has_var(INPUT_GET,"token")){
    $token = filter_input(INPUT_GET,"token",FILTER_DEFAULT);

    $db = new DbManagerCRUD();
    $id = $db->rendPersonneIdToken($token);

    if($id>0){
        $db->changePersonneToken($id,"");
    }
    header("Location:./connexion.php?".($id>0?"activated=true":""));
    exit();
}