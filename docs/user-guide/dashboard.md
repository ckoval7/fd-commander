# Dashboard User Guide

Welcome to the Field Day Dashboard! This guide covers everything you need to know to use the dashboard effectively during Field Day operations.

## Getting Started

After logging in, click the **Dashboard** link in the navigation menu. The dashboard provides a real-time overview of your event's progress, including QSO counts, scoring, and more.

**Important**: The dashboard only appears when there's an active Field Day event. If you don't see the dashboard, contact your event administrator to create an event.

## Dashboard Overview

The dashboard has four main areas:

1. **Layout Selector** (top left)
2. **Main Content Area** (displays widgets)
3. **Real-time Status Indicator** (bottom right)
4. **Widget Customizer** (sidebar, if available)

## Switching Layouts

The **Layout Selector** dropdown (usually in the top-left corner) lets you switch between different dashboard views:

- **Default Dashboard**: Customizable widget display for normal operations
- **TV Display**: Large-text, dark-theme view optimized for remote viewing (10+ feet away)

To switch layouts:

1. Click the **Layout Selector** dropdown
2. Select your preferred layout
3. The dashboard immediately switches to the new layout
4. Your choice is automatically saved and remembered for future sessions

## Available Widgets

The dashboard displays various widgets depending on your role and permissions. Common widgets include:

### QSO Count & Rate

Shows the total number of contacts logged and the current logging rate (contacts per hour).

- **Primary metric**: Total QSOs
- **Secondary metric**: Current rate in QSOs/hour
- **Updates**: Real-time when new contacts are logged
- **Visible in**: Default Dashboard, TV Display

### Current Score

Displays the running score for your event, including:
- QSO points
- Bonus points
- Power multiplier
- Final total score

The score updates in real-time as new contacts are logged.

**Visible in**: Default Dashboard, TV Display

### Time Remaining

Shows how much time is left in the current event.

- **Countdown timer** to event end
- **Percentage complete**
- **Time-based status** (early/mid/late event)

**Visible in**: Default Dashboard, TV Display

### Recent Contacts

A scrollable list of the last 10 contacts logged, showing:
- Callsign
- Band
- Mode
- Time logged
- Points awarded

**Visible in**: Default Dashboard only

### Band/Mode Activity Grid

A detailed breakdown of contacts by band and mode (CW, Phone, Digital), useful for:
- Identifying weak bands
- Balancing mode diversity
- Tracking activity patterns

**Visible in**: Default Dashboard (hidden on TV by default)

### Progress Toward Goals

Shows your progress against target goals for:
- Total QSO count
- Total score
- Band coverage

Helps you stay on track during the event.

**Visible in**: Default Dashboard, TV Display

### Equipment Status

Shows the status of radio equipment and antennas (if you have equipment management permissions).

**Visible in**: Default Dashboard only, Admin users

### Participant List with Stats

Lists all participants and their contributions (if you have user management permissions).

**Visible in**: Default Dashboard only, Admin users

### Bonus Points Manager

Tool for awarding and managing bonus points (if you have bonus management permissions).

**Visible in**: Default Dashboard only, Admin users

### Guestbook Stats

Summary of guestbook entries and visitor information (if you have guestbook management permissions).

**Visible in**: Default Dashboard only, Admin users

## Customizing Your Dashboard (Default Layout Only)

The **Default Dashboard** layout allows you to customize which widgets you see.

### Show/Hide Widgets

In the Default Dashboard, look for the **Widget Customizer** panel (usually on the left sidebar).

1. Find the widget you want to toggle
2. Click the checkbox to show or hide it
3. Your preferences are automatically saved to your browser

### Widget Order

Widgets are displayed in a predefined order. You cannot manually reorder widgets yet (this feature is coming soon).

## Real-Time Updates

The dashboard updates in real-time when new contacts are logged. You'll notice:

- **Number changes**: QSO counts and scores update instantly
- **Flash animations**: Numbers briefly highlight when they change
- **Live indicator**: A green "Live" badge appears in the bottom-right when WebSocket connection is active

### Connection Status

The dashboard shows connection status in the **bottom-right corner**:

- **Green "Live"**: WebSocket connected and receiving real-time updates
- **Yellow "Offline"**: Internet or WebSocket connection lost
- **Data still updates**: Every 1 minute the dashboard automatically refreshes data even if offline

If you see "Offline":
- Check your internet connection
- Refresh the page (Ctrl+R or Cmd+R)
- The dashboard will auto-reconnect when internet is restored

## TV Display Mode

The TV Dashboard is designed to be readable from 10+ feet away, making it perfect for displaying on a large monitor during Field Day operations.

### When to Use TV Mode

- Display on a large monitor or projector in the shack
- Show progress to team members from across the room
- Keep QSO count and score visible at all times

### TV Display Features

- **Large fonts**: Easy to read from a distance
- **Dark theme**: Reduces eye strain
- **Real-time updates**: Shows latest QSO counts and scoring
- **No interactive elements**: Pure information display
- **F-key toggle**: Hide/show navigation for full-screen viewing

### Using the F Key

Press **F** to toggle the navigation header and layout selector:

- **First press**: Hides the header for full-screen viewing
- **Second press**: Shows the header again
- **Useful for**: Maximizing screen space on displays

To exit TV Display mode and go back to normal dashboard:

1. Press **F** to show the navigation (if hidden)
2. Use the **Layout Selector** to switch back to "Default Dashboard"

### TV Mode Widgets

