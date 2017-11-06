<!doctype html>
<html lang="en">
<head>
    <title>Bibliometric Snowballing | <?=$this->e($title)?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/style.css">
    <link rel="shortcut icon" type="image/png" href="/static/gfx/favicon.png"/>
    <script src="/static/js/jquery-3.2.1.min.js"></script>
    <script src="/static/js/popper.min.js"></script>
    <script src="/static/js/bootstrap.min.js"></script>
    <script src="/static/js/source-code-pro.js"></script>
</head>
<body>
<?=$this->fetch('partials/navbar')?>
<?=$this->fetch('partials/header', ['title' => $title, 'subTitle' => isset($subTitle) ? $subTitle : null])?>
<?php if (isset($message)): ?>
    <?php $this->insert('partials/message') ?>
<?php endif ?>
<?=$this->section('content')?>
<?=$this->fetch('partials/footer')?>
</body>
</html>