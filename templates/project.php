<?php if (!isset($project)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php /** @var \BS\Model\Entity\Project $project */ ?>
<?php $this->layout('layout', ['title' => 'Manage Project', 'subTitle' => $project->getName()]) ?>
<script type="text/javascript">
$(document).ready(function () {
    var table = $('#table_works').DataTable({
        scrollY: false,
        lengthChange: false,
        buttons: [
            {
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-add-2.png" alt="New Work" alt="New Work"> New Work',
                className: 'btn btn-primary btn-outline btn-new-work',
                action: function (e, dt, node, config) {
                }
            },
            {
                extend: 'copy',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" alt="Copy to Clipboard"> Clipboard',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            },
            {
                extend: 'csv',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" alt="CSV Export"> CSV',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Excel',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" alt="PDF Export"> PDF',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            },
            {
                extend: 'colvis',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/filter.png" alt="Show/Hide columns" alt="Show/Hide columns"> Columns'
            }
        ]
    });
    table.buttons().container().appendTo('#table_works_wrapper .col-sm-6:eq(0)');
    table.on('buttons-action', function (e, button) {
        if (button.text().indexOf('New Work') !== -1) {
            var modal = $('#new_work_modal');
            modal.modal('show');
            modal.on('shown.bs.modal', function () {
                $(this).find('#input_work_doi').focus();
            });
        }
    });

    // Modify search input
    $('.dt-buttons').parent().removeClass('col-sm-6').addClass('col-sm-10');
    var filter = $('#table_works_filter');
    var filterInput = filter.find('label>input').detach().attr('placeholder', 'Search Works');
    filter.parent()
        .removeClass('col-sm-6').addClass('col-sm-2 input-group')
        .html('<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>')
        .append(filterInput);

    // API call to crossref.org, when DOI is filled out and nothing else
    $('#input_work_doi').on('blur', function(e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#new_work_modal');
        var allInputEmpty = true;

        modal.find('input').not($this).each(function() {
            if ($(this).val() !== '') {
                allInputEmpty = false;
            }
        });

        if (!allInputEmpty) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'https://api.crossref.org/works/' + $this.val(),
            success: function (data) {
                var work = data['message'];
                $('#input_work_title').val(work['title'][0]);
                $('#input_work_subtitle').val('');
                $('#input_work_year').val(work['created']['date-parts'][0][0]);
                modal.find('.alert').addClass('hidden');
            },
            error: function (xhr) {
                modal.find('.alert')
                    .text(JSON.parse(xhr.responseText).error)
                    .removeClass('hidden');
            }
        });
    });

    // New project button submit click.
    $('#btn_work_create').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#new_work_modal');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/works/new',
            data: {
                'project_id': modal.data('projectId'),
                'work_title': $('#input_work_title').val(),
                'work_subtitle': $('#input_work_subtitle').val(),
                'work_year': $('#input_work_year').val(),
                'work_doi': $('#input_work_doi').val()
            },
            success: function (data) {
                var work = JSON.parse(data);
                $this.button('reset');
                $('#input_work_title').val('');
                $('#input_work_subtitle').val('');
                $('#input_work_year').val('');
                $('#input_work_doi').val('');
                modal.modal('toggle');

                var authors = work.authors === null ? '' : work.authors;
                var journals = work.journals === null ? '' : work.journals;

                $('#table_works').find('tr:last').after(
                    '<tr><td><label><input name="work_include" type="checkbox" value="'
                    + work.id_work + '" checked></label></td><td>' +
                    '<a href="/works/view/' + work.id_work + '" class="work-link">'
                    + work.title + '</a></td><td>' + authors + '</td><td>'
                    + journals + '</td><td>' + work.doi + '</td></tr>'
                );
                modal.find('.alert-warning').addClass('hidden');
            },
            error: function (xhr) {
                $this.button('reset');
                modal.find('.alert-warning')
                    .text(JSON.parse(xhr.responseText).error)
                    .removeClass('hidden');
            }
        });
    });
});
</script>
<div id="new_work_modal" class="modal fade" role="dialog" data-project-id="<?=$project->getId()?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" data-toggle="validator">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create new work</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning hidden"></div>
                    <div class="alert alert-info">
                        <span class="bold">Hint:</span> When you only enter a DOI we will try to fill the remaining information automatically!
                    </div>
                    <div class="form-group">
                        <label for="input_work_doi">Document Object Identifier (DOI)</label>
                        <input type="text" class="form-control" id="input_work_doi" placeholder="Enter a DOI" data-minlength="1" maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="input_work_title">Work title</label>
                        <input type="text" class="form-control" id="input_work_title" placeholder="Enter a work title" data-minlength="1" maxlength="250" required>
                    </div>
                    <div class="form-group">
                        <label for="input_work_subtitle">Work subtitle</label>
                        <input type="text" class="form-control" id="input_work_subtitle" placeholder="Enter a work subtitle" data-minlength="1" maxlength="250">
                    </div>

                    <div class="form-group">
                        <label for="input_work_year">Work year</label>
                        <input type="number" class="form-control" id="input_work_year" placeholder="Enter the year of the work" min="1500" max="2200">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_work_create" class="btn btn-primary" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Adding work...">Add work</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-sorted" id="table_works">
        <thead>
        <tr>
            <th>Include</th>
            <th>Work title</th>
            <th>Authors</th>
            <th>Journal</th>
            <th>DOI</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($project->getWorks() as $work): ?>
            <?php /** @var \BS\Model\Entity\Work $work */ ?>
                <tr>
                    <td>
                        <label>
                            <input name="work_include" type="checkbox" value="<?=$work->getId()?>" checked>
                        </label>
                    </td>
                    <td><a href="/works/view/<?=$work->getId()?>" class="work-link"><?=$work->getTitle()?></a></td>
                    <td><?=$this->join($work->getAuthors())?></td>
                    <td><?=$this->join($work->getJournals())?></td>
                    <td><?=$work->getDoi()?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif ?>
