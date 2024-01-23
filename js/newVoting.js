var options = [];
var voting_type;

$(document).ready(function () {
  oofNotSekretarz();
});

function addOption(name, def = true) {
  var id = name.replace(/\s+/g, "-").toLowerCase();
  $("label[for='dodaneopcje']").html("Dodane opcje");
  options.push(name);
  if (!def) {
    if (options.length <= 6) {
      var newItem =
        "<li id='" +
        id +
        "' class='list-group-item'>" +
        name +
        " <div style='float: right; width: 25px;'><span class='delete-option material-symbols-outlined' onClick='deleteOption(\"" +
        id +
        "\")'>backspace</span></div></li>";
      $("#dodaneopcje").prepend(newItem);
    }
    $("#option-name").val("");
  } else {
    var newItem =
      "<li id='" + id + "' class='list-group-item'>" + name + "</li>";
    $("#dodaneopcje").append(newItem);
  }

  if (voting_type == 3 && options.length == 6) {
    $("#add-option").attr("disabled", "disabled");
  }
}

function deleteAll() {
  $("#dodaneopcje").html("");
  options = [];
}

function deleteOption(o) {
  $("#" + o).remove();
  options.pop(o);
  if (options.length <= 6) {
    $("#add-option").removeAttr("disabled");
  }
}

$("#pre-created-options").on("change", function () {
  switch ($(this).val()) {
    case "1":
      voting_type = 1;
      deleteAll();
      addOption("Za");
      addOption("Przeciw");
      $("#add-option").attr("disabled", "disabled");
      break;
    case "2":
      voting_type = 2;
      deleteAll();
      addOption("Za");
      addOption("Przeciw");
      addOption("Wstrzymuję się");
      $("#add-option").attr("disabled", "disabled");
      $("input[name='toggle-wiekszosc']").attr("disabled", false);
      break;
    case "3":
      voting_type = 3;
      deleteAll();
      $("#add-option").removeAttr("disabled");
      addOption("Wstrzymuję się");
      $("input[name='toggle-wiekszosc']")
        .prop("checked", false)
        .attr("disabled", true);

      break;
  }
});

$("#add-option").click(function (e) {
  e.preventDefault();
  var name = $("#option-name").val();
  if (name != "") addOption(name, false);
});

$("#new_voting_form").validate({
  errorPlacement: function (label, element) {
    if (element.attr("name") == "toggle-wiekszosc") {
      label.appendTo(element.parent("div").find("#error_toggle"));
    } else {
      label.addClass("text-danger mt-1");
      label.insertAfter(element);
    }
  },
  wrapper: "small",

  submitHandler: function (form) {
    $("#dodaj-glosowanie").attr("disabled", "disabled")
      .html(`<div class="spinner-border text-light" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>`);

    values = $("#new_voting_form").serializeArray();
    values.push(
      { name: "kworum", value: $("input[name='toggle-kworum']:checked").val() },
      {
        name: "zwykle",
        value: $("input[name='toggle-wiekszosc']:checked").val(),
      },
      { name: "options", value: options },
      { name: "v_type", value: voting_type }
    );
    $("input").prop("disabled", true);
    $("select").prop("disabled", true);
    $("textarea").prop("disabled", true);
    $.ajax({
      url: "scripts/addVoting.php",
      type: "post",
      data: $.param(values),
      headers: {
        Authorization: `Bearer ${Cookies.get("jwt")}`,
      },
      statusCode: {
        401: function (res) {
          appendAlert("Brak uprawnień!", "danger");
          $("#voted-option-display").html($("#v_opcje option:selected").text());
          $("#dodaj-glosowanie")
            .html("Dodaj")
            .removeClass("btn-success")
            .removeAttr("disabled");
          $("input").prop("disabled", false);
          $("select").prop("disabled", false);
          $("textarea").prop("disabled", false);
        },
      },
    })
      .done(function (response) {
        console.log(response);
        $("#dodaj-glosowanie").html("Dodano!").addClass("btn-success");
        appendAlert("Dodano głosowanie! ", "success");
      })
      .fail(function (e, msg) {
        //console.log(e, msg);
      });
  },
});
