<?php $this->layout('layout', ['title' => 'Password reset', 'subTitle' => 'Reset your password.']) ?>

<form action="/password_reset" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">Password reset</legend>
        <div class="form-group row">
            <div class="col-sm-12 text-center">
                If you have forgotten your password, please enter your username and email address. We will send you a new random password.
                Please update your password after the procedure as soon as possible.
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
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row form-actions">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <a href="/login" class="btn btn-default max-width" role="button">Back to login</a>
            </div>
            <div class="col-sm-3">
                <button id="password_reset" type="submit" class="btn btn-primary max-width">Reset password</button>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
</form>
