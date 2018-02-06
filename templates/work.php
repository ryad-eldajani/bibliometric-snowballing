<?php if (!isset($work)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php /** @var \BS\Model\Entity\Work $work */ ?>
<?php $this->layout('layout', ['title' => $this->isAdmin() ? 'Update Work' : 'View Work', 'subTitle' => $work->getTitle()]) ?>
<?php endif ?>
<script type="text/javascript">
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?php if ($this->isAdmin()): ?>
<form action="/works/update/<?php $work->getId() ?>" method="post">
<?php endif ?>
    <fieldset class="landscape_nomargin">
        <legend class="legend"><?php if ($this->isAdmin()): ?>Update Work<?php else: ?>View Work<?php endif ?></legend>

        <div class="form-group">
            <label for="title">Work Title</label>
            <div class="input-group">
                <span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="Work Title"><i class="glyphicon glyphicon-book"></i></span>
                <input type="text" class="form-control" name="title" id="title" value="<?=$work->getTitle()?>" placeholder="Work Title."<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="subtitle">Work Sub-Title</label>
            <div class="input-group">
                <span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="Work Sub-Title"><i class="glyphicon glyphicon-tag"></i></span>
                <input type="text" class="form-control" name="subtitle" id="subtitle" value="<?=$work->getSubTitle()?>" placeholder="Work Sub-Title."<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="year">Work Year</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" data-toggle="tooltip" data-placement="left" title="Work Year"></i></span>
                <input type="text" class="form-control" name="year" id="year" value="<?=$work->getWorkYear()?>" placeholder="Work Year."<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="doi">Digital Object Identifier (DOI)</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-barcode" data-toggle="tooltip" data-placement="left" title="Work DOI"></i></span>
                <input type="text" class="form-control" name="doi" id="doi" value="<?=$work->getDoi()?>" placeholder="Work DOI."<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_authors">Authors</label>
            <select multiple class="form-control" id="select_work_authors"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>></select>
        </div>
        <div class="form-group row">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_author_first_name" placeholder="First name"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_author_last_name" placeholder="Last name"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_author" class="btn btn-default max-width"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>Add Author</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_journals">Journals</label>
            <select multiple class="form-control" id="select_work_journals"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>></select>
        </div>

        <div class="form-group row">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_journal_name" placeholder="Journal name"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_journal_issn" placeholder="ISSN"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_journal" class="btn btn-default max-width"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>Add Journal</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_references">Referenced DOIs</label>
            <select multiple class="form-control" id="select_work_references"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>></select>
        </div>

        <div class="form-group row">
            <div class="col-sm-8">
                <input type="text" class="form-control" id="work_add_doi_reference" placeholder="DOI Reference"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_doi_reference" class="btn btn-default max-width"<?php if (!$this->isAdmin()): ?> disabled<?php endif ?>>Add DOI Reference</button>
            </div>
        </div>

        <div class="form-group row form-actions">
            <div class="col-sm-3"></div>
            <div class="<?php if ($this->isAdmin()): ?>col-sm-3<?php else: ?>col-sm-6<?php endif ?>">
                <a href="#" onclick="window.history.back()" class="btn btn-default max-width" role="button">Back to Project</a>
            </div>
            <?php if ($this->isAdmin()): ?>
                <div class="col-sm-3">
                    <button id="work_update" type="submit" class="btn btn-primary max-width">Update Work</button>
                </div>
            <?php endif ?>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
<?php if ($this->isAdmin()): ?>
</form>
<?php endif ?>