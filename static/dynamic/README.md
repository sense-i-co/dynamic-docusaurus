# Docusaurus Extension: Dynamic Content
The dynamic content extension enables the integration of PHP pages within Docusaurus. Docusaurus was
originally designed to only work with static content and so extending it to accept dynamic content
requires some workarounds and limitations. These limitations are listed in the [Limitations](#Limitations)
section.

Overall, this extension effectively allows for PHP pages to be embedded within the default Docusaurus 
page layout and be accessed, in the same way as normal (static) pages, from the navigation bar.

## Installation
To install the extension, simply copy the entire `dynamic/` folder into the `static/` directory of 
your Docusaurus website. 

The resulting directory tree should be as follows:

```
static/
└── dynamic/
    ├── include/
    │   └── docusaurus.php
    ├── pages/
    ├── scripts/
    │   ├── page-capture.js
    │   └── page-restore.js
    └── README.md
```

## Configuration
The only configuration required is to change the constants declared at the top of the 
`static/dynamic/include/docusaurus.php` file.

These constants are as follows:
- `DOCUSAURUS_SITE_TITLE` - set this to the title for your website (also declared as `title` in 
`docusaurus.config.js`).
- `DOCUSAURUS_TEMPLATE_FILE` - set this to the path for a basic static page on your website generated 
by Docusaurus (the default is `../../index.html` which you shouldn't need to change unless you have 
special circustamces, e.g., index.html does not exist for your website).

## Compatibility

### Supported Version
- **@docusaurus/core**: 2.0.0-alpha.70
- **@docusaurus/preset-classic**: 2.0.0-alpha.70

This extension was written for, and verified to work with, the above-mentioned version of Docusaurus. 
Compatibility with future releases of Docusaurus is not guaranteed due to the nature of the
implementation.

### Breaking Changes
If updating Docusaurus causes this extension to break, it is most likely because of changes to
either the HTML structure of Docusaurus's pages or the behaviour of Docusaurus's included scripts. 

The technical functionality of this extension is explained in the [Implementation](#Implementation)
section below. To resolve breaking changes, read this section to understand how the extension works
and the potential points of failure. Systematically working through these implementation notes should
uncover the changes that have caused the extension to break.

## Usage
To add a new dynamic PHP page for your website, create a new `page.php` file (where _page_ is replaced
with the name of your page) within the `static/dynamic/pages/` directory.

This file (`static/dynamic/pages/page.php`) should have the following structure:

```php
<?php
  require '../include/docusaurus.php';
  $DOCUSAURUS_PAGE_TITLE = "Page Title";
  $DOCUSAURUS_PAGE_DESCRIPTION = "Page Description";
?>

<?php start_docusaurus_page(); ?>

<main>
  Page Content
</main>

<?php end_docusaurus_page(); ?>
```

To add a link to your new page within Docusaurus's navigation system, simply add an entry as presented below
to `navbar.items` property in `docusaurus.config.js`:

```javascript
module.exports = {
  // ...
  themeConfig: {
    navbar: {
      // ...
      items: [
        // ...
        {
          to: `${URL}/dynamic/pages/page.php`,
          label: 'Page Title',
          position: 'left',
          target: '_self',
        },
        // ...
      ],
    },
    // ...
  },
  // ...
};
```

**Note**:
- `to` must be an absolute URL (e.g. "https://www.mysite.co.za/dynamic/pages/page.php")
- `target` must be provided and set to "_self"

## Implementation
To integrate dynamic PHP pages within the Docusaurus framework (designed for static content), some
sneeky (and non-optimal) workarounds have been employed.

Firstly, all dynamic pages must be placed within the `static/` directory to prevent Docusaurus from
processing them during compilation. All files in `static/` are copied, without modification, to the
root of the compiled website. For consistency, it is recommended that the dynamic pages are all placed 
within the `static/dynamic/pages/` directory (or in custom subdirectories within there).

Since these dynamic pages are not processed during compilation, Docusaurus has no knowledge of their
existence as potential page end-points. Therefore, navigating to these pages from the Docusaurus 
navigation system is not as simple as normal static pages. We fool Docusaurus into thinking we are
creating a navigation link to an external website (by providing the absolute URL for the "to" 
attribute) and instructing the browser to open this page in the current tab (by setting the "target" 
attribute to "_self"), as opposed to Docusaurus's default behaviour of opening a new tab.

This approach results in a page reload on changing to the dynamic page (instead of the normal 
behaviour of page switching being handled by React Router, without a page reload). Upon reloading
the page, Docusaurus reinitialises itself which leads to its imported scripts running again. These
scripts – besides enabling the interactive functionality of multiple Docusaurus features (e.g. dark 
mode, mobile navigation menu, website search, etc.) – initialise the React Router which handles the 
loading of page content without a full page reload. Since our dynamic pages were not processed during 
compilation, Docusaurus's scripts are not aware of them and the URL of the current dynamic page 
registers as "Not Found". The page content is automatically removed and the page title is changed to 
"Page Not Found".

To resolve this, the extension includes two additional client-side scripts to restore the dynamic
page content after Docusaurus removes it. These scripts are `page-capture.js` and `page-restore.js`,
the former executing before Docusaurus's scripts to capture the dynamic page content and the latter
executing after Docusaurus's scripts to replace the content. The `page-restore.js` script not only
restores the visible page content but also the associated metadata, including page title and 
description. Finally, it also highlights the navigation links to the current dynamic page. Script 
operation depends on certain elements of Docusaurus's HTML structure not changing, which presents 
the risk of breaking changes with Docusaurus updates.

Before listing which page elements are expected, it is worth explaining how the extension retrieves
the outer Docusaurus HTML (i.e. navigation bar, footer, etc.) required to embed the dynamic content.
To achieve this, the extension uses a reference file to retrieve the generated Docusaurus HTML 
structure. By default, this is the home page (generated by the compiler as `index.html`). The
chosen reference file is declared at the top of `static/dynamic/include/docusaurus.php` in the
`DOCUSAURUS_TEMPLATE_FILE` constant.

The following elements are expected to be present (exactly as below) in the reference file (i.e. 
before page generation):
- `<div class="main-wrapper"> ... </div>` - containing the page content
- `<title data-react-helmet="true"> ... | ... </title>` - containing the page title
- `<meta data-react-helmet="true" property="og:title" content=" ... | ... ">` - containing the page title
- `<meta data-react-helmet="true" name="description" content=" ... ">` - containing the page description
- `<meta data-react-helmet="true" property="og:description" content=" ... ">` - containing the page description
- `<footer> ... </footer>` - containing the page footer

The following elements are expected to be present (exactly as below) after Docusaurus's scripts 
finishing running (i.e. before `page-restore.js` executes):
- `<div id="__docusaurus"> ... </div>` - containing the entire Docusaurus page structure
- `<title data-react-helmet="true"> ... | ... </title>` - containing the page title
- `<meta data-react-helmet="true" property="og:title" content=" ... | ... ">` - containing the page title
- `<footer> ... </footer>` - containing the page footer

## Limitations

### Dynamic Content Previews Require Build
During development, dynamic pages cannot be viewed when running the website with the `yarn start` or 
`npm run start` commands. The website must be fully built using the `yarn build` command and then the build
output (in `build/`) must be copied to a server set up to run PHP. For example, one could use either WAMP or
XAMPP server during development to preview dynamic content.

### Cannot Host Using GitHub Pages
Since our Docusaurus website now contains dynamic content, we are no longer able to make use of the free,
static hosting offered by GitHub Pages. Instead, website content must be manually hosted by an external 
server provider capable of serving PHP content.

### Dynamic Pages Require Reload
One other small side effect of using this extension is that Docusaurus is no longer able to provide an entirely
seamless browsing experience with no page reloads (implemented using React Router). As mentioned in the
[Implementation](#Implementation) section, switching to a dynamic page will now trigger a page reload.