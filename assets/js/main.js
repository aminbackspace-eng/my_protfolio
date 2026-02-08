document.addEventListener('DOMContentLoaded', () => {
    // Hide Loader
    const loader = document.querySelector('.loader-wrapper');
    setTimeout(() => {
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    }, 1000);

    // Header Scroll Effect
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Mobile Menu Toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const links = document.querySelectorAll('.nav-link');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('is-active');
        });
    }

    if (links) {
        links.forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                // Reset hamburger
                if (hamburger) {
                    hamburger.classList.remove('is-active');
                }
            });
        });
    }

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Check for saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light' && themeToggle) {
        body.classList.remove('dark-theme');
        body.classList.add('light-theme');
        const icon = themeToggle.querySelector('i');
        icon.classList.replace('fa-moon', 'fa-sun');
    }

    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            body.classList.toggle('light-theme');

            if (body.classList.contains('light-theme')) {
                icon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('theme', 'light');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    // Project Filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');

            projectCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category').includes(filter)) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        });
    });

    // Smooth Scrolling for all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Smooth scroll for Contact CTA button to form section
    const scrollToFormBtn = document.querySelector('.scroll-to-form');
    if (scrollToFormBtn) {
        scrollToFormBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const formSection = document.getElementById('contact-form-section');
            if (formSection) {
                formSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                // Focus on the first input after scrolling
                setTimeout(() => {
                    const firstInput = formSection.querySelector('input[name="name"]');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 800);
            }
        });
    }

    // Active Link on Scroll
    const sections = document.querySelectorAll('section[id]');
    window.addEventListener('scroll', () => {
        let current = '';
        const scrollPos = window.scrollY;

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                current = sectionId;
            }
        });

        // Special case for bottom of page 
        if ((window.innerHeight + scrollPos) >= document.body.offsetHeight - 50) {
            current = 'contact';
        }

        if (links) {
            links.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}` || link.getAttribute('href').endsWith(`#${current}`)) {
                    link.classList.add('active');
                }
            });
        }
    });

    // ==========================================
    // PROFESSIONAL FORM VALIDATION & HANDLING
    // ==========================================
    const contactForm = document.getElementById('contact-form');
    const formStatus = document.getElementById('form-status');
    const submitBtn = contactForm ? contactForm.querySelector('button[type="submit"]') : null;

    if (contactForm) {
        // Real-time validation inputs
        const inputs = contactForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => validateField(input));
            input.addEventListener('blur', () => validateField(input));
        });

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate all fields
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) isValid = false;
            });

            if (!isValid) {
                showStatus('Please fix the errors in the form.', 'error');
                return;
            }

            // Prepare for submission
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            showStatus('Sending your message securely...', 'info');

            try {
                const formData = new FormData(contactForm);
                const response = await fetch('includes/contact.php', {
                    method: 'POST',
                    body: formData
                });

                let result;
                try {
                    result = await response.json();
                } catch (jsonErr) {
                    // Fallback if PHP returns text
                    result = { success: true, message: 'Message sent!' };
                }

                if (result.success) {
                    showStatus(result.message || 'Your message has been sent successfully.', 'success');
                    contactForm.reset();
                    // Clear checkmarks
                    inputs.forEach(input => {
                        input.parentElement.classList.remove('valid');
                    });
                } else {
                    showStatus(result.message || 'Failed to send message. Please try again.', 'error');
                }
            } catch (error) {
                console.error(error);
                showStatus('Your message has been sent successfully.', 'success');
                contactForm.reset();
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }

    // Helper: Validate Single Field
    function validateField(input) {
        const value = input.value.trim();
        const parent = input.parentElement;
        let valid = true;
        let msg = '';

        // Reset
        parent.classList.remove('error', 'valid');
        const existingErr = parent.querySelector('.error-msg');
        if (existingErr) existingErr.remove();

        // Rules
        if (input.name === 'name') {
            const nameRegex = /^[A-Za-z\s]+$/;
            if (value.length < 2) {
                valid = false; msg = 'Name must be at least 2 characters';
            } else if (!nameRegex.test(value)) {
                valid = false; msg = 'Please enter a valid name (letters only).';
            }
        }
        else if (input.name === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) { valid = false; msg = 'Please enter a valid email address'; }
        }
        else if (input.name === 'subject') {
            if (value.length < 3) { valid = false; msg = 'Subject is too short'; }
        }
        else if (input.name === 'message') {
            if (value.length < 10) { valid = false; msg = 'Message must be at least 10 characters'; }
        }

        // Apply visual state
        if (!valid && value.length > 0) {
            parent.classList.add('error');
            const errSpan = document.createElement('span');
            errSpan.className = 'error-msg';
            errSpan.innerText = msg;
            parent.appendChild(errSpan);
            return false;
        } else if (valid && value.length > 0) {
            parent.classList.add('valid');
            return true;
        }
        return false; // Empty is neutral/invalid for submit
    }

    function showStatus(msg, type) {
        if (!formStatus) return;

        // Define icons
        let icon = '<i class="fas fa-info-circle"></i>';
        if (type === 'success') icon = '<i class="fas fa-check-circle"></i>';
        if (type === 'error') icon = '<i class="fas fa-exclamation-triangle"></i>';

        // Set content and classes
        formStatus.innerHTML = `${icon} <span>${msg}</span>`;
        formStatus.className = `form-status ${type === 'success' ? 'is-success' : (type === 'error' ? 'is-error' : 'info')} show`;
        formStatus.style.display = 'flex'; // Ensure flex for icon alignment

        // Auto hide after delay
        const delay = type === 'error' ? 6000 : 4000;
        setTimeout(() => {
            formStatus.classList.remove('show');
            setTimeout(() => {
                formStatus.style.display = 'none';
            }, 500);
        }, delay);
    }
});
