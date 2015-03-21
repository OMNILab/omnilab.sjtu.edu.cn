# omnilab.sjtu.edu.cn

Main site of OMNILab at Shanghai Jiao Tong University.

## Requirements

* MySQL >= 5.0
* PHP >= 5.5

## Deployment

1. Install basic Web utilities including Web server, PHP, MySQL etc.
2. Clone this code repository into local web root folder, e.g.,

    $ git clone --recursive git@github.com:OMNILab/omnilab.sjtu.edu.cn.git /www/omnilab

3. Create new WordPress configuration file and change database settings for
your requirements:

    $ cp wp-config-sample.php wp-config.php


### Post-Installation

* Activate the OmniParallax theme modified for our own site style.

* Allocate FrontPage and Home page in `Settings -> Reading -> FrontPageDisplay`.

## Migration Issues and solutions

1. **Requested URL xxx (page) URL not found on this server**.
Try flushing your mod_rewrite rules: `Dashboard -> Settings -> Permalinks`.
Save settings (no need to make any changes).
