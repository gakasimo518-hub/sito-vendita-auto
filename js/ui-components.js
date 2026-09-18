/*!
 * ui-components.js
 * Design System – UI Helpers
 * Vanilla JavaScript implementation for the auto‑sales website.
 * Dependencies: Bootstrap 5 (CSS + JS) already loaded on the page.
 */

(() => {
  'use strict';

  /** -------------------------------------------------
   *  NAVIGATION TOGGLE (mobile hamburger)
   * ------------------------------------------------- */
  const initToggleMenu = () => {
    const toggleBtn = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (!toggleBtn || !navMenu) return;

    const toggle = () => {
      const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
      toggleBtn.setAttribute('aria-expanded', !expanded);
      navMenu.classList.toggle('show', !expanded);
    };

    toggleBtn.addEventListener('click', toggle);
    // close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!navMenu.contains(e.target) && !toggleBtn.contains(e.target)) {
        navMenu.classList.remove('show');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });
  };

  /** -------------------------------------------------
   *  CAROUSEL AUTOPLAY (Bootstrap 5)
   * ------------------------------------------------- */
  const initCarouselAutoplay = () => {
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach((el) => {
      // Use Bootstrap's Carousel class
      const carousel = new bootstrap.Carousel(el, {
        interval: 5000,
        pause: 'hover',
        ride: 'carousel',
        wrap: true,
      });

      // Expose pause/play on hover (already handled by `pause: 'hover'`)
      // but we also add keyboard navigation fallback
      el.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          carousel.prev();
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          carousel.next();
        }
      });
    });
  };

  /** -------------------------------------------------
   *  MODAL HANDLING (vanilla, compatible with Bootstrap)
   * ------------------------------------------------- */
  const openModal = (modalId) => {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
    modal.show();
    // Store reference for later close
    modalEl.dataset.bsModalInstance = modal;
  };

  const closeModal = (modalId) => {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
  };

  const initModalTriggers = () => {
    // Elements with data-modal-target="#modalId"
    document.body.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-modal-target]');
      if (trigger) {
        e.preventDefault();
        const targetId = trigger.getAttribute('data-modal-target').replace('#', '');
        openModal(targetId);
      }

      // Close buttons: data-modal-close or .btn-close inside modal
      const closeBtn = e.target.closest('[data-modal-close]');
      if (closeBtn) {
        e.preventDefault();
        const modalEl = closeBtn.closest('.modal');
        if (modalEl) closeModal(modalEl.id);
      }
    });
  };

  /** -------------------------------------------------
   *  FORM VALIDATION (Bootstrap 5 validation classes)
   * ------------------------------------------------- */
  const initFormValidation = () => {
    const forms = document.querySelectorAll('form.needs-validation');
    forms.forEach((form) => {
      form.addEventListener('submit', (e) => {
        // Reset previous validation state
        form.classList.remove('was-validated');

        const isValid = form.checkValidity();
        if (!isValid) {
          e.preventDefault();
          e.stopPropagation();
        }

        form.classList.add('was-validated');
      });
    });

    // Custom email validation (HTML5 already checks pattern, but we add visual feedback)
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach((input) => {
      input.addEventListener('input', () => {
        const parent = input.parentElement;
        if (input.validity.valid) {
          parent.classList.remove('has-error');
          parent.classList.add('has-success');
        } else {
          parent.classList.remove('has-success');
          parent.classList.add('has-error');
        }
      });
    });
  };

  /** -------------------------------------------------
   *  TOOLTIP INTERACTION (lightweight custom tooltip)
   * ------------------------------------------------- */
  const tooltipContainer = document.createElement('div');
  tooltipContainer.className = 'custom-tooltip';
  tooltipContainer.style.position = 'absolute';
  tooltipContainer.style.pointerEvents = 'none';
  tooltipContainer.style.zIndex = '1080';
  tooltipContainer.style.padding = '0.4rem 0.8rem';
  tooltipContainer.style.background = 'rgba(0,0,0,0.75)';
  tooltipContainer.style.color = '#fff';
  tooltipContainer.style.borderRadius = '0.25rem';
  tooltipContainer.style.fontSize = '0.875rem';
  tooltipContainer.style.transition = 'opacity 0.15s ease-in-out';
  tooltipContainer.style.opacity = '0';
  document.body.appendChild(tooltipContainer);

  const showTooltip = (target) => {
    const title = target.getAttribute('data-tooltip');
    if (!title) return;
    tooltipContainer.textContent = title;
    const rect = target.getBoundingClientRect();
    const top = rect.top - tooltipContainer.offsetHeight - 6;
    const left = rect.left + rect.width / 2 - tooltipContainer.offsetWidth / 2;
    tooltipContainer.style.top = `${top < 0 ? rect.bottom + 6 : top}px`;
    tooltipContainer.style.left = `${Math.max(8, left)}px`;
    tooltipContainer.style.opacity = '1';
  };

  const hideTooltip = () => {
    tooltipContainer.style.opacity = '0';
  };

  const initTooltips = () => {
    document.body.addEventListener('mouseenter', (e) => {
      const target = e.target.closest('[data-tooltip]');
      if (target) showTooltip(target);
    }, true);

    document.body.addEventListener('mouseleave', (e) => {
      const target = e.target.closest('[data-tooltip]');
      if (target) hideTooltip();
    }, true);
  };

  /** -------------------------------------------------
   *  INITIALISATION
   * ------------------------------------------------- */
  document.addEventListener('DOMContentLoaded', () => {
    initToggleMenu();
    initCarouselAutoplay();
    initModalTriggers();
    initFormValidation();
    initTooltips();
  });

  // Export for possible manual usage (e.g., from other scripts)
  window.UIComponents = {
    openModal,
    closeModal,
    initToggleMenu,
    initCarouselAutoplay,
    initModalTriggers,
    initFormValidation,
    initTooltips,
  };
})();