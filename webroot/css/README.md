# CSS File Documentation

This section provides details on the purpose and customization points for the CSS files within this project.

## Root Files (`webroot/css/`)

*   **`main.css`**
    *   **Purpose:** The main entry point for all custom application styles. It uses `@import` rules to include partials from the subdirectories in a specific order, defining the cascade.
    *   **Customization:** **Do not edit directly.** Modify the imported partial files instead. The order of imports matters; changes should generally be made within existing partials or by creating new partials and importing them in the appropriate section (e.g., a new component, a new page style).

*   **`fonts.css`**
    *   **Purpose:** Defines `@font-face` rules for loading custom web fonts (e.g., Raleway, cakefont).
    *   **Customization:** Add new `@font-face` rules here if you need to load additional custom fonts. Ensure font files are placed in the `webroot/font/` directory and paths in the `url()` are correct.

*   **`pages-text-override.css`**
    *   **Purpose:** Aggressively resets default browser/framework styles and applies Tailwind-inspired base styles. It is **only loaded** via the layout (`jenbury.php`) for specific static pages (About, Contact, FAQ) to provide a clean slate for potentially different styling on those pages.
    *   **Customization:** Modify with caution. Changes here will *only* affect the specific pages where it's loaded. If you need different base styles for those static pages, edit here. For site-wide base style changes, edit files in the `base/` directory.

## `base/` Directory

Contains foundational styles applied globally.

*   **`base/_variables.css`**
    *   **Purpose:** Defines primary CSS custom properties (variables) for colors, spacing, fonts, shadows, etc., used throughout the application (originally from `jenbury.css`).
    *   **Customization:** **Primary customization point for theming.** Modify the variable values here (e.g., `--jenbury-primary`, `--spacing-md`) to change the site's overall look and feel. Add new global variables here if needed. *Note:* Some page-specific variable files exist in `pages/` (`_courses-variables.css`, `_dashboard-variables.css`) which might override or supplement these; consider merging them here for consistency if desired.

*   **`base/_base.css`**
    *   **Purpose:** Basic styles for core HTML elements (`html`, `body`, headings `h1`-`h6`, links `a`, images `img`), including the global reset (`*`).
    *   **Customization:** Avoid editing unless necessary for fundamental changes to base element styling across the entire site. Prefer using variables defined in `_variables.css`.

*   **`base/_responsive.css`**
    *   **Purpose:** Contains all `@media` queries for responsive design adjustments across different screen sizes. Includes styles merged from `jenbury.css`, `courses.css`, and `admindashboard.css`.
    *   **Customization:** Add or modify styles within the existing `@media` blocks to adjust layout or appearance for specific breakpoints. Ensure selectors are specific enough to target the intended elements.

## `layout/` Directory

Contains styles for major site structure elements.

*   **`layout/_header.css`**
    *   **Purpose:** Styles the main site header, including logo, navigation (`.main-nav`, `.user-nav`), dropdown menus, and specific header buttons (`.login-button`, `.signup-button`). Includes overrides and customizations.
    *   **Customization:** Modify rules here to change header appearance or layout. Variables from `_variables.css` should be used where possible.

*   **`layout/_footer.css`**
    *   **Purpose:** Styles the main site footer (`.site-footer`, `.footer-content`, `.footer-links`).
    *   **Customization:** Modify rules here to change footer appearance or layout.

*   **`layout/_main.css`**
    *   **Purpose:** Styles the main content area (`.main`) and overrides default container behavior for header, main, and footer sections.
    *   **Customization:** Generally avoid editing unless changing the fundamental page structure or padding.

## `components/` Directory

Contains styles for reusable UI components.

*   **`components/_buttons.css`**
    *   **Purpose:** Defines base styles for buttons (`.button`, `.btn`) and variants (`.btn-primary`, `.button-outline`, `.button-small`, `.purchase-button`). Originally from `jenbury.css`.
    *   **Customization:** Modify existing variants or add new button variant classes here. Use variables for colors/spacing. *Note:* Page-specific button styles might exist in `pages/` or `admin/` partials.

