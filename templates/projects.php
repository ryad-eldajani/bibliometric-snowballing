<?php $this->layout('layout', ['title' => 'Manage Projects', 'subTitle' => 'Please manage your projects.']) ?>
<div class="table-responsive">
    <table class="table table-striped table-sorted">
        <thead>
        <tr>
            <th>Projectname</th>
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
        </tbody>
    </table>
</div>