#!/usr/bin/env bash
set -euo pipefail

MAX_WIDTH=${MAX_WIDTH:-400}
WEBP_QUALITY=${WEBP_QUALITY:-82}
AVIF_QUALITY=${AVIF_QUALITY:-60}

usage() {
  cat <<'EOF'
Usage:
  tools/resize-clinic-logos.sh [--max-width 400] [--dry-run] [dir ...]

Defaults:
  --max-width: 400
  dirs: assets/clinic-images images/clinic-images

Notes:
  - Only downscales when image width > max-width (never upscales).
  - Uses macOS `sips`.
  - For .webp, uses `dwebp` + `cwebp` (from libwebp).
  - For .avif, uses `avifdec` + `avifenc` (from libavif).
  - Control WebP output quality with WEBP_QUALITY env var (default: 82).
  - Control AVIF output quality with AVIF_QUALITY env var (default: 60).
EOF
}

DRY_RUN=0
DIRS=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help)
      usage
      exit 0
      ;;
    --max-width)
      MAX_WIDTH="$2"
      shift 2
      ;;
    --dry-run)
      DRY_RUN=1
      shift
      ;;
    *)
      DIRS+=("$1")
      shift
      ;;
  esac
done

if ! command -v sips >/dev/null 2>&1; then
  echo "Error: 'sips' not found (macOS required)." >&2
  exit 1
fi

