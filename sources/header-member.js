$("#homepage-link").mouseenter(function () {
  if (
    document.getElementById("homepage-link").style.backgroundColor !=
    "rgb(217, 222, 255)"
  ) {
    document.getElementById("homepage-link").style.backgroundColor =
      "rgb(217, 222, 255)";
    console.log("change colour");

    $("#homepage-link").mouseleave(function () {
      document.getElementById("homepage-link").style.backgroundColor =
        "rgb(255, 255, 255)";
    });
  }
});
$("#tasks-link").mouseenter(function () {
  if (
    document.getElementById("tasks-link").style.backgroundColor !=
    "rgb(217, 222, 255)"
  ) {
    document.getElementById("tasks-link").style.backgroundColor =
      "rgb(217, 222, 255)";
    console.log("change colour");

    $("#tasks-link").mouseleave(function () {
      document.getElementById("tasks-link").style.backgroundColor =
        "rgb(255, 255, 255)";
    });
  }
});
$("#forums-link").mouseenter(function () {
  if (
    document.getElementById("forums-link").style.backgroundColor !=
    "rgb(217, 222, 255)"
  ) {
    document.getElementById("forums-link").style.backgroundColor =
      "rgb(217, 222, 255)";
    console.log("change colour");

    $("#forums-link").mouseleave(function () {
      document.getElementById("forums-link").style.backgroundColor =
        "rgb(255, 255, 255)";
    });
  }
});
$("#to-do-list-link").mouseenter(function () {
  if (
    document.getElementById("to-do-list-link").style.backgroundColor !=
    "rgb(217, 222, 255)"
  ) {
    document.getElementById("to-do-list-link").style.backgroundColor =
      "rgb(217, 222, 255)";
    console.log("change colour");

    $("#to-do-list-link").mouseleave(function () {
      document.getElementById("to-do-list-link").style.backgroundColor =
        "rgb(255, 255, 255)";
    });
  }
});
$("#logout-link").mouseenter(function () {
  if (
    document.getElementById("logout-link").style.backgroundColor !=
    "rgb(217, 222, 255)"
  ) {
    document.getElementById("logout-link").style.backgroundColor =
      "rgb(217, 222, 255)";
    console.log("change colour");

    $("#logout-link").mouseleave(function () {
      document.getElementById("logout-link").style.backgroundColor =
        "rgb(255, 255, 255)";
    });
  }
});
