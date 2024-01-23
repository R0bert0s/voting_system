const vote_id = new URLSearchParams(window.location.search).get("vid");
$(document).ready(function () {
  oofNotSekretarz();
  tkn = parseJwt();
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
      displayVotingDetails(response);
      fetchVotes();
      fetchRaport();
    })
    .fail(function (e, msg) {});
});

function generateRaport() {
  values = [{ name: "vid", value: vote_id }];

  $.ajax({
    url: "scripts/generateRaport.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
  })
    .done(function (res) {
      fetchRaport();
    })
    .fail(function (res) {
      console.log(res);
    });
}

function fetchVotes() {
  $.ajax({
    url: "scripts/getVotingResult.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
  }).done(function (result_res) {
    result_res = $.parseJSON(result_res);
    result_res.sort(function (a, b) {
      var a1 = parseInt(a[1]);
      var b1 = parseInt(b[1]);
      if (a1 == b1) return 0;
      return a1 < b1 ? 1 : -1;
    });
    i = 0;
    result_res.forEach((wynik) => {
      //let badge_style = i == 0 ? "bg-success" : "bg-secondary";
      let badge_style = "bg-secondary";
      let row_style = i == 0 ? "list-group-item-success" : "";
      let w =
        `<li class="list-group-item ">
                      <b>` +
        wynik[0] +
        `</b>
                      <span class="badge ` +
        badge_style +
        ` float-end">` +
        wynik[1] +
        `</span>
                    </li>`;
      $("#wynik-opcje-list").append(w);
      i += 1;
    });
  });
}

function fetchRaport() {
  values = [{ name: "vid", value: vote_id }];

  $.ajax({
    url: "scripts/getRaport.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      404: function () {
        generateRaport();
      },
    },
  })
    .done(function (res) {
      res = $.parseJSON(res);
      displayRaport(res);
    })
    .fail(function (res) {
      console.log(res);
    });
}

function displayRaport(data) {
  let user_not_voting = parseInt(data.all_users) - parseInt(data.voting_users);
  let kworum_per_is = parseInt(data.voting_users) / parseInt(data.all_users);
  let kworum_ans = "Nie";
  if (data.kworum_ok == "1") {
    kworum_ans = "Tak";
    $("#kworum_ok").addClass("text-bg-success").removeClass("text-bg-danger");
    $("#is_ok").addClass("text-bg-success").removeClass("text-bg-secondary");
  } else {
    $("#kworum_ok").addClass("text-bg-danger").removeClass("text-bg-secondary");
    $("#is_ok").addClass("text-bg-danger").removeClass("text-bg-secondary");
  }
  $("#is_ok").html(kworum_ans);
  let kworum_per_msg = "Brak";
  if (data.kworum_type == 1) {
    kworum_per = 2 / 3;
    kworum_per_msg = "66.66%";
  } else if (kworum_type == 2) {
    kworum_per = 0.5;
    kworum_per_msg = "50%";
  }

  $("#winner_vote").html(data.winner_opt);
  $("#winner_vote_count").html(
    data.winner_votes == 0 ? "-" : data.winner_votes
  );
  $("#kworum_voting_poepole").html(data.voting_users);
  $("#kworum_not_voting_poepole").html(user_not_voting);
  $("#kworum_poepole").html(data.all_users);
  $("#kworum_need").html(kworum_per_msg);
  $("#kworum_is").html((kworum_per_is * 100).toFixed(2) + "%");
  $("#kworum_ok").html(kworum_ans);
  $("#votes_count").html("Oddane głosy: " + data.voting_users);
  $("#raport-gen_date").html(
    " (Raport wygenerowany dania: " + data.generation_time + ")"
  );
}

function displayVotingDetails(data) {
  $("#v_title").html(data[1]);
  $("#v_desc").html(data[2]);
  $("#v_start").html(data[3]);
  $("#v_id").html(data[0]);
  $("#v_end").html(data[4]);

  kworum_type = parseInt(data[5]);
  vote_count = parseInt(data[9]);
  user_count = parseInt(data[10]);
  wzgledne_type = parseInt(data[6]);

  switch (data[5]) {
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
  switch (data[6]) {
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
}

var opt = {
  margin: 0.1,
  filename: "raport_id_" + vote_id + ".pdf",
  image: { type: "jpeg", quality: 0.98 },
  html2canvas: {
    scale: 2,
    scrollY: 0,
  },
  jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
};
$("#raport-pdf-download").click(function () {
  if ($(window).width() < 768) {
    delete opt.html2canvas.window;
    opt.html2canvas.windowWidth = 800;
    opt.html2canvas.height = 1000;
  } else {
    delete opt.html2canvas.windowWidth;
    delete opt.html2canvas.height;
    opt.html2canvas.width = 800;
  }

  $("#raport-pdf-download").addClass("d-none").removeClass("d-block");
  var element = document.getElementById("raport-row");
  html2pdf().set(opt).from(element).save();
  setTimeout(function () {
    $("#raport-pdf-download").removeClass("d-none").addClass("d-block");
  }, 1000);
});
