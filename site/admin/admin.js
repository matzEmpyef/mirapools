(function () {
  'use strict';

  /* Phone menu */
  var toggles = document.querySelectorAll('[data-menu-toggle]');
  var btn = document.querySelector('.menu-btn');
  function setMenu(open) {
    document.body.classList.toggle('menu-open', open);
    if (btn) { btn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
  }
  toggles.forEach(function (t) {
    t.addEventListener('click', function () { setMenu(!document.body.classList.contains('menu-open')); });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); } });

  /* Popups (native <dialog>) */
  function openModal(id) {
    var d = document.getElementById(id);
    if (!d || d.open) { return d; }
    if (typeof d.showModal === 'function') { d.showModal(); } else { d.setAttribute('open', ''); }
    var first = d.querySelector('input:not([type=hidden]):not([type=file]), select, textarea');
    if (first) { setTimeout(function () { first.focus(); }, 30); }
    return d;
  }
  function closeModal(d) {
    if (!d) { return; }
    d.classList.add('closing');
    setTimeout(function () {
      d.classList.remove('closing');
      if (typeof d.close === 'function') { d.close(); } else { d.removeAttribute('open'); }
    }, 160);
  }
  document.addEventListener('click', function (e) {
    var opener = e.target.closest('[data-modal-open]');
    if (opener) { e.preventDefault(); setMenu(false); openModal(opener.getAttribute('data-modal-open')); return; }
    var closer = e.target.closest('[data-modal-close]');
    if (closer) { closeModal(closer.closest('dialog')); return; }
    // click on the dimmed backdrop closes the popup
    if (e.target.tagName === 'DIALOG' && e.target.classList.contains('modal')) {
      var r = e.target.getBoundingClientRect();
      if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) { closeModal(e.target); }
    }
  });
  document.querySelectorAll('dialog.modal').forEach(function (d) {
    d.addEventListener('cancel', function (e) { e.preventDefault(); closeModal(d); });
  });

  /* Confirm popup for destructive forms */
  var confirmBox = document.getElementById('confirm-modal');
  var pendingForm = null;
  document.addEventListener('submit', function (e) {
    var form = e.target;
    var msg = form.getAttribute && form.getAttribute('data-confirm');
    if (!msg || form.dataset.confirmed === '1') { return; }
    e.preventDefault();
    if (!confirmBox) { if (window.confirm(msg)) { form.dataset.confirmed = '1'; form.submit(); } return; }
    pendingForm = form;
    confirmBox.querySelector('[data-confirm-text]').textContent = msg;
    openModal('confirm-modal');
    setTimeout(function () { confirmBox.querySelector('[data-confirm-yes]').focus(); }, 40);
  });
  if (confirmBox) {
    confirmBox.querySelector('[data-confirm-yes]').addEventListener('click', function () {
      if (!pendingForm) { return; }
      pendingForm.dataset.confirmed = '1';
      this.disabled = true;
      pendingForm.submit();
    });
  }

  /* Gallery: fill the edit popup from the chosen tile */
  var editForm = document.querySelector('[data-edit-form]');
  if (editForm) {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-edit]');
      if (!btn) { return; }
      var card = btn.closest('.g-card');
      if (!card) { return; }
      var d = card.dataset;
      editForm.elements.id.value = d.id;
      editForm.elements.title.value = d.title || '';
      editForm.elements.alt.value = d.alt || '';
      editForm.elements.location.value = d.location || '';
      editForm.elements.category.value = d.category || '';
      editForm.elements.featured.checked = d.featured === '1';
      editForm.querySelector('[data-edit-img]').src = d.thumb;
      openModal('edit-modal');
    });
  }

  /* Toast */
  function toast(text, isErr) {
    var t = document.createElement('div');
    t.className = 'toast' + (isErr ? ' err' : '');
    t.setAttribute('role', 'status');
    t.textContent = text;
    document.body.appendChild(t);
    setTimeout(function () { t.remove(); }, 2600);
  }

  /* Upload previews */
  var input = document.querySelector('[data-file-input]');
  var box = document.querySelector('[data-previews]');
  if (input && box) {
    var zone = input.closest('[data-dropzone]');
    var count = document.querySelector('[data-dz-count]');
    if (zone) {
      ['dragenter', 'dragover'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.add('over'); }); });
      ['dragleave', 'drop'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.remove('over'); }); });
    }
    input.addEventListener('change', function () {
      if (count) {
        var n = input.files.length;
        count.textContent = n ? n + (n === 1 ? ' photo selected' : ' photos selected') : 'You can pick several at once.';
      }
      box.textContent = '';
      Array.prototype.forEach.call(input.files, function (f) {
        if (!/^image\//.test(f.type)) { return; }
        var fig = document.createElement('figure');
        var img = document.createElement('img');
        var cap = document.createElement('figcaption');
        img.alt = '';
        img.src = URL.createObjectURL(f);
        img.onload = function () { URL.revokeObjectURL(img.src); };
        cap.textContent = f.name;
        fig.appendChild(img);
        fig.appendChild(cap);
        box.appendChild(fig);
      });
    });
    var form = input.closest('form');
    if (form) {
      form.addEventListener('submit', function () {
        var b = form.querySelector('button[type=submit]');
        if (b) { b.disabled = true; b.textContent = 'Uploading…'; }
      });
    }
  }

  /* Drag-and-drop ordering */
  var grid = document.querySelector('[data-sortable]');
  if (grid) {
    var dragging = null;
    var cards = function () { return Array.prototype.slice.call(grid.querySelectorAll('.g-card')); };
    var clear = function () { cards().forEach(function (c) { c.classList.remove('drop-before'); }); };

    grid.addEventListener('dragstart', function (e) {
      var card = e.target.closest && e.target.closest('.g-card');
      if (!card || e.target.closest('.g-actions, form, input, select, textarea')) { e.preventDefault(); return; }
      dragging = card;
      card.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      try { e.dataTransfer.setData('text/plain', card.getAttribute('data-id')); } catch (err) { /* old browsers */ }
    });
    grid.addEventListener('dragover', function (e) {
      if (!dragging) { return; }
      e.preventDefault();
      var over = e.target.closest('.g-card');
      clear();
      if (over && over !== dragging) { over.classList.add('drop-before'); }
    });
    grid.addEventListener('drop', function (e) {
      if (!dragging) { return; }
      e.preventDefault();
      var over = e.target.closest('.g-card');
      if (over && over !== dragging) { grid.insertBefore(dragging, over); } else if (!over) { grid.appendChild(dragging); }
      clear();
      save();
    });
    grid.addEventListener('dragend', function () {
      if (dragging) { dragging.classList.remove('dragging'); }
      dragging = null;
      clear();
    });

    function save() {
      var body = new URLSearchParams();
      body.append('csrf', grid.getAttribute('data-csrf'));
      body.append('action', 'reorder');
      body.append('ajax', '1');
      cards().forEach(function (c) { body.append('order[]', c.getAttribute('data-id')); });
      fetch(grid.getAttribute('data-post'), { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok && j.ok, j: j }; }); })
        .then(function (res) {
          if (res.ok) { toast('Order saved'); } else { toast((res.j && res.j.error) || 'Could not save the order', true); }
        })
        .catch(function () { toast('Could not save the order', true); });
    }
  }

  /* Branch repeater */
  var br = document.querySelector('[data-branches]');
  if (br) {
    var add = br.querySelector('[data-add-branch]');
    var extras = function () { return br.querySelectorAll('[data-extra][hidden]'); };
    var sync = function () { add.hidden = extras().length === 0; };
    add.addEventListener('click', function () {
      var next = extras()[0];
      if (next) { next.hidden = false; var i = next.querySelector('input'); if (i) { i.focus(); } }
      sync();
    });
    sync();
  }
})();

