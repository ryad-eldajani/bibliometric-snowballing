<?php $this->layout('layout', ['title' => 'Registration', 'subTitle' => 'Welcome to Bibliometric Snowballing, please register.']) ?>

<form action="/register" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">Registration</legend>
        <div class="form-group row">
            <div class="col-sm-12 text-center">
                Welcome to the Bibliometric Snowballing registration. Please choose a password between 6 and 30 characters. Fields with an asterisk (*) are required.
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" name="username" id="username" value="<?=$this->postParam('username')?>" placeholder="Enter your username *">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password *">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Enter your password again *">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control" value="<?=$this->postParam('email')?>" placeholder="Enter your Email address *">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                <input type="text" name="country" id="country" class="form-control" value="<?=$this->postParam('country')?>" placeholder="Enter your Country *">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-education"></i></span>
                <input type="text" name="university" id="university" class="form-control" value="<?=$this->postParam('university')?>" placeholder="Enter your University">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row form-actions">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <a href="/login" class="btn btn-default max-width" role="button">Back to login</a>
            </div>
            <div class="col-sm-3">
                <button id="register" type="submit" class="btn btn-primary max-width">Register</button>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
</form>
