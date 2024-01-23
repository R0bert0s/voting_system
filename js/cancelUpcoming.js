$(document).ready(function () {
  backToLogin();
  myModalAlternative = new bootstrap.Modal("#exampleModal");

  $("#nav").load("dom_elements/nav.html");
  getUpcomingVotings();
});

function getUpcomingVotings() {
  $.ajax({
    url: "scripts/getUpcomingVotings.php",
    type: "get",
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      204: function (responseObject, textStatus, jqXHR) {
        openVotingsToTable([]);
      },
      200: function (res) {
        openVotingsToTable($.parseJSON(res));
      },
    },
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log(textStatus, errorThrown);
  });
}

function openVotingsToTable(openVoting) {
  openVoting.sort(function (a, b) {
    var a1 = a[5];
    var b1 = b[5];
    if (a1 == b1) return 0;
    return a1 > b1 ? 1 : -1;
  });
  var i = 0;
  $("#openVotingsTable").html("");
  openVoting.forEach((voting) => {
    $v =
      ' <tr>\
              <th scope="row">' +
      voting[0] +
      "</th>\
       <td>\
                " +
      voting[1] +
      " \
              </td>\
              <td>" +
      voting[3] +
      "</td>\
              <td>" +
      voting[4] +
      '</td> <td>\
      <span onClick="deleteVoting(' +
      voting[0] +
      ')" class="delete-voting-button material-symbols-outlined">\
delete_forever\
</span></td>\
            </tr>"';
    $("#openVotingsTable").append($v);
    i++;
  });
  $("#openVotingsCounter").html(i);
}

function deleteVoting(vid) {
  values = [
    {
      name: "vid",
      value: vid,
    },
  ];

  myModalAlternative.show();
  $("#deleteButton").on("click", function () {
    $.ajax({
      url: "scripts/cancelUpcoming.php",
      type: "post",
      data: values,
      headers: {
        Authorization: `Bearer ${Cookies.get("jwt")}`,
      },
      statusCode: {
        204: function (responseObject, textStatus, jqXHR) {
          appendAlert("Głosowanie usunięte!", "success");
        },
        200: function (res) {
          appendAlert("Głosowanie usunięte!", "success");
        },
        501: function (res) {
          console.log(res);
          appendAlert("Błąd w trakcie usuwania!", "danger");
        },
      },
    })
      .done(function () {
        myModalAlternative.hide();
        getUpcomingVotings();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        myModalAlternative.hide();
        appendAlert("Błąd w trakcie usuwania!", "danger");
      });
  });
}
