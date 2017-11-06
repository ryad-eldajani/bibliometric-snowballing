<?php $this->layout('layout', ['title' => 'Login', 'subTitle' => 'Welcome to Bibliometric Snowballing, please login.']) ?>

<div class="col-sm-12">
    <form action="/login" method="post">
        <fieldset class="landscape_nomargin">
            <legend class="legend">Login</legend>
            <div class="form-group row">
                <label for="username" class="col-sm-2">Username:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username">
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-sm-2">Password:</label>
                <div class="col-sm-10">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password">
                </div>
            </div>
            <div class="form-group row form-actions">
                <div class="col-sm-12">
                    <button id="login" type="submit" class="btn btn-primary">Login</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
