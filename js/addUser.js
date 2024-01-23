$(document).ready(function () {
  oofNotAnAdmin();
});

$("#new_user_form").validate({
  errorPlacement: function (label, element) {
    label.addClass("text-danger mt-1");
    label.insertAfter(element);
  },
  wrapper: "small",
  submitHandler: function (form) {
    values = $("#new_user_form").serializeArray();
    $("#add-user-button").attr("disabled", "disabled").html("Poczekaj");
    $("input").prop("disabled", true);

    $.ajax({
      url: "scripts/addUser.php",
      type: "post",
      data: $.param(values),
      headers: {
        Authorization: `Bearer ${Cookies.get("jwt")}`,
      },
      statusCode: {
        201: function (res) {
          appendAlert("Użytkownik został dodany!", "success");
          $("#add-user-button").html("Dodano!").addClass("btn-success");
        },
        202: function (res) {
          appendAlert("Użytkownik został dodany!", "success");
          $("#add-user-button").html("Dodano!").addClass("btn-success");
        },
        400: function (res) {
          appendAlert("Brak uprawnień.", "danger");
          $("#add-user-button").html("Brak uprawnień!").removeAttr("disabled");
        },
        401: function (res) {
          appendAlert("Brak uprawnień.", "danger");
          $("#add-user-button").html("Brak uprawnień!").removeAttr("disabled");
        },
        409: function (res) {
          appendAlert(
            "<b>Nie udało się!</b> Użytkownik o takim adresie email już istnieje.",
            "warning"
          );
          $("#add-user-button").html("Dodaj").removeAttr("disabled");
          $("input").prop("disabled", false);
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
      })
      .fail(function (e, msg, responseText) {
        appendAlert("Brak uprawnień.", "danger");
      });
  },
});
