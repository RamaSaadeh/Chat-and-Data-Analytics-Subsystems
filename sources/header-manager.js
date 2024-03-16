$("#dashboard-link").mouseenter(function () {
  "rgb(217, 222, 255)" !=
    document.getElementById("dashboard-link").style.backgroundColor &&
    ((document.getElementById("dashboard-link").style.backgroundColor =
      "rgb(217, 222, 255)"),
    $("#dashboard-link").mouseleave(function () {
      document.getElementById("dashboard-link").style.backgroundColor =
        "rgb(255, 255, 255)";
    }));
}),
  $("#tasks-link").mouseenter(function () {
    "rgb(217, 222, 255)" !=
      document.getElementById("tasks-link").style.backgroundColor &&
      ((document.getElementById("tasks-link").style.backgroundColor =
        "rgb(217, 222, 255)"),
      $("#tasks-link").mouseleave(function () {
        document.getElementById("tasks-link").style.backgroundColor =
          "rgb(255, 255, 255)";
      }));
  }),
  $("#projects-link").mouseenter(function () {
    "rgb(217, 222, 255)" !=
      document.getElementById("projects-link").style.backgroundColor &&
      ((document.getElementById("projects-link").style.backgroundColor =
        "rgb(217, 222, 255)"),
      $("#projects-link").mouseleave(function () {
        document.getElementById("projects-link").style.backgroundColor =
          "rgb(255, 255, 255)";
      }));
  }),
  $("#forums-link").mouseenter(function () {
    "rgb(217, 222, 255)" !=
      document.getElementById("forums-link").style.backgroundColor &&
      ((document.getElementById("forums-link").style.backgroundColor =
        "rgb(217, 222, 255)"),
      $("#forums-link").mouseleave(function () {
        document.getElementById("forums-link").style.backgroundColor =
          "rgb(255, 255, 255)";
      }));
  }),
  $("#to-do-list-link").mouseenter(function () {
    "rgb(217, 222, 255)" !=
      document.getElementById("to-do-list-link").style.backgroundColor &&
      ((document.getElementById("to-do-list-link").style.backgroundColor =
        "rgb(217, 222, 255)"),
      $("#to-do-list-link").mouseleave(function () {
        document.getElementById("to-do-list-link").style.backgroundColor =
          "rgb(255, 255, 255)";
      }));
  }),
  $("#logout-link").mouseenter(function () {
    "rgb(217, 222, 255)" !=
      document.getElementById("logout-link").style.backgroundColor &&
      ((document.getElementById("logout-link").style.backgroundColor =
        "rgb(217, 222, 255)"),
      $("#logout-link").mouseleave(function () {
        document.getElementById("logout-link").style.backgroundColor =
          "rgb(255, 255, 255)";
      }));
  });
