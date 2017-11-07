<?php $this->layout('layout', ['title' => 'Login', 'subTitle' => 'Welcome to Bibliometric Snowballing, please login.']) ?>

<form action="/login" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">Login</legend>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row form-actions">
            <div class="col-sm-12">
                <button id="login" type="submit" class="btn btn-primary">Login</button>
            </div>
        </div>
    </fieldset>
</form>
