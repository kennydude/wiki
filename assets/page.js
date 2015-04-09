function id(d){ return document.getElementById(d); }
var menu = id("menu");
var sidebar = id("sidebar");

function toggleSidebar(){
    if(sidebar.classList.contains("hidden")){
        sidebar.classList.remove("hidden");
    } else{
        sidebar.classList.add("hidden");
    }
}

menu.addEventListener("click", toggleSidebar);
sidebar.addEventListener("click", toggleSidebar);
