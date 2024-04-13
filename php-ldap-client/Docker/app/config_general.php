<?php
$mattermost_host      = getenv('MATTERMOST_URL') ?: "https://localhost";
$html_page_title      = getenv('HTML_PAGES_TITLE') ?: "Your Company Name";
$footer_text          = getenv('FOOTER_TEXT') ?: "Copyright Your Company Name 2024";
$company_logo_url     = getenv('COMPANY_LOGO_URL') ?: "./images/company_logo.png";
