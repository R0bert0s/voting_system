$("#login_from").validate({
  errorPlacement: function (label, element) {
    label.addClass("text-danger mt-1");
    label.insertAfter(element);
  },
  wrapper: "small",
  submitHandler: function (form) {
    values = $("#login_from").serialize();
    $.ajax({
      url: "scripts/login.php",
      type: "post",
      headers: {
        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      data: values,
      success: function (res) {
        Cookies.set("jwt", res);
        window.location.href = "/wwx9438/glosowanie/dostepneglosowania.html";
      },
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);
      appendAlert(
        "Nieprawidłowy dane lgowania lub konto dezaktywowane.",
        "danger"
      );

      Cookies.remove("jwt");
    });
  },
});
