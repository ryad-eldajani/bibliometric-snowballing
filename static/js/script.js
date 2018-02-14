/**
 * Converts a Unix timestamp to DD.MM.YYYY formatted date.
 *
 * @param {number} timestamp Unix timestamp
 * @param {boolean} full also time if true
 * @returns {string} date in DD.MM.YYYY (/ HH:MM) format
 */
var timestampToDate = function(timestamp, full) {
    var date = new Date(timestamp);

    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var min = date.getMinutes();
    var hour = date.getHours();

    month = (month < 10 ? '0' : '') + month;
    day = (day < 10 ? '0' : '') + day;
    hour = (hour < 10 ? '0' : '') + hour;
    min = (min < 10 ? '0' : '') + min;

    return day + '.' + month + '.' + year
        + (full ? ' / ' + hour + ':' + min : '');
};

/**
 * Rounds by precision.
 * See: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/round#PHP-Like_rounding_Method
 *
 * @param {number} number to round
 * @param {number} precision precision
 * @returns {number} rounded number
 */
Math.roundPrecision = function (number, precision) {
    var factor = Math.pow(10, precision);
    var tempNumber = number * factor;
    var roundedTempNumber = Math.round(tempNumber);
    return roundedTempNumber / factor;
};

/**
 * Calculates the share of the work/author/journal statistics.
 *
 * @param {object} objects to handle
 * @param {number} sum of all objects
 */
var calcShares = function(objects, sum) {
    for (var element in objects) {
        if (objects.hasOwnProperty(element)) {
            var elementObject = objects[element];
            elementObject.share = elementObject.count / sum;
        }
    }
};

/**
 * Sorts objects by share and adds to new element "_sorted" as array.
 *
 * @param {object} objects to handle
 */
var sortObjects = function(objects) {
    var sortable = [];
    for (var element in objects) {
        if (objects.hasOwnProperty(element)) {
            if (element === 'type') {
                continue;
            }

            var elementObject = objects[element];
            sortable.push([
                element,
                elementObject.count,
                elementObject.share,
                elementObject.ids,
                objects['type']
            ]);
        }
    }

    // Sort by name/title ascending first.
    sortable.sort(function (a, b) {
        if(a[0] < b[0]) return -1;
        if(a[0] > b[0]) return 1;
        return 0;
    });

    // Then sort by share descending.
    sortable.sort(function(a, b) {
        return b[2] - a[2];
    });

    objects._sorted = sortable;
};

/**
 * Fills a statistic data table.
 *
 * @param {object} objects to handle
 * @param {number} sum of the objects
 * @param {object} dataTable data table instance
 */
var fillDataTable = function(objects, sum, dataTable) {
    var aggregated = 0;
    for (var index in objects._sorted) {
        if (objects._sorted.hasOwnProperty(index)) {
            var element = objects._sorted[index];
            aggregated += element[2];
            var row = dataTable.row.add([
                parseInt(index) + 1,
                element[0],
                element[1],
                Math.roundPrecision(element[2], 2).toFixed(2),
                Math.roundPrecision(aggregated, 2).toFixed(2)
            ]).draw(false);
            $(row.node())
                .data('ids', element[3])
                .data('type', element[4])
                .on('click', function() {
                    workAddObjectRowClick($(this));
                });
        }
    }

    $(dataTable.column(2).footer()).html('&Sigma;: ' + sum);
    $(dataTable.column(3).footer()).html('&Sigma;: 1');
};

/**
 * Handles the statistics of work/author/journal.
 *
 * @param {object} objects to handle
 * @param {number} sum of all objects
 * @param {object} dataTable data table instance
 */
var handleStatistics = function(objects, sum, dataTable) {
    calcShares(objects, sum);
    sortObjects(objects);
    fillDataTable(objects, sum, dataTable);
};

/**
 * Determines if an array contains one or more items from another array.
 * See: https://stackoverflow.com/a/25926600
 *
 * @param {array} haystack the array to search.
 * @param {array} array the array providing items to check for in the haystack.
 * @return {boolean} true|false if haystack contains at least one item from arr.
 */
var hasElementOf = function (haystack, array) {
    return array.some(function (v) {
        return haystack.indexOf(v) >= 0;
    });
};

/**
 * Handles allSelectedIds when a row from add works/authors/journal statistic tables
 * is clicked.
 *
 * @param {object} node jQuery node
 */
var allSelectedIds = [];
var workAddObjectRowClick = function(node) {
    var ids = node.data('ids');
    var type = node.data('type');
    var selected = true;
    if (node.hasClass('selected')) {
        node.removeClass('selected');
        selected = false;
    } else {
        node.addClass('selected');
    }

    // Update allSelectedIds.
    for (var i in ids) {
        if (ids.hasOwnProperty(i)) {
            var currentId = ids[i];
            var posId = allSelectedIds.indexOf(currentId);
            if (selected && posId === -1) {
                allSelectedIds.push(currentId);
            } else if (!selected && posId !== -1) {
                allSelectedIds.splice(posId, 1);
            }
        }
    }

    checkWorksAddCheckBoxes();
    selectWorksAddObjectRows(type);
};

/**
 * Handles allSelectedIds when a checkbox from works add is clicked.
 *
 * @param {object} node jQuery node
 */
var workAddCheckBoxClick = function(node) {
    var checkedWorkId = node.data('workId');
    var checked = node.prop('checked');
    var posId = allSelectedIds.indexOf(checkedWorkId);

    if (checked && posId === -1) {
        allSelectedIds.push(checkedWorkId);
    } else if (!checked && posId !== -1) {
        allSelectedIds.splice(posId, 1);
    }

    selectWorksAddObjectRows(null);
};

/**
 * Checks all necessary checkboxes when row from add works/authors/journal statistic tables
 * is clicked.
 */
var checkWorksAddCheckBoxes = function() {
    $('#table_works_add').DataTable().rows().every(function() {
        var tr = $(this.node());
        tr.find('input').prop('checked', hasElementOf(allSelectedIds, tr.data('ids')));
    });
};

/**
 * Selects all (other) rows from add works/authors/journal statistic tables.
 *
 * @param {(string|null)} exceptTable table to ignore (as this call comes from that table anyway)
 */
var selectWorksAddObjectRows = function (exceptTable) {
    var allTables = ['works', 'authors', 'journals'];
    if (exceptTable !== null) {
        allTables.splice(allTables.indexOf(exceptTable), 1);
    }

    for (var i in allTables) {
        if (allTables.hasOwnProperty(i)) {
            $('#table_works_add_' + allTables[i]).DataTable().rows().every(function() {
                var tr = $(this.node());
                var associatedWorkIds = tr.data('ids');
                if (hasElementOf(allSelectedIds, associatedWorkIds)) {
                    tr.addClass('selected');
                } else {
                    tr.removeClass('selected');
                }
            });
        }
    }
};
