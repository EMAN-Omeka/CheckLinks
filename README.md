## CheckLinks

### Features 

Simple plugin to check for dead links in your Omeka Classic database.

After installation, you can go to the `yoursite.com/admin/checklinks` page.

You'll see an empty page with a `Re-check` button.

When you click on this button, the plugin will start to scan the database for URLs and test if they respond with an OK HTTP code (200, 301, etc). 

This could, and should, be done with a job, but development time lacking, the script is executed normally with an unlimited time limit.

**THIS MEANS A POSSIBLY VERY LONG EXECUTION TIME, UP TO SEVERAL HOURS IF YOUR DATABASE IS BIG**.

So please be careful with this button.

Once the script is finished, you'll see a table listing the faulty links, with the return code and a link to the related Omeka content.

The plugin checks for dead links in :

- metadata for items, collections and files
- Simple Pages
- Exhibits (pages, blocks and attachments)

This plugin is provided "as is". 

### Crédits

Mention crédits : Plugin réalisé pour la plate-forme [EMAN](http://eman-archives.org) (Item, ENS-CNRS) par Vincent Buard ([Numerizen](http://www.numerizen.com)).
