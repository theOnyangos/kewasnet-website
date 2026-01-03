# Quick Start Guide - Adding Sections and Lectures

## Problem: Can't see the "Add Section" button

The "Add Section" button is located in the **Curriculum tab**, not the Course Details tab.

## Step-by-Step Solution:

### 1. Navigate to Course Edit Page
```
Go to: http://localhost:8080/auth/courses/courses
Click "Edit" on any course
```

### 2. Switch to Curriculum Tab (THIS IS KEY!)
When the edit page loads, you'll see **TWO TABS** at the top:
- **Course Details** (Active by default - has a blue underline)
- **Curriculum** (Gray - click this!)

**Click on the "Curriculum" tab** - it's the second tab with a layers icon.

### 3. Find the Add Section Button
After clicking "Curriculum", you should see:
- Header: "Course Curriculum"
- Subtitle: "Organize your course into sections and lectures"
- **Blue "Add Section" button on the right side**

### 4. If You Still Don't See It

**Check 1: Is the Curriculum tab active?**
- The active tab should have a blue underline
- If "Course Details" still has the blue underline, click "Curriculum" again

**Check 2: Browser Console**
Press F12 and check for JavaScript errors:
```javascript
// Run this in the console:
console.log($('#content-curriculum').hasClass('hidden'));
// Should return: false (if tab is visible)

// If it returns true, manually show it:
$('#content-curriculum').removeClass('hidden');
lucide.createIcons();
```

**Check 3: Force Tab Switch**
Run this in browser console:
```javascript
switchTab('curriculum');
```

### 5. Visual Guide

```
┌─────────────────────────────────────────────────────┐
│ Edit Course                         Back to Courses │
├─────────────────────────────────────────────────────┤
│ 📋 Course Details  │  📚 Curriculum  ← CLICK HERE   │
│    (blue line)     │    (gray)                       │
├─────────────────────────────────────────────────────┤
│                                                       │
│  Course Curriculum                  [Add Section] ← HERE
│  Organize your course...                            │
│                                                       │
│  ┌──────────────────────────────────────────────┐  │
│  │ No sections yet. Add your first section...   │  │
│  └──────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

## Still Having Issues?

### Debug Checklist:
- [ ] jQuery is loaded (check console: `typeof jQuery`)
- [ ] Lucide icons are loaded (check console: `typeof lucide`)
- [ ] No JavaScript errors in console (F12 → Console tab)
- [ ] You're on the edit page, not create page
- [ ] You clicked the "Curriculum" tab (second tab)

### Emergency Fix - Run in Console:
```javascript
// Show curriculum tab
$('.tab-content').addClass('hidden');
$('#content-curriculum').removeClass('hidden');

// Update tab styling
$('.tab-button').removeClass('active border-cyan-500 text-cyan-600').addClass('border-transparent text-gray-500');
$('#tab-curriculum').removeClass('border-transparent text-gray-500').addClass('active border-cyan-500 text-cyan-600');

// Refresh icons
lucide.createIcons();
```

### Test the Modal:
```javascript
// This should open the Add Section modal
showAddSectionModal();
```

## Once You Can See the Add Section Button:

1. Click "Add Section" button
2. Fill in:
   - Section Title (required)
   - Description (optional)
   - Order Index (defaults to 1)
3. Click "Add Section" in the modal
4. Page will reload with your new section

## Adding Lectures:

1. Find your section in the Curriculum tab
2. Click the **chevron-down icon** (▼) to expand the section
3. Click "+ Add Lecture" link
4. Fill in lecture details
5. Click "Add Lecture"

## Common URLs:

- **All Courses**: `http://localhost:8080/auth/courses/courses`
- **Edit Course**: `http://localhost:8080/auth/courses/edit/1` (change 1 to your course ID)
- **Course Dashboard**: `http://localhost:8080/auth/courses`

## Need More Help?

Check the full testing guide: [COURSE_MANAGEMENT_TESTING.md](COURSE_MANAGEMENT_TESTING.md)
