@page { margin: 18mm; }
html, body { margin: 20px; padding: 20px; }
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    color: {{ $text }};
    line-height: 1.45;
    box-sizing: border-box;
}
body.cv-pdf-classic {
    font-family: DejaVu Serif, serif;
}

/* —— Sidebar: no full-page table (Dompdf blank pages). Float + margin main. —— */
body.cv-pdf-sidebar .cv-sheet {
    width: 100%;
}
body.cv-pdf-sidebar .cv-sidebar-layout {
    width: 100%;
}
body.cv-pdf-sidebar .cv-sidebar-panel {
    float: left;
    width: 36%;
    padding: 5mm 3.5mm 6mm 3.5mm;
    color: #ffffff;
    background: {{ $primary }};
    word-wrap: break-word;
    overflow-wrap: break-word;
}
body.cv-pdf-sidebar .cv-main-panel {
    margin-left: 36%;
    padding: 5mm 4mm 6mm 5mm;
    background: #ffffff;
    color: {{ $text }};
    word-wrap: break-word;
    overflow-wrap: break-word;
}
body.cv-pdf-sidebar .cv-sidebar-panel p,
body.cv-pdf-sidebar .cv-sidebar-panel li,
body.cv-pdf-sidebar .cv-sidebar-panel .contact-line {
    font-size: 10px;
    line-height: 1.4;
}
body.cv-pdf-sidebar .cv-sidebar-panel .contact-value {
    word-break: break-all;
}
body.cv-pdf-sidebar .rich-content,
body.cv-pdf-sidebar .cv-sidebar-rich {
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}
body.cv-pdf-sidebar .rich-content img {
    max-width: 100%;
    height: auto;
}
body.cv-pdf-sidebar .project-head .project-title,
body.cv-pdf-sidebar .project-head h3.project-title {
    display: block;
    width: auto;
    font-size: 13px;
}
body.cv-pdf-sidebar .project-head .project-period {
    display: block;
    width: auto;
    text-align: left;
    margin-top: 1mm;
}
body.cv-pdf-sidebar .cv-sidebar-panel h2,
body.cv-pdf-sidebar .cv-sidebar-heading {
    margin: 4mm 0 2.5mm;
    padding-bottom: 1mm;
    border-bottom: 1px solid rgba(255, 255, 255, 0.28);
    border-left: none;
    font-size: 9px;
    font-weight: bold;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.95);
}
body.cv-pdf-sidebar .cv-sidebar-panel h2:first-of-type {
    margin-top: 0;
}
body.cv-pdf-sidebar .cv-sidebar-panel p,
body.cv-pdf-sidebar .cv-sidebar-panel li,
body.cv-pdf-sidebar .cv-sidebar-panel .contact-value,
body.cv-pdf-sidebar .cv-sidebar-panel strong {
    color: rgba(255, 255, 255, 0.88);
}
body.cv-pdf-sidebar .cv-sidebar-panel .edu-degree,
body.cv-pdf-sidebar .cv-sidebar-panel .edu-degree strong {
    color: #ffffff;
    font-size: 12px;
}
body.cv-pdf-sidebar .cv-sidebar-panel .muted {
    color: rgba(255, 255, 255, 0.75);
}
body.cv-pdf-sidebar .cv-sidebar-panel .edu-period {
    color: rgba(255, 255, 255, 0.6);
    font-size: 10px;
}
body.cv-pdf-sidebar .cv-sidebar-panel ul {
    list-style-type: disc;
    padding-left: 1.1rem;
}
body.cv-pdf-sidebar .cv-main-panel h2,
body.cv-pdf-sidebar .cv-main-heading {
    margin: 0 0 3mm;
    padding-bottom: 1mm;
    border-bottom: 2px solid {{ $primary }};
    border-left: none;
    font-size: 9px;
    font-weight: bold;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: {{ $primary }};
}
body.cv-pdf-sidebar .cv-main-panel h3,
body.cv-pdf-sidebar .cv-main-panel .project-title {
    color: {{ $text }};
    background: transparent;
}
body.cv-pdf-sidebar .cv-main-panel .project-period {
    color: {{ $muted }};
}
body.cv-pdf-sidebar .cv-main-panel ul {
    list-style-type: disc;
    padding-left: 1.35rem;
}
body.cv-pdf-sidebar .cv-main-panel li {
    display: list-item;
    color: {{ $muted }};
}

.cv-sidebar-profile { text-align: center; }
body.cv-pdf-sidebar .cv-sidebar-profile h1 {
    margin: 2mm 0 1mm;
    font-size: 15px;
    font-weight: bold;
    color: #ffffff;
    word-wrap: break-word;
}
body.cv-pdf-sidebar .cv-sidebar-profile .sidebar-headline {
    margin: 0;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.85);
}
body.cv-pdf-sidebar .cv-sidebar-profile img {
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.35);
}
body.cv-pdf-sidebar .cv-sidebar-divider {
    height: 1px;
    margin: 4mm 0;
    background: rgba(255, 255, 255, 0.22);
}
body.cv-pdf-sidebar .cv-sidebar-block {
    margin-bottom: 5mm;
}
body.cv-pdf-sidebar .cv-sidebar-rich ul {
    list-style: disc;
    padding-left: 1.1rem;
}

.rich-content { line-height: 1.65; }
.rich-content > :first-child { margin-top: 0; }
.rich-content > :last-child { margin-bottom: 0; }
.rich-content p, .rich-content ul, .rich-content ol { margin: 0.5em 0; }
.rich-content ul { list-style: disc; padding-left: 1.25rem; }

