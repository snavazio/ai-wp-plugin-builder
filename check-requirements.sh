#!/usr/bin/env bash
#
# check-requirements.sh
# Preflight checker for the AI WP Plugin Builder (Phase 0 + Phase 1).
# Verifies every prerequisite from Document 3 before you run Claude Code.
#
# Usage:
#   ./check-requirements.sh          # normal run
#   ./check-requirements.sh --quiet  # only show problems + summary
#   ./check-requirements.sh --no-color
#
# Exit code: 0 if all HARD requirements pass, 1 otherwise.
# WARN items don't fail the run — they're things you can work around.

set -uo pipefail

# ----------------------------------------------------------------------------
# Config: minimum versions
# ----------------------------------------------------------------------------
MIN_NODE_MAJOR=20
MIN_PHP_MAJOR=8
MIN_PHP_MINOR=0
MIN_DISK_GB=5          # free space needed (Docker images + builds)

QUIET=0
USE_COLOR=1
for arg in "$@"; do
  case "$arg" in
    --quiet|-q)    QUIET=1 ;;
    --no-color)    USE_COLOR=0 ;;
    -h|--help)
      grep '^#' "$0" | sed 's/^#\s\?//' | head -n 14
      exit 0 ;;
  esac
done

# No color if not a terminal
if [ ! -t 1 ]; then USE_COLOR=0; fi

if [ "$USE_COLOR" = "1" ]; then
  C_RESET='\033[0m'; C_GREEN='\033[0;32m'; C_RED='\033[0;31m'
  C_YELLOW='\033[0;33m'; C_BLUE='\033[0;34m'; C_BOLD='\033[1m'; C_DIM='\033[2m'
else
  C_RESET=''; C_GREEN=''; C_RED=''; C_YELLOW=''; C_BLUE=''; C_BOLD=''; C_DIM=''
fi

PASS_COUNT=0
WARN_COUNT=0
FAIL_COUNT=0
FAILED_ITEMS=()
WARN_ITEMS=()

# ----------------------------------------------------------------------------
# Helpers
# ----------------------------------------------------------------------------

# print a result line: status, label, detail
report() {
  local status="$1" label="$2" detail="${3:-}"
  local icon color
  case "$status" in
    PASS) icon="✔"; color="$C_GREEN"; PASS_COUNT=$((PASS_COUNT+1)) ;;
    WARN) icon="!"; color="$C_YELLOW"; WARN_COUNT=$((WARN_COUNT+1)); WARN_ITEMS+=("$label") ;;
    FAIL) icon="✘"; color="$C_RED";   FAIL_COUNT=$((FAIL_COUNT+1)); FAILED_ITEMS+=("$label") ;;
  esac
  # In quiet mode, only show WARN/FAIL
  if [ "$QUIET" = "1" ] && [ "$status" = "PASS" ]; then return; fi
  printf "  ${color}%s${C_RESET}  %-22s ${C_DIM}%s${C_RESET}\n" "$icon" "$label" "$detail"
}

section() {
  if [ "$QUIET" = "1" ]; then return; fi
  printf "\n${C_BOLD}${C_BLUE}%s${C_RESET}\n" "$1"
}

have() { command -v "$1" >/dev/null 2>&1; }

# Extract the first dotted version number from a string, e.g. "v20.11.1" -> "20.11.1"
extract_version() {
  echo "$1" | grep -oE '[0-9]+(\.[0-9]+){1,2}' | head -n1
}

# ver_ge A B  -> true if A >= B  (semantic, dotted)
ver_ge() {
  # returns 0 (true) if $1 >= $2
  [ "$1" = "$2" ] && return 0
  local lower
  lower=$(printf '%s\n%s\n' "$1" "$2" | sort -t. -k1,1n -k2,2n -k3,3n | head -n1)
  [ "$lower" = "$2" ]
}

# ----------------------------------------------------------------------------
# Header
# ----------------------------------------------------------------------------
if [ "$QUIET" != "1" ]; then
  printf "${C_BOLD}AI WP Plugin Builder — requirements check${C_RESET}\n"
  printf "${C_DIM}%s${C_RESET}\n" "$(date)"
fi

# ----------------------------------------------------------------------------
# 1. Core toolchain (HARD)
# ----------------------------------------------------------------------------
section "Core toolchain"

# --- Node.js ---
if have node; then
  NODE_RAW="$(node --version 2>/dev/null)"
  NODE_VER="$(extract_version "$NODE_RAW")"
  NODE_MAJOR="${NODE_VER%%.*}"
  if [ -n "$NODE_MAJOR" ] && [ "$NODE_MAJOR" -ge "$MIN_NODE_MAJOR" ] 2>/dev/null; then
    report PASS "Node.js" "v$NODE_VER (need >= $MIN_NODE_MAJOR)"
  else
    report FAIL "Node.js" "v$NODE_VER found — need >= $MIN_NODE_MAJOR. Install Node LTS."
  fi
else
  report FAIL "Node.js" "not found — install Node.js LTS (v$MIN_NODE_MAJOR+) from nodejs.org or nvm"
fi

# --- npm ---
if have npm; then
  report PASS "npm" "v$(extract_version "$(npm --version 2>/dev/null)")"
else
  report FAIL "npm" "not found — normally ships with Node.js"
fi

# --- git ---
if have git; then
  report PASS "git" "v$(extract_version "$(git --version 2>/dev/null)")"
else
  report FAIL "git" "not found — needed for per-phase commits. Install git."
fi

