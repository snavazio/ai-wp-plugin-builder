/**
 * Exemplar retrieval: pick the closest harness-verified plugin(s) from the corpus to use as a TEMPLATE
 * for the local model. Local models are far more reliable adapting a working, gate-passing example than
 * generating from abstract rules — this directly attacks the two measured failure modes: first-pass
 * WPCS style (mirror the exemplar's exact formatting) and fix-regression oscillation (anchor structure).
 *
 * Scoring blends feature-tag overlap (the dominant signal for "which template fits") with a description
 * embedding similarity as a tiebreak.
 */
import { readManifest, readEntrySource, featureTags, type CorpusEntry } from './corpus.js';
import { cosine } from './rag.js';
import { ollamaEmbed, type OllamaConfig } from './engines/ollama.js';
import type { StructuredSpec } from './spec.js';

function jaccard(a: string[], b: string[]): number {
  const A = new Set(a);
  const B = new Set(b);
  if (A.size === 0 && B.size === 0) return 0;
  let inter = 0;
  for (const x of A) if (B.has(x)) inter++;
  return inter / (A.size + B.size - inter);
}

export interface Exemplar {
  entry: CorpusEntry;
  score: number;
  source: string;
}

/** Select up to k verified exemplars most similar to the target spec (excluding the spec's own slug). */
export async function selectExemplars(
  repoRoot: string,
  cfg: OllamaConfig,
  spec: StructuredSpec,
  k = 2,
): Promise<Exemplar[]> {
  const manifest = await readManifest(repoRoot);
  const candidates = manifest.entries.filter((e) => e.slug !== spec.slug);
  if (candidates.length === 0) return [];

  const targetTags = featureTags(spec);
  let queryVec: number[] | null = null;
  try {
    queryVec = await ollamaEmbed(cfg, spec.description);
  } catch {
    queryVec = null; // embeddings optional; fall back to tag-only scoring
  }

  const scored: Array<{ entry: CorpusEntry; score: number }> = [];
  for (const e of candidates) {
    const tagScore = jaccard(targetTags, e.tags);
    let embScore = 0;
    if (queryVec) {
      try {
        embScore = cosine(queryVec, await ollamaEmbed(cfg, e.description));
      } catch {
        embScore = 0;
      }
    }
    scored.push({ entry: e, score: 0.7 * tagScore + 0.3 * embScore });
  }
  scored.sort((a, b) => b.score - a.score);

  const top = scored.slice(0, k).filter((s) => s.score > 0);
  const out: Exemplar[] = [];
  for (const s of top) {
    out.push({ entry: s.entry, score: s.score, source: await readEntrySource(repoRoot, s.entry) });
  }
  return out;
}

/** Render exemplars as a template block for the coder prompt. */
export function formatExemplars(exemplars: Exemplar[]): string {
  if (exemplars.length === 0) return '';
  const header =
    'VERIFIED REFERENCE PLUGIN(S) — each of these PASSED ALL 8 GATES (php-lint, WordPress Coding Standards, ' +
    'PHPStan, Plugin Check security, activation, PHPUnit). Mirror their EXACT structure, escaping/sanitizing ' +
    'patterns, prefixing, docblocks, and WPCS formatting (tabs, Yoda conditions, spacing, inline comments ' +
    'ending in a period). Adapt them to the new spec — do not copy names verbatim.';
  const blocks = exemplars.map(
    (e) => `### Reference: ${e.entry.slug} [${e.entry.tags.join(', ')}]\n${e.source}`,
  );
  return `${header}\n\n${blocks.join('\n\n')}`;
}
