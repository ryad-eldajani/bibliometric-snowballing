<?php $this->layout('layout', ['title' => 'Login', 'subTitle' => 'Welcome to Bibliometric Snowballing, please login.']) ?>

<form action="/login" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">Login</legend>
        <div class="form-group row">
            <div class="col-sm-12 text-center">
                Welcome to the Bibliometric Snowballing login. If you don't have an account yet, <a href="/register">please register</a>.
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" name="username" id="username" value="<?=$this->postParam('username')?>" placeholder="Enter your username">
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
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <a href="/register" class="btn btn-default max-width" role="button">Register</a>
            </div>
            <div class="col-sm-3">
                <button id="login" type="submit" class="btn btn-primary max-width">Login</button>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
</form>
