<?php if (!isset($work)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php /** @var \BS\Model\Entity\Work $work */ ?>
<?php $this->layout('layout', ['title' => 'View Work', 'subTitle' => $work->getTitle()]) ?>
<?php endif ?>
<script type="text/javascript">
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<form action="/works/update/<?php $work->getId() ?>" method="post">
    <fieldset class="landscape_nomargin">
        <legend class="legend">View and update Work</legend>

        <div class="form-group">
            <label for="title">Work Title</label>
            <div class="input-group">
                <span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="Work Title"><i class="glyphicon glyphicon-book"></i></span>
                <input type="text" class="form-control" name="title" id="title" value="<?=$work->getTitle()?>" placeholder="Work Title.">
            </div>
        </div>

        <div class="form-group">
            <label for="subtitle">Work Sub-Title</label>
            <div class="input-group">
                <span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="Work Sub-Title"><i class="glyphicon glyphicon-tag"></i></span>
                <input type="text" class="form-control" name="subtitle" id="subtitle" value="<?=$work->getSubTitle()?>" placeholder="Work Sub-Title.">
            </div>
        </div>

        <div class="form-group">
            <label for="year">Work Year</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" data-toggle="tooltip" data-placement="left" title="Work Year"></i></span>
                <input type="text" class="form-control" name="year" id="year" value="<?=$work->getWorkYear()?>" placeholder="Work Year.">
            </div>
        </div>

        <div class="form-group">
            <label for="doi">Digital Object Identifier (DOI)</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-barcode" data-toggle="tooltip" data-placement="left" title="Work DOI"></i></span>
                <input type="text" class="form-control" name="doi" id="doi" value="<?=$work->getDoi()?>" placeholder="Work DOI.">
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_authors">Authors</label>
            <select multiple class="form-control" id="select_work_authors"></select>
        </div>
        <div class="form-group row">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_author_first_name" placeholder="First name">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_author_last_name" placeholder="Last name">
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_author" class="btn btn-default max-width">Add Author</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_journals">Journals</label>
            <select multiple class="form-control" id="select_work_journals"></select>
        </div>

        <div class="form-group row">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_journal_name" placeholder="Journal name">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_journal_issn" placeholder="ISSN">
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_journal" class="btn btn-default max-width">Add Journal</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_references">Referenced DOIs</label>
            <select multiple class="form-control" id="select_work_references"></select>
        </div>

        <div class="form-group row">
            <div class="col-sm-8">
                <input type="text" class="form-control" id="work_add_doi_reference" placeholder="DOI Reference">
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_doi_reference" class="btn btn-default max-width">Add DOI Reference</button>
            </div>
        </div>

        <div class="form-group row form-actions">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <a href="#" onclick="window.history.back()" class="btn btn-default max-width" role="button">Back to Project</a>
            </div>
            <div class="col-sm-3">
                <button id="work_update" type="submit" class="btn btn-primary max-width">Update Work</button>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
</form>
