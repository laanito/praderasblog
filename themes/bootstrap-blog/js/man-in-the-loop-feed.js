/**
 * Infinite scroll + sidebar scroll-spy for /man-in-the-loop feed.
 */
(function () {
  var feed = document.getElementById('mitl-feed');
  var sentinel = document.getElementById('mitl-feed-sentinel');
  var status = document.getElementById('mitl-feed-status');
  var sidebarNav = document.getElementById('mitl-sidebar-nav');
  if (!feed) {
    return;
  }

  var page = parseInt(feed.getAttribute('data-page') || '1', 10);
  var limit = parseInt(feed.getAttribute('data-limit') || '8', 10);
  var jsonBase = feed.getAttribute('data-json-url') || '/man-in-the-loop.json';
  var loading = false;
  var done = feed.getAttribute('data-has-more') === '0';

  function setStatus(msg, show) {
    if (!status) {
      return;
    }
    status.textContent = msg;
    status.hidden = !show;
  }

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function renderItem(item) {
    var anchor = item.anchor || 'mitl-entry';
    var author = item.author ? ' · ' + escapeHtml(item.author) : '';
    var desc = item.description
      ? '<p class="praderas-mitl__entry-excerpt mb-2">' + escapeHtml(item.description) + '</p>'
      : '';
    return (
      '<article class="praderas-mitl__entry" id="' +
      escapeHtml(anchor) +
      '">' +
      '<h2 class="praderas-mitl__entry-title h4 mb-1"><a href="' +
      escapeHtml(item.url) +
      '">' +
      escapeHtml(item.title) +
      '</a></h2>' +
      '<p class="praderas-mitl__entry-meta text-muted small mb-2">' +
      escapeHtml(item.date) +
      author +
      '</p>' +
      desc +
      '<p class="mb-0"><a class="praderas-mitl__read-more" href="' +
      escapeHtml(item.url) +
      '">' +
      (document.documentElement.lang === 'en' ? 'Continue reading' : 'Seguir leyendo') +
      '</a></p>' +
      '</article>'
    );
  }

  function loadMore() {
    if (loading || done) {
      return;
    }
    loading = true;
    setStatus(document.documentElement.lang === 'en' ? 'Loading more…' : 'Cargando más entradas…', true);
    var nextPage = page + 1;
    var url =
      jsonBase +
      (jsonBase.indexOf('?') >= 0 ? '&' : '?') +
      'page=' +
      encodeURIComponent(String(nextPage)) +
      '&limit=' +
      encodeURIComponent(String(limit));

    fetch(url, { headers: { Accept: 'application/json' } })
      .then(function (res) {
        if (!res.ok) {
          throw new Error('HTTP ' + res.status);
        }
        return res.json();
      })
      .then(function (data) {
        var posts = data.posts || [];
        var meta = data.meta || {};
        posts.forEach(function (item) {
          feed.insertAdjacentHTML('beforeend', renderItem(item));
        });
        page = nextPage;
        feed.setAttribute('data-page', String(page));
        if (!meta.has_more || posts.length === 0) {
          done = true;
          setStatus(
            document.documentElement.lang === 'en' ? 'No more entries.' : 'No hay más entradas.',
            true
          );
        } else {
          setStatus('', false);
        }
        observeEntries();
      })
      .catch(function () {
        setStatus(
          document.documentElement.lang === 'en'
            ? 'Could not load more entries.'
            : 'No se pudieron cargar más entradas.',
          true
        );
      })
      .finally(function () {
        loading = false;
      });
  }

  function observeEntries() {
    if (!sidebarNav || !('IntersectionObserver' in window)) {
      return;
    }
    var links = sidebarNav.querySelectorAll('[data-mitl-anchor]');
    var entries = feed.querySelectorAll('.praderas-mitl__entry');
    if (!entries.length) {
      return;
    }
    var observer = new IntersectionObserver(
      function (observed) {
        observed.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }
          var id = entry.target.id;
          links.forEach(function (link) {
            var match = link.getAttribute('data-mitl-anchor') === id;
            link.classList.toggle('active', match);
          });
        });
      },
      { rootMargin: '-20% 0px -55% 0px', threshold: 0 }
    );
    entries.forEach(function (el) {
      observer.observe(el);
    });
  }

  if (sentinel && 'IntersectionObserver' in window) {
    var loadObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            loadMore();
          }
        });
      },
      { rootMargin: '240px 0px' }
    );
    loadObserver.observe(sentinel);
  }

  observeEntries();
})();
