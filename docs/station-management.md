# Station Management User Guide

## Overview

Stations are the operating positions at your Field Day event where operators will log contacts. Each station represents a complete setup: a primary radio, optional additional equipment (antennas, amplifiers, etc.), and configuration settings that affect scoring and operation.

Managing stations effectively is critical to Field Day success. Well-organized stations with clear equipment assignments ensure operators know where to go, what equipment they're using, and how the station is configured for scoring purposes. Stations can be specialized (VHF-only, satellite, or GOTA) to support different operating modes and participant types.

Field Day stations are event-specific—each event has its own set of stations with unique configurations. This allows you to reuse station setups from previous events or create entirely new configurations for each Field Day.

## Table of Contents

1. [Permissions](#permissions)
2. [Viewing Stations](#viewing-stations)
3. [Creating Stations](#creating-stations)
4. [Station Type Flags](#station-type-flags)
5. [Assigning Equipment](#assigning-equipment)
6. [Cloning Stations from Previous Events](#cloning-stations-from-previous-events)
7. [Best Practices](#best-practices)
8. [Troubleshooting](#troubleshooting)
9. [Related Features](#related-features)

## Permissions

Station management features are protected by two key permissions:

### view-stations

**Who has it:** Operators, Station Captains, Event Managers, Admins

**What it allows:**
- View the station list for any event
- See station details (name, radio, power settings, equipment)
- View active operating sessions at each station
- See which operators are currently logged in

This permission is essential for operators during Field Day who need to know which station to staff and what equipment is available.

### manage-stations

**Who has it:** Station Captains, Event Managers, Admins

**What it allows:**
- Create new stations
- Edit station configurations
- Delete stations (if no active sessions or contacts)
- Clone stations from previous events
- Assign equipment to stations

### Permission Reference Table

| Action | Operator | Station Captain | Event Manager | Admin |
|--------|----------|-----------------|---------------|-------|
| View stations list | ✓ | ✓ | ✓ | ✓ |
| View station details | ✓ | ✓ | ✓ | ✓ |
| Create stations | ✗ | ✓ | ✓ | ✓ |
| Edit stations | ✗ | ✓ | ✓ | ✓ |
| Delete stations | ✗ | ✓ | ✓ | ✓ |
| Assign equipment | ✗ | ✓ | ✓ | ✓ |
| Clone stations | ✗ | ✓ | ✓ | ✓ |

## Viewing Stations

### Accessing the Station List

1. Click **Stations** in the main navigation menu
2. The station list page loads with an event selector at the top

### Filtering by Event

1. Use the **Event** dropdown to select a specific Field Day event
2. The view automatically updates to show stations for that event
3. Quick stats display at the top:
   - **Total Stations**: Number of configured stations
   - **Active Now**: Stations with active operating sessions
   - **Equipment Items**: Total additional equipment assigned

### Understanding Station Cards

Each station displays as a card showing:

**Station Name and Badges**
- The station name prominently displayed at the top
- Color-coded badge indicators for special station types:
  - **GOTA** (orange): Get-On-The-Air station for new operators
  - **VHF-only** (blue): Restricted to VHF frequencies
  - **Satellite** (purple): Dedicated satellite operations

**Primary Radio**
- Radio make and model (e.g., "Icom IC-7300")
- Power capability (if recorded) in watts
- Owner/organization name

**Power Settings**
- Maximum power output in watts
- Power source description (e.g., "Generator", "Solar + Battery")

**Equipment Summary**
- Number of additional equipment items assigned
- Quick link to manage equipment (if you have manage-stations permission)

**Quick Actions** (if you have manage-stations permission)
- Edit button: Modify station configuration
- Equipment button: Assign or manage equipment
- Clone button: Duplicate this station to another event
- Delete button: Remove the station

**Operating Session Status**
- Shows if the station is currently active
- Displays name of current operator (if applicable)

## Creating Stations

### Accessing the Station Creation Form

1. Click **Stations** in the navigation menu
2. Select an event using the Event dropdown
3. Click the **Add Station** button in the top right
4. A form modal opens with configuration options

Alternatively, you can click **Add Station** in the empty state message if no stations exist for the selected event.

### Step 1: Basic Information

**Station Name** (required)
- Enter a meaningful name for the station
- Names must be unique within the event
- Examples: "20m CW", "40m SSB", "Digital Station", "VHF/UHF Rig"
- Use clear naming that helps operators quickly identify their assignment

**Select Event** (required)
- Choose the Field Day event this station belongs to
- The form defaults to your active event if one is set
- You can only create stations for active or future events

### Step 2: Primary Radio Selection

**Primary Radio** (required)
- Click the searchable dropdown to find your radio
- Search by make (e.g., "Icom"), model (e.g., "7300"), or radio characteristics
- The dropdown shows:
  - Radio make and model
  - Power output capability in watts (if recorded)
  - Owner/organization name
  - Current event commitment status (if applicable)

**Automatic Power Configuration**
- When you select a radio, the form automatically fills in the Maximum Power field with the radio's power output (if available)
- You can override this with a different power limit if needed

### Step 3: Power Configuration

**Maximum Power Output** (optional but recommended)
- Enter the maximum power in watts this station can operate at
- Leave blank to allow full radio capability
- Examples: 5W (QRP), 100W (typical), 1500W (legal limit)
- This setting affects Field Day scoring based on your operating class rules

**Power Source Description** (optional but recommended)
- Document how the station is powered
- Examples: "Commercial power", "Generator on north wall", "Solar + battery pack", "12V battery"
- Helpful for operators to understand setup requirements and troubleshooting

### Step 4: Station Type Flags

See the "Station Type Flags" section below for detailed information about GOTA, VHF-only, and Satellite designations.

**GOTA (Get-On-The-Air)** (if allowed)
- Check this box to designate this as the GOTA station
- Only one GOTA station is allowed per event
- GOTA stations have special scoring rules and are reserved for new operators
- The form shows an error if you try to create a second GOTA station
- Only available if your event's operating class allows GOTA stations

**VHF-only**
- Check this box if the station is restricted to VHF frequencies
- Affects scoring calculations based on Field Day rules
- Typical for stations using 2m, 70cm, or higher frequency bands

**Satellite**
- Check this box for stations dedicated to satellite communications
- Identifies the station as a specialized satellite operation
- May have separate scoring rules depending on your event's operating class

### Saving the Station

1. Review all entered information
2. Click the **Save Station** button
3. You'll see a success notification
4. The form closes and you return to the station list
5. The new station appears in the grid

## Station Type Flags

Station type flags are critical for Field Day scoring and operations. Understanding each flag helps you configure stations correctly.

### GOTA (Get-On-The-Air) Stations

**What is GOTA?**
GOTA stations allow newcomers and people who haven't been on the air for a long time to participate in Field Day. These operators work alongside experienced operators who help and guide them.

**Rules:**
- Only one GOTA station per event (enforced by the system)
- The GOTA station operates on the same frequencies and bands as other stations
- Can work the same stations as non-GOTA stations (no restriction on duplicates)
- May have different power limits depending on your event class

**When to use:**
- When you have new operators or guests attending
- When you want to specifically encourage participation from inactive hams
- Required by most Field Day operating class rules

**Power considerations:**
- GOTA stations often run reduced power (5W or 100W) for a more realistic Field Day experience
- Check your Field Day rules for GOTA power requirements

### VHF-Only Stations

**What is VHF-only?**
VHF-only stations are restricted to VHF frequencies (144 MHz and above). These stations typically use 2m, 70cm, or higher bands.

**Rules:**
- Limited to VHF frequencies in scoring
- May have separate multiplier or scoring rules
- Can coexist with HF stations at the same event

**When to use:**
- When setting up mobile or VHF/UHF rigs
- To encourage participation in VHF contests
- When you have limited HF antenna space but good VHF coverage
- For operators who specialize in VHF work

**Power considerations:**
- VHF-only stations typically run QRP power (5-100W)
- Full power VHF operations are possible (up to 1500W in many areas)

### Satellite Stations

**What is a Satellite station?**
Satellite stations communicate through amateur radio satellites (typically LEO - Low Earth Orbit satellites). These require directional antennas and precise tracking.

**Rules:**
- Satellite contacts count toward Field Day scoring
- May have special multiplier rules depending on your event class
- Satellites worked are typically limited to specific amateur satellites (AO-91, AO-92, etc.)

**When to use:**
- When you have operators experienced in satellite work
- When you have the necessary equipment (directional antenna, tracking capability)
- To attract satellite enthusiasts and specialized operators

**Power considerations:**
- Satellite operations can run on QRP power (often more effective than high power)
- Most use 5-100W for satellite contacts
- You can still configure maximum power in the form

### Combining Flags

Station type flags are independent:
- A station can be **VHF-only AND Satellite** (VHF satellites)
- A station cannot be **GOTA AND simultaneously another type** (GOTA is exclusive to the designation)
- Most stations will have **no flags** (standard HF stations)

## Assigning Equipment

Equipment assignment connects your radio equipment inventory to specific stations. This workflow ensures clear understanding of what equipment is at each station.

### Prerequisites

Before assigning equipment, ensure:
1. Equipment is added to your Equipment Inventory (see [Equipment Inventory Management](./equipment-inventory.md))
2. You have the **manage-stations** permission
3. The station already exists with a primary radio selected

### Accessing Equipment Assignment

**Method 1: From the Station List**
1. Find the station in the list
2. Click the **Equipment** button on the station card
3. The equipment assignment interface opens

**Method 2: During Station Creation/Editing**
1. In the station form, after selecting the primary radio
2. There's an **Equipment** tab or section
3. Click to access the equipment assignment interface

### Understanding the Equipment Assignment Interface

The interface uses a two-column layout:

**Left Column: Available Equipment**
- Shows equipment from your inventory that can be assigned
- Includes equipment type, make, model, and owner
- Sorted by equipment type
- Search and filter options available

**Right Column: Assigned Equipment**
- Shows equipment currently assigned to this station
- Includes the primary radio at the top (shaded/disabled)
- Additional equipment below with ability to remove

### Assigning Equipment: Drag & Drop Method

1. **Find the equipment** in the left column (Available Equipment)
2. **Click and hold** the equipment item
3. **Drag** it to the right column (Assigned Equipment)
4. **Release** the mouse button to drop it
5. The equipment moves to the assigned column
6. Confirmation message appears

### Assigning Equipment: Button Method

**For mouse-only or accessibility:**

1. **Click** the equipment item in the Available Equipment column
2. The item highlights
3. **Click** the right arrow button (→) between columns
4. The equipment moves to the assigned column
5. Or use the **+** button next to the equipment

### Keyboard Navigation for Accessibility

**Tab navigation:**
- Tab through equipment items
- Enter/Space: Select/deselect items

**Arrow keys:**
- Up/Down: Navigate between equipment items
- Left/Right: Switch between columns
- Enter: Move between columns

### Removing Equipment Assignments

1. **Click** the equipment in the right column (Assigned Equipment)
2. **Click** the left arrow button (←) or **-** button
3. The equipment returns to Available Equipment
4. Confirmation message appears

### Detecting and Resolving Equipment Conflicts

**What is a conflict?**
A conflict occurs when:
- You try to assign equipment already assigned to another station in the same event
- Equipment is marked as committed to a different event overlapping with this one
- Equipment has a status that prevents assignment (e.g., Lost, Damaged)

**When conflicts appear:**
- A warning message displays when attempting the conflicting assignment
- You can view details about the conflict
- You have options to:
  - **Cancel** the assignment and keep current allocation
  - **Reassign** from the conflicting station (overwrite)
  - **Check other events** to see the conflicting assignment

**Reassigning Equipment Between Stations**

If equipment is already assigned to another station:

1. The system shows a conflict warning
2. Click **Reassign** in the warning message
3. Confirm the reassignment
4. Equipment is removed from the previous station and assigned to this one
5. Notifications are sent to relevant users

### Saving Equipment Assignments

1. Once you've assigned all equipment
2. Click **Save Equipment Assignments** button
3. The system validates for conflicts
4. If no conflicts: Assignments are saved, notifications sent
5. If conflicts exist: You're prompted to resolve them
6. Return to station list view

## Cloning Stations from Previous Events

Station cloning saves time when setting up events with similar configurations to past events. Instead of recreating stations from scratch, you can clone them and optionally copy equipment assignments.

### When to Use Cloning

Use cloning when:
- Your new event has similar band/frequency plans to a previous event
- You want to reuse station names and configurations
- You want to copy equipment assignments from a previous event
- You're setting up a seasonal or annual event with consistent structure

### The Cloning Workflow

#### Step 1: Open the Clone Dialog

1. Go to **Stations** in the navigation
2. Select an event using the Event dropdown
3. Click **Clone from Event** button in the top right
4. A multi-step wizard opens

#### Step 2: Select Source Event

1. In the **Source Event** dropdown, select a completed past event
2. Only events that have already occurred are shown
3. The station count displays next to the event name
4. Review available stations to clone

#### Step 3: Select Stations to Clone

1. The wizard shows all stations from the selected event
2. **Individually select** stations by clicking checkboxes next to each
3. **Select All** using the checkbox at the top
4. **Deselect All** by unchecking the top checkbox again
5. Selected stations display with a highlight
6. Review your selections and click **Next**

#### Step 4: Configure Clone Options

**Target Event** (required)
- Select the Field Day event where you want to clone stations
- Only future or active events are available
- Cannot clone to the same source event

**Copy Equipment Assignments**
- Check to also copy equipment assignments from the source stations
- Leave unchecked to only clone station configuration (radio, power, flags)
- Helpful when previous event used same equipment

**Name Suffix** (optional)
- Add a suffix to cloned station names for clarity
- Example: Source "20m CW" + Suffix " - 2025" = "20m CW - 2025"
- Leave blank to use identical names

**Skip Conflicts**
- When checked (default): Skip equipment that cannot be assigned due to conflicts
- When unchecked: Prompt you to resolve each conflict
- Useful when equipment is committed to other overlapping events

#### Step 5: Review Conflicts (if applicable)

If equipment conflicts are detected:

1. A **Conflict Preview** section appears
2. Shows which equipment cannot be assigned and why
3. Displays reasons:
   - "Equipment committed to [other event]"
   - "Equipment marked as Lost/Damaged"
   - "Equipment already assigned to [other station]"
4. You can:
   - **View details** of conflicting commitments
   - **Reassign** equipment from conflicting stations
   - **Skip** conflicts and proceed without this equipment

#### Step 6: Confirm and Clone

1. Review the final settings
2. Click **Clone Stations** button
3. Processing message appears
4. System creates stations and handles equipment assignments
5. Success message shows:
   - Number of stations cloned
   - Number of equipment items assigned
   - Any skipped due to conflicts
6. Redirects to station list showing the new stations

### Understanding Clone Results

After cloning completes, you see a summary:

- **Stations Cloned**: Count of successfully created stations
- **Equipment Assigned**: Count of equipment items attached
- **Equipment Skipped**: Count of items skipped due to conflicts
- **Warnings**: Any issues that occurred during cloning

### Handling Equipment Conflicts During Clone

**Common conflict scenarios:**

1. **Equipment committed to overlapping event**
   - Resolution: Manually reassign from conflicting event first, or skip
   - Then clone again with the equipment now available

2. **Equipment marked as Lost/Damaged**
   - Resolution: Mark as Returned in Equipment Inventory first
   - Then clone again

3. **Previous station had primary radio that's unavailable**
   - Resolution: The clone fails for that station
   - Option: Clone without equipment assignments, manually select new radio

## Best Practices

### Setup and Planning

- **Set up stations weeks before the event** - Don't wait until the last minute
- **Hold a planning meeting** - Discuss band assignments, operating classes, and GOTA participation
- **Use consistent naming** - "20m CW", "40m SSB", "Digital" are clear; "Station 1", "Rig 2" are not
- **Verify equipment first** - Ensure all primary radios are in inventory before creating stations
- **Document power sources** - Write detailed descriptions like "240V wall power, backup generator on south side" not just "Generator"

### Configuration

- **Set accurate power limits** - Match your operating class rules exactly
- **Record equipment specs** - Include antenna types and other details in equipment notes
- **Flag special stations** - Mark GOTA, VHF, and satellite stations clearly
- **Test before Field Day** - Configure a complete station and verify all equipment works together
- **Plan equipment conflicts** - Know which equipment is going to which stations in advance

### During Field Day

- **Brief operators** - Point operators to the station page so they understand the setup
- **Keep assignments current** - Update equipment status as conditions change
- **Monitor active sessions** - Use the "Active Now" stat to track which stations are operating
- **Document changes** - If you reassign equipment during the event, note why in equipment status

### Maintenance and Record Keeping

- **Archive setups** - Keep successful station configurations for future events
- **Clone proven setups** - Reuse configurations that worked well
- **Review after events** - Document what worked well and what needs improvement
- **Clean up unused stations** - Delete stations without contacts after event ends

## Troubleshooting

### "Why can't I assign this radio?"

**Radios must be the primary station radio, not additional equipment.**

The equipment assignment interface only shows equipment OTHER than the primary radio. To assign a specific radio:
1. Go back to the station form
2. Click **Edit Station**
3. Change the Primary Radio selection
4. Save the station
5. Now the previous primary radio is available for assignment to other stations

### "Equipment shows conflict - what do I do?"

**Equipment is already assigned elsewhere or unavailable.**

Solutions:
1. **Check conflicting assignment** - Click the conflict warning to see which station has it
2. **Reassign from other station** - Remove equipment from the conflicting station first
3. **Skip the equipment** - Leave it unassigned and use different equipment
4. **Check event overlap** - Verify equipment isn't committed to a different overlapping event
5. **Check equipment status** - Ensure equipment isn't marked Lost/Damaged in inventory

### "Can't delete the station - why?"

**Stations cannot be deleted if they have active operating sessions or logged contacts.**

Solutions:
1. **If session is active**: End all operating sessions first (wait until QSO logging is complete)
2. **If station has contacts**: The station is soft-deleted (archived) instead of hard-deleted - contact history is preserved
3. **Verify deletion**: Soft-deleted stations don't appear in the normal list but can be restored by administrators

### "GOTA station won't save - error message"

**"This event already has a GOTA station. Only one GOTA station is allowed per event."**

Solutions:
1. **Check existing GOTA** - Find the current GOTA station in the list
2. **Convert it** - If you don't want it as GOTA, edit it and uncheck the GOTA flag
3. **Delete it** - If it's not needed, delete the old GOTA station then create the new one
4. **Edit instead of create** - If you're updating, click Edit on existing GOTA station rather than Add

### "Clone failed - what went wrong?"

**Clone operation encountered errors during processing.**

Check the error message:

- **"No stations selected"** - Go back and select at least one station to clone
- **"Source and target are the same"** - Select a different target event
- **"Target event not found"** - The event may have been deleted; select another target
- **"Primary radio not available"** - The original station's radio isn't in inventory; clone without equipment and manually assign

**Partial success?** If some stations cloned but others failed:
- Review the results summary
- Manually create the failed stations or try cloning again with different equipment options

### "I assigned the wrong equipment - how do I fix it?"

**Equipment assignments can be changed anytime.**

To reassign equipment:
1. Open the station's equipment assignment interface
2. Click/drag the wrong equipment to remove it (back to left column)
3. Search for and assign the correct equipment
4. Click **Save Equipment Assignments**
5. Notifications are sent about the change

### "Where do I see what's actually in my Equipment Inventory?"

**Visit the Equipment section.**

To view and manage your equipment inventory:
1. Click **Equipment** in main navigation
2. See all your owned equipment with status, type, and event commitments
3. Add new equipment here
4. Mark equipment as committed to events
5. Track equipment status during events

See [Equipment Inventory Management](./equipment-inventory.md) for detailed information.

## Related Features

### Equipment Inventory Management

Learn how to add equipment to your inventory, commit it to events, and track status throughout Field Day.

Read: [Equipment Inventory Management](./equipment-inventory.md)

### Event Management

Create Field Day events, set operating class rules, and configure overall event parameters.

Read: [Event Management](./event-management.md)

### Operating Sessions

Start operating sessions at stations and begin logging contacts. Sessions track which operator is at which station and for how long.

Learn more in the contact logging section of the main documentation.

### Contact Logging

Log QSOs (contacts) during Field Day operations. Each contact is tied to a station and operating session.

Contacts automatically associate with the station and operating session where they're logged.

## Quick Reference

### Keyboard Shortcuts

- **Alt+S**: Jump to Station menu (if available)
- **Ctrl+F**: Focus search in lists
- **Tab**: Navigate form fields and equipment items

### Common URLs

- Station list: `/stations`
- Create station: `/stations/create`
- Equipment inventory: `/equipment`
- Event management: `/events`

### Icon Legend

- 📻 Radio icon: Primary radio equipment
- ⚡ Lightning: Station is currently active
- 🎓 Academic cap: GOTA station
- 📡 Signal: VHF-only station
- 🛰️ Satellite: Satellite station
- 🔧 Wrench: Edit/configure
- 🗑️ Trash: Delete
- ➕ Plus: Add/create
- ➖ Minus: Remove/delete
- ↔️ Arrows: Assign/transfer

## Support and Feedback

For additional help, refer to:
- **System Administrator**: For permission-related issues
- **Equipment Manager**: For equipment inventory questions
- **Event Manager**: For event-specific station setup questions
- **Documentation**: Check other guides at `/docs/`

Found an issue or have a suggestion? Contact your system administrator with details about what you were trying to do and what happened.
