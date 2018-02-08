<?php if (!isset($work)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php /** @var \BS\Model\Entity\Work $work */ ?>
<?php $this->layout('layout', ['title' => 'View Work', 'subTitle' => $work->getTitle()]) ?>
<?php endif ?>
<script type="text/javascript">
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    // Add Journal.
    $('#btn_work_add_journal').click(function() {
        var journalName = $('#work_add_journal_name');
        var journalIssn = $('#work_add_journal_issn');
        $('#select_work_journals').append($('<option>', {
            text : journalName.val(),
            class: 'confirm-delete',
            attr: {
                'data-issn': journalIssn.val(),
                'data-id': 'journal_' + journalIssn.val()
            }
        }).on('dblclick', function() {
            $('#modal_delete').data('id', $(this).data('id')).modal('show');
        }));
        journalName.val('');
        journalIssn.val('');
    });

    // Add Author.
    $('#btn_work_add_author').click(function() {
        var authorFirstName = $('#work_add_author_first_name');
        var authorLastName = $('#work_add_author_last_name');
        $('#select_work_authors').append($('<option>', {
            text : authorFirstName.val() + ' ' + authorLastName.val(),
            class: 'confirm-delete',
            attr: {
                'data-firstname': authorFirstName.val(),
                'data-lastname': authorLastName.val(),
                'data-id': 'author_' + authorFirstName.val() + authorLastName.val()
            }
        }).on('dblclick', function() {
            $('#modal_delete').data('id', $(this).data('id')).modal('show');
        }));
        authorFirstName.val('');
        authorLastName.val('');
    });

    // Add DOI.
    $('#btn_work_add_doi_reference').click(function() {
        var doi = $('#work_add_doi_reference');
        $('#select_work_references').append($('<option>', {
            text : doi.val(),
            class: 'confirm-delete',
            attr: {
                'data-doi': doi.val(),
                'data-id': 'doi_' + doi.val()
            }
        }).on('dblclick', function() {
            $('#modal_delete').data('id', $(this).data('id')).modal('show');
        }));
        doi.val('');
    });

    // Doubleclick on option.
    $('.confirm-delete').on('dblclick', function() {
        $('#modal_delete').data('id', $(this).data('id')).modal('show');
    });

    // Deletion confirmed.
    $('#btn_modal_delete_yes').click(function() {
        var modal = $('#modal_delete');
        var id = modal.data('id');
        $('[data-id="' + id + '"]').remove();
        modal.modal('hide');
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
                    <p>You are about to delete this entry.</p>
                    <p>Do you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="submit" id="btn_modal_delete_yes" class="btn btn-danger">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
            <select multiple class="form-control" id="select_work_authors">
                <?php foreach ($work->getAuthors() as $author): ?>
                    <?php /** @var \BS\Model\Entity\Author $author */ ?>
                    <option value="<?=$author->getId()?>" data-first-name="<?=$author->getFirstName()?>" data-last-name="<?=$author->getLastName()?>" data-id="journal_<?=$author->getId()?>" class="confirm-delete"><?=$author?></option>
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
                <button type="button" id="btn_work_add_author" class="btn btn-default max-width">Add Author</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_journals">Journals</label>
            <select multiple class="form-control" id="select_work_journals">
                <?php foreach ($work->getJournals() as $journal): ?>
                    <?php /** @var \BS\Model\Entity\Journal $journal */ ?>
                    <option value="<?=$journal->getId()?>" data-issn="<?=$journal->getIssn()?>" data-id="journal_<?=$journal->getId()?>" class="confirm-delete"><?=$journal->getJournalName()?></option>
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
                <button type="button" id="btn_work_add_journal" class="btn btn-default max-width">Add Journal</button>
            </div>
        </div>

        <div class="form-group">
            <label for="select_work_references">Referenced DOIs</label>
            <select multiple class="form-control" id="select_work_references">
                <?php foreach ($work->getWorkDois() as $referenceDoi): ?>
                    <option value="<?=$referenceDoi?>" data-doi="<?=$referenceDoi?>" data-id="doi_<?=$referenceDoi?>" class="confirm-delete"><?=$referenceDoi?></option>
                <?php endforeach ?>
            </select>
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
