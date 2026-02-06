#!/usr/bin/env bash
# Fetch prebuilt front-end assets over HTTPS (no SSH keys needed).
# Run from repo root: bash map_search/scripts/fetch_assets.sh
set -euo pipefail

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
assets_dir="$root_dir/map_search/bower_components"
mkdir -p "$assets_dir"
mkdir -p "$assets_dir/jquery" "$assets_dir/jqueryui"

fetch_zip () {
  local url="$1" dest="$2" strip="$3"
  tmp=$(mktemp)
  echo "Downloading $url ..."
  curl -L "$url" -o "$tmp"
  rm -rf "$dest"
  mkdir -p "$dest"
  unzip -q "$tmp" -d "$dest"
  if [[ -n "$strip" ]]; then
    # move contents of single top folder up one level
    inner="$dest/$strip"
    if [[ -d "$inner" ]]; then
      shopt -s dotglob
      mv "$inner"/* "$dest"/
      rmdir "$inner"
    fi
  fi
  rm "$tmp"
}

fetch_tgz () {
  local url="$1" dest="$2" strip="$3"
  tmp=$(mktemp)
  echo "Downloading $url ..."
  curl -L "$url" -o "$tmp"
  rm -rf "$dest"
  mkdir -p "$dest"
  tar -xzf "$tmp" -C "$dest"
  if [[ -n "$strip" ]]; then
    inner="$dest/$strip"
    if [[ -d "$inner" ]]; then
      shopt -s dotglob
      mv "$inner"/* "$dest"/
      rmdir "$inner"
    fi
  fi
  rm "$tmp"
}

echo "Fetching core libraries..."
curl -L "https://code.jquery.com/jquery-2.0.3.min.js" -o "$assets_dir/jquery/jquery.min.js"

# jQuery UI (fallback-friendly): first try npm tarball, else CDN files
set +e
fetch_tgz "https://registry.npmjs.org/jquery-ui/-/jquery-ui-1.10.5.tgz" "$assets_dir/jqueryui" "package" && ui_ok=1 || ui_ok=0
set -e
if [[ "$ui_ok" -eq 0 ]]; then
  echo "npm tarball failed; pulling from code.jquery.com CDN instead."
  mkdir -p "$assets_dir/jqueryui/ui" "$assets_dir/jqueryui/themes/kfl-theme/images"
  curl -L "https://code.jquery.com/ui/1.10.5/jquery-ui.min.js" -o "$assets_dir/jqueryui/ui/jquery-ui.js"
  curl -L "https://code.jquery.com/ui/1.10.5/themes/smoothness/jquery-ui.css" -o "$assets_dir/jqueryui/themes/kfl-theme/jquery-ui.css"
  # images list from smoothness theme
  base="https://code.jquery.com/ui/1.10.5/themes/smoothness/images"
  for img in ui-bg_flat_0_aaaaaa_40x100.png ui-bg_flat_75_ffffff_40x100.png ui-bg_glass_55_fbf9ee_1x400.png ui-bg_glass_65_ffffff_1x400.png ui-bg_glass_75_dadada_1x400.png ui-bg_glass_75_e6e6e6_1x400.png ui-bg_glass_95_fef1ec_1x400.png ui-bg_highlight-soft_75_cccccc_1x100.png ui-icons_222222_256x240.png ui-icons_2e83ff_256x240.png ui-icons_454545_256x240.png ui-icons_888888_256x240.png ui-icons_cd0a0a_256x240.png; do
    curl -L "$base/$img" -o "$assets_dir/jqueryui/themes/kfl-theme/images/$img"
  done
fi

fetch_tgz "https://github.com/ehynds/jquery-ui-multiselect-widget/archive/1.13.tar.gz" "$assets_dir/jquery-ui-multiselect" "jquery-ui-multiselect-widget-1.13"
# Normalize multiselect layout
if [[ -f "$assets_dir/jquery-ui-multiselect/src/jquery.multiselect.js" ]]; then
  mv "$assets_dir/jquery-ui-multiselect/src/jquery.multiselect.js" "$assets_dir/jquery-ui-multiselect/jquery.multiselect.js"
  rm -rf "$assets_dir/jquery-ui-multiselect/src" "$assets_dir/jquery-ui-multiselect/tests"
fi

fetch_zip "https://github.com/juven14/Collapsible/archive/master.zip" "$assets_dir/jquery-collapsible" "Collapsible-master"

# jQRangeSlider 5.5.0 (cdnjs fallback because GitHub release assets moved)
jq_dir="$assets_dir/jQRangeSlider-5.5.0"
mkdir -p "$jq_dir/css"
base_js="https://cdnjs.cloudflare.com/ajax/libs/jQRangeSlider/5.5.0"
curl -L "$base_js/jQRangeSlider.min.js" -o "$jq_dir/jQRangeSlider.min.js"
curl -L "$base_js/jQRangeSliderMouseTouch.min.js" -o "$jq_dir/jQRangeSliderMouseTouch.min.js"
curl -L "$base_js/jQRangeSliderDraggable.min.js" -o "$jq_dir/jQRangeSliderDraggable.min.js"
curl -L "$base_js/jQRangeSliderBar.min.js" -o "$jq_dir/jQRangeSliderBar.min.js"
curl -L "$base_js/jQRangeSliderHandle.min.js" -o "$jq_dir/jQRangeSliderHandle.min.js"
curl -L "$base_js/jQRangeSliderLabel.min.js" -o "$jq_dir/jQRangeSliderLabel.min.js"
curl -L "$base_js/jQRuler.min.js" -o "$jq_dir/jQRuler.min.js"
curl -L "$base_js/css/classic.min.css" -o "$jq_dir/css/classic.min.css"
curl -L "$base_js/css/iThing.min.css" -o "$jq_dir/css/iThing.min.css"
rm -rf "$assets_dir/jQRangeSlider" 2>/dev/null || true

# OpenLayers 2.13.1 (use cdnjs to avoid GitHub release rate limits)
mkdir -p "$assets_dir/openlayers"
curl -L "https://cdnjs.cloudflare.com/ajax/libs/openlayers/2.13.1/OpenLayers.js" -o "$assets_dir/openlayers/OpenLayers.js"

fetch_tgz "https://github.com/jashkenas/underscore/archive/1.5.2.tar.gz" "$assets_dir/underscore" "underscore-1.5.2"
fetch_tgz "https://github.com/jashkenas/backbone/archive/1.1.0.tar.gz" "$assets_dir/backbone" "backbone-1.1.0"
fetch_tgz "https://github.com/janl/mustache.js/archive/0.7.3.tar.gz" "$assets_dir/mustache" "mustache.js-0.7.3"

# VC (viewcomponents) — provide minimal stub if real repo not reachable
vc_dir="$assets_dir/vc/src"
mkdir -p "$vc_dir"
if [[ ! -f "$vc_dir/VC.js" ]]; then
cat > "$vc_dir/VC.js" <<'EOF'
var VC = VC || {};
VC.SearchResultsFormat = function () {
    this._geojson = new OpenLayers.Format.GeoJSON();
};
VC.SearchResultsFormat.prototype.read = function (features) {
    return this._geojson.read({
        type: "FeatureCollection",
        features: features
    });
};
EOF
fi
if [[ ! -f "$vc_dir/SearchResultsLayer.js" ]]; then
cat > "$vc_dir/SearchResultsLayer.js" <<'EOF'
// Placeholder: app uses only VC.SearchResultsFormat in ResultLayer.js
EOF
fi

echo "Reminder: QuB PHP library is already present under map_search/search/QuB. If you want the upstream repo, download it via HTTPS into map_search/bower_components/QuB/."
echo "Done. Assets are in $assets_dir"
