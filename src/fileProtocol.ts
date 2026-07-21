/**
 * File-generation protocol for the Ollama engine. Local models don't drive tools, so the model emits
 * whole files inside explicit markers and the orchestrator writes them:
 *
 *   ===FILE: includes/thing.php===
 *   <verbatim file content>
 *   ===ENDFILE===
 *
 * Also extracts a leading ```json ... ``` block when we ask for structured JSON (the spec step).
 */
import { writeFile, mkdir } from 'node:fs/promises';
import { dirname, join, resolve, sep } from 'node:path';

export interface GeneratedFile {
  path: string;
  content: string;
}

const FILE_START = /^===FILE:\s*(.+?)\s*===\s*$/;
const FILE_END = /^===ENDFILE===\s*$/;

/** Local models often wrap file bodies in a ```lang … ``` fence despite instructions — strip it. */
function stripCodeFence(s: string): string {
  let t = s.replace(/^\n+/, '').replace(/\n+$/, '');
  const open = t.match(/^```[a-zA-Z0-9_-]*[ \t]*\n/);
  if (open) {
    t = t.slice(open[0].length);
    t = t.replace(/\n```[ \t]*$/, '');
  }
  return t;
}

/** Parse the ===FILE===/===ENDFILE=== blocks out of a model response. */
export function parseFiles(text: string): GeneratedFile[] {
  const lines = text.split('\n');
  const files: GeneratedFile[] = [];
  let current: { path: string; body: string[] } | null = null;
  for (const line of lines) {
    const start = FILE_START.exec(line);
    if (start && !current) {
      current = { path: start[1].trim(), body: [] };
      continue;
    }
    if (FILE_END.test(line) && current) {
      files.push({ path: current.path, content: stripCodeFence(current.body.join('\n')) });
      current = null;
      continue;
    }
    if (current) current.body.push(line);
  }
  return files;
}

/** Extract the first fenced JSON object from a response (for the spec step). */
export function extractJson(text: string): string | null {
  const fenced = text.match(/```(?:json)?\s*([\s\S]*?)```/i);
  if (fenced) return fenced[1].trim();
  const brace = text.match(/(\{[\s\S]*\})/);
  return brace ? brace[1].trim() : null;
}

/** Safely write generated files inside a workspace, refusing paths that escape it. */
export async function writeGeneratedFiles(files: GeneratedFile[], workspaceDir: string): Promise<{ written: string[]; rejected: string[] }> {
  const written: string[] = [];
  const rejected: string[] = [];
  const root = resolve(workspaceDir);
  for (const f of files) {
    const abs = resolve(workspaceDir, f.path);
    if (abs !== root && !abs.startsWith(root + sep)) {
      rejected.push(f.path);
      continue;
    }
    await mkdir(dirname(abs), { recursive: true });
    await writeFile(abs, f.content.endsWith('\n') ? f.content : f.content + '\n', 'utf8');
    written.push(f.path);
  }
  return { written, rejected };
}

export { join as _join };
