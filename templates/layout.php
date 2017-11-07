<!doctype html>
<html lang="en">
<head>
    <title>Bibliometric Snowballing | <?=$this->e($title)?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/png" href="/static/gfx/favicon.png">
    <link rel="stylesheet" type="text/css" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/style.css">

    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/popper.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/datatables.min.js"></script>
    <script type="text/javascript" src="/static/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/script.js"></script>
</head>
<body>
<?=$this->fetch('parts/navbar')?>
<?=$this->fetch('parts/header', ['title' => $title, 'subTitle' => isset($subTitle) ? $subTitle : null])?>
<?php if (isset($message)): ?>
    <?php $this->insert('parts/message') ?>
<?php endif ?>
<div class="container">
    <div class="row">
        <?=$this->section('content')?>
    </div>
</div>
<?=$this->fetch('parts/footer')?>
</body>
</html>