PHP Image Upload and Gallery System
A simple PHP-based web application for uploading images to a GitHub repository and displaying them in a gallery. The system supports secure file uploads with CSRF protection, Cloudflare Turnstile for bot prevention, and randomized filenames to handle special characters (e.g., Chinese). It supports JPEG, PNG, GIF, and ICO file formats.
Features

Image Upload: Upload images via a user-friendly form with client-side and server-side validation.
GitHub Storage: Store images in a GitHub repository using the GitHub API.
Gallery Display: View uploaded images in a responsive grid with direct URLs.
Security:
CSRF protection to prevent unauthorized requests.
Cloudflare Turnstile for bot prevention.
Randomized filenames (timestamp + 8-character random string) to avoid encoding issues.
XSS prevention with htmlspecialchars.


File Support: Supports JPEG, PNG, GIF, and ICO files (max 10MB).
Responsive Design: Built with Tailwind CSS for a modern, mobile-friendly UI.

Prerequisites

PHP: Version 7.4 or higher with curl and session extensions enabled.
Web Server: Apache, Nginx, or any PHP-compatible server.
GitHub Account: A repository and personal access token for API access.
Cloudflare Turnstile: Site Key and Secret Key for bot protection.
Composer (optional): For dependency management if extended.

Installation

Clone the Repository:
git clone https://github.com/your-username/php-image-gallery.git
cd php-image-gallery


Configure the Environment:

Copy config.example.php to config.php and update with your credentials:
<?php
return [
    'GITHUB_TOKEN' => 'your_github_personal_access_token',
    'GITHUB_REPO' => 'your_repository_name',
    'GITHUB_OWNER' => 'your_github_username',
    'GITHUB_BRANCH' => 'main',
    'CUSTOM_DOMAIN' => 'https://your-custom-domain.com',
    'CLOUDFLARE_TURNSTILE_SITE_KEY' => 'your_turnstile_site_key',
    'CLOUDFLARE_TURNSTILE_SECRET_KEY' => 'your_turnstile_secret_key'
];


Ensure the GitHub token has repo scope.

Create an images directory in your GitHub repository.



Set Up the Web Server:

Place the project in your web server's root directory (e.g., /var/www/html).
Ensure the server has write access to PHP's temporary directory (sys_get_temp_dir()).
Configure the server to handle PHP files (e.g., enable mod_php for Apache).


Verify Dependencies:

The project uses Tailwind CSS (CDN) and Cloudflare Turnstile (CDN), requiring no local installation.
Ensure curl is enabled in PHP (php.ini).



Usage

Access the Application:

Open http://your-domain.com/index.php in a browser.
The homepage displays an upload form.


Upload an Image:

Select a JPEG, PNG, GIF, or ICO file (max 10MB).
Complete the Cloudflare Turnstile verification.
Submit the form to upload the image.
On success, a direct URL to the image is displayed.


View the Gallery:

Navigate to gallery.php via the link on the homepage.
Browse uploaded images in a responsive grid, with filenames and direct URLs.



Configuration

config.php:
GITHUB_TOKEN: GitHub personal access token (generate at GitHub Settings > Developer Settings).
GITHUB_REPO, GITHUB_OWNER, GITHUB_BRANCH: Your repository details.
CUSTOM_DOMAIN: Base URL for direct image links (e.g., a CDN or raw GitHub URL).
CLOUDFLARE_TURNSTILE_SITE_KEY, CLOUDFLARE_TURNSTILE_SECRET_KEY: Obtain from Cloudflare Turnstile dashboard.


File Size Limit: Default 10MB, adjustable in upload.php ($max_size).
Allowed File Types: JPEG, PNG, GIF, ICO, modifiable in upload.php ($allowed_types).

Security Features

CSRF Protection: Each form submission includes a unique token, validated server-side.
Cloudflare Turnstile: Prevents automated uploads by bots.
Randomized Filenames: Avoids issues with special characters (e.g., Chinese) using timestamp_randomstring.extension.
XSS Prevention: All outputs are escaped with htmlspecialchars.
Secure File Handling: Uses move_uploaded_file to prevent unauthorized file access.

Testing

Upload Tests:
Upload various file types (JPEG, PNG, GIF, ICO) and sizes.
Test with special characters in filenames (e.g., Chinese).
Verify direct URLs work and images display in gallery.php.


Security Tests:
Submit a form with an invalid CSRF token; expect “CSRF 验证失败”.
Skip Cloudflare Turnstile; expect “请完成人机验证”.
Attempt to upload an invalid file type or size; check error messages.


UI Tests:
Confirm direct URLs in index.php wrap correctly (no overflow).
Test responsiveness on mobile and desktop devices.


GitHub API Tests:
Simulate API failures (e.g., invalid token) and verify error handling.
Ensure the images directory exists in the repository.



Troubleshooting

Upload Fails:
Check config.php for correct GitHub and Cloudflare credentials.
Verify the GitHub token has repo scope and the images directory exists.
Ensure PHP curl is enabled and the server can reach GitHub/Cloudflare APIs.


Direct URLs Broken:
Confirm CUSTOM_DOMAIN is correct and points to your repository’s raw file URL.


CSRF/Turnstile Errors:
Clear browser cookies or start a new session.
Verify Turnstile keys and network connectivity to Cloudflare.



Contributing

Fork the repository.
Create a feature branch (git checkout -b feature/your-feature).
Commit changes (git commit -m "Add your feature").
Push to the branch (git push origin feature/your-feature).
Open a pull request with a detailed description.

License
MIT License. See LICENSE for details.
Acknowledgments

Built with PHP, Tailwind CSS, and Cloudflare Turnstile.
Uses the GitHub API for storage.
