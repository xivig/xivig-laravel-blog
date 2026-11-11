<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    exclude-result-prefixes="sitemap image">

    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/">
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Master XML Sitemap Codex | XIVIG Maison Gazette</title>
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
                <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
                <style>
                    :root {
                        --bg-canvas: #090d10;
                        --bg-card: #12171c;
                        --bg-card-hover: #161c22;
                        --bg-accent: #1a2128;
                        --border-subtle: rgba(201, 169, 110, 0.18);
                        --border-glow: rgba(201, 169, 110, 0.35);
                        --text-primary: #f8fafc;
                        --text-secondary: #94a3b8;
                        --text-muted: #64748b;
                        --gold-primary: #c9a96e;
                        --gold-light: #e8d5b5;
                        --gold-glow: rgba(201, 169, 110, 0.15);
                        --emerald: #10b981;
                        --cyan: #06b6d4;
                    }
                    * {
                        box-sizing: border-box;
                        margin: 0;
                        padding: 0;
                    }
                    body {
                        background-color: var(--bg-canvas);
                        color: var(--text-primary);
                        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
                        line-height: 1.6;
                        padding: 2.5rem 1.5rem;
                        min-height: 100vh;
                        -webkit-font-smoothing: antialiased;
                    }
                    .sitemap-wrapper {
                        max-width: 1200px;
                        margin: 0 auto;
                    }
                    /* Top Navigation Bar */
                    .top-nav {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding-bottom: 2rem;
                        border-bottom: 1px solid var(--border-subtle);
                        margin-bottom: 2.5rem;
                    }
                    .brand-title {
                        font-family: 'Cinzel', serif;
                        font-size: 1.25rem;
                        font-weight: 700;
                        letter-spacing: 0.15em;
                        color: var(--gold-primary);
                        text-decoration: none;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                    }
                    .brand-badge {
                        background: var(--gold-glow);
                        border: 1px solid var(--border-subtle);
                        color: var(--gold-light);
                        font-size: 0.7rem;
                        padding: 0.2rem 0.6rem;
                        border-radius: 999px;
                        letter-spacing: 0.08em;
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        font-weight: 600;
                        text-transform: uppercase;
                    }
                    .nav-links {
                        display: flex;
                        gap: 1.25rem;
                        align-items: center;
                    }
                    .nav-link {
                        color: var(--text-secondary);
                        text-decoration: none;
                        font-size: 0.85rem;
                        font-weight: 500;
                        transition: all 0.2s ease;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.4rem;
                    }
                    .nav-link:hover {
                        color: var(--gold-primary);
                    }
                    /* Hero Header */
                    .hero-header {
                        margin-bottom: 2.5rem;
                    }
                    .hero-header h1 {
                        font-family: 'Cinzel', serif;
                        font-size: 2.25rem;
                        font-weight: 700;
                        color: #ffffff;
                        letter-spacing: 0.04em;
                        margin-bottom: 0.6rem;
                    }
                    .hero-header p {
                        color: var(--text-secondary);
                        font-size: 1rem;
                        max-width: 780px;
                    }
                    /* Notice Box */
                    .notice-box {
                        background: linear-gradient(135deg, rgba(201, 169, 110, 0.08) 0%, rgba(18, 23, 28, 0.6) 100%);
                        border: 1px solid var(--border-subtle);
                        border-left: 3px solid var(--gold-primary);
                        padding: 1rem 1.25rem;
                        border-radius: 8px;
                        margin-bottom: 2rem;
                        font-size: 0.88rem;
                        color: var(--text-secondary);
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        flex-wrap: wrap;
                        gap: 1rem;
                    }
                    .notice-box strong {
                        color: var(--gold-light);
                    }
                    /* Metrics Grid */
                    .metrics-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                        gap: 1.25rem;
                        margin-bottom: 2.5rem;
                    }
                    .metric-card {
                        background: var(--bg-card);
                        border: 1px solid var(--border-subtle);
                        border-radius: 12px;
                        padding: 1.25rem;
                        transition: transform 0.2s ease, border-color 0.2s ease;
                    }
                    .metric-card:hover {
                        border-color: var(--border-glow);
                        transform: translateY(-2px);
                    }
                    .metric-label {
                        font-size: 0.72rem;
                        text-transform: uppercase;
                        letter-spacing: 0.1em;
                        color: var(--text-muted);
                        font-weight: 600;
                        margin-bottom: 0.4rem;
                    }
                    .metric-value {
                        font-family: 'Cinzel', serif;
                        font-size: 1.85rem;
                        font-weight: 700;
                        color: var(--gold-light);
                    }
                    /* Search &amp; Controls Bar */
                    .controls-bar {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 1rem;
                        margin-bottom: 1.5rem;
                    }
                    .search-box {
                        position: relative;
                        flex: 1;
                        max-width: 420px;
                    }
                    .search-input {
                        width: 100%;
                        background: var(--bg-card);
                        border: 1px solid var(--border-subtle);
                        border-radius: 999px;
                        padding: 0.7rem 1.2rem;
                        font-size: 0.88rem;
                        color: #ffffff;
                        outline: none;
                        transition: all 0.2s ease;
                    }
                    .search-input:focus {
                        border-color: var(--gold-primary);
                        box-shadow: 0 0 0 3px var(--gold-glow);
                    }
                    .tab-filters {
                        display: flex;
                        gap: 0.5rem;
                        flex-wrap: wrap;
                    }
                    .tab-btn {
                        background: var(--bg-card);
                        border: 1px solid var(--border-subtle);
                        color: var(--text-secondary);
                        padding: 0.45rem 0.9rem;
                        border-radius: 999px;
                        font-size: 0.78rem;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    }
                    .tab-btn:hover, .tab-btn.active {
                        background: var(--gold-primary);
                        color: #0b0f12;
                        border-color: var(--gold-primary);
                    }
                    /* Data Table Card */
                    .table-card {
                        background: var(--bg-card);
                        border: 1px solid var(--border-subtle);
                        border-radius: 14px;
                        overflow: hidden;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
                        margin-bottom: 2.5rem;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        text-align: left;
                        font-size: 0.88rem;
                    }
                    thead th {
                        background: #0f1318;
                        color: var(--text-muted);
                        text-transform: uppercase;
                        font-size: 0.72rem;
                        letter-spacing: 0.1em;
                        font-weight: 700;
                        padding: 1rem 1.25rem;
                        border-bottom: 1px solid var(--border-subtle);
                    }
                    tbody tr {
                        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
                        transition: background-color 0.15s ease;
                    }
                    tbody tr:hover {
                        background-color: var(--bg-card-hover);
                    }
                    tbody td {
                        padding: 1rem 1.25rem;
                        vertical-align: middle;
                    }
                    .url-cell a {
                        color: #f1f5f9;
                        text-decoration: none;
                        font-weight: 500;
                        display: inline-block;
                        max-width: 600px;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        transition: color 0.15s ease;
                    }
                    .url-cell a:hover {
                        color: var(--gold-primary);
                        text-decoration: underline;
                    }
                    .priority-badge {
                        display: inline-block;
                        padding: 0.2rem 0.6rem;
                        border-radius: 6px;
                        font-size: 0.75rem;
                        font-weight: 700;
                        font-family: monospace;
                    }
                    .priority-high {
                        background: rgba(201, 169, 110, 0.15);
                        color: var(--gold-light);
                        border: 1px solid rgba(201, 169, 110, 0.3);
                    }
                    .priority-med {
                        background: rgba(16, 185, 129, 0.12);
                        color: #6ee7b7;
                        border: 1px solid rgba(16, 185, 129, 0.25);
                    }
                    .priority-low {
                        background: rgba(148, 163, 184, 0.12);
                        color: #cbd5e1;
                        border: 1px solid rgba(148, 163, 184, 0.25);
                    }
                    .freq-badge {
                        color: var(--text-secondary);
                        font-size: 0.75rem;
                        text-transform: capitalize;
                    }
                    .date-cell {
                        color: var(--text-muted);
                        font-size: 0.8rem;
                        white-space: nowrap;
                    }
                    .image-badge {
                        background: rgba(6, 182, 212, 0.12);
                        color: var(--cyan);
                        border: 1px solid rgba(6, 182, 212, 0.25);
                        padding: 0.2rem 0.5rem;
                        border-radius: 4px;
                        font-size: 0.72rem;
                        font-weight: 600;
                    }
                    /* Footer */
                    .sitemap-footer {
                        text-align: center;
                        padding-top: 2rem;
                        border-top: 1px solid var(--border-subtle);
                        color: var(--text-muted);
                        font-size: 0.82rem;
                    }
                    .sitemap-footer a {
                        color: var(--gold-primary);
                        text-decoration: none;
                    }
                    .sitemap-footer a:hover {
                        text-decoration: underline;
                    }
                    @media (max-width: 768px) {
                        body { padding: 1.5rem 1rem; }
                        .url-cell a { max-width: 260px; }
                        .hide-mobile { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="sitemap-wrapper">
                    <!-- Top Navigation Bar -->
                    <nav class="top-nav">
                        <a href="/" class="brand-title">
                            <span>XIVIG MAISON GAZETTE</span>
                            <span class="brand-badge">Sitemap 0.9</span>
                        </a>
                        <div class="nav-links">
                            <a href="/" class="nav-link">Home</a>
                            <a href="/sitemap" class="nav-link">HTML Directory</a>
                            <a href="/feed.xml" class="nav-link">RSS 2.0 Feed</a>
                            <a href="/robots.txt" class="nav-link">Robots.txt</a>
                        </div>
                    </nav>

                    <!-- Hero Header -->
                    <header class="hero-header">
                        <h1>Master XML Sitemap Codex</h1>
                        <p>Complete search engine indexing codex for Google, Bing, and web syndication engines. Enhanced with Maison XSLT styling for human inspection.</p>
                    </header>

                    <!-- Notice Box -->
                    <div class="notice-box">
                        <div>
                            <strong>Protocol Information:</strong> Standard Sitemaps 0.9 XML protocol with Google Image Sitemap 1.1 extension.
                        </div>
                        <div id="filter-status">
                            Showing <span id="visible-count"><xsl:value-of select="count(//sitemap:url)"/></span> of <xsl:value-of select="count(//sitemap:url)"/> URLs
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-label">Total Indexed URLs</div>
                            <div class="metric-value"><xsl:value-of select="count(//sitemap:url)"/></div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Editorial Stories &amp; Featured</div>
                            <div class="metric-value">
                                <xsl:value-of select="count(//sitemap:url[sitemap:priority &gt;= '0.8'])"/>
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Image Assets Attached</div>
                            <div class="metric-value">
                                <xsl:value-of select="count(//sitemap:url/image:image)"/>
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Indexing Authority</div>
                            <div class="metric-value" style="font-size: 1.35rem; padding-top: 0.35rem; color: #10b981;">Validated 200 OK</div>
                        </div>
                    </div>

                    <!-- Controls Bar -->
                    <div class="controls-bar">
                        <div class="search-box">
                            <input type="text" id="searchInput" class="search-input" placeholder="Search indexed locations, stories, tags..." autocomplete="off" />
                        </div>
                        <div class="tab-filters">
                            <button class="tab-btn active" data-filter="all">All URLs</button>
                            <button class="tab-btn" data-filter="chronicles">Chronicles</button>
                            <button class="tab-btn" data-filter="services">Services</button>
                            <button class="tab-btn" data-filter="categories">Categories</button>
                            <button class="tab-btn" data-filter="pages">Pages</button>
                            <button class="tab-btn" data-filter="authors-tags">Authors &amp; Tags</button>
                        </div>
                    </div>

                    <!-- Table Card -->
                    <div class="table-card">
                        <table id="sitemapTable">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">#</th>
                                    <th>Indexed Canonical URL</th>
                                    <th style="width: 100px;">Priority</th>
                                    <th style="width: 130px;" class="hide-mobile">Frequency</th>
                                    <th style="width: 180px;">Last Modified</th>
                                    <th style="width: 100px;" class="hide-mobile">Media</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="//sitemap:url">
                                    <tr class="sitemap-row">
                                        <xsl:attribute name="data-url">
                                            <xsl:value-of select="sitemap:loc"/>
                                        </xsl:attribute>
                                        <td class="date-cell" style="color: var(--text-muted);">
                                            <xsl:value-of select="position()"/>
                                        </td>
                                        <td class="url-cell">
                                            <a target="_blank">
                                                <xsl:attribute name="href">
                                                    <xsl:value-of select="sitemap:loc"/>
                                                </xsl:attribute>
                                                <xsl:value-of select="sitemap:loc"/>
                                            </a>
                                        </td>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="sitemap:priority &gt;= '0.9'">
                                                    <span class="priority-badge priority-high"><xsl:value-of select="sitemap:priority"/></span>
                                                </xsl:when>
                                                <xsl:when test="sitemap:priority &gt;= '0.7'">
                                                    <span class="priority-badge priority-med"><xsl:value-of select="sitemap:priority"/></span>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <span class="priority-badge priority-low"><xsl:value-of select="sitemap:priority"/></span>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </td>
                                        <td class="freq-badge hide-mobile">
                                            <xsl:value-of select="sitemap:changefreq"/>
                                        </td>
                                        <td class="date-cell">
                                            <xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/>
                                            <span style="font-size: 0.72rem; margin-left: 4px; opacity: 0.6;">
                                                <xsl:value-of select="substring(sitemap:lastmod, 12, 5)"/>
                                            </span>
                                        </td>
                                        <td class="hide-mobile">
                                            <xsl:if test="image:image">
                                                <span class="image-badge">Image</span>
                                            </xsl:if>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer -->
                    <footer class="sitemap-footer">
                        <p>XIVIG Maison Gazette &amp; Atelier &#8212; Architecture by Haute Couture &amp; Horology Guild.</p>
                        <p style="margin-top: 0.4rem;">
                            <a href="/">Home</a> &#183; 
                            <a href="/sitemap">HTML Sitemap</a> &#183; 
                            <a href="/feed.xml">RSS Syndication</a> &#183; 
                            <a href="/privacy-policy">Privacy Codex</a> &#183; 
                            <a href="/contact">Editorial Desk</a>
                        </p>
                    </footer>
                </div>

                <script>
                    <![CDATA[
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('searchInput');
                        const rows = Array.from(document.querySelectorAll('.sitemap-row'));
                        const countDisplay = document.getElementById('visible-count');
                        const tabBtns = document.querySelectorAll('.tab-btn');
                        let currentFilter = 'all';

                        function filterRows() {
                            const query = searchInput.value.toLowerCase().trim();
                            let visible = 0;

                            rows.forEach(row => {
                                const url = (row.getAttribute('data-url') || '').toLowerCase();
                                const textMatch = !query || url.includes(query);
                                let tabMatch = true;

                                if (currentFilter === 'chronicles') {
                                    tabMatch = url.includes('/post/') || url.includes('/chronicle/') || (!url.includes('/category/') && !url.includes('/services') && !url.includes('/page/') && !url.includes('/author/') && !url.includes('/tag/') && url !== window.location.origin + '/');
                                } else if (currentFilter === 'services') {
                                    tabMatch = url.includes('/services');
                                } else if (currentFilter === 'categories') {
                                    tabMatch = url.includes('/category/');
                                } else if (currentFilter === 'pages') {
                                    tabMatch = url.includes('/page/') || url.includes('/contact');
                                } else if (currentFilter === 'authors-tags') {
                                    tabMatch = url.includes('/author/') || url.includes('/tag/');
                                }

                                if (textMatch && tabMatch) {
                                    row.style.display = '';
                                    visible++;
                                } else {
                                    row.style.display = 'none';
                                }
                            });

                            if (countDisplay) countDisplay.textContent = visible;
                        }

                        if (searchInput) {
                            searchInput.addEventListener('input', filterRows);
                        }

                        tabBtns.forEach(btn => {
                            btn.addEventListener('click', function() {
                                tabBtns.forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
                                currentFilter = this.getAttribute('data-filter') || 'all';
                                filterRows();
                            });
                        });
                    });
                    ]]>
                </script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
