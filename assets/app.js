/**
 * app.js – Modern vanilla JS for Article SEO Site
 * Zero dependencies · ES2020+ · Passive listeners
 */

'use strict';

/* ── 1. Dark / Light Mode ────────────────────────────────────── */
const ThemeToggle = (() => {
  const KEY  = 'site-theme';
  const html = document.documentElement;

  const icons = { light: '🌙', dark: '☀️' };
  const labels = { light: 'Switch to dark mode', dark: 'Switch to light mode' };

  const apply = (theme) => {
    html.dataset.theme = theme;
    localStorage.setItem(KEY, theme);
    document.querySelectorAll('.theme-toggle').forEach(btn => {
      btn.textContent = icons[theme];
      btn.setAttribute('aria-label', labels[theme]);
      btn.setAttribute('aria-pressed', theme === 'dark');
    });
  };

  const init = () => {
    const saved = localStorage.getItem(KEY)
      ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    apply(saved);
    document.querySelectorAll('.theme-toggle').forEach(btn =>
      btn.addEventListener('click', () =>
        apply(html.dataset.theme === 'dark' ? 'light' : 'dark')
      )
    );
  };

  return { init };
})();

/* ── 2. Reading Progress Bar ─────────────────────────────────── */
const ReadingProgress = (() => {
  const bar = document.getElementById('reading-progress');
  if (!bar) return { init: () => {} };

  const update = () => {
    const scrollTop  = window.scrollY;
    const docHeight  = document.documentElement.scrollHeight - window.innerHeight;
    bar.style.transform = `scaleX(${docHeight > 0 ? scrollTop / docHeight : 0})`;
  };

  return {
    init: () => {
      window.addEventListener('scroll', update, { passive: true });
      update();
    }
  };
})();

/* ── 3. Back to Top ──────────────────────────────────────────── */
const BackToTop = (() => {
  const btn = document.getElementById('back-to-top');
  if (!btn) return { init: () => {} };

  return {
    init: () => {
      window.addEventListener('scroll', () =>
        btn.classList.toggle('visible', window.scrollY > 500)
      , { passive: true });

      btn.addEventListener('click', () =>
        window.scrollTo({ top: 0, behavior: 'smooth' })
      );
    }
  };
})();

/* ── 4. TOC Active Section (IntersectionObserver) ────────────── */
const TocHighlight = (() => {
  const toc = document.getElementById('toc');
  if (!toc || !('IntersectionObserver' in window)) return { init: () => {} };

  return {
    init: () => {
      const headings = document.querySelectorAll('.article-content h2[id], .article-content h3[id]');
      const links    = toc.querySelectorAll('a[href^="#"]');
      if (!headings.length || !links.length) return;

      let active = null;

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          const link = toc.querySelector(`a[href="#${entry.target.id}"]`);
          if (!link) return;
          if (entry.isIntersecting) {
            links.forEach(a => a.classList.remove('toc-active'));
            link.classList.add('toc-active');
            active = link;
          }
        });
      }, { rootMargin: '-10% 0px -60% 0px', threshold: 0 });

      headings.forEach(h => observer.observe(h));
    }
  };
})();

/* ── 5. Copy Code Blocks ─────────────────────────────────────── */
const CopyCode = (() => ({
  init: () => {
    document.querySelectorAll('.article-content pre').forEach(pre => {
      const wrap = document.createElement('div');
      wrap.className = 'code-block';
      pre.parentNode.insertBefore(wrap, pre);
      wrap.appendChild(pre);

      const btn = document.createElement('button');
      btn.className   = 'copy-btn';
      btn.innerHTML   = '<span>Copy</span>';
      btn.setAttribute('aria-label', 'Copy code to clipboard');
      wrap.appendChild(btn);

      btn.addEventListener('click', async () => {
        const text = pre.querySelector('code')?.innerText ?? pre.innerText;
        try {
          await navigator.clipboard.writeText(text);
          btn.innerHTML = '<span>Copied ✓</span>';
          btn.classList.add('copied');
        } catch {
          btn.innerHTML = '<span>Failed ✗</span>';
        }
        setTimeout(() => {
          btn.innerHTML = '<span>Copy</span>';
          btn.classList.remove('copied');
        }, 2000);
      });
    });
  }
}))();

/* ── 6. Mobile Navigation Hamburger ─────────────────────────── */
const MobileNav = (() => {
  const toggle = document.getElementById('nav-toggle');
  const nav    = document.querySelector('.site-header nav');
  if (!toggle || !nav) return { init: () => {} };

  const close = () => {
    nav.classList.remove('nav-open');
    toggle.setAttribute('aria-expanded', 'false');
  };

  return {
    init: () => {
      toggle.addEventListener('click', () => {
        const open = nav.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', String(open));
      });

      document.addEventListener('click', e => {
        if (!toggle.contains(e.target) && !nav.contains(e.target)) close();
      });

      document.addEventListener('keydown', e => {
        if (e.key === 'Escape') close();
      });
    }
  };
})();

/* ── 7. Scroll Fade-in for Cards ─────────────────────────────── */
const FadeIn = (() => ({
  init: () => {
    if (!('IntersectionObserver' in window)) return;

    const els = document.querySelectorAll('.article-card, .related-card, .widget');
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('is-visible');
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.08 });

    els.forEach(el => { el.classList.add('fade-up'); observer.observe(el); });
  }
}))();

