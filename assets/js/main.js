// assets/js/main.js

// ===== Global toggleNav (dipakai oleh navbar hamburger button) =====
function toggleNav() {
  const navLinks = document.querySelector('.nav-links');
  if (navLinks) {
    navLinks.classList.toggle('open');
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // ===== Hamburger menu =====
  const hamburger = document.querySelector('.hamburger');
  const navLinks  = document.querySelector('.nav-links');
  
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', (e) => {
      e.stopPropagation();
      navLinks.classList.toggle('open');
    });
    
    // Close menu when clicking on a link
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
      });
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('open');
      }
    });
  }

  // ===== Active nav link =====
  const currentPage = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.nav-links a').forEach(link => {
    const href = link.getAttribute('href');
    if (href && (href.includes(currentPage) || (currentPage === '' && href.includes('index.php')))) {
      link.classList.add('active');
    }
  });

  // ===== Scroll reveal =====
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) { 
          e.target.classList.add('visible'); 
          io.unobserve(e.target); 
        }
      });
    }, { threshold: 0.1 });
    reveals.forEach(el => io.observe(el));
  }

  // ===== Navbar scroll effect =====
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.style.boxShadow = window.scrollY > 20
        ? '0 4px 30px rgba(44,58,140,0.12)'
        : '0 2px 20px rgba(44,58,140,0.07)';
    });
  }

  // ===== User dropdown click toggle (mobile) =====
  const navUser = document.querySelector('.nav-user');
  if (navUser) {
    navUser.addEventListener('click', function(e) {
      this.classList.toggle('open');
      e.stopPropagation();
    });
    document.addEventListener('click', () => navUser.classList.remove('open'));
  }

  // ===== Auto-hide alerts =====
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });

  // ===== Form validation feedback =====
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
      const btn = form.querySelector('[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Memproses...';
        setTimeout(() => { btn.disabled = false; btn.innerHTML = btn.dataset.originalText || btn.innerHTML; }, 5000);
      }
    });
    form.querySelectorAll('[type="submit"]').forEach(btn => {
      btn.dataset.originalText = btn.innerHTML;
    });
  });
});
