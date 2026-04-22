#!/usr/bin/env bash
# Creates all PR labeler labels with colors in the GitHub repository.
# Run once: gh auth login && bash .github/create-labels.sh
set -euo pipefail

create_label() {
  local name="$1" color="$2" description="$3"
  gh label create "$name" --color "$color" --description "$description" --force
}

# Source area labels (green) — src/ subdirectories
for area in configuration contracts decorators dtos enums exceptions \
  factories http services transformers service-provider; do
  create_label "area:${area}" "0E8A16" "Changes in src/${area}"
done

# Project area labels (blue)
create_label "area:tests"        "1D76DB" "Changes in tests or test configuration"
create_label "area:docs"         "1D76DB" "Documentation changes"
create_label "area:config"       "1D76DB" "Changes in package configuration"
create_label "area:ci"           "1D76DB" "Changes in CI/workflows or code style configuration"
create_label "area:dependencies" "1D76DB" "Changes in composer dependencies"
create_label "area:release"      "1D76DB" "Release tooling changes"

# Language labels
create_label "lang:php"   "4F5D95" "Contains PHP changes"
create_label "lang:yaml"  "CB171E" "Contains YAML changes"
create_label "lang:shell" "89E051" "Contains shell script changes"

echo "✅ All labels created successfully."
