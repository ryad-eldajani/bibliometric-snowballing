<?php $this->layout('layout', ['title' => 'Manage Projects', 'subTitle' => 'Please manage your projects.']) ?>
<script type="text/javascript">
$(document).ready(function () {
    var tableProjectsEmpty = $('<p>No projects available yet.</p><button class="btn btn-primary">Create new Project</button>').on('click', function() {
        var modal = $('#new_project_modal');
        modal.modal('show');
        modal.on('shown.bs.modal', function() {
            $(this).find('input').focus();
        });
    });
    var table = $('#table_projects').DataTable({
        scrollY: false,
        lengthChange: false,
        language: {
            emptyTable: tableProjectsEmpty
        },
        buttons: [
            {
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-add-2.png" alt="New Project" title="New Project"> New Project',
                className: 'btn btn-primary btn-outline btn-new-project',
                action: function () {
                    var modal = $('#new_project_modal');
                    modal.modal('show');
                    modal.on('shown.bs.modal', function() {
                        $(this).find('input').focus();
                    });
                }
            },
            {
                extend: 'collection',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/document-export-4.png" alt="Export" title="Export"> Export',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" title="Copy to Clipboard"> Clipboard',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" title="CSV Export"> CSV',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Excel',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" title="PDF Export"> PDF',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/filter.png" alt="Show/Hide columns" title="Show/Hide columns"> Columns'
            }
        ]
    });
    table.buttons().container().appendTo('#table_projects_wrapper .col-sm-6:eq(0)');

    // Modify search input
    $('.dt-buttons').parent().removeClass('col-sm-6').addClass('col-sm-10');
    var filter = $('#table_projects_filter');
    var filterInput = filter.find('label>input').detach().attr('placeholder', 'Search Project');
    filter.parent()
        .removeClass('col-sm-6').addClass('col-sm-2 input-group')
        .html('<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>')
        .append(filterInput);

    // New project button submit click.
    $('#btn_project_create').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#new_project_modal');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/projects/new',
            data: {'project_name': $('#input_project_name').val()},
            success: function (data) {
                var project = JSON.parse(data);
                $this.button('reset');
                $('#input_project_name').val('');
                modal.find('.alert').addClass('hidden');
                modal.modal('toggle');

                var newRow = table.row.add([
                    '<a href="/projects/view/' + project['id'] + '"  class="project-link">' + project['name'] + '</a>',
                    0,
                    timestampToDate(project['createdAt']),
                    '<div class="dropdown"><button class="btn btn-primary dropdown-toggle dropdown-option" type="button" ' +
                    'data-toggle="dropdown"><i class="fa fa-cog"></i><span class="caret"></span></button>' +
                    '<ul class="dropdown-menu"><li><a href="#" id="rename_project_toggle" data-toggle="modal" ' +
                    'data-target="#rename_project_modal" data-project-id="' + project['id'] + '" data-project-name="'
                    + project['name'] + '"><span class="glyphicon glyphicon-pencil"></span>Rename</a></li><li>' +
                    '<a href="#" class="color-danger" data-toggle="modal" data-target="#delete_project_modal" ' +
                    'data-project-id="' + project['id'] + '" data-project-name="' + project['name'] + '">' +
                    '<span class="glyphicon glyphicon-trash"></span> Delete </a> </li></ul></div>'
                ]);
                $(newRow.node()).attr('id', 'tr_project_id_' + project['id']);
                newRow.draw();
            },
            error: function (xhr) {
                modal.find('.alert')
                    .text(JSON.parse(xhr.responseText).error)
                    .removeClass('hidden');
            }
        });
    });

    // Delete project modal dialog.
    $('#delete_project_modal').on('show.bs.modal', function(e) {
        var data = $(e.relatedTarget).data();
        $('.modal-body', this)
            .html('<div class="alert alert-warning hidden"></div><p class="color-danger bold">Do you really want to delete the following project?</p>')
            .append(document.createTextNode(data['projectName']));
        $('#btn_delete_project').data('projectId', data['projectId']);
    });

    // Delete project button submit click.
    $('#btn_delete_project').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#delete_project_modal');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/projects/delete',
            data: {'project_id': $(this).data('projectId')},
            success: function (data) {
                $this.button('reset');
                modal.find('.alert').addClass('hidden');
                table.row('#tr_project_id_' + JSON.parse(data['project_id'])).remove().draw();
                modal.modal('toggle');
            },
            error: function (xhr) {
                modal.find('.alert')
                    .text(JSON.parse(xhr.responseText).error)
                    .removeClass('hidden');
                $this.button('reset');
            }
        });
    });

    // Rename project modal dialog.
    $('#rename_project_modal').on('show.bs.modal', function(e) {
        var data = $(e.relatedTarget).data();
        var input = $('#input_project_name_rename');
        input.val(data['projectName']);
        input.data('projectId', data['projectId']);
    }).on('shown.bs.modal', function() {
        $(this).find('#input_project_name_rename').focus();
    });

    // Rename project button submit click.
    $('#btn_project_rename').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#rename_project_modal');
        var input = $('#input_project_name_rename');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/projects/rename',
            data: {
                'project_id': input.data('projectId'),
                'project_name': input.val()
            },
            success: function (data) {
                var project = JSON.parse(data);
                $this.button('reset');
                $('#input_project_name_rename').val('');
                modal.modal('toggle');
                $('#tr_project_id_' + project.id)
                    .find('a.project-link')
                    .text(project['name']);
                $('#rename_project_toggle').data('projectName', project['name']);
                modal.find('.alert').addClass('hidden');
            },
            error: function (xhr) {
                modal.find('.alert')
                    .text(JSON.parse(xhr.responseText).error)
                    .removeClass('hidden');
            }
        });
    });
});
</script>
<div id="new_project_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" data-toggle="validator">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create new Project</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning hidden"></div>
                    <div class="form-group">
                        <label for="input_project_name">Project Name</label>
                        <input type="text" class="form-control" id="input_project_name" placeholder="Enter a new Project Name" data-minlength="1" maxlength="250" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_project_create" class="btn btn-primary" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Creating project...">Create project</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_project_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title color-danger">Delete Project</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning hidden"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btn_delete_project" class="btn btn-danger" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Deleting project...">Delete</button>
            </div>
        </div>
    </div>
