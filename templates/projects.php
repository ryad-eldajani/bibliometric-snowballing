<?php $this->layout('layout', ['title' => 'Manage Projects', 'subTitle' => 'Please manage your projects.']) ?>
<script type="text/javascript">
$(document).ready(function () {
    var table = $('#table_projects').DataTable({
        lengthChange: false,
        buttons: [
            {
                text: 'New project',
                className: 'btn btn-primary btn-outline btn-new-project',
                action: function (e, dt, node, config) {}
            },
            { extend: 'copy', text: 'Copy to clipboard' },
            { extend: 'excel', text: 'Export to Excel' },
            {
                text: 'Export to CSV',
                action: function ( e, dt, button, config ) {
                    var data = dt.buttons.exportData();

                    $.fn.dataTable.fileSave(
                        new Blob([JSON.stringify(data)]),
                        'Export.csv'
                    );
                }
            },
            { extend: 'pdf', text: 'Export to PDF' },
            { extend: 'colvis', text: 'Show columns' }
        ]
    });
    table.buttons().container().appendTo('#table_projects_wrapper .col-sm-6:eq(0)');
    table.on('buttons-action', function (e, button, dataTable, node, config) {
        if (button.text() === 'New project') {
            $('#new_project_modal').modal('show');
        }
    });
    $('.dt-buttons').parent().removeClass('col-sm-6').addClass('col-sm-10');
    var filter = $('#table_projects_filter');
    var filterInput = filter.find('label>input').detach().attr('placeholder', 'Search project');
    filter.parent()
        .removeClass('col-sm-6').addClass('col-sm-2 input-group')
        .html('<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>')
        .append(filterInput);
    $('#btn_project_create').click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/projects/new',
            data: {'project_id': 1, 'project_name': $('#input_project_name').val()},
            dataType: 'json',
            success: function (data, status) {
                console.log(data.project_name);
                $('#new_project_modal').modal('toggle');
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
                    <h4 class="modal-title">Create new project</h4>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label for="inputName">Project name</label>
                            <input type="text" class="form-control" id="input_project_name" placeholder="Enter a new project name" data-minlength="4" required>
                            <div class="help-block">Minimum of 4 characters</div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_project_create" class="btn btn-primary">Create project</button>
                </div>
            </form>
        </div>

    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-sorted" id="table_projects">
        <thead>
        <tr>
            <th>Project name</th>
            <th>Number of objects</th>
            <th>Created</th>
            <th>Options</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Project 1</td>
            <td>23</td>
            <td>01.11.2017</td>
            <td>
                <button type="button" class="btn btn-xs btn-info">
                    <span class="glyphicon glyphicon-pencil"></span>
                </button>
                <button type="button" class="btn btn-xs btn-danger">
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
            </td>
        </tr>
        <tr>
            <td>Project 2</td>
            <td>42</td>
            <td>03.11.2017</td>
            <td>
                <button type="button" class="btn btn-xs btn-info">
                    <span class="glyphicon glyphicon-pencil"></span>
                </button>
                <button type="button" class="btn btn-xs btn-danger">
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
            </td>
        </tr>
        <tr>
            <td>This is a test</td>
            <td>111</td>
            <td>08.11.2017</td>
            <td>
                <button type="button" class="btn btn-xs btn-info">
                    <span class="glyphicon glyphicon-pencil"></span>
                </button>
                <button type="button" class="btn btn-xs btn-danger">
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</div>