# --- PHP ---
if have php; then
  PHP_VER="$(extract_version "$(php --version 2>/dev/null | head -n1)")"
  PHP_MAJOR="${PHP_VER%%.*}"
  PHP_REST="${PHP_VER#*.}"; PHP_MINOR="${PHP_REST%%.*}"
  if [ -n "$PHP_MAJOR" ] && { [ "$PHP_MAJOR" -gt "$MIN_PHP_MAJOR" ] 2>/dev/null || { [ "$PHP_MAJOR" -eq "$MIN_PHP_MAJOR" ] && [ "${PHP_MINOR:-0}" -ge "$MIN_PHP_MINOR" ]; }; }; then
    report PASS "PHP" "v$PHP_VER (need >= $MIN_PHP_MAJOR.$MIN_PHP_MINOR)"
  else
    report FAIL "PHP" "v$PHP_VER found — need >= $MIN_PHP_MAJOR.$MIN_PHP_MINOR"
  fi
else
  report FAIL "PHP" "not found — install PHP $MIN_PHP_MAJOR.x (php -v)"
fi

# --- Composer ---
if have composer; then
  report PASS "Composer" "v$(extract_version "$(composer --version 2>/dev/null | head -n1)")"
else
  report FAIL "Composer" "not found — needed for PHPCS/PHPStan/PHPUnit deps. getcomposer.org"
fi

# ----------------------------------------------------------------------------
# 2. WordPress tooling (HARD)
# ----------------------------------------------------------------------------
section "WordPress tooling"

# --- WP-CLI ---
if have wp; then
  WP_VER="$(extract_version "$(wp --version 2>/dev/null)")"
  report PASS "WP-CLI" "v${WP_VER:-unknown}"
else
  report FAIL "WP-CLI" "not found — install from wp-cli.org (needed for Plugin Check + activate gate)"
fi

# ----------------------------------------------------------------------------
# 3. Docker sandbox (HARD, unless you use the wp-now fallback)
# ----------------------------------------------------------------------------
section "Sandbox (Docker for @wordpress/env)"

if have docker; then
  DOCKER_VER="$(extract_version "$(docker --version 2>/dev/null)")"
  # Is the daemon actually running?
  if docker info >/dev/null 2>&1; then
    report PASS "Docker" "v$DOCKER_VER, daemon running"
  else
    report FAIL "Docker" "v$DOCKER_VER installed but daemon NOT running — start Docker Desktop (or use the wp-now fallback)"
  fi
else
  report WARN "Docker" "not found — required for @wordpress/env. You can fall back to @wordpress/wp-now (SQLite); tell Claude Code to use it."
fi

# ----------------------------------------------------------------------------
# 4. Claude Code + API access (HARD-ish)
# ----------------------------------------------------------------------------
section "Claude Code + API access"

if have claude; then
  CLAUDE_VER="$(extract_version "$(claude --version 2>/dev/null)")"
  report PASS "Claude Code" "v${CLAUDE_VER:-installed}"
else
  report FAIL "Claude Code" "not found — install: npm i -g @anthropic-ai/claude-code (then run 'claude')"
fi

# API key: either an env var OR Claude Code is logged in. Env var only needed
# if you drive the generator headlessly via the Agent SDK.
if [ -n "${ANTHROPIC_API_KEY:-}" ]; then
  report PASS "ANTHROPIC_API_KEY" "set in environment"
else
  report WARN "ANTHROPIC_API_KEY" "not set — fine if you run the interactive 'claude' CLI (it uses your login). Needed for headless Agent SDK runs."
fi

# ----------------------------------------------------------------------------
# 5. Disk space (WARN)
# ----------------------------------------------------------------------------
section "Disk space"

# Free GB on the filesystem holding the current directory
FREE_GB=""
if have df; then
  # -P for portable output; try -BG, fall back to KB math
  if df -PBG . >/dev/null 2>&1; then
    FREE_GB="$(df -PBG . | awk 'NR==2 {gsub("G","",$4); print $4}')"
  else
    FREE_KB="$(df -Pk . | awk 'NR==2 {print $4}')"
    if [ -n "$FREE_KB" ]; then FREE_GB=$(( FREE_KB / 1024 / 1024 )); fi
  fi
fi

if [ -n "$FREE_GB" ]; then
  if [ "$FREE_GB" -ge "$MIN_DISK_GB" ] 2>/dev/null; then
    report PASS "Free disk" "${FREE_GB} GB free (need ~${MIN_DISK_GB} GB)"
  else
    report WARN "Free disk" "${FREE_GB} GB free — recommend >= ${MIN_DISK_GB} GB for Docker images + builds"
  fi
else
  report WARN "Free disk" "could not determine free space; ensure ~${MIN_DISK_GB} GB available"
fi

# ----------------------------------------------------------------------------
# Summary
# ----------------------------------------------------------------------------
printf "\n${C_BOLD}Summary${C_RESET}\n"
printf "  ${C_GREEN}%d passed${C_RESET}   ${C_YELLOW}%d warnings${C_RESET}   ${C_RED}%d failed${C_RESET}\n" \
  "$PASS_COUNT" "$WARN_COUNT" "$FAIL_COUNT"

if [ "$FAIL_COUNT" -gt 0 ]; then
  printf "\n${C_RED}${C_BOLD}Not ready.${C_RESET} Fix these hard requirements first:\n"
  for item in "${FAILED_ITEMS[@]}"; do printf "  ${C_RED}•${C_RESET} %s\n" "$item"; done
fi

if [ "$WARN_COUNT" -gt 0 ]; then
  printf "\n${C_YELLOW}Warnings (won't block, but review):${C_RESET}\n"
  for item in "${WARN_ITEMS[@]}"; do printf "  ${C_YELLOW}•${C_RESET} %s\n" "$item"; done
fi

if [ "$FAIL_COUNT" -eq 0 ]; then
  printf "\n${C_GREEN}${C_BOLD}All hard requirements met — you're ready to run Claude Code with the build prompt.${C_RESET}\n"
  exit 0
else
  exit 1
fi