</div>
<div id="rename_project_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" data-toggle="validator">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Rename Project</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning hidden"></div>
                    <div class="form-group">
                        <label for="input_project_name_rename">Project name</label>
                        <input type="text" class="form-control" id="input_project_name_rename" placeholder="Enter a new Project Name" data-minlength="1" maxlength="250" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_project_rename" class="btn btn-primary" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Renaming project...">Rename project</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-sorted" id="table_projects">
        <thead>
        <tr>
            <th>Project Name</th>
            <th>Number of Objects</th>
            <th>Created</th>
            <th>Options</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($projects) && is_array($projects) && count($projects) > 0): ?>
        <?php foreach ($projects as $project): ?>
        <?php /** @var \BS\Model\Entity\Project $project */ ?>
        <tr id="tr_project_id_<?=$project->getId()?>">
            <td><a href="/projects/view/<?=$project->getId()?>" class="project-link"><?=$project->getName()?></a></td>
            <td><?=count($project->getWorkIds())?></td>
            <td><?=$this->date($project->getCreatedAt())?></td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle dropdown-option" type="button" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" id="rename_project_toggle" data-toggle="modal" data-target="#rename_project_modal" data-project-id="<?=$project->getId()?>" data-project-name="<?=$project->getName()?>">
                                <span class="glyphicon glyphicon-pencil"></span> Rename
                            </a>
                        </li>
                        <li>
                            <a href="#" class="color-danger" data-toggle="modal" data-target="#delete_project_modal" data-project-id="<?=$project->getId()?>" data-project-name="<?=$project->getName()?>">
                                <span class="glyphicon glyphicon-trash"></span> Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <?php endforeach ?>
        <?php endif ?>
        </tbody>
    </table>
</div>
