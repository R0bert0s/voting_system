let myModalAlternative;

function fetchUserList() {
  $.ajax({
    url: "scripts/getUserList.php",
    type: "get",
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      204: function (responseObject, textStatus, jqXHR) {
        userListToTable([]);
      },
      200: function (res) {
        userListToTable($.parseJSON(res));
      },
    },
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log(textStatus, errorThrown);
  });
}

$(document).ready(function () {
  myModalAlternative = new bootstrap.Modal("#exampleModal");
  oofNotAnAdmin();
  fetchUserList();
  $("form").submit(function (e) {
    e.preventDefault();
    if ($("input[name='toggle-role']:checked").val() != undefined) {
      callChangeRole();
      $("input[name=toggle-role]").prop("checked", false);
      myModalAlternative.hide();
    }
  });
});

function userListToTable(userList) {
  var i = 0;
  $("#userListTable").html("");
  userList.forEach((user) => {
    $v =
      ` 
    <tr class="user-row">
        <td>
            ` +
      i +
      `
        </td>
        <td>
            ` +
      user[1] +
      `
        </td>
        <td>
            ` +
      user[2] +
      `
        </td>
        <td>
            ` +
      user[3] +
      `
        </td>
        <td>
            ` +
      user[4] +
      `
        </td>
        <td class="text-center">
            ` +
      (user[5] == null
        ? ""
        : `<span class="text-primary material-symbols-outlined">clinical_notes</span>`) +
      `
        </td>
        <td class="text-center">
            ` +
      (user[6] == null
        ? ""
        : `<span  class="text-success material-symbols-outlined">shield_person</span>`) +
      `
        </td>
        <td class="text-center">
        <span onClick="changeRole(` +
      user[0] +
      ` ,'` +
      user[1] +
      `',' ` +
      user[2] +
      `')" style="cursor: pointer" class="edit-user-button material-symbols-outlined">
        person_edit
        </span>
        </td>
        <td class="text-center">
        <span onClick="resetPass(` +
      user[0] +
      `)"  class="material-symbols-outlined password-user-button" style="cursor: pointer">
          lock_reset
        </span>


        </td>
        <td class="text-center">
          <span onClick="deactivateUser(` +
      user[0] +
      `)" class="delete-user-button text-danger material-symbols-outlined " style="cursor: pointer">
          person_remove
          </span>
        </td>

    </tr>`;

    $("#userListTable").append($v);
    i++;
  });
  $("#userListCounter").html(i);
}

function callChangeRole() {
  uid = $("#u_id").val();
  role = $("input[name='toggle-role']:checked").val();
  values = [
    {
      name: "uid",
      value: uid,
    },
    {
      name: "role",
      value: role,
    },
  ];

  $.ajax({
    url: "scripts/changeRole.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      200: function (res) {
        fetchUserList();
        appendAlert("Zmieniono uprawnienia!", "success");
      },
      403: function (res) {
        appendAlert(
          "Ten użytkownik już głosował i nie może być administratorem!",
          "danger"
        );
      },
      409: function (res) {
        appendAlert("Nadaj inną rolę!", "danger");
      },
    },
  })
    .done(function (res) {})
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR, textStatus, errorThrown);
    });
}

function changeRole(uid, uname, usname) {
  $("#u_name").val(uname);
  $("#u_sname").val(usname);
  $("#u_id").val(uid);

  myModalAlternative.show();
}

function deactivateUser(uid) {
  values = [
    {
      name: "uid",
      value: uid,
    },
  ];
  $.ajax({
    url: "scripts/deactivateUser.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      200: function (res) {
        fetchUserList();
        appendAlert("Użytkownik dezaktywowany!", "success");
      },
      409: function (res) {
        appendAlert("Błąd!", "Danger");
      },
    },
  })
    .done(function (res) {
      console.log(res);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR, textStatus, errorThrown);
    });
}

function resetPass(uid) {
  values = [
    {
      name: "uid",
      value: uid,
    },
  ];
  $.ajax({
    url: "scripts/resetPass.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
    statusCode: {
      200: function (res) {
        fetchUserList();
        appendAlert("Hasło zresetowane!", "success");
      },
      409: function (res) {
        appendAlert("Błąd!", "Danger");
      },
    },
  })
    .done(function (res) {
      console.log(res);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR, textStatus, errorThrown);
    });
}
