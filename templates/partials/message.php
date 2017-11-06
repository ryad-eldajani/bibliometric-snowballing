<?php if (!isset($type)): $type = 'info'; endif ?>
<div class="alert alert-<?=$this->e($type)?>">
    <?=$this->e($message)?>
</div>