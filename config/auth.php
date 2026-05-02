<?php
define('GOOGLE_AUTH_URL',  'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v3/userinfo');
define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google/callback');
define('GOOGLE_SCOPE', 'openid email profile');
define('ALLOWED_DOMAIN', 'groupe-speed.cloud');
