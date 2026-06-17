# Dunedin Admin Implementation Plan

## Overview
Create a DunedinAdmin class and admin.php entry point to provide an API explorer and CRUD management interface for the API, preventing the "not yet implemented" errors from becoming a blocker during development.

## Information Gathered

### Frontend Colors (from /mnt/Rincewind/dunedin-ai/app/src/style.css)
- **Primary Color**: `#5a2672` (purple)
- **Secondary Color**: `#24673b` (green)
- **Background**: `#fafaf7` (light beige)
- **Surface**: `#ffffff` (white)
- **Text**: `#111111` (dark)
- **Border**: `#e5e3dc` (light gray)

### Frontend Logo
- Location: `/mnt/Rincewind/dunedin-ai/app/src/assets/logo.svg`
- An abstract dunedin design in purple and green

### Existing Auth Pattern
- File: `/mnt/Rincewind/dunedin-ai/api/src/public/magrathea.php`
- Uses `AdminManager::Instance()->StartDefault()` for admin auth
- Admin authentication is built into Magrathea2 framework

### Example Implementation
From guialol project - pattern to follow:
```
class GuiaLolAdmin extends \Magrathea2\Admin\Admin implements \Magrathea2\Admin\iAdmin {
    public function Initialize() { /* Set title, colors, logo */ }
    public function Auth($user): bool { /* Auth check */ }
    public function SetFeatures() { /* Load all features */ }
    public function LoadApi() { /* ApiExplorer */ }
    public function LoadConfig() { /* App settings */ }
    public function LoadFeatures() { /* CRUD features */ }
    public function BuildMenu(): AdminMenu { /* Menu structure */ }
}
```

## Files to Create

### 1. `/mnt/Rincewind/dunedin-ai/api/src/admin/DunedinAdmin.php`
Main admin class extending `\Magrathea2\Admin\Admin`
- Initialize() - set title, colors, logo
- Auth() - uses default Magrathea auth
- SetFeatures() - load all feature modules
- LoadApi() - instantiate ApiExplorer with the existing API
- LoadConfig() - settings panel
- LoadFeatures() - load all CRUD admin classes
- BuildMenu() - organize menu structure

### 2. CRUD Admin Classes (in `/mnt/Rincewind/dunedin-ai/api/src/admin/`)

#### `/api/src/admin/UserAdmin.php`
- Extends `AdminCrudObject`
- Model: User
- Fields: firebase_uid, email, display_name, photo_url, active, status
- Read-only views with delete capability
- No password management (Firebase managed)

#### `/api/src/admin/HighlightAdmin.php`
- Extends `AdminCrudObject`
- Model: Highlight
- Fields: user_id, text, origin, author, date, hash
- Read-only (highlights auto-created from file imports)
- Can view associated notes inline

#### `/api/src/admin/NoteAdmin.php`
- Extends `AdminCrudObject`
- Model: Note
- Fields: highlight_id, user_id, note, date
- Can edit note text, delete notes

#### `/api/src/admin/FileAdmin.php`
- Extends `AdminCrudObject`
- Model: File
- Fields: user_id, filename, type, status, imported_date
- Read-only for most fields
- Show file path for reference
- Can trigger manual import (status update)

### 3. `/mnt/Rincewind/dunedin-ai/api/src/public/admin.php`
Entry point file
- Similar to api.php but returns admin instance
- Requires vendor/autoload.php
- Loads MagratheaPHP with app paths
- Returns initialized DunedinAdmin instance

## Implementation Steps

1. **Create admin directory structure**
   - `/api/src/admin/` folder (if not exists)

2. **Copy frontend logo to admin assets**
   - `/api/src/admin/logo.svg` (copy from app/src/assets/logo.svg)

3. **Create DunedinAdmin class**
   - Initialize with title "Dunedin Admin"
   - Set primary color: #5a2672 (purple)
   - Set secondary color: #24673b (green)
   - Set logo path
   - Configure features

4. **Create CRUD Admin classes**
   - One for each model (User, Highlight, Note, File)
   - Configure fields, permissions, display

5. **Create admin.php entry point**
   - Bootstrap Magrathea2
   - Instantiate and run DunedinAdmin

6. **Test**
   - Access admin panel via admin.php
   - Verify API Explorer shows all endpoints
   - Test CRUD operations (if allowed)
   - Verify authentication

## Special Considerations

### Authentication
- Use default Magrathea2 Admin authentication
- Same security model as existing magrathea.php
- No additional auth changes needed

### API Explorer
- Will automatically display all endpoints from api.php
- Handles auth token validation
- No additional configuration needed

### CRUD Permissions
- Make most fields read-only (especially auto-generated ones)
- Allow deletion where safe
- For File model, consider status updates for import triggers
- Highlight/Note - consider read-only (user-generated data)

### Color Scheme
- Primary: #5a2672 (purple from logo)
- Should match frontend design
- Improves UX consistency

## Current API Endpoints (for reference)
```
GET    /api/v1/health
GET    /api/v1/me
POST   /api/v1/auth/login
GET    /api/v1/highlights
GET    /api/v1/highlights/:id
DELETE /api/v1/highlights/:id
POST   /api/v1/highlights/:id/notes
PUT    /api/v1/notes/:id
DELETE /api/v1/notes/:id
POST   /api/v1/files/upload
GET    /api/v1/files
POST   /api/v1/files/:id/import  <-- Currently returns 501
```

The API Explorer will display all of these automatically.
