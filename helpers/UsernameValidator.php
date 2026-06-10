<?php

class UsernameValidator
{
    /**
     * Validate username format
     * @param string $username
     * @return array [isValid => bool, errors => array of error messages]
     */
    public static function validate($username)
    {
        $errors = [];

        if (empty($username)) {
            $errors[] = "Username tidak boleh kosong";
        }

        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
            $errors[] = "Username tidak boleh mengandung simbol (hanya huruf, angka, titik, underscore, dan strip yang diizinkan)";
        }

        if (strlen($username) < 3) {
            $errors[] = "Username minimal 3 karakter";
        }

        if (strlen($username) > 50) {
            $errors[] = "Username maksimal 50 karakter";
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Check if username contains symbols
     */
    public static function hasSymbols($username)
    {
        return !preg_match('/^[a-zA-Z0-9._-]+$/', $username);
    }

    /**
     * Check if username length is valid
     */
    public static function isValidLength($username)
    {
        return strlen($username) >= 3 && strlen($username) <= 50;
    }
}
