import { chromium } from '@playwright/test';
import assert from 'node:assert/strict';
import { mkdir, readFile } from 'node:fs/promises';

const envText = await readFile('.env', 'utf8');
const env = Object.fromEntries(envText.split(/\r?\n/).filter(line => line && !line.startsWith('#') && line.includes('=')).map(line => {
  const index = line.indexOf('=');
  return [line.slice(0, index), line.slice(index + 1).replace(/^"|"$/g, '')];
}));
const baseUrl = process.env.BASE_URL || 'http://127.0.0.1:8000';
const invitationUrl = `${baseUrl}/`;
const qaName = `QA Tamu ${Date.now()}`;

await mkdir('test-results', { recursive: true });
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const context = await browser.newContext({ viewport: { width: 1440, height: 1000 }, permissions: ['clipboard-read', 'clipboard-write'] });
const page = await context.newPage();
const errors = [];
page.on('pageerror', error => errors.push(error.message));
page.on('response', response => { if (response.url().startsWith(baseUrl) && response.status() >= 500) errors.push(`${response.status()} ${response.url()}`); });

await page.goto(`${invitationUrl}?to=Nadia%20%26%20Keluarga`);
await page.locator('.photo-arch img').waitFor();
await page.evaluate(() => document.fonts.ready);
assert.equal(await page.locator('.guest p').textContent(), 'Nadia & Keluarga');
assert.equal((await page.locator('.footer-brand').innerText()).replace(/\s+/g, ' ').trim(), 'Digital invitation made by Tivity');
assert.equal(await page.locator('.hero-flower').count(), 0);
assert.equal(await page.locator('.gallery-image').count(), 12);
assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
await page.screenshot({ path: 'test-results/laravel-desktop-hero.png' });

await page.locator('#open-invitation').click();
await page.locator('#music-toggle').click();
await page.locator('#galeri').scrollIntoViewIfNeeded();
await page.locator('.gallery-image').last().locator('img').waitFor();
await page.locator('.gallery-image.revealed').first().waitFor();
await page.waitForTimeout(1100);
await page.screenshot({ path: 'test-results/laravel-desktop-gallery.png' });
await page.locator('.gallery-image').first().click();
await page.locator('#lightbox[open]').waitFor();
assert.match(await page.locator('#lightbox figcaption').textContent(), /1 \/ 12/);
await page.locator('.lightbox-next').click();
assert.match(await page.locator('#lightbox figcaption').textContent(), /2 \/ 12/);
await page.locator('#lightbox .dialog-close').click();
await page.locator('#hadiah').scrollIntoViewIfNeeded();
await page.locator('[data-copy="0"]').click();
assert.equal(await page.evaluate(() => navigator.clipboard.readText()), '0000000000');

await page.locator('#ucapan').scrollIntoViewIfNeeded();
await page.locator('#guest-name').fill(qaName);
await page.locator('#attendance').selectOption('yes');
await page.locator('#guests').selectOption('2');
await page.locator('#wish-message').fill('Semoga menjadi keluarga yang penuh kasih dan kebahagiaan. https://example.com');
await page.locator('#rsvp-form [type=submit]').click();
await page.locator('#form-status').filter({ hasText: 'menunggu persetujuan' }).waitFor();
assert.equal(await page.locator('#wish-list').getByText(qaName).count(), 0);

await page.goto(`${baseUrl}/admin/login`);
await page.locator('#email').fill(env.ADMIN_EMAIL);
await page.locator('#password').fill(env.ADMIN_PASSWORD);
await page.getByRole('button', { name: 'Masuk ke Dashboard' }).click();
await page.getByRole('link', { name: 'Kelola Ucapan' }).click();
const qaCard = page.locator('.wish-admin-card').filter({ hasText: qaName });
await qaCard.waitFor();
assert.match(await qaCard.textContent(), /Menunggu/);
await page.screenshot({ path: 'test-results/laravel-dashboard-moderation.png' });
await qaCard.getByRole('button', { name: 'Tampilkan' }).click();
await page.locator('.alert.success').waitFor();

await page.goto(`${baseUrl}/dashboard/invitations/alya-dan-salman/guests`);
await page.getByRole('heading', { name: 'Generator Nama Tamu' }).waitFor();
await page.screenshot({ path: 'test-results/laravel-dashboard-guests.png' });

await page.goto(invitationUrl);
await page.locator('#ucapan').scrollIntoViewIfNeeded();
await page.getByText(qaName, { exact: true }).waitFor();
assert.match(await page.locator('#wish-list').textContent(), new RegExp(qaName));

for (const width of [390, 320]) {
  await page.setViewportSize({ width, height: 844 });
  await page.goto(invitationUrl);
  await page.locator('.photo-arch img').waitFor();
  await page.evaluate(() => document.fonts.ready);
  await page.waitForTimeout(1400);
  assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false, `Overflow at ${width}px`);
  if (width === 390) {
    await page.screenshot({ path: 'test-results/laravel-mobile-hero.png' });
    await page.locator('#galeri').scrollIntoViewIfNeeded();
    await page.locator('.gallery-image.revealed').first().waitFor();
    await page.waitForTimeout(1100);
    await page.screenshot({ path: 'test-results/laravel-mobile-gallery.png' });
  }
}

await page.emulateMedia({ reducedMotion: 'reduce' });
await page.reload();
assert.equal(await page.locator('#motion-toggle').getAttribute('aria-pressed'), 'true');
assert.deepEqual(errors, []);
console.log('PASS: Laravel invitation, database wish submission, pending moderation, customer login, approval, public display, Lottie, clipboard, responsive layout, and reduced motion.');
await browser.close();
