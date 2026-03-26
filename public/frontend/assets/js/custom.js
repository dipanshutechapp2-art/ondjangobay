// download app 
 (function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var WRAPPER_SELECTOR = '.download-apps-main';
    var ACTIVE_CLASS = 'da-active';
    var HIDE_DELAY = 200; // ms

    function isTouchDevice() {
      // robust touch detection: pointer coarse or touch support or maxTouchPoints
      return (('ontouchstart' in window) ||
              (navigator.maxTouchPoints && navigator.maxTouchPoints > 0) ||
              window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches);
    }

    var wrappers = document.querySelectorAll(WRAPPER_SELECTOR);
    if (!wrappers || wrappers.length === 0) return;

    wrappers.forEach(function (wrapper) {
      var toggleBtn = wrapper.querySelector('.da-toggle-btn');
      var togglableDiv = wrapper.querySelector('.togglable-div');
      if (!toggleBtn || !togglableDiv) return;

      var hideTimeout = null;

      // helper open / close
      function openMenu() {
        clearTimeout(hideTimeout);
        togglableDiv.classList.add(ACTIVE_CLASS);
        wrapper.setAttribute('data-da-open', 'true');
      }
      function closeMenu() {
        clearTimeout(hideTimeout);
        togglableDiv.classList.remove(ACTIVE_CLASS);
        wrapper.removeAttribute('data-da-open');
      }

      // MOBILE / TOUCH: click toggles (open/close)
      toggleBtn.addEventListener('click', function (ev) {
        if (!isTouchDevice()) return; // ignore click for non-touch (desktop hover handled separately)
        ev.preventDefault();
        ev.stopPropagation(); // avoid document-level click closing instantly
        if (togglableDiv.classList.contains(ACTIVE_CLASS)) {
          closeMenu();
        } else {
          openMenu();
        }
      });

      // Prevent clicks inside the dropdown from closing it (for touch)
      togglableDiv.addEventListener('click', function (ev) {
        if (isTouchDevice()) {
          ev.stopPropagation();
        }
      });

      // Click outside (only on touch devices) should close
      document.addEventListener('click', function (ev) {
        if (!isTouchDevice()) return;
        if (!wrapper.contains(ev.target) && togglableDiv.classList.contains(ACTIVE_CLASS)) {
          closeMenu();
        }
      });

      // Escape key closes if open
      document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape' && togglableDiv.classList.contains(ACTIVE_CLASS)) {
          closeMenu();
        }
      });

      // DESKTOP: hover on wrapper (covers button + dropdown) with delay to avoid accidental close
      wrapper.addEventListener('mouseenter', function () {
        if (isTouchDevice()) return;
        clearTimeout(hideTimeout);
        openMenu();
      });

      wrapper.addEventListener('mouseleave', function () {
        if (isTouchDevice()) return;
        clearTimeout(hideTimeout);
        hideTimeout = setTimeout(function () {
          closeMenu();
        }, HIDE_DELAY);
      });

      // Optional: handle window resize -> close menus when switching contexts
      window.addEventListener('resize', function () {
        // close on resize to avoid stale state across layout changes
        closeMenu();
      });
    });
  });
})();

// download app end 




