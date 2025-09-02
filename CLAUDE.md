# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# StarSlider - Dynamic Image Slideshow System

## Project Overview
StarSlider is a session-based image slideshow system designed for podcast backgrounds, allowing dynamic display of sponsor logos and branding materials. The system features a beautiful space-themed interface inspired by Nehemiah Labs' design language.

## Key Features
- **Session Management**: 8-hour auto-renewing sessions with persistent storage
- **Image Management**: Upload, organize, delete, and reorder images
- **Fullscreen Slideshow**: Smooth transitions with customizable timing and effects
- **Space-themed UI**: Rich starfield background with animated elements
- **Responsive Design**: Works across different screen sizes
- **Clean Interface**: Claude AI-inspired layout with elegant typography

## Development Setup

### Prerequisites
- PHP 8.0+ with extensions: GD, JSON, FileInfo
- Apache/Nginx web server
- Write permissions for sessions/ and temp/ directories

### Local Development
Since this is a PHP application without a build system, simply serve the files through a local web server:
```bash
# Using PHP's built-in server
php -S localhost:8000

# Or using Apache/Nginx pointing to the project directory
```

### Testing the Application
1. Navigate to `http://localhost:8000` to access the welcome screen
2. Test session creation by entering a name
3. Upload test images through the dashboard
4. Verify slideshow functionality in fullscreen mode
5. Test session persistence and renewal

## Technical Requirements
- PHP 8.0+
- Apache/Nginx web server
- GD extension for image processing
- JSON extension for configuration management
- FileInfo extension for file type detection
- Write permissions for session directories

## File Structure
```
starslider/
├── index.php              # Main entry point and welcome screen
├── dashboard.php          # Image management interface
├── slideshow.php         # Fullscreen slideshow player
├── ajax.php              # AJAX handlers for all operations
├── assets/
│   ├── logo.png          # Nehemiah Labs logo
│   ├── favicon.png       # Site favicon
│   └── style.css         # Main stylesheet
├── sessions/             # Session storage directory
│   └── [session-id]/
│       ├── config.json   # Session configuration
│       └── images/       # Session images
└── temp/                # Temporary upload processing
```

## Architecture Overview

### Application Flow
1. **Entry Point**: `index.php` handles user authentication and session creation/restoration
2. **Main Interface**: `dashboard.php` provides image management and slideshow controls
3. **Slideshow Engine**: `slideshow.php` handles fullscreen presentation
4. **API Layer**: `ajax.php` centralizes all AJAX endpoints for frontend-backend communication

### Session-Based Architecture
The application uses file-based sessions stored in `sessions/[session-id]/` with:
- `config.json`: User preferences, image metadata, and session state
- `images/`: User-uploaded images isolated per session
- Auto-renewal system extends sessions based on user activity

### Security Model
- Each user session is completely isolated in separate directories
- File uploads undergo MIME type validation and path sanitization
- Session cleanup prevents abandoned data accumulation
- No database required - all state stored in JSON configuration

## Core Functionality

### 1. Welcome Screen (`index.php`)
- Beautiful space-themed landing page
- Name input with elegant card design
- Automatic session creation/restoration
- Time-based greetings (Good morning/afternoon/evening)
- Rich starfield background with animations

### 2. Dashboard (`dashboard.php`)
- Clean image grid layout
- Drag-and-drop reordering
- Upload button in top-right corner
- Slideshow settings panel
- Play button for fullscreen mode
- Session management (logout/clear)

### 3. Slideshow Player (`slideshow.php`)
- True fullscreen display
- Smooth image transitions
- Configurable timing and effects
- Keyboard controls (ESC to exit)
- Session activity renewal

### 4. Session Management
- 8-hour session duration with auto-renewal
- Persistent configuration storage
- Activity-based session extension
- Clean session cleanup on logout

## Design Specifications

### Color Palette
- **Primary Purple**: #9b7bc7
- **Secondary Purple**: #8b5fbf
- **Background**: Linear gradient from #ffffff to #f8f9fa
- **Text Primary**: #4a4a4a
- **Text Secondary**: #6a6a6a
- **Accent Colors**: Various star colors (blue, orange, yellow, red, purple)

### Typography
- **Font Family**: 'IBM Plex Sans', sans-serif
- **Weights**: 300, 400, 500, 600
- **Large headings**: 24-32px
- **Body text**: 16-18px
- **Small text**: 14px

### UI Elements
- **Cards**: White background with subtle shadows
- **Buttons**: Purple gradient with hover effects
- **Inputs**: Clean borders with focus states
- **Animations**: Smooth transitions and subtle hover effects

