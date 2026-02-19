/**
 * Main JavaScript (Vanilla JS)
 */

import '../scss/style.scss';

document.addEventListener('DOMContentLoaded', () => {

  // ========================================
  // 100vh対応（iOS対策）
  // ========================================
  const setFillHeight = () => {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  };

  window.addEventListener('resize', setFillHeight);
  setFillHeight();


  // ========================================
  // ハンバーガーメニュー
  // ========================================
  const hamburger = document.querySelector('.hamburger');
  const gnav = document.querySelector('.gnav');
  const header = document.querySelector('.header');
  const body = document.body;

  if (hamburger) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('js-active');
      body.classList.toggle('js-fixed');
      if (header) header.classList.toggle('js-active');
      if (gnav) gnav.classList.toggle('js-active');
    });
  }

  // gnavリンククリックで閉じる
  const gnavLinks = document.querySelectorAll('.gnav__item a');
  gnavLinks.forEach(link => {
    link.addEventListener('click', () => {
      body.classList.remove('js-fixed');
      if (hamburger) hamburger.classList.remove('js-active');
      if (gnav) gnav.classList.remove('js-active');
    });
  });


  // ========================================
  // SPでのhover無効化
  // ========================================
  const disableHoverOnTouch = () => {
    const touch = 'ontouchstart' in document.documentElement ||
                  navigator.maxTouchPoints > 0 ||
                  navigator.msMaxTouchPoints > 0;

    if (touch) {
      try {
        for (let si = 0; si < document.styleSheets.length; si++) {
          const styleSheet = document.styleSheets[si];
          if (!styleSheet.cssRules) continue;

          for (let ri = styleSheet.cssRules.length - 1; ri >= 0; ri--) {
            const rule = styleSheet.cssRules[ri];
            if (!rule.selectorText) continue;

            if (rule.selectorText.match(':hover')) {
              styleSheet.deleteRule(ri);
            }
          }
        }
      } catch (ex) {
        // CORSエラーなどを無視
      }
    }
  };

  disableHoverOnTouch();


  // ========================================
  // ヘッダー表示/非表示（スクロール方向判定）
  // ========================================
  let beforePos = 0;

  const scrollAnime = () => {
    const elemTop = 500;
    const scroll = window.pageYOffset || document.documentElement.scrollTop;
    const headerEl = document.querySelector('.header');

    if (!headerEl) return;

    if (scroll === beforePos) {
      // 何もしない
    } else if (elemTop > scroll || scroll - beforePos < 0) {
      headerEl.classList.remove('up');
      headerEl.classList.add('down');
    } else {
      headerEl.classList.remove('down');
      headerEl.classList.add('up');
    }

    beforePos = scroll;
  };

  window.addEventListener('scroll', scrollAnime);
  window.addEventListener('load', scrollAnime);


  // ========================================
  // ページ内スムーススクロール
  // ========================================
  const smoothScrollLinks = document.querySelectorAll('a[href*="#"]');

  smoothScrollLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href');
      const hash = href.includes('#') ? href.substring(href.indexOf('#')) : '';

      if (!hash || hash === '#') return;

      const target = document.querySelector(hash);
      if (!target) return;

      e.preventDefault();

      const headerHeight = document.querySelector('header')?.offsetHeight || 0;
      const position = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

      window.scrollTo({
        top: position,
        behavior: 'smooth'
      });

      if (hash !== '#') {
        history.pushState(null, '', hash);
      }
    });
  });


  // ========================================
  // 別ページ遷移後のスムーススクロール
  // ========================================
  const urlHash = location.hash;
  if (urlHash) {
    const target = document.querySelector(urlHash);
    if (target) {
      history.replaceState(null, '', window.location.pathname);
      window.scrollTo(0, 0);

      window.addEventListener('load', () => {
        const headerHeight = document.querySelector('header')?.offsetHeight || 0;
        const position = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

        window.scrollTo({
          top: position,
          behavior: 'smooth'
        });

        history.replaceState(null, '', window.location.pathname + urlHash);
      });
    }
  }

});
