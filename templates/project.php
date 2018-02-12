<?php if (!isset($project)): ?>
    <?php (new \BS\Model\Http\RedirectResponse('/404'))->send(); ?>
<?php else: ?>
<?php /** @var \BS\Model\Entity\Project $project */ ?>
<?php $this->layout('layout', ['title' => $project->getName()]) ?>
<script type="text/javascript">
$(document).ready(function () {
    var table = $('#table_works').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
        columnDefs: [{
            width: '10px',
            targets: 0,
            orderable: false
        }],
        order: [[1, 'asc']],
        scrollY: false,
        buttons: [
            {
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-add-2.png" alt="New Work" title="New Work"> New Work',
                className: 'btn btn-primary btn-outline btn-new-work',
                action: function () {
                    var modal = $('#new_work_modal');
                    modal.modal('show');
                    modal.on('shown.bs.modal', function () {
                        $(this).find('#input_work_doi').focus();
                    });
                }
            },
            {
                text: '<img src="/static/gfx/glyphicons/glyphicons/png/glyphicons-508-cluster.png" alt="Show Graph" title="Show Graph"> Graph',
                action: function () {
                    $('#show_graph_modal').modal('show');
                }
            },
            {
                extend: 'collection',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/document-export-4.png" alt="Export" title="Export"> Export',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" title="Copy to Clipboard"> Copy to Clipboard',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" title="CSV Export"> Export to CSV',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Export to Excel',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" title="PDF Export"> Export to PDF',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/image-svg+xml.png" alt="SVG Export" title="SVG Export"> Export to SVG',
                        action: function () {
                            window.location.href='/projects/request/graph_svg/<?=$project->getId()?>';
                        }
                    },
                    {
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/image-x-generic-2.png" alt="PNG Export" title="PNG Export"> Export to PNG',
                        action: function () {
                            window.location.href='/projects/request/graph_png/<?=$project->getId()?>';
                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/filter.png" alt="Show/Hide columns" title="Show/Hide columns"> Columns',
                columns: ':gt(0)'
            }
        ]
    });
    var checkAll = '<span><img src="/static/gfx/glyphicons/glyphicons/png/glyphicons-153-check.png" alt="Check all" title="Check all"> Check all</span>';
    var uncheckAll = '<span><img src="/static/gfx/glyphicons/glyphicons/png/glyphicons-154-unchecked.png" alt="Uncheck all" title="Uncheck all"> Uncheck all</span>';
    var tableAdd = $('#table_works_add').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
        autoWidth: false,
        order: [[1, 'asc']],
        columnDefs: [{
            width: '10px',
            targets: 0,
            orderable: false
        }],
        buttons: [
            {
                text: checkAll,
                className: 'btn btn-outline',
                action: function (e, dt, node) {
                    // Checks or unchecks the checkboxes in first column.
                    var checkUncheck = function(toggle) {
                        dt.column(0).nodes().each(function(e) {
                            $(e).find('input').prop('checked', toggle);
                        });
                    };

                    // If "Check" is found in text of this button node, uncheck
                    // all checkboxes, otherwise check all checkboxes.
                    if (node.text().indexOf('Check') !== -1) {
                        checkUncheck(false);
                        node.html(uncheckAll);
                    } else {
                        checkUncheck(true);
                        node.html(checkAll);
                    }
                }
            },
            {
                extend: 'collection',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/document-export-4.png" alt="Export" title="Export"> Export',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" title="Copy to Clipboard"> Copy to Clipboard',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" title="CSV Export"> Export to CSV',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Export to Excel',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" title="PDF Export"> Export to PDF',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/filter.png" alt="Show/Hide columns" title="Show/Hide columns"> Columns',
                columns: ':gt(0)'
            }
        ]
    });

    var tableAddWorks = $('#table_works_add_works').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
        autoWidth: false,
        order: [[0, 'asc']],
        buttons: [
            {
                extend: 'collection',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/document-export-4.png" alt="Export" title="Export"> Export',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" title="Copy to Clipboard"> Copy to Clipboard'
                    },
                    {
                        extend: 'csv',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" title="CSV Export"> Copy to CSV'
                    },
                    {
                        extend: 'excel',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Export to Excel'
                    },
                    {
                        extend: 'pdf',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" title="PDF Export"> Export to PDF'
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/filter.png" alt="Show/Hide columns" title="Show/Hide columns"> Columns'
            }
        ]
    });

    var tableAddAuthors = $('#table_works_add_authors').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
        autoWidth: false,
        order: [[0, 'asc']],
        buttons: [
            {
                extend: 'collection',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/document-export-4.png" alt="Export" title="Export"> Export',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" title="Copy to Clipboard"> Copy to Clipboard',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" title="CSV Export"> Export to CSV',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Export to Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" title="PDF Export"> Export to PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
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

    var tableAddJournals = $('#table_works_add_journals').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
        autoWidth: false,
        order: [[0, 'asc']],
        buttons: [
            {
                extend: 'collection',
                text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/document-export-4.png" alt="Export" title="Export"> Export',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/actions/edit-paste-8.png" alt="Copy to Clipboard" title="Copy to Clipboard"> Copy to Clipboard',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/text-csv.png" alt="CSV Export" title="CSV Export"> Export to CSV',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-vnd.ms-excel.png" alt="Excel Export" title="Excel Export"> Export to Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/application-pdf.png" alt="PDF Export" title="PDF Export"> Export to PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
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

    // Modify data table options (filter, buttons, ...).
    var tables = [table, tableAdd, tableAddWorks, tableAddAuthors, tableAddJournals];
    for (var tableIndex in tables) {
        var currentTable = tables[tableIndex];
        $(currentTable.table().container()).find('.row:eq(0)').children().each(function(j) {
            $(this).removeClass('col-sm-6').addClass('col-sm-2');

            if (j === 0) {
                // Length selector.
                var lengthSelect = $(this).find('label>select').detach();
                var lengthSelectIcon = $('<span class="input-group-addon"><i class="glyphicon glyphicon-eye-open"></i></span>');
                $(this).find('label').addClass('input-group').empty()
                    .append(lengthSelect, lengthSelectIcon);

                // Insert table buttons.
                currentTable.buttons().container().insertAfter($(this)).addClass('col-sm-8');
            } else if (j === 1) {
                // Filter (search) input.
                var filter = $(this).find('div:eq(0)');
                var filterInput = filter.find('label>input').detach().attr('placeholder', 'Search');
                var filterInputIcon = $('<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>')
                    .on('click', function() {
                        $(this).parent().find('input').focus();
                    });
                filter.parent().removeClass('col-sm-6').addClass('col-sm-2 input-group')
                    .empty()
                    .append(filterInputIcon, filterInput);
            }
        });
    }

    // If we have data available, enable button "Start Snowballing Analysis".
    if (table.rows().count() > 0) {
        $('#btn_start_snowballing')
            .removeClass('btn-disabled disabled')
            .addClass('btn-primary')
            .prop('disabled', false);
    }

    // Auto-fill on Enter for #input_work_doi
    $('#input_work_doi').on('keypress', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            $('#btn_work_doi_autofill').trigger('click');
        }
    });

    // API call for DOI.
    $('#btn_work_doi_autofill').click(function() {
        var $this = $(this);
        var doi = $('#input_work_doi');
        var modal = $('#new_work_modal');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/works/request/doi',
            data: {
                'work_doi': doi.val()
            },
            success: function (data) {
                var work = JSON.parse(data);
                if (work === null) return;
                $('#select_work_authors').empty();
                $('#select_work_journals').empty();
                $('#input_work_title').val(work['title']);
                $('#input_work_subtitle').val(work['subTitle']);
                $('#input_work_year').val(work['workYear']);
                $.each(work.authors, function (i, author) {
                    $('#select_work_authors').append($('<option>', {
                        value: author['id'],
                        text : author['firstName'] + ' ' + author['lastName'],
                        attr: {
                            'data-firstname': author['firstName'],
                            'data-lastname': author['lastName']
                        }
                    }));
                });
                $.each(work.journals, function (i, journal) {
                    $('#select_work_journals').append($('<option>', {
                        value: journal['id'],
                        text : journal['journalName'],
                        attr: {'data-issn': journal.issn}
                    }));
                });
                $('form').validator('validate');
                $this.button('reset');
                modal.find('.alert').addClass('hidden');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                $this.button('reset');
                modal.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            }
        });
    });

    // "Reset/Cancel" button click in "Create new work" modal.
    $('#btn_work_reset, #btn_work_cancel').click(function () {
        $('#new_work_modal').find('input').val('');
        $('#select_work_authors').empty();
        $('#select_work_journals').empty();
    });

    // Add Journal.
    $('#btn_work_add_journal').click(function() {
        var journalName = $('#work_add_journal_name');
        var journalIssn = $('#work_add_journal_issn');
        $('#select_work_journals').append($('<option>', {
            text : journalName.val(),
            attr: {'data-issn': journalIssn.val()}
        }).on('dblclick', function() {
            $(this).remove();
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
            attr: {
                'data-firstname': authorFirstName.val(),
                'data-lastname': authorLastName.val()
            }
        }).on('dblclick', function() {
            $(this).remove();
        }));
        authorFirstName.val('');
        authorLastName.val('');
    });

    // New project button submit click.
    $('#btn_work_create').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#new_work_modal');
        $this.button('loading');

        // Setup author data:
        var dataAuthors = [];
        $('#select_work_authors').find('option').each(function() {
            dataAuthors.push({
                'id': $(this).attr('value') ? $(this).attr('value') : null,
                'first_name': $(this).data('firstname'),
                'last_name': $(this).data('lastname')
            });
        });

        // Setup journal data:
        var dataJournals = [];
        $('#select_work_journals').find('option').each(function() {
            dataJournals.push({
                'id': $(this).attr('value') ? $(this).attr('value') : null,
                'journal_name': $(this).text(),
                'issn': $(this).data('issn')
            });
        });

        $.ajax({
            type: 'POST',
            url: '/works/new',
            data: {
                'project_id': modal.data('projectId'),
                'work_title': $('#input_work_title').val(),
                'work_subtitle': $('#input_work_subtitle').val(),
                'work_year': $('#input_work_year').val(),
                'work_doi': $('#input_work_doi').val(),
                'authors': dataAuthors,
                'journals': dataJournals
            },
            success: function (data, textStatus) {
                $this.button('reset');
                $('#input_work_title').val('');
                $('#input_work_subtitle').val('');
                $('#input_work_year').val('');
                $('#input_work_doi').val('');
                $('#select_work_authors').empty();
                $('#select_work_journals').empty();

                if (textStatus === 'nocontent') {
                    modal.find('.alert-warning')
                        .text('Work already assigned.')
                        .removeClass('hidden');
                    return;
                }

                var work = JSON.parse(data);
                modal.modal('toggle');
                modal.find('.alert-warning').addClass('hidden');

                // Enable button "Start Snowballing Analysis".
                $('#btn_start_snowballing')
                    .addClass('btn-primary')
                    .removeClass('btn-disabled disabled')
                    .prop('disabled', false);

                var j;
                var authors = '';
                if (work.authors !== null && Object.keys(work.authors).length > 0) {
                    j = 0;
                    $.each(work.authors, function (i, author) {
                        if (j++ > 0) authors += ', ';
                        authors += author['firstName']
                            + ' ' + author['lastName'];
                    });
                }

                var journals = '';
                if (work.journals !== null && Object.keys(work.journals).length > 0) {
                    j = 0;
                    $.each(work.journals, function (i, journal) {
                        if (j++ > 0) journals += ', ';
                        journals += journal['journalName'];
                    });
                }

                table.row.add([
                    '<label><input name="work_include" type="checkbox" value="'
                        + work['id'] + '" checked></label>',
                    '<a href="/works/view/' + work['id'] + '" class="work-link">'
                        + work['title'] + '</a>',
                    authors,
                    journals,
                    timestampToDate((new Date()).getTime(), true),
                    work['doi'],
                    '<a href="#" class="btn btn-danger" data-toggle="modal" data-target="#delete_work_modal" data-project-id="'
                        + modal.data('projectId') + '" data-work-id="'
                        + work['id'] + '" data-work-title="' + work['title']
                        + '"><span class="glyphicon glyphicon-trash"></span></a>'
                ]).draw(false);
            },
            error: function () {
                $this.button('reset');
                modal.find('.alert-warning')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            }
        });
    });

    // Snowballing modal dialog hide and show.
    $('#snowballing_modal')
        .on('hide.bs.modal', function() {
            // Set "Check All" to first button.
            tableAdd.buttons(0).nodes(0).html(checkAll);
        })
        .on('show.bs.modal', function() {
        var modal = $(this);
        var selectedWorkIds = [];
        var spinner = $('#snowballing_spin');
        var gauge = $('#progress_gauge');
        var gaugeContainer = $('#progress_container');
        var progressText = $('#progress_text');
        var buttonAdd = $('#btn_works_add');
        var tabs = $('#snowballing_tabs');
        var tabsNav = $('#snowballing_tabs_nav');

        tableAdd.clear().draw();
        tableAddWorks.clear().draw();
        tableAddAuthors.clear().draw();
        tableAddJournals.clear().draw();
        tabs.hide();
        tabsNav.hide();
        gaugeContainer.show();
        gauge
            .removeClass('progress-bar-success progress-bar-danger')
            .attr('aria-valuenow', 0)
            .animate({width: '0%'});
        spinner.addClass('fa-spin');
        progressText
            .show()
            .text('Initializing...');
        buttonAdd.prop('disabled', true);

        table.columns(0).every(function() {
            this.nodes().to$().find('input[type=checkbox]:checked').each(function (i, checkbox) {
                selectedWorkIds.push({work_id: $(checkbox).val()});
            });
        });

        $.ajax({
            type: 'POST',
            url: '/works/request/references',
            data: {
                'work_ids': selectedWorkIds,
                'project_id': modal.data('projectId')
            },
            success: function (data) {
                if (data.length === 0) {
                    spinner.removeClass('fa-spin');
                    progressText.text('Sorry, no new or usable references found!');
                    gauge
                        .addClass('progress-bar-danger')
                        .attr('aria-valuenow', 100)
                        .animate({width: '100%'});
                    return;
                }
                var stats = {
                    sumWorks: 0,
                    works: {},
                    sumAuthors: 0,
                    authors: {},
                    sumJournals: 0,
                    journals: {}
                };
                var countDois = Object.keys(data).length;

                if (countDois > 0) {
                    stats.sumWorks = countDois;

                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            (function(currentDoi, countDois, countCurrentDoi, stats) {
                                // Request current work.
                                $.ajax({
                                    type: 'POST',
                                    url: '/works/request/doi',
                                    data: {
                                        'work_doi': currentDoi
                                    },
                                    complete: function(data) {
                                        var work = JSON.parse(data['responseJSON']);
                                        if (work === null) return;

                                        var j;
                                        var authors = '';
                                        if (work.authors !== null && Object.keys(work.authors).length > 0) {
                                            j = 0;
                                            for (var authorKey in work.authors) {
                                                if (work.authors.hasOwnProperty(authorKey)) {
                                                    var author = work.authors[authorKey];
                                                    var currentAuthor = author['firstName'] + ' ' + author['lastName'];
                                                    if (j++ > 0) authors += ', ';
                                                    authors += currentAuthor;

                                                    if (stats.authors.hasOwnProperty(currentAuthor)) {
                                                        stats.authors[currentAuthor].count++;
                                                    } else {
                                                        stats.authors[currentAuthor] = {
                                                            count: 1,
                                                            share: 0
                                                        };
                                                    }
                                                    stats.sumAuthors++;
                                                }
                                            }
                                        }

                                        var journals = '';
                                        if (work.journals !== null && Object.keys(work.journals).length > 0) {
                                            j = 0;
                                            for (var journalsKey in work.journals) {
                                                if (work.journals.hasOwnProperty(journalsKey)) {
                                                    var journal = work.journals[journalsKey];
                                                    var journalName = journal['journalName'];
                                                    if (j++ > 0) journals += ', ';
                                                    journals += journalName;

                                                    if (stats.journals.hasOwnProperty(journalName)) {
                                                        stats.journals[journalName].count++;
                                                    } else {
                                                        stats.journals[journalName] = {
                                                            count: 1,
                                                            share: 0
                                                        };
                                                    }
                                                    stats.sumJournals++;
                                                }
                                            }
                                        }

                                        if (stats.works.hasOwnProperty(currentDoi)) {
                                            stats.works[work['title']].count++;
                                        } else {
                                            stats.works[work['title']] = {
                                                count: 1,
                                                share: 0
                                            }
                                        }

                                        tableAdd.row.add([
                                            '<label><input name="work_include" type="checkbox" value="'
                                            + work['id'] + '" checked></label>',
                                            work['title'],
                                            authors,
                                            journals,
                                            work['doi']
                                        ]).draw(false);

                                        var currentAbsolute = tableAdd.rows().count();
                                        var currentPercent = Math.round(currentAbsolute / countDois * 100);
                                        if (currentAbsolute < countDois) {
                                            progressText.text(currentAbsolute + '/' + countDois + ' references handled (' + currentPercent + '%)...');
                                        } else {
                                            progressText.text(currentAbsolute + '/' + countDois + ' references handled. Performing post processing...');
                                        }

                                        gauge.attr('aria-valuenow', currentPercent).css('width', currentPercent + '%');
                                        if (currentPercent >= 100) {
                                            spinner.removeClass('fa-spin');
                                            gauge.addClass('progress-bar-success');
                                            progressText
                                                .fadeOut(1000)
                                                .text('Done!');
                                            gaugeContainer.fadeOut(1000, function() {
                                                tabs.fadeIn('slow');
                                                tabsNav.fadeIn('slow');
                                                buttonAdd.prop('disabled', false);
                                            });

                                            handleStatistics(stats.works, stats.sumWorks, tableAddWorks);
                                            handleStatistics(stats.authors, stats.sumAuthors, tableAddAuthors);
                                            handleStatistics(stats.journals, stats.sumJournals, tableAddJournals);
                                        }
                                    },
                                    error: function(xhr) {
                                        console.log(xhr.responseText);
                                        modal.find('.alert')
                                            .text('Sorry, an error occurred while requesting. Please try again later.')
                                            .removeClass('hidden');
                                    }
                                });
                            })(key, countDois, data[key], stats);
                        }
                    }
                }

                modal.find('.alert').addClass('hidden');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                modal.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            }
        });
    });

    // Snowballing modal button "Add Works" click.
    $('#btn_works_add').click(function () {
        var $this = $(this);
        var modal = $('#snowballing_modal');
        var selectedWorkIds = [];

        tableAdd.columns(0).every(function() {
            this.nodes().to$().find('input[type=checkbox]:checked').each(function (i, checkbox) {
                selectedWorkIds.push({work_id: $(checkbox).val()});
            });
        });

        $this.button('loading');

        $.ajax({
            type: 'POST',
            url: '/works/assign',
            data: {
                'project_id': modal.data('projectId'),
                'work_ids': selectedWorkIds
            },
            success: function (data) {
                $this.button('reset');
                modal.find('.alert').addClass('hidden');
                modal.modal('toggle');

                if (data.length === 0) {
                    return;
                }

                for (var referenceIndex in data) {
                    if (data.hasOwnProperty(referenceIndex)) {
                        var work = data[referenceIndex];
                        var j;
                        var authors = '';
                        if (work.authors !== null && Object.keys(work.authors).length > 0) {
                            j = 0;
                            $.each(work.authors, function (i, author) {
                                if (j++ > 0) authors += ', ';
                                authors += author['firstName']
                                    + ' ' + author['lastName'];
                            });
                        }

                        var journals = '';
                        if (work.journals !== null && Object.keys(work.journals).length > 0) {
                            j = 0;
                            $.each(work.journals, function (i, journal) {
                                if (j++ > 0) journals += ', ';
                                journals += journal['journalName'];
                            });
                        }

                        table.row.add([
                            '<label><input name="work_include" type="checkbox" value="'
                            + work['id'] + '" checked></label>',
                            '<a href="/works/view/' + work['id'] + '" class="work-link">'
                            + work['title'] + '</a>',
                            authors,
                            journals,
                            timestampToDate(work['created_at'], true),
                            work['doi'],
                            '<a href="#" class="btn btn-danger" data-toggle="modal" data-target="#delete_work_modal" data-project-id="'
                                + modal.data('projectId') + '" data-work-id="'
                                + work['id'] + '" data-work-title="' + work['title']
                                + '"><span class="glyphicon glyphicon-trash"></span></a>'
                        ]).draw(false);
                    }
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                modal.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            }
        });
    });

    // Graph modal dialog.
    $('#show_graph_modal').on('show.bs.modal', function() {
        var $this = $(this);
        var loader = $('#visualization_loader');
        loader.show();
        $.ajax({
            type: 'GET',
            url: '/projects/request/graph/' + $this.data('projectId'),
            success: function (data) {
                if (data.length === 0) {
                    return;
                }

                var options = {
                    layout: {
                        hierarchical: {
                            direction: 'LR',
                            levelSeparation: 500
                        }
                    },
                    nodes: {
                        shape: 'box',
                        margin: 10,
                        widthConstraint: {
                            maximum: 200
                        }
                    }
                };

                $this.find('.modal-dialog').animate({
                    width: $(window).innerWidth() - 50
                }, {
                    complete: function() {
                        $this.find('.modal-body').animate({
                            height: $(window).innerHeight() - 170
                        }, {
                            complete: function() {
                                new vis.Network(document.getElementById('visualization_container'), data, options);
                                loader.hide();
                            }
                        });
                    }
                });
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                $this.find('.alert')
                    .text('Sorry, an error occurred while requesting. Please try again later.')
                    .removeClass('hidden');
            }
        });
    });

    // Remove work modal dialog.
    $('#delete_work_modal').on('show.bs.modal', function(e) {
        var data = $(e.relatedTarget).data();
        $('.modal-body', this)
            .html('<div class="alert alert-warning hidden"></div><p class="color-danger bold">Do you really want to remove the following work from your project?</p>')
            .append(document.createTextNode(data['workTitle']));
        $('#btn_delete_work')
            .data('projectId', data['projectId'])
            .data('workId', data['workId']);
    });

    // Remove work button submit click.
    $('#btn_delete_work').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $('#delete_work_modal');
        $this.button('loading');
        $.ajax({
            type: 'POST',
            url: '/projects/work/remove',
            data: {
                'project_id': $(this).data('projectId'),
                'work_id': $(this).data('workId')
            },
            success: function (data) {
                $this.button('reset');
                modal.find('.alert').addClass('hidden');
                table.row('#tr_work_id_' + JSON.parse(data['work_id'])).remove().draw();
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
                    <div class="form-group">
                        <label for="input_work_doi">Digital Object Identifier (DOI)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="input_work_doi" placeholder="Enter a DOI" data-minlength="1" maxlength="250">
                            <span class="input-group-btn">
                            <button class="btn btn-default" id="btn_work_doi_autofill" type="button" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading...">Autofill</button>
                        </span>
                        </div>
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
                            <button type="button" id="btn_work_add_author" class="btn btn-default">Add Author</button>
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
                            <button type="button" id="btn_work_add_journal" class="btn btn-default">Add Journal</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_work_cancel" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn_work_reset" class="btn btn-default">Reset</button>
                    <button type="submit" id="btn_work_create" class="btn btn-primary" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Adding Work...">Add Work</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="snowballing_modal" class="modal fade" role="dialog" data-project-id="<?=$project->getId()?>">
    <div class="modal-dialog modal-dialog-full-width">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i id="snowballing_spin" class='fa bs-icon fa-spin'></i> Processing Snowballing...</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning hidden"></div>
                <div id="progress_text"></div>
                <div class="progress" id="progress_container">
                    <div id="progress_gauge" class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <ul class="nav nav-tabs" id="snowballing_tabs_nav">
                    <li class="active"><a data-toggle="tab" href="#tab_selection">Selection</a></li>
                    <li><a data-toggle="tab" href="#tab_works">Works</a></li>
                    <li><a data-toggle="tab" href="#tab_authors">Authors</a></li>
                    <li><a data-toggle="tab" href="#tab_journals">Journals</a></li>
                </ul>
                <div class="tab-content" id="snowballing_tabs">
                    <div id="tab_selection" class="tab-pane fade in active">
                        <div class="table-responsive">
                            <table class="table table-striped table-sorted" id="table_works_add">
                                <thead>
                                <tr>
                                    <th>Add?</th>
                                    <th>Work title</th>
                                    <th>Authors</th>
                                    <th>Journal</th>
                                    <th>DOI</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab_works" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-striped table-sorted" id="table_works_add_works">
                                <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Work title</th>
                                    <th># Citations</th>
                                    <th>Share</th>
                                    <th>Aggregated</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab_authors" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-striped table-sorted" id="table_works_add_authors">
                                <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Author name</th>
                                    <th># Citations</th>
                                    <th>Share</th>
                                    <th>Aggregated</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab_journals" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-striped table-sorted" id="table_works_add_journals">
                                <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Journal name</th>
                                    <th># Citations</th>
                                    <th>Share</th>
                                    <th>Aggregated</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btn_works_add" class="btn btn-primary" disabled data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Adding Works...">Add Works</button>
            </div>
        </div>
    </div>
</div>
<div id="show_graph_modal" class="modal fade" role="dialog" data-project-id="<?=$project->getId()?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Graph</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning hidden"></div>
                <table id="visualization_loader">
                    <tbody>
                    <tr>
                        <td class="align-middle">
                            <div class="loader"></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div id="visualization_container"></div>
            </div>
            <div class="modal-footer">
                <a href="/projects/request/graph_png/<?=$project->getId()?>" class="btn btn-primary" role="button"><img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/image-x-generic-2.png" alt="PNG Export" title="PNG Export"> Export to PNG</a>
                <a href="/projects/request/graph_svg/<?=$project->getId()?>" class="btn btn-primary" role="button"><img src="/static/gfx/open_icon_library/oxygen-style/mimetypes/image-svg+xml.png" alt="SVG Export" title="SVG Export">Export to SVG</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_work_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title color-danger">Remove Work</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning hidden"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btn_delete_work" class="btn btn-danger" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Removing work...">Remove</button>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-sorted" id="table_works">
        <thead>
        <tr>
            <th>Inc?</th>
            <th>Work Title</th>
            <th>Authors</th>
            <th>Journal</th>
            <th>Added</th>
            <th>DOI</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($project->getWorks() as $work): ?>
            <?php /** @var \BS\Model\Entity\Work $work */ ?>
                <tr id="tr_work_id_<?=$work->getId()?>">
                    <td>
                        <label>
                            <input name="work_include" type="checkbox" value="<?=$work->getId()?>" checked>
                        </label>
                    </td>
                    <td><a href="/works/view/<?=$work->getId()?>" class="work-link"><?=$work->getTitle()?></a></td>
                    <td><?=$this->joinEntities($work->getAuthors(), array('firstName', 'lastName'))?></td>
                    <td><?=$this->joinEntities($work->getJournals(), array('journalName'))?></td>
                    <td><?=$this->date($project->getWorkCreatedAt($work), 'd.m.Y / h:i')?></td>
                    <td><?=$work->getDoi()?></td>
                    <td>
                        <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#delete_work_modal" data-project-id="<?=$project->getId()?>" data-work-id="<?=$work->getId()?>" data-work-title="<?=$work->getTitle()?>">
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div class="container">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <button class="btn btn-disabled disabled" disabled id="btn_start_snowballing" data-toggle="modal" data-target="#snowballing_modal">Start Snowballing Analysis</button>
        </div>
        <div class="col-sm-3"></div>
    </div>
</div>
<?php endif ?>