/* Projects: add / edit popup */
(function () {
  'use strict';
  var form = document.querySelector('[data-project-form]');
  if (!form) { return; }
  var modal = document.getElementById('project-modal');
  var title = document.getElementById('project-modal-title');
  var dates = form.querySelector('[data-completed-fields]');
  function syncStatus() { dates.hidden = !form.querySelector('input[name=status][value=completed]').checked; }
  form.querySelectorAll('input[name=status]').forEach(function (r) { r.addEventListener('change', syncStatus); });
  function open(data) {
    form.reset();
    form.elements.id.value = data ? data.id : '';
    form.elements.name.value = data ? data.name : '';
    form.elements.location.value = data ? data.location : '';
    form.querySelector('input[name=status][value=' + (data && data.status === 'completed' ? 'completed' : 'ongoing') + ']').checked = true;
    if (data && data.status === 'completed') {
      form.elements.year.value = data.year;
      form.elements.month.value = data.month;
    }
    title.textContent = data ? 'Edit project' : 'Add project';
    syncStatus();
    modal.showModal ? modal.showModal() : modal.setAttribute('open', '');
    setTimeout(function () { form.elements.name.focus(); }, 30);
  }
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-project-add]')) { open(null); return; }
    var b = e.target.closest('[data-project-edit]');
    if (b) { open(b.closest('tr').dataset); }
  });
})();