/* ── 8. Native Share API / Fallback Copy Link ────────────────── */
const ShareBtn = (() => ({
  init: () => {
    const btn = document.getElementById('share-btn');
    if (!btn) return;

    btn.style.display = 'inline-flex';

    btn.addEventListener('click', async () => {
      const data = { title: document.title, url: location.href };

      if (navigator.share) {
        try { await navigator.share(data); return; } catch { /* cancelled */ return; }
      }

      // Fallback: copy URL to clipboard
      try {
        await navigator.clipboard.writeText(location.href);
        const orig = btn.innerHTML;
        btn.innerHTML = '✅ Link Copied!';
        setTimeout(() => { btn.innerHTML = orig; }, 2000);
      } catch {
        prompt('Copy this link:', location.href);
      }
    });
  }
}))();

/* ── 9. Estimated Read Progress in Meta ──────────────────────── */
const ReadMeta = (() => ({
  init: () => {
    const content = document.querySelector('.article-content');
    const el      = document.querySelector('.reading-progress-text');
    if (!content || !el) return;

    const headings = [...content.querySelectorAll('h2, h3')];
    if (!headings.length) return;

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const idx = headings.indexOf(entry.target);
          const pct = Math.round(((idx + 1) / headings.length) * 100);
          el.textContent = `${pct}%`;
        }
      });
    }, { rootMargin: '-30% 0px -50% 0px' });

    headings.forEach(h => observer.observe(h));
  }
}))();

/* ── 10. Smooth Anchor Scroll ────────────────────────────────── */
const SmoothAnchor = (() => ({
  init: () => {
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const target = document.getElementById(a.getAttribute('href').slice(1));
        if (!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        history.replaceState(null, '', a.getAttribute('href'));
      });
    });
  }
}))();

/* ── 11. Language Dropdown ───────────────────────────────────── */
const LangDropdown = (() => {
  const wrap = document.querySelector('.lang-switcher');
  const btn  = wrap?.querySelector('.lang-current');
  if (!wrap || !btn) return { init: () => {} };

  const toggle = (force) => {
    const open = force ?? !wrap.classList.contains('open');
    wrap.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', String(open));
  };

  return {
    init: () => {
      btn.addEventListener('click', e => { e.stopPropagation(); toggle(); });

      document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) toggle(false);
      });

      document.addEventListener('keydown', e => {
        if (e.key === 'Escape') toggle(false);
      });
    }
  };
})();


/* ── 12. Load More Articles / Infinite Scroll ───────────────── */
const LoadMoreArticles = (() => {
  const wrap = document.querySelector('[data-load-more]');
  const list = document.querySelector('[data-load-more-list]');
  const link = wrap?.querySelector('[data-load-more-next]');
  const status = wrap?.querySelector('[data-load-more-status]');

  if (!wrap || !list || !link) return { init: () => {} };

  let loading = false;
  let currentPage = Number(wrap.dataset.currentPage ?? 1);
  const totalPages = Number(wrap.dataset.totalPages ?? currentPage);
  const originalLabel = link.textContent.trim();

  const setStatus = (message = '') => {
    if (status) status.textContent = message;
  };

  const setLoading = (state) => {
    loading = state;
    link.classList.toggle('is-loading', state);
    link.setAttribute('aria-busy', String(state));
    if (state) {
      link.textContent = link.dataset.loadingLabel || originalLabel;
      setStatus(link.dataset.loadingLabel || originalLabel);
    } else {
      link.textContent = originalLabel;
    }
  };

  const finish = () => {
    const done = link.dataset.completeLabel || '';
    link.textContent = done;
    link.setAttribute('aria-disabled', 'true');
    link.setAttribute('aria-busy', 'false');
    link.removeAttribute('href');
    wrap.classList.add('is-complete');
    setStatus(done);
  };

  const hydrateNewCards = (cards) => {
    if (!('IntersectionObserver' in window)) {
      return;
    }

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08 });

    cards.forEach(card => {
      card.classList.add('fade-up');
      observer.observe(card);
    });
  };

  const loadNext = async (event) => {
    event?.preventDefault();
    if (loading || currentPage >= totalPages || !link.href) return;

    setLoading(true);

    try {
      const response = await fetch(link.href, { headers: { 'X-Requested-With': 'fetch' } });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);

      const html = await response.text();
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const nextList = doc.querySelector('[data-load-more-list]');
      const cards = [...(nextList?.children ?? [])];
      if (!cards.length) {
        finish();
        return;
      }

      cards.forEach(card => list.appendChild(document.importNode(card, true)));
      const appended = [...list.children].slice(-cards.length);
      hydrateNewCards(appended);

      currentPage += 1;
      wrap.dataset.currentPage = String(currentPage);

      const nextLink = doc.querySelector('[data-load-more-next]');
      if (nextLink?.getAttribute('href') && currentPage < totalPages) {
        link.href = nextLink.href;
        setStatus('');
      } else {
        finish();
      }
    } catch (error) {
      console.error('Unable to load more articles:', error);
      setStatus(link.dataset.errorLabel || 'Unable to load more articles.');
    } finally {
      if (!wrap.classList.contains('is-complete')) {
        setLoading(false);
      }
    }
  };

  return {
    init: () => {
      link.addEventListener('click', loadNext);

      if (!('IntersectionObserver' in window)) return;

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) loadNext();
        });
      }, { rootMargin: '320px 0px', threshold: 0 });

      observer.observe(wrap);
    }
  };
})();

/* ── Bootstrap ───────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  ThemeToggle.init();
  ReadingProgress.init();
  BackToTop.init();
  TocHighlight.init();
  CopyCode.init();
  MobileNav.init();
  FadeIn.init();
  ShareBtn.init();
  ReadMeta.init();
  SmoothAnchor.init();
  LangDropdown.init();
  LoadMoreArticles.init();
});

