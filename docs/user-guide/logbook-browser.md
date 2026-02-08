# Logbook Browser User Guide

## Overview

The Logbook Browser is a powerful tool for searching, filtering, and analyzing all contacts logged during a Field Day event. Whether you're looking for specific QSOs, finding duplicate contacts, or analyzing operating patterns by band or mode, the Logbook Browser provides flexible filtering and export capabilities.

The Logbook Browser is designed to work on all devices—from desktop computers to tablets and smartphones—with responsive controls that adapt to smaller screens. You can filter by band, mode, station, operator, time period, callsign, and section, then export results to CSV for further analysis.

## Table of Contents

1. [Accessing the Logbook Browser](#accessing-the-logbook-browser)
2. [Understanding the Interface](#understanding-the-interface)
3. [Using Filters](#using-filters)
4. [Filtering by Band](#filtering-by-band)
5. [Filtering by Mode](#filtering-by-mode)
6. [Filtering by Station](#filtering-by-station)
7. [Filtering by Operator](#filtering-by-operator)
8. [Filtering by Time Range](#filtering-by-time-range)
9. [Filtering by Callsign](#filtering-by-callsign)
10. [Filtering by Section](#filtering-by-section)
11. [Finding Duplicates](#finding-duplicates)
12. [Combining Multiple Filters](#combining-multiple-filters)
13. [Pagination and Results](#pagination-and-results)
14. [Exporting to CSV](#exporting-to-csv)
15. [Mobile Usage](#mobile-usage)
16. [Troubleshooting](#troubleshooting)

## Accessing the Logbook Browser

1. From the main navigation menu, click **Logbook**
2. The Logbook Browser page loads, displaying all contacts from the active Field Day event
3. If no event is currently active, a message indicates that no logbook is available

The logbook automatically filters to the currently active event—you cannot view logbooks from past or future events through this interface.

## Understanding the Interface

The Logbook Browser consists of three main sections:

### Filter Panel (Left Side / Top on Mobile)
- Displays all available filter options
- Each filter can be used independently or combined with others
- A **Reset Filters** button clears all selections

### Statistics Summary (Top / Below Filters on Mobile)
- Shows real-time statistics for the current filtered view
- Displays total QSOs, total points, and unique sections worked
- Updates automatically as you change filters

### Results Area (Right Side / Below Stats on Mobile)
- Lists contacts matching your filter criteria
- Shows 50 contacts per page by default
- Includes pagination controls for navigation
- Each contact displays: QSO time, callsign, band, mode, section, exchange, points, and logger name

## Using Filters

Filters work independently and in combination. The logbook updates automatically as you change filter selections.

### How to Use a Filter

1. Locate the filter in the left panel (or top of page on mobile)
2. Click the dropdown or input field
3. Select or enter your desired value
4. Results update automatically
5. The URL updates to preserve your filter selections (shareable links!)

### How to Clear a Filter

- Click the filter field and select "None" or leave it blank
- Or use the **Reset Filters** button to clear all filters at once

## Filtering by Band

Search contacts by radio band (20m, 40m, 80m, 160m, 2m, 70cm, etc.).

### Example: Finding All 40m Contacts

1. Open the **Band** filter dropdown
2. Select **40m**
3. Results update to show only 40m contacts
4. Statistics update to show QSO count, points, and sections for 40m only

### Typical Use Cases

- **Analyze band performance**: See which bands had the most activity
- **Verify band coverage**: Ensure operators worked all planned bands
- **Review problematic bands**: Examine contacts from bands with lower activity

## Filtering by Mode

Search contacts by operating mode (SSB, CW, Digital, etc.).

### Example: Finding All CW Contacts

1. Open the **Mode** filter dropdown
2. Select **CW**
3. Results show only CW contacts
4. Statistics reflect CW-only data

### Typical Use Cases

- **Analyze mode distribution**: See SSB vs CW performance
- **Find CW operators**: Identify which operators prefer CW
- **Digital mode review**: Check digital mode activity and success

## Filtering by Station

Search contacts logged at a specific station.

### Example: Finding All Contacts from Station 1

1. Open the **Station** filter dropdown
2. Select **Station 1** (or your station name)
3. Results show only contacts from that station

### Typical Use Cases

- **Verify station performance**: See total QSOs and points per station
- **Check equipment**: Review contacts from a specific radio setup
- **Compare stations**: Filter one station at a time to compare activity levels

## Filtering by Operator

Search contacts logged by a specific operator or logger.

### Example: Finding All Contacts by John Smith

1. Open the **Operator** filter dropdown
2. Select **John Smith**
3. Results show only contacts logged by that operator
4. Statistics show their QSO count and points

### Typical Use Cases

- **Review individual performance**: See how many QSOs each operator made
- **Identify logging errors**: Find contacts logged by a specific person to verify accuracy
- **Audit operators**: Check activity levels across team members

## Filtering by Time Range

Search contacts within a specific time window.

### Example: Finding All Contacts During Morning Hours

1. Open the **Time From** filter
2. Enter or select **2025-02-08 06:00:00**
3. Open the **Time To** filter
4. Enter or select **2025-02-08 12:00:00**
5. Results show only contacts logged between 6am and noon

### Time Format

Enter times as: `YYYY-MM-DD HH:MM:SS` (for example: `2025-02-08 14:30:00`)

### Typical Use Cases

- **Find operating sessions**: See what happened during specific time periods
- **Analyze performance by time of day**: Compare morning vs afternoon activity
- **Verify event timing**: Confirm event started and ended at expected times
- **Debug logging issues**: Find contacts from a specific time window to verify accuracy

## Filtering by Callsign

Search for contacts with a specific callsign.

### Example: Finding All Contacts with W1AW

1. Open the **Callsign** filter
2. Type **W1AW**
3. Results show only contacts with W1AW (case-insensitive)

### Partial Matching

- Enter partial callsigns: **W1** matches W1AW, W1OP, W1XYZ, etc.
- Works for any part of the callsign

### Typical Use Cases

- **Find specific stations**: Look for contacts with famous callsigns (W1AW, K1N, etc.)
- **Track exchanges**: See all contacts with a specific call to verify exchanges
- **Duplicate detection**: Find multiple contacts with the same callsign
- **Specific search**: Look for contacts from a particular region or club

## Filtering by Section

Search contacts from a specific ARRL section or RAC section (Connecticut, Massachusetts, New York, etc.).

### Example: Finding All Connecticut Section Contacts

1. Open the **Section** filter dropdown
2. Select **Connecticut**
3. Results show only contacts from Connecticut

### Available Sections

The Section dropdown lists all ARRL and RAC sections. This helps ensure you've worked the required number of sections for Field Day scoring.

### Typical Use Cases

- **Section counting**: See how many different sections you've worked
- **Verify section coverage**: Check that all targeted sections have been contacted
- **Regional analysis**: Compare section performance

## Finding Duplicates

The duplicate filter helps identify and manage duplicate QSOs.

### What is a Duplicate?

In Field Day scoring, a duplicate is a second contact with the same station on the same band or mode. Only the first contact counts toward your score.

### How to Find Duplicates

1. Open the **Duplicates** filter dropdown
2. Select one of:
   - **All** (default): Show all contacts
   - **Exclude Duplicates**: Show only valid contacts (not marked duplicate)
   - **Duplicates Only**: Show only contacts marked as duplicates

### Example: Finding All Duplicate Contacts

1. Set **Duplicates** filter to **Duplicates Only**
2. Results show only QSOs marked as duplicates
3. Points are 0 for all duplicate contacts
4. Review these to verify they were correctly identified

### Typical Use Cases

- **Scoring verification**: Ensure duplicates are correctly identified
- **Operating review**: See where operators accidentally worked the same station twice
- **Score calculation**: Verify duplicate handling for final scoring

## Combining Multiple Filters

Filters work together—apply multiple filters to narrow down results.

### Example: Finding All 40m SSB Contacts by John Smith

1. Set **Band** to **40m**
2. Set **Mode** to **SSB**
3. Set **Operator** to **John Smith**
4. Results show only 40m SSB contacts logged by John Smith
5. Statistics update to reflect this narrow slice

### Tips for Combining Filters

- Start with broad filters (band, mode) and narrow down
- Use time range and callsign filters for specific investigations
- Monitor the statistics panel—it updates in real-time as you add filters
- Results update automatically with each filter change

## Pagination and Results

Results display in pages of 50 contacts each.

### Navigating Pages

- Use **Next** and **Previous** buttons to navigate between pages
- The current page number and total pages display at the bottom
- The URL updates to reflect your current page

### Notes

- Pagination resets to page 1 when you change filters
- Each page shows up to 50 contacts
- Mobile view displays contacts as cards (one per line)
- Desktop view displays contacts in a more compact table format

## Exporting to CSV

Export your filtered results to a CSV file for further analysis in Excel, Google Sheets, or other tools.

### How to Export

1. Set your filters to show the contacts you want to export
2. Click the **Export to CSV** button
3. A file downloads with the filename: `field-day-logbook-YYYY-MM-DD-HHMMSS.csv`
4. Open the file in your preferred spreadsheet application

### CSV Contents

The exported file includes these columns:

| Column | Example | Description |
|--------|---------|-------------|
| QSO Time | 2025-02-08 14:30:00 | Date and time of contact |
| Callsign | W1AW | Contacted station callsign |
| Band | 40m | Radio band used |
| Mode | SSB | Operating mode |
| Section | CT | ARRL/RAC section |
| Exchange | 5 | Received exchange value |
| Points | 2 | Scoring points (0 if duplicate) |
| Duplicate Status | No | "Yes" if duplicate, "No" if valid |
| Logger | John Smith | Operator who logged the contact |
| Station | Station 1 | Operating position name |

### Export Tips

- Filter before exporting to reduce file size
- Export "Duplicates Only" to review questionable contacts
- Export by time range to analyze specific operating sessions
- Use the Station filter to get results for specific equipment

## Mobile Usage

The Logbook Browser works on phones and tablets with a responsive design.

### Mobile Layout

**Small Screens (Phones)**
- Filters appear at the top in collapsible sections
- Tap **Filters** to expand/collapse the filter panel
- Results display as full-width cards
- One contact per card (easier to read)

**Tablets (Landscape)**
- Filters appear on the left
- Results appear on the right
- Similar to desktop layout

### Mobile Tips

- Tap filter fields to open dropdowns
- Use **Reset Filters** button to quickly clear selections
- Swipe to scroll results
- Collapse unused filter sections to see more results
- Landscape mode is easier for larger filter panels
- Export to CSV to view results in your preferred app

### Touch Gestures

- **Tap**: Select filters and buttons
- **Scroll**: Navigate through contacts
- **Swipe**: Change pages (if supported by your browser)

## Troubleshooting

### No Results Displayed

**Possible causes:**
- Filters are too restrictive (no contacts match all criteria)
- No active event exists for the current date
- All contacts have been marked as duplicates

**Solutions:**
- Click **Reset Filters** and start over
- Check if an event is currently active (should display in page header)
- Verify filters one at a time to find which filters exclude results

### Statistics Show Zero

**Causes:**
- No contacts exist for the current filters
- Event is not active

**Solutions:**
- Reset filters
- Check the event dates and time
- Verify operators have logged contacts

### Export File is Empty or Shows Headers Only

**Causes:**
- All contacts are marked as duplicates
- Filters are too restrictive with no matching results

**Solutions:**
- Change the "Duplicates" filter to "All"
- Reset filters to see all contacts
- Verify an event is currently active

### Filters Not Updating Results

**Causes:**
- Browser cache issue
- JavaScript not fully loaded

**Solutions:**
- Refresh the page (Ctrl+R or Cmd+R)
- Clear browser cache for this site
- Try a different browser
- Check that JavaScript is enabled in your browser settings

### Callsign Search Returns No Results

**Causes:**
- Callsign is not spelled correctly
- Callsign is capitalized differently (search is case-insensitive, but spelling must match)
- No contacts exist with that callsign

**Solutions:**
- Check the exact spelling in one of your logged contacts
- Try partial callsign search (e.g., **W1** instead of **W1AW**)
- Remove other filters to broaden the search

### Performance is Slow

**Causes:**
- Large number of contacts to display
- Mobile device with limited memory
- Slow internet connection

**Solutions:**
- Use filters to reduce result set
- Close other browser tabs
- Try a different device
- Export to CSV for offline analysis

## Tips for Best Results

1. **Start Broad, Then Narrow**: Begin with one or two filters, then add more
2. **Use Time Range for Sessions**: Filter by time to review specific operating periods
3. **Combine Band + Mode**: See how specific band-mode combinations performed
4. **Export for Analysis**: Use CSV export to pivot, sort, and summarize data
5. **Monitor Statistics**: Watch the stats panel update to understand what your filters are showing
6. **Section Counting**: Use the section filter to verify all needed sections are represented
7. **Duplicate Investigation**: Filter "Duplicates Only" to audit second contacts
8. **Mobile First**: On phone? Use landscape mode for better filter panel visibility
