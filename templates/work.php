<?php if (!isset($work)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php /** @var \BS\Model\Entity\Work $work */ ?>
<?php $this->layout('layout', ['title' => 'View Work', 'subTitle' => $work->getTitle()]) ?>
<?php endif ?>
<script type="text/javascript">
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    // "Update work" clicked.
    $('#work_update').click(function () {
        var $this = $(this);
        var form = $('#work_form');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/works/update',
            data: {
                'work_id': form.data('workId'),
                'work_title': $('#input_work_title').val(),
                'work_subtitle': $('#input_work_subtitle').val(),
                'work_year': $('#input_work_year').val(),
                'work_doi': $('#input_work_doi').val()
            },
            complete: function() {
                $this.button('reset');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                form.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            },
            success: function (data) {
                if (data !== true) {
                    form.find('.alert')
                        .text('Sorry, an error occurred while requesting. Please try again later.')
                        .removeClass('hidden');
                }
            }
        });
    });

    // "Add Author/Journal/DOI" clicked.
    $('.btn-add-entity').click(function () {
        var $this = $(this);
        var form = $('#work_form');
        var firstNameInput = $('#work_add_author_first_name');
        var lastNameInput = $('#work_add_author_last_name');
        var issnInput = $('#work_add_journal_issn');
        var journalNameInput = $('#work_add_journal_name');
        var doiInput = $('#work_add_doi_reference');
        var doiValue = doiInput.val().toLowerCase();
        var postData = {};
        var url, entityType;
        $this.button('loading');

        if ($this.attr('id').indexOf('author') !== -1) {
            entityType = 'author';
            url = '/works/author/add';
            postData['first_name'] = firstNameInput.val();
            postData['last_name'] = lastNameInput.val();
        } else if ($this.attr('id').indexOf('journal') !== -1) {
            entityType = 'journal';
            url = '/works/journal/add';
            postData['issn'] = issnInput.val();
            postData['journal_name'] = journalNameInput.val();
        } else if ($this.attr('id').indexOf('doi') !== -1) {
            entityType = 'doi';
            url = '/works/doi/add';
            postData['work_doi'] = doiValue;
        }

        postData['work_id'] = form.data('workId');
        $.ajax({
            type: 'POST',
            url: url,
            data: postData,
            complete: function() {
                $this.button('reset');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                form.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            },
            success: function (data) {
                var entityData = JSON.parse(data);
                if (entityType === 'author') {
                    $('#select_work_authors').append($('<option>', {
                        text : entityData['firstName'] + ' ' + entityData['lastName'],
                        class: 'confirm-delete',
                        attr: {'data-id': 'author_' + entityData['id']}
                    }).on('dblclick', function() {
                        $('#modal_delete').data('id', $(this).data('id')).modal('show');
                    }).val(entityData['id']));
                    firstNameInput.val('');
                    lastNameInput.val('');
                } else if (entityType === 'journal') {
                    $('#select_work_journals').append($('<option>', {
                        text : entityData['journalName'],
                        class: 'confirm-delete',
                        attr: {'data-id': 'journal_' + entityData['id']}
                    }).on('dblclick', function() {
                        $('#modal_delete').data('id', $(this).data('id')).modal('show');
                    }).val(entityData['id']));
                    issnInput.val('');
                    journalNameInput.val('');
                } else if (entityType === 'doi') {
                    $('#select_work_references').append($('<option>', {
                        text : doiValue,
                        class: 'confirm-delete',
                        attr: {'data-id': 'doi_' + doiValue}
                    }).on('dblclick', function() {
                        $('#modal_delete').data('id', $(this).data('id')).modal('show');
                    }).val(doiValue));
                    doiInput.val('');
                }
            }
        });
    });

    // Show confirmation modal dialog, when double-clicked on option.
    $('.confirm-delete').on('dblclick', function() {
        $('#modal_delete').data('id', $(this).data('id')).modal('show');
    });

    // Deletion confirmed.
    $('#btn_modal_delete_yes').click(function() {
        var $this = $(this);
        var modal = $('#modal_delete');
        var form = $('#work_form');
        var modalId = modal.data('id');
        var postData = {'work_id': form.data('workId')};
        var deletionId = $('[data-id="' + modalId + '"]').val();
        var url;
        $this.button('loading');

        if (modalId.indexOf('author') !== -1) {
            url = '/works/author/delete';
            postData['author_id'] = deletionId;
        } else if (modalId.indexOf('journal') !== -1) {
            url = '/works/journal/delete';
            postData['journal_id'] = deletionId;
        } else if (modalId.indexOf('doi') !== -1) {
            url = '/works/doi/delete';
            postData['work_doi'] = deletionId;
        }

        $.ajax({
            type: 'POST',
            url: url,
            data: postData,
            complete: function() {
                $this.button('reset');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                modal.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            },
            success: function (data) {
                if (data !== true) {
                    modal.find('.alert')
                        .text('Sorry, an error occurred while requesting. Please try again later.')
                        .removeClass('hidden');
                    return;
                }

                $('[data-id="' + modalId + '"]').remove();
                modal.modal('hide');
            }
        });
    });
});
</script>
<div id="modal_delete" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" data-toggle="validator">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete entry</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning hidden"></div>
                    <p>You are about to delete this entry.</p>
                    <p>Do you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="button" id="btn_modal_delete_yes" class="btn btn-danger">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<form id="work_form" data-work-id="<?=$work->getId()?>">
    <fieldset class="landscape_nomargin">
        <legend class="legend">View and update Work</legend>
        <div class="alert alert-warning hidden"></div>
        <div class="form-group">
            <label for="title">Work Title</label>
            <div class="input-group">
                <span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="Work Title"><i class="glyphicon glyphicon-book"></i></span>
                <input type="text" class="form-control" name="title" id="input_work_title" value="<?=$work->getTitle()?>" placeholder="Work Title.">
            </div>
        </div>

        <div class="form-group">
            <label for="subtitle">Work Sub-Title</label>
            <div class="input-group">
                <span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="Work Sub-Title"><i class="glyphicon glyphicon-tag"></i></span>
                <input type="text" class="form-control" name="subtitle" id="input_work_subtitle" value="<?=$work->getSubTitle()?>" placeholder="Work Sub-Title.">
            </div>
        </div>

        <div class="form-group">
            <label for="year">Work Year</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" data-toggle="tooltip" data-placement="left" title="Work Year"></i></span>
                <input type="number" class="form-control" name="year" id="input_work_year" value="<?=$work->getWorkYear()?>" placeholder="Work Year.">
            </div>
        </div>

        <div class="form-group">
            <label for="doi">Digital Object Identifier (DOI)</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-barcode" data-toggle="tooltip" data-placement="left" title="Work DOI"></i></span>
                <input type="text" class="form-control" name="doi" id="input_work_doi" value="<?=$work->getDoi()?>" placeholder="Work DOI.">
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_authors">Authors</label>
            <select multiple class="form-control" id="select_work_authors">
                <?php foreach ($work->getAuthors() as $author): ?>
                    <?php /** @var \BS\Model\Entity\Author $author */ ?>
                    <option value="<?=$author->getId()?>" data-id="author_<?=$author->getId()?>" class="confirm-delete"><?=$author?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group row">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_author_first_name" placeholder="First name">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_author_last_name" placeholder="Last name">
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_author" class="btn btn-default max-width btn-add-entity" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Adding Author...">Add Author</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_journals">Journals</label>
            <select multiple class="form-control" id="select_work_journals">
                <?php foreach ($work->getJournals() as $journal): ?>
                    <?php /** @var \BS\Model\Entity\Journal $journal */ ?>
                    <option value="<?=$journal->getId()?>" data-id="journal_<?=$journal->getId()?>" class="confirm-delete"><?=$journal->getJournalName()?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group row">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_journal_name" placeholder="Journal name">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="work_add_journal_issn" placeholder="ISSN">
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_journal" class="btn btn-default max-width btn-add-entity" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Adding Journal...">Add Journal</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_references">Referenced DOIs</label>
            <select multiple class="form-control" id="select_work_references">
                <?php foreach ($work->getWorkDois() as $referenceDoi): ?>
                    <option value="<?=$referenceDoi?>" data-id="doi_<?=$referenceDoi?>" class="confirm-delete"><?=$referenceDoi?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group row">
            <div class="col-sm-8">
                <input type="text" class="form-control" id="work_add_doi_reference" placeholder="DOI Reference">
            </div>
            <div class="col-sm-4">
                <button type="button" id="btn_work_add_doi_reference" class="btn btn-default max-width btn-add-entity" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Adding DOI Reference...">Add DOI Reference</button>
            </div>
        </div>

        <div class="form-group row form-actions">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <a href="#" onclick="window.history.back()" class="btn btn-default max-width" role="button">Back to Project</a>
            </div>
            <div class="col-sm-3">
                <button id="work_update" type="button" class="btn btn-primary max-width" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Updating Work...">Update Work</button>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </fieldset>
</form>
