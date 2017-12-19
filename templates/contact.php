<?php $this->layout('layout', ['title' => 'Contact Us', 'subTitle' => 'Don\'t hesitate to contact us!']) ?>
<form action="/contact" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">Contact Us</legend>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" id="name" name="name" maxlength="250" placeholder="Your Name">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input id="email" type="email" class="form-control" name="email" maxlength="250" placeholder="Your Email">
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="input-group col-sm-6">
                <label for="message">Your message:</label>
                <textarea class="form-control" rows="5" id="message" name="message" maxlength="1000"></textarea>
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group row form-actions">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">Send message</button>
            </div>
        </div>
    </fieldset>
</form>
