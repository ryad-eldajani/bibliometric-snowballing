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
            var elementObject = objects[element];

            sortable.push([element, elementObject.count, elementObject.share]);
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
            dataTable.row.add([
                parseInt(index) + 1,
                element[0],
                element[1],
                Math.roundPrecision(element[2], 2).toFixed(2),
                Math.roundPrecision(aggregated, 2).toFixed(2)
            ]).draw(false);
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
 * Checks, if a number is numeric and integer.
 * See: https://stackoverflow.com/a/9716488
 *
 * @param n number to check
 * @returns {boolean} true, if numeric.
 */
function isNumeric(n) {
    return !isNaN(parseInt(n)) && isFinite(n);
}
