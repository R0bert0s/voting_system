$("#nav").load("dom_elements/nav.html");
$("#footer-container").load("dom_elements/footer.html");

function appendAlert(message, type) {
  $("#liveAlertPlaceholder").html(
    `<div class="alert-css alert alert-${type} alert-dismissible" role="alert">
        <div>${message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`
  );
}

function parseJwt(token = Cookies.get("jwt")) {
  var base64Url = token.split(".")[1];
  var base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
  var jsonPayload = decodeURIComponent(
    window
      .atob(base64)
      .split("")
      .map(function (c) {
        return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
      })
      .join("")
  );

  return JSON.parse(jsonPayload);
}

function logout() {
  Cookies.remove("jwt");
  window.location.href = "/wwx9438/glosowanie/login.html";
}

function backToLogin() {
  if (Cookies.get("jwt") == null) {
    window.location.href = "/wwx9438/glosowanie/login.html";
  }
}

function oofNotAnAdmin() {
  if (parseJwt()["admin"] != true) {
    window.location.href = "/wwx9438/glosowanie/dostepneglosowania.html";
  }
}

function oofNotSekretarz() {
  if (parseJwt()["admin"] != true && parseJwt()["sekretarz"] != true) {
    window.location.href = "/wwx9438/glosowanie/dostepneglosowania.html";
  }
}

function openVotingByID(id) {
  window.location.href = "/wwx9438/glosowanie/glosowanie.html?vid=" + id;
}