.muted { color: {{ $muted }}; }

.cv-main-block { margin-bottom: 6mm; }
.cv-main-block:last-child { margin-bottom: 0; }
.cv-experience-item { margin-bottom: 5mm; }
.cv-experience-item + .cv-experience-item {
    padding-top: 4mm;
    border-top: 1px solid #e4e4e7;
}
.cv-main-panel h3,
.cv-main-panel .project-title {
    margin: 0;
    font-size: 14px;
    font-weight: bold;
    color: {{ $text }};
    background: transparent;
}
.cv-main-panel .project-head h3.project-title {
    display: inline-block;
    width: 68%;
    vertical-align: top;
}
.cv-main-panel .project-period {
    font-size: 10px;
    color: {{ $muted }};
}
.cv-main-panel ul { list-style: disc; padding-left: 1.35rem; }
.cv-main-panel li { display: list-item; color: {{ $muted }}; font-size: 11px; }

/* —— Modern —— */
.cv-modern-accent {
    height: 5px;
    margin: 0 0 4mm;
    background: {{ $primary }};
}
.cv-modern-header {
    padding-bottom: 3mm;
    margin-bottom: 2mm;
    border-bottom: 1px solid #e4e4e7;
}
.cv-modern-header h1 {
    font-size: 22px;
    font-weight: bold;
    margin: 0;
    color: {{ $text }};
}
.cv-modern-headline {
    margin: 1mm 0 0;
    font-size: 14px;
    font-weight: 600;
    color: {{ $primary }};
}
.cv-modern-intro { margin-bottom: 3mm; }
.cv-modern-avatar { float: left; margin-right: 4mm; }
.cv-modern-avatar img { border-radius: 6px; }
.modern-contact .contact-line {
    display: inline-block;
    width: 32%;
    vertical-align: top;
    margin-bottom: 2px;
    padding-right: 4px;
    box-sizing: border-box;
}
body.cv-pdf-modern h2,
.cv-modern-section-title {
    margin: 5mm 0 2.5mm;
    padding-left: 2.5mm;
    border-left: 3px solid {{ $primary }};
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: {{ $primary }};
    border-bottom: none;
}
.cv-modern-timeline {
    border-left: 2px solid {{ $primary }};
    margin-left: 1px;
    padding-left: 4mm;
}
.cv-modern-timeline-item {
    margin-bottom: 4mm;
    padding-bottom: 3mm;
    border-bottom: 1px solid #e4e4e7;
}
.cv-modern-timeline-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

/* —— Classic —— */
.cv-classic-header { text-align: center; margin-bottom: 2mm; }
.cv-classic-rule {
    border: none;
    border-top: 1px solid #a1a1aa;
    margin: 3mm 0;
}
.cv-classic-rule-strong { border-top: 2px solid #27272a; }
.cv-classic-name {
    text-align: center;
    font-size: 20px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin: 2mm 0;
    color: {{ $text }};
}
.cv-classic-headline {
    text-align: center;
    font-style: italic;
    font-size: 12px;
    color: {{ $muted }};
    margin: 0 0 2mm;
}
.classic-contact-list { text-align: center; }
.classic-contact-list .contact-line {
    display: inline-block;
    margin: 0 3mm 2px 0;
    white-space: nowrap;
}
body.cv-pdf-classic h2,
.cv-classic-section-title {
    margin: 4mm 0 2mm;
    padding-bottom: 1mm;
    border-bottom: 1px solid #71717a;
    font-size: 9px;
    font-weight: bold;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    text-align: center;
    color: #27272a;
    border-left: none;
}
.cv-classic-entry { margin-bottom: 3mm; }
.cv-classic-entry-divider {
    margin-top: 3mm;
    padding-top: 3mm;
    border-top: 1px dotted #d4d4d8;
}
.cv-classic-entry-row { margin-bottom: 1mm; }
.cv-classic-date { float: right; color: #71717a; font-size: 11px; }
.cv-classic-subline { font-size: 11px; color: {{ $muted }}; margin: 1mm 0; }
.cv-classic-list { list-style: disc; padding-left: 1.1rem; font-size: 11px; color: {{ $muted }}; }
.cv-classic-skill-line { font-size: 11px; margin: 1.5mm 0; color: #3f3f46; }

/* —— Shared projects / contact —— */
.contact-line { margin: 0 0 2px; line-height: 1.35; }
.contact-icon { vertical-align: middle; margin-right: 4px; }
.contact-value { vertical-align: middle; color: {{ $muted }}; font-size: 10px; }
.project-head { margin: 0 0 2mm; width: 100%; }
.project-head .project-title {
    display: inline-block;
    width: 68%;
    vertical-align: top;
    font-weight: bold;
    font-size: 13px;
    color: {{ $text }};
}
.project-head .project-period {
    display: inline-block;
    width: 30%;
    text-align: right;
    vertical-align: top;
    font-size: 10px;
    color: {{ $muted }};
}
.project-subtitle { font-size: 10px; margin: 1mm 0; color: {{ $muted }}; }
.tech-tags { margin-top: 2mm; line-height: 1.6; }
.tech-tag {
    display: inline-block;
    border: 1px solid #d4d4d8;
    border-radius: 3px;
    padding: 0 4px;
    margin: 0 3px 3px 0;
    font-size: 8px;
    font-weight: 600;
    text-transform: uppercase;
    color: {{ $primary }};
}
