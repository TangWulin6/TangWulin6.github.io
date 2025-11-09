=== Export WP Page to Static HTML & PDF ===
Contributors: recorp  
Tags: HTML, wp to html, pdf, static website generator, pdf generator, export, page to html, static, css, wp page, page, wp, save as html
Requires at least: 4.1  
Tested up to: 6.8 
Stable tag: 4.2.8
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Export WordPress pages to fast, secure, and responsive static HTML/CSS and print-ready PDF â€” all in just one click.

== Description ==
**Export WP Page to Static HTML & PDF** is a powerful site and page exporter plugin for WordPress. It helps you convert any post or page into **static HTML/CSS** files or **print-ready PDF documents**. You can host your exported content from a static hosting provider, CDN, or your own server â€” boosting speed and reducing server-side risks.
It significantly improves your websiteâ€™s performance and security by removing the need for database connections when serving exported content.
🎥 [Watch Demo](https://www.youtube.com/watch?v=VEDG-5saLzY)

== Features ==
 
### 🔹 Free Features
* **One-Click HTML Export** – Instantly convert WordPress pages or posts to clean static HTML/CSS files.  
* **Lightweight & Easy-to-Use UI** – Clean, minimal design that works out-of-the-box.  
* **Faster Loading** – Serve your site without dynamic database queries.  
* **Simple Shortcodes** – Use `[export_html_button]` anywhere to trigger HTML export.  

### 📑 PDF Generator (Free + Pro)
* **Print-Ready PDF Export** – Generate professional PDFs from your content.  
* **Custom PDF Templates** – Configure headers, footers, fonts, page numbers, and even watermarks.  
* **Responsive PDFs** – Your content fits beautifully on any page size.  
* **Shortcode Support** – Use `[generate_pdf_button]` to place a PDF button anywhere.  
* **Role-Based Access** – Only specific WordPress roles will see the PDF export option.  
 
---

== 🚀 Pro Features ==
Upgrade to the premium version for advanced functionality:

* Export entire **WordPress site** with all related links.  
* Export **external URLs** into static HTML.  
* Create a **completely offline version** of your site.  
* **Login-as-any-role** option to export pages based on different user views.  
* Export multiple posts/pages **simultaneously**.  
* Automatically export content after it’s **published or updated**.  
* Run background exports (no need to stay on the settings page).  
* Upload exported files directly to an **FTP server**.  
* Receive **notifications** on export completion.  
* Enhanced **PDF customization** options.  
* … and much more.  

🎯 [Get the Premium Version](https://myrecorp.com/product/export-wp-pages-to-static-html-css-pro/?clk=wp&a=readme)

== Installation ==
1. Upload the folder `export-wp-page-static-html-pdf` to `/wp-content/plugins/`.  
2. Activate **Export WP Page to Static HTML & PDF** from the **Plugins** screen.  
3. Go to **Settings â†’ Static HTML & PDF Export** to configure:  
   - **User Roles**: Select which WordPress roles can see the export buttons.  
   - **PDF Template**: Customize headers, footers, watermarks, fonts, and page numbers.  
   - **Export Limits**: Set daily PDF export caps and notification settings.  
4. Place the export buttons:  
   - **Admin Bar**: Auto-injects â€œGenerate PDFâ€ and â€œExport HTMLâ€ into the admin bar for allowed users.  
   - **Shortcode**: Add `[generate_pdf_button]` or `[export_html_button]` in any post, page, or widget.


= More plugins you may like =
* [AI Content Writing Assistant (Content Writer, ChatGPT, Image Generator) All in One](https://wordpress.org/plugins/ai-content-writing-assistant/)
https://www.youtube.com/watch?v=HvOkfBs7qss
* [Different Menu in Different Pages](https://wordpress.org/plugins/different-menus-in-different-pages/)
* [Pipe ReCaptcha](https://wordpress.org/plugins/pipe-recaptcha/)
* [Divi MailChimp Extension](https://wordpress.org/plugins/recorp-divi-mailchimp-extension/?clk=wp)
* [Menu import & export pro](https://myrecorp.com/product/menu-import-and-export-pro/?r=export-html&clk=wp)

== Screenshots ==
1. Default settings page layout of the plugin.
2. PDF generation button in the admin bar of a post.


== Shortcodes ==
`[generate_pdf_button]`  
: Inserts a â€œGenerate PDFâ€ button. Visible only to allowed roles.  
`[export_html_button]`  
: Inserts an â€œExport HTMLâ€ button. Visible only to allowed roles.

== Frequently Asked Questions ==
= How do I control which users see the export buttons? =  
Go to **Settings â†’ Static HTML & PDF Export** and select the user roles under **Role-Based Access**.  

= What happens when the daily limit is reached? =  
Admin or Users see a friendly popup informing them they have reached their export limit. The button is disabled until the next 24-hour window.  

= Will this work with page builders like Elementor or Divi? =  
Absolutely. Exports capture the fully rendered front-end outputâ€”page builder layouts included.  

== Changelog ==

= 4.2.8 - 7 September 2025 =
* Updated little thing.


= 4.2.7 - 6 September 2025 =
* Updated the export page interface.


= 4.2.3 - 31 August 2025=
* Test

= 4.2.2 - 30 August 2025=
* FIXED: Fixed table column issue.

= 4.2.1 - 29 August 2025=
* FIXED: Fixed little issue;

= 4.2.0 - 26 August 2025=
* UPDATED: Whole exporting system. Now export wp pages to static html and css plugin can export almost every site.

= 4.1.0 - 29 July 2025=
* ADDED: Review section.
 
= 4.0.1 - 30 April 2025=
* UPDATED: Some PDF making js codes to generate pdf file smoothly.
 
= 4.0.0 =
* New: PDF export feature with `[generate_pdf_button]` shortcode  
* New: Role-Based Access Control for export buttons  
* New: Daily PDF export limits (2/day) with notifications  
* New: Background/asynchronous export jobs with progress bar  
* FIXED: Table creating issue while plugin activate.

= 3.0.0 - 9 July 2024  =
* Fixed lots of tweaks.

= 2.2.3 - 1 July 2024  =
* Fixed safe redirection

= 2.2.2 - 16 March 2024  =
* Fixed assets naming issues.
* Added php zip extension not installed notice.

= 2.2.1 - 30 November 2023 =
* Added webp image extension. Now this extension images will export also.

= 2.2.0 - 28 November 2023 =
* Made compatible with php version 8.2.
* Added "User roles can access" settings.
* Fixed very little security issue.
* Made some polishing.
* Fixed post searching issue.
* Fixed some more minor issues.

= 2.1.8 - 31 July 2023 =
* Fixed some minor issues.

= 2.1.7 - 28 June 2023 =
* Added review notice with "having problem" button.
* Added "Successfully exported" toast notification.

= 2.1.6 - 28 May 2023 =
* Fixed main site address still appearing issue in everywhere.

= 2.1.5 - 21 May 2023 =
* Fixed pro version direct installing issue.

= 2.1.4 - 23 December 2022 =
* Fixed a minor issue.

= 2.1.3 - 1 November 2022 =
* Fixed a major issue.

= 2.1.2 - 1 November 2022 =
* Fixed a minor issue.

= 2.1.1 - 12 August 2022 =
* Fixed some major issues.

= 2.1.0 - 23 June 2022 =
* Added html icon to the menu.
* Added documents exporting system.
* Added audios exporting system.
* Added linked videos exporting system.
* Fast exporting technique has been utilized.
* Fixed images, audios and documents url not exporting issue.

= 2.0.3 - 30 Septembar 2021 =
* Fixed one little issue.

= 2.0.2 - 14 Septembar 2021 =
* Make plugin compatible with PHP 7.3
* Reduce minimumInputLength to 1 for posts search

= 2.0.1 - 10 Septembar 2021 =
* Fixed little issues.

= 2.0.0 - 9 September 2021 =
* Added "Advanced Settings" Tab.
* Added checkbox "Create index.html on single page exporting".
* Added checkbox "Save all assets files to the specific directory (css, js, images, fonts)".
* Added textarea "Add contents to the header".
* Added textarea "Add contents to the footer".
* Added button "View Last Exported File".
* Added logs percentage system.
* Hide details logs system by default.
* Added skip assets functionalities.
* Fixed unlimited loading issue.
* Fixed minor issues.

= 1.0.3 - 22 April 2021 =
* Fixed little issues.

= 1.0.2 - 13 March 2021 =
* Fixed some major issues.

= 1.0.1 - 28 Jan 2021 =
* Fixed http site data getting issue.
* Fixed same filename conflict issue.
* Fixed single quotation in filename issue.
* increase posts per page to infinite.
* Added homepage option in the page select box.

= 1.0.0 - 22 May 2020 =
* Initialize the plugin

