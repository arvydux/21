import html
import sys

from data import CATEGORIES, LANGS, ML, NAV, PAGES, UI

OUT = sys.argv[1]
LANG_CODES = {"lt": "LT", "ru": "RU", "en": "EN", "pl": "PL"}
N18_LABEL = {"lt": "N18", "ru": "18+", "en": "18+", "pl": "18+"}

FONTS = ('<link rel="preconnect" href="https://fonts.googleapis.com">'
         '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
         '<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800'
         '&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">')


def e(text):
    return html.escape(text, quote=True)


def price(value, lang):
    if value == 0:
        return e(UI["included"][lang])
    return f'{value:.2f}'.replace('.', ',') + '&nbsp;€'


def name_html(it, lang):
    out = f'<span class="nm">{e(it["name"][lang])}</span>'
    if it["vol"]:
        out += f' <span class="vol">{it["vol"]}&nbsp;{ML[lang]}</span>'
    if it["n18"]:
        out += f' <span class="n18">{N18_LABEL[lang]}</span>'
    if it["desc"]:
        out += f'<span class="ds">{e(it["desc"][lang])}</span>'
    return out


def row(it, lang, two_prices):
    num = f'<span class="no">{it["n"]}</span>' if it["n"] is not None else '<span class="no no-empty"></span>'
    if two_prices and it["half"] is not None:
        prices = f'<span class="pr pr-half">{price(it["half"], lang)}</span><span class="pr">{price(it["price"], lang)}</span>'
    else:
        prices = f'<span class="pr">{price(it["price"], lang)}</span>'
    return f'<li class="it{" it-2" if two_prices and it["half"] is not None else ""}">{num}<span class="tx">{name_html(it, lang)}</span>{prices}</li>'


def group(g, lang, two_prices):
    head = ''
    if g.get("title"):
        note = f'<span class="gnote">{e(UI[g["note"]][lang])}</span>' if g.get("note") else ''
        head = f'<div class="gh"><h3>{e(g["title"][lang])}</h3>{note}</div>'
    if g.get("compact"):
        chips = ''.join(
            f'<li><span>{e(it["name"][lang])}</span><b>{price(it["price"], lang)}</b></li>' for it in g["items"]
        )
        return f'<div class="grp grp-compact">{head}<ul class="chips">{chips}</ul></div>'
    has_two = two_prices and any(it["half"] is not None for it in g["items"])
    legend = ''
    if has_two:
        legend = f'<div class="legend"><span>{e(UI["half"][lang])}</span><span>{e(UI["full"][lang])}</span></div>'
    rows = ''.join(row(it, lang, two_prices) for it in g["items"])
    return f'<div class="grp">{head}{legend}<ul class="items">{rows}</ul></div>'


def section(cat, lang, tag='section'):
    badge = f'<span class="n18 n18-lg">{N18_LABEL[lang]}</span>' if cat.get("n18") else ''
    groups = ''.join(group(g, lang, cat.get("two_prices", False)) for g in cat["groups"])
    return f'<{tag} class="sec" id="{lang}-{cat["id"]}"><h2>{e(cat["title"][lang])}{badge}</h2>{groups}</{tag}>'


def footer_notes(lang, page_no):
    notes = [UI["packaging"][lang]]
    if page_no == 3:
        notes.append(UI["n18"][lang])
    return ' · '.join(e(n) for n in notes)


# ───────────────────────── PRINT (A4) ─────────────────────────