if [[ ${#DIRS[@]} -eq 0 ]]; then
  DIRS=("assets/clinic-images" "images/clinic-images")
fi

get_dim() {
  local file="$1"
  local w h
  w=$(sips -g pixelWidth "$file" 2>/dev/null | awk '/pixelWidth/ {print $2}')
  h=$(sips -g pixelHeight "$file" 2>/dev/null | awk '/pixelHeight/ {print $2}')
  if [[ -z "${w:-}" || -z "${h:-}" ]]; then
    return 1
  fi
  printf '%s %s' "$w" "$h"
}

get_size() {
  # macOS stat
  stat -f%z "$1" 2>/dev/null || echo "0"
}

resize_webp() {
  local file="$1"
  local tmpdir decoded resized out
  local new_dims new_w

  if ! command -v dwebp >/dev/null 2>&1 || ! command -v cwebp >/dev/null 2>&1; then
    echo "Skip (.webp requires dwebp+cwebp): $file" >&2
    return 2
  fi

  tmpdir=$(mktemp -d)
  decoded="$tmpdir/decoded.png"
  resized="$tmpdir/resized.png"
  out="$tmpdir/out.webp"

  if ! dwebp "$file" -o "$decoded" >/dev/null 2>&1; then
    rm -rf "$tmpdir"
    return 1
  fi

  if ! sips --resampleWidth "$MAX_WIDTH" "$decoded" --out "$resized" >/dev/null; then
    rm -rf "$tmpdir"
    return 1
  fi

  # -m 6 is higher effort/better compression; keep alpha quality high for logos.
  if ! cwebp -q "$WEBP_QUALITY" -m 6 -alpha_q 100 "$resized" -o "$out" >/dev/null 2>&1; then
    rm -rf "$tmpdir"
    return 1
  fi

  if ! new_dims=$(get_dim "$out"); then
    rm -rf "$tmpdir"
    return 1
  fi

  new_w=${new_dims%% *}
  if [[ "$new_w" -gt "$MAX_WIDTH" ]]; then
    rm -rf "$tmpdir"
    return 1
  fi

  mv "$out" "$file"
  rm -rf "$tmpdir"
  return 0
}

resize_avif() {
  local file="$1"
  local tmpdir decoded resized out
  local new_dims new_w

  if ! command -v avifdec >/dev/null 2>&1 || ! command -v avifenc >/dev/null 2>&1; then
    echo "Skip (.avif requires avifdec+avifenc): $file" >&2
    return 2
  fi

  tmpdir=$(mktemp -d)
  decoded="$tmpdir/decoded.png"
  resized="$tmpdir/resized.png"
  out="$tmpdir/out.avif"

  if ! avifdec "$file" "$decoded" >/dev/null 2>&1; then
    # Some assets may be incorrectly named (e.g., JPEG data with .avif extension).
    # sips can often still read them by content and convert to PNG.
    if ! sips -s format png "$file" --out "$decoded" >/dev/null 2>&1; then
      rm -rf "$tmpdir"
      return 1
    fi
  fi

  if ! sips --resampleWidth "$MAX_WIDTH" "$decoded" --out "$resized" >/dev/null; then
    rm -rf "$tmpdir"
    return 1
  fi

  if ! avifenc -q "$AVIF_QUALITY" --qalpha 100 "$resized" -o "$out" >/dev/null 2>&1; then
    rm -rf "$tmpdir"
    return 1
  fi

  if ! new_dims=$(get_dim "$out"); then
    rm -rf "$tmpdir"
    return 1
  fi

  new_w=${new_dims%% *}
  if [[ "$new_w" -gt "$MAX_WIDTH" ]]; then
    rm -rf "$tmpdir"
    return 1
  fi

  mv "$out" "$file"
  rm -rf "$tmpdir"
  return 0
}

changed=0
skipped=0
missing=0

for dir in "${DIRS[@]}"; do
  if [[ ! -d "$dir" ]]; then
    echo "Missing dir: $dir"
    missing=$((missing + 1))
    continue
  fi

  while IFS= read -r -d '' file; do
    if ! dims=$(get_dim "$file"); then
      echo "Skip (unreadable): $file"
      skipped=$((skipped + 1))
      continue
    fi

    old_w=${dims%% *}
    old_h=${dims##* }

    if [[ "$old_w" -le "$MAX_WIDTH" ]]; then
      continue
    fi

    old_size=$(get_size "$file")

    if [[ "$DRY_RUN" -eq 1 ]]; then
      echo "Would resize: ${old_w}x${old_h} (${old_size} bytes)  $file"
      changed=$((changed + 1))
      continue
    fi

    if [[ "$file" == *.webp || "$file" == *.WEBP ]]; then
      rc=0
      resize_webp "$file" || rc=$?
      if [[ "$rc" -eq 2 ]]; then
        skipped=$((skipped + 1))
        continue
      fi
      if [[ "$rc" -ne 0 ]]; then
        echo "Failed resize (.webp): $file" >&2
        skipped=$((skipped + 1))
        continue
      fi

      if ! new_dims=$(get_dim "$file"); then
        echo "Failed read resized dims: $file" >&2
        skipped=$((skipped + 1))
        continue
      fi

      new_w=${new_dims%% *}
      new_h=${new_dims##* }
      new_size=$(get_size "$file")
      if [[ "$new_size" -le 0 || "$new_w" -gt "$MAX_WIDTH" || "$new_w" -ge "$old_w" ]]; then
        echo "Bad resize output: $file" >&2
        skipped=$((skipped + 1))
        continue
      fi

      echo "Resized: ${old_w}x${old_h} -> ${new_w}x${new_h} | ${old_size} -> ${new_size} bytes  $file"
      changed=$((changed + 1))
      continue
    fi

    if [[ "$file" == *.avif || "$file" == *.AVIF ]]; then
      rc=0
      resize_avif "$file" || rc=$?
      if [[ "$rc" -eq 2 ]]; then
        skipped=$((skipped + 1))
        continue
      fi
      if [[ "$rc" -ne 0 ]]; then
        echo "Failed resize (.avif): $file" >&2
        skipped=$((skipped + 1))
        continue
      fi

      if ! new_dims=$(get_dim "$file"); then
        echo "Failed read resized dims: $file" >&2
        skipped=$((skipped + 1))
        continue
      fi

      new_w=${new_dims%% *}
      new_h=${new_dims##* }
      new_size=$(get_size "$file")
      if [[ "$new_size" -le 0 || "$new_w" -gt "$MAX_WIDTH" || "$new_w" -ge "$old_w" ]]; then
        echo "Bad resize output: $file" >&2
        skipped=$((skipped + 1))
        continue
      fi

      echo "Resized: ${old_w}x${old_h} -> ${new_w}x${new_h} | ${old_size} -> ${new_size} bytes  $file"
      changed=$((changed + 1))
      continue
    fi

    tmpdir=$(mktemp -d)
    out="$tmpdir/$(basename "$file")"

    if ! sips --resampleWidth "$MAX_WIDTH" "$file" --out "$out" >/dev/null; then
      echo "Failed resize: $file" >&2
      rm -rf "$tmpdir"
      skipped=$((skipped + 1))
      continue
    fi

    if ! new_dims=$(get_dim "$out"); then
      echo "Failed read resized dims: $file" >&2
      rm -rf "$tmpdir"
      skipped=$((skipped + 1))
      continue
    fi

    new_w=${new_dims%% *}
    new_h=${new_dims##* }

    # Sanity: ensure we actually downscaled and didn’t produce empty output.
    new_size=$(get_size "$out")
    if [[ "$new_size" -le 0 || "$new_w" -gt "$MAX_WIDTH" || "$new_w" -ge "$old_w" ]]; then
      echo "Bad resize output: $file" >&2
      rm -rf "$tmpdir"
      skipped=$((skipped + 1))
      continue
    fi

    mv "$out" "$file"
    rm -rf "$tmpdir"

    echo "Resized: ${old_w}x${old_h} -> ${new_w}x${new_h} | ${old_size} -> ${new_size} bytes  $file"
    changed=$((changed + 1))
  done < <(find "$dir" -type f \( -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.webp' -o -iname '*.avif' \) -print0)

done

echo
if [[ "$DRY_RUN" -eq 1 ]]; then
  echo "Dry run complete. Would resize: $changed file(s)."
else
  echo "Done. Resized: $changed file(s)."
fi
if [[ "$missing" -gt 0 ]]; then
  echo "Missing dirs: $missing"
fi
if [[ "$skipped" -gt 0 ]]; then
  echo "Skipped files: $skipped"
fi
