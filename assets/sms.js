var style = document.createElement("link");
style.type = "text/css";
style.rel = "stylesheet";
style.href = "assets/sms/default.css";
document.head.appendChild(style);

var selector = document.getElementById("style");
selector.value = "default";
selector.addEventListener("change", function(){
  style.href = "assets/sms/" + selector.value + ".css";
});
