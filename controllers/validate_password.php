<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../helpers/PasswordValidator.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (empty($password)) {
        echo json_encode([
            'isValid' => false,
            'errors' => ['Password tidak boleh kosong'],
            'requirements' => PasswordValidator::getRequirements(),
            'checks' => [
                'uppercase' => false,
                'lowercase' => false,
                'symbol' => false,
                'minLength' => false
            ]
        ]);
        return;
    }

    $validation = PasswordValidator::validate($password);

    echo json_encode([
        'isValid' => $validation['isValid'],
        'errors' => $validation['errors'],
        'requirements' => PasswordValidator::getRequirements(),
        'checks' => [
            'uppercase' => PasswordValidator::checkRequirement($password, 'uppercase'),
            'lowercase' => PasswordValidator::checkRequirement($password, 'lowercase'),
            'symbol' => PasswordValidator::checkRequirement($password, 'symbol'),
            'minLength' => PasswordValidator::checkRequirement($password, 'minLength')
        ]
    ]);
}
