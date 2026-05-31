/**
 * Infinite scroll for /man-in-the-loop (Blogspot-style feed).
 */
(function () {
  var feed = document.getElementById('mitl-feed');
  var sentinel = document.getElementById('mitl-feed-sentinel');
  var status = document.getElementById('mitl-feed-status');
  if (!feed || !sentinel) {
    return;
  }

  var page = parseInt(feed.getAttribute('data-page') || '1', 10);
  var limit = parseInt(feed.getAttribute('data-limit') || '8', 10);
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
    var author = item.author ? ' · ' + escapeHtml(item.author) : '';
    var desc = item.description
      ? '<p class="praderas-mitl__entry-excerpt mb-2">' + escapeHtml(item.description) + '</p>'
      : '';
    return (
      '<article class="praderas-mitl__entry">' +
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
      '">Seguir leyendo</a></p>' +
      '</article>'
    );
  }

  function loadMore() {
    if (loading || done) {
      return;
    }
    loading = true;
    setStatus('Cargando más entradas…', true);
    var nextPage = page + 1;
    var url =
      '/man-in-the-loop.json?page=' +
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
          setStatus('No hay más entradas.', true);
        } else {
          setStatus('', false);
        }
      })
      .catch(function () {
        setStatus('No se pudieron cargar más entradas. Desplázate de nuevo o recarga la página.', true);
      })
      .finally(function () {
        loading = false;
      });
  }

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            loadMore();
          }
        });
      },
      { rootMargin: '240px 0px' }
    );
    observer.observe(sentinel);
  } else {
    window.addEventListener('scroll', function () {
      var rect = sentinel.getBoundingClientRect();
      if (rect.top < window.innerHeight + 200) {
        loadMore();
      }
    });
  }
})();
