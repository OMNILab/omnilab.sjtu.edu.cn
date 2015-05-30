# omnilab.sjtu.edu.cn

Main site of OMNILab at Shanghai Jiao Tong University.

## Requirements

* MySQL >= 5.0
* PHP >= 5.5

## Install

1. Install basic Web utilities including Web server, PHP, MySQL etc.
2. Clone this code repository into local web root folder, e.g.,

        $ git clone --recursive git@github.com:OMNILab/omnilab.sjtu.edu.cn.git omnilab

3. Create new WordPress configuration file and change database settings for
your requirements:

        $ cp wp-config-sample.php wp-config.php


### First Configuration

* Activate the `OmniParallax` theme modified for our own site style.

* Active `Team Member` plugin.

* Create our pages: Frontpage, Team, Researches, Publications, Blogs, About
  etc.

* Allocate the `Frontpage` and `Post` page in `Settings -> Reading ->
  FrontPageDisplay`.

* Configure the permalinks as "Day and name" for a friendly reading
  style.
(**NOTE:** You have to enable the `rewrite_module` in apache2 and add
  "AllowOverride All" in `httpd.conf`. See: [How to enable rewrite_mod](http://xmodulo.com/how-to-enable-mod_rewrite-in-apache2-on-debian-ubuntu.html))

## Migration Issues and solutions

1. **Requested URL xxx (page) URL not found on this server**.
Try flushing your mod_rewrite rules: `Dashboard -> Settings -> Permalinks`.
Save settings (no need to make any changes).
