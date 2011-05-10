<?php

$bitcms_modules = array(
/*
  'attach_photo',
  'captcha',
  'cart',
  'catalog',
  'constructor',
  'cron',
  'counter',
  'document',
  'dynamic_catalog',
  'embed_video',
  'faq',
  'feedback',
  'history',
  'image',
  'mail',
*/
  'navigation',
/*
  'news',
  'option',
  'photogallery',
  'poll',
  'seo',
  'sphinx_search',
  'text_block',
  'user',
  'user_tracking',
  'vacancy',
*/
);

$conf = array(
  '_project_repo' => 'file:///home/xanm/Documents/test/git',
  '_project_db' => 'mysqli://root:test@localhost/project?charset=utf8',
  '_project_path' => '/home/xanm/Documents/test/project',

  '_bitcms_modules' => $bitcms_modules,

  '_limb_src' => '/home/xanm/Documents/Limb2010.1.2', //'git://github.com/r-kitaev/limb.git',
  '_limb_tag' => 'Limb2010.1.2',

  '_bitcms_src' => '/home/xanm/Documents/bitcms_git',//'git://git.bit/bitcms.git',
  '_bitcms_branch' => 'master',
);
