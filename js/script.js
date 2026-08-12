// ============================================================
// Shared site behaviour: reveal-on-scroll, skill bars, modal,
// gallery lightbox, contact form validation
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Reveal on scroll ---------- */
  const revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
  }

  /* ---------- Skill bars fill on view ---------- */
  const bars = document.querySelectorAll('.bar__fill');
  if (bars.length) {
    const barIO = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const target = entry.target.getAttribute('data-fill') || '0%';
          entry.target.style.width = target;
          barIO.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });
    bars.forEach((el) => barIO.observe(el));
  }

  /* ---------- Project details modal ---------- */
  const modalOverlay = document.getElementById('projectModal');
  if (modalOverlay) {
    const modalTitle = modalOverlay.querySelector('[data-modal-title]');
    const modalBody = modalOverlay.querySelector('[data-modal-body]');
    const modalTag = modalOverlay.querySelector('[data-modal-eyebrow]');

    document.querySelectorAll('[data-open-modal]').forEach((btn) => {
      btn.addEventListener('click', () => {
        modalTitle.textContent = btn.getAttribute('data-title') || '';
        modalBody.textContent = btn.getAttribute('data-details') || '';
        modalTag.textContent = btn.getAttribute('data-eyebrow') || 'Project';
        modalOverlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
      });
    });

    const closeModal = () => {
      modalOverlay.classList.remove('is-open');
      document.body.style.overflow = '';
    };

    modalOverlay.querySelector('.modal__close').addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeModal();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModal();
    });
  }

  /* ---------- Contact form validation ---------- */
  const form = document.getElementById('contactForm');
  if (form) {
    const status = document.getElementById('formStatus');

    const validators = {
      name: (v) => v.trim().length >= 2 || 'Enter your full name.',
      email: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) || 'Enter a valid email address.',
      subject: (v) => v.trim().length >= 3 || 'Add a short subject line.',
      message: (v) => v.trim().length >= 10 || 'Message should be at least 10 characters.',
    };

    const showError = (field, message) => {
      const wrap = field.closest('.field');
      wrap.classList.add('has-error');
      wrap.querySelector('.field-error').textContent = message;
    };
    const clearError = (field) => {
      const wrap = field.closest('.field');
      wrap.classList.remove('has-error');
    };

    form.addEventListener('submit', function (e) {
      let valid = true;
      Object.keys(validators).forEach((name) => {
        const field = form.elements[name];
        if (!field) return;
        const result = validators[name](field.value);
        if (result !== true) {
          showError(field, result);
          valid = false;
        } else {
          clearError(field);
        }
      });

      if (!valid) {
        e.preventDefault();
        status.textContent = 'Please fix the highlighted fields.';
        status.className = 'form-status show error';
        return;
      }

      // Let the form submit normally to contact_process.php.
      // If JS-only demo mode is desired (no PHP server), uncomment below:
      // e.preventDefault();
      // status.textContent = 'Message sent — thank you for reaching out.';
      // status.className = 'form-status show success';
      // form.reset();
    });

    form.querySelectorAll('input, textarea').forEach((field) => {
      field.addEventListener('input', () => clearError(field));
    });
  }

  /* ---------- Mark active nav tab ---------- */
  const path = window.location.pathname.split('/').pop() || 'home.html';
  document.querySelectorAll('.tab').forEach((tab) => {
    const href = tab.getAttribute('href');
    if (href && href.includes(path)) tab.classList.add('is-active');
  });
});
