<?php $this->layout('layout', ['title' => 'Contact Us', 'subTitle' => 'Don\'t hesitate to contact us!']) ?>
<div class=”container”>
<div class="form-group">
  <label for="usr">Your name:</label>
  <input type="text" class="form-control" id="usr">
</div>

<div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
    <input id="email" type="text" class="form-control" name="email" placeholder="Email">
  </div>

<div class="form-group">
  <label for="comment">Your message:</label>
  <textarea class="form-control" rows="5" id="message"></textarea>
</div>
<button type="button" class="btn btn-success">Send message</button>
</div>
