<?php if (!isset($project)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php $this->layout('layout', ['title' => 'Manage Project', 'subTitle' => $project->getName()]) ?>
<div class="table-responsive">
    <table class="table table-striped table-sorted" id="table_projects">
        <thead>
        <tr>
            <th>Work name</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($project->getWorks() as $work): ?>
            <?php /** @var \BS\Model\Entity\Work $work */ ?>
                <tr>
                    <td><a href="/works/view/<?=$work->getId()?>" class="work-link"><?=$work->getTitle()?></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif ?>
