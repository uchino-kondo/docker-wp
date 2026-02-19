#!/usr/bin/env node

/**
 * TinyPNG 画像圧縮スクリプト
 *
 * 使い方:
 *   npm run img          → TinyPNG API で PNG/JPG を圧縮、GIF/SVG/WebP はコピー
 *   npm run img:copy     → 全画像を圧縮せずにコピーのみ（API不要）
 *
 * 事前準備:
 *   1. .env ファイルを作成し TINYPNG_API_KEY=your_key を設定
 *   2. https://tinypng.com/developers で API キーを取得
 */

import 'dotenv/config';
import tinify from 'tinify';
import fs from 'fs';
import path from 'path';
import { glob } from 'glob';
import crypto from 'crypto';

const SRC_DIR = 'src/img';
const DEST_DIR = 'themes/themesName/img';
const SIG_FILE = path.join(SRC_DIR, '.tinypng-sigs.json');

const TINYPNG_EXTENSIONS = ['.jpg', '.jpeg', '.png'];
const COPY_EXTENSIONS = ['.gif', '.svg', '.webp', '.ico'];
const ALL_EXTENSIONS = [...TINYPNG_EXTENSIONS, ...COPY_EXTENSIONS];

const isCopyOnly = process.argv.includes('--copy-only');

function loadSigs() {
  if (fs.existsSync(SIG_FILE)) {
    return JSON.parse(fs.readFileSync(SIG_FILE, 'utf-8'));
  }
  return {};
}

function saveSigs(sigs) {
  fs.writeFileSync(SIG_FILE, JSON.stringify(sigs, null, 2));
}

function getFileHash(filePath) {
  const content = fs.readFileSync(filePath);
  return crypto.createHash('md5').update(content).digest('hex');
}

function ensureDir(filePath) {
  const dir = path.dirname(filePath);
  fs.mkdirSync(dir, { recursive: true });
}

async function compressWithTinyPNG(srcPath, destPath) {
  ensureDir(destPath);
  const source = tinify.fromFile(srcPath);
  await source.toFile(destPath);
}

function copyFile(srcPath, destPath) {
  ensureDir(destPath);
  fs.copyFileSync(srcPath, destPath);
}

async function main() {
  if (!isCopyOnly) {
    if (!process.env.TINYPNG_API_KEY) {
      console.error('エラー: TINYPNG_API_KEY が設定されていません。');
      console.error('.env ファイルに TINYPNG_API_KEY=your_key を記述してください。');
      process.exit(1);
    }
    tinify.key = process.env.TINYPNG_API_KEY;
  }

  if (!fs.existsSync(SRC_DIR)) {
    console.log(`${SRC_DIR} ディレクトリが存在しません。スキップします。`);
    return;
  }

  const sigs = loadSigs();
  const extPattern = ALL_EXTENSIONS.map(e => e.slice(1)).join(',');
  const files = await glob(`${SRC_DIR}/**/*.{${extPattern}}`);

  if (files.length === 0) {
    console.log('処理対象の画像が見つかりません。');
    return;
  }

  let compressed = 0;
  let copied = 0;
  let skipped = 0;

  for (const file of files) {
    const relativePath = path.relative(SRC_DIR, file);
    const destPath = path.join(DEST_DIR, relativePath);
    const hash = getFileHash(file);
    const ext = path.extname(file).toLowerCase();

    if (sigs[relativePath] === hash && fs.existsSync(destPath)) {
      console.log(`  skip: ${relativePath}`);
      skipped++;
      continue;
    }

    if (!isCopyOnly && TINYPNG_EXTENSIONS.includes(ext)) {
      try {
        console.log(`  tiny: ${relativePath}`);
        await compressWithTinyPNG(file, destPath);
        compressed++;
      } catch (err) {
        console.error(`  error: ${relativePath} - ${err.message}`);
        copyFile(file, destPath);
        copied++;
      }
    } else {
      console.log(`  copy: ${relativePath}`);
      copyFile(file, destPath);
      copied++;
    }

    sigs[relativePath] = hash;
  }

  saveSigs(sigs);

  console.log('');
  console.log(`完了: ${compressed} 圧縮 / ${copied} コピー / ${skipped} スキップ`);

  if (!isCopyOnly && compressed > 0) {
    try {
      const compressionsThisMonth = await tinify.compressionCount;
      console.log(`TinyPNG API 使用回数（今月）: ${compressionsThisMonth} / 500`);
    } catch {
      // API使用回数取得に失敗しても無視
    }
  }
}

main().catch((err) => {
  console.error('予期しないエラー:', err.message);
  process.exit(1);
});
