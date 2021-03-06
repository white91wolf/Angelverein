var element_counter = 1;

$(document).ready(function() {
    $("#addFishGroup").click(function() {
        addField();
    });

    $("#removeFishGroup").click(function() {
        removeField();
    });
});

function addField() {
    $.ajax({
        type: "POST",
        url: baseUrl + "/index.php/backend/fangliste/newfishgroup/format/html",
        data: "counter=" + element_counter++,
        success: function(newGroup) {
            $("#submit-element").before(newGroup);
            setHiddenFieldValue(element_counter);
        }
    });
}

function removeField() {
    if (element_counter > 1) {
        var pattern = '*[id^=element_' + (--element_counter) + '_]';
        console.log(pattern);
        $(pattern).remove();
        setHiddenFieldValue(element_counter);
    }
}

function setHiddenFieldValue(val) {
    $("#group_counter").attr("value", val);
}