# Self-hosted fonts

This theme self-hosts the webfonts used by the 360 Settings **Body Font** / **Heading Font** selectors.

## Included families

- Anton
- Arvo
- Bodoni Moda
- Cabin
- Chivo
- Inter
- Marcellus
- Playfair Display
- Roboto

## How files are generated

Font files (`.woff2`) and the stylesheet `fonts.css` are generated from Google Fonts CSS2 endpoints using:

- `tools/selfhost-fonts.py`

The generated stylesheet declares `@font-face` rules and references local font files under:

- `assets/fonts/<slug>/*.woff2`

## Licenses

License texts for each included family are stored in:

- `assets/fonts/licenses/`

If you add/remove families, re-run the generator and ensure the corresponding license files are updated.