*   **`components/_cards.css`**
    *   **Purpose:** Defines base styles for card components (`.card`, `.card-header`, `.card-body`, `.card-footer`). Originally from `jenbury.css`.
    *   **Customization:** Modify base card appearance. Specific card types (like course cards) might have additional styles in `pages/` or `admin/` partials.

*   **`components/_forms.css`**
    *   **Purpose:** Defines base styles for forms and form elements (`form`, `.input`, `label`, `input[type=...]`, `textarea`, `select`) and includes styles for validation errors (`.error-message`, `.form-error`). Originally from `jenbury.css`.
    *   **Customization:** Modify base form element appearance. Specific forms (login, register, admin forms) might have additional styles in `pages/` or `admin/` partials.

*   **`components/_flash-messages.css`**
    *   **Purpose:** Styles flash messages (`.message`, `.message.success`, `.message.error`, etc.). Originally from `jenbury.css`.
    *   **Customization:** Change the appearance of different flash message types. Use variables defined in `_variables.css`.

*   **`components/_badges.css`**
    *   **Purpose:** Styles badge elements (`.badge`, `.badge-success`). Originally from `jenbury.css`.
    *   **Customization:** Modify existing badge styles or add new variants.

*   **`components/_pagination.css`**
    *   **Purpose:** Styles pagination controls (`.pagination-container`, `.pagination`). Originally from `jenbury.css`.
    *   **Customization:** Change the appearance of pagination links and containers.

*   **`components/_status-indicators.css`**
    *   **Purpose:** Styles simple status indicators like `.bullet`, `.success`, `.problem` (using `cakefont`). Originally from `home.css`.
    *   **Customization:** Modify the appearance or add new indicator types. Requires `cakefont` to be loaded (see `fonts.css`).

## `pages/` Directory

Contains styles specific to individual public-facing pages or page groups.

*   **General:** Files here style specific pages or sections (e.g., Login, Register, Course Index, Course View, User Dashboard). They often contain overrides or additions to base/component styles.
*   **Customization:** If adding styles for a *new* page, create a new partial (e.g., `_new-page.css`) and import it in `main.css`. To modify an *existing* page's style, edit the corresponding partial here. Use page-specific wrapper classes (e.g., `.login-form-container`) to scope styles. Files like `_courses-variables.css` and `_dashboard-variables.css` contain variables specific to those sections; consider merging them into `base/_variables.css` if appropriate.

*   **Files:** `_change-password.css`, `_course-view.css`, `_courses-base.css`, `_courses-buttons.css`, `_courses-index.css`, `_courses-variables.css`, `_dashboard-buttons.css`, `_dashboard-cards.css`, `_dashboard-empty.css`, `_dashboard-layout.css`, `_dashboard-sidebar.css`, `_dashboard-variables.css`, `_dashboard-welcome.css`, `_error.css`, `_home.css`, `_login.css`, `_module-view.css`, `_register.css`

## `admin/` Directory

Contains styles specific to administrative sections.

*   **General:** Files here style specific admin pages or components (e.g., Admin Dashboard, Add/Edit forms, Site Content management).
*   **Customization:** Add styles for new admin sections by creating new partials here and importing them in `main.css`. Modify existing admin styles by editing the relevant partial. Use admin-specific wrapper classes (e.g., `.admin-dashboard`, `.add-course-page`) to scope styles.

*   **Files:** `_add-course.css`, `_add-module.css`, `_dashboard.css`, `_edit-course.css`, `_edit-module.css`, `_site-content.css`

## `utils/` Directory

Contains utility helper classes.

*   **`utils/_helpers.css`**
    *   **Purpose:** Defines reusable utility classes (e.g., `.text-center`, `.strikethrough`, `.no-courses`).
    *   **Customization:** Add new, simple utility classes here. Avoid complex styling; keep utilities focused on single purposes.

## `vendors/` Directory

Contains third-party CSS files.

*   **General:** These files (`cake.css`, `milligram.min.css`, `normalize.min.css`) provide base resets, framework styles, or grid systems.
*   **Customization:** **Do not edit these files directly.** If overrides are needed, add them in the appropriate `base/` or `components/` partials with higher specificity. Update these files only when upgrading the respective library/framework version.