PRINT_CSS = """
@page { size: A4; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
:root {
  --paper: #FBF7F0; --ink: #1D2A23; --muted: #6E756F; --line: #E4DCCD;
  --green: #14664A; --green-soft: #E3EFE8; --amber: #B5761B; --card: #F3ECDF;
}
body { font-family: 'Manrope', system-ui, sans-serif; color: var(--ink); background: #D9D4CA; }
.page {
  width: 210mm; height: 297mm; margin: 0 auto 10mm; padding: 12mm 13mm 10mm;
  background: var(--paper); position: relative; overflow: hidden;
  display: flex; flex-direction: column; break-after: page;
}
@media print { body { background: none; } .page { margin: 0; } }
.page::before {
  content: ""; position: absolute; inset: 0 0 auto 0; height: 3mm; background: var(--green);
}
.top { display: flex; justify-content: space-between; align-items: center; }
.brand { font-weight: 800; font-size: 8.5pt; letter-spacing: .28em; text-transform: uppercase; color: var(--green); }
.lang { font-size: 8pt; font-weight: 700; color: var(--muted); letter-spacing: .08em; }
.lang b { display: inline-block; background: var(--green); color: #fff; border-radius: 99px; padding: .5mm 2.4mm; margin-right: 1.5mm; letter-spacing: .1em; }
.title { margin: 3.5mm 0 4mm; display: flex; align-items: baseline; gap: 4mm; }
.title h1 { font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 25pt; line-height: 1.05; }
.title .rule { flex: 1; height: .35mm; background: var(--line); align-self: center; }
.title .pg { font-size: 8pt; font-weight: 700; color: var(--muted); }
.cols { flex: 1; column-count: 2; column-gap: 9mm; column-fill: balance; }
.sec { margin-bottom: 5mm; }
.sec h2 {
  font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 18pt; color: var(--green);
  display: flex; align-items: center; gap: 2.5mm; padding-bottom: 1.2mm; margin-bottom: 1.6mm;
  border-bottom: .5mm solid var(--green); break-after: avoid;
}
.grp { break-inside: avoid; margin-bottom: 2.6mm; }
.gh { display: flex; align-items: baseline; justify-content: space-between; gap: 2mm; margin: 1.6mm 0 .6mm; }
.gh h3 { font-size: 7.6pt; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--amber); }
.gnote { font-size: 7pt; color: var(--muted); font-style: italic; }
.items { list-style: none; }
.it { display: grid; grid-template-columns: 5.6mm 1fr auto; column-gap: 2mm; align-items: baseline; padding: 1.3mm 0; border-bottom: .2mm dotted var(--line); }
.it:last-child { border-bottom: 0; }
.it-2 { grid-template-columns: 5.6mm 1fr 15mm 15mm; }
.no { width: 5mm; height: 5mm; border-radius: 50%; background: var(--green-soft); color: var(--green); font-size: 6.8pt; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; transform: translateY(.4mm); }
.no-empty { background: none; }
.tx { font-size: 10.2pt; line-height: 1.25; }
.nm { font-weight: 650; }
.ds { display: block; font-size: 8pt; color: var(--muted); margin-top: .3mm; }
.vol { font-size: 7.4pt; font-weight: 700; color: var(--muted); white-space: nowrap; }
.n18 { font-size: 6.4pt; font-weight: 800; color: #fff; background: #B3412F; border-radius: 99px; padding: .2mm 1.4mm; vertical-align: .3mm; white-space: nowrap; letter-spacing: .04em; }
.n18-lg { font-family: 'Manrope', sans-serif; font-size: 7pt; vertical-align: middle; }
.pr { font-size: 10.2pt; font-weight: 800; white-space: nowrap; text-align: right; font-variant-numeric: tabular-nums; }
.pr-half { color: var(--muted); font-weight: 700; }
.legend { display: grid; grid-template-columns: 1fr 15mm 15mm; column-gap: 2mm; font-size: 6.6pt; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); text-align: right; margin-bottom: .2mm; }
.legend span:first-child { grid-column: 2; }
.grp-compact { background: var(--card); border-radius: 2.5mm; padding: 1.8mm 2.6mm 2mm; }
.grp-compact .gh { margin-top: 0; }
.chips { list-style: none; display: grid; grid-template-columns: 1fr 1fr; column-gap: 4mm; row-gap: .6mm; }
.chips li { display: flex; justify-content: space-between; gap: 1.5mm; font-size: 8.8pt; }
.chips b { font-weight: 800; white-space: nowrap; font-variant-numeric: tabular-nums; }
.foot { display: flex; justify-content: space-between; align-items: center; gap: 4mm; border-top: .3mm solid var(--line); padding-top: 2.4mm; font-size: 7.4pt; color: var(--muted); }
.foot .enjoy { font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-weight: 500; font-size: 11pt; color: var(--green); white-space: nowrap; }
"""


def print_page(lang, page_no):
    cats = ''.join(section(c, lang) for c in CATEGORIES if c["page"] == page_no)
    return f'''<div class="page" lang="{lang}">
  <div class="top"><span class="brand">{e(UI["brand"][lang])}</span><span class="lang"><b>{LANG_CODES[lang]}</b>{e(UI["lang_name"][lang])}</span></div>
  <div class="title"><h1>{e(PAGES[page_no][lang])}</h1><span class="rule"></span><span class="pg">{page_no}&nbsp;/&nbsp;3</span></div>
  <div class="cols">{cats}</div>
  <div class="foot"><span>{footer_notes(lang, page_no)}</span><span class="enjoy">{e(UI["enjoy"][lang])}</span></div>
</div>'''


