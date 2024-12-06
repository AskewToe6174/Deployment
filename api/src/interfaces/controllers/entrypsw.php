<?php
$password = "91aae394151417289465f00aab34deba2af924c78bcef8c8bf594122a17bef6c";

// Generar el hash de la contraseña usando bcrypt
$hash = password_hash($password, PASSWORD_BCRYPT);

// Mostrar el hash
echo "Hash: " . $hash;