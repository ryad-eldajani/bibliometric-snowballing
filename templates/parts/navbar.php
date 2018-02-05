<nav class="navbar navbar-default navbar-fixed-top">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">Bibliometric Snowballing</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <?php if (isset($user)): ?>
            <li class="dropdown<?=$this->active('/', true)?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Projects <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/">Manage</a></li>
                    <?php if (isset($projects) && count($projects) > 0): ?>
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-header">Available projects</li>
                    <?php foreach ($projects as $project): ?>
                    <?php /** @var \BS\Model\Entity\Project $project */ ?>
                    <li><a href="/projects/view/<?=$project->getId()?>"><?=$project->getName()?></a></li>
                    <?php endforeach ?>
                    <?php endif ?>
                </ul>
            </li>
            <?php endif ?>
            <li<?=$this->active('/about')?>><a href="/about">About</a></li>
            <li<?=$this->active('/contact')?>><a href="/contact">Contact</a></li>
        </ul>
        <?php if (isset($user)): ?>
        <ul class="nav navbar-nav navbar-right navbar-user">
            <li class="dropdown<?=$this->active('/profile', true)?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    Logged in as:
                    <?php if ($this->isAdmin()): ?>
                        <span class="glyphicon glyphicon-star"></span>
                    <?php else: ?>
                        <span class="glyphicon glyphicon-user"></span>
                    <?php endif ?>
                    <?=$this->e($user['username'])?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php if (!$this->isAdmin()): ?>
                        <li><a href="/profile">Manage profile</a></li>
                        <li role="separator" class="divider"></li>
                    <?php endif ?>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </li>
        </ul>
        <?php endif ?>
    </div><!-- /.navbar-collapse -->
</nav>