def build_print():
    pages = '\n'.join(print_page(lang, p) for lang in LANGS for p in (1, 2, 3))
    return f'''<!doctype html>
<html lang="lt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Meniu 7 — Prusakovų užkandinė (spausdinimui)</title>
{FONTS}
<style>{PRINT_CSS}</style>
</head>
<body>
{pages}
</body>
</html>
'''


# ───────────────────────── MOBILE ─────────────────────────

MOBILE_CSS = """
* { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --paper: #FBF7F0; --ink: #1D2A23; --muted: #6E756F; --line: #E8E0D2;
  --green: #14664A; --green-dark: #0E4A36; --green-soft: #E3EFE8; --amber: #B5761B; --card: #FFFFFF; --chip: #F3ECDF;
}
html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body { font-family: 'Manrope', system-ui, sans-serif; color: var(--ink); background: var(--paper); font-size: 16px; }
.hdr { position: sticky; top: 0; z-index: 10; background: var(--green); color: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.12); }
.hdr-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px 10px; max-width: 760px; margin: 0 auto; }
.brand { font-weight: 800; font-size: 13px; letter-spacing: .2em; text-transform: uppercase; }
.brand small { display: block; font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-weight: 500; letter-spacing: 0; text-transform: none; font-size: 15px; opacity: .85; margin-top: 2px; }
.langs { display: flex; gap: 4px; background: rgba(255,255,255,.12); border-radius: 99px; padding: 3px; }
.langs button { border: 0; background: none; color: #fff; font: 700 13px 'Manrope', sans-serif; padding: 7px 10px; border-radius: 99px; cursor: pointer; min-width: 38px; }
.langs button.on { background: #fff; color: var(--green-dark); }
.nav { display: flex; gap: 8px; overflow-x: auto; padding: 8px 16px 12px; scrollbar-width: none; max-width: 760px; margin: 0 auto; }
.nav::-webkit-scrollbar { display: none; }
.nav a { flex: none; color: #fff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 8px 14px; border-radius: 99px; background: rgba(255,255,255,.14); white-space: nowrap; }
.nav a.on { background: #fff; color: var(--green-dark); }
main { max-width: 760px; margin: 0 auto; padding: 8px 16px 32px; }
.panel { display: none; }
.panel.on { display: block; }
.sec { scroll-margin-top: 150px; margin-top: 24px; }
.sec h2 { font-family: 'Playfair Display', Georgia, serif; font-size: 26px; font-weight: 700; color: var(--green); display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.grp { background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 6px 14px; margin-bottom: 12px; }
.gh { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; padding: 10px 0 4px; }
.gh h3 { font-size: 12px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--amber); }
.gnote { font-size: 12px; color: var(--muted); font-style: italic; text-align: right; }
.items { list-style: none; }
.it { display: grid; grid-template-columns: 26px 1fr auto; column-gap: 10px; align-items: baseline; padding: 11px 0; border-bottom: 1px solid var(--line); }
.it:last-child { border-bottom: 0; }
.it-2 { grid-template-columns: 26px 1fr auto auto; }
.no { width: 24px; height: 24px; border-radius: 50%; background: var(--green-soft); color: var(--green); font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; transform: translateY(5px); }
.no-empty { background: none; }
.tx { line-height: 1.35; }
.nm { font-weight: 650; }
.ds { display: block; font-size: 13.5px; color: var(--muted); margin-top: 2px; }
.vol { font-size: 13px; font-weight: 700; color: var(--muted); white-space: nowrap; }
.n18 { font-size: 11px; font-weight: 800; color: #fff; background: #B3412F; border-radius: 99px; padding: 1px 7px; vertical-align: 2px; white-space: nowrap; }
.sec h2 .n18 { font-family: 'Manrope', sans-serif; font-size: 12px; }
.pr { font-weight: 800; white-space: nowrap; text-align: right; font-variant-numeric: tabular-nums; }
.pr-half { color: var(--muted); font-weight: 700; min-width: 64px; }
.it-2 .pr:last-child { min-width: 64px; }
.legend { display: flex; justify-content: flex-end; gap: 10px; font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); padding-top: 6px; }
.legend span { min-width: 64px; text-align: right; }
.grp-compact { background: var(--chip); border-color: transparent; }
.chips { list-style: none; display: grid; grid-template-columns: 1fr 1fr; column-gap: 16px; row-gap: 6px; padding: 4px 0 12px; }
.chips li { display: flex; justify-content: space-between; gap: 8px; font-size: 14.5px; }
.chips b { white-space: nowrap; font-variant-numeric: tabular-nums; }
@media (max-width: 480px) { .chips { grid-template-columns: 1fr; } }
.foot { text-align: center; color: var(--muted); font-size: 13.5px; line-height: 1.6; margin-top: 28px; }
.foot .enjoy { display: block; font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-size: 24px; color: var(--green); margin-bottom: 6px; }
.foot a { display: inline-block; margin-top: 14px; color: var(--green); font-weight: 800; text-decoration: none; border: 2px solid var(--green); border-radius: 99px; padding: 8px 18px; }
"""

