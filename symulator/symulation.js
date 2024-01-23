$(document).ready(function () {
  oofNotAnAdmin();
});
$("#add_user_form").validate({
  errorPlacement: function (label, element) {
    label.addClass("text-danger mt-1");
    label.insertAfter(element);
  },
  wrapper: "small",
  submitHandler: function (form) {
    $.ajax({
      url: "symulator/add100users.php",
      type: "post",
      headers: {
        Authorization: `Bearer ${Cookies.get("jwt")}`,
      },
      statusCode: {
        201: function (res) {
          appendAlert("Użytkownicy zostali dodani!", "success");
          $("#add-user-button").html("Dodano!").addClass("btn-success");
        },

        501: function (res) {
          console.log(res.responseText);
          appendAlert("Błąd serwera! Proszę spróbować później.", "danger");
          $("#add-user-button").html("Dodaj").removeAttr("disabled");
          $("input").prop("disabled", false);
        },
      },
    })
      .done(function (response) {
        console.log(response);
      })
      .fail(function (e, msg, responseText) {
        console.log(msg);
        appendAlert("Brak uprawnień.", "danger");
      });
  },
});

$("#add_voting_form").validate({
  rules: {
    sym_voting_type: { required: true },
    sym_kworum_type: { required: true },
    syn_kworum_res: { required: true },
    sym_end: { required: true },
    sym_start: { required: true },
  },
  errorPlacement: function (error, element) {
    error.appendTo(element.parent("div").find(".error_toggle"));
  },
  submitHandler: function (form) {
    console.log($("#add_voting_form").serializeArray());
    $("#generateButton").attr("disabled", true)
      .html(`<div class="spinner-border text-light" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>`);
    $.ajax({
      url: "symulator/addSymVoting.php",
      type: "post",
      data: $("#add_voting_form").serializeArray(),
      headers: {
        Authorization: `Bearer ${Cookies.get("jwt")}`,
      },
      statusCode: {
        201: function (res) {
          appendAlert("Wygenerowano głosowanie", "success");
          $("#add-user-button").html("Dodano!").addClass("btn-success");
        },

        501: function (res) {
          appendAlert("Błąd serwera! Proszę spróbować później.", "danger");
          $("#add-user-button").html("Dodaj").removeAttr("disabled");
          $("input").prop("disabled", false);
        },
      },
    })
      .done(function (response) {
        console.log(response);
        $("#generateButton")
          .attr("disabled", false)
          .html(`Wygeneruj losowanie`);
      })
      .fail(function (e, msg, responseText) {
        console.log(e, msg);
        appendAlert("Brak uprawnień.", "danger");
      });
  },
});

$("input[name=sym_kworum_type]").change(function () {
  if ($("#sym_kworum_type_1").is(":checked")) {
    $("#syn_kworum_res_2").prop("disabled", true).prop("checked", false);
  } else {
    $("#syn_kworum_res_2").prop("disabled", false);
  }
});
