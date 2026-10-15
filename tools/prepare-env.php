<?php

// Utilitário independente: não inicializa Laravel nem acessa banco.
$root = dirname(__DIR__);
$target = $root.'/.env';

if (file_exists($target)) {
    fwrite(STDOUT, ".env existente preservado.\n");
    exit(0);
}

$contents = file_get_contents($root.'/.env.example');

if ($contents === false) {
    fwrite(STDERR, "Não foi possível ler .env.example.\n");
    exit(1);
}

$contents = str_replace("APP_KEY=\n", 'APP_KEY=base64:'.base64_encode(random_bytes(32))."\n", $contents);
$contents = str_replace("DB_PASSWORD=\n", 'DB_PASSWORD='.bin2hex(random_bytes(24))."\n", $contents);
$contents = str_replace("MYSQL_ROOT_PASSWORD=\n", 'MYSQL_ROOT_PASSWORD='.bin2hex(random_bytes(24))."\n", $contents);

if (function_exists('posix_getuid') && function_exists('posix_getgid')) {
    $contents = str_replace('LOCAL_UID=1000', 'LOCAL_UID='.posix_getuid(), $contents);
    $contents = str_replace('LOCAL_GID=1000', 'LOCAL_GID='.posix_getgid(), $contents);
}

umask(0077);
$file = fopen($target, 'x');

if ($file === false) {
    fwrite(STDERR, "Não foi possível criar .env sem sobrescrever arquivos.\n");
    exit(1);
}

$written = fwrite($file, $contents);
fclose($file);

if ($written !== strlen($contents)) {
    unlink($target);
    fwrite(STDERR, "Falha ao gravar .env.\n");
    exit(1);
}

fwrite(STDOUT, ".env criado com chave e senhas aleatórias; valores não exibidos.\n");
