<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="keywords" content="Login,reset password,signup" /> 
    <meta name="author" content="Dennis Otieno; Email: denonyango@gmail.com">
    <meta name="base-url" content="<?= base_url(); ?>">

    <link rel="shortcut icon" href="<?= base_url("mission.jpg") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        input:focus {
            outline-offset: 2px;
            outline: 2px solid #27aae0 !important;
        }
    </style>

</head>
<body class="bg-light relative">
    <div class="radial-pattern-container">
        <div class="radial-pattern"></div>
    </div>