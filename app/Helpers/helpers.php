<?php

use Illuminate\Support\Facades\Auth;

/**
 * Retourne l'utilisateur connecté
 */
function user()
{
    return Auth::user();
}

/**
 * Retourne l'ID de la pharmacie connectée
 */
function pharmacy_id()
{
    return Auth::user()?->pharmacy_id;
}

/**
 * Vérifie si utilisateur connecté
 */
function is_logged_in()
{
    return Auth::check();
}