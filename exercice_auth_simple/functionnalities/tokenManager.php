<?php

namespace functionnalities;

/**
 * Permet de gérer la création de Token
 */
class TokenManager {
    /**
     * Retourne une chaîne de 16 caractères hexadécimaux
     * @return string
     */
    public static function generateToken():string{
        //$rand = random_int(1,2);
        //return (string)$rand;
        return bin2hex(random_bytes(8));
    }


}