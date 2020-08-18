# Craft Starter

Start learning faster.

Unlike other starters, this one focuses on a more complex, multi-langual information model and includes
some php stuff for common use cases.

This is just a starter demo, learning resource and a simple Proof of Concept for headless preview.


> **Warning**: This is not indended to be used as a starting point for a real world 
> application. It developed over the time by learning, discovering and experimenting, a lot of stuff done by interns (thanks all, especially Melissa and Aylin).
> Some things made to our real products, others didn't and some are just to crazy. And some may be broken.

TODO: Mel and Aylin will add some docs as part of their master thesis.

### What is not covered

* Advanced front end stuff (except for the example headless app). This is a simple bootstrap layout. 
You can setup a file watcher to compile sass in PhpStorm. 
* Really accessible markup.
* Everything that can be achieved with prominent plugins (advanced SEO, image handling...)
* Performance tuning

## Conventions

There are some conventions:

* Naming for single/channel sections: us xxxIndex (single) => xxx (channel)
e.g. newsIndex => news

* Names of volume root folders must not begin with an underscore. 

## Installation Considerations

* Ships with sample data (images and database backup) in /snapshot folder
* Ships with a couple of custom plugins, that represent a 'Proof of Concept' status. 
You may choose to disable or remove them.
* Remove path repository settings from composer.json, delete composer.lock before installing on a remote machine.