// login toggle js 
(function(){
  // helpers
  const MOBILE_BREAK = 768; // treat <768px as mobile
  const openDelay = 80;     // ms before opening on hover
  const closeDelay = 180;   // ms before closing on hover out

  function isDesktop() {
    return window.innerWidth >= MOBILE_BREAK && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  }

  function closeAll(menus){
    menus.forEach(m => {
      const dd = m.querySelector('.bay-dropdown');
      if(dd) dd.classList.remove('bay-open');
      m.__isOpen = false;
      clearTimeout(m.__openT); clearTimeout(m.__closeT);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const menus = Array.from(document.querySelectorAll('.bay-user-menu'));
    if (!menus.length) return;

    // click outside handler to close (works for mobile & desktop)
    document.addEventListener('click', function(e){
      // if click outside all menus -> close all
      const clickedInsideAny = menus.some(m => m.contains(e.target));
      if(!clickedInsideAny) closeAll(menus);
    });

    // esc key closes
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') closeAll(menus);
    });

    // init each menu
    menus.forEach(menu => {
      const btn = menu.querySelector('.login-icon-wrapper');
      const dropdown = menu.querySelector('.bay-dropdown');

      // safety checks
      if(!btn || !dropdown) return;

      // ensure wrapper has position relative (in case CSS missing)
      const st = window.getComputedStyle(menu);
      if(st.position === 'static') menu.style.position = 'relative';

      // ----- Hover behavior for desktop (mouseenter/leave with small delays) -----
      menu.addEventListener('mouseenter', function(){
        if(!isDesktop()) return;
        clearTimeout(menu.__closeT);
        menu.__openT = setTimeout(function(){
          // close other menus first
          menus.forEach(m => { if(m !== menu){
            const d = m.querySelector('.bay-dropdown'); if(d) d.classList.remove('bay-open');
            m.__isOpen = false;
          }});
          dropdown.classList.add('bay-open');
          menu.__isOpen = true;
        }, openDelay);
      });

      menu.addEventListener('mouseleave', function(){
        if(!isDesktop()) return;
        clearTimeout(menu.__openT);
        menu.__closeT = setTimeout(function(){
          dropdown.classList.remove('bay-open');
          menu.__isOpen = false;
        }, closeDelay);
      });

      // also keep open when mouse enters dropdown itself
      dropdown.addEventListener('mouseenter', function(){
        if(!isDesktop()) return;
        clearTimeout(menu.__closeT);
      });
      dropdown.addEventListener('mouseleave', function(){
        if(!isDesktop()) return;
        menu.__closeT = setTimeout(function(){
          dropdown.classList.remove('bay-open');
          menu.__isOpen = false;
        }, closeDelay);
      });

      // ----- Click/tap behavior for mobile (toggle) -----
      btn.addEventListener('click', function(e){
        if(isDesktop()){
          // on desktop, click should not toggle (hover handles it)
          return;
        }
        e.stopPropagation();
        // close other menus
        menus.forEach(m => { if(m !== menu){
          const d = m.querySelector('.bay-dropdown'); if(d) d.classList.remove('bay-open');
          m.__isOpen = false;
        }});
        // toggle current
        const nowOpen = dropdown.classList.toggle('bay-open');
        menu.__isOpen = nowOpen;
      });
    });

    // close on resize to avoid stuck open states
    window.addEventListener('resize', function(){
      closeAll(menus);
    });
  });
})();
 

// login toggle js end 


// login register code 

 // Get modals
         var loginModal = document.getElementById("loginModal");
         var registerModal = document.getElementById("registerModal");

         // Get buttons
         var loginBtn = document.getElementById("loginBtn");
         var registerBtn = document.getElementById("registerBtn");

         // Get close buttons
         var closeBtns = document.getElementsByClassName("search_closesMarketPlace");

         // Open Login Modal
         loginBtn.onclick = function() {
            loginModal.style.display = "block";
         }

         // Open Register Modal
         registerBtn.onclick = function() {
            registerModal.style.display = "block";
         }
		 
		  // Open MagicLink Modal
         sendMagicLinkModel.onclick = function() {
            loginModalMagicLink.style.display = "block";
         }

         // Close Modals when clicking on X
         for (var i = 0; i < closeBtns.length; i++) {
            closeBtns[i].onclick = function() {
               this.parentElement.parentElement.style.display = "none";
            }
         }

         // Close when clicking outside
         window.onclick = function(event) {
            if (event.target == loginModal) {
               loginModal.style.display = "none";
            }
            if (event.target == registerModal) {
               registerModal.style.display = "none";
            }
			if (event.target == loginModalMagicLink) {
               loginModalMagicLink.style.display = "none";
            }
         }


// login register code end

// market place new desktop menu 
 

// market place new desktop menu end 


// accordinate css js code 




// shop page js 
 





	   
















