"""Petite bibliothèque SVG commune + rendu PNG via Chrome headless."""
import html, os, subprocess

CHROME = "C:/Users/User/.cache/puppeteer/chrome-headless-shell/win64-148.0.7778.97/chrome-headless-shell-win64/chrome-headless-shell.exe"
FONT = "Segoe UI, Arial, Helvetica, sans-serif"
CW = 6.7  # largeur moyenne d'un caractère en 13px


def esc(t):
    return html.escape(str(t))


def wrap(text, maxw, cw=CW):
    out = []
    for para in str(text).split("\n"):
        cur = ""
        for w in para.split(" "):
            t = (cur + " " + w).strip()
            if len(t) * cw <= maxw or not cur:
                cur = t
            else:
                out.append(cur)
                cur = w
        out.append(cur)
    return out


class Svg:
    def __init__(self, w, h, bg="white"):
        self.w, self.h = w, h
        self.o = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{w}" height="{h}" viewBox="0 0 {w} {h}" '
                  f'font-family="{FONT}" font-size="13">',
                  '<defs>'
                  '<marker id="ar" viewBox="0 0 10 10" refX="9.5" refY="5" markerWidth="9" markerHeight="9" orient="auto-start-reverse"><path d="M0,0 L10,5 L0,10 z" fill="#333"/></marker>'
                  '<marker id="open" viewBox="0 0 12 12" refX="11" refY="6" markerWidth="11" markerHeight="11" orient="auto-start-reverse"><path d="M0,0 L11,6 L0,12" fill="none" stroke="#333" stroke-width="1.4"/></marker>'
                  '<marker id="tri" viewBox="0 0 14 14" refX="13" refY="7" markerWidth="14" markerHeight="14" orient="auto-start-reverse"><path d="M0,0 L13,7 L0,14 z" fill="white" stroke="#333" stroke-width="1.3"/></marker>'
                  '</defs>',
                  f'<rect width="100%" height="100%" fill="{bg}"/>']

    def add(self, s):
        self.o.append(s)

    def rect(self, x, y, w, h, fill="white", stroke="#333", sw=1.2, rx=0, dash=None, extra=""):
        d = f' stroke-dasharray="{dash}"' if dash else ""
        self.add(f'<rect x="{x:.1f}" y="{y:.1f}" width="{w:.1f}" height="{h:.1f}" rx="{rx}" fill="{fill}" '
                 f'stroke="{stroke}" stroke-width="{sw}"{d} {extra}/>')

    def line(self, x1, y1, x2, y2, stroke="#333", sw=1.2, dash=None, marker=None):
        d = f' stroke-dasharray="{dash}"' if dash else ""
        m = f' marker-end="url(#{marker})"' if marker else ""
        self.add(f'<line x1="{x1:.1f}" y1="{y1:.1f}" x2="{x2:.1f}" y2="{y2:.1f}" stroke="{stroke}" stroke-width="{sw}"{d}{m}/>')

    def path(self, d, stroke="#333", sw=1.2, fill="none", dash=None, marker=None):
        ds = f' stroke-dasharray="{dash}"' if dash else ""
        m = f' marker-end="url(#{marker})"' if marker else ""
        self.add(f'<path d="{d}" fill="{fill}" stroke="{stroke}" stroke-width="{sw}"{ds}{m}/>')

    def text(self, x, y, t, size=13, anchor="start", weight="normal", fill="#222", italic=False, halo=False):
        st = ' font-style="italic"' if italic else ""
        hl = ' stroke="white" stroke-width="4" paint-order="stroke" stroke-linejoin="round"' if halo else ""
        self.add(f'<text x="{x:.1f}" y="{y:.1f}" font-size="{size}" text-anchor="{anchor}" font-weight="{weight}" '
                 f'fill="{fill}"{st}{hl}>{esc(t)}</text>')

    def mtext(self, cx, cy, lines, size=13, lh=None, anchor="middle", weight="normal", fill="#222", italic=False):
        lh = lh or size * 1.25
        y0 = cy - (len(lines) - 1) * lh / 2 + size * 0.35
        for i, l in enumerate(lines):
            self.text(cx, y0 + i * lh, l, size, anchor, weight, fill, italic)

    def circle(self, cx, cy, r, fill="white", stroke="#333", sw=1.2):
        self.add(f'<circle cx="{cx:.1f}" cy="{cy:.1f}" r="{r}" fill="{fill}" stroke="{stroke}" stroke-width="{sw}"/>')

    def ellipse(self, cx, cy, rx, ry, fill="white", stroke="#333", sw=1.2):
        self.add(f'<ellipse cx="{cx:.1f}" cy="{cy:.1f}" rx="{rx:.1f}" ry="{ry:.1f}" fill="{fill}" stroke="{stroke}" stroke-width="{sw}"/>')

    def svg(self):
        return "\n".join(self.o + ["</svg>"])


def render(svg_obj, out_png, scale=2):
    """Écrit le SVG puis le rend en PNG (chrome headless)."""
    svg = svg_obj.svg()
    base = os.path.splitext(out_png)[0]
    open(base + ".svg", "w", encoding="utf-8").write(svg)
    page = base + ".tmp.html"
    open(page, "w", encoding="utf-8").write(f'<html><body style="margin:0;background:white">{svg}</body></html>')
    subprocess.run([CHROME, "--headless", "--no-sandbox", "--hide-scrollbars",
                    f"--force-device-scale-factor={scale}", f"--window-size={int(svg_obj.w)},{int(svg_obj.h)}",
                    f"--screenshot={out_png}", "file:///" + os.path.abspath(page).replace("\\", "/")],
                   check=True, capture_output=True)
    os.remove(page)
