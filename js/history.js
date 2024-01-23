function goToRaportByID(vid) {
  window.location.href = "raport.html?vid=" + vid;
}

$(document).ready(function () {
  $("#nav").load("dom_elements/nav.html");
  backToLogin();

  $.ajax({
    url: "scripts/getClosedVotings.php",
    type: "get",
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      204: function (responseObject, textStatus, jqXHR) {
        closedVotingsToTable([]);
      },
      200: function (res) {
        closedVotingsToTable($.parseJSON(res));
      },
    },
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log(textStatus, errorThrown);
  });
});

function closedVotingsToTable(openVoting) {
  openVoting.sort(function (a, b) {
    var a1 = a[4];
    var b1 = b[4];
    if (a1 == b1) return 0;
    return a1 < b1 ? 1 : -1;
  });
  var i = 0;
  $("#closedVotingsTable").html("");
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
      "\
        </td>\
        <td>" +
      voting[4] +
      '</td>\
        <td class="text-center">\
          <button onClick="goToRaportByID(' +
      voting[0] +
      ')" class="btn btn-secondary btn-sm">\
            <span class="material-symbols-outlined"\
              >quick_reference_all</span\
            >\
          </button>\
        </td>\
      </tr>';
    i++;
    $("#closedVotingsTable").append($v);
  });
}
