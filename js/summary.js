/* var popoverTriggerList = $('td[data-bs-toggle="popover"]').toArray();

var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl);
});
 */
$(document).ready(function () {
  oofNotSekretarz();
  tkn = parseJwt();

  var now = new Date();
  var day = ("0" + (now.getDate() - 7)).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var today = now.getFullYear() + "-" + month + "-" + day;

  $("#summary_from").val(today);

  var now = new Date();
  var day = ("0" + now.getDate()).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var today = now.getFullYear() + "-" + month + "-" + day;

  $("#summary_to").val(today);

  $("#summary_form").submit();
});

function getSummary(from, to) {
  values = [
    { name: "from", value: from },
    { name: "to", value: to },
  ];

  $.ajax({
    url: "scripts/getSummaryRaports.php",
    type: "post",
    data: $.param(values),
    headers: {
      Authorization: `Bearer ${Cookies.get("jwt")}`,
    },
  })
    .done(function (res) {
      res = $.parseJSON(res);
      fetchSummary(res);
    })
    .fail(function (res) {
      console.log(res);
    });
}

function fetchSummary(data) {
  $("#summary_table_body").html("");

  data.forEach((element) => {
    let record =
      `
        <tr>
            <td>` +
      element[0] +
      `</td>
            <td>` +
      element[9] +
      `</td>
            <td>` +
      element[8] +
      `</td>
            <td>` +
      element[7] +
      `</td>
            <td  data-bs-container="body" data-bs-trigger="hover focus" data-bs-toggle="popover" title="Szczegoły rozkładu głosów"  data-bs-placement="top" data-bs-content="` +
      element[11] +
      `">` +
      element[3] +
      `</td>
            <td>` +
      (element[4] == 0 ? "-" : element[4]) +
      `</td>
            <td class="fw-bold text-` +
      (element[6] == 1 ? "success" : "danger") +
      `">` +
      (element[6] == 1 ? "Tak" : "Nie") +
      `</td>
        </tr>    
    `;
    $("#summary_table_body").append(record);
  });
  $("#raport-pdf-download").removeClass("d-none").addClass("d-block");

  var popoverTriggerList = [].slice.call(
    document.querySelectorAll('td[data-bs-toggle="popover"]')
  );
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });
}

$("#summary_form").validate({
  errorPlacement: function (label, element) {
    label.addClass("text-danger mt-1");
    label.insertAfter(element);
  },
  wrapper: "small",
  submitHandler: function (form) {
    getSummary($("#summary_from").val(), $("#summary_to").val());

    $("#oddaj-glos").attr("disabled", "disabled")
      .html(`<div class="spinner-border text-light" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>`);
    $("#v_opcje").attr("disabled", "disabled");
  },
});

var opt = {
  margin: 0.2,
  filename: "raport_summary.pdf",
  pagebreak: { avoid: ["tr", "td"] },
  image: { type: "jpeg", quality: 1 },
  html2canvas: { scale: 2, windwoWidth: 800, scrollY: 0 },
  jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
};
$("#raport-pdf-download").click(function () {
  if ($("#summary_from").val() != "" && $("#summary_to").val() != "") {
    $("#raport-pdf-download").addClass("d-none").removeClass("d-block");
    $("#see_button").addClass("d-none").removeClass("d-block");
    var element = document.getElementById("summary_container");
    html2pdf(element, opt);
    setTimeout(function () {
      $("#raport-pdf-download").removeClass("d-none").addClass("d-block");
      $("#see_button").removeClass("d-none").addClass("d-block");
    }, 1000);
  }
});
