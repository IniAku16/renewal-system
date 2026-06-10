<?php

class PasswordValidator
{
    private static $minLength = 8;

    /**
     * Validate password strength
     * @param string $password
     * @return array [isValid => bool, errors => array of error messages]
     */
    public static function validate($password)
    {
        $errors = [];

        if (strlen($password) < self::$minLength) {
            $errors[] = "Password minimal " . self::$minLength . " karakter";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password harus memiliki huruf besar (A-Z)";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password harus memiliki huruf kecil (a-z)";
        }

        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/ ]/', $password)) {
            $errors[] = "Password harus memiliki simbol (!@#$%^&* dll)";
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get password requirements for frontend display
     */
    public static function getRequirements()
    {
        return [
            'minLength' => self::$minLength,
            'requiresUppercase' => true,
            'requiresLowercase' => true,
            'requiresSymbol' => true
        ];
    }

    /**
     * Check if password meets specific requirement
     */
    public static function checkRequirement($password, $requirement)
    {
        switch ($requirement) {
            case 'uppercase':
                return preg_match('/[A-Z]/', $password);
            case 'lowercase':
                return preg_match('/[a-z]/', $password);
            case 'symbol':
                return preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/ ]/', $password);
            case 'minLength':
                return strlen($password) >= self::$minLength;
            default:
                return false;
        }
    }
}
