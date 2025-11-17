document.addEventListener("DOMContentLoaded", function () {

  /* Desktop Hover */
  if (window.innerWidth > 992) {
    document.querySelectorAll('.dropdown, .dropend').forEach(function (dd) {
      dd.addEventListener('mouseenter', function () {
        let menu = this.querySelector('.dropdown-menu');
        menu.classList.add('show');
      });
      dd.addEventListener('mouseleave', function () {
        let menu = this.querySelector('.dropdown-menu');
        menu.classList.remove('show');
      });
    });
  }

  /* Mobile Click Behavior */
  document.querySelectorAll('.dropdown-toggle').forEach(function (element) {
    element.addEventListener('click', function (e) {
      let parent = this.parentElement;

      // Close other shown dropdowns at same level
      parent.parentElement.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
        if (menu !== parent.querySelector('.dropdown-menu')) {
          menu.classList.remove('show');
        }
      });

      // Toggle submenu
      let submenu = parent.querySelector('.dropdown-menu');
      if (submenu) {
        submenu.classList.toggle('show');
        e.preventDefault();
        e.stopPropagation();
      }
    });
  });

});