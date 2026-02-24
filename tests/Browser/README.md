# Browser Tests (Laravel Dusk)

## Prerequisites

Browser tests require Chrome or Chromium to be installed on the system.

### Installing Chrome/Chromium

**On Ubuntu/Debian:**
```bash
# Install Chromium
sudo apt-get update
sudo apt-get install -y chromium-browser

# Or install Google Chrome
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo dpkg -i google-chrome-stable_current_amd64.deb
sudo apt-get install -f
```

**On macOS:**
```bash
brew install --cask google-chrome
```

**On Red Hat/CentOS/Rocky Linux:**
```bash
# Install Chromium
sudo dnf install -y chromium

# Or install Google Chrome
wget https://dl.google.com/linux/direct/google-chrome-stable_current_x86_64.rpm
sudo dnf install -y ./google-chrome-stable_current_x86_64.rpm
```

### Updating ChromeDriver

After installing Chrome, update the ChromeDriver binary:
```bash
php artisan dusk:chrome-driver --detect
```

## Running Browser Tests

### Run all browser tests:
```bash
php artisan dusk
```

### Run specific test file:
```bash
php artisan dusk tests/Browser/TvDashboardTest.php
```

### Run specific test method:
```bash
php artisan dusk --filter=test_can_switch_to_tv_layout
```

### Run in headless mode (recommended for CI):
```bash
php artisan dusk --without-tty
```

## TV Dashboard Tests

The `TvDashboardTest.php` file tests:

1. **Layout Switching** - Switching between default and TV layouts via dropdown
2. **LocalStorage Persistence** - Layout preference persists across page reloads
3. **Theme Application** - TV layout applies `data-theme="tvdashboard"` attribute
4. **F Key Toggle** - F key toggles navigation visibility in TV mode
5. **Default Behavior** - Default layout shown when no preference exists
6. **Authentication** - Dashboard requires user authentication
7. **UI Elements** - LIVE badge, connection status, layout selector presence

## Test Data Attributes

Tests use `data-cy="*"` attributes for stable element selection:

- `data-cy="layout-selector"` - Layout dropdown button
- `data-cy="layout-default"` - Default layout option in dropdown
- `data-cy="layout-tv"` - TV layout option in dropdown

## Troubleshooting

### Chrome binary not found
Make sure Chrome or Chromium is installed (see Prerequisites above).

### ChromeDriver version mismatch
Run `php artisan dusk:chrome-driver --detect` to update ChromeDriver to match your Chrome version.

### Port 9515 already in use
Stop any running ChromeDriver processes:
```bash
pkill -f chromedriver
```

### Tests failing due to timing issues
Increase wait times in tests or check network/system performance.

## CI/CD Integration

For automated testing in CI/CD:

1. Install Chromium in the CI environment
2. Run tests headlessly: `php artisan dusk --without-tty`
3. Capture screenshots on failure (stored in `tests/Browser/screenshots/`)
4. Capture console logs on failure (stored in `tests/Browser/console/`)

Example GitHub Actions workflow:
```yaml
- name: Install Chromium
  run: sudo apt-get install -y chromium-browser

- name: Update ChromeDriver
  run: php artisan dusk:chrome-driver --detect

- name: Run Dusk Tests
  run: php artisan dusk --without-tty
```
