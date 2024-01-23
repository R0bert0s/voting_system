$(document).on("click", "#archive-zip-download", function () {
  var now = new Date();
  var dtcode = now
    .toISOString()
    .replace(/[-:]|T/g, "")
    .slice(2, 14);

  var oReq = new XMLHttpRequest();
  oReq.open("POST", "scripts/exportDB.php", true);
  oReq.responseType = "blob";

  oReq.onload = function (oEvent) {
    var blob = oReq.response;
    var link = document.createElement("a");
    link.href = window.URL.createObjectURL(blob);
    link.download = "_backup_" + dtcode + ".zip";
    link.click();
  };

  oReq.send();
});
