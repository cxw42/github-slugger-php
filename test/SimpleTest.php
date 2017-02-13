<?php
# SimpleTest.php: Simple test of GitHubSlugger

namespace GitHubSlugger;

require '../src/slugger.php';

$s = new Slugger();
print_r($s->slug("Howdy, y'all!"));

