$(document).ready(function () {
  backToLogin();
  $("#nav").load("dom_elements/nav.html");
  $.ajax({
    url: "scripts/getOpenVotings.php",
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
});

function openVotingsToTable(openVoting) {
  openVoting.sort(function (a, b) {
    var a1 = a[4];
    var b1 = b[4];
    if (a1 == b1) return 0;
    return a1 > b1 ? 1 : -1;
  });
  var i = 0;
  $("#openVotingsTable").html("");
  openVoting.forEach((voting) => {
    $v =
      ' <tr>\
            <th scope="row">' +
      (i + 1) +
      "</th>\
            <td>" +
      voting[1] +
      "</td>\
            <td>\
              " +
      voting[2] +
      " \
            </td>\
            <td>" +
      voting[4] +
      '</td>\
            <td><button onClick="openVotingByID(' +
      voting[0] +
      ')" class="btn btn-primary">Głosuj</button></td>\
          </tr>"';
    $("#openVotingsTable").append($v);
    i++;
  });
  $("#openVotingsCounter").html(i);
}
