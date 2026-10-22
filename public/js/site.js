(function () {
  // الوضع الداكن
  var root = document.documentElement;
  var saved = null;
  try { saved = localStorage.getItem('theme'); } catch (e) {}
  if (saved) root.setAttribute('data-theme', saved);
  var tbtn = document.getElementById('theme-toggle');
  if (tbtn) tbtn.addEventListener('click', function () {
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    try { localStorage.setItem('theme', next); } catch (e) {}
  });

  // قائمة الجوال
  var toggle = document.getElementById('menu-toggle');
  var nav = document.getElementById('mainnav');
  if (toggle && nav) toggle.addEventListener('click', function () { nav.classList.toggle('open'); });

  // شريط العاجل
  var items = document.querySelectorAll('.ticker-track a');
  if (items.length) {
    var i = 0;
    items[0].classList.add('on');
    if (items.length > 1) setInterval(function () {
      items[i].classList.remove('on');
      i = (i + 1) % items.length;
      items[i].classList.add('on');
    }, 4500);
  }

  // نسخ الرابط
  document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var url = b.getAttribute('data-copy');
      (navigator.clipboard ? navigator.clipboard.writeText(url) : Promise.reject()).then(function () {
        var t = b.textContent; b.textContent = 'تم النسخ ✓'; setTimeout(function () { b.textContent = t; }, 1800);
      }).catch(function () { window.prompt('انسخ الرابط:', url); });
    });
  });
})();
