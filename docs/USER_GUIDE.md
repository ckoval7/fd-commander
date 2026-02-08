# Dashboard System User Guide

Welcome to the Field Day Logging Database Dashboard System! This guide will help you create custom dashboards to monitor your Field Day event in real-time.

## Table of Contents
1. [Getting Started](#getting-started)
2. [Creating Your First Dashboard](#creating-your-first-dashboard)
3. [Available Widgets](#available-widgets)
4. [Customizing Widgets](#customizing-widgets)
5. [Editing Dashboard Layout](#editing-dashboard-layout)
6. [TV Dashboard Mode](#tv-dashboard-mode)
7. [Keyboard Shortcuts](#keyboard-shortcuts)
8. [Troubleshooting](#troubleshooting)

---

## Getting Started

### Accessing Dashboards

Once logged in, access your dashboards from the main navigation menu. You'll see:
- **My Dashboards:** Your personal dashboards (up to 10)
- **Default Dashboard:** Created automatically with common widgets
- **Create New:** Add a new custom dashboard

### Dashboard Limits
- **Maximum dashboards per user:** 10
- **Maximum widgets per dashboard:** 20
- **Recommended widgets for performance:** 6-8 per dashboard

---

## Creating Your First Dashboard

### Step 1: Create a New Dashboard

1. Click **"Manage"** button in the dashboard header
2. Click **"Create New Dashboard"**
3. Enter a **title** (e.g., "Main Station Monitor")
4. Add an optional **description**
5. Click **"Create"**

Your new dashboard starts empty - let's add some widgets!

### Step 2: Enter Edit Mode

1. Open your new dashboard
2. Click **"Edit Mode"** in the top-right corner
3. The dashboard grid appears with widget placeholders

### Step 3: Add Widgets

1. In Edit Mode, click **"Add Widget"**
2. Choose a widget type (see [Available Widgets](#available-widgets))
3. Configure the widget settings
4. Click **"Add"**
5. Repeat to add more widgets

### Step 4: Save Your Dashboard

1. Click **"Save Changes"** to keep your layout
2. Click **"Exit Edit Mode"** to view your dashboard live

---

## Available Widgets

### 1. **Stat Card** 📊
Displays a single statistic with an icon.

**Available Metrics:**
- **Total Score:** Current event point total
- **QSO Count:** Number of contacts made
- **Sections Worked:** Unique ARRL/RAC sections contacted
- **Operators Count:** Number of active operators

**Best Used For:** Quick overview of key metrics

**Size:** Small (1 grid cell)

---

### 2. **Chart** 📈
Visualizes data as a bar, line, or pie chart.

**Available Data Sources:**
- **QSOs per Hour:** Contact rate over time
- **QSOs per Band:** Distribution across frequency bands
- **QSOs per Mode:** Distribution across operating modes (SSB, CW, Digital)

**Chart Types:**
- **Bar Chart:** Compare values across categories
- **Line Chart:** Show trends over time
- **Pie Chart:** Show proportions (best for band/mode distribution)

**Best Used For:** Analyzing activity patterns

**Size:** Medium to Large (2-4 grid cells)

---

### 3. **Progress Bar** 📊
Shows progress toward next milestone (50, 100, 150, 200, etc.).

**Configuration:**
- **Metric:** Next milestone (default)

**Best Used For:** Motivating operators toward goals

**Size:** Small (1 grid cell)

---

### 4. **List Widget** 📋
Displays recent activity as a scrolling list.

**Available Lists:**
- **Recent Contacts:** Last 15 QSOs with callsign, band, mode, operator
- **Active Stations:** Currently operating stations with operator and band
- **Equipment Status:** Radio and antenna assignments

**Best Used For:** Monitoring real-time activity

**Size:** Medium (2 grid cells)

---

### 5. **Timer** ⏱️
Counts up from event start or down to event end.

**Modes:**
- **Count Up:** Time since event started
- **Count Down:** Time remaining in event

**Best Used For:** Tracking event progress

**Size:** Small (1 grid cell)

---

### 6. **Info Card** ℹ️
Displays static information or instructions.

**Configuration:**
- **Title:** Card heading
- **Content:** Text or instructions

**Best Used For:** Station rules, frequency plans, operating instructions

**Size:** Small to Medium (1-2 grid cells)

---

### 7. **Feed** 📢
Live activity feed with system notifications.

**Feed Types:**
- **All Activity:** Everything happening at the event
- **Operator Activity:** New operators, session changes
- **Equipment Updates:** Radio/antenna changes, issues
- **System Events:** Milestones, bonus points, achievements

**Best Used For:** Staying informed of event-wide activity

**Size:** Medium to Large (2-4 grid cells)

---

## Customizing Widgets

### Editing Widget Settings

1. Enter **Edit Mode**
2. Click the **gear icon (⚙️)** on any widget
3. Modify settings:
   - Change metric/data source
   - Adjust chart type
   - Update display options
4. Click **"Save"**

### Hiding Widgets

Widgets can be hidden without deleting:

1. Enter **Edit Mode**
2. Click the **eye icon (👁️)** on any widget
3. Hidden widgets show with reduced opacity
4. Click again to show
5. **Save Changes** to persist

**Use Case:** Hide widgets you don't need right now but might want later.

### Removing Widgets

1. Enter **Edit Mode**
2. Click the **trash icon (🗑️)** on any widget
3. Widget is removed from dashboard
4. **Save Changes** to confirm

**Note:** Removed widgets must be re-added from scratch.

---

## Editing Dashboard Layout

### Reordering Widgets (Drag & Drop)

1. Enter **Edit Mode**
2. **Hover over a widget** until you see the drag handle (six dots ⋮⋮)
3. **Click and hold** the drag handle
4. **Drag the widget** to a new position
5. **Release** to drop
6. **Save Changes** to keep the new order

**Tip:** Widgets automatically fill the grid left-to-right, top-to-bottom.

### Responsive Layout

Dashboards automatically adjust to screen size:

| Screen Size | Columns | Best For |
|-------------|---------|----------|
| Mobile | 1 column | Phones, monitoring on the go |
| Tablet | 2 columns | iPad, small displays |
| Desktop | 3 columns | Laptops, standard monitors |
| Large Desktop | 4 columns | Large monitors, multiple displays |

**You don't need to do anything** - the layout adapts automatically!

---

## TV Dashboard Mode

### What is TV Dashboard?

TV Dashboard is a **large-format display** optimized for viewing on TVs, projectors, or public monitors at your Field Day site.

**Features:**
- **5-column fixed grid** (optimized for 1080p)
- **Larger text and numbers** for distance viewing
- **Fullscreen/Kiosk Mode** for distraction-free display
- **Event countdown timer** always visible
- **Auto-adjusting row heights** to fit screen

### Accessing TV Dashboard

From any dashboard, click the **"TV Mode"** button or visit:
```
/dashboard/{id}/tv
```

### Using Kiosk Mode

**Kiosk Mode** hides the header for clean full-screen display.

**Option 1: URL Parameter**
```
/dashboard/{id}/tv?kiosk=1
```

**Option 2: Keyboard Shortcut**
1. Open TV Dashboard
2. Press **F** key (toggles fullscreen + kiosk)
3. Press **ESC** or **F** again to exit

**Kiosk Mode Indicator:**
Look for the green dot and "Kiosk Mode" text in the bottom-left corner.

### TV Dashboard Best Practices

**Widget Selection:**
- Limit to **10-15 widgets** for readability
- Use **large stat cards** for key metrics
- **Charts work great** - easy to read from distance
- **Progress bars** motivate operators
- **Feed widget** keeps everyone informed

**Layout Tips:**
- Put most important widgets in **top row**
- Group related widgets together
- Hide widgets that aren't relevant for public display
- Test on actual display before the event

**Technical Setup:**
- Use **Chrome or Firefox** for best performance
- Enable **fullscreen mode** (F key)
- Disable screen timeout/sleep
- Use **landscape orientation**
- Recommended resolution: **1920x1080 (1080p)**

---

## Keyboard Shortcuts

### Global Shortcuts

| Key | Action | Available In |
|-----|--------|-------------|
| **F** | Toggle Fullscreen | All dashboards |
| **ESC** | Exit Fullscreen / Exit Kiosk | All dashboards |
| **Tab** | Navigate controls | All dashboards |
| **Enter/Space** | Activate button | All dashboards |

### Dashboard Manager Shortcuts

| Key | Action |
|-----|--------|
| **Tab** | Navigate between dashboards |
| **Enter** | Select/Edit dashboard |
| **Delete** | Delete dashboard (when selected) |

### Edit Mode Shortcuts

| Key | Action |
|-----|--------|
| **S** | Save Changes (Ctrl/Cmd+S) |
| **ESC** | Exit Edit Mode |
| **Tab** | Navigate widgets |

**Accessibility Note:** All dashboard functions are fully accessible via keyboard. No mouse required!

---

## Troubleshooting

### Dashboard Not Updating

**Problem:** Widgets show stale data or "Loading..." indefinitely.

**Solutions:**
1. **Check Internet Connection:**
   - Widgets need internet to update
   - Look for red "Connection Lost" banner

2. **Check Event Status:**
   - Dashboards only show data from active events
   - Verify event is currently running (check dates/times)

3. **Refresh the Page:**
   - Press **F5** or **Ctrl+R** (Cmd+R on Mac)
   - This reloads all widgets

4. **Clear Browser Cache:**
   - Chrome: Ctrl+Shift+Delete → Clear browsing data
   - Firefox: Ctrl+Shift+Delete → Clear recent history

### Widgets Showing "No Data"

**Problem:** Widgets display "No data available" or empty states.

**Causes:**
- **No Active Event:** Dashboard needs an event to be running
- **No Contacts Yet:** QSO-based widgets need contacts to display
- **Event Not Started:** Widgets activate when event begins

**Solution:** Wait for event to start and contacts to be logged.

### Drag-and-Drop Not Working

**Problem:** Can't reorder widgets by dragging.

**Solutions:**
1. **Check Edit Mode:**
   - Drag-and-drop only works in Edit Mode
   - Click "Edit Mode" button first

2. **Use Drag Handle:**
   - Click and hold the **six-dot handle** (⋮⋮)
   - Don't drag the widget content itself

3. **Browser Compatibility:**
   - Use Chrome, Firefox, Safari, or Edge
   - Update to latest browser version

4. **Try Keyboard (Accessibility):**
   - Tab to widget
   - Use arrow keys to move (if implemented)
   - Or delete and re-add widget in desired position

### Real-time Updates Stopped

**Problem:** "Real-time updates paused" banner appears.

**Cause:** WebSocket connection to server lost.

**What Happens:**
- Dashboard **automatically falls back to polling** every 5 seconds
- Data still updates, just slightly slower
- Orange "Reconnecting..." banner shows during attempts

**Solutions:**
1. **Wait for Reconnection:**
   - System retries automatically
   - Usually reconnects within 30 seconds

2. **Check Network:**
   - Verify internet connection is stable
   - Look for firewall blocking WebSocket (port 6001)

3. **Dismiss Banner:**
   - Click "Dismiss" to hide banner
   - Polling continues in background

### TV Dashboard Not Fitting Screen

**Problem:** TV dashboard has scroll bars or cut-off content.

**Solutions:**
1. **Enter Fullscreen:**
   - Press **F** key
   - This hides browser UI and optimizes layout

2. **Adjust Browser Zoom:**
   - **Zoom Out:** Ctrl+Minus (Cmd+Minus on Mac)
   - **Zoom In:** Ctrl+Plus (Cmd+Plus on Mac)
   - **Reset:** Ctrl+0 (Cmd+0 on Mac)

3. **Check Display Resolution:**
   - TV dashboard optimized for **1920x1080 (1080p)**
   - Lower resolutions may require zoom adjustment

4. **Reduce Widget Count:**
   - Limit to 10-15 widgets for 1080p displays
   - More widgets = smaller text/content

### Can't Find Dashboard Manager

**Problem:** Don't see "Manage" button or can't create dashboards.

**Solutions:**
1. **Check Login:**
   - Must be logged in to create dashboards
   - Guest users can only view shared dashboards

2. **Look for Button:**
   - "Manage" button in top-right of dashboard header
   - Has gear icon (⚙️)

3. **Dashboard Limit Reached:**
   - Maximum 10 dashboards per user
   - Delete unused dashboards to create new ones

### Performance Issues (Slow/Laggy)

**Problem:** Dashboard loads slowly or feels unresponsive.

**Causes:**
- Too many widgets (>15)
- Slow internet connection
- Old browser version
- Many browser tabs open

**Solutions:**
1. **Reduce Widgets:**
   - Recommended: 6-8 widgets per dashboard
   - Maximum: 20 widgets
   - Hide or remove unused widgets

2. **Close Other Tabs:**
   - Browsers limit resources per tab
   - Close unused tabs for better performance

3. **Update Browser:**
   - Chrome, Firefox, Safari, Edge recommended
   - Update to latest version

4. **Check Internet Speed:**
   - Widgets poll every 3-5 seconds
   - Slow connection delays updates

5. **Restart Browser:**
   - Close all browser windows
   - Reopen and try again

---

## Tips & Best Practices

### Dashboard Organization

**Create Multiple Dashboards for Different Roles:**
- **Operator Dashboard:** QSO count, progress bar, recent contacts
- **Station Manager:** All stations, equipment status, operator activity
- **Public Display (TV):** Score, sections, chart, progress, timer
- **Admin Dashboard:** Full statistics, charts, activity feed

### Widget Selection

**Don't Overwhelm:**
- Start with 4-6 essential widgets
- Add more as needed
- Quality over quantity - focus on what matters

**Group Related Widgets:**
- Put all stats together
- Keep charts separate
- Place timer near score for context

### Optimal Widget Combinations

**Small Dashboard (4-6 widgets):**
- 1× Total Score (stat card)
- 1× QSO Count (stat card)
- 1× Progress Bar
- 1× Recent Contacts (list)
- 1× QSOs per Hour (chart)
- 1× Timer

**Medium Dashboard (8-10 widgets):**
- Add: Sections Worked, Operators Count
- Add: QSOs per Band chart
- Add: Activity Feed

**Large Dashboard (12-15 widgets):**
- Add: Active Stations list
- Add: Equipment Status list
- Add: QSOs per Mode chart
- Add: Info Card with operating rules

**TV Dashboard (10-12 widgets):**
- Focus on large, readable widgets
- Stat cards, charts, progress bar, timer
- Minimal text-heavy widgets (feeds/lists)

### Event Day Checklist

**Before Event Starts:**
- [ ] Create dashboards for each role
- [ ] Test TV dashboard on actual display
- [ ] Configure kiosk mode URL
- [ ] Hide unused widgets
- [ ] Verify internet connection stable
- [ ] Screenshot dashboards for reference

**During Event:**
- [ ] Monitor connection status banner
- [ ] Check widgets updating every few minutes
- [ ] Adjust TV dashboard zoom if needed
- [ ] Show public TV dashboard for visitors

**After Event:**
- [ ] Keep dashboards for future events
- [ ] Duplicate best dashboard as template
- [ ] Note what widgets worked best

---

## Getting Help

### Support Resources

**Documentation:**
- **User Guide:** This document
- **Developer Docs:** `docs/dashboard-system.md` (technical details)
- **Accessibility Guide:** `docs/dashboard-accessibility.md`

**Community:**
- GitHub Issues: Report bugs or suggest features
- Email: support@fdlogdb.org (if configured)

### Common Questions

**Q: Can I share my dashboard with others?**
A: Not yet - dashboards are currently per-user. This feature is planned for future release.

**Q: Can I export dashboard data?**
A: Not directly from dashboards. Use the Reports section for data export.

**Q: Do dashboards work on mobile?**
A: Yes! Dashboards are fully responsive and work on phones/tablets.

**Q: Can I customize widget colors/themes?**
A: Theme customization is planned for a future update.

**Q: How often do widgets update?**
A: Every 3-5 seconds via WebSocket, or 5-second polling if WebSocket unavailable.

**Q: Do I need to save my dashboard?**
A: In Edit Mode, yes. Click "Save Changes" before exiting Edit Mode. In view mode, your dashboard persists automatically.

---

## What's Next?

**Explore Advanced Features:**
- Try different widget configurations
- Set up multiple dashboards for different purposes
- Create a public TV dashboard for your event
- Experiment with different chart types

**Provide Feedback:**
- Report bugs or unexpected behavior
- Suggest new widget types or features
- Share your dashboard layouts with the community

**Stay Updated:**
- Check for new features in future releases
- Read release notes for improvements
- Join the community for tips and best practices

---

## Version History

**v1.0.0 (February 2026)**
- Initial release
- 7 widget types (StatCard, Chart, ProgressBar, List, Timer, InfoCard, Feed)
- TV Dashboard mode with kiosk support
- Real-time updates via WebSockets + polling fallback
- Responsive layouts for mobile, tablet, desktop
- Full keyboard accessibility
- Dashboard management (create, edit, delete, duplicate)

---

**Thank you for using the FD Log DB Dashboard System!**

For technical support or feature requests, please visit our GitHub repository or contact your system administrator.

---

*Last Updated: February 8, 2026*
