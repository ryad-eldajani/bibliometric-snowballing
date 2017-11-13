<?php if (!isset($messageType)): $messageType = 'info'; endif ?>
<div class="col-sm-12">
    <div class="alert alert-<?=$messageType?>">
        <?=$message?>
    </div>
</div>