TV Display shows:
- QSO Count & Rate (primary focus)
- Current Score
- Time Remaining
- Recent Contacts (scrollable list)
- Band/Mode Activity Grid
- Progress Toward Goals

Widgets not shown:
- Equipment Status
- Participant List
- Bonus Points Manager
- Guestbook Stats

(Admin-only widgets are hidden in TV mode for security and simplicity)

## Using the Default Dashboard

### Primary Workflow

The Default Dashboard is designed for normal Field Day operations:

1. **At a glance**: See total QSOs, current score, and time remaining
2. **Recent activity**: Check the last 10 contacts logged
3. **Detailed analysis**: Switch to Band/Mode grid or Progress widgets for deeper insights
4. **Admin tasks**: Access equipment, participant, and bonus point management

### Tips for Operations

**Keep it Simple**: The default widget order shows the most important metrics first:
1. QSO Count & Rate
2. Current Score
3. Time Remaining
4. Recent Contacts

**Show What You Need**: Use the Widget Customizer to hide widgets you don't need to reduce clutter.

**Monitor from Different Areas**: If you can't see the dashboard from your station, open it on a phone or tablet in a separate browser tab.

## Troubleshooting

### Dashboard Doesn't Load

**Issue**: Dashboard page shows "No active event"

**Solutions**:
- Contact your event administrator to create an event
- Verify the event dates are correct (start and end times)
- Check that you have permission to view the dashboard

### Widgets Don't Update

**Issue**: QSO counts and scores aren't updating in real-time

**Solutions**:
- Check the connection status indicator (should show "Live")
- Refresh the page (Ctrl+R or Cmd+R)
- If offline, check your internet connection
- Wait 1-2 minutes—the dashboard auto-refreshes every minute

### Widget Shows Error

**Issue**: A widget displays a "Failed to load widget" message

**Solutions**:
- Refresh the page
- Check your browser console for error messages (F12)
- Try switching layouts and back
- Contact an administrator if the error persists

### Layout Won't Switch

**Issue**: Layout Selector dropdown doesn't work or selection doesn't stick

**Solutions**:
- Check that JavaScript is enabled in your browser
- Clear your browser cache and cookies
- Try a different browser
- Make sure localStorage is enabled (privacy settings)

### TV Display Text Too Small

**Issue**: TV Display text is hard to read from a distance

**Solutions**:
- Increase your monitor's zoom level (Ctrl++ on keyboard)
- Use a larger monitor
- Move the monitor closer to the viewing area
- Contact your administrator—they may need to adjust theme sizes

### Can't See Widgets I Should Have Access To

**Issue**: Some widgets don't appear even though you should have permission

**Solutions**:
- Refresh the page
- Check with an administrator about your assigned role
- Log out and log back in
- Clear browser cache

## Best Practices

### During Operations

✅ **DO**:
- Keep the dashboard visible on a monitor while logging contacts
- Use TV Display on a large shared monitor for team visibility
- Refresh periodically if you notice data seems stale
- Use real-time updates to track momentum and pace

❌ **DON'T**:
- Rely solely on cached data—refresh if needed
- Hide all widgets and try to remember what you've accomplished
- Leave the dashboard unmonitored if you're trying to hit goals

### For Administrators

✅ **DO**:
- Set up an active event with correct start/end times before operations begin
- Display the TV Dashboard on a shared monitor in the shack
- Monitor participant activity and equipment status
- Update bonus points in real-time to reflect special achievements

❌ **DON'T**:
- Hide critical widgets needed for operations
- Leave the dashboard customizer open during the event (can confuse operators)
- Use non-admin layouts to restrict what operators see (use permissions instead)

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| **F** | Toggle navigation in TV Display mode |
| **Ctrl+R** (or **Cmd+R**) | Refresh dashboard |
| **Ctrl+F** (or **Cmd+F**) | Search within Recent Contacts widget |

## Features Coming Soon

The dashboard development team is working on several enhancements:

- **Drag-to-reorder widgets**: Customize widget layout order
- **Mobile layout**: Optimized dashboard for phones and tablets
- **Kiosk mode**: Full-screen display with no navigation
- **Export data**: Download QSO and scoring reports
- **Custom themes**: Create your own dashboard color schemes

Check back for updates!

## Getting Help

### Common Questions

**Q: Can I customize the TV Display?**
A: No, TV Display is non-customizable to ensure consistent operation. Use the Default Dashboard if you want to customize widgets.

**Q: Do real-time updates work offline?**
A: No, you need an internet connection for real-time updates. The dashboard will auto-refresh every 1 minute if offline.

**Q: Can I access the dashboard on my phone?**
A: Yes! The Default Dashboard is responsive and works on phones and tablets. TV Display is optimized for large screens.

**Q: How often does the dashboard refresh if offline?**
A: Every 1 minute (60 seconds) if the WebSocket connection is lost.

**Q: Why do I see different widgets than other operators?**
A: Role-based permissions determine which widgets you can see. Contact an administrator if you need access to additional widgets.

### Contact Support

For technical issues:
1. Check the **Troubleshooting** section above
2. Note the error message and when it occurred
3. Contact your event administrator or technical support

## More Information

- **Creating widgets**: See `docs/guides/creating-dashboard-widgets.md`
- **Adding layouts**: See `docs/guides/adding-dashboard-layouts.md`
- **Dashboard architecture**: See `docs/plans/2026-02-07-dashboard-system-design.md`
- **Responsive design patterns**: See `docs/responsive-patterns.md`

---

**Last Updated**: 2026-02-07
**Version**: 1.0
