<?php $this->layout('layout', ['title' => 'My Profile', 'subTitle' => 'Please manage your profile.']) ?>
<script type="text/javascript">
$(document).ready(function () {
    $('#current_password').on('keyup', function () {
        $('#fieldset_profile').prop('disabled', $(this).val().length <= 0);
        $('#fieldset_password').prop('disabled', $(this).val().length <= 0);
    })
});
</script>
<form action="/profile" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">User Profile</legend>
        <fieldset class="landscape_nomargin profile">
            <legend class="legend">Current Password</legend>
            <div class="form-group row">
                <div class="col-sm-12 text-center">
                    For safety reasons, please enter your current password, to make changes to your profile.
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="input-group col-sm-6">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Enter your current password *">
                </div>
                <div class="col-sm-3"></div>
            </div>
        </fieldset>
        <fieldset class="landscape_nomargin profile" disabled id="fieldset_profile">
            <legend class="legend">Profile</legend>
            <div class="form-group row">
                <div class="col-sm-12 text-center">
                    You can change your profile settings here.
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="input-group col-sm-6">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control" value="<?=$this->userParam('email')?>" placeholder="Enter your Email address *">
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="input-group col-sm-6">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                    <input type="text" name="country" id="country" class="form-control" value="<?=$this->userParam('country')?>" placeholder="Enter your Country *">
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="input-group col-sm-6">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-education"></i></span>
                    <input type="text" name="university" id="university" class="form-control" value="<?=$this->userParam('company')?>" placeholder="Enter your University">
                </div>
                <div class="col-sm-3"></div>
            </div>
        </fieldset>
        <fieldset class="landscape_nomargin profile" disabled id="fieldset_password">
            <legend class="legend">Change Password</legend>
            <div class="form-group row">
                <div class="col-sm-12 text-center">
                    If you wish to change your password, please enter your new password.
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="input-group col-sm-6">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter your new password *">
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="input-group col-sm-6">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="password" name="new_password_confirm" id="new_password_confirm" class="form-control" placeholder="Enter your new password again *">
                </div>
                <div class="col-sm-3"></div>
            </div>

        </fieldset>
        <div class="form-group row form-actions">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <button id="register" type="submit" class="btn btn-primary max-width">Save</button>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
</form>
