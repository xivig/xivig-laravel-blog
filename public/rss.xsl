<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:media="http://search.yahoo.com/mrss/"
    exclude-result-prefixes="atom dc content media">

    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/">
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title><xsl:value-of select="/rss/channel/title"/> &#8212; RSS Syndication Feed</title>
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
                    .feed-wrapper {
                        max-width: 960px;
                        margin: 0 auto;
                    }
                    /* Navigation */
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
                        background: rgba(245, 158, 11, 0.15);
                        border: 1px solid rgba(245, 158, 11, 0.3);
                        color: #fcd34d;
                        font-size: 0.7rem;
                        padding: 0.2rem 0.6rem;
                        border-radius: 999px;
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
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
                        transition: color 0.2s ease;
                    }
                    .nav-link:hover {
                        color: var(--gold-primary);
                    }
                    /* Channel Hero Card */
                    .channel-hero {
                        background: var(--bg-card);
                        border: 1px solid var(--border-subtle);
                        border-radius: 16px;
                        padding: 2.5rem 2rem;
                        margin-bottom: 2.5rem;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
                        position: relative;
                        overflow: hidden;
                    }
                    .channel-hero::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        height: 3px;
                        background: linear-gradient(90deg, #c9a96e 0%, #e0c58e 50%, #b89355 100%);
                    }
                    .channel-title {
                        font-family: 'Cinzel', serif;
                        font-size: 2.15rem;
                        font-weight: 700;
                        color: #ffffff;
                        letter-spacing: 0.03em;
                        margin-bottom: 0.75rem;
                    }
                    .channel-desc {
                        color: var(--text-secondary);
                        font-size: 1.05rem;
                        max-width: 720px;
                        margin-bottom: 1.75rem;
                    }
                    .channel-meta {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 1.5rem;
                        align-items: center;
                        padding-top: 1.5rem;
                        border-top: 1px solid rgba(255, 255, 255, 0.06);
                    }
                    .meta-pill {
                        font-size: 0.8rem;
                        color: var(--text-muted);
                        display: flex;
                        align-items: center;
                        gap: 0.4rem;
                    }
                    .meta-pill strong {
                        color: var(--gold-light);
                    }
                    /* Action Buttons */
                    .action-buttons {
                        display: flex;
                        gap: 0.75rem;
                        flex-wrap: wrap;
                        margin-top: 1.5rem;
                    }
                    .btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 0.6rem 1.25rem;
                        border-radius: 999px;
                        font-size: 0.85rem;
                        font-weight: 600;
                        cursor: pointer;
                        text-decoration: none;
                        transition: all 0.2s ease;
                    }
                    .btn-gold {
                        background: var(--gold-primary);
                        color: #0b0f12;
                        border: 1px solid var(--gold-primary);
                    }
                    .btn-gold:hover {
                        background: var(--gold-light);
                        box-shadow: 0 4px 15px rgba(201, 169, 110, 0.35);
                    }
                    .btn-outline {
                        background: transparent;
                        color: var(--text-primary);
                        border: 1px solid var(--border-subtle);
                    }
                    .btn-outline:hover {
                        border-color: var(--gold-primary);
                        color: var(--gold-primary);
                    }
                    /* Explanatory Banner */
                    .info-banner {
                        background: rgba(18, 23, 28, 0.6);
                        border: 1px solid var(--border-subtle);
                        border-left: 3px solid var(--gold-primary);
                        border-radius: 10px;
                        padding: 1rem 1.25rem;
                        margin-bottom: 2rem;
                        font-size: 0.88rem;
                        color: var(--text-secondary);
                    }
                    .info-banner strong {
                        color: var(--gold-light);
                    }
                    /* Items List */
                    .items-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 1.5rem;
                    }
                    .items-title {
                        font-family: 'Cinzel', serif;
                        font-size: 1.35rem;
                        color: #ffffff;
                        letter-spacing: 0.05em;
                    }
                    .items-count {
                        font-size: 0.85rem;
                        color: var(--text-muted);
                    }
                    .feed-stream {
                        display: flex;
                        flex-direction: column;
                        gap: 1.5rem;
                        margin-bottom: 3rem;
                    }
                    .item-card {
                        background: var(--bg-card);
                        border: 1px solid var(--border-subtle);
                        border-radius: 14px;
                        padding: 1.75rem;
                        transition: all 0.2s ease;
                    }
                    .item-card:hover {
                        border-color: var(--border-glow);
                        transform: translateY(-2px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
                    }
                    .item-top-row {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 0.5rem;
                        margin-bottom: 0.75rem;
                    }
                    .item-category {
                        background: var(--gold-glow);
                        border: 1px solid var(--border-subtle);
                        color: var(--gold-light);
                        font-size: 0.72rem;
                        font-weight: 700;
                        padding: 0.2rem 0.65rem;
                        border-radius: 999px;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                    }
                    .item-date {
                        font-size: 0.78rem;
                        color: var(--text-muted);
                    }
                    .item-title {
                        font-family: 'Cinzel', serif;
                        font-size: 1.35rem;
                        font-weight: 700;
                        line-height: 1.35;
                        margin-bottom: 0.75rem;
                    }
                    .item-title a {
                        color: #ffffff;
                        text-decoration: none;
                        transition: color 0.15s ease;
                    }
                    .item-title a:hover {
                        color: var(--gold-primary);
                    }
                    .item-body {
                        display: flex;
                        gap: 1.5rem;
                        align-items: flex-start;
                    }
                    .item-thumb {
                        width: 140px;
                        height: 95px;
                        object-fit: cover;
                        border-radius: 8px;
                        border: 1px solid var(--border-subtle);
                        flex-shrink: 0;
                    }
                    .item-desc {
                        color: var(--text-secondary);
                        font-size: 0.92rem;
                        line-height: 1.6;
                        flex: 1;
                    }
                    .item-footer {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-top: 1.25rem;
                        padding-top: 1rem;
                        border-top: 1px solid rgba(255, 255, 255, 0.05);
                        font-size: 0.82rem;
                    }
                    .item-author {
                        color: var(--text-muted);
                    }
                    .item-author strong {
                        color: var(--text-secondary);
                    }
                    .read-more {
                        color: var(--gold-primary);
                        text-decoration: none;
                        font-weight: 600;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.35rem;
                        transition: all 0.15s ease;
                    }
                    .read-more:hover {
                        color: var(--gold-light);
                        transform: translateX(3px);
                    }
                    /* Toast */
                    .toast-alert {
                        position: fixed;
                        bottom: 2rem;
                        right: 2rem;
                        background: #1e252e;
                        color: var(--gold-light);
                        border: 1px solid var(--gold-primary);
                        padding: 0.8rem 1.4rem;
                        border-radius: 999px;
                        font-size: 0.88rem;
                        font-weight: 600;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
                        opacity: 0;
                        pointer-events: none;
                        transition: opacity 0.3s ease;
                        z-index: 9999;
                    }
                    .toast-alert.show {
                        opacity: 1;
                    }
                    /* Footer */
                    .feed-footer {
                        text-align: center;
                        padding-top: 2rem;
                        border-top: 1px solid var(--border-subtle);
                        color: var(--text-muted);
                        font-size: 0.82rem;
                    }
                    .feed-footer a {
                        color: var(--gold-primary);
                        text-decoration: none;
                    }
                    .feed-footer a:hover {
                        text-decoration: underline;
                    }
                    @media (max-width: 640px) {
                        body { padding: 1.5rem 1rem; }
                        .channel-title { font-size: 1.65rem; }
                        .item-body { flex-direction: column; }
                        .item-thumb { width: 100%; height: 160px; }
                    }
                </style>
            </head>
            <body>
                <div class="feed-wrapper">
                    <!-- Top Navigation Bar -->
                    <nav class="top-nav">
                        <a href="/" class="brand-title">
                            <span>XIVIG MAISON GAZETTE</span>
                            <span class="brand-badge">RSS 2.0</span>
                        </a>
                        <div class="nav-links">
                            <a href="/" class="nav-link">Home</a>
                            <a href="/sitemap.xml" class="nav-link">Sitemap</a>
                            <a href="/sitemap" class="nav-link">Directory</a>
                            <a href="/contact" class="nav-link">Editorial Desk</a>
                        </div>
                    </nav>

                    <!-- Channel Hero Card -->
                    <section class="channel-hero">
                        <h1 class="channel-title"><xsl:value-of select="/rss/channel/title"/></h1>
                        <p class="channel-desc"><xsl:value-of select="/rss/channel/description"/></p>
                        
                        <div class="channel-meta">
                            <div class="meta-pill">Language: <strong><xsl:value-of select="/rss/channel/language"/></strong></div>
                            <div class="meta-pill">Total Chronicles: <strong><xsl:value-of select="count(/rss/channel/item)"/></strong></div>
                            <div class="meta-pill">Format: <strong>RSS 2.0 + Media RSS</strong></div>
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-gold" id="copyFeedBtn">
                                <span>Copy Feed URL</span>
                            </button>
                            <a class="btn btn-outline" target="_blank">
                                <xsl:attribute name="href">
                                    https://feedly.com/i/subscription/feed/<xsl:value-of select="/rss/channel/link"/>/feed.xml
                                </xsl:attribute>
                                <span>Open in Feedly</span>
                            </a>
                            <a class="btn btn-outline" target="_blank">
                                <xsl:attribute name="href">
                                    https://www.inoreader.com/feed/<xsl:value-of select="/rss/channel/link"/>/feed.xml
                                </xsl:attribute>
                                <span>Open in Inoreader</span>
                            </a>
                        </div>
                    </section>

                    <!-- Informational Banner -->
                    <div class="info-banner">
                        <strong>Syndication Feed:</strong> This stream broadcasts latest chronicles directly to RSS newsreaders (Feedly, NetNewsWire, Apple News, Reeder). Styled via Maison Gazette XSLT for human preview.
                    </div>

                    <!-- Items List Header -->
                    <div class="items-header">
                        <h2 class="items-title">Recent Gazette Dispatches</h2>
                        <span class="items-count">Displaying latest <xsl:value-of select="count(/rss/channel/item)"/> chronicles</span>
                    </div>

                    <!-- Articles Stream -->
                    <div class="feed-stream">
                        <xsl:for-each select="/rss/channel/item">
                            <article class="item-card">
                                <div class="item-top-row">
                                    <xsl:if test="category">
                                        <span class="item-category"><xsl:value-of select="category"/></span>
                                    </xsl:if>
                                    <span class="item-date"><xsl:value-of select="pubDate"/></span>
                                </div>
                                
                                <h3 class="item-title">
                                    <a>
                                        <xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
                                        <xsl:value-of select="title"/>
                                    </a>
                                </h3>

                                <div class="item-body">
                                    <xsl:if test="enclosure/@url or media:content/@url or media:thumbnail/@url">
                                        <img class="item-thumb" alt="Chronicle Cover">
                                            <xsl:attribute name="src">
                                                <xsl:value-of select="enclosure/@url | media:content/@url | media:thumbnail/@url"/>
                                            </xsl:attribute>
                                        </img>
                                    </xsl:if>
                                    <div class="item-desc">
                                        <xsl:value-of select="description"/>
                                    </div>
                                </div>

                                <div class="item-footer">
                                    <div class="item-author">
                                        <xsl:if test="dc:creator or author">
                                            <span>By <strong><xsl:value-of select="dc:creator | author"/></strong></span>
                                        </xsl:if>
                                    </div>
                                    <a>
                                        <xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
                                        <xsl:attribute name="class">read-more</xsl:attribute>
                                        <span>Read Chronicle &#8594;</span>
                                    </a>
                                </div>
                            </article>
                        </xsl:for-each>
                    </div>

                    <!-- Toast Notification -->
                    <div id="toastAlert" class="toast-alert">
                        &#10003; RSS Feed URL copied to clipboard!
                    </div>

                    <!-- Footer -->
                    <footer class="feed-footer">
                        <p>XIVIG Maison Gazette &#8212; Fine Horology &amp; Haute Couture Syndication.</p>
                        <p style="margin-top: 0.4rem;">
                            <a href="/">Home</a> &#183; 
                            <a href="/sitemap.xml">XML Sitemap</a> &#183; 
                            <a href="/sitemap">HTML Directory</a> &#183; 
                            <a href="/privacy-policy">Privacy Codex</a> &#183; 
                            <a href="/contact">Contact Salon</a>
                        </p>
                    </footer>
                </div>

                <script>
                    <![CDATA[
                    document.addEventListener('DOMContentLoaded', function() {
                        const copyBtn = document.getElementById('copyFeedBtn');
                        const toast = document.getElementById('toastAlert');

                        if (copyBtn) {
                            copyBtn.addEventListener('click', function() {
                                const feedUrl = window.location.href;
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(feedUrl).then(showToast);
                                } else {
                                    const ta = document.createElement('textarea');
                                    ta.value = feedUrl;
                                    document.body.appendChild(ta);
                                    ta.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(ta);
                                    showToast();
                                }
                            });
                        }

                        function showToast() {
                            if (!toast) return;
                            toast.classList.add('show');
                            setTimeout(() => {
                                toast.classList.remove('show');
                            }, 2600);
                        }
                    });
                    ]]>
                </script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
