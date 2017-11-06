<?php $this->layout('layout', ['title' => 'Exception']) ?>
<div class="container exception alert alert-danger" role="alert">
    <div class="row">
        <div class="col-sm-12 exception-message">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            Error: <?=$this->e($exception->getMessage())?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 exception-stacktrace">
            <?=$this->e($exception->getTraceAsString())?>
        </div>
    </div>
</div>