<?php if (!isset($messageType)): $messageType = 'info'; endif ?>
<div class="col-sm-12">
    <div class="alert alert-<?=$this->e($messageType)?>">
        <?=$this->e($message)?>
    </div>
</div>
