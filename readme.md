# Description
The **mPress Hide from Search** plugin allows to you hide specific pages from WordPress search.

It isn't uncommon to have pages on your site that are public, but not intended to be found. Take, for example, a download page where people who have signed up for your email newsletter can download your amazing white paper.  You don't want just anyone to be able to download your white paper, but the page has to be public because people who sign up for your newsletter aren't going to be logged into your site.  You were smart enough to use your favorite SEO plugin and block the search engines from indexing that page, but now you have realized that people who perform a search on your site for the title of the white paper are taken directly to the download page.  The solution?  Download this plugin and hide your download page from WordPress search!

## Features
- Works with custom post types
- No settings page, just a simple, easy-to-use checkbox
- Clean, well written code that won't bog down your site

# Contributors

## Pull Requests
All pull requests are welcome.  This plugin is relatively simple, so I'll probably be selective when it comes to features.  However, if you would like to submit a translation, this is the place to do it!

## SVN Access
If you have been granted access to SVN, this section details the processes for reliably checking out the code and committing your changes.

### Prerequisites
- Install Node.js
- Run `npm install -g gulp`
- Run `npm install` from the project root

### Checkout
- Run `gulp svn:checkout` from the project root

### Checkin
- Be sure that all version numbers in the code and readme have been updated.  Add changelog and upgrade notice entries.
- Tag the new version in Git
- Run `gulp svn:build` from the project root.
- Run `gulp svn:tag --v={version}`
- Run `svn ci -m "{commit message}"` from the SVN root to commit changes