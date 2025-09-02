# StarSlider

A dynamic image slideshow system designed for professional presentations with a beautiful space-themed interface. Perfect for podcasts, conferences, and live streaming backgrounds.

**🌐 Live Demo:** [https://nehemiahlabs.com/tools/star-slider](https://nehemiahlabs.com/tools/star-slider)

## 🌟 Features

- **Session Management**: 8-hour auto-renewing sessions with persistent storage
- **Image Management**: Upload, organize, delete, and reorder images with drag-and-drop
- **Fullscreen Slideshow**: Smooth transitions with customizable timing and effects
- **Space-themed UI**: Rich starfield background with animated celestial objects
- **Dynamic Greetings**: Time-based welcome messages with animated word rotation
- **Modern Modal System**: Professional settings interface with glassmorphism effects
- **Responsive Design**: Works across different screen sizes and devices
- **Clean Interface**: Modern design with elegant typography and smooth animations
- **Version Management**: Built-in versioning system following SemVer standards

## 🚀 Quick Start

### Prerequisites

- PHP 8.0+ with extensions:
  - GD (image processing)
  - JSON (configuration management)
  - FileInfo (file type detection)
- Apache/Nginx web server
- Write permissions for `sessions/` and `temp/` directories

### Installation

1. **Clone or download** the StarSlider files to your web server directory

2. **Set permissions** for session storage:
   ```bash
   chmod 755 sessions/
   chmod 755 temp/
   ```

3. **Configure web server** to serve PHP files (remove `.htaccess` if causing issues)

4. **Access the application** via your web browser:
   ```
   http://your-domain.com/starslider/
   ```

## 📖 Usage

### Getting Started

1. **Enter your name** on the welcome screen to create a session
2. **Upload images** using the upload button or drag-and-drop
3. **Reorder images** by dragging them to your preferred sequence
4. **Configure slideshow** settings (timing, effects, auto-play)
5. **Start the show** in fullscreen mode

### Session Management

- Sessions automatically expire after 8 hours of inactivity
- Session data is stored locally in the `sessions/` directory
- Each session is completely isolated from others
- Sessions can be renewed by user activity

### Slideshow Features

- **Transition Effects**: Fade, slide-left, slide-right, zoom-in, zoom-out
- **Timing Control**: 1-30 seconds per image
- **Auto-play**: Optional automatic progression
- **Progress Bar**: Optional progress indicator
- **Keyboard Controls**: ESC to exit, SPACE to pause/play, arrow keys for navigation

## 🎨 Design

StarSlider features a stunning space-themed interface with:

- **Dynamic Starfield**: Animated stars, planets, and nebulae
- **Floating UI Elements**: Modern glassmorphism design
- **Responsive Layout**: Adapts to different screen sizes
- **Elegant Typography**: IBM Plex Sans font family
- **Smooth Animations**: Subtle transitions and effects

## 📁 File Structure

```
starslider/
├── index.php              # Welcome screen and session management
├── dashboard.php          # Image management interface
├── slideshow.php          # Fullscreen slideshow player
├── ajax.php               # AJAX API endpoints
├── version.json           # Version information
├── assets/
│   ├── logo.png          # Main logo
│   ├── logo_pb.png       # Black & white logo variant
│   ├── favicon.png       # Site favicon
│   └── style.css         # Main stylesheet
├── sessions/             # User session storage (auto-created)
│   └── [session-id]/
│       ├── config.json   # Session configuration
│       └── images/       # User images
└── temp/                # Temporary file processing (auto-created)
```

## 🔧 Configuration

### Session Settings

Sessions are configured via `config.json` in each session directory:

```json
{
  "name": "User Name",
  "session_id": "unique_session_id",
  "created_at": "2025-01-02T19:30:00Z",
  "last_activity": "2025-01-02T19:30:00Z",
  "expires_at": "2025-01-03T03:30:00Z",
  "images": [...],
  "slideshow": {
    "transition_time": 3,
    "transition_effect": "fade",
    "auto_play": true,
    "show_progress_bar": false
  }
}
```

### Supported Image Formats

- JPEG/JPG
- PNG
- GIF
- WEBP

## 🛡️ Security

- **Session Isolation**: Each session has completely isolated file access
- **File Validation**: MIME type checking and extension validation
- **Path Sanitization**: Protection against directory traversal attacks
- **Upload Limits**: Configurable file size and count restrictions
- **Clean Filenames**: Special character sanitization

## 🌐 Browser Compatibility

- **Modern Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Features**: Fullscreen API, CSS Grid, Flexbox, ES6+
- **Mobile**: Responsive design works on tablets and mobile devices

## 🐛 Troubleshooting

### Common Issues

**Images not uploading:**
- Check file permissions on `sessions/` and `temp/` directories
- Verify PHP file upload limits in `php.ini`
- Ensure GD extension is installed

**Session expired errors:**
- Check server timezone configuration
- Verify write permissions on session directories

**Slideshow not working:**
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify Fullscreen API support

## 📄 License

Copyright © 2025 Nehemiah Labs. All rights reserved.

## 🤝 Support

For support and bug reports, please contact the development team or check the project documentation.

---

**StarSlider** - Making professional presentations beautiful, one slide at a time. ✨