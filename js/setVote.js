const vote_id = new URLSearchParams(window.location.search).get("vid");

$(document).ready(function () {
  backToLogin();
  tkn = parseJwt();

  $("#u_name").html(tkn["name"]);
  $("#u_last_name").html(tkn["lastname"]);
  $("#u_email").html(tkn["email"]);

  values = [{ name: "vid", value: vote_id }];

  $.ajax({
    url: "scripts/getVotingDetails.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
  })
    .done(function (response) {
      response = $.parseJSON(response);
      console.log(response);
      $("#v_title").html(response[1]);
      $("#v_desc").html(response[2]);
      $("#v_start").html(response[3]);
      $("#v_end").html(response[4]);

      switch (response[5]) {
        case "0":
          $("#v_kworum").html("Bez kworum");
          break;
        case "1":
          $("#v_kworum").html("Kworum 2/3");
          break;
        case "2":
          $("#v_kworum").html("Kworum 1/2");
          break;
      }
      switch (response[6]) {
        case "0":
          $("#v_zwykle").html("Głosowanie niestandardowe");
          break;
        case "1":
          $("#v_zwykle").html("Głosowanie zwykłe - większość bezwzględna");
          break;
        case "2":
          $("#v_zwykle").html("Głosowanie zwykłe - większość względna");
          break;
      }
      $("#voted-option-display").html(response[8]);
      response[7].forEach((option) => {
        var opt =
          ' <option value="' + option[0] + '">' + option[1] + "</option>";

        $("#v_opcje").append(opt);
      });
      end = new Date(response[4]);
      timer = setInterval(showRemaining, 1000);
    })
    .fail(function (e, msg) {
      //console.log(e, msg);
    });
});

$("#setvote_form").validate({
  errorPlacement: function (label, element) {
    label.addClass("text-danger mt-1");
    label.insertAfter(element);
  },
  wrapper: "small",
  submitHandler: function (form) {
    $("#oddaj-glos").attr("disabled", "disabled")
      .html(`<div class="spinner-border text-light" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>`);
    $("#v_opcje").attr("disabled", "disabled");
    values.push({ name: "vote_id", value: vote_id });
    values.push({
      name: "option_id",
      value: $("#v_opcje").find(":selected").val(),
    });
    $.ajax({
      url: "scripts/setVote.php",
      type: "post",
      data: $.param(values),
      headers: {
        Authorization: `Bearer ${Cookies.get("jwt")}`,
      },
      statusCode: {
        201: function (res) {
          appendAlert("Twój głos został zapisany!", "success");
          $("#voted-option-display").html($("#v_opcje option:selected").text());
          $("#oddaj-glos").html("Dodano!").addClass("btn-success");
        },
        202: function (res) {
          appendAlert("Twój głos został zmieniony i zapisany!", "success");
          $("#voted-option-display").html($("#v_opcje option:selected").text());
          $("#oddaj-glos").html("Dodano!").addClass("btn-success");
        },
        406: function (res) {
          appendAlert(
            "Głosowanie zamknięte - nie można oddać głosu.",
            "danger"
          );
          $("#oddaj-glos").removeAttr("disabled").html("Oddaj głos");
        },
        403: function (res) {
          appendAlert("Administrator nie może głosować.", "danger");
          $("#oddaj-glos").removeAttr("disabled").html("Oddaj głos");
        },
      },
    })
      .done(function (res) {
        //console.log(res);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        //console.log(textStatus, errorThrown);
      });
  },
});

var end;
var _second = 1000;
var _minute = _second * 60;
var _hour = _minute * 60;
var _day = _hour * 24;
var timer;

function showRemaining() {
  var now = new Date();
  var distance = end - now;
  if (distance < 0) {
    clearInterval(timer);
    document.getElementById("countdown").innerHTML = "Zamknięte!";
    $("#countdown").removeClass("text-bg-info");
    $("#countdown").addClass("text-bg-danger");
    $("#glos").prop("disabled", true);
    $("#oddaj-glos").prop("disabled", true);

    return;
  }
  var days = Math.floor(distance / _day);
  var hours = Math.floor((distance % _day) / _hour);
  var minutes = Math.floor((distance % _hour) / _minute);
  var seconds = Math.floor((distance % _minute) / _second);

  document.getElementById("countdown").innerHTML = days + " dni ";
  document.getElementById("countdown").innerHTML += hours + " godzin ";
  document.getElementById("countdown").innerHTML += minutes + " minut ";
}
