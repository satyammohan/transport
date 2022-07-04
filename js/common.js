$(function () {
    jQuery.browser = {};
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
});
function print() {
    $('.print_content').jqprint();
}
function download() {
    flname = $("#excelfile").val() ? $("#excelfile").val() : "accounts";
    $("#report").table2excel({
        exclude: ".noExl",
        name: "Worksheet",
        filename: flname
    });
}
$(document).ready(function () {
    $('.openPopup').on('click', function () {
        var dataURL = $(this).attr('data-href');
        $('.modal-body').load(dataURL, function () {
            $('#myModal').modal({ show: true });
        });
    });
    tbl_handler();
});

function fancy_handler(id) {
    $("." + id).fancybox({
        'scrolling': 'yes',
        'type': 'ajax',
        'titleShow': false,
        'autoScale': false,
        'hideOnContentClick': false
    });
}
function fancy_handler_iframe(id) {
    $("." + id).fancybox({
        'scrolling': 'no',
        'type': 'ajax',
        'titleShow': false,
        'autoScale': false,
        'hideOnContentClick': false,
        'type' : 'iframe'
    });
}
function tbl_handler() {
    $('#dataTable').DataTable();
}
function update_status(tbl, id, row_status, list_status) {
    let url = "index.php?module=" + tbl + "&func=update_flag&table=" + tbl + "&id=" + id + "&row_status=" + row_status + "&list_status=" + list_status;
    window.location.href = url;
}
function update_listing(tbl, id) {
    if (id > 1)
        window.location.href = "index.php?module=" + tbl + "&func=listing";
    else
        window.location.href = "index.php?module=" + tbl + "&func=listing&status=" + id;
}
function cancelthis(msg, url, id) {
    if (confirm(msg)) {
        $.post(url, { id: id, ce: 0 }, function (res) {
            $(".modal-body").html(res);
            $('#myModal').modal('show');
        });
    }
}

function callauto(id, url, hid, headers = '') {
    $("#" + id).addClass("ac_dropdown").focus(function () {
        $("#" + id).select();
    });
    $(".ac_dropdown").bind( "click", function() {
        $("#" + this.id).autocomplete("search", "a");
    });
    $("#" + id).autocomplete({
        autoFocus: true,
        open: function () {
            if (headers) {
                col_names = "";
                $.each(headers, function (key, value) {
                    firstcol = (key == 0) ? "ACFirst" : "ACColumn";
                    col_names += "<div class='" + firstcol + "'>" + value + "</div>";
                });
                $('ul.ui-autocomplete').prepend("<li><div class='ACRow ACtitle'>" + col_names + "</div></li>");
            }
        },
        source: function (request, response) {
            var name = $("#" + id).val();
            $.ajax({
                url: url,
                dataType: "json",
                data: { filter: name },
                success: function (data) {
                    response(jQuery.map(data, function (item) {
                        if (typeof (hid) == "string") {
                            return { value: item.name, key: item.id };
                        } else {
                            return item;
                        }
                    }));
                }
            });
        },
        select: function (event, ui) {
            if (typeof (hid) == "string") {
                $("#" + hid).val(ui.item.key);
            } else {
                for (j in hid) {
                    $("#" + hid[j]).val(ui.item["col" + j]).trigger('change');
                }
            }
        }
    });
}