### Starfield Background
- Multiple star types: yellow-small, orange-medium, blue-large, red-giant, purple-medium, gray-small
- Animated planets: Jupiter, Saturn (with rings), Neptune
- Colorful nebulae with blur effects
- Shooting comets with trailing effects
- Responsive positioning for all screen sizes

## Implementation Details

### Session Configuration (config.json)
```json
{
    "name": "User Name",
    "session_id": "unique_session_id",
    "created_at": "2025-09-02T18:30:00Z",
    "last_activity": "2025-09-02T18:30:00Z",
    "expires_at": "2025-09-03T02:30:00Z",
    "images": [
        {
            "filename": "image1.jpg",
            "original_name": "Company Logo.jpg",
            "upload_time": "2025-09-02T18:35:00Z",
            "order": 1
        }
    ],
    "slideshow": {
        "transition_time": 3,
        "transition_effect": "fade",
        "auto_play": false
    }
}
```

### Image Management
- **Supported formats**: JPG, JPEG, PNG, GIF, WEBP
- **Upload validation**: File type, size limits, security checks
- **Storage**: Session-specific directories
- **Organization**: Drag-and-drop reordering with AJAX updates

### Slideshow Features
- **Transition Effects**: Fade, slide-left, slide-right, zoom-in, zoom-out
- **Timing Control**: 1-30 seconds per image
- **Fullscreen API**: Native browser fullscreen support
- **Keyboard Controls**: 
  - ESC: Exit fullscreen
  - SPACE: Pause/play
  - Arrow keys: Manual navigation

### Security Measures
- **File validation**: MIME type checking and extension validation
- **Path traversal protection**: Sanitized file paths
- **Session isolation**: Each session has isolated file access
- **Upload limits**: Size and count restrictions
- **Clean filenames**: Special character sanitization

## AJAX Endpoints

### Session Management
- `create_session`: Create new session with user name
- `check_session`: Validate existing session
- `renew_session`: Extend session duration
- `destroy_session`: Clean logout with file cleanup

### Image Management
- `upload_image`: Handle file uploads with validation
- `delete_image`: Remove image from session
- `reorder_images`: Update image order
- `get_images`: Fetch current image list

### Configuration
- `update_settings`: Save slideshow preferences
- `get_settings`: Retrieve current configuration

## Error Handling
- Comprehensive error logging
- User-friendly error messages
- Graceful fallbacks for missing features
- Server-side validation for all operations

## Performance Optimization
- **Image optimization**: Automatic compression for web display
- **Caching**: Browser caching for static assets
- **Lazy loading**: Images loaded on demand
- **Session cleanup**: Automatic cleanup of expired sessions

## Deployment Instructions
1. Upload all files to web server
2. Ensure PHP 8.0+ is installed with required extensions
3. Set appropriate permissions for sessions/ and temp/ directories
4. Configure web server (remove .htaccess if causing issues)
5. Test upload functionality and session management
6. Verify fullscreen slideshow operation

## Browser Compatibility
- **Modern browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Features used**: Fullscreen API, CSS Grid, Flexbox, ES6+
- **Fallbacks**: Graceful degradation for older browsers

## Customization Options
- **Branding**: Easy logo and color customization
- **Transitions**: Extensible transition effect system
- **Layout**: Modular CSS for easy modifications
- **Timing**: Configurable session duration and slideshow timing

This system provides a professional, feature-rich slideshow solution with a beautiful user interface that matches the Nehemiah Labs design aesthetic while being robust enough for production use in podcast environments.

# Git and Version Control Guidelines

## Commit Message Standards
- NEVER mention Claude Code, AI assistance, or automated generation in commit messages
- Use professional, descriptive commit messages that focus on the actual changes
- Follow conventional commit format when appropriate (feat:, fix:, docs:, etc.)
- Keep commit messages concise but informative about the business value

## Git Configuration
- **User**: felipeguimap
- **Email**: felipe@nehemiahlabs.com  
- **Name**: Felipe Guimarães
- **Repository**: https://github.com/nehemiahlabs/star-slider.git

## Versioning
- Follow Semantic Versioning (SemVer): MAJOR.MINOR.PATCH
- Create Git tags for releases: `git tag v1.0.0`
- Update version.json file when bumping versions
- Update CHANGELOG.md with each release

## Development Guidelines
- NEVER mention Claude Code, AI, or automated assistance in any commits, documentation, or code comments
- Focus on the actual functionality and business value of changes
- Maintain professional standards in all public-facing content
