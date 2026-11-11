/**
 * ==============================================================================
 * XIVIG MAISON GAZETTE - ULTRAMODERN CLIENT INTERACTION CONTROLLER (2026/2027)
 * ==============================================================================
 * Powers real-time world capital clocks, glassmorphic scroll elevation,
 * instant command palette, font scaler, like mechanics, toast feedback & TOC.
 * ==============================================================================
 */
(function() {
    'use strict';

    // 1. REAL-TIME MULTI-CAPITAL CLOCKS
    function updateCapitalClocks() {
        const now = new Date();
        const options = { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };

        const parisEl = document.getElementById('clockParis');
        const genevaEl = document.getElementById('clockGeneva');
        const nycEl = document.getElementById('clockNYC');
        const tokyoEl = document.getElementById('clockTokyo');

        if (parisEl) {
            parisEl.textContent = new Intl.DateTimeFormat('en-GB', { ...options, timeZone: 'Europe/Paris' }).format(now);
        }
        if (genevaEl) {
            genevaEl.textContent = new Intl.DateTimeFormat('en-GB', { ...options, timeZone: 'Europe/Zurich' }).format(now);
        }
        if (nycEl) {
            nycEl.textContent = new Intl.DateTimeFormat('en-US', { ...options, timeZone: 'America/New_York' }).format(now);
        }
        if (tokyoEl) {
            tokyoEl.textContent = new Intl.DateTimeFormat('en-GB', { ...options, timeZone: 'Asia/Tokyo' }).format(now);
        }
    }
    updateCapitalClocks();
    setInterval(updateCapitalClocks, 1000);

    // 2. SCROLL ELEVATION FOR GLASSMORPHIC HEADER
    const siteHeader = document.querySelector('.site-header');
    if (siteHeader) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 30) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        }, { passive: true });
    }

    // 3. ZERO-FLASH THEME CONTROLLER
    const themeBtn = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');
    const mobileThemeBtn = document.getElementById('mobileThemeToggleBtn');

    function updateThemeVisuals(theme) {
        const isDark = theme === 'dark';
        if (themeIcon) {
            themeIcon.className = isDark ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill text-gold';
        }
        const mobileIcon = document.getElementById('mobileThemeIcon');
        if (mobileIcon) {
            mobileIcon.className = isDark ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill text-gold';
        }
    }

    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    updateThemeVisuals(currentTheme);

    function toggleTheme() {
        const active = document.documentElement.getAttribute('data-bs-theme');
        const next = active === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', next);
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('xivig_blog_theme', next);
        updateThemeVisuals(next);
        showXivigToast(next === 'dark' ? 'Night Edition Activated' : 'Day Gallery Activated');
    }

    if (themeBtn) themeBtn.addEventListener('click', toggleTheme);
    if (mobileThemeBtn) mobileThemeBtn.addEventListener('click', toggleTheme);

    // 4. FLOATING TOAST NOTIFICATION
    window.showXivigToast = function(msg) {
        let toast = document.getElementById('xivigToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'xivigToast';
            toast.className = 'xivig-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 2600);
    };

    // 5. COPY ARTICLE LINK TO CLIPBOARD
    window.copyCurrentLink = function() {
        if (navigator.clipboard && window.location.href) {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showXivigToast('Chronicle link copied to clipboard');
                const btnText = document.getElementById('copyLinkBtnText');
                if (btnText) {
                    const original = btnText.textContent;
                    btnText.textContent = 'Copied!';
                    setTimeout(() => { btnText.textContent = original; }, 2000);
                }
            }).catch(() => {
                showFallbackCopy();
            });
        } else {
            showFallbackCopy();
        }
    };

    function showFallbackCopy() {
        showXivigToast('Link copied: ' + window.location.href);
    }

    // 6. READING PROGRESS & FLOATING DOCK TRACKER
    window.addEventListener('scroll', function() {
        const article = document.querySelector('.article-detail-container');
        if (!article) return;

        const rect = article.getBoundingClientRect();
        const winHeight = window.innerHeight;
        const totalHeight = article.offsetHeight - winHeight;
        const scrolled = -rect.top;
        const progress = Math.max(0, Math.min(100, Math.round((scrolled / totalHeight) * 100)));

        const bar = document.getElementById('readingProgressBar');
        if (bar) bar.style.width = progress + '%';

        const dockProgress = document.getElementById('dockProgressText');
        if (dockProgress) {
            dockProgress.textContent = progress + '%';
        }
    }, { passive: true });

    // 7. DYNAMIC FONT SIZE ADJUSTER FOR ARTICLE READER
    const fontSizes = ['font-size-sm', 'font-size-md', 'font-size-lg'];
    let currentSizeIndex = 1; // default md

    const savedSize = localStorage.getItem('xivig_article_font_size');
    if (savedSize && fontSizes.includes(savedSize)) {
        currentSizeIndex = fontSizes.indexOf(savedSize);
        applyArticleFontSize();
    }

    function applyArticleFontSize() {
        const bodyContent = document.getElementById('articleBodyContent');
        if (!bodyContent) return;
        fontSizes.forEach(cls => bodyContent.classList.remove(cls));
        bodyContent.classList.add(fontSizes[currentSizeIndex]);
        localStorage.setItem('xivig_article_font_size', fontSizes[currentSizeIndex]);
    }

    window.toggleArticleFontSize = function() {
        currentSizeIndex = (currentSizeIndex + 1) % fontSizes.length;
        applyArticleFontSize();
        const labels = ['Compact Type', 'Standard Type', 'Expansive Type'];
        showXivigToast('Reading Mode: ' + labels[currentSizeIndex]);
    };

    // 8. INTERACTIVE AJAX LIKE COUNTER WITH HEART BURST
    const likeBtn = document.getElementById('likeArticleBtn');
    const dockLikeBtn = document.getElementById('dockLikeBtn');

    function handleLikePost(postId) {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

        fetch(`/article/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const counter = document.getElementById('likeCounterText');
                if (counter) counter.textContent = data.likes_count;

                const dockCounter = document.getElementById('dockLikeCounter');
                if (dockCounter) dockCounter.textContent = data.likes_count;

                const heartIcon = document.getElementById('likeHeartIcon');
                if (heartIcon) {
                    heartIcon.classList.remove('heart-pulse');
                    void heartIcon.offsetWidth; // trigger reflow
                    heartIcon.classList.add('heart-pulse');
                }
                const dockHeartIcon = document.getElementById('dockLikeHeartIcon');
                if (dockHeartIcon) {
                    dockHeartIcon.classList.remove('heart-pulse');
                    void dockHeartIcon.offsetWidth;
                    dockHeartIcon.classList.add('heart-pulse');
                }

                showXivigToast('Chronicle appreciated (+1)');
            }
        })
        .catch(err => console.error('Error liking chronicle:', err));
    }

    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            handleLikePost(this.getAttribute('data-post-id'));
        });
    }
    if (dockLikeBtn) {
        dockLikeBtn.addEventListener('click', function() {
            handleLikePost(this.getAttribute('data-post-id'));
        });
    }

    // 9. INSTANT PREDICTIVE COMMAND PALETTE SEARCH (Ctrl + K)
    const liveInput = document.getElementById('liveSearchInput');
    const liveResults = document.getElementById('liveSearchResults');
    let searchDebounce = null;

    if (liveInput && liveResults) {
        liveInput.addEventListener('input', function() {
            clearTimeout(searchDebounce);
            const query = this.value.trim();

            if (query.length < 2) {
                liveResults.innerHTML = `
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-search display-6 opacity-25 d-block mb-2"></i>
                        <span>Type at least 2 characters to discover curated chronicles...</span>
                    </div>
                `;
                return;
            }

            searchDebounce = setTimeout(function() {
                liveResults.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                        <span class="ms-2 extra-small text-muted">Searching the Maison Gazette...</span>
                    </div>
                `;

                fetch('/api/search?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        if (!data.results || data.results.length === 0) {
                            liveResults.innerHTML = `
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-journal-x display-6 opacity-25 d-block mb-2"></i>
                                    <span>No chronicles matching "<strong>${escapeHtml(query)}</strong>" found.</span>
                                </div>
                            `;
                            return;
                        }

                        let html = '<div class="d-flex flex-column gap-2">';
                        data.results.forEach(item => {
                            html += `
                                <a href="${item.url}" class="live-search-item">
                                    ${item.image ? `<img src="${item.image}" width="48" height="48" class="rounded-3 object-fit-cover flex-shrink-0" alt="">` : `
                                    <div class="rounded-3 bg-secondary-subtle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                                        <i class="bi bi-journal-richtext text-gold"></i>
                                    </div>
                                    `}
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-gold-subtle text-gold extra-small">${escapeHtml(item.category)}</span>
                                            <span class="text-muted extra-small">${escapeHtml(item.reading_time)}</span>
                                        </div>
                                        <div class="fw-semibold text-truncate small text-body">${escapeHtml(item.title)}</div>
                                    </div>
                                    <i class="bi bi-arrow-right text-gold opacity-75"></i>
                                </a>
                            `;
                        });
                        html += '</div>';
                        liveResults.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Live search error:', err);
                        liveResults.innerHTML = '<p class="text-danger small mb-0 p-3 text-center">Unable to fetch search results at this time.</p>';
                    });
            }, 200);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // 10. GLOBAL SHORTCUT FOR SEARCH (Ctrl + K / Cmd + K)
    window.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const modalEl = document.getElementById('searchModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
                setTimeout(() => {
                    if (liveInput) liveInput.focus();
                }, 250);
            }
        }
    });

    // 11. COMMENT REPLY HELPER
    window.replyToComment = function(commentId, authorName) {
        const parentInput = document.getElementById('commentParentId');
        if (parentInput) parentInput.value = commentId;

        const area = document.getElementById('commentsArea');
        if (area) {
            area.scrollIntoView({ behavior: 'smooth' });
            const textarea = area.querySelector('textarea');
            if (textarea) {
                textarea.placeholder = `Replying to @${authorName}...`;
                textarea.focus();
            }
        }
    };

    // 12. DYNAMIC TABLE OF CONTENTS & SCROLLSPY
    (function initTOC() {
        const body = document.getElementById('articleBodyContent');
        const tocWidget = document.getElementById('tableOfContentsWidget');
        const tocList = document.getElementById('tocList');
        if (!body || !tocWidget || !tocList) return;

        const headings = body.querySelectorAll('h2, h3');
        if (headings.length < 2) return;

        headings.forEach((heading, idx) => {
            const id = heading.id || 'chronicle-section-' + (idx + 1);
            heading.id = id;

            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = '#' + id;
            a.className = 'toc-link' + (heading.tagName.toLowerCase() === 'h3' ? ' ps-4 extra-small opacity-75' : '');
            a.textContent = heading.textContent.trim();
            li.appendChild(a);
            tocList.appendChild(li);
        });

        tocWidget.classList.remove('d-none');

        // Scrollspy for active heading highlight
        window.addEventListener('scroll', function() {
            let currentId = '';
            headings.forEach(heading => {
                const rect = heading.getBoundingClientRect();
                if (rect.top <= 140) {
                    currentId = heading.id;
                }
            });

            document.querySelectorAll('.toc-link').forEach(link => {
                const isActive = link.getAttribute('href') === '#' + currentId;
                link.classList.toggle('active', isActive);
            });
        }, { passive: true });
    })();

    // 7. EDITORIAL DROPDOWN FLICKER-FREE & TOUCH SUPPORT
    (function initEditorialDropdowns() {
        document.querySelectorAll('.nav-editorial-dropdown').forEach(dropdown => {
            const trigger = dropdown.querySelector('.nav-editorial-link');
            if (!trigger) return;

            // Prevent '#' jump on click for dropdown triggers
            trigger.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#' || this.getAttribute('role') === 'button') {
                    e.preventDefault();
                    dropdown.classList.toggle('is-open');
                }
            });
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-editorial-dropdown')) {
                document.querySelectorAll('.nav-editorial-dropdown.is-open').forEach(d => {
                    d.classList.remove('is-open');
                });
            }
        });
    })();

})();
