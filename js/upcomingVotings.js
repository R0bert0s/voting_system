$(document).ready(function () {
  backToLogin();
  $("#nav").load("dom_elements/nav.html");
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
});

var voting_dates = [];

function openVotingsToTable(openVoting) {
  openVoting.sort(function (a, b) {
    var a1 = a[5];
    var b1 = b[5];
    if (a1 == b1) return 0;
    return a1 > b1 ? 1 : -1;
  });
  var i = 0;
  $("#openVotingsTable").html("");
  const c_date = new Date();

  openVoting.forEach((voting) => {
    let v_date = new Date(voting[3]);
    if (c_date.getMonth() === v_date.getMonth()) {
      voting_dates.push(v_date.getDate());
    }
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
      voting[3] +
      "</td>\
            <td>" +
      voting[4] +
      '</td>\
          </tr>"';
    $("#openVotingsTable").append($v);
    i++;
  });
  $("#openVotingsCounter").html(i);
  showCalendar();
}

function showCalendar() {
  const c_date = new Date();
  var firstDay = new Date(c_date.getFullYear(), c_date.getMonth(), 1);
  var lastDay = new Date(c_date.getFullYear(), c_date.getMonth(), 0);

  var day = 1;
  for (i = firstDay.getDay() - 1; i < lastDay.getDate(); i++) {
    if (day == c_date.getDate()) {
      $("#calendar-cell-" + i).addClass("bg-primary text-bg-primary fw-bold");
    } else if (voting_dates.includes(day)) {
      $("#calendar-cell-" + i).addClass("bg-warning text-bg-warning fw-bold");
    }
    $("#calendar-cell-" + i).html(day);
    day++;
  }
}
