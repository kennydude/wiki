var style = document.createElement("link");
style.type = "text/css";
style.rel = "stylesheet";
style.href = "assets/sms/default.css";

var selector = document.getElementById("style");

selector.addEventListener("change", function(){
  style.href = "assets/sms/" + selector.value + ".css";
  history.pushState(null, null, document.location.pathname + "?theme=" + selector.value);
});

if(document.location.search){
  var th = document.location.search.indexOf("theme=");
  selector.value = document.location.search.substring(
    th + "theme=".length
  );

  style.href = "assets/sms/" + selector.value + ".css";
} else{
  selector.value = "default";
}

document.head.appendChild(style);
