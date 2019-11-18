var Student = function (name, surname, age, course) {
    this.name = name;
    this.surname = surname;
    this.age = age;
    this.course = course;

    this.GetOlder = function (years) {
        this.age += years;
    };
    this.ChangeSurname = function (surname) {
        this.surname = surname
    };
    this.MoveToSecondCourse = function() {
        this.course++;
    }
};

function my_min(vals) {
    var total_min = +Infinity;
    for (var i in vals) {
        if(vals.hasOwnProperty(i) && vals[i] < total_min)
            total_min = vals[i];
    }
    return total_min;
}



$.validator.addMethod(
    "regex",
    function (value, element, regexp) {
        var okay = this.optional(element) || regexp.test(value);
        $('#infobox').text(okay ? "" : "Щось не те! Ім'я може бути лише латиницею, а пошта має бути на домені gmail.com");
        return okay
    },
    "Перевірте коректність вводу"
);
$.validator.messages.required = "Обов'язкове поле";

function validate_names() {
    var inputs = {
        n1: $('#name-1'),
        n2: $('#name-2'),
        n3: $('#name-3'),
        em: $('#email')
    };

    var r1 = /@gmail.com$/;
    var r2 = /^[A-Za-z\-']*$/;

    var checks = {
        'name-1': r2,
        'name-2': r2,
        'name-3': r2,
        'email': r1
    };

    var edited = $(this);
    var id = edited.prop("id");

    var okay = checks[id].test(edited.val());
    console.log(id, edited.val(), okay, checks[id]);
    if (!okay) {
        alert("Щось не те! Ім'я може бути лише латиницею, а пошта має бути на домені gmail.com")
        edited.focus();
    }
}

function add_regex(selector, regex) {
    $(selector).rules("add", {regex: regex})
}

function async_submit() {
    var data = {
        "name-1": $('#name-1').val(),
        "name-2": $('#name-2').val(),
        "name-3": $('#name-3').val(),
        "gradyear": $('#gradyear').val(),
        "phone": $('#phone').val(),
        "email": $('#email').val(),
        "notifs": $("#notifs").val()
    };
    $.get("../lab_php/reg_handler.php?noui=1", data, function (res) {
        $('#result').show().text(res);
    });
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("result-1").innerHTML = this.responseText;
        }
    };
    var postfix = "";
    for (var f in data) {
        postfix += (postfix.length === 0 ? "?" : "&") + encodeURIComponent(f) + "=" + encodeURIComponent(data[f]);
    }
    console.log(postfix);
    xhttp.open("GET", "../lab_php/reg_handler.php" + postfix, true);
    xhttp.send();
}

$(document).ready(function () {
    // Validation
    var r1 = /@gmail.com$/;
    var r2 = /^[A-Za-z\-']*$/;
    add_regex("#email", r1);
    add_regex("#name-1", r2);
    add_regex("#name-2", r2);
    add_regex("#name-3", r2);

    // $('input').change(validate_names);

    // Other
    // $('input[id^=g]').change(function () {
    //     $('input[id^=g]').prop("checked", false);
    //     $(this).prop("checked", true);
    // });
    // $('#calculate1').click(function(){
    //     var inputs = $('input[type=text]');
    //     var resdiv = $('#result');
    //     resdiv.text("");
    //     var counter = 0;
    //     for (var i in inputs) {
    //         if (inputs.hasOwnProperty(i)) {
    //             resdiv.text( resdiv.text() + (++counter) + " - " + $(inputs[i]).prop("name") + "\r\n");
    //         }
    //     }
    //     resdiv.show();
    // });

    $('#fill').click(function() {
        $("#name-1").val("Name");
        $("#name-2").val("SurName");
        $("#name-3").val("FatherName");
        $("#gradyear").val(2000);
        $("#phone").val("+3805000000000");
        $("#email").val("mail@gmail.com");
    });

    // Submit
    $('#async-submit').click(async_submit);

    // Popup
    var popup = $('#popup');
    var popup_close_btn = $('#close-popup');
    popup_close_btn.click(function () {
        popup.hide()

    });
    // setTimeout(function() {
    //     popup.show()
    // }, 5 * 1000)
});