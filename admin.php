<?php
/**
 * =============================================================================
 * MURLI CREATIONS - STUDIO ADMINISTRATION PORTAL ENTRY POINT
 * =============================================================================
 * Yeh file public web entry point hai jo administration portal ko load karti hai.
 * Iska main backend controller 'backend/admin/index.php' me sthit hai.
 * 
 * Fayde (Advantages):
 * - Hostinger cPanel / Apache web root (public_html) me 'http://domain.com/admin.php'
 *   directly open hota hai.
 * - Developers ke liye backend ka saara code 'backend/admin/' folder me clean
 *   aur organized rehta hai jisse koi bhi aasaani se edit kar sakta hai.
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

require_once ROOT_PATH . '/backend/admin/index.php';
