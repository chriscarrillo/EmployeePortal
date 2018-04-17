function selectGrade(row, gradeID) {
    var val = gradeID;
    var sel = row;
    var opts = sel.options;
    for (var opt, j = 1; opt = opts[j]; j++) {
        if (opt.value == val) {
            sel.selectedIndex = j;
            break;
        }
    }
}

function select(sel, val) {
    $(sel).val(val);
}

$(document).ready(function($) {
    $(".studentRow").click(function() {
       window.location = $(this).data("href");
    });
    
    $(".courseRow").click(function() {
       window.location = $(this).data("href");
    });
});