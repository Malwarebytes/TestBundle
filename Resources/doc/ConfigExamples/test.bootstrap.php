<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonathan
 * Date: 8/14/13
 * Time: 11:07 AM
 */

require_once __DIR__.'/bootstrap.php.cache';
require_once __DIR__.'/AppKernel.php';


$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();



if ($kernel->getContainer()->getParameter("database_driver").$kernel->getContainer()->getParameter("database_host").$kernel->getContainer()->getParameter("database_name") === $kernel->getContainer()->getParameter("test_db_driver").$kernel->getContainer()->getParameter("test_db_host").$kernel->getContainer()->getParameter("test_db_name")) {
    throw new Exception("Test DB is the same as Production DB. We will not run tests against the production DB.");
}
