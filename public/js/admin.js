(function () {
  // القائمة الجانبية على الجوال
  var burger = document.getElementById('burger');
  var side = document.getElementById('sidebar');
  if (burger && side) burger.addEventListener('click', function () { side.classList.toggle('open'); });

  // تأكيد الحذف
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (f.hasAttribute('data-confirm') && !confirm(f.getAttribute('data-confirm'))) e.preventDefault();
  });

  // معاينة الصورة قبل الرفع
  document.querySelectorAll('input[type=file][data-preview]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var img = document.getElementById(inp.getAttribute('data-preview'));
      if (img && inp.files[0]) { img.src = URL.createObjectURL(inp.files[0]); img.style.display = 'block'; }
    });
  });

  // المحرر النصي
  var holder = document.getElementById('editor');
  if (holder && window.Quill) {
    var input = document.getElementById('body-input');
    var csrf = document.querySelector('meta[name=csrf-token]').content;
    var quill = new Quill(holder, {
      theme: 'snow',
      placeholder: 'اكتب نص المادة هنا...',
      modules: {
        toolbar: {
          container: [
            [{ header: [2, 3, false] }], ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link', 'image'],
            [{ align: [] }], [{ direction: 'rtl' }], ['clean']
          ],
          handlers: {
            image: function () {
              var f = document.createElement('input'); f.type = 'file'; f.accept = 'image/*';
              f.onchange = function () {
                var fd = new FormData(); fd.append('image', f.files[0]); fd.append('_token', csrf);
                fetch(holder.dataset.upload, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
                  .then(function (r) { return r.json(); })
                  .then(function (j) { var r = quill.getSelection(true); quill.insertEmbed(r.index, 'image', j.url); })
                  .catch(function () { alert('تعذّر رفع الصورة'); });
              };
              f.click();
            }
          }
        }
      }
    });
    quill.root.setAttribute('dir', 'rtl');
    if (input.value) quill.clipboard.dangerouslyPasteHTML(input.value);
    var form = holder.closest('form');
    form.addEventListener('submit', function () {
      input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
    });
  }

  // إظهار حقل سبب الرفض
  var rb = document.getElementById('reject-toggle');
  if (rb) rb.addEventListener('click', function () { document.getElementById('reject-box').style.display = 'block'; rb.style.display = 'none'; });

  // عدّاد حروف
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var out = document.getElementById(el.getAttribute('data-count'));
    var upd = function () { out.textContent = el.value.length + ' / ' + el.maxLength; };
    el.addEventListener('input', upd); upd();
  });
})();
