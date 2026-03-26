 
// Desktop: open mega menu & sync
function marketPlaceOpenMegaMenu(type) {
  let mega = document.getElementById("market-place-megaMenu");
  mega.classList.add("show");
  marketPlaceShowSubmenu(type);

  // sync left menu
  document.querySelectorAll(".market-place-mega-left ul li").forEach(li=>{
    li.classList.remove("active");
    if(li.dataset.type === type) li.classList.add("active");
  });
}

// Left hover: show submenu
document.querySelectorAll(".market-place-mega-left ul li").forEach(li=>{
  li.addEventListener("mouseover", function(){
    marketPlaceShowSubmenu(this.dataset.type);
  });
});

function marketPlaceShowSubmenu(type) {
  document.querySelectorAll(".market-place-mega-right").forEach(e=>e.classList.remove("active"));
  let target = document.getElementById("market-place-submenu-" + type);
  if(target) target.classList.add("active");
}

// ===== Smooth Hide =====
const navbar = document.querySelector(".market-place-navbar");
const megaMenu = document.getElementById("market-place-megaMenu");

function marketPlaceHideMegaMenu() {
  megaMenu.classList.remove("show");
}

[navbar, megaMenu].forEach(el => {
  el.addEventListener("mouseleave", () => {
    setTimeout(() => {
      if (!navbar.matches(":hover") && !megaMenu.matches(":hover")) {
        marketPlaceHideMegaMenu();
      }
    }, 200);
  });
});





(function () {
  document.addEventListener("DOMContentLoaded", function () {
    var navbars = document.querySelectorAll(".market-place-new-desktop-menu");

    if (navbars.length > 0) {
      window.addEventListener("scroll", function () {
        navbars.forEach(function (nav) {
          if (window.scrollY > nav.offsetHeight) {
            nav.classList.add("scrolled");
          } else {
            nav.classList.remove("scrolled");
          }
        });
      });
    }
  });

  // independent function
  window.scrollTabSection = function () {
    var navbars = document.querySelectorAll(".market-place-new-desktop-menu");
    navbars.forEach(function (nav) {
      nav.classList.remove("scrolled");
    });
    return true;
  };
})();





















 