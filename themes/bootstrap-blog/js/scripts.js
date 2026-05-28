/*!
 * Praderas theme — post-body enhancements (table scroll wrappers).
 */
(function () {
  function wrapPostTables() {
    var body = document.querySelector('.post-body');
    if (!body) {
      return;
    }
    body.querySelectorAll('table').forEach(function (table) {
      if (table.parentElement && table.parentElement.classList.contains('post-body-table-wrap')) {
        return;
      }
      var wrap = document.createElement('div');
      wrap.className = 'post-body-table-wrap';
      table.parentNode.insertBefore(wrap, table);
      wrap.appendChild(table);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wrapPostTables);
  } else {
    wrapPostTables();
  }
})();
