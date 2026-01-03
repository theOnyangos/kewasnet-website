# Course Management Testing Guide

## Prerequisites
Before testing, ensure:
1. You're logged into the admin panel at `auth/courses`
2. You have at least one course in the database
3. Categories exist in either `course_categories` or `blog_categories` table

## Step-by-Step Testing

### 1. Access Course Edit Page
```
URL: http://localhost:8080/auth/courses/edit/{course_id}
Example: http://localhost:8080/auth/courses/edit/1
```

**Expected Result:**
- Page loads with two tabs: "Course Details" and "Curriculum"
- Course information is pre-filled in the form
- If sections exist, they appear in the Curriculum tab

### 2. Edit Course Details (Tab 1)
1. Click on "Course Details" tab
2. Modify any field (title, summary, price, etc.)
3. Click "Update Course" button
4. **Expected**: Success message and page reloads with updated data

### 3. Add a Section (Tab 2)
1. Click on "Curriculum" tab
2. Click "Add Section" button (blue gradient button)
3. Fill in the modal:
   - Section Title: "Introduction" (required)
   - Description: "Getting started with the course"
   - Order Index: 1
4. Click "Add Section"
5. **Expected**:
   - Success alert message
   - Page reloads
   - New section appears in the curriculum list

### 4. Edit a Section
1. In Curriculum tab, find your section
2. Click the pencil/edit icon next to the section
3. Modal opens with pre-filled data
4. Modify the title or description
5. Click "Update Section"
6. **Expected**: Success message and section updates

### 5. Add a Lecture to Section
1. In Curriculum tab, click the chevron-down icon to expand a section
2. Click "+ Add Lecture" link
3. Fill in the lecture modal:
   - Lecture Title: "Welcome Video" (required)
   - Description: "Course introduction"
   - Video URL: https://www.youtube.com/watch?v=example
   - Duration: 15 (minutes)
   - Order Index: 1
   - Check "Free Preview" if you want
4. Click "Add Lecture"
5. **Expected**:
   - Success message
   - Page reloads
   - Lecture appears under the section

### 6. Edit a Lecture
1. Expand a section that has lectures
2. Click the edit icon next to a lecture
3. Modal opens with pre-filled data
4. Modify any fields
5. Click "Update Lecture"
6. **Expected**: Success message and lecture updates

### 7. Delete a Lecture
1. Click the trash icon next to a lecture
2. Confirm deletion in the prompt
3. **Expected**: Success message and lecture is removed

### 8. Delete a Section
1. Click the trash icon next to a section
2. Confirm deletion
3. **Expected**:
   - Success message
   - Section and all its lectures are removed (soft delete)

## Troubleshooting

### Section/Lecture buttons not working
**Check browser console (F12) for errors:**
- Look for 404 errors (route not found)
- Check for CSRF token errors
- Verify JavaScript is loading correctly

### Modal not opening
**Possible issues:**
1. JavaScript not initialized - check if jQuery is loaded
2. Lucide icons not rendering - check network tab
3. Z-index issues - inspect the modal element

### AJAX requests failing
**Common causes:**
1. **CSRF Token**: Ensure CSRF protection is enabled in `app/Config/Security.php`
2. **Routes**: Verify routes exist in `app/Config/Routes.php`:
   ```php
   GET  auth/courses/sections/get/:id
   POST auth/courses/sections/create
   POST auth/courses/sections/update/:id
   POST auth/courses/sections/delete/:id
   GET  auth/courses/lectures/get/:id
   POST auth/courses/lectures/create
   POST auth/courses/lectures/update/:id
   POST auth/courses/lectures/delete/:id
   ```
3. **Authentication**: Ensure you're logged in and session is active

### Database Errors
**Check:**
1. All required tables exist:
   - `courses`
   - `course_sections`
   - `course_lectures`
   - `course_categories` or `blog_categories`
2. Tables have correct columns (check migrations)
3. Foreign key constraints are properly set

## Testing Checklist
- [ ] Can access course edit page
- [ ] Can edit course details
- [ ] Can add a section
- [ ] Can edit a section
- [ ] Can delete a section
- [ ] Can expand/collapse section lectures
- [ ] Can add a lecture
- [ ] Can edit a lecture
- [ ] Can delete a lecture
- [ ] Lecture count updates correctly
- [ ] Total duration calculates correctly
- [ ] Page reloads show updated data
- [ ] All modals open and close properly
- [ ] All AJAX calls return proper success/error messages

## Browser Console Commands for Debugging

```javascript
// Check if jQuery is loaded
typeof jQuery

// Check if course ID is available
console.log(courseId)

// Test section modal opening
showAddSectionModal()

// Test lecture modal for section 1
showAddLectureModal(1)

// Check if Lucide icons are initialized
lucide.createIcons()
```

## Quick Fixes

### If modals don't close
```javascript
// Run in console
$('#sectionModal').addClass('hidden');
$('#lectureModal').addClass('hidden');
```

### If forms don't submit
1. Check Network tab in DevTools
2. Look for the POST request
3. Check request payload
4. Check response (should be JSON with `success: true/false`)

### If icons don't show
```javascript
// Re-initialize Lucide icons
lucide.createIcons();
```

## Expected API Responses

### Successful Section Creation
```json
{
  "success": true,
  "message": "Section created successfully",
  "data": {
    "section_id": 123
  }
}
```

### Successful Lecture Update
```json
{
  "success": true,
  "message": "Lecture updated successfully"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Section not found"
}
```

## Support
If issues persist:
1. Check PHP error logs: `tail -f writable/logs/log-*.php`
2. Check database queries in profiler (if enabled)
3. Verify CodeIgniter environment is set to 'development' for detailed errors