MOBILE_JS = """
(function () {
  var langs = ['lt', 'ru', 'en', 'pl'];
  function stored() { try { return localStorage.getItem('menu-lang'); } catch (e) { return null; } }
  function remember(l) { try { localStorage.setItem('menu-lang', l); } catch (e) {} }
  function pick() {
    var q = new URLSearchParams(location.search).get('lang');
    if (langs.indexOf(q) > -1) return q;
    var s = stored(); if (langs.indexOf(s) > -1) return s;
    var b = (navigator.language || 'lt').slice(0, 2);
    return langs.indexOf(b) > -1 ? b : 'lt';
  }
  function setLang(l) {
    document.documentElement.lang = l;
    langs.forEach(function (x) {
      document.getElementById('panel-' + x).classList.toggle('on', x === l);
      document.getElementById('nav-' + x).style.display = x === l ? 'flex' : 'none';
    });
    document.querySelectorAll('.langs button').forEach(function (b) { b.classList.toggle('on', b.dataset.lang === l); });
    remember(l);
    spy();
  }
  document.querySelectorAll('.langs button').forEach(function (b) {
    b.addEventListener('click', function () { setLang(b.dataset.lang); });
  });
  function spy() {
    var l = document.documentElement.lang, nav = document.getElementById('nav-' + l);
    var secs = document.querySelectorAll('#panel-' + l + ' .sec'), current = secs[0];
    secs.forEach(function (s) { if (s.getBoundingClientRect().top < 170) current = s; });
    nav.querySelectorAll('a').forEach(function (a) {
      var on = current && a.getAttribute('href') === '#' + current.id;
      a.classList.toggle('on', on);
      if (on) { var r = a.getBoundingClientRect(); if (r.left < 0 || r.right > innerWidth) a.scrollIntoView({ inline: 'center', block: 'nearest' }); }
    });
  }
  addEventListener('scroll', spy, { passive: true });
  setLang(pick());
})();
"""


def build_mobile():
    lang_buttons = ''.join(f'<button type="button" data-lang="{l}">{LANG_CODES[l]}</button>' for l in LANGS)
    navs = ''.join(
        f'<nav class="nav" id="nav-{l}">' + ''.join(
            f'<a href="#{l}-{c["id"]}">{e(NAV[c["id"]][l])}</a>' for c in CATEGORIES
        ) + '</nav>'
        for l in LANGS
    )
    panels = ''
    for l in LANGS:
        secs = ''.join(section(c, l) for c in CATEGORIES)
        panels += f'''<div class="panel" id="panel-{l}" lang="{l}">{secs}
<div class="foot"><span class="enjoy">{e(UI["enjoy"][l])}</span>{e(UI["packaging"][l])}<br>{e(UI["n18"][l])}<br><a href="menu-7.pdf">{e(UI["pdf"][l])} ↓</a></div></div>'''
    return f'''<!doctype html>
<html lang="lt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#14664A">
<title>Meniu — Prusakovų užkandinė</title>
{FONTS}
<style>{MOBILE_CSS}</style>
</head>
<body>
<header class="hdr">
  <div class="hdr-top"><div class="brand">Prusakovų užkandinė<small>Meniu · Menu</small></div><div class="langs">{lang_buttons}</div></div>
  {navs}
</header>
<main>{panels}</main>
<script>{MOBILE_JS}</script>
</body>
</html>
'''


open(f'{OUT}/menu-7-print.html', 'w').write(build_print())
open(f'{OUT}/menu-7-mobile.html', 'w').write(build_mobile())
print